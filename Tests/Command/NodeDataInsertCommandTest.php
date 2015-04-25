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
        $commandTester->execute(array('command' => $command->getName(), 'node' => '5', 'type' => '666', 'data' => '28', '--autopopulate' => true));

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
        $commandTester->execute(array('command' => $command->getName(), 'node' => '666', 'type' => '1', 'data' => '28', '--autopopulate' => true));

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
        $commandTester->execute(array('command' => $command->getName(), 'node' => '5', 'type' => '1', 'data' => '28', '--autopopulate' => '1'));

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
        $nodeA->setRoom($roomB);
        $nodeA->addType($ntA);
        $this->em->persist($nodeA);

        $nodeB = new Node();
        $nodeB->setCode(6);
        $nodeB->setDescription('Description du deuwième node de la chambre');
        $nodeB->setIsActive(true);
        $nodeB->setName("Humidité Chambre");
        $nodeB->setRoom($roomB);
        $nodeB->addType($ntB);
        $this->em->persist($nodeB);

        $this->dateA = new \DateTime('now');
        $nodeData = new NodeData();
        $nodeData->setNode($nodeA);
        $nodeData->setType($ntA);
        $nodeData->setData(25);
        $nodeData->setCreated($this->dateA);
        $this->em->persist($nodeData);

        $this->em->flush();
    }
}