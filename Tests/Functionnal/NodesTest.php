<?php

namespace Ydle\HubBundle\Tests\Functionnal;

use Ydle\HubBundle\Entity\NodeType;
use Ydle\HubBundle\Entity\RoomType;
use Ydle\HubBundle\Entity\Room;
use Ydle\HubBundle\Entity\Node;
use Ydle\HubBundle\Entity\NodeData;

use Ydle\HubBundle\Tests\DataBaseTestCase;
use Ydle\HubBundle\Tests\Helper;

class NodesTest extends DataBaseTestCase
{
    protected $client;
    protected $crawler;
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    public $container;

    public $dateA;

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
        $this->truncateTable('node_sensor');
        $this->truncateTable('node');
        $this->truncateTable('node_data');
        $this->loadContext();

        $this->helper->logIn($this->client, 'adminTest', 'test');
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @group nodes
     */
    public function testIndex()
    {
        $this->client->request('GET', '/nodes');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('Ydle\HubBundle\Controller\NodesController::indexAction', $this->client->getRequest()->attributes->get('_controller'));
    }

    /**
     * @group nodes
     */
    public function testCreateOrEditNode()
    {
        $this->client->request('GET', '/nodes/list.json');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('ydle.settings.nodes.controller:getNodesListAction', $this->client->getRequest()->attributes->get('_controller'));

        $formDatasA = array(
            'submit' => 'submit',
            'datas' => array(
                'node_form[name]' => 'Node Test',
                'node_form[code]' => '3',
                'node_form[room]' => '1',
                'node_form[types]' => array('1','3'),
                'node_form[description]' => 'Node Description',
                'node_form[is_active]' => '1'
            ),
            'token' => 'node_form[_token]'
        );    

        // Création d'un node
        $this->crawler = $this->checkForm('/nodes/form/0/submit', 'POST', $formDatasA);
        $this->assertEquals(201, $this->client->getResponse()->getStatusCode());
        // TODO : Renvoi le formulaire et plus de message de confirmation. Régression ?
        //$this->assertEquals('"Node saved successfully"', $this->client->getResponse()->getContent());
        $this->assertEquals('Ydle\HubBundle\Controller\NodesController::submitNodeFormAction', $this->client->getRequest()->attributes->get('_controller'));

        $formDatasB = array(
            'submit' => 'submit',
            'datas' => array(
                'node_form[name]' => 'Node Test',
                'node_form[code]' => '4',
                'node_form[room]' => '1',
                'node_form[types]' => array('1','3'),
                'node_form[description]' => 'Node Description',
                'node_form[is_active]' => '1'
            ),
            'token' => 'node_form[_token]'
        );
        // Edition d'un node
        $this->crawler = $this->checkForm('/nodes/form/1', 'POST', $formDatasB);
        $this->assertEquals(201, $this->client->getResponse()->getStatusCode());
        // TODO : Renvoi le formulaire et plus de message de confirmation. Régression ?
        //$this->assertEquals('"Type room saved successfully"', $this->client->getResponse()->getContent());
    }

    /**
     * @group nodes
     */
    public function testDeleteNode()
    {
        $this->client->request('DELETE', '/node.json?node_id=2');
        $this->assertEquals(204, $this->client->getResponse()->getStatusCode());
        // TODO : Ne renvoi plus rien, plus de message de confirmation. Régression ?
        //$this->assertEquals('"Node type deleted successfully"', $this->client->getResponse()->getContent());
        $this->assertEquals('ydle.settings.nodes.controller:deleteNodeAction', $this->client->getRequest()->attributes->get('_controller'));

        $this->client->request('DELETE', '/node.json?node_id=999');
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
        // TODO : Améliorer le getContent pour avoir un test plus propre.
        $this->assertContains('This node does not exist', $this->client->getResponse()->getContent());
        $this->assertEquals('ydle.settings.nodes.controller:deleteNodeAction', $this->client->getRequest()->attributes->get('_controller'));
    }

    /**
     * @group nodes
     */
    public function testActiveNode()
    {
        $this->client->request('PUT', '/node/state.json?node_id=1&state=0');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('true', $this->client->getResponse()->getContent());

        $this->client->request('PUT', '/node/state.json?node_id=1&state=1');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('true', $this->client->getResponse()->getContent());

        $this->client->request('PUT', '/node/state.json?node_id=999&state=0');
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
        $this->assertContains('node.not.found', $this->client->getResponse()->getContent());
    }

    /**
     * @group nodes
     */
    public function testLinkNode()
    {
        $this->client->request('PUT', '/node/link.json?node=1');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(200, $this->client->getResponse()->getContent());

        $this->client->request('PUT', '/node/link.json?node=999');
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
        // TODO : Améliorer le getContent pour avoir un test plus propre.
        $this->assertContains("node.not.found", $this->client->getResponse()->getContent());
    }

    /**
     * @group nodes
     */
    public function testResetNode()
    {
        $this->client->request('PUT', '/node/reset.json?node=1');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(200, $this->client->getResponse()->getContent());

        $this->client->request('PUT', '/node/reset.json?node=999');
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
        // TODO : Améliorer le getContent pour avoir un test plus propre.
        $this->assertContains("node.not.found", $this->client->getResponse()->getContent());
    }

    /**
     * @group nodes
     */
    public function testgetRoomNodeStats()
    {
        // Mauvais Code - renvoi 404
        $this->client->request('GET', '/room/node/stats.json?node=666&filter=month');
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
        $this->assertContains('{"error":{"code":404,"message":"Not Found"', $this->client->getResponse()->getContent());

        // Bon code
        $this->client->request('GET', '/room/node/stats.json?node=5&filter=day');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('{"1":{"label":"Temperature (\u00b0C)","data":[['.((int) $this->dateA->format('U') * 1000).',0.3]],"yaxis":1}}', $this->client->getResponse()->getContent());

        // Bon code
        $this->client->request('GET', '/room/node/stats.json?node=5&filter=week');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('{"1":{"label":"Temperature (\u00b0C)","data":[['.((int) $this->dateA->format('U') * 1000).',0.3]],"yaxis":1}}', $this->client->getResponse()->getContent());

        // Bon code
        $this->client->request('GET', '/room/node/stats.json?node=5&filter=month');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('{"1":{"label":"Temperature (\u00b0C)","data":[['.((int) $this->dateA->format('U') * 1000).',0.3]],"yaxis":1}}', $this->client->getResponse()->getContent());
    }

    /**
     * @group nodes
     */
    public function testpostNodesDatas()
    {
        $this->client->request('POST', '/nodes/datas.json?sender=666&type=1&data=27');
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
        $this->assertContains('{"error":{"code":404,"message":"Not Found"', $this->client->getResponse()->getContent());

        $this->client->request('POST', '/nodes/datas.json?sender=5&type=666&data=27');
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
        $this->assertContains('{"error":{"code":404,"message":"Not Found"', $this->client->getResponse()->getContent());

        $this->client->request('POST', '/nodes/datas.json?sender=5&type=1&data=27');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('{"code":0,"result":"data sent"}', $this->client->getResponse()->getContent());
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
