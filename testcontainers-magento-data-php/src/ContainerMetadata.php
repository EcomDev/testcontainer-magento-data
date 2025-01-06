<?php

namespace EcomDev\TestContainers\MagentoData;

final class ContainerMetadata
{
    private const CONTAINER_NAME = 'ghcr.io/ecomdev/testcontainer-magento-data/%s';

    public static function getImageName(string $type)
    {
        return sprintf(self::CONTAINER_NAME, $type);
    }
}
