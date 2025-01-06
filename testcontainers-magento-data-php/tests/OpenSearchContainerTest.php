<?php

namespace EcomDev\TestContainers\MagentoData;

use GuzzleHttp\Client;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class OpenSearchContainerTest extends TestCase
{
    #[Test]
    public function generatesDefaultImageNameWithLatestMagentoVersion()
    {
        $this->assertEquals(
            'ghcr.io/ecomdev/testcontainer-magento-data/opensearch:latest',
            OpenSearchContainerBuilder::new()
             ->getImageName()
        );
    }


    #[Test]
    public function generatesDefaultImageNameWithCustomMagentoVersion()
    {
        $this->assertEquals(
            'ghcr.io/ecomdev/testcontainer-magento-data/opensearch:2.4.7-p2',
            OpenSearchContainerBuilder::new()
                ->withMagentoVersion('2.4.7-p2')
                ->getImageName()
        );
    }


    #[Test]
    public function generatesSampleDataImageNameWithCustomMagentoVersion()
    {
        $this->assertEquals(
            'ghcr.io/ecomdev/testcontainer-magento-data/opensearch:2.4.7-p2-sampledata',
            OpenSearchContainerBuilder::new()
                ->withMagentoVersion('2.4.7-p2')
                ->withSampleData()
                ->getImageName()
        );
    }

    #[Test]
    #[Group("slow")]
    public function createsContainerWithSampleData()
    {
        $container = OpenSearchContainerBuilder::new()
            ->withSampleData()
            ->withMagentoVersion('2.4.7-p2')
            ->build();

        $client = new Client([
            'base_uri' => $container->getBaseUrl()
        ]);

        $result = json_decode(
            $client->get('magento2_product_1/_count')->getBody()->getContents(),
            true
        );

        $this->assertEquals(
            181,
            $result['count']
        );
    }

    #[Test]
    #[Group("slow")]
    public function sharedContainersSmokeTest()
    {
        $this->assertSame(
            OpenSearchContainerBuilder::new()
                ->withSampleData()
                ->shared('instance1'),
            OpenSearchContainerBuilder::new()
                ->withSampleData()
                ->shared('instance1'),
        );

        $this->assertNotSame(
            OpenSearchContainerBuilder::new()
                ->withSampleData()
                ->shared('instance1'),
            OpenSearchContainerBuilder::new()
                ->withSampleData()
                ->shared('instance2'),
        );
    }
}
