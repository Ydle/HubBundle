<?php

namespace Ydle\HubBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Ydle\HubBundle\Entity\NodeData;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
	$node = $this->get('')->find(1);
	$type = $this->get('')->find(1);
	for($i = 0; $i < 1000; $i ++)
	{
		$nodeData = new NodeData();
		$nodeData->setType($type);
		$nodeData->setNode($node);

	}
        return $this->render('YdleHubBundle:Default:index.html.twig', array('name' => $name));
    }
}
