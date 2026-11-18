# 🐳 Test-Containers for Quick Magento Development
[![Magento Images](https://github.com/EcomDev/testcontainer-magento-data/actions/workflows/magento-images.yml/badge.svg)](https://github.com/EcomDev/testcontainer-magento-data/actions/workflows/magento-images.yml)

[![Mage-OS Images](https://github.com/EcomDev/testcontainer-magento-data/actions/workflows/mage-os-images.yml/badge.svg)](https://github.com/EcomDev/testcontainer-magento-data/actions/workflows/mage-os-images.yml)

This package simplifies the process of automated testing with real database and search engine

## ✨ Features

- 📦 **Pre-configured database and search containers**: Instantly spin up containers with ready-to-use Magento data
- ⚙️ **Easy setup and use**: Use PHP or Rust package to automatically discard container after tests
- 🎯 **Blazingly Fast**: Container takes only few seconds to start, so you can focus on testing instead of waiting for db initialization

## 📋 Requirements

- **Docker**: Ensure Docker is installed and operational on your system.

## 📦 Available images

Every day images generated 

Each container image has a version tag with minimum supported version of data container (mysql, mariadb, opensearch) associated with Magento version.

### Container images
Container images are generated under such naming schema:
`ghcr.io/ecomdev/testcontainer-magento-data/[IMAGE_NAME]`

With the following `IMAGE_NAME` variations:
- `mysql` - Pre-populated MariaDB database container with relevant Magento schema and data
- `mariadb` - Pre-populated MariaDB database container with relevant Magento schema and data
- `opensearch` - Pre-populated OpenSearch container with relevant Magento indexes based on db data
- `media` - Pre-populated media container with relevant Magento media files. Can be mounted into your own containers as `pub/media`

## Prebuild Packages

Packages available in the following languages to streamline usage of the Magento data image:
- [ecomdev/testcontainers-magento-data](https://github.com/EcomDev/testcontainer-magento-data-php) composer package in PHP
- [testcontainers-magento-data](https://github.com/EcomDev/testcontainer-magento-data-rust) cargo crate in Rust

## License

Licensed under the [Apache License, Version 2.0](./LICENSE). Copyright © EcomDev B.V.