<?php

namespace Ydle\HubBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * NodeDataRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class NodeDataRepository extends EntityRepository
{
    
    public function findByParams($params)
    {
        $qb = $this->createQueryBuilder("d");
        $qb->where('1=1');
        
        $qb->join('d.node', 'n');
		if(!empty($params['room_id'])) {
            $qb->andWhere('n.room = :roomid')
               ->setParameter('roomid', $params['room_id']);
		}
		if(!empty($params['node_id'])) {
            $qb->andWhere('d.node = :nodeid')
               ->setParameter('nodeid', $params['node_id']);
		}
		if(!empty($params['type_id'])) {
			$qb->andWhere('d.type = :type')
                ->setParameter('type', $params['type_id']);
		}
		if(!empty($params['start_date'])) {
            $qb->andWhere('d.created > :startdate')
                ->setParameter('startdate', $params['start_date']);
		}
        $qb->orderBy('d.created')
        ;
        
        
        $q = $qb->getQuery();
        try {
            if(isset($params['querybuilder'])){
                return $qb;
            } else {
                return $q->getResult();
            }
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
    
    public function getLastData($roomId)
    {
        $qb = $this->createQueryBuilder("d")
                ->join('d.node', 'n')
                ->join('d.type', 't')
                ->where('n.room = :roomid')
                    ->setParameter('roomid', $roomId)
                ->andWhere('n.isActive = 1')
                ->groupBy('d.type')
                ->orderBy('d.created', 'DESC')
        ;
        
        try {
            return $qb->getQuery()->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
}
