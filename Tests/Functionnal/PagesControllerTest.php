<?php

namespace Ydle\HubBundle\Tests;

use Ydle\HubBundle\Tests\Helper;

class PageControllerTest extends DataBaseTestCase
{
    protected $client;
    protected $crawler;

    public function setup()
    {
        parent::setup();
        $this->helper = new Helper();
        $this->client = static::createClient();

        $this->truncateTable('fos_user');
        $this->loadContext();

        $this->helper->logIn($this->client, 'adminTest','test');
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @group pages
     */
    public function testIndex() {
        $this->client->request('GET', '/pages/about');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
	$this->assertEquals('Ydle\HubBundle\Controller\PagesController::indexAction', $this->client->getRequest()->attributes->get('_controller'));
    }

    private function loadContext()
    {
        $this->createAdmin('adminTest', 'test');
    }
}
