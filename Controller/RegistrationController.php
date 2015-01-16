<?php

namespace Ydle\HubBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\UserBundle\Controller\RegistrationController as BaseController;
use Symfony\Component\HttpFoundation\Request;

class RegistrationController extends BaseController
{
    public function registerAction(Request $request)
    {
        return new RedirectResponse($this->container->get('router')->generate('homeYdle'));
    }
}
