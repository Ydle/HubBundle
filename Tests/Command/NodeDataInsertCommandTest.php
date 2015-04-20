<?php

namespace Ydle\HubBundle\Tests;

use Ydle\HubBundle\Command\NodeDataInsertCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Ydle\HubBundle\Tests\Helper;

use Ydle\HubBundle\Entity\NodeType;
use Ydle\HubBundle\Entity\RoomType;
use Ydle\HubBundle\Entity\Room;
use Ydle\HubBundle\Entity\Node;
use Ydle\HubBundle\Entity\NodeData;

use Ydle\HubBundle\Repository\NodeDataRepository;

class NodeDataInsertCommandTest extends DataBaseTestCase
{
    /**
     *
     * @var Application
     */
    protected $application;
	
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    public $container;
    
    protected $client;
    protected $crawler;

    protected function setup()
    {
        parent::setup();
        $this->helper = new Helper();
        $this->client = static::createClient();
        $this->container = $this->client->getContainer();
        $this->em = $this->container->get('doctrine.orm.entity_manager');

        $kernel = $this->createKernel();
        $kernel->boot();

        $this->application = new Application($kernel);
        $this->application->add(new NodeDataInsertCommand());

        $this->truncateTable('fos_user');
        $this->truncateTable('fos_user');
        $this->truncateTable('roomtype');
        $this->truncateTable('sensortype');
        $this->truncateTable('room');
        $this->truncateTable('node_sensor');
        $this->truncateTable('node');
        $this->truncateTable('node_data');
        $this->loadContext();
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @test
     * @group importNodeDataInsert
     */
    public function nodeDataInsertCommandWithBadNodeTypeTest()
    {
        $command = $this->application->find('ydle:nodedata:insert');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            array('command' => $command->getName(), 'node' => '5', 'type' => '666', 'data' => '28', '--autopopulate' => true));

        $actual = $commandTester->getDisplay();
        $this->assertContains('Unknown node type', $actual);
    }

    /**
     * @test
     * @group importNodeDataInsert
     */
    public function nodeDataInsertCommandWithBadNodeCodeTest()
    {
        $command = $this->application->find('ydle:nodedata:insert');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            array('command' => $command->getName(), 'node' => '666', 'type' => '1', 'data' => '28', '--autopopulate' => true));

        $actual = $commandTester->getDisplay();
        $this->assertContains('Unknown node', $actual);
    }

    /**
     * @test
     * @group importNodeDataInsert2
     */
    public function nodeDataInsertCommandWithBadNodeTest()
    {
        $command = $this->application->find('ydle:nodedata:insert');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            array('command' => $command->getName(), 'node' => '5', 'type' => '1', 'data' => '28', '--autopopulate' => '1'));

        $actual = $commandTester->getDisplay();
        /*
        
        $nodeDataRepository = $this->container->get('ydle.data.manager');
        $params = array(
            'node_id' => '1',
            'type_id' => '1'
        );
        $datas = $nodeDataRepository->findByParams($params);
        var_dump($datas[0]);die();
         * */
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

        $node1 = new Node();
        $node1->setCode(5);
        $node1->setDescription('Description du node de la chambre');
        $node1->setIsActive(true);
        $node1->setName("Température Chambre");
        $node1->setRoom($room2);
        $node1->addType($nt1);
        $this->em->persist($node1);

        $node2 = new Node();
        $node2->setCode(6);
        $node2->setDescription('Description du deuwième node de la chambre');
        $node2->setIsActive(true);
        $node2->setName("Humidité Chambre");
        $node2->setRoom($room2);
        $node2->addType($nt2);
        $this->em->persist($node2);

        $this->dateA = new \DateTime('now');
        $nodeData = new NodeData();
        $nodeData->setNode($node1);
        $nodeData->setType($nt1);
        $nodeData->setData(25);
        $nodeData->setCreated($this->dateA);
        $this->em->persist($nodeData);

        $this->em->flush();
    }
}