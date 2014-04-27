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
        $dashboardTitle = $translator->trans('title.dashboard');
        $menu->addChild($dashboardTitle, array('route' => 'homeYdle'));
        
        // Rooms
        $roomsTitle = $translator->trans('title.rooms');
        $allRoomsTitle = $translator->trans('title.rooms.all');
        $newRoomTitle = $translator->trans('title.rooms.new');
        $menu->addChild($roomsTitle, array('route' => 'rooms'));
        
        // Nodes
        $nodesTitle = $translator->trans('title.nodes');
        $allNodesTitle = $translator->trans('title.nodes.all');
        $newNodeTitle = $translator->trans('title.nodes.new');
        $menu->addChild($nodesTitle, array('route' => 'nodes'));
        
        // Settings
        $configTitle = $translator->trans('title.config');
        $configGeneralTitle = $translator->trans('title.config.general');
        $configRoomsTitle = $translator->trans('title.config.rooms');
        $configNodesTitle = $translator->trans('title.config.nodes');
        $menu->addChild($configTitle, array('route' => 'configDashboard'));
        
        // Logs
        $logsTitle = $translator->trans('title.logs');
        $menu->addChild(logsTitle, array('route' => 'pagesAbout'));
        
        // About
        $aboutTitle = $translator->trans('title.about');
        $menu->addChild($aboutTitle, array('route' => 'pagesAbout'));

        $menu->setChildrenAttribute('class', 'sidebar-menu');

        return $menu;
    }

}
