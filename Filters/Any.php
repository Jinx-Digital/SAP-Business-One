<?php

namespace Jinx\SapB1\Filters;

class Any extends Filter
{
  private string $collection;
  private Filter $filter;
  private string $alias;

  public function __construct(string $collection, Filter $filter, string $alias = 'x')
  {
    $this->collection = $collection;
    $this->filter = $filter;
    $this->alias = $alias;
  }

  public function execute(): string
  {
    return "{$this->collection}/any({$this->alias}: {$this->filter->execute()})";
  }
}
