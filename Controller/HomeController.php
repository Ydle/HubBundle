<?php

namespace Ydle\HubBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomeController extends Controller
{
    public function indexAction()
    {
        return $this->render('YdleHubBundle:Home:index.html.twig', array('mainpage' => 'home', 'rooms' => array()));
    }
}
