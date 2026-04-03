<?php

namespace Jinx\SapB1\Filters;

class IsNull extends Filter
{
  private string $field;

  public function __construct(string $field)
  {
    $this->field = $field;
  }

  public function execute(): string
  {
    return "{$this->field} eq null";
  }
}
