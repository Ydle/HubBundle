<?php

namespace Ydle\HubBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('YdleHubBundle:Default:index.html.twig', array('name' => $name));
    }
}
