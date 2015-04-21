<?php

namespace Ydle\HubBundle\Tests;

use Ydle\HubBundle\Tests\Helper;

class LogsControllerTest extends DataBaseTestCase
{
    protected $client;
    protected $crawler;

    public function setup()
    {
        parent::setup();
        $this->helper = new Helper();
        $this->client = static::createClient();

        $this->truncateTable('fos_user');
        $this->truncateTable('logs');
        $this->loadContext();

        $this->helper->logIn($this->client, 'adminTest', 'test');
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @group logs
     */
    public function testIndex()
    {
        $this->client->request('GET', '/logs');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('Ydle\HubBundle\Controller\LogsController::indexAction', $this->client->getRequest()->attributes->get('_controller'));

        // Check la requête de récupération des logs
        // A voir si on conserve ou si le check dans le controller RestLogsController suffit
        $this->client->request('GET', '/logs/list.json');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('ydle.settings.logs.controller:getLogsListAction', $this->client->getRequest()->attributes->get('_controller'));

        // Test toutes les combinaisons de filtres
        // Pour les différents types/Supports de Logs, ça serait pas bien de faire un ENUM ?
        $errorType = array("all", "info", "error", "warning");
        $errorSupport = array("all", "api", "hub", "master", "nodes");

        for ($i = 0; $i > count($errorType); $i++) {
            for ($j = 0; $j > count($errorSupport); $j++) {
                $this->client->request('GET', '/logs/list.json?type='.$errorType[$i].'&source='.$errorSupport[$j].'&');
                $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
                $this->assertEquals('ydle.settings.logs.controller:getLogsListAction', $this->client->getRequest()->attributes->get('_controller'));
            }
        }
    }

    /**
     * @group logs
     */
    public function testReset()
    {
        $this->crawler = $this->client->request('DELETE', '/logs/list.json');
        $this->assertEquals(204, $this->client->getResponse()->getStatusCode());
        $this->assertTrue($this->crawler->filter('.dataTables_wrapper .dataTable td:contains("logs.table.empty")')->count() == 0);
        $this->assertEquals('ydle.settings.logs.controller:deleteLogsListAction', $this->client->getRequest()->attributes->get('_controller'));
    }

    /**
     * @group logs
     */
    public function testReset2()
    {
        $this->crawler = $this->client->request('GET', '/logs/reset');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $this->client->followRedirects();
        $this->assertTrue($this->crawler->filter('.dataTables_wrapper .dataTable td:contains("logs.table.empty")')->count() == 0);
        $this->assertEquals('Ydle\HubBundle\Controller\LogsController::resetAction', $this->client->getRequest()->attributes->get('_controller'));
    }

    /**
     * @group logs
     */
    public function testApiLog()
    {
        $this->client->request('POST', '/api/log/add.json?message=msgtest&level=info');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('ydle.settings.logs.controller:postApiLogAction', $this->client->getRequest()->attributes->get('_controller'));
        $this->assertContains('{"id":1,"type":"info","source":"master","content":"msgtest"', $this->client->getResponse()->getContent());
    }

    private function loadContext()
    {
        $this->crawler = $this->createAdmin('adminTest', 'test');
    }
}