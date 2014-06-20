<?php

namespace Ydle\HubBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class HomeController extends Controller
{
    public function indexAction()
    {
        //$rooms = $this->get("ydle.rooms.manager")->retrieve(array('limit' => 3));
        return $this->render('YdleHubBundle:Home:index.html.twig', array('mainpage' => 'home', 'rooms' => array()));
    }
}
