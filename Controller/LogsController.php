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

use Ydle\HubBundle\Entity\Logs;


class LogsController extends Controller
{

    /**
     * Homepage for logs
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return type
     */
    public function indexAction(Request $request)
    {
        $query = $this->get('ydle.logger')->createViewLogQuery();

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1)/*page number*/,
            25/*limit per page*/
        );
        
        $logs = new Logs();
        $form = $this->createForm("logsfilter_form", $logs);
        return $this->render('YdleHubBundle:Logs:index.html.twig', array(
            'pagination' => $pagination,
            'form' => $form->createView()
        ));
    }
    
    public function resetAction(Request $request)
    {
        $this->get('ydle.logger')->reset();
        
        $this->get('session')->getFlashBag()->add('notice', 'You logs table is now empty');
        return $this->redirect($this->generateUrl('logs'));
    }
}
