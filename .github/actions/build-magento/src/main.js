import { info, error, getInput, group, setOutput } from "@actions/core";
import { exec, getExecOutput } from "@actions/exec";
import { mkdirP, mv, rmRF, which } from "@actions/io";
import { DefaultArtifactClient } from "@actions/artifact";
import { v7 } from "uuid";
import path from "node:path";
import { isFeatureAvailable, restoreCache, saveCache } from "@actions/cache";
import semver from "semver";

const currentBuildId = "build-" + v7();
let currentDir = "";

function cwd() {
  return process.env["GITHUB_WORKSPACE"] ?? process.cwd();
}

export async function run(actionPath) {
  currentDir = path.dirname(actionPath);

  try {
    const artifactDir = await prepareCachedArtifact();

    await group("Upload Artifact", async () => {
      const artifactClient = new DefaultArtifactClient();

      await artifactClient.uploadArtifact(
        getInput("artifact"),
        [
          path.join(artifactDir, "dump.sql"),
          path.join(artifactDir, "search.tgz"),
        ],
        artifactDir,
      );
    });
  } catch (e) {
    error(`Failed to install Magento: ${e}`);
    throw e;
  }
}

async function prepareCachedArtifact() {
  const artifact = `cache-magento-build-${getInput("artifact")}`;
  const artifactDir = path.join("build", "artifact");

  const isCached = await group("Loading Magento build cache", async () => {
    if (!isFeatureAvailable()) {
      info("Cache feature is not available");
      return false;
    }

    const cacheId = await restoreCache(["build/*"], artifact, []);
    return cacheId === artifact;
  });

  if (isCached) {
    return artifactDir;
  }

  await mkdirP(path.join(cwd(), artifactDir));
  await prepareArtifact(artifactDir);

  await group("Storing Magento build cache", async () => {
    if (!isFeatureAvailable()) {
      info("Cache feature is not available");
      return;
    }
    await saveCache(["build/*"], artifact);
  });

  return artifactDir;
}

async function prepareArtifact(artifactDir) {
  await group("Create Environment", async () => {
    await runDockerCompose("up", ["-d", "--wait"]);
  });

  try {
    await installMagento();
    const dbOutputFile = "/docker-entrypoint-initdb.d/dump.sql";
    await group("Prepare Artifact", async () => {
      await runDockerCompose("exec", [
        "db",
        "mariadb-dump",
        "-u",
        "magento",
        "-pmagento",
        "magento",
        "-r",
        dbOutputFile,
      ]);
      await runDocker([
        "cp",
        `${currentBuildId}-db-1:${dbOutputFile}`,
        path.join(cwd(), artifactDir, "dump.sql"),
      ]);
      await runDockerCompose("stop", ["opensearch"]);
      await runDocker([
        "cp",
        `${currentBuildId}-opensearch-1:/usr/share/opensearch/data`,
        path.join(cwd(), artifactDir, "search"),
      ]);
      await exec("tar", ["czvf", "./search.tgz", "./search"], {
        cwd: path.join(cwd(), artifactDir),
      });
      await rmRF(path.join(cwd(), artifactDir, "search"));
    });
  } finally {
    await group("Clean Environment", () => runDockerCompose("down", ["-v"]));
  }
}

async function runDocker(args, withOutput = false, options = {}) {
  const docker = await which("docker");
  options = {
    cwd: currentDir,
    env: {
      MARIADB_VERSION: getInput("mariadb"),
      PHP_VERSION: getInput("php"),
      OPENSEARCH_VERSION: getInput("opensearch"),
      COMPOSER_AUTH: getInput("composerAuth"),
      ...process.env,
    },
    ...options,
  };

  if (withOutput) {
    return await getExecOutput(docker, args, options);
  }

  return await exec(docker, args, options);
}

async function runDockerCompose(
  action,
  args,
  withOutput = false,
  options = {},
) {
  args = ["compose", "-p", currentBuildId, action, ...args];
  return await runDocker(args, withOutput, options);
}

async function executeInMagentoContainer(args, withOutput = false) {
  return await runDockerCompose("exec", ["php-fpm", ...args], withOutput);
}

async function installMagento() {
  const version = getInput("version");
  const stability = getInput("stability");
  const kind = getInput("kind");

  await group(`Setup Magento ${version}`, () =>
    setupMagento(version, stability),
  );

  switch (kind) {
    case "sampledata":
      await group("Install Sample Data", installSampleData);
      break;
  }

  await group("Re-index Magento data", runReindex);
}

async function setupMagento(version, stability) {
  await executeInMagentoContainer([
    "composer",
    "create-project",
    "--stability",
    stability,
    "--prefer-dist",
    "--no-progress",
    "--repository-url",
    getInput("composerRepository"),
    `magento/project-community-edition:${version}`,
    "/var/www/html/",
  ]);

  const searchEnginePrefix = semver.satisfies(semver.coerce(version), ">=2.4.6")
    ? "opensearch"
    : "elasticsearch";

  const searchEngineName =
    searchEnginePrefix === "opensearch" ? "opensearch" : "elasticsearch7";

  await executeInMagentoContainer([
    "bin/magento",
    "setup:install",
    "--backend-frontname",
    "backend",
    "--db-host",
    "db",
    "--db-name",
    "magento",
    "--db-password",
    "magento",
    "--search-engine",
    searchEngineName,
    `--${searchEnginePrefix}-host`,
    "opensearch",
    `--${searchEnginePrefix}-port`,
    "9200",
    `--${searchEnginePrefix}-index-prefix`,
    "magento2",
    `--${searchEnginePrefix}-enable-auth`,
    "0",
    `--${searchEnginePrefix}-timeout`,
    "15",
  ]);
}

async function installSampleData() {
  info("Install sample data");

  await executeInMagentoContainer(["bin/magento", "sampledata:deploy"]);

  info("Run setup upgrade");
  await executeInMagentoContainer(["bin/magento", "setup:upgrade"]);
}

async function runReindex() {
  await executeInMagentoContainer([
    "bin/magento",
    "indexer:set-mode",
    "schedule",
  ]);
  await executeInMagentoContainer(["bin/magento", "indexer:reindex"]);
}
