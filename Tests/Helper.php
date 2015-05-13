<?php

namespace Ydle\HubBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class Helper extends WebTestCase
{
    public $dataGenerator;

    public function logIn($client, $username, $password)
    {
        $crawler = $client->request('GET', '/login');

        $buttonCrawler = $crawler->selectButton('action.connexion');
        $form = $buttonCrawler->form();
        $form['_username'] = $username;
        $form['_password'] = $password;

        return $client->submit($form);
    }
}
