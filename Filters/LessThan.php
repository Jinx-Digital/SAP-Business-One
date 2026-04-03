<?php

namespace Jinx\SapB1\Filters;

class LessThan extends Filter
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
    return "{$this->field} lt {$this->escape($this->value)}";
  }
}
