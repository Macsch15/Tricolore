<?php
namespace Tricolore\Tests;

use Tricolore\Application;

class TranslationTest extends \PHPUnit_Framework_TestCase
{
    public function testStandardTrans()
    {
        $service_locator = $this->getMockForAbstractClass('Tricolore\Services\ServiceLocator');
        $service_trans = $service_locator->get('translator', [
            Application::createPath('app:Tricolore:Tests:Fixtures:Translation_enEN.xliff'),
        ]);

        $expected = 'Translated unit!';
        $actual = $service_trans->trans('Example unit');

        $this->assertEquals($expected, $actual);
    }

    public function testPlaceholderTrans()
    {
        $service_locator = $this->getMockForAbstractClass('Tricolore\Services\ServiceLocator');
        $service_trans = $service_locator->get('translator', [
            Application::createPath('app:Tricolore:Tests:Fixtures:Translation_enEN.xliff'),
        ]);

        $expected = 'Welcome Peter';
        $actual = $service_trans->trans('Hello %name%', ['%name%' => 'Peter']);

        $this->assertEquals($expected, $actual);
    }

    public function testPluralizationTrans()
    {
        $service_locator = $this->getMockForAbstractClass('Tricolore\Services\ServiceLocator');
        $service_trans = $service_locator->get('translator', [
            Application::createPath('app:Tricolore:Tests:Fixtures:Translation_enEN.xliff'),
        ]);

        $expected = 'Bob has 6 apples';
        $actual = $service_trans->transChoice('There is one apple|There are %count% apples', 6, ['%count%' => 6]);

        $this->assertEquals($expected, $actual);        
    }

    public function testTwigIntegration()
    {
        $service_locator = $this->getMockForAbstractClass('Tricolore\Services\ServiceLocator');
        $service_view = $service_locator->get('view');

        $expected = 'Translated template';
        $actual = $service_view->display(null, 'TestTranslationTemplate', [], true);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @expectedException Tricolore\Exception\AssetNotFound
     */
    public function testExceptionResourceNotFound()
    {
        $service_locator = $this->getMockForAbstractClass('Tricolore\Services\ServiceLocator');

        return $service_locator->get('translator', ['fake/path']);
    }
}
