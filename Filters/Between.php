<?php

namespace Jinx\SapB1\Filters;

class Between extends Filter
{
  private string $field;
  private $fromValue;
  private $toValue;

  public function __construct(string $field, $fromValue, $toValue)
  {
    $this->field = $field;
    $this->fromValue = $fromValue;
    $this->toValue = $toValue;
  }

  public function execute(): string
  {
    return "({$this->field} ge {$this->escape($this->fromValue)} and {$this->field} le {$this->escape($this->toValue)})";
  }
}
