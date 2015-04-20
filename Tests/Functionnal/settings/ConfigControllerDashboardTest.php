<?php

namespace Ydle\HubBundle\Tests;

use Ydle\HubBundle\Entity\NodeType;
use Ydle\HubBundle\Entity\RoomType;

use Ydle\HubBundle\Tests\Helper;

class ConfigControllerDashboardTest extends DataBaseTestCase
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

        $this->crawler = $this->helper->logIn($this->client, 'adminTest','test');
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @group configDashboard
     */
    public function testDashboard()
    {
        $this->client->request('GET', '/conf/dashboard');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('Ydle\HubBundle\Controller\ConfigController::dashboardAction', $this->client->getRequest()->attributes->get('_controller'));
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

        $this->em->flush();
    }
}
