<?php

namespace Ydle\HubBundle\Tests;

// A voir si on peux le supprimer
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Ydle\HubBundle\Tests\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

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
     * @group homeTest
     */
    public function testIndexConnexionFail() {
        $this->crawler = $this->logIn('badUser', 'badPwd');
        $this->assertTrue($this->client->getResponse()->isRedirect());
        
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
	$this->assertTrue($this->crawler->filter('.body-content:contains("Invalid credentials")')->count() == 0);
	$this->assertEquals('FOS\UserBundle\Controller\SecurityController::checkAction', $this->client->getRequest()->attributes->get('_controller'));
    }

    /**
     * @group homeTest
     */
    public function testIndexConnexionDone() {
        $this->crawler = $this->logIn('adminTest', 'test');
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