# 🐳 Test-Containers for Quick Magento Development
[![Docker Build](https://github.com/EcomDev/testcontainer-magento-data/actions/workflows/docker-images.yml/badge.svg)](https://github.com/EcomDev/testcontainer-magento-data/actions/workflows/docker-images.yml)
[![PHP Package](https://github.com/EcomDev/testcontainer-magento-data/actions/workflows/php-package.yml/badge.svg)](https://github.com/EcomDev/testcontainer-magento-data/actions/workflows/php-package.yml)

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

## Usage in PHP

### MySQL container 

Create Latest Magento Database Build
```php
use EcomDev\TestContainers\MagentoData\DbContainerBuilder;

$container = DbContainerBuilder::mysql()
    ->build();
```

Create Latest Magento Database Build with sample data
```php
use EcomDev\TestContainers\MagentoData\DbContainerBuilder;

$container = DbContainerBuilder::mysql()
    ->withSampleData()
    ->build();
```

Create 2.4.7-p2 with sample data and fetch number of products
```php
use EcomDev\TestContainers\MagentoData\DbContainerBuilder;
use PDO;

$container = DbContainerBuilder::mysql()
    ->withMagentoVersion('2.4.7-p2')
    ->withSampleData()
    ->build();

$connectionSettings = $container->getConnectionSettings();
$connection = new PDO(
    $connectionSettings->dsn(),
    $connectionSettings->user,
    $connectionSettings->password
);

$result = $connection->query('SELECT COUNT(*) FROM catalog_product_entity');
// Outputs 2040
echo $result->fetch(PDO::FETCH_COLUMN);
```

### MariaDB container
Everything the same as for MySQL container, just a different builder method

```php
use EcomDev\TestContainers\MagentoData\DbContainerBuilder;

$container = DbContainerBuilder::mariadb()
    ->withMagentoVersion('2.4.7-p2')
    ->withSampleData()
    ->build();
```

## OpenSearch container

For OpenSearch container there is a different builder and container, that allows building base url for http connection

Here is a small example

```php
use EcomDev\TestContainers\MagentoData\OpenSearchContainerBuilder;
use GuzzleHttp\Client;

$container = OpenSearchContainerBuilder::new()
            ->withSampleData()
            ->build();

$client = new Client([
    'base_uri' => $container->getBaseUrl()
]);

$result = json_decode(
    $client->get('magento2_product_1/_count')->getBody()->getContents(),
    true
);

// Outputs 181
echo $result['count'];
```

## 📜 License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for more details.