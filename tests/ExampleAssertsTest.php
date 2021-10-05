<?php

declare(strict_types=1);

namespace App\Tests;

use PHPUnit\Framework\TestCase;

class ExampleAssertsTest extends TestCase
{
    public function testThatStringsMatch()
    {
        $str1 = 'testing';
        $str2 = 'testing';
        $str3 = 'notTesting';

        $this->assertSame($str1, $str2);
        $this->assertNotSame($str1, $str3);
        $this->assertNotSame($str2, $str3);
    }

    public function testThatNumbersAddUp()
    {
        $this->assertEquals(10, 5 + 5);
        $this->assertNotEquals(10, 5 - 5);
    }

    public function testThatBooleanIs()
    {
        $this->assertTrue(true);
        $this->assertTrue(1 === 1);
        $this->assertFalse(false);
        $this->assertFalse(1 === '1');
    }
}
