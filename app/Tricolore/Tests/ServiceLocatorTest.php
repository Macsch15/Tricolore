<?php
namespace Tricolore\Tests;

use Tricolore\Application;

class ServiceLocatorTest extends \PHPUnit_Framework_TestCase
{
    public function testServiceInstanceOf()
    {
        $service_locator = $this->getMockForAbstractClass('Tricolore\Services\ServiceLocator');
        $service_path = Application::createPath('app:Tricolore:Tests:Fixtures:ServiceLocator.yml');
        $service_extra = $service_locator->get('extra', [], $service_path);

        $expected = 'Tricolore\Tests\Fixtures\ServiceLocatorExtra';
        $actual = $service_extra->getInstance();

        $this->assertInstanceOf($expected, $actual);
    }

    /**
     * @expectedException Tricolore\Exception\ServicesException
     */
    public function testExceptionServiceNotExists()
    {
        $service_locator = $this->getMockForAbstractClass('Tricolore\Services\ServiceLocator');
        $service_path = Application::createPath('app:Tricolore:Tests:Fixtures:ServiceLocator.yml');

        return $service_locator->get('fake_foo', [], $service_path);
    }

    /**
     * @expectedException Tricolore\Exception\AssetNotFound
     */
    public function testExceptionWrongPath()
    {
        $service_locator = $this->getMockForAbstractClass('Tricolore\Services\ServiceLocator');

        return $service_locator->get('fake', [], 'fake/path');
    }

    public function testMethodReturn()
    {
        $service_locator = $this->getMockForAbstractClass('Tricolore\Services\ServiceLocator');
        $service_path = Application::createPath('app:Tricolore:Tests:Fixtures:ServiceLocator.yml');
        $service_extra = $service_locator->get('extra', [], $service_path);

        $expected = 'Hello World';
        $actual = $service_extra->getInstance()->stringReturn();

        $this->assertEquals($expected, $actual);
    }

    public function testServiceFunction()
    {
        $service_locator = $this->getMockForAbstractClass('Tricolore\Services\ServiceLocator');
        $service_path = Application::createPath('app:Tricolore:Tests:Fixtures:ServiceLocator.yml');

        $expected = 'myFunc';
        $actual = $service_locator->get('extra_func', [], $service_path);

        $this->assertEquals($expected, $actual);
    }
}
