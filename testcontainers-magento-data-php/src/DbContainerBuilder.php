<?php

namespace EcomDev\TestContainers\MagentoData;

/**
 * A builder class for creating and managing database container instances,
 * with configurations for different database types, Magento versions, and variations.
 */
final class DbContainerBuilder
{
    private static $sharedContainers = [];

    /**
     * Constructor method for initializing the class with specified parameters
     */
    private function __construct(
        private readonly string $type,
        private string $magentoVersion = 'latest',
        private string $variation = '',
    ) {

    }

    public static function mysql(): self
    {
        return new self(type: 'mysql');
    }

    public static function mariadb(): self
    {
        return new self(type: 'mariadb');
    }

    /**
     * Sets the Magento version and returns a new instance with the updated value.
     *
     * @param string $version The version of Magento to be set.
     * @return self A new instance with the specified Magento version.
     */
    public function withMagentoVersion(string $version): self
    {
        $other = clone $this;
        $other->magentoVersion = $version;
        return $other;
    }

    /**
     * Sets the variation and returns a new instance with the updated value.
     *
     * @param string $variation The variation to be set.
     * @return self A new instance with the specified variation.
     */
    public function withVariation(string $variation): self
    {
        $other = clone $this;
        $other->variation = $variation;
        return $other;
    }

    /**
     * Configures the instance to use sample data and returns a new instance with the updated configuration.
     *
     * @return self A new instance configured to include sample data.
     */
    public function withSampleData(): self
    {
        return $this->withVariation('sampledata');
    }

    /**
     * Retrieves a shared database container instance by its identifier. If the container
     * does not already exist, it creates and stores a new one.
     *
     * @param string $id The identifier for the shared database container.
     * @return DbContainer The shared database container instance.
     */
    public function shared(string $id): DbContainer
    {
        if (!isset(self::$sharedContainers[$this->getImageName()][$id])) {
            self::$sharedContainers[$this->getImageName()][$id] = $this->build();
        }

        return self::$sharedContainers[$this->getImageName()][$id];
    }


    /**
     * Builds and returns a DbContainer instance using the specified image name.
     *
     * @return DbContainer The created DbContainer instance.
     */
    public function build(): DbContainer
    {
        return DbContainer::fromImage($this->getImageName());
    }

    /**
     * Internal image name generator, do not use from outside as it might get changed in future
     * @private
     */
    public function getImageName(): string
    {
        $imageName = ContainerMetadata::getImageName($this->type);
        $imageTag = $this->magentoVersion . ($this->variation ? '-' . $this->variation : '');
        return sprintf('%s:%s', $imageName, $imageTag);
    }
}