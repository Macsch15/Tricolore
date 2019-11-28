<?php
namespace Tricolore\Controller\Frontend;

use Tricolore\Services\ServiceLocator;

class HelloAction extends ServiceLocator
{
    /**
     * @Route('/hello/{name}', name="hello")
     */
    public function sayHello($name)
    {
        $render = [
            'name' => $name
        ];

        return $this->get('view')->display('Actions/Frontend', 'HelloAction', $render);
    }
}
