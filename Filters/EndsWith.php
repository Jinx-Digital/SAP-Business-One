<?php

namespace Jinx\SapB1\Filters;

class EndsWith extends Filter
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
    return "endswith({$this->field},{$this->escape($this->value)})";
  }
}
