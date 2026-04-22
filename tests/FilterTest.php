<?php

namespace Jinx\SapB1\Tests;

use PHPUnit\Framework\TestCase;
use Jinx\SapB1\Config;
use Jinx\SapB1\Query;
use Jinx\SapB1\Filters\Equal;
use Jinx\SapB1\Filters\MoreThan;
use Jinx\SapB1\Filters\InArray;
use Jinx\SapB1\Filters\IsNull;

class FilterTest extends TestCase
{
    private $config;

    protected function setUp(): void
    {
        $this->config = new Config(['host' => 'localhost']);
    }

    public function testSimpleEqualFilter()
    {
        $q = new Query($this->config, [], 'Items', []);
        $q->where(new Equal('ItemCode', 'A001'));
        $this->assertEquals("ItemCode eq 'A001'", $q->buildFilter());
    }

    public function testMultipleConditionsAnd()
    {
        $q = new Query($this->config, [], 'Items', []);
        $q->where(new Equal('ItemCode', 'A001'))
          ->andWhere(new MoreThan('OnHand', 10));
        $this->assertEquals("ItemCode eq 'A001' and OnHand gt 10", $q->buildFilter());
    }

    public function testMultipleConditionsOr()
    {
        $q = new Query($this->config, [], 'Items', []);
        $q->where(new Equal('Group', 'Hardware'))
          ->orWhere(new Equal('Group', 'Software'));
        $this->assertEquals("Group eq 'Hardware' or Group eq 'Software'", $q->buildFilter());
    }

    public function testNestedSubQueries()
    {
        $q = new Query($this->config, [], 'Items', []);
        $sub = $q->subQuery()->where(new Equal('A', 1))->orWhere(new Equal('B', 2));
        $q->where(new Equal('Main', 0))->andWhere($sub);
        $this->assertEquals("Main eq 0 and (A eq 1 or B eq 2)", $q->buildFilter());
    }

    public function testMagicWhereMethods()
    {
        $q = new Query($this->config, [], 'Items', []);
        $q->whereItemCode('M100')->wherePrice(29.99);
        $this->assertEquals("ItemCode eq 'M100' and Price eq 29.99", $q->buildFilter());
    }

    public function testSpecialFilters()
    {
        $q = new Query($this->config, [], 'Items', []);
        $q->where(new InArray('Status', ['Active', 'Pending']))
          ->andWhere(new IsNull('UpdateDate'));
        $this->assertEquals("(Status eq 'Active' or Status eq 'Pending') and UpdateDate eq null", $q->buildFilter());
    }
}
