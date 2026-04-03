<?php

namespace Jinx\SapB1\Filters;

class Not extends Filter
{
  private Filter $filter;

  public function __construct(Filter $filter)
  {
    $this->filter = $filter;
  }

  public function execute(): string
  {
    return 'not (' . $this->filter->execute() . ')';
  }
}
