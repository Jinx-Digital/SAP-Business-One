<?php

namespace Jinx\SapB1\Filters;

class IsNotNull extends Filter
{
  private string $field;

  public function __construct(string $field)
  {
    $this->field = $field;
  }

  public function execute(): string
  {
    return "{$this->field} ne null";
  }
}
