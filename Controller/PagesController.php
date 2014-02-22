<?php

namespace Ydle\HubBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PagesController extends Controller
{
    public function indexAction()
    {
        return $this->render('YdleHubBundle:Pages:index.html.twig', array('mainpage' => 'about'));
    }
}
