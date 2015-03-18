<?php

namespace Ydle\HubBundle\Tests;

// A voir si on ne peux pas le supprimer
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Ydle\HubBundle\Tests\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class PageControllerTest extends DataBaseTestCase
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

        return $this->crawler = $this->client->submit($form);
    }
    
    /**
     * @group pagesTest
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
