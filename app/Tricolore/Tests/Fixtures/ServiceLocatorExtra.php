<?php
namespace Tricolore\Tests\Fixtures;

class ServiceLocatorExtra
{
    public function getInstance()
    {
        return $this;
    }

    public function stringReturn()
    {
        return 'Hello World';
    }

    public function myFunc()
    {
        return 'myFunc';
    }
}
