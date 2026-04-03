<?php

namespace Jinx\SapB1\Filters;

class Equal extends Filter
{
  private string $field;
  private $value;

  public function __construct(string $field, $value)
  {
    $this->field = $field;
    $this->value = $value;
  }

  public function execute(): string
  {
    return "{$this->field} eq {$this->escape($this->value)}";
  }
}
