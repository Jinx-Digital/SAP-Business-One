<?php

namespace Jinx\SapB1;

/**
 * The Client class is the main entry point for the SAP B1 REST API.
 * It handles authentication and provides access to various services.
 */
class Client
{
  private Config $config;
  private array $session = [];

  public function __construct(array $configOptions)
  {
    $this->config = new Config($configOptions);
  }
  
  /**
   * Returns the current SAP B1 session cookies.
   * 
   * @return array<string, string>
   */
  public function getSession(): array
  {
    return $this->session;
  }

  /**
   * Sets the SAP B1 session cookies manually.
   * 
   * @param array<string, string> $session
   * @return $this
   */
  public function setSession(array $session): self
  {
    $this->session = $session;
    return $this;
  }

  /**
   * Returns a Service instance for the specified SAP B1 entity.
   * 
   * @param string $serviceName
   * @return Service
   */
  public function getService(string $serviceName): Service
  {
    return new Service($this->config, $this->session, $serviceName);
  }

  /**
   * Shorthand for the Items service.
   * 
   * @return Service
   */
  public function items(): Service
  {
    return $this->getService('Items');
  }

  /**
   * Shorthand for the BusinessPartners service.
   * 
   * @return Service
   */
  public function businessPartners(): Service
  {
    return $this->getService('BusinessPartners');
  }

  /**
   * Shorthand for the Orders service.
   * 
   * @return Service
   */
  public function orders(): Service
  {
    return $this->getService('Orders');
  }

  /**
   * Shorthand for the Invoices service.
   * 
   * @return Service
   */
  public function invoices(): Service
  {
    return $this->getService('Invoices');
  }

  /**
   * Shorthand for the DeliveryNotes service.
   * 
   * @return Service
   */
  public function deliveryNotes(): Service
  {
    return $this->getService('DeliveryNotes');
  }

  /**
   * Authenticates with SAP B1 and returns a new Client instance.
   * 
   * @param array<string, mixed> $configOptions Configuration including host, username, password, company, etc.
   * @return static
   * @throws ClientException if authentication fails.
   */
  public static function new(array $configOptions): self
  {
    $config = new Config($configOptions);

    $request = new Request($config->getServiceUrl('Login'), $config->getSSLOptions());
    $request->setMethod('POST');
    $request->setPost([
      'UserName'  => $config->getUsername(),
      'Password'  => $config->getPassword(),
      'CompanyDB' => $config->getCompany()
    ]);
    
    $response = $request->getResponse(); 
    
    if (200 === $response->getStatusCode()) {
      $client = new self($configOptions);
      $client->setSession($response->getCookies());
      return $client;
    }
    
    throw new ClientException($response);
  }
}
