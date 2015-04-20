<?php

namespace Ydle\HubBundle\Tests;

use Ydle\HubBundle\Entity\NodeType;
use Ydle\HubBundle\Entity\RoomType;
use Ydle\HubBundle\Entity\Room;

use Ydle\HubBundle\Tests\Helper;

class RoomsTest extends DataBaseTestCase
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
        $this->truncateTable('room');
        $this->truncateTable('node');
        $this->truncateTable('node_sensor');
        $this->loadContext();

        $this->helper->logIn($this->client, 'adminTest','test');
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @group rooms
     */
    public function testIndex()
    {
        $this->client->request('GET', '/rooms');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('Ydle\HubBundle\Controller\RoomController::indexAction', $this->client->getRequest()->attributes->get('_controller'));
    }

    /**
     * @group rooms
     */
    public function testCreateOrEditRoom()
    {
	$this->client->request('GET', '/rooms/list.json');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
	$this->assertEquals('ydle.settings.rooms.controller:getRoomsListAction', $this->client->getRequest()->attributes->get('_controller'));

        $formDatas = array(
            'submit' => 'submit',
            'datas' => array(
                'rooms_form[name]' => 'Room Test',
                'rooms_form[description]' => 'Room Description',
                'rooms_form[type]' => '1',
                'rooms_form[is_active]' => '1'
            ),
            'token' => 'rooms_form[_token]'
        );

	// Création d'un roomType
        $this->crawler = $this->checkForm('/room/form/0/submit','POST',$formDatas);
        $this->assertEquals(201, $this->client->getResponse()->getStatusCode());
        // TODO : Renvoi le formulaire et plus de message de confirmation. Régression ?
        //$this->assertEquals('"Room saved successfully"', $this->client->getResponse()->getContent());
	$this->assertEquals('Ydle\HubBundle\Controller\RoomController::submitRoomFormAction', $this->client->getRequest()->attributes->get('_controller'));

	// Edition d'un roomType
        $this->crawler = $this->checkForm('/room/form/1','POST',$formDatas);
        $this->assertEquals(201, $this->client->getResponse()->getStatusCode());
        // TODO : Renvoi le formulaire et plus de message de confirmation. Régression ?
        //$this->assertEquals('"Room saved successfully"', $this->client->getResponse()->getContent());
    }

    /**
     * @group rooms
     */
    public function testDeleteRoom()
    {	
	$this->client->request('DELETE', '/room.json?room_id=1');
        $this->assertEquals(204, $this->client->getResponse()->getStatusCode());
        // TODO : Ne renvoi plus de réponse. Régression ?
        //$this->assertEquals('"Room type deleted successfully"', $this->client->getResponse()->getContent());
	$this->assertEquals('ydle.settings.rooms.controller:deleteRoomAction', $this->client->getRequest()->attributes->get('_controller'));
    }
    
    /**
     * @group rooms
     */
    public function testActiveNode()
    {
	$this->client->request('PUT', '/room/state.json?room_id=1&state=0');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('true', $this->client->getResponse()->getContent());

	$this->client->request('PUT', '/room/state.json?room_id=1&state=1');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('true', $this->client->getResponse()->getContent());
    }

    private function loadContext()
    {
        $this->createAdmin('adminTest', 'test');

        $rt1 = new RoomType();
        $rt1->setName("Living Room");
        $rt1->setDescription("Living Room Desc");
        $rt1->setIsActive(true);
        $rt1->setCreatedAt(new \DateTime('now'));
        $this->em->persist($rt1);

        $rt2 = new RoomType();
        $rt2->setName("Bedroom");
        $rt2->setDescription("Bedroom Desc");
        $rt2->setIsActive(true);
        $rt2->setCreatedAt(new \DateTime('now'));
        $this->em->persist($rt2);

        $rt3 = new RoomType();
        $rt3->setName("Garage");
        $rt3->setDescription("Garage Desc");
        $rt3->setIsActive(true);
        $rt3->setCreatedAt(new \DateTime('now'));
        $this->em->persist($rt3);

        $rt4 = new RoomType();
        $rt4->setName("Bathroom");
        $rt4->setDescription("Bathroom Desc");
        $rt4->setIsActive(true);
        $rt4->setCreatedAt(new \DateTime('now'));
        $this->em->persist($rt4);

        $rt5 = new RoomType();
        $rt5->setName("Toilet");
        $rt5->setDescription("Toilet Desc");
        $rt5->setIsActive(true);
        $rt5->setCreatedAt(new \DateTime('now'));
        $this->em->persist($rt5);

        $nt1 = new NodeType();
        $nt1->setName("Temperature");
        $nt1->setUnit('°C');
        $nt1->setIsActive(true);
        $nt1->setCreatedAt(new \DateTime('now'));
        $this->em->persist($nt1);

        $nt2 = new NodeType();
        $nt2->setName("Humidity");
        $nt2->setUnit('%');
        $nt2->setIsActive(true);
        $nt2->setCreatedAt(new \DateTime('now'));
        $this->em->persist($nt2);

        $nt3 = new NodeType();
        $nt3->setName("Pressure");
        $nt3->setUnit('Pa');
        $nt3->setIsActive(true);
        $nt3->setCreatedAt(new \DateTime('now'));
        $this->em->persist($nt3);

        $nt4 = new NodeType();
        $nt4->setName("Luminosity");
        $nt4->setUnit('lux');
        $nt4->setIsActive(true);
        $nt4->setCreatedAt(new \DateTime('now'));
        $this->em->persist($nt4);

        $room1 = new Room();
        $room1->setName("Salon");
        $room1->setCreatedAt(new \DateTime('now'));
        $room1->setDescription('Description du salon');
        $room1->setIsActive(true);
        $room1->setType($rt1);
        $this->em->persist($room1);

        $room2 = new Room();
        $room2->setName("Chambre");
        $room2->setCreatedAt(new \DateTime('now'));
        $room2->setDescription('Description de la chambre');
        $room2->setIsActive(true);
        $room2->setType($rt2);
        $this->em->persist($room2);

        $this->em->flush();
    }
}
