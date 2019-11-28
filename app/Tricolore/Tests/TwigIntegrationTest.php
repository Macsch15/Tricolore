<?php
namespace Tricolore\Tests;

use Tricolore\Config\Config;

class TwigIntegrationTest extends \PHPUnit_Framework_TestCase
{
    private function getView()
    {
        return $this->getMockForAbstractClass('Tricolore\Services\ServiceLocator')
        ->get('view');
    }

    public function testAssetFunction()
    {
        $expected = 'http://localhost/Tricolore/tests/' . Config::key('directory.assets') . '/css/foo';
        $actual = $this->getView()->display(null, 'TestAsset', [], true);

        $this->assertEquals($expected, $actual);
    }

    public function testConfigFunction()
    {
        $expected = 'en';
        $actual = $this->getView()->display(null, 'TestConfig', [], true);

        $this->assertEquals($expected, $actual);
    }

    public function testAppGlobal()
    {
        $expected = 'test';
        $actual = $this->getView()->display(null, 'TestAppGlobal', [], true);

        $this->assertEquals($expected, $actual);
    }

    public function testUrlFunction()
    {
        $expected = 'http://localhost/Tricolore/tests/index.php?/my/test';
        $actual = $this->getView()->display(null, 'TestUrl', [], true);

        $this->assertEquals($expected, $actual);
    }

    public function testUrlFunctionNoRoute()
    {
        $expected = 'http://localhost/Tricolore/tests';
        $actual = $this->getView()->display(null, 'TestUrlNotRoute', [], true);

        $this->assertEquals($expected, $actual);
    }

    public function testTwigEnvironmentInstance()
    {
        $expected = 'Twig_Environment';
        $actual = $this->getView()->getEnv();

        $this->assertInstanceOf($expected, $actual);
    }
}
