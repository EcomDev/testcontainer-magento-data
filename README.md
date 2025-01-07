# 🐳 Test-Containers for Quick Magento Development
[![Docker Build](https://github.com/EcomDev/testcontainer-magento-data/actions/workflows/docker-images.yml/badge.svg)](https://github.com/EcomDev/testcontainer-magento-data/actions/workflows/docker-images.yml)
[![PHP Package](https://github.com/EcomDev/testcontainer-magento-data-php/actions/workflows/php-package.yml/badge.svg)](https://github.com/EcomDev/testcontainer-magento-data-php/actions/workflows/php-package.yml)
[![Rust Package](https://github.com/EcomDev/testcontainer-magento-data-rust/actions/workflows/rust-package.yml/badge.svg)](https://github.com/EcomDev/testcontainer-magento-data-rust/actions/workflows/rust-package.yml)

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
Container images are generated under such naming schema:
`ghcr.io/ecomdev/testcontainer-magento-data/[IMAGE_NAME]` 

With the following `IMAGE_NAME` variations:
- `mysql` - Pre-populated MariaDB database container with relevant Magento schema and data
- `mariadb` - Pre-populated MariaDB database container with relevant Magento schema and data
- `opensearch` - Pre-populated OpenSearch container with relevant Magento indexes based on db data

## Prebuild Packages

Packages available in the following languages to streamline usage of the Magento data image:
- [ecomdev/testcontainers-magento-data](`https://github.com/EcomDev/testcontainer-magento-data-php`) composer package in PHP
- [testcontainers-magento-data](`https://github.com/EcomDev/testcontainer-magento-data-rust`) cargo crate in Rust

## 📜 License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for more details.