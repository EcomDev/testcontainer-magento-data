<?php

namespace EcomDev\TestContainers\MagentoData;

/**
 * Represents a running container interface with methods to retrieve its details.
 */
interface RunningContainer
{
    /**
     * Retrieves the container IP address
     *
     * @return string
     */
    public function getAddress(): string;

    /**
     * Retrieves the unique identifier
     *
     * @return string
     */
    public function getId(): string;

    /**
     * Returns container image name
     *
     * @return string
     */
    public function getImageName(): string;
}
