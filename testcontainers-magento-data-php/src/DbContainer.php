<?php

namespace EcomDev\TestContainers\MagentoData;

use Testcontainers\Container\Container;
use Testcontainers\Trait\DockerContainerAwareTrait;
use Testcontainers\Wait\WaitForLog;

final class DbContainer implements RunningContainer
{
    use DockerContainerAwareTrait;

    private array $environmentVars = [];

    private function __construct(private readonly Container $container)
    {

    }
    public static function fromImage(string $imageName): self
    {
        $container = Container::make($imageName);
        $container->withWait(new WaitForLog('ready for connections'));
        $container->run();

        return new self($container);
    }

    public function getConnectionSettings(): DbConnectionSettings
    {
        return DbConnectionSettings::fromEnvironment($this->getEnvironmentVars(), $this->getAddress());
    }

    private function getEnvironmentVars(): array
    {
        if (!$this->environmentVars) {
            $inspect = self::dockerContainerInspect($this->container->getId());
            parse_str(implode('&', $inspect[0]['Config']['Env']), $this->environmentVars);
        }

        return $this->environmentVars;
    }

    public function getAddress(): string
    {
        return $this->container->getAddress();
    }

    public function getId(): string
    {
        return $this->container->getId();
    }

    public function __destruct()
    {
        $this->container->remove();
    }
}