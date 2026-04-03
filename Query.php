<?php

namespace Jinx\SapB1;

/**
 * Query builder for SAP B1 OData services.
 * Supports fluent interface and nested filter groups.
 */
class Query
{
  private Config $config;
  private array $session;
  private string $serviceName;
  private array $query = [];
  private array $filters = [];
  private array $headers = [];

  /**
   * Initializes a new Query instance.
   */
  public function __construct(Config $config, array $session, string $serviceName, array $headers)
  {
    $this->config = $config;
    $this->session = $session;
    $this->serviceName = $serviceName;
    $this->headers = $headers;
  }

  /**
   * Creates a new sub-query for nested filter grouping.
   */
  public function subQuery(): self
  {
    return new self($this->config, $this->session, $this->serviceName, $this->headers);
  }

  /**
   * Specifies the fields to return ($select).
   */
  public function select(string $fields = '*'): self
  {
    $this->query['select'] = $fields;
    return $this;
  }

  /**
   * Specifies top and skip for pagination ($top, $skip).
   */
  public function limit(int $top, int $skip = 0): self
  {
    $this->query['top'] = $top;
    $this->query['skip'] = $skip;
    return $this;
  }

  /**
   * Specifies how many results to skip ($skip).
   */
  public function skip(int $skip): self
  {
    $this->query['skip'] = $skip;
    return $this;
  }

  /**
   * Specifies the sort order ($orderby).
   */
  public function orderBy(string $field, string $direction = 'asc'): self
  {
    $this->query['orderby'] = "{$field} {$direction}";
    return $this;
  }

  /**
   * Includes the total count in the response ($inlinecount).
   */
  public function inlineCount(): self
  {
    $this->query['inlinecount'] = 'allpages';
    return $this;
  }

  private string $operator = 'and';

  public function setOperator(string $op): void
  {
    $this->operator = $op;
  }

  public function getOperator(): string
  {
    return $this->operator;
  }

  /**
   * Adds an AND filter condition or sub-query.
   * 
   * @param Filters\Filter|Query $filter
   * @return $this
   */
  public function where($filter): self
  {
    if ($filter instanceof Filters\Filter || $filter instanceof self) {
      $filter->setOperator('and');
      $this->filters[] = $filter;
    }
    return $this;
  }

  /**
   * An alias for where() for better semantic flow.
   * 
   * @param Filters\Filter|Query $filter
   * @return $this
   */
  public function andWhere($filter): self
  {
    return $this->where($filter);
  }

  /**
   * Adds an OR filter condition or sub-query.
   * 
   * @param Filters\Filter|Query $filter
   * @return $this
   */
  public function orWhere($filter): self
  {
    if ($filter instanceof Filters\Filter || $filter instanceof self) {
      $filter->setOperator('or');
      $this->filters[] = $filter;
    }
    return $this;
  }

  /**
   * Specifies navigation properties to expand ($expand).
   */
  public function expand($name): self
  {
    $this->query['expand'] = $name;
    return $this;
  }

  /**
   * Recursively builds the OData $filter string.
   */
  public function buildFilter(): string
  {
    $filterString = '';

    foreach ($this->filters as $idx => $filter) {
      $op = ($idx > 0) ? ' ' . $filter->getOperator() . ' ' : '';
      
      if ($filter instanceof self) {
        $content = '(' . $filter->buildFilter() . ')';
      } else {
        $content = $filter->execute();
      }
      
      $filterString .= $op . $content;
    }

    return $filterString;
  }

  /**
   * Executes the query and returns the total count of matching records.
   */
  public function count(): Response
  {
    return $this->doRequest('/$count');
  }

  /**
   * Executes the query and returns a collection of results.
   */
  public function find(): Response
  {
    return $this->doRequest();
  }

  private function doRequest(string $action = ''): Response
  {
    $requestQuery = '?';

    foreach ($this->query as $name => $value) {
      $requestQuery .= '$'.$name.'='.rawurlencode($value).'&';
    }

    $filter = $this->buildFilter();
    if ('' !== $filter) {
      $requestQuery .= '$filter=' . rawurlencode($filter);
    }

    $request = new Request($this->config->getServiceUrl($this->serviceName.$action).$requestQuery, $this->config->getSSLOptions());
    $request->setMethod('GET');
    $request->setHeaders($this->headers);
    $request->setCookies($this->session);

    return $request->getResponse();
  }

  /**
   * Magic method to handle shorthand where{Field}() calls.
   * 
   * @param string $name
   * @param array<int, mixed> $arguments
   * @return $this
   * @throws \BadMethodCallException if method does not start with 'where' or has no arguments.
   */
  public function __call(string $name, array $arguments): self
  {
    if (str_starts_with($name, 'where') && count($arguments) > 0) {
      $field = substr($name, 5);
      return $this->where(new Filters\Equal($field, $arguments[0]));
    }
    
    throw new \BadMethodCallException("Method {$name} does not exist.");
  }
}
