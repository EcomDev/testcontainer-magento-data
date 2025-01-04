<?php

namespace EcomDev\TestContainers\MagentoData;

use JsonException;
use Override;
use Symfony\Component\Process\Process;
use Testcontainers\Container\Container;
use Testcontainers\Registry;
use Testcontainers\Trait\DockerContainerAwareTrait;

/**
 * Container extension for removing volume associated with test container
 *
 * Original implementation does not remove ephemeral volumes of the container
 *
 * @private
 */
final class ContainerWithVolume extends Container
{
    use DockerContainerAwareTrait;

    private array $environmentVariables = [];

    private function __construct(public readonly string $image)
    {
        parent::__construct($image);
    }

    #[Override]
    public static function make(string $image): self
    {
        return new self($image);
    }

    /**
     * Starts a container
     *
     * When container is started it populates environment variables from inspect statement
     */
    #[Override]
    public function run(bool $wait = true): self
    {
        parent::run($wait);

        try {
            $inspect = self::dockerContainerInspect($this->getId());
            parse_str(implode('&', $inspect[0]['Config']['Env']), $this->environmentVariables);
        } catch (JsonException) {
            $this->environmentVariables = [];
        }

        return $this;
    }

    /**
     * St
     * @return array
     */
    public function getEnvironmentVariables(): array
    {
        return $this->environmentVariables;
    }

    /**
     * Removes container together with attached volume
     *
     * @return self
     */
    #[Override]
    public function remove(): self
    {
        $remove = new Process(['docker', 'rm', '-f', '-v', $this->getId()]);
        $remove->mustRun();

        Registry::remove($this);
        return $this;
    }
}
