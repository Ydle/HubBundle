<?php

namespace Ydle\HubBundle\Tests;

use Ydle\HubBundle\Tests\Helper;

class HomeControllerTest extends DataBaseTestCase
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
        $this->container = $this->client->getContainer();
        $this->em = $this->container->get('doctrine.orm.entity_manager');

        $this->truncateTable('fos_user');
        $this->loadContext();
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @group home
     */
    public function testIndexConnexionFail() {
        $this->crawler = $this->helper->logIn($this->client, 'badUser', 'badPwd');
        $this->assertTrue($this->client->getResponse()->isRedirect());

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
	$this->assertTrue($this->crawler->filter('.body-content:contains("Invalid credentials")')->count() == 0);
	$this->assertEquals('FOS\UserBundle\Controller\SecurityController::checkAction', $this->client->getRequest()->attributes->get('_controller'));
    }

    /**
     * @group home
     */
    public function testIndexConnexionDone() {
        $this->crawler = $this->helper->logIn($this->client, 'adminTest', 'test');
        $this->assertTrue($this->client->getResponse()->isRedirect());

        $this->client->request('GET', '/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Connexion Fail");
	$this->assertEquals('Ydle\HubBundle\Controller\HomeController::indexAction', $this->client->getRequest()->attributes->get('_controller'));
    }

    private function loadContext()
    {
        $this->createAdmin('adminTest', 'test');
    }
}