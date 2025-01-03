# 🐳 Test-Containers for Quick Magento Development

This package simplifies the process of automated testing with real database and search engine

## ✨ Features

- 📦 **Pre-configured database and search containers**: Instantly spin up containers with ready-to-use Magento data
- ⚙️ **Easy setup and use**: Use PHP or Rust package to automatically discard container after tests
- 🎯 **Blazingly Fast**: Container takes only few seconds to start, so you can focus on testing instead of waiting for db initialization

## 📋 Requirements

- **🐳 Docker**: Ensure Docker is installed and operational on your system.

## 📦 Available images

Each container image has a version tag with maximum software version supported by the Magento. 

| Image Tag       | MariaDB Version | MySQL Version | OpenSearch |
|-----------------|-----------------|---------------|------------|
| 2.4.6-p7        | 10.6            | 8.0           | 2.12       |
| 2.4.6-p8        | 10.6            | 8.0           | 2.12       |
| 2.4.7-p2        | 10.6            | 8.0           | 2.12       |
| 2.4.7-p3,latest | 10.6            | 8.0           | 2.12       |
| 2.4.8-beta1     | 11.4            | 8.4           | 2.12       |

For now only `2.4.7` tags have `sampledata` (e.g. `2.4.7-p3-sampledata`) variation, as on previous version sample data deploy fails.

### Container images
- **ghcr.io/ecomdev/testcontainer-magento-mysql** - Pre-populated MariaDB database container with relevant Magento schema and data
- **ghcr.io/ecomdev/testcontainer-magento-mariadb** - Pre-populated MariaDB database container with relevant Magento schema and data
- **ghcr.io/ecomdev/testcontainer-magento-opensearch** - Pre-populated OpenSearch container with relevant Magento indexes based on db data

## 📜 License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for more details.