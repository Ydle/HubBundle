<?php

namespace Ydle\HubBundle\Tests\Functionnal\Settings;

use Ydle\HubBundle\Entity\NodeType;
use Ydle\HubBundle\Entity\RoomType;

use Ydle\HubBundle\Tests\DataBaseTestCase;
use Ydle\HubBundle\Tests\Helper;

class ConfigControllerTypeRoomTest extends DataBaseTestCase
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

        $this->crawler = $this->helper->logIn($this->client, 'adminTest', 'test');
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @group configTypeRoom
     */
    public function testTypeRoom()
    {
        $this->crawler = $this->client->request('GET', '/conf/typeroom');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('Ydle\HubBundle\Controller\ConfigController::typeroomAction', $this->client->getRequest()->attributes->get('_controller'));
    }

    /**
     * @group configTypeRoom
     */
    public function testCreateOrEditRoom()
    {
        $this->client->request('GET', '/room/type.json');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('ydle.settings.roomtype.controller:getRoomTypeAction', $this->client->getRequest()->attributes->get('_controller'));

        $formDatas = array(
            'submit' => 'submit',
            'datas' => array(
                'room_types[name]' => 'Nom Test',
                'room_types[description]' => 'Room Description'
            ),
            'token' => 'room_types[_token]'
        );

        // Création d'un roomType
        $this->crawler = $this->checkForm('/conf/typeroom/form', 'POST', $formDatas);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('{"result":"success","message":"Room type added successfully"}', $this->client->getResponse()->getContent());
        $this->assertEquals('Ydle\HubBundle\Controller\ConfigController::typeroomFormAction', $this->client->getRequest()->attributes->get('_controller'));

        // Edition d'un roomType
        $this->crawler = $this->checkForm('/conf/typeroom/form/4', 'POST', $formDatas);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('{"result":"success","message":"Room type modified successfully"}', $this->client->getResponse()->getContent());
    }

    /**
     * @group configTypeRoom
     */
    public function testRoomTypeState()
    {
        $this->client->request('PUT', '/room/type/state.json?roomtype_id=1&state=0');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('true', $this->client->getResponse()->getContent());
        $this->assertEquals('ydle.settings.roomtype.controller:putRoomTypeStateAction', $this->client->getRequest()->attributes->get('_controller'));

        $this->client->request('PUT', '/room/type/state.json?roomtype_id=1&state=1');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('true', $this->client->getResponse()->getContent());
        $this->assertEquals('ydle.settings.roomtype.controller:putRoomTypeStateAction', $this->client->getRequest()->attributes->get('_controller'));

        $this->client->request('PUT', '/room/type/state.json?roomtype_id=666&state=0');
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
        $this->assertContains('This room type does not exist', $this->client->getResponse()->getContent());
        $this->assertEquals('ydle.settings.roomtype.controller:putRoomTypeStateAction', $this->client->getRequest()->attributes->get('_controller'));
    }
    
    /**
     * @group configTypeRoom
     */
    public function testDeleteRoomtype()
    {
        $this->client->request('DELETE', '/room/type.json?roomtype_id=4');
        // TODO : Avant le code retour était 200 avec en plus un message de retour. Régression ?
        $this->assertEquals(204, $this->client->getResponse()->getStatusCode());
        //$this->assertEquals('"Room type deleted successfully"', $this->client->getResponse()->getContent());
        $this->assertEquals('ydle.settings.roomtype.controller:deleteRoomTypeAction', $this->client->getRequest()->attributes->get('_controller'));

        $this->client->request('DELETE', '/room/type.json?roomtype_id=666');
        // TODO : Avant le code retour était 200 avec en plus un message de retour. Régression ?
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
        $this->assertContains('This room type does not exist', $this->client->getResponse()->getContent());
        $this->assertEquals('ydle.settings.roomtype.controller:deleteRoomTypeAction', $this->client->getRequest()->attributes->get('_controller'));
    }

    /**
     * @group configTypeRoom
     */
    public function testgetRoomTypeDetail()
    {
        $this->crawler = $this->client->request('GET', '/room/type/detail.json?roomtype_id=4');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertContains('{"id":4,"name":"Bathroom","description":"Bathroom Desc","is_active":true,"rooms":[],', $this->client->getResponse()->getContent());
        $this->assertEquals('ydle.settings.roomtype.controller:getRoomTypeDetailAction', $this->client->getRequest()->attributes->get('_controller'));

        $this->crawler = $this->client->request('GET', '/room/type/detail.json?roomtype_id=666');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertContains('ok', $this->client->getResponse()->getContent());
        $this->assertEquals('ydle.settings.roomtype.controller:getRoomTypeDetailAction', $this->client->getRequest()->attributes->get('_controller'));
    }

    /**
     * @group configTypeRoom
     */
    public function testCreateOrEditNode()
    {
        $formDatas = array(
            'submit' => 'submit',
            'datas' => array(
                'nodetypes_form[name]' => 'Nom Test',
                'nodetypes_form[unit]' => 'unit Test',
                'nodetypes_form[description]' => 'Room Description',
                'nodetypes_form[is_active]' => 1
            ),
            'token' => 'nodetypes_form[_token]'
        );

        // Création d'un nodeType
        $this->crawler = $this->checkForm('/conf/typenode/form', 'POST', $formDatas);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('{"result":"success","message":"Node type added successfully"}', $this->client->getResponse()->getContent());
        $this->assertEquals('Ydle\HubBundle\Controller\ConfigController::typenodeFormAction', $this->client->getRequest()->attributes->get('_controller'));

        // Edition d'un nodeType
        $this->crawler = $this->checkForm('/conf/typenode/form/4', 'POST', $formDatas);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('{"result":"success","message":"Node type modified successfully"}', $this->client->getResponse()->getContent());
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
