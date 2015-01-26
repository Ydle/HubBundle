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
        if ($this->currentNode) {
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
        if ($this->currentNode) {
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
            if ($node->getId()) {
                $message = $this->get('translator')->trans('node.edit.success');
            }
            $this->get('session')->getFlashBag()->add('notice', $message);
            $this->get('ydle.logger')->log('info', $message, 'hub');
            
            return new JsonResponse('Node saved successfully', 200);
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


    public function sensorsAction(Request $request)
    {
        $msg = 'ok';
        $sensors = array();
        if (!$node = $this->get("ydle.nodes.manager")->getRepository()->find($request->get('node'))) {
            $msg = 'ko';
        } else {
            foreach ($node->getTypes() as $type) {
                $sensors[] = $type->getId();
            }
        }

        return new JsonResponse(array('msgReturn' => $msg, 'data' => $sensors, 'nodeId' => $node->getId(), 'roomId' => $node->getRoom()->getId()));
    }
}
