<?php

namespace Ydle\HubBundle\Tests;

// A voir si on ne peux pas le supprimer
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Ydle\HubBundle\Entity\NodeType;
use Ydle\HubBundle\Entity\RoomType;
use Ydle\HubBundle\Entity\Room;
use Ydle\HubBundle\Entity\Node;

use Ydle\HubBundle\Tests\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Ydle\HubBundle\Tests;

class NodesTest extends DataBaseTestCase
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
        $this->helper = new \Ydle\HubBundle\Tests\Helper();
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
        $this->loadContext();
        
        $this->helper->logIn($this->client, 'adminTest','test');
    }
	
    public function tearDown()
    {
        parent::tearDown();
    }

    protected function checkForm($url, $method, $formDatas)
    {
        // TODO : Récup du token à mettre ailleurs
        $this->crawler = $this->client->request($method, $url);
        $extract = $this->crawler->filter('input[name="'.$formDatas['token'].'"]')->extract(array('value'));
        
        $csrf_token = $extract[0];
        $formDatas['datas'] = array_merge($formDatas['datas'], array($formDatas['token'] => $csrf_token));

        $buttonCrawler = $this->crawler->selectButton($formDatas['submit']);
        $form = $buttonCrawler->form();
        
        foreach($formDatas['datas'] as $key => $value){
            $form[$key] = $value;
        }
        $this->client->submit($form);
    }

    /**
     * @group nodesTest
     */
    public function testIndex()
    {
        $this->client->request('GET', '/nodes');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('Ydle\HubBundle\Controller\NodesController::indexAction', $this->client->getRequest()->attributes->get('_controller'));
    }
	
    /**
     * @group nodesTest
     */
    public function testCreateOrEditNode()
    {
	$this->client->request('GET', '/nodes/list.json');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
	$this->assertEquals('ydle.settings.nodes.controller:getNodesListAction', $this->client->getRequest()->attributes->get('_controller'));
        
        $formDatas1 = array(
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
        $this->crawler = $this->checkForm('/nodes/form/0/submit','POST',$formDatas1);
        $this->assertEquals(201, $this->client->getResponse()->getStatusCode());
        // TODO : Renvoi le formulaire et plus de message de confirmation. Régression ?
        //$this->assertEquals('"Node saved successfully"', $this->client->getResponse()->getContent());
	$this->assertEquals('Ydle\HubBundle\Controller\NodesController::submitNodeFormAction', $this->client->getRequest()->attributes->get('_controller'));
        
        
        $formDatas2 = array(
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
        $this->crawler = $this->checkForm('/nodes/form/1','POST',$formDatas2);
        $this->assertEquals(201, $this->client->getResponse()->getStatusCode());
        // TODO : Renvoi le formulaire et plus de message de confirmation. Régression ?
        //$this->assertEquals('"Type room saved successfully"', $this->client->getResponse()->getContent());
    }
    
    /**
     * @group nodesTest
     */
    public function testDeleteNode()
    {
	$this->client->request('DELETE', '/node.json?node_id=2');
        $this->assertEquals(204, $this->client->getResponse()->getStatusCode());
        // TODO : Ne renvoi plus rien, plus de message de confirmation. Régression ?
        //$this->assertEquals('"Node type deleted successfully"', $this->client->getResponse()->getContent());
	$this->assertEquals('ydle.settings.nodes.controller:deleteNodeAction', $this->client->getRequest()->attributes->get('_controller'));
    }
    
    /**
     * @group nodesTest
     */
    public function testActiveNode()
    {
	$this->client->request('PUT', '/node/state.json?node_id=1&state=0');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('true', $this->client->getResponse()->getContent());
        
	$this->client->request('PUT', '/node/state.json?node_id=1&state=1');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('true', $this->client->getResponse()->getContent());
    }
    
    /**
     * @group nodesTest
     */
    public function testLinkNode()
    {
	$this->client->request('PUT', '/node/link.json?node=1');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(200, $this->client->getResponse()->getContent());
        /*
	$this->client->request('PUT', '/node/state.json?node_id=1&state=1');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('true', $this->client->getResponse()->getContent());
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
        
        $this->em->flush();
    }
}
