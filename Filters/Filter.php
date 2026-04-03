<?php

namespace Jinx\SapB1\Filters;

abstract class Filter
{
  private string $op;

  /**
   * Sets the logical operator (and/or) for the filter.
   * 
   * @param string $op
   * @return void
   */
  public function setOperator(string $op): void
  {
    $this->op = $op;
  }

  /**
   * Gets the logical operator.
   * 
   * @return string
   */
  public function getOperator(): string
  {
    return $this->op;
  }

  /**
   * Escapes values for OData filter strings.
   * 
   * @param mixed $value
   * @return string|mixed
   */
  public function escape($value)
  {
    if (is_string($value)) {
      $value = str_replace("'", "''", $value);
      return "'".$value."'";
    }
    return $value;
  }

  /**
   * Generates the OData filter string.
   * 
   * @return string
   */
  abstract public function execute(): string;
}
