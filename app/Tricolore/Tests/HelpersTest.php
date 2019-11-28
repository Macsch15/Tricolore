<?php
namespace Tricolore\Tests;

class HelpersTest extends \PHPUnit_Framework_TestCase
{
    public function testStartsWithTrue()
    {
        $string = 'Example string';
        $actual = startsWith('Exa', $string, 3);

        $this->assertTrue($actual);
    }

    public function testStartsWithFalse()
    {
        $string = 'Example string';
        $actual = startsWith('Ex', $string, 3);

        $this->assertFalse($actual);
    }

    public function testStartsWithDefaultLenTrue()
    {
        $string = 'Example string';
        $actual = startsWith('E', $string);

        $this->assertTrue($actual);
    }

    public function testStartsWithDefaultLenFalse()
    {
        $string = 'Example string';
        $actual = startsWith('M', $string);

        $this->assertFalse($actual);
    }

    public function testEndsWithTrue()
    {
        $string = 'Example string';
        $actual = endsWith('ing', $string, 3);

        $this->assertTrue($actual);
    }

    public function testEndsWithFalse()
    {
        $string = 'Example string';
        $actual = endsWith('ple', $string, 3);

        $this->assertFalse($actual);
    }

    public function testEndsWithDefaultLenTrue()
    {
        $string = 'Example string';
        $actual = endsWith('g', $string);

        $this->assertTrue($actual);
    }

    public function testEndsWithDefaultLenFalse()
    {
        $string = 'Example string';
        $actual = endsWith('w', $string);

        $this->assertFalse($actual);
    }

    public function testStartsWithIgnoreWhitespacesTrue()
    {
        $string = ' Example string';
        $actual = startsWith('Exa', $string, 3, true);

        $this->assertTrue($actual);
    }

    public function testStartsWithIgnoreWhitespacesFalse()
    {
        $string = ' Example string';
        $actual = startsWith('Exa', $string, 3, false);

        $this->assertFalse($actual);
    }

    public function testEndsWithIgnoreWhitespacesTrue()
    {
        $string = 'Example string ';
        $actual = endsWith('ing', $string, 3, true);

        $this->assertTrue($actual);
    }

    public function testEndsWithIgnoreWhitespacesFalse()
    {
        $string = 'Example string ';
        $actual = endsWith('ing', $string, 3, false);

        $this->assertFalse($actual);
    }

    public function testUnderscoreToStudlyCaps()
    {
        $string = 'example_string';
        $expected = 'ExampleString';
        $actual = underscoreToStudlyCaps($string);

        $this->assertEquals($expected, $actual);
    }
}
