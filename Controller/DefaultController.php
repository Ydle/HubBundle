<?php

namespace Ydle\HubBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Ydle\HubBundle\Entity\NodeData;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('YdleHubBundle:Default:index.html.twig', array('name' => $name));
    }
}
