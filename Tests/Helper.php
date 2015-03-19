<?php

namespace Ydle\HubBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class Helper extends WebTestCase
{
    public $dataGenerator;


    public function logIn($client, $username, $password)
    {
        // TODO : Faire marcher ce mécanisme à la place de l'actuel qui est du bricolage
        /*
        $this->client->setServerParameters(array(
            'PHP_AUTH_USER' => $username,
            'PHP_AUTH_PW'   => $password,
        )); */
        $crawler = $client->request('GET', '/login');
         
        $buttonCrawler = $crawler->selectButton('action.connexion');
        $form = $buttonCrawler->form();
        $form['_username'] = $username;
        $form['_password'] = $password;

        return $client->submit($form);
    }
}
