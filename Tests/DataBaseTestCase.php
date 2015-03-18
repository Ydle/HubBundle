<?php

namespace Ydle\HubBundle\Tests;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as WTC;

use Ydle\HubBundle\Entity\User;

class DataBaseTestCase extends WTC
{
    /**
     * @var EntityManager
     */
    protected $em;

    protected $client;
    protected $container;

    protected function setup()
    {
        parent::setup();
        $this->client = static::createClient();
        $this->container = $this->client->getContainer();
        $this->em = $this->container->get('doctrine.orm.entity_manager');
        //$this->em->getConnection()->beginTransaction();
    }

    public function tearDown()
    {
        parent::tearDown();
        //$this->em->getConnection()->rollBack();
        //$this->container->get('doctrine')->getConnection()->close();
    }

    /**
     * @param $tableName
     */
    protected function truncateTable($tableName)
    {
        $connection = $this->em->getConnection();
        $dbPlatform = $connection->getDatabasePlatform();
        $connection->query('SET FOREIGN_KEY_CHECKS=0');
        $q = $dbPlatform->getTruncateTableSql($tableName);
        $connection->executeUpdate($q);
        $connection->executeUpdate("ALTER TABLE $tableName AUTO_INCREMENT = 1");
        $connection->query('SET FOREIGN_KEY_CHECKS=1');
    }

    /**
     * check if Data is Json
     * @param $expected
     */
    protected function assertResponseIsJson($expected)
    {
        $content = $this->client->getResponse()->getContent();
        $this->assertJson($content);
        $actual = json_decode($content, true);
        $this->assertEquals($expected, $actual);
    }
    
    public function createAdmin($user, $passwd)
    {        
        $adm = new User();
        $adm->setUsername($user);
        $adm->setUsernameCanonical($user);
        $adm->setEmail('admin@admin.fr');
        $adm->setEmailCanonical('admin@admin.fr');
        $adm->setEnabled(true);
        $adm->setPassword($passwd);
        $adm->setPlainPassword($passwd);
        $this->em->persist($adm);
        $this->em->flush();
    }    
    
    
/*
    protected function logIn($username, $password)
    {
        $this->client->setServerParameters(array(
            'PHP_AUTH_USER' => $username,
            'PHP_AUTH_PW'   => $password,
        ));
    }
 * */
}
