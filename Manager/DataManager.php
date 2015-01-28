<?php

namespace Ydle\HubBundle\Manager;

use Doctrine\ORM\EntityManager;
use Ydle\HubBundle\Model\DataManagerInterface;
use Ydle\HubBundle\Model\BaseEntityManager;

class DataManager extends BaseEntityManager  implements DataManagerInterface
{

    public function findAllByName()
    {
        return $this->getRepository()->findAll();
    }

    public function getRepository()
    {
        return $this->em->getRepository('YdleHubBundle:NodeData');
    }

    public function findByRoom($params = array())
    {
        return $this->getRepository()->findByRoom($params);
    }

    public function findByParams($params = array())
    {
        return $this->getRepository()->findByParams($params);
    }

    public function getLastData($roomId)
    {
        return $this->getRepository()->getLastData($roomId);
    }

    public function getNodeLastData($nodeId, $params = array())
    {
        return $this->getRepository()->getNodeLastData($nodeId, $params);
    }
    
    public function deleteNodeData($nodeId)
    {
        return $this->getRepository()->deleteNodeData($nodeId);
    }

}
