<?php

namespace Lemberg\LaravelCsc\Tests;

use Lemberg\LaravelCsc\Console\CodeStyleCommand;
use PHPUnit\Framework\TestCase;

/**
 * Class CodeStyleCommandTest
 * @package Lemberg\LaravelCsc\Tests
 *
 * TODO Invent working tests :)
 */
class CodeStyleCommandTest extends TestCase
{

    public function testUselessInstance()
    {
        $instance = new CodeStyleCommand();

        $this->assertInstanceOf(CodeStyleCommand::class, $instance);
    }
}
