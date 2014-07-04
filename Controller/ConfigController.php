<?php

namespace Ydle\HubBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Ydle\RoomBundle\Entity\RoomType;
use Ydle\RoomBundle\Entity\Room;
use Ydle\NodesBundle\Entity\SensorType;
use Ydle\NodesBundle\Entity\Node;
use Ydle\HubBundle\Entity\NodeData;

class ConfigController extends Controller
{
    public function indexAction(Request $request)
    {
        return $this->render('YdleHubBundle:Config:index.html.twig', array(
        ));
    }

    /**
     * Homepage for type room, listing and editing types
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return type
     */
    public function typeroomAction(Request $request)
    {
        $setttings = $this->get('ydle.settings.controller');
   	$roomType = new RoomType();

	$form = $this->createForm("room_types", $roomType);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($roomType);
            $em->flush();
            $message = 'roomtype.add.success';
            if($roomType->getId()){
                $message = 'roomtype.edit.success';
            }
            
            $response = new JsonResponse();
            $data = array(
                'result' => 'ok',
                'message' => $message
            );
            $response->setData($data);
            return $response;
        }

        return $this->render('YdleHubBundle:Config:typeroom.html.twig', array(
            'form' => $form->createView()
        ));
    }
    
    public function typeroomFormAction(Request $request)
    {
        $result = "ok";
   	$roomType = new RoomType();
        if($typeId = $request->get('type')){
            $roomType = $this->get("ydle.roomtype.manager")->find($typeId);
        }
        $action = $this->get('router')->generate('configTypeRoomForm', array('type' => $typeId));
        
	$form = $this->createForm("room_types", $roomType);
        if($request->isMethod('POST')){
            $form->bind($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($roomType);
                $em->flush();
                $message = 'roomtype.add.success';
                if($roomType->getId()){
                    $message = 'roomtype.edit.success';
                }

                $response = new JsonResponse();
                $result = 'success';
                $data = array(
                    'result' => $result,
                    'message' => $message
                );
                $response->setData($data);
                return $response;
            } else{
                $result = 'error';
            }
        }

        return $this->render('YdleHubBundle:Config:typeroomForm.html.twig', array(
            'action' => $action,
            'form' => $form->createView())
        );
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
    public function typenodeAction(Request $request)
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
    public function typenodedeleteAction(Request $request)
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
    public function typenodeactivationAction(Request $request)
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
    }
}
