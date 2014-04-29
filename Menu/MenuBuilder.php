<?php

namespace Ydle\HubBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\TranslatorInterface;

class MenuBuilder
{
    private $factory;

    /**
     * @param FactoryInterface $factory
     */
    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function createMainMenu(Request $request, TranslatorInterface $translator)
    {
        $menu = $this->factory->createItem('root');
        $menu->setCurrentUri($request->getRequestUri());

        // home
        $menu->addChild('title.dashboard', array('route' => 'homeYdle', 'attributes' => array('icon' => 'fa fa-bar-chart-o')));
        
        // Rooms
        $menu->addChild('title.rooms', array(
            'route' => 'rooms', 
            'attributes' => array(
                'class' => 'treeview', 
                'icon' => 'fa fa-home',
                'treeview' => true
             ), 
            'childrenAttributes' => array('class' =>'treeview-menu')
        ));
        //$menu->addChild('title.rooms')->setChildrenAttribute('class', 'test');
        $menu->getChild('title.rooms')->addChild('title.rooms.all', array(
            'route' => 'rooms',
            'attributes' => array(
                'icon' => 'fa fa-angle-double-right'
            )
        ));
        $menu->getChild('title.rooms')->addChild('title.rooms.new', array(
            'route' => 'rooms',
            'attributes' => array(
                'icon' => 'fa fa-angle-double-right'
            )
        ));
        
        // Nodes
        $menu->addChild('title.nodes', array(
            'route' => 'nodes', 
            'attributes' => array(
                'icon' => 'fa fa-dot-circle-o',
                'treeview' => true
             )
        ));
        
        // Settings
        $configGeneralTitle = $translator->trans('title.config.general');
        $configRoomsTitle = $translator->trans('title.config.rooms');
        $configNodesTitle = $translator->trans('title.config.nodes');
        $menu->addChild('title.config', array(
            'route' => 'configDashboard', 
            'attributes' => array(
                'icon' => 'fa fa-cogs',
                'treeview' => true
            )
        ));
        
        // Logs
        $menu->addChild('title.logs', array(
            'route' => 'pagesAbout', 
            'attributes' => array(
                'icon' => 'fa fa-list'
            )
        ));
        
        // About
        $menu->addChild('title.about', array(
            'route' => 'pagesAbout',
            'attributes' => array(
                'icon' => 'fa fa-question-circle'
            )
        ));

        $menu->setChildrenAttribute('class', 'sidebar-menu');

        return $menu;
    }

}
