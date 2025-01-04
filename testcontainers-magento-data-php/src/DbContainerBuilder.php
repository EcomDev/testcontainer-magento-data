<?php

namespace EcomDev\TestContainers\MagentoData;

use ReturnTypeWillChange;

/**
 * A builder class for creating and managing database container instances,
 * with configurations for different database types, Magento versions, and variations.
 */
final class DbContainerBuilder implements ContainerBuilder
{
    use ContainerBuilderConfiguration;

    /**
     * Constructor method for initializing the class with specified parameters
     */
    private function __construct(
        private readonly string $type
    ) {
    }

    public static function mysql(): self
    {
        return self::for('mysql');
    }

    public static function mariadb(): self
    {
        return self::for('mariadb');
    }

    public static function for($type): self
    {
        return new self($type);
    }

    #[ReturnTypeWillChange]
    public function shared(string $id): DbContainer
    {
        /** @var DbContainer $container */
        $container = ContainerRegistry::findContainer($this->getImageName(), $id)
            ?? ContainerRegistry::registerContainer($id, $this->build());
        return $container;
    }

    #[ReturnTypeWillChange]
    public function build(): DbContainer
    {
        return DbContainer::fromImage($this->getImageName());
    }

    public function getImageName(): string
    {
        return sprintf('%s:%s', ContainerMetadata::getImageName($this->type), $this->generateImageTag());
    }
}
