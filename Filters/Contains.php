<?php

namespace Jinx\SapB1\Filters;

class Contains extends Filter
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
    return "contains({$this->field},{$this->escape($this->value)})";
  }
}
