<?php

namespace Webchecker\Utilities;
class ConfigLoader
{

    private array $config;

    public function __construct(string $name = "config.json")
    {
        $configPath = dirname(__DIR__, 2) . '/config/' . $name;
        if (!file_exists($configPath)) {
            throw new \Exception("Config file not found: " . $configPath);
        }
        $configContent = file_get_contents($configPath);
        $this->config = json_decode($configContent, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("Error parsing config file: " . json_last_error_msg());
        }
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->config[$key] ?? $default;
    }

    public function getAll(): array
    {
        return $this->config;
    }

    public function set(string $key, mixed $value): void
    {
        $this->config[$key] = $value;
    }

    public function save(string $name = "config.json"): void
    {
        $configPath = dirname(__DIR__, 2) . '/config/' . $name;
        $configContent = json_encode($this->config, JSON_PRETTY_PRINT);
        if (file_put_contents($configPath, $configContent) === false) {
            throw new \Exception("Error saving config file: " . $configPath);
        }
    }

    public function reload(string $name = "config.json"): void
    {
        $configPath = dirname(__DIR__, 2) . '/config/' . $name;
        if (!file_exists($configPath)) {
            throw new \Exception("Config file not found: " . $configPath);
        }
        $configContent = file_get_contents($configPath);
        $this->config = json_decode($configContent, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("Error parsing config file: " . json_last_error_msg());
        }
    }

    public function delete(string $key): void
    {
        unset($this->config[$key]);
    }

    public function clear(): void
    {
        $this->config = [];
    }

    public function exists(string $key): bool
    {
        return array_key_exists($key, $this->config);
    }

    public function count(): int
    {
        return count($this->config);
    }

    public function keys(): array
    {
        return array_keys($this->config);
    }

}

?>