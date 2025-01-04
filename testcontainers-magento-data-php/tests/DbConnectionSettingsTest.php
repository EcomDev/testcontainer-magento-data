<?php

namespace EcomDev\TestContainers\MagentoData;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class DbConnectionSettingsTest extends TestCase
{
    #[Test]
    public function createsConnectionSettingsFromEnvVariablesAndAddress()
    {
        $this->assertEquals(
            new DbConnectionSettings('localhost'),
            DbConnectionSettings::fromEnvironment([], 'localhost')
        );
    }

    #[Test]
    public function usesMysqlUserFromEnvironment()
    {
        $this->assertEquals(
            new DbConnectionSettings('localhost', user: 'magento'),
            DbConnectionSettings::fromEnvironment(
                ['MYSQL_USER' => 'magento'],
                'localhost'
            )
        );
    }

    #[Test]
    public function usesMysqlPasswordFromEnvironment()
    {
        $this->assertEquals(
            new DbConnectionSettings('localhost', password: 'magento-pass'),
            DbConnectionSettings::fromEnvironment(
                ['MYSQL_PASSWORD' => 'magento-pass'],
                'localhost'
            )
        );
    }

    #[Test]
    public function usesMysqlDatabaseFromEnvironment()
    {
        $this->assertEquals(
            new DbConnectionSettings('localhost', database: 'magento2'),
            DbConnectionSettings::fromEnvironment(
                ['MYSQL_DATABASE' => 'magento2'],
                'localhost'
            )
        );
    }

    #[Test]
    public function createsDsnWithDefaultOptions()
    {
        $connection = new DbConnectionSettings('db');

        $this->assertEquals(
            'mysql:host=db;port=3306;charset=utf8mb4',
            $connection->dsn()
        );
    }

    #[Test]
    public function createsDsnWithDatabaseName()
    {
        $connection = new DbConnectionSettings('db2', database: 'magento2');

        $this->assertEquals(
            'mysql:host=db2;port=3306;dbname=magento2;charset=utf8mb4',
            $connection->dsn()
        );
    }
}
