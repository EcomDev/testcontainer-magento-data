<?php

namespace EcomDev\TestContainers\MagentoData;

/**
 * Provides configuration options for building a container.
 */
trait ContainerBuilderConfiguration
{
    /**
     * Version of Magento which container is based on
     *
     * @var string
     */
    private string $magentoVersion = 'latest';

    /**
     * Name of the container variations
     *
     * @var string
     */
    private string $variation = '';

    /**
     * Sets the Magento version and returns a new instance with the updated value.
     */
    public function withMagentoVersion(string $version): self
    {
        $other = clone $this;
        $other->magentoVersion = $version;
        return $other;
    }

    /**
     * Sets the variation and returns a new instance with the updated value.
     */
    public function withVariation(string $variation): self
    {
        $other = clone $this;
        $other->variation = $variation;
        return $other;
    }

    /**
     * Configures the instance to use sample data and returns a new instance with the updated configuration.
     */
    public function withSampleData(): self
    {
        return $this->withVariation('sampledata');
    }

    private function generateImageTag(): string
    {
        return $this->magentoVersion . ($this->variation ? '-' . $this->variation : '');
    }
}
