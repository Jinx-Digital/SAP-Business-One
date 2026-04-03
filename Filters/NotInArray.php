<?php

namespace Jinx\SapB1\Filters;

class NotInArray extends Filter
{
  private string $field;
  private array $values;

  public function __construct(string $field, array $values)
  {
    $this->field = $field;
    $this->values = $values;
  }

  public function execute(): string
  {
    $values = array_map(fn($v) => $this->escape($v), $this->values);
    return "not ({$this->field} in (".implode(',', $values)."))";
  }
}
