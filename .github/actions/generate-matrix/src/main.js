import { setOutput, info, getInput, setFailed } from "@actions/core";
import { versions, variations } from "./metadata";
import semver from "semver";

export async function run() {
  const magento = [];
  const containers = [];
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
          !semver.satisfies(normalizedVersion, variation.condition, true)
        ) {
          continue;
        }

        if (requestedVersion !== "" && requestedVersion !== version) {
          continue;
        }

        const tags = [version + variation.suffix];

        const joinTags = (tags) => tags.map((v) => `type=raw,${v}`).join(",");

        if (context.latest === true && version === lastVersion) {
          containers.push(
            ...containersForTag(
              "latest" + variation.suffix,
              version,
              variation,
              context,
            ),
          );
        }

        containers.push(
          ...containersForTag(
            version + variation.suffix,
            version,
            variation,
            context,
          ),
        );

        magento.push({
          artifact: version + variation.suffix,
          mariadb: context.mariadb,
          php: context.php,
          opensearch: context.opensearch,
          kind: variation.kind,
          magentoVersion: version,
          stability: context.stability ? context.stability : "stable",
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
  setOutput("containers", JSON.stringify(containers));
  info(
    `Successfully generated matrix for execution with versions: ${magento.map((v) => v.magentoVersion).join(",")}`,
  );
}

function containersForTag(tag, version, variation, context) {
  return [
    {
      artifact: version + variation.suffix,
      tag: `type=raw,${tag}`,
      containerType: "mysql",
      version: context.mysql,
    },
    {
      artifact: version + variation.suffix,
      tag: `type=raw,${tag}`,
      containerType: "mariadb",
      version: context.mariadb,
    },
    {
      artifact: version + variation.suffix,
      tag: `type=raw,${tag}`,
      containerType: "opensearch",
      version: context.opensearch,
    },
  ];
}
