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

        // home
        $menu->addChild('title.dashboard', array('route' => 'homeYdle', 'attributes' => array('icon' => 'fa fa-bar-chart-o')));
        
        // Rooms
        $menu->addChild('title.rooms', array(
            'route' => 'rooms', 
            'attributes' => array(
                'class' => '', 
                'icon' => 'fa fa-home',
        //        'treeview' => true
             ), 
            'childrenAttributes' => array('class' =>'treeview-menu')
        ));
        //$menu->addChild('title.rooms')->setChildrenAttribute('class', 'test');
        /*$menu->getChild('title.rooms')->addChild('title.rooms.all', array(
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
        */
        // Nodes
        $menu->addChild('title.nodes', array(
            'route' => 'nodes', 
            'attributes' => array(
                'icon' => 'fa fa-dot-circle-o',
                'treeview' => true
             )
        ));
        
        // Settings
        $configNodesTitle = $translator->trans('title.config.nodes');
        $menu->addChild('title.config', array(
            'route' => 'configYdle', 
            'attributes' => array(
                'class' => 'treeview', 
                'icon' => 'fa fa-cogs',
                'treeview' => true
            ),
            'childrenAttributes' => array('class' =>'treeview-menu')
        ));
        $menu->getChild('title.config')->addChild('title.config.general', array(
            'route' => 'configYdle',
            'attributes' => array(
                'icon' => 'fa fa-angle-double-right'
            )
        ));
        $menu->getChild('title.config')->addChild('title.config.rooms', array(
            'route' => 'configTypeRoom',
            'attributes' => array(
                'icon' => 'fa fa-angle-double-right'
            )
        ));
        $menu->getChild('title.config')->addChild('title.config.nodes', array(
            'route' => 'configTypeNode',
            'attributes' => array(
                'icon' => 'fa fa-angle-double-right'
            )
        ));
        
        // Logs
        $menu->addChild('title.logs', array(
            'route' => 'logs', 
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
