<?php

namespace Jinx\SapB1\Filters;

class Raw extends Filter
{
  private string $value;

  public function __construct(string $value)
  {
    $this->value = $value;
  }

  public function execute(): string
  {
    return $this->value;
  }
}
