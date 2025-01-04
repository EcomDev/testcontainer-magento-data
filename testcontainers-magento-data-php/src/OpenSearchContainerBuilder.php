<?php

namespace EcomDev\TestContainers\MagentoData;

use ReturnTypeWillChange;

final class OpenSearchContainerBuilder implements ContainerBuilder
{
    use ContainerBuilderConfiguration;

    public static function new():self
    {
        return new self();
    }

    #[ReturnTypeWillChange]
    public function build(): OpenSearchContainer
    {
        // TODO: Implement build() method.
    }

    #[ReturnTypeWillChange]
    public function shared(string $id): OpenSearchContainer
    {
        // TODO: Implement shared() method.
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