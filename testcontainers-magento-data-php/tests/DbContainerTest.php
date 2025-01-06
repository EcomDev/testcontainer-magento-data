<?php

namespace EcomDev\TestContainers\MagentoData;

use PDO;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class DbContainerTest extends TestCase
{
    private function createConnection(DbConnectionSettings $connectionSettings): PDO
    {
        return new PDO(
            $connectionSettings->dsn(),
            $connectionSettings->user,
            $connectionSettings->password,
            [
                'attributes' => [
                    PDO::ATTR_STRINGIFY_FETCHES => false,
                ]
            ]
        );
    }

    #[Test]
    #[Group("slow")]
    public function startsLatestMySQLContainerByDefault()
    {
        $container = DbContainerBuilder::mysql()
            ->build();

        $connectionSettings = $container->getConnectionSettings();

        $connection = $this->createConnection($connectionSettings);
        $result = $connection->query('SELECT COUNT(*) FROM catalog_product_entity');
        $this->assertEquals(0, $result->fetch(PDO::FETCH_COLUMN));
    }

    #[Test]
    #[Group("slow")]
    public function killsContainerOnDestruct()
    {
        $container = DbContainerBuilder::mysql()
            ->build();

        $connectionSettings = $container->getConnectionSettings();
        unset($container);

        $this->expectException(\PDOException::class);
        $this->createConnection($connectionSettings);
    }

    #[Test]
    #[Group("slow")]
    public function doesNotKillContainerWhenBuildAsShared()
    {
        $container = DbContainerBuilder::mysql()
            ->shared('container1');

        $connectionSettings = $container->getConnectionSettings();
        unset($container);

        $connection = $this->createConnection($connectionSettings);
        $result = $connection->query('SELECT COUNT(*) FROM catalog_product_entity');
        $this->assertEquals(0, $result->fetch(PDO::FETCH_COLUMN));
    }

    #[Test]
    #[Group("slow")]
    public function containersAreIdenticalWhenShared()
    {
        $this->assertSame(
            DbContainerBuilder::mysql()
                ->shared('container1'),
            DbContainerBuilder::mysql()
                ->shared('container1')
        );

        $this->assertNotSame(
            DbContainerBuilder::mysql()
                ->shared('container1'),
            DbContainerBuilder::mysql()
                ->shared('container2')
        );
    }

    #[Test]
    #[Group("slow")]
    public function loadsMagentoSampleDataContainer()
    {
        $container = DbContainerBuilder::mysql()
            ->withMagentoVersion('2.4.7-p3')
            ->withSampleData()
            ->build();

        $connectionSettings = $container->getConnectionSettings();
        $connection = $this->createConnection($connectionSettings);
        $result = $connection->query('SELECT COUNT(*) FROM catalog_product_entity');
        $this->assertEquals(2040, $result->fetch(PDO::FETCH_COLUMN));
    }

    #[Test]
    public function generatesImageForSpecificMagentoVersion()
    {
        $this->assertEquals(
            'ghcr.io/ecomdev/testcontainer-magento-data/mysql:2.4.7-p2-sampledata',
            DbContainerBuilder::mysql()
                ->withSampleData()
                ->withMagentoVersion('2.4.7-p2')
                ->getImageName()
        );
    }

    #[Test]
    public function generatesMariadbImageForMagentoVersion()
    {
        $this->assertEquals(
            'ghcr.io/ecomdev/testcontainer-magento-data/mariadb:2.4.7-p2-sampledata',
            DbContainerBuilder::mariadb()
                ->withSampleData()
                ->withMagentoVersion('2.4.7-p2')
                ->getImageName()
        );
    }
}
