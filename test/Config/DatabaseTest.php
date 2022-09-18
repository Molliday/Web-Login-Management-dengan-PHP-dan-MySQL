<?php

namespace Pzn\BelajarPhpMvc\Config;

use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
{
    public function testGetConnection()
    {
        $connection = Database::getConnetion();
        self::assertNotNull($connection);
    }
    public function testGetConnectionSingleton()
    {
        $connection1 = Database::getConnetion();
        $connection2 = Database::getConnetion();
        self::assertSame($connection1, $connection2);
    }
}
