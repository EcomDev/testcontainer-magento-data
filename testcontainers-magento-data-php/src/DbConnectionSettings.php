<?php

namespace EcomDev\TestContainers\MagentoData;

final class DbConnectionSettings
{
    private const int DEFAULT_PORT = 3306;
    private const string DEFAULT_USER = 'root';
    private const string DEFAULT_PASSWORD = '';
    private const string DEFAULT_DATABASE = '';
    private const string DEFAULT_CHARSET = 'utf8mb4';

    public function __construct(
        public readonly string $host,
        public readonly int $port = self::DEFAULT_PORT,
        public readonly string $user = self::DEFAULT_USER,
        public readonly string $password = self::DEFAULT_PASSWORD,
        public readonly string $database = self::DEFAULT_DATABASE,
        public readonly string $charset = self::DEFAULT_CHARSET,
    ) {
    }

    public static function fromEnvironment(array $envVars, string $address): self
    {
        return new self(
            $address,
            user: $envVars['MYSQL_USER'] ?? self::DEFAULT_USER,
            password: $envVars['MYSQL_PASSWORD'] ?? self::DEFAULT_PASSWORD,
            database: $envVars['MYSQL_DATABASE'] ?? self::DEFAULT_DATABASE
        );
    }

    public function dsn(): string
    {
        if ($this->database) {
            return sprintf(
                'mysql:host=%s;port=%d;dbname=%s;charset=%s',
                $this->host,
                $this->port,
                $this->database,
                $this->charset
            );
        }

        return sprintf('mysql:host=%s;port=%d;charset=%s', $this->host, $this->port, $this->charset);
    }
}
