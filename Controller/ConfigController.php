<?php

namespace Ydle\HubBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Ydle\RoomBundle\Entity\RoomType;
use Ydle\RoomBundle\Entity\Room;
use Ydle\NodesBundle\Entity\SensorType;
use Ydle\NodesBundle\Entity\Node;
use Ydle\HubBundle\Entity\NodeData;

class ConfigController extends Controller
{
    public function indexAction()
    {
        return $this->render('YdleHubBundle:Config:dashboard.html.twig', array('current' => 'dashboard', 'mainpage' => 'config'));
    }

    /**
     * Homepage for type room, listing and editing types
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return type
     */
    public function typeroomAction(Request $request)
    {
        $types = $this->get("ydle.roomtypes.manager")->findAllByName();
        $roomType = new RoomType();
        
        // Manage edition mode
        $this->currentType = $request->get('type');
        if($this->currentType){
            $roomType = $this->get("ydle.roomtypes.manager")->getRepository()->find($request->get('type'));
        }
        $form = $this->createForm("room_types", $roomType);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($roomType);
            $em->flush();
            $message = 'Type de pièce ajoutée avec succès';
            if($roomType->getId()){
                $message = 'Type de pièce modifié avec succès';
            }
            $this->get('session')->getFlashBag()->add('notice', $message);
            return $this->redirect($this->generateUrl('configTypeRoom'));
        }

        return $this->render('YdleHubBundle:Config:typeroom.html.twig', array(
            'form' => $form->createView(), 
            'current' => 'typeroom', 
            'mainpage' => 'config',
            'items' => $types
        ));
    }
    
    /**
    * Manage activation of a room type
    * 
    * @param Request $request
    */
    public function typeroomactivationAction(Request $request)
    {
        $isActive = $request->get('active');
        $message = $isActive?'Type activé':'Type désactivé';
        $object = $this->get("ydle.roomtypes.manager")->getRepository()->find($request->get('type'))->setIsActive($isActive);
        $em = $this->getDoctrine()->getManager();                                                                         
        $em->persist($object);
        $em->flush();
        $this->get('session')->getFlashBag()->add('notice', $message);
        return $this->redirect($this->generateUrl('configTypeRoom'));
    }
    
    /**
     * Delete a type
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return type
     */
    public function typeroomdeleteAction(Request $request)
    {
        $object = $this->get("ydle.roomtypes.manager")->getRepository()->find($request->get('type'));
        $em = $this->getDoctrine()->getManager();                                                                         
        $em->remove($object);
        $em->flush();
        $this->get('session')->getFlashBag()->add('notice', 'Type supprimé');
        return $this->redirect($this->generateUrl('configTypeRoom'));
    }
    
    /**
     * Homepage for type sensors, listing and editing types
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return type
     */
    public function typesensorAction(Request $request)
    {
        $types = $this->get("ydle.sensortypes.manager")->findAllByName();
        $sensorType = new SensorType();
        
        // Manage edition mode
        $this->currentType = $request->get('type');
        if($this->currentType){
            $sensorType = $this->get("ydle.sensortypes.manager")->getRepository()->find($request->get('type'));
        }
        $form = $this->createForm("sensor_types", $sensorType);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($sensorType);
            $em->flush();
            $message = 'Type de capteur ajoutée avec succès';
            if($sensorType->getId()){
                $message = 'Type de capteur modifié avec succès';
            }
            $this->get('session')->getFlashBag()->add('notice', $message);
            return $this->redirect($this->generateUrl('configTypeSensor'));
        }

        return $this->render('YdleHubBundle:Config:typesensor.html.twig', array(
            'form' => $form->createView(), 
            'current' => 'typesensor', 
            'mainpage' => 'config',
            'items' => $types
        ));
    }
    
    /**
     * Delete a type
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return type
     */
    public function typesensordeleteAction(Request $request)
    {
        $object = $this->get("ydle.sensortypes.manager")->getRepository()->find($request->get('type'));
        $em = $this->getDoctrine()->getManager();                                                                         
        $em->remove($object);
        $em->flush();
        $this->get('session')->getFlashBag()->add('notice', 'Type supprimé');
        return $this->redirect($this->generateUrl('configTypeSensor'));
    }
    
    /**
    * Manage activation of a sensor type
    * 
    * @param Request $request
    */
    public function typesensoractivationAction(Request $request)
    {
        $isActive = $request->get('active');
        $message = $isActive?'Type activé':'Type désactivé';
        $object = $this->get("ydle.sensortypes.manager")->getRepository()->find($request->get('type'))->setIsActive($isActive);
        $em = $this->getDoctrine()->getManager();                                                                         
        $em->persist($object);
        $em->flush();
        $this->get('session')->getFlashBag()->add('notice', $message);
        return $this->redirect($this->generateUrl('configTypeSensor'));
    }

    public function dashboardAction()
    {
        return $this->render('YdleHubBundle:Config:dashboard.html.twig', array('current' => 'dashboard', 'mainpage' => 'config'));
    }
    
    public function populateAction()
    {
        ini_set('max_execution_time', 0);
    	$em = $this->getDoctrine()->getManager();
    	
    	// Delete from tables
    	//$em->createQuery('DELETE FROM node_sensor')->execute();
    	$em->createQuery('DELETE FROM YdleHubBundle:NodeData')->execute();
    	$em->createQuery('DELETE FROM YdleNodesBundle:Node')->execute();
    	$em->createQuery('DELETE FROM YdleNodesBundle:SensorType')->execute();
    	$em->createQuery('DELETE FROM YdleRoomBundle:Room')->execute();
    	$em->createQuery('DELETE FROM YdleRoomBundle:RoomType')->execute();
    	
    	// Creation des types de pièces
    	$types = array();
    	for($i = 0; $i < mt_rand(2, 4); $i++){
    		$roomType = new RoomType();
    		$roomType->setName('Type '.$i);
    		$roomType->setIsActive(true);                                                                      
	        $em->persist($roomType);
	        $types[] = $roomType;
    	}
    	
    	// Création des pièces
    	$rooms = array();
    	for($i = 0; $i < mt_rand(3, 7); $i++){
    		$room = new Room();
    		$room->setName('Pièce #'.$i);
    		$room->setType($types[mt_rand(0, count($types) - 1)]);
    		$room->setIsActive(true);                                                                    
	        $em->persist($room);
	        $rooms[] = $room;
    	}
    	
    	// Création des types de capteurs
   		$ctypes = array();
   		$units = array('t','°','%','m');
    	for($i = 0; $i < mt_rand(2, 4); $i++){
    		$nodeType = new SensorType();
    		$nodeType->setName('Type '.$i);
    		$nodeType->setIsActive(true);  
    		$nodeType->setUnit($units[mt_rand(0, 2)]);                                                                    
	        $em->persist($nodeType);
	        $ctypes[] = $nodeType;
    	}
    	
    	// Création des capteurs
    	$nodes = array();
    	for($i = 0; $i < mt_rand(4, 8); $i++)
    	{
    		$node = new Node();
    		$node->setName('test node #'.$i);
    		$node->setCode($i);
    		$node->setIsActive(true);
    		$node->setRoom($rooms[mt_rand(0, count($rooms) -1)]); 
    		
    		$nbSensors = mt_rand(1,2);
    		for($j = 0; $j < $nbSensors; $j++)
    		{
    			$newType = $ctypes[mt_rand(0, count($ctypes)-1)];
    			if(!$node->hasType($newType)){
    				$node->addType($newType);
    			}
    		}
	        $em->persist($node);
	        $nodes[] = $node;
    	}
    	
        $now = strtotime("now");
    	foreach($nodes as $n)
    	{
            foreach($n->getTypes() as $type)
            {
                $startDate = strtotime("1 january 2014");
                switch($type->getUnit()){
                    case 'm':
                        $data = 1000;
                        while($startDate < $now){
                            $dt = \Datetime::createFromFormat("U", $startDate);
                            $nodeData = new NodeData();
                            $nodeData->setType($type);
                            $nodeData->setNode($n);
                            $nodeData->setData($data);
                            $nodeData->setCreated($dt);
                            $nodeData->setUpdated($dt);
                            $em->persist($nodeData);
                            
                            $data += mt_rand(-30,30);
                            if($data < 0){ $data = 0; }
                            $startDate += 600;
                        }
                        break;
                    case '%':
                        $data = 600;
                        while($startDate < $now){
                            $dt = \Datetime::createFromFormat("U", $startDate);
                            $nodeData = new NodeData();
                            $nodeData->setType($type);
                            $nodeData->setNode($n);
                            $nodeData->setData($data);
                            $nodeData->setCreated($dt);
                            $nodeData->setUpdated($dt);
                            $em->persist($nodeData);
                            
                            $data += mt_rand(-10,10);
                            if($data < 0){ $data = 0; }
                            elseif($data > 100) { $data = 100; }
                            $startDate += 600;
                        }
                        break;
                    case '°':
                        $data = 1000;
                        while($startDate < $now){
                            $dt = \Datetime::createFromFormat("U", $startDate);
                            $nodeData = new NodeData();
                            $nodeData->setType($type);
                            $nodeData->setNode($n);
                            $nodeData->setData($data);
                            $nodeData->setCreated($dt);
                            $nodeData->setUpdated($dt);
                            $em->persist($nodeData);
                            
                            $data += mt_rand(-10,10);
                            $startDate += 600;
                        }
                        break;
                }
            }
    	}
    	
    	$em->flush();
    	
    	// Ajout des datas aux capteurs
        return $this->redirect($this->generateUrl('configDashboard'));
    }
}
