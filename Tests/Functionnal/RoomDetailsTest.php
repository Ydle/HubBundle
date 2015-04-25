<?php

namespace Ydle\HubBundle\Tests;

use Ydle\HubBundle\Entity\NodeType;
use Ydle\HubBundle\Entity\RoomType;
use Ydle\HubBundle\Entity\Room;
use Ydle\HubBundle\Entity\Node;
use Ydle\HubBundle\Entity\NodeData;

use Ydle\HubBundle\Tests\Helper;

class RoomDetailsTest extends DataBaseTestCase
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
        $this->truncateTable('node_data');
        $this->loadContext();

        $this->helper->logIn($this->client, 'adminTest', 'test');
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @group roomDetails
     */
    public function testIndex()
    {
        $repository = $this->em->getRepository('YdleHubBundle:Room');
        $room = $repository->find(1);
        $this->client->request('GET', '/room/detail/'.$room->getSlug());
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('Ydle\HubBundle\Controller\RoomController::roomDetailAction', $this->client->getRequest()->attributes->get('_controller'));

        $room = $repository->find(2);
        $this->client->request('GET', '/room/detail/'.$room->getSlug());
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('Ydle\HubBundle\Controller\RoomController::roomDetailAction', $this->client->getRequest()->attributes->get('_controller'));
    }

    /**
     * @group roomDetails
     */
    public function testListBadParamNode()
    {
        $this->client->request('GET', '/room/nodes/list.json?page=2&count=1&room_id=');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('ydle.settings.nodes.controller:getRoomNodesListAction', $this->client->getRequest()->attributes->get('_controller'));
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

        $nodeA = new Node();
        $nodeA->setCode(5);
        $nodeA->setDescription('Description du node de la chambre');
        $nodeA->setIsActive(true);
        $nodeA->setName("Température Chambre");
        $nodeA->addType($ntA);
        $nodeA->setRoom($roomA);
        $this->em->persist($nodeA);

        $roomA->addNode($nodeA);
        $this->em->persist($roomA);

        $this->dateA = new \DateTime('now');
        $nodeData = new NodeData();
        $nodeData->setNode($nodeA);
        $nodeData->setType($ntA);
        $nodeData->setData(25);
        $nodeData->setCreated($this->dateA);
        $this->em->persist($nodeData);

        $this->dateB = $this->dateA->modify("-1 hour");
        $nodeDataB = new NodeData();
        $nodeDataB->setNode($nodeA);
        $nodeDataB->setType($ntA);
        $nodeDataB->setData(23);
        $nodeDataB->setCreated($this->dateB);
        $this->em->persist($nodeDataB);

        $this->em->flush();
    }
}
