<?php

namespace Jinx\SapB1\Filters;

class InArray extends Filter
{
  private string $field;
  private array $collection;

  public function __construct(string $field, array $collection)
  {
    $this->field = $field;
    $this->collection = $collection;
  }

  public function execute(): string
  {
    $items = array_map(
      fn($value) => "{$this->field} eq {$this->escape($value)}",
      $this->collection
    );

    return "(".implode(' or ', $items).")";
  }
}
