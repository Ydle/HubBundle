<?php

namespace Ydle\HubBundle\Tests\Functionnal\Settings;

use Ydle\HubBundle\Entity\NodeType;
use Ydle\HubBundle\Entity\RoomType;

use Ydle\HubBundle\Tests\DataBaseTestCase;
use Ydle\HubBundle\Tests\Helper;

class ConfigControllerNodeTypeTest extends DataBaseTestCase
{
    protected $client;
    protected $crawler;
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    public $container;

    public function setup()
    {
        parent::setup();
        $this->helper = new Helper();
        $this->client = static::createClient();
        $this->client->followRedirects();
        $this->container = $this->client->getContainer();

        //Invalidate latest session
        $this->container->get('session')->invalidate();

        $this->truncateTable('fos_user');
        $this->truncateTable('roomtype');
        $this->truncateTable('sensortype');
        $this->loadContext();

        $this->helper->logIn($this->client, 'adminTest', 'test');
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @group configNodeType
     */
    public function testTypenode()
    {
        $this->client->request('GET', '/conf/typenode');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('Ydle\HubBundle\Controller\ConfigController::typenodeAction', $this->client->getRequest()->attributes->get('_controller'));
    }

    /**
     * @group configNodeType
     */
    public function testListBadParamNodeType()
    {
        $this->client->request('GET', '/node/type.json?page=2&count=1&room_id=');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('ydle.settings.nodetype.controller:getNodeTypeAction', $this->client->getRequest()->attributes->get('_controller'));
    }

    /**
     * @group configNodeType
     */
    public function testTypeCreateOrEditnode()
    {
        $this->client->request('GET', '/node/type.json');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('ydle.settings.nodetype.controller:getNodeTypeAction', $this->client->getRequest()->attributes->get('_controller'));

        $formDatas = array(
            'submit' => 'submit',
            'datas' => array(
                'nodetypes_form[name]' => 'Nom Test',
                'nodetypes_form[description]' => 'Unité Description',
                'nodetypes_form[unit]' => 'unité test'
            ),
            'token' => 'nodetypes_form[_token]'
        );

        // Création d'un nodeType
        $this->crawler = $this->checkForm('/conf/typenode/form', 'POST', $formDatas);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('{"result":"success","message":"Node type added successfully"}', $this->client->getResponse()->getContent());
        $this->assertEquals('Ydle\HubBundle\Controller\ConfigController::typenodeFormAction', $this->client->getRequest()->attributes->get('_controller'));

        // Edition d'un nodeType
        $this->crawler = $this->checkForm('/conf/typenode/form/2', 'POST', $formDatas);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('{"result":"success","message":"Node type modified successfully"}', $this->client->getResponse()->getContent());
        $this->assertEquals('Ydle\HubBundle\Controller\ConfigController::typenodeFormAction', $this->client->getRequest()->attributes->get('_controller'));
    }

    /**
     * @group configNodeType
     */
    public function testDeleteNode()
    {
        $this->client->request('DELETE', '/node/type.json?nodetype_id=4');
        // TODO : Avant le code retour était 200 avec en plus un message de retour. Régression ?
        $this->assertEquals(204, $this->client->getResponse()->getStatusCode());
        //$this->assertEquals('"Node type deleted successfully"', $this->client->getResponse()->getContent());
        $this->assertEquals('ydle.settings.nodetype.controller:deleteNodeTypeAction', $this->client->getRequest()->attributes->get('_controller'));

        $this->client->request('DELETE', '/node/type.json?nodetype_id=999');
        // TODO : Avant le code retour était 200 avec en plus un message de retour. Régression ?
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
        $this->assertContains('This node type does not exist', $this->client->getResponse()->getContent());
        $this->assertEquals('ydle.settings.nodetype.controller:deleteNodeTypeAction', $this->client->getRequest()->attributes->get('_controller'));
    }

    /**
     * @group configNodeType2
     */
    public function testTypenodeDetails()
    {
        $this->client->request('GET', '/node/type/detail.json?nodetype_id=1');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('ydle.settings.nodetype.controller:getNodeTypeDetailAction', $this->client->getRequest()->attributes->get('_controller'));

        $this->client->request('GET', '/node/type/detail.json?nodetype_id=666');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('ydle.settings.nodetype.controller:getNodeTypeDetailAction', $this->client->getRequest()->attributes->get('_controller'));
    }

    /**
     * @group configNodeType
     */
    public function testTypenodeState()
    {
        $this->client->request('PUT', '/node/type/state.json?nodetype_id=4&state=0');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('true', $this->client->getResponse()->getContent());
        $this->assertEquals('ydle.settings.nodetype.controller:putNodeTypeStateAction', $this->client->getRequest()->attributes->get('_controller'));

        $this->client->request('PUT', '/node/type/state.json?nodetype_id=4&state=1');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('true', $this->client->getResponse()->getContent());
        $this->assertEquals('ydle.settings.nodetype.controller:putNodeTypeStateAction', $this->client->getRequest()->attributes->get('_controller'));

        $this->client->request('PUT', '/node/type/state.json?nodetype_id=999&state=0');
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
        $this->assertContains('This node type does not exist', $this->client->getResponse()->getContent());
        $this->assertEquals('ydle.settings.nodetype.controller:putNodeTypeStateAction', $this->client->getRequest()->attributes->get('_controller'));

        $this->client->request('PUT', '/node/type/state.json?nodetype_id=999&state=1');
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
        $this->assertContains('This node type does not exist', $this->client->getResponse()->getContent());
        $this->assertEquals('ydle.settings.nodetype.controller:putNodeTypeStateAction', $this->client->getRequest()->attributes->get('_controller'));
    }

    private function loadContext()
    {
        $this->createAdmin('adminTest', 'test');

        $rtA = new RoomType();
        $rtA->setName("Living Room");
        $rtA->setDescription("Living Room Desc");
        $rtA->setIsActive(true);
        $rtA->setCreatedAt(new \DateTime('now'));
        $this->em->persist($rtA);

        $rtB = new RoomType();
        $rtB->setName("Bedroom");
        $rtB->setDescription("Bedroom Desc");
        $rtB->setIsActive(true);
        $rtB->setCreatedAt(new \DateTime('now'));
        $this->em->persist($rtB);

        $rtC = new RoomType();
        $rtC->setName("Garage");
        $rtC->setDescription("Garage Desc");
        $rtC->setIsActive(true);
        $rtC->setCreatedAt(new \DateTime('now'));
        $this->em->persist($rtC);

        $rtD = new RoomType();
        $rtD->setName("Bathroom");
        $rtD->setDescription("Bathroom Desc");
        $rtD->setIsActive(true);
        $rtD->setCreatedAt(new \DateTime('now'));
        $this->em->persist($rtD);

        $rtE = new RoomType();
        $rtE->setName("Toilet");
        $rtE->setDescription("Toilet Desc");
        $rtE->setIsActive(true);
        $rtE->setCreatedAt(new \DateTime('now'));
        $this->em->persist($rtE);

        $ntA = new NodeType();
        $ntA->setName("Temperature");
        $ntA->setUnit('°C');
        $ntA->setIsActive(true);
        $ntA->setCreatedAt(new \DateTime('now'));
        $this->em->persist($ntA);

        $ntB = new NodeType();
        $ntB->setName("Humidity");
        $ntB->setUnit('%');
        $ntB->setIsActive(true);
        $ntB->setCreatedAt(new \DateTime('now'));
        $this->em->persist($ntB);

        $ntC = new NodeType();
        $ntC->setName("Pressure");
        $ntC->setUnit('Pa');
        $ntC->setIsActive(true);
        $ntC->setCreatedAt(new \DateTime('now'));
        $this->em->persist($ntC);

        $ntD = new NodeType();
        $ntD->setName("Luminosity");
        $ntD->setUnit('lux');
        $ntD->setIsActive(true);
        $ntD->setCreatedAt(new \DateTime('now'));
        $this->em->persist($ntD);

        $this->em->flush();
    }
}
