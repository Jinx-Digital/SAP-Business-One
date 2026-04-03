<?php

namespace Jinx\SapB1\Tests;

use PHPUnit\Framework\TestCase;
use Jinx\SapB1\Config;

class ConfigTest extends TestCase
{
    public function testServiceUrlGeneration()
    {
        $config = new Config([
            'host' => '1.2.3.4',
            'port' => 50000,
            'version' => 2,
            'https' => true
        ]);

        $this->assertEquals('https://1.2.3.4:50000/b1s/v2/Items', $config->getServiceUrl('Items'));
    }

    public function testCustomTableUrlGeneration()
    {
        $config = new Config([
            'host' => 'localhost',
            'port' => 50000,
            'https' => false
        ]);

        $this->assertEquals('http://localhost:50000/custom-table/v1/customTables/MY_TABLE', $config->getCustomTableUrl('MY_TABLE'));
    }
}
