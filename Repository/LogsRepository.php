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
namespace Ydle\HubBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * LogsRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class LogsRepository extends EntityRepository
{
    /**
     * Create a query for the knppaginator
     * @return type
     */
    public function createViewLogQuery()
    {
        $query = $this->createQueryBuilder('l')
                    ->addOrderBy('l.created_at', "DESC")  
        ;
        return $query;
    }
    
    /**
     * Delete all data from the logs table
     * 
     * @return integer
     */
    public function reset()
    {
        $query = $this->getEntityManager()->createQuery('DELETE FROM YdleHubBundle:Logs');
        return $query->execute();
    }
}
