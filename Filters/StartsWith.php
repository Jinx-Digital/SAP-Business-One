<?php

namespace Jinx\SapB1\Filters;

class StartsWith extends Filter
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
    return "startswith({$this->field},{$this->escape($this->value)})";
  }
}
