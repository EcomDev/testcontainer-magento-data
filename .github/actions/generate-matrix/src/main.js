import { setOutput, info, getInput, setFailed } from "@actions/core";
import { versions, variations } from "./metadata";
import semver from "semver";

export async function run() {
  const magento = [];
  const requestedVersion = getInput("version");
  const requestedVariation = getInput("variation");
  for (const variation of variations) {
    if (requestedVariation !== "" && requestedVariation !== variation.kind) {
      continue;
    }
    for (const context of versions) {
      const lastVersion = context.versions[context.versions.length - 1];
      for (const version of context.versions) {
        const normalizedVersion = semver.coerce(version);
        if (
          variation.condition &&
          !semver.satisfies(normalizedVersion, variation.condition)
        ) {
          continue;
        }

        if (requestedVersion !== "" && requestedVersion !== version) {
          continue;
        }

        const tags = [version + variation.suffix];

        if (context.latest === true && version === lastVersion) {
          tags.push("latest" + variation.suffix);
        }

        const joinTags = (tags) => tags.map((v) => `type=raw,${v}`).join(",");

        magento.push({
          artifact: version + variation.suffix,
          mariadb: context.mariadb,
          php: context.php,
          opensearch: context.opensearch,
          kind: variation.kind,
          magentoVersion: version,
          stability: context.stability ? context.stability : "stable",
          containers: JSON.stringify([
            {
              tag: joinTags(tags),
              containerType: "mysql",
              version: context.mysql,
            },
            {
              tag: joinTags(tags),
              containerType: "mariadb",
              version: context.mariadb,
            },
            {
              tag: joinTags(tags),
              containerType: "opensearch",
              version: context.opensearch,
            },
          ]),
        });
      }
    }
  }

  if (magento.length === 0) {
    setFailed(
      `No versions found matching criteria: ${requestedVersion}-${requestedVariation}`,
    );
    return;
  }
  setOutput("magento", JSON.stringify(magento));
  info(
    `Successfully generated matrix for execution with versions: ${magento.map((v) => v.magentoVersion).join(",")}`,
  );
}
