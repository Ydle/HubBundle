<?php
/*
  This file is part of Ydle.

    Ydle is free software: you can redistribute it and/or modify
    it under the terms of the GNU  Lesser General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Ydle is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU  Lesser General Public License for more details.

    You should have received a copy of the GNU Lesser General Public License
    along with Ydle.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace Ydle\HubBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Ydle\HubBundle\Entity\Node;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class NodesController extends Controller
{
    public function indexAction(Request $request)
    {
        return $this->render('YdleHubBundle:Nodes:index.html.twig', array(
            )
        );
    }
    
    /**
     * Display a form to create or edit a node.
     * 
     * @param Request $request
     */
    public function nodesFormAction(Request $request)
    {
        $node = new Node();
        // Manage edition mode
        $this->currentNode = $request->get('node');
        if($this->currentNode){
            $node = $this->get("ydle.node.manager")->find($request->get('node'));
        }
        $action = $this->get('router')->generate('submitNodeForm', array('node' => $this->currentNode));

        $form = $this->createForm("node_form", $node);
        $form->handleRequest($request);
        
       
	return $this->render('YdleHubBundle:Nodes:form.html.twig', array(
            'action' => $action,
            'entry' => $node,
            'form' => $form->createView()
        ));
    }

    public function submitNodeFormAction(Request $request)
    {
        $statusCode = 200;
        $node = new Node();
        // Manage edition mode
        $this->currentNode = $request->get('node');
        if($this->currentNode){
            $node = $this->get("ydle.node.manager")->find($request->get('node'));
        }
        $action = $this->get('router')->generate('submitNodeForm', array('node' => $this->currentNode));
        
	$form = $this->createForm("node_form", $node);
        $form->handleRequest($request);
        
	if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($node);
            $em->flush();
            $message = $this->get('translator')->trans('node.add.success');
            if($node->getId()){
                $message = $this->get('translator')->trans('node.edit.success');
            }
            $this->get('session')->getFlashBag()->add('notice', $message);
            $this->get('ydle.logger')->log('info', $message, 'hub');
            $statusCode = 201;
        } else {
            $statusCode = 400;
        }

	$html =  $this->renderView('YdleHubBundle:Nodes:form.html.twig', array(
            'action' => $action,
            'form' => $form->createView()
        ));
        
        $response = new Response();
        $response->setContent($html);
        $response->setStatusCode($statusCode);        
        $response->headers->set('Content-Type', 'text/html');
        return $response;
    }
    
    
    /**
     * Reset a node, sending an http request to the master 
     * and a 433 mhz request then
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return type
     */
    public function resetAction(Request $request)
    {
        $object = $this->get("ydle.nodes.manager")->getRepository()->find($request->get('node'));
              
        
        $this->get('ydle.logger')->log('info', 'Initialization signal sent to node #'.$object->getCode());
        $this->get('session')->getFlashBag()->add('notice', 'Reset envoyé');
        return $this->redirect($this->generateUrl('nodes'));
    }
    
    
    /**
     * Create a link with a node, sending an http request to the master 
     * and a 433 mhz request then
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return type
     */
    public function linkAction(Request $request)
    {
        $object = $this->get("ydle.nodes.manager")->getRepository()->find($request->get('node'));
        
        $address = $this->container->getParameter('master_address');
        $address .= ':8888/node/link?target='.$object->getCode().'&sender=';
        $address .= $this->container->getParameter('master_id');
        
        $ch = curl_init($address);
        curl_exec($ch);
        curl_close($ch);
        
        $this->get('ydle.logger')->log('info', 'Initialization signal sent to node #'.$object->getCode());
        $this->get('session')->getFlashBag()->add('notice', 'Link action envoyée');
        return $this->redirect($this->generateUrl('nodes'));
    }
    
    /**
    * Manage activation of a node
    * 
    * @param Request $request
    */
    public function activationAction(Request $request)
    {
        $isActive = $request->get('active');
        $message = $isActive?'Node activated':'Node deactivated';
        $object = $this->get("ydle.nodes.manager")->getRepository()->find($request->get('node'))->setIsActive($isActive);
        $em = $this->getDoctrine()->getManager();                                                                         
        $em->persist($object);
        $em->flush();
        $this->get('session')->getFlashBag()->add('notice', $message);
        
        if($isActive){
           $this->get('ydle.logger')->log('info', 'Node #'.$object->getCode().' activated', 'hub');
        } else {
           $this->get('ydle.logger')->log('info', 'Node #'.$object->getCode().' deactivated', 'hub');
        }
        return $this->redirect($this->generateUrl('nodes'));
    }
    
    /**
    * Manage initialization of a node
    * 
    * @param Request $request
    */
    public function initializeAction(Request $request)
    {
        $isActive = $request->get('active');
        $message = $isActive?'Node activated':'Node deactivated';
        $object = $this->get("ydle.nodes.manager")->getRepository()->find($request->get('node'))->setIsActive($isActive);
        $em = $this->getDoctrine()->getManager();                                                                         
        $em->persist($object);
        $em->flush();
        $this->get('session')->getFlashBag()->add('notice', $message);
        return $this->redirect($this->generateUrl('nodes'));
    }
    
    public function sensorsAction(Request $request)
    {
        $msg = 'ok';
        $sensors = array();
        if(!$node = $this->get("ydle.nodes.manager")->getRepository()->find($request->get('node'))){
            $msg = 'ko';
        } else {
            foreach($node->getTypes() as $type)
            {
                $sensors[] = $type->getId();
            }
        }
        return new JsonResponse(array('msgReturn' => $msg, 'data' => $sensors, 'nodeId' => $node->getId(), 'roomId' => $node->getRoom()->getId()));
    }
}
