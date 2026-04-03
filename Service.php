<?php

namespace Jinx\SapB1;

/**
 * Represents an individual OData service (entity set) in SAP B1.
 */
class Service
{
  private Config $config;
  private array $session;
  private string $serviceName;
  private array $headers = [];

  /**
   * Initializes a new Service instance.
   */
  public function __construct(Config $config, array $session, string $serviceName)
  {
    $this->config = $config;
    $this->session = $session;
    $this->serviceName = $serviceName;
  }

  /**
   * Retrieves a single entity by its primary key.
   */
  public function find(string|int $id): Response
  {
    if (is_string($id)) {
      $id = "'".str_replace("'", "''", $id)."'";
    }

    return $this->doRequest('GET', $this->config->getServiceUrl($this->serviceName)."({$id})");
  }

  /**
   * Alias for find() to improve semantic clarity for documents (Orders, Invoices, etc.)
   */
  public function document(string|int $id): Response
  {
    return $this->find($id);
  }

  /**
   * Accesses custom tables endpoints.
   */
  public function tableEntries(): Response
  {
    return $this->doRequest('GET', $this->config->getCustomTableUrl($this->serviceName).'/entries');
  }

  /**
   * Retrieves the binary stream ($value) for a specific entity (e.g. an item image or an attachment).
   */
  public function attachment(string|int $id, ?string $filename = null): Response
  {
    if (is_string($id)) {
      $id = "'".str_replace("'", "''", $id)."'";
    }

    $url = $this->config->getServiceUrl($this->serviceName)."({$id})/\$value";

    if (null !== $filename) {
      $url .= "?filename='{$filename}'";
    }

    return $this->doRequest('GET', $url);
  }

  /**
   * Creates a new entity (POST).
   */
  public function create(array $data): Response
  {
    return $this->doRequest('POST', $this->config->getServiceUrl($this->serviceName), $data);
  }

  /**
   * Updates an entity using PATCH.
   */
  public function update(string|int $id, array $data): Response
  {
    if (is_string($id)) {
      $id = "'".str_replace("'", "''", $id)."'";
    }

    return $this->doRequest('PATCH', $this->config->getServiceUrl($this->serviceName)."({$id})", $data);
  }

  /**
   * Updates an entity using PUT.
   */
  public function put(string|int $id, array $data): Response
  {
    if (is_string($id)) {
      $id = "'".str_replace("'", "''", $id)."'";
    }

    return $this->doRequest('PUT', $this->config->getServiceUrl($this->serviceName)."({$id})", $data);
  }

  /**
   * Deletes an entity (DELETE).
   */
  public function delete(string|int $id): Response
  {
    if (is_string($id)) {
      $id = "'".str_replace("'", "''", $id)."'";
    }

    return $this->doRequest('DELETE', $this->config->getServiceUrl($this->serviceName)."({$id})");
  }

  /**
   * Executes a custom action on an entity (POST).
   */
  public function action(string|int $id, string $action): Response
  {
    if (is_string($id)) {
      $id = "'".str_replace("'", "''", $id)."'";
    }

    return $this->doRequest('POST', $this->config->getServiceUrl($this->serviceName)."({$id})/{$action}");
  }

  /**
   * Returns a new Query builder for this service.
   */
  public function query(): Query
  {
    return new Query($this->config, $this->session, $this->serviceName, $this->headers);
  }

  /**
   * Overrides request headers for this service instance.
   * 
   * @param array<string, string> $headers
   * @return $this
   */
  public function setHeaders(array $headers): self
  {
    $this->headers = $headers;
    return $this;
  }

  /**
   * Executes a manual HTTP request against the service layer.
   * 
   * @param string $method
   * @param string $path
   * @param array<string, mixed> $postData
   * @return Response
   */
  public function doRequest(string $method, string $path, array $postData = []): Response
  {
    $request = new Request($path, $this->config->getSSLOptions());
    $request->setMethod($method);
    $request->setCookies($this->session);
    $request->setHeaders($this->headers);

    if ('GET' !== $method) {
      $request->setPost($postData);
    }

    return $request->getResponse();
  }
}
