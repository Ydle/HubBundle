<?php

namespace Ydle\HubBundle\Tests;

// A voir si on ne peux pas le supprimer
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Ydle\HubBundle\Entity\NodeType;
use Ydle\HubBundle\Entity\RoomType;

use Ydle\HubBundle\Tests\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Ydle\HubBundle\Tests;

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
        $this->helper = new \Ydle\HubBundle\Tests\Helper();
        $this->client = static::createClient();
        $this->client->followRedirects();
        $this->container = $this->client->getContainer();

        //Invalidate latest session
        $this->container->get('session')->invalidate();
        
        
        $this->truncateTable('fos_user');
        $this->truncateTable('roomtype');
        $this->truncateTable('sensortype');
        $this->loadContext();
        
        $crawler = $this->helper->logIn($this->client, 'adminTest','test');
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
     * @group configNodeTypeTest
     */
    public function testTypenode()
    {
	$this->client->request('GET', '/conf/typenode');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
	$this->assertEquals('Ydle\HubBundle\Controller\ConfigController::typenodeAction', $this->client->getRequest()->attributes->get('_controller'));
    }
    
    /**
     * @group configNodeTypeTest
     */
    public function testListBadParamNodeType()
    {
	$this->client->request('GET', '/node/type.json?page=2&count=1&room_id=');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
	$this->assertEquals('ydle.settings.nodetype.controller:getNodeTypeAction', $this->client->getRequest()->attributes->get('_controller'));
    }
    
    /**
     * @group configNodeTypeTest
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
        $this->crawler = $this->checkForm('/conf/typenode/form','POST',$formDatas);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('{"result":"success","message":"Node type added successfully"}', $this->client->getResponse()->getContent());
	$this->assertEquals('Ydle\HubBundle\Controller\ConfigController::typenodeFormAction', $this->client->getRequest()->attributes->get('_controller'));
        
	// Edition d'un nodeType
        $this->crawler = $this->checkForm('/conf/typenode/form/2','POST',$formDatas);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('{"result":"success","message":"Node type modified successfully"}', $this->client->getResponse()->getContent());
        $this->assertEquals('Ydle\HubBundle\Controller\ConfigController::typenodeFormAction', $this->client->getRequest()->attributes->get('_controller'));
    }
    
    /**
     * @group configNodeTypeTest
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
     * @group configNodeTypeTest2
     */
        /*
    public function testTypenodeDetails()
    {
	$this->client->request('PUT', '/node/type.json?nodetype_id=4&state=0');
        $this->assertEquals(405, $this->client->getResponse()->getStatusCode());
	$this->assertEquals('Ydle\HubBundle\Controller\ConfigController::typenodeAction', $this->client->getRequest()->attributes->get('_controller'));
        
	$this->client->request('PUT', '/node/type.json?nodetype_id=4&state=1');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
	$this->assertEquals('Ydle\HubBundle\Controller\ConfigController::typenodeAction', $this->client->getRequest()->attributes->get('_controller'));
        
	$this->client->request('PUT', '/node/type.json?nodetype_id=999&state=0');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
	$this->assertEquals('Ydle\HubBundle\Controller\ConfigController::typenodeAction', $this->client->getRequest()->attributes->get('_controller'));
        
	$this->client->request('PUT', '/node/type.json?nodetype_id=999&state=1');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
	$this->assertEquals('Ydle\HubBundle\Controller\ConfigController::typenodeAction', $this->client->getRequest()->attributes->get('_controller'));
    }
         * 
         */
    
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
        
        $this->em->flush();
    }
}
