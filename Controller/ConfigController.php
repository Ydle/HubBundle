<?php

namespace Ydle\HubBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Ydle\HubBundle\Entity\NodeType;
use Ydle\HubBundle\Entity\RoomType;

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
     * @param  \Symfony\Component\HttpFoundation\Request $request
     * @return type
     */
    public function typeroomAction(Request $request)
    {
        $roomType = new RoomType();

        $form = $this->createForm("room_types", $roomType);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($roomType);
            $em->flush();
            $message = 'roomtype.add.success';
            if ($roomType->getId()) {
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
        if ($typeId = $request->get('type')) {
            $roomType = $this->get("ydle.roomtype.manager")->find($typeId);
        }
        $action = $this->get('router')->generate('configTypeRoomForm', array('type' => $typeId));

        $response = new Response();
        $form = $this->createForm("room_types", $roomType);
        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $message = $this->get('translator')->trans('roomtype.add.success');
                if ($roomType->getId()) {
                    $message = $this->get('translator')->trans('roomtype.edit.success');
                }
                $em->persist($roomType);
                $em->flush();
                
                $this->get('ydle.logger')->log('info', $message, 'hub');
                
                return new JsonResponse('Type room saved successfully', 200);
            } else {
                $response->setStatusCode(400);
            }
        }

        return $this->render('YdleHubBundle:Config:typeroomForm.html.twig', array(
            'action' => $action,
            'form' => $form->createView()), $response
        );
    }

    public function typenodeFormAction(Request $request)
    {
        $result = "ok";
        $nodeType = new NodeType();
        if ($typeId = $request->get('type')) {
            $nodeType = $this->get("ydle.nodetype.manager")->find($typeId);
        }
        $action = $this->get('router')->generate('configTypeNodeForm', array('type' => $typeId));

        $response = new Response();
        $form = $this->createForm("nodetypes_form", $nodeType);
        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $message = $this->get('translator')->trans('nodetype.add.success');
                if ($nodeType->getId()) {
                    $message = $this->get('translator')->trans('nodetype.edit.success');
                }
                $em->persist($nodeType);
                $em->flush();

                $this->get('ydle.logger')->log('info', $message, 'hub');
                
                return new JsonResponse('Type node saved successfully', 200);
            } else {
                $response->setStatusCode(400);
            }
        }

        return $this->render('YdleHubBundle:Config:typenodeForm.html.twig', array(
            'action' => $action,
            'form' => $form->createView()), $response
        );
    }

    /**
     * Homepage for type sensors, listing and editing types
     *
     * @param  \Symfony\Component\HttpFoundation\Request $request
     * @return type
     */
    public function typenodeAction(Request $request)
    {
        $types = $this->get("ydle.nodetype.manager")->findAllByName();
        $sensorType = new NodeType();

        // Manage edition mode
        $this->currentType = $request->get('type');
        if ($this->currentType) {
            $sensorType = $this->get("ydle.nodetype.manager")->getRepository()->find($request->get('type'));
        }
        $form = $this->createForm("nodetypes_form", $sensorType);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($sensorType);
            $em->flush();
            $message = 'Type de capteur ajoutée avec succès';
            if ($sensorType->getId()) {
                $message = 'Type de capteur modifié avec succès';
            }
            $this->get('session')->getFlashBag()->add('notice', $message);

            return $this->redirect($this->generateUrl('configTypeSensor'));
        }

        return $this->render('YdleHubBundle:Config:typenode.html.twig', array(
            'form' => $form->createView(),
            'current' => 'typesensor',
            'mainpage' => 'config',
            'items' => $types
        ));
    }

    public function dashboardAction()
    {
        return $this->render('YdleHubBundle:Config:dashboard.html.twig', array('current' => 'dashboard', 'mainpage' => 'config'));
    }

}
