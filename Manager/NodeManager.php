<?php
/*
  This file is part of Ydle.

    Ydle is free software: you can redistribute it and/or modify
    it under the terms of the GNU  Lesser General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Ydle is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU  Lesser General Public License for more details.

    You should have received a copy of the GNU Lesser General Public License
    along with Ydle.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace Ydle\HubBundle\Manager;

use Ydle\HubBundle\Model\NodeManagerInterface;
use Ydle\HubBundle\Model\BaseEntityManager;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Console\Application;

use Sonata\DatagridBundle\Pager\Doctrine\Pager;
use Sonata\DatagridBundle\ProxyQuery\Doctrine\ProxyQuery;

class NodeManager extends BaseEntityManager implements NodeManagerInterface
{

    /**
    * {@inheritdoc}
    */
    public function getPager(array $criteria, $page, $limit = 10, array $sort = array())
    {
        $parameters = array();
        
        $query = $this->getRepository()
            ->createQueryBuilder('n')
            ->select('n')
            ->where('1=1');
        
        if(!empty($criteria['room_id'])){
            $parameters['room_id'] = $criteria['room_id'];
            $query->andWhere('n.room = :room_id');
        }

        $query->setParameters($parameters);

        $pager = new Pager();
        $pager->setQuery(new ProxyQuery($query));
        $pager->setMaxPerPage($limit);
        $pager->setPage($page);
        $pager->init();

//        echo '<pre>';
//        \Doctrine\Common\Util\Debug::dump($pager);die();
        return $pager;
    }
    
    /**
     * Change the state of a node
     * 
     * @param integer $id
     * @param boolean $newState
     * @return boolean
     */
    public function changeState($id, $newState = 0)
    {
        if(!$object = $this->find($id)){
            return false;
        }
        $object->setIsActive($newState);
        $this->save($object);
        return true;
    }
    
    public function find($id)
    {
        return $this->getRepository()->find($id);
    }
    
    public function findOneBy(array $criteria, array $orderBy = null)
    {
        return $this->getRepository()->findOneBy($criteria);
    }
}
