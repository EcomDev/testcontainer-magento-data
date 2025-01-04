<?php

namespace EcomDev\TestContainers\MagentoData;

use Testcontainers\Wait\WaitForLog;

final class OpenSearchContainer implements RunningContainer
{
    private function __construct(private readonly ContainerWithVolume $container)
    {

    }
    public static function fromImage(string $imageName): self
    {
        $container = ContainerWithVolume::make($imageName);
        $container->withWait(new WaitForLog('ready for connections'));
        $container->run();

        return new self($container);
    }

    public function getBaseUrl(): string
    {
        return sprintf('https://%s:9200/', $this->getAddress());
    }

    public function getAddress(): string
    {
        return $this->container->getAddress();
    }

    public function getId(): string
    {
        return $this->container->getId();
    }

    public function getImageName(): string
    {
        return $this->container->image;
    }

    public function __destruct()
    {
        $this->container->remove();
    }

}