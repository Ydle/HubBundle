<?php

namespace Ydle\HubBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\HttpFoundation\Request;

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

    public function createMainMenu(Request $request)
    {
        $menu = $this->factory->createItem('root');
        $menu->setCurrentUri($request->getRequestUri());

        $menu->addChild('Accueil', array('route' => 'homeYdle'));
        $menu->addChild('Pièces', array('route' => 'rooms'));
        $menu->addChild('Capteurs', array('route' => 'nodes'));
        $menu->addChild('Configuration', array('route' => 'configDashboard'));
        $menu->addChild('A propos', array('route' => 'pagesAbout'));
        
        $menu->setChildrenAttribute('class', 'nav navbar-nav');

        return $menu;
    }
}
