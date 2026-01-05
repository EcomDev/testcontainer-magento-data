export const variations = [
  {
    suffix: "-sampledata",
    kind: "sampledata",
    condition: ">=2.4.7 && <2.4.9",
  },
  { suffix: "", kind: "default" },
];
export const versions = [
  {
    mysql: "8.0",
    mariadb: "10.6",
    opensearch: "2.12",
    php: "8.2",
    versions: [
      "2.4.6-p7",
      "2.4.6-p8",
      ,
      "2.4.6-p8",
      ,
      "2.4.6-p9",
      "2.4.6-p10",
      "2.4.6-p11",
      "2.4.6-p12",
      "2.4.6-p13",
    ],
  },
  {
    mysql: "8.0",
    mariadb: "10.6",
    opensearch: "2.12",
    php: "8.3",
    versions: [
      "2.4.7-p2",
      "2.4.7-p3",
      "2.4.7-p4",
      "2.4.7-p5",
      "2.4.7-p6",
      "2.4.7-p7",
      "2.4.7-p8",
    ],
  },
  {
    mysql: "8.4",
    mariadb: "11.4",
    opensearch: "2.12",
    php: "8.4",
    latest: true,
    versions: ["2.4.8", "2.4.8-p1", "2.4.8-p2", "2.4.8-p3"],
  },
];
