<?php
namespace Tricolore\Controller\Frontend;

use Tricolore\Services\ServiceLocator;

class IndexAction extends ServiceLocator
{
    /**
     * @Route('/', name="home")
     */
    public function index()
    {
        return $this->get('view')->display('Actions/Frontend', 'IndexAction');
    }
}
