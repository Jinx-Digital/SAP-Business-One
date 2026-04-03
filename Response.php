<?php

namespace Jinx\SapB1;

/**
 * Wrapper for the SAP B1 HTTP response.
 */
class Response
{
  protected int $statusCode;
  protected array $headers;
  protected array $cookies;
  protected string $body;

  /**
   * Initializes a new Response instance.
   */
  public function __construct(int $statusCode, array $headers = [], array $cookies = [], string $body = '')
  {
    $this->statusCode = $statusCode;
    $this->headers = $headers;
    $this->cookies = $cookies;
    $this->body = $body;
  }

  /**
   * Returns the HTTP status code.
   */
  public function getStatusCode(): int
  {
    return $this->statusCode;
  }

  /**
   * Returns specific or all response headers.
   * 
   * @param string $header
   * @return string|array<string, string>|null
   */
  public function getHeaders(string $header = '')
  {
    if ('' !== $header) {
      if (array_key_exists($header, $this->headers)) {
        return $this->headers[$header];
      }
      return null;
    }
    return $this->headers;
  }

  /**
   * Returns extracted session cookies.
   * 
   * @return array<string, string>
   */
  public function getCookies(): array
  {
    return $this->cookies;
  }

  /**
   * Returns the raw response body.
   */
  public function getBody(): string
  {
    return $this->body;
  }

  /**
   * Returns the body as a JSON object (stdClass).
   */
  public function getJson(): \stdClass
  {
    if ('' !== $this->body) {
      return json_decode($this->body) ?: new \stdClass();
    }
    return new \stdClass();
  }

  /**
   * Returns true if status code is in 2xx range.
   */
  public function isOk(): bool
  {
    return $this->statusCode >= 200 && $this->statusCode < 300;
  }

  /**
   * Parses and returns the SAP error message if available.
   */
  public function getErrorMessage(): ?string
  {
    $json = $this->getJson();
    if (isset($json->error->message->value)) {
      return $json->error->message->value;
    }
    return null;
  }
}
