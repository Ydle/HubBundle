<?php

namespace Ydle\HubBundle\Tests\Functionnal\Settings;

use Ydle\HubBundle\Entity\NodeType;
use Ydle\HubBundle\Entity\RoomType;

use Ydle\HubBundle\Tests\DataBaseTestCase;
use Ydle\HubBundle\Tests\Helper;

class ConfigControllerIndexTest extends DataBaseTestCase
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
     * @group configIndex
     */
    public function testIndex()
    {
        $this->client->request('GET', '/conf');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('Ydle\HubBundle\Controller\ConfigController::indexAction', $this->client->getRequest()->attributes->get('_controller'));
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
