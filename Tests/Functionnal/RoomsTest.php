<?php

namespace Ydle\HubBundle\Tests\Functionnal;

use Ydle\HubBundle\Entity\NodeType;
use Ydle\HubBundle\Entity\RoomType;
use Ydle\HubBundle\Entity\Room;

use Ydle\HubBundle\Tests\DataBaseTestCase;
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

        $this->helper->logIn($this->client, 'adminTest', 'test');
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
        $this->crawler = $this->checkForm('/room/form/0/submit', 'POST', $formDatas);
        $this->assertEquals(201, $this->client->getResponse()->getStatusCode());
        // TODO : Renvoi le formulaire et plus de message de confirmation. Régression ?
        //$this->assertEquals('"Room saved successfully"', $this->client->getResponse()->getContent());
        $this->assertEquals('Ydle\HubBundle\Controller\RoomController::submitRoomFormAction', $this->client->getRequest()->attributes->get('_controller'));

        // Edition d'un roomType
        $this->crawler = $this->checkForm('/room/form/1', 'POST', $formDatas);
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

        $this->client->request('DELETE', '/room.json?room_id=666');
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
        $this->assertContains('"This room does not exist"', $this->client->getResponse()->getContent());
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

        $this->client->request('PUT', '/room/state.json?room_id=666&state=0');
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
        $this->assertContains('This room does not exist', $this->client->getResponse()->getContent());
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

        $roomA = new Room();
        $roomA->setName("Salon");
        $roomA->setCreatedAt(new \DateTime('now'));
        $roomA->setDescription('Description du salon');
        $roomA->setIsActive(true);
        $roomA->setType($rtA);
        $this->em->persist($roomA);

        $roomB = new Room();
        $roomB->setName("Chambre");
        $roomB->setCreatedAt(new \DateTime('now'));
        $roomB->setDescription('Description de la chambre');
        $roomB->setIsActive(true);
        $roomB->setType($rtB);
        $this->em->persist($roomB);

        $this->em->flush();
    }
}
