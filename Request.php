<?php

namespace Jinx\SapB1;

/**
 * Handles PHP cURL requests to the SAP B1 Service Layer.
 * Supports headers, cookies, and manual response parsing.
 */
class Request
{
  protected string $url;
  protected array $sslOptions = [];
  protected string $method = 'GET';
  protected ?array $postParams = null;
  protected array $cookies = [];
  protected array $headers = [];

  /**
   * Initializes a new Request instance.
   */
  public function __construct(string $url, array $sslOptions = [])
  {
    $this->url = $url;
    $this->sslOptions = $sslOptions;
  }

  /**
   * Sets the HTTP request method.
   * 
   * @param string $method
   * @return $this
   */
  public function setMethod(string $method): self
  {
    $this->method = $method;
    return $this;
  }

  /**
   * Sets the request body data (JSON encoded).
   * 
   * @param array<string, mixed>|null $postParams
   * @return $this
   */
  public function setPost(?array $postParams): self
  {
    $this->postParams = $postParams;
    return $this;
  }

  /**
   * Sets session cookies for the request.
   * 
   * @param array<string, string> $cookies
   * @return $this
   */
  public function setCookies(array $cookies): self
  {
    $this->cookies = $cookies;
    return $this;
  }

  /**
   * Sets custom HTTP headers for the request.
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
   * Executes the cURL request and returns a Response object.
   * @throws \Exception on cURL failure.
   */
  public function getResponse(): Response
  {
    $headers = [];
    foreach ($this->headers as $key => $value) {
      $headers[] = "{$key}: {$value}";
    }
    
    if (null !== $this->postParams && 'GET' !== $this->method) {
      $headers[] = 'Content-Type: application/json';
    }

    $cookieStrings = [];
    foreach ($this->cookies as $name => $value) {
      $cookieStrings[] = "{$name}={$value}";
    }
    if (!empty($cookieStrings)) {
      $headers[] = 'Cookie: ' . implode('; ', $cookieStrings);
    }

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $this->url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->method);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 45);

    if (isset($this->sslOptions['verify_peer'])) {
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->sslOptions['verify_peer']);
    }
    if (isset($this->sslOptions['verify_peer_name'])) {
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $this->sslOptions['verify_peer_name'] ? 2 : 0);
    }

    if (null !== $this->postParams && 'GET' !== $this->method) {
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this->postParams));
    }

    $response = curl_exec($ch);

    if (false === $response) {
      $error = curl_error($ch);
      curl_close($ch);
      throw new \Exception('cURL Error: ' . $error);
    }

    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $headerContent = substr($response, 0, $headerSize);
    $body = substr($response, $headerSize);

    $parsedHeaders = [];
    $parsedCookies = [];
    
    foreach (explode("\r\n", $headerContent) as $i => $line) {
      if ($i === 0 || empty($line)) {
        continue;
      }

      $parts = explode(': ', $line, 2);
      if (count($parts) === 2) {
        $key = $parts[0];
        $value = $parts[1];
        
        $parsedHeaders[$key] = $value;

        if (strtolower($key) === 'set-cookie') {
          $cookieParts = explode(';', $value);
          if (!empty($cookieParts[0])) {
            $kv = explode('=', $cookieParts[0], 2);
            if (count($kv) === 2) {
              $parsedCookies[trim($kv[0])] = trim($kv[1]);
            }
          }
        }
      }
    }

    return new Response($statusCode, $parsedHeaders, $parsedCookies, $body);
  }
}
