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

use Ydle\HubBundle\Model\LogsManagerInterface;
use Ydle\CoreBundle\Model\BaseEntityManager;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Console\Application;

use Sonata\DatagridBundle\Pager\Doctrine\Pager;
use Sonata\DatagridBundle\ProxyQuery\Doctrine\ProxyQuery;
use Ydle\HubBundle\Entity\Logs;

class LogsManager extends BaseEntityManager implements LogsManagerInterface
{

    /**
    * {@inheritdoc}
    */
    public function getPager(array $criteria, $page, $limit = 10, array $sort = array())
    {
        $parameters = array();

        $query = $this->getRepository()
            ->createQueryBuilder('l')
            ->select('l')
            ->where('1=1');
        
        if(!empty($criteria['type']) && $criteria['type'] != "all"){ 
           $query->andWhere('l.type = :type');
           $parameters['type'] = $criteria['type'];
                //->setParameter('type', $criteria['type']);
        }
        if(!empty($criteria['source']) && $criteria['source'] != "all"){ 
           $query->andWhere('l.source = :source');
           $parameters['source'] = $criteria['source'];
                //->setParameter('source', $criteria['source']);            
        }

        $query->setParameters($parameters);

        $pager = new Pager();
        $pager->setQuery(new ProxyQuery($query));
        $pager->setMaxPerPage($limit);
        $pager->setPage($page);
        $pager->init();

        return $pager;
    }
    
    /**
    * Save a log in database
    * 
    * @param string $type
    * @param string $text
    * @return Logs
    */
    public function log($type, $text, $source='')
    {
        $log = new Logs();
        $log->setType($type);
        $log->setSource($source);
        $log->setContent($text);
        
        $this->em->persist($log);
        $this->em->flush();
        
        return $log;
    }
    
    public function createViewLogQuery()
    {
        return $this->getRepository()->createViewLogQuery();
    }
    
    public function reset()
    {
        return $this->getRepository()->reset();
    }

}
