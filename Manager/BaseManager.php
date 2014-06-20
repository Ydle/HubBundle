<?php

namespace Ydle\HubBundle\Manager;

abstract class BaseManager
{

    public function findBy(array $criteria, array $orderBy = null)
    {
        return $this->getRepository()->findBy($criteria, $orderBy);
    }

    public function findOneBy(array $criteria, array $orderBy = null)
    {
        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

    protected function persistAndFlush($entity)
    {
        $this->em->persist($entity);
        $this->em->flush();
    }

}