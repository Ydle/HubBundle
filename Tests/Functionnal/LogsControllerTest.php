<?php

namespace Ydle\HubBundle\Tests;

// A voir si on ne peux pas le supprimer
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Ydle\HubBundle\Tests\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class LogsControllerTest extends DataBaseTestCase
{
    protected $client;
    protected $crawler;
    
    public function setup()
    {
        parent::setup();
        $this->client = static::createClient();
        
        $this->truncateTable('fos_user');
        $this->loadContext();
        
        $this->logIn('adminTest','test');
    }
	
    public function tearDown()
    {
        parent::tearDown();
    }

    protected function logIn($username, $password)
    {
        // TODO : Faire marcher ce mécanisme à la place de l'actuel qui est du bricolage
        /*
        $this->client->setServerParameters(array(
            'PHP_AUTH_USER' => $username,
            'PHP_AUTH_PW'   => $password,
        )); */
        $this->crawler = $this->client->request('GET', '/login'); //login_check
         
        $buttonCrawler = $this->crawler->selectButton('action.connexion');
        $form = $buttonCrawler->form();
        $form['_username'] = $username;
        $form['_password'] = $password;

        $this->crawler = $this->client->submit($form);
    }
  
    /**
     * @group logsTest
     */
    public function testIndex() {
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
		
	for($i = 0; $i > count($errorType); $i++) {
		for($j = 0; $j > count($errorSupport); $j++) {
			$this->client->request('GET', '/logs/list.json?type='.$errorType[$i].'&source='.$errorSupport[$j].'&');
			$this->assertEquals(200, $this->client->getResponse()->getStatusCode());
                        $this->assertEquals('ydle.settings.logs.controller:getLogsListAction', $this->client->getRequest()->attributes->get('_controller'));
		}
	}
    }
    
    /**
     * @group logsTest
     */
    public function testReset() {
        $this->client->request('DELETE', '/logs/list.json');
        $this->assertEquals(204, $this->client->getResponse()->getStatusCode());
	$this->assertTrue($this->crawler->filter('.dataTables_wrapper .dataTable td:contains("logs.table.empty")')->count() == 0);
	$this->assertEquals('ydle.settings.logs.controller:deleteLogsListAction', $this->client->getRequest()->attributes->get('_controller'));
    }
    
    /**
     * @group logsTest
     */
    public function testReset2() {
        $this->client->request('GET', '/logs/reset');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $this->client->followRedirects();
	$this->assertTrue($this->crawler->filter('.dataTables_wrapper .dataTable td:contains("logs.table.empty")')->count() == 0);
	$this->assertEquals('Ydle\HubBundle\Controller\LogsController::resetAction', $this->client->getRequest()->attributes->get('_controller'));
    }
    
    private function loadContext()
    {
        $this->createAdmin('adminTest', 'test');
    }
}