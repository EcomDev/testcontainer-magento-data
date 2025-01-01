export const variations = [
  {
    suffix: "-sampledata",
    kind: "sampledata",
    condition: ">=2.4.7 && <2.4.8",
  },
  { suffix: "", kind: "default" },
];
export const versions = [
  {
    mysql: "8.0",
    mariadb: "10.6",
    opensearch: "2.12",
    php: "8.2",
    versions: ["2.4.6-p7", "2.4.6-p8"],
  },
  {
    mysql: "8.0",
    mariadb: "10.6",
    opensearch: "2.12",
    php: "8.3",
    latest: true,
    versions: ["2.4.7-p2", "2.4.7-p3"],
  },
  {
    mysql: "8.4",
    mariadb: "11.4",
    opensearch: "2.12",
    php: "8.3",
    stability: "beta",
    versions: ["2.4.8-beta1"],
  },
];
