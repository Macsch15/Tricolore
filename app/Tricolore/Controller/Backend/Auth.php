<?php
namespace Tricolore\Controller\Backend;

use Tricolore\FormFactory\FormTypes\Backend\AuthType;
use Tricolore\Services\ServiceLocator;
use Symfony\Component\HttpFoundation\Request;

class Auth extends ServiceLocator
{
    /**
     * @Route('/admincp/auth', name="admincp_auth")
     */
    public function process()
    {
        $form = $this->get('form')->create(new AuthType(), [
            'translator' => $this->get('translator')
        ]);

        $form->handleRequest(Request::createFromGlobals());

        $validated = false;

        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $validated = true;
        }

        $render = [
            'form' => $form->createView(),
            'is_valid' => $validated
        ];

        return $this->get('view')->display('Actions/Backend', 'Auth', $render);
    }
}
