<?php

namespace EcomDev\TestContainers\MagentoData;

use ReturnTypeWillChange;

final class OpenSearchContainerBuilder implements ContainerBuilder
{
    use ContainerBuilderConfiguration;

    public static function new(): self
    {
        return new self();
    }

    #[ReturnTypeWillChange]
    public function build(): OpenSearchContainer
    {
        return OpenSearchContainer::fromImage($this->getImageName());
    }

    #[ReturnTypeWillChange]
    public function shared(string $id): OpenSearchContainer
    {
        /** @var OpenSearchContainer $container */
        $container = ContainerRegistry::findContainer($this->getImageName(), $id)
            ?? ContainerRegistry::registerContainer($id, $this->build());
        return $container;
    }

    public function getImageName(): string
    {
        return sprintf(
            "%s:%s",
            ContainerMetadata::getImageName('opensearch'),
            $this->generateImageTag()
        );
    }
}
