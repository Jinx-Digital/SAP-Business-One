<?php

namespace Jinx\SapB1;

/**
 * Manages configuration settings for the SAP B1 connection.
 */
class Config
{
  private array $config = [];

  /**
   * Initializes a new Config instance.
   */
  public function __construct(array $config)
  {
    $this->config = $config;
  }

  /**
   * Gets the full Service Layer URL for a given service.
   * 
   * @param string $serviceName
   * @return string
   */
  public function getServiceUrl(string $serviceName): string
  {
    $scheme = true === ($this->get('https')) ? 'https' : 'http';  
    return "{$scheme}://{$this->get('host')}:{$this->get('port', 50000)}/b1s/v{$this->get('version', 2)}/{$serviceName}";
  }

  /**
   * Gets the URL for custom tables.
   * 
   * @param string $table
   * @return string
   */
  public function getCustomTableUrl(string $table): string
  {
    $scheme = true === ($this->get('https')) ? 'https' : 'http'; 
    return "{$scheme}://{$this->get('host')}:{$this->get('port', 50000)}/custom-table/v1/customTables/{$table}";
  }

  /**
   * Gets the SSL options for cURL.
   * 
   * @return array<string, mixed>
   */
  public function getSSLOptions(): array
  {
    return $this->get('sslOptions', []);
  }

  /**
   * Gets the SAP Username.
   * 
   * @return string
   */
  public function getUsername(): string
  {
    return $this->get('username', '');
  }

  /**
   * Gets the SAP Password.
   * 
   * @return string
   */
  public function getPassword(): string
  {
    return $this->get('password', '');
  }

  /**
   * Gets the SAP Company Database name.
   * 
   * @return string
   */
  public function getCompany(): string
  {
    return $this->get('company', '');
  }

  /**
   * Returns config as array.
   * 
   * @return array<string, mixed>
   */
  public function toArray(): array
  {
    return $this->config;
  }

  /**
   * Retrieves a configuration value by key.
   * 
   * @param string $name
   * @param mixed $default
   * @return mixed
   */
  private function get(string $name, $default = null)
  {
    return $this->config[$name] ?? $default;
  }
}
