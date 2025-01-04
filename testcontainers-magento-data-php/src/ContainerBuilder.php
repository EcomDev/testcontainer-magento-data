<?php

namespace EcomDev\TestContainers\MagentoData;

/**
 * Interface defining methods for building and configuring containers.
 */
interface ContainerBuilder
{
    /**
     * Sets the Magento version and returns a new instance with the updated value.
     *
     * @param string $version The version of Magento to be set.
     * @return self A new instance with the specified Magento version.
     */
    public function withMagentoVersion(string $version): self;

    /**
     * Sets the variation and returns a new instance with the updated value.
     *
     * @param string $variation The variation to be set.
     * @return self A new instance with the specified variation.
     */
    public function withVariation(string $variation): self;

    /**
     * Configures the instance to use sample data and returns a new instance with the updated configuration.
     *
     * @return self A new instance configured to include sample data.
     */
    public function withSampleData(): self;

    /**
     * Builds container with configured input
     */
    public function build(): RunningContainer;

    /**
     * Creates new container with unique identifier or returns existing one with the same id
     *
     * Helpful for re-using read-only container usage
     */
    public function shared(string $id): RunningContainer;

    /**
     * Returns final image name based on all configuration provided
     */
    public function getImageName(): string;
}
