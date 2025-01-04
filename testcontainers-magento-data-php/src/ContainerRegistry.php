<?php

namespace EcomDev\TestContainers\MagentoData;

/**
 * A class responsible for managing a registry of running containers,
 * identified by their image name and a unique identifier.
 */
final class ContainerRegistry
{
    private static array $containers = [];

    /**
     * Register container with specified id
     */
    public static function registerContainer(string $id, RunningContainer $container): RunningContainer
    {
        $imageName = $container->getImageName();
        self::$containers[$imageName][$id] = $container;
        return $container;
    }

    /**
     * Returns container by image and id if it was registered before
     */
    public static function findContainer(string $image, string $id): ?RunningContainer
    {
        return self::$containers[$image][$id] ?? null;
    }
}
