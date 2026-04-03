<?php

namespace Jinx\SapB1;

class ClientException extends \Exception
{
  protected int $statusCode;

  public function __construct(Response $response)
  {
    $this->statusCode = $response->getStatusCode();
    parent::__construct($response->getBody());
  }

  public function getStatusCode(): int
  {
    return $this->statusCode;
  }
}
