<?php

namespace EcomDev\TestContainers\MagentoData;

use Testcontainers\Container\Container;
use Testcontainers\Trait\DockerContainerAwareTrait;
use Testcontainers\Wait\WaitForLog;

final class DbContainer implements RunningContainer
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

    public function getConnectionSettings(): DbConnectionSettings
    {
        return DbConnectionSettings::fromEnvironment($this->container->getEnvironmentVariables(), $this->getAddress());
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
