<?php

/*
 * Copyright (C) 2015 Julien Fastré <julien.fastre@champs-libres.coop>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Chill\CustomFieldsBundle\EntityRepository\CustomFieldLongChoice;

use Doctrine\ORM\EntityRepository;
use Chill\CustomFieldsBundle\Entity\CustomFieldLongChoice\Option;

/**
 * 
 *
 * @author Julien Fastré <julien.fastre@champs-libres.coop>
 */
class OptionRepository extends EntityRepository
{
    /**
     * 
     * @param string $key
     * @return Option[]
     */
    public function findFilteredByKey($key, $includeParents = true, $active = true)
    {
        $qb = $this->createQueryBuilder('option');
        $qb->where('option.key = :key');
        
        if ($active === true){
            $qb->andWhere('option.active = true');
        }
        
        if ($includeParents === false) {
            $qb->andWhere('option.parent IS NOT NULL');
            
            if ($active === TRUE) {
                $qb->join('option.parent', 'p');
                $qb->andWhere('p.active = true');
            }
        }
        
        $qb->setParameter('key', $key);
        
        return $qb->getQuery()->getResult();
    }
    
    /**
     * 
     * @return string[]
     */
    public function getKeys()
    {
        $keys = $this->createQueryBuilder('option')
                ->select('option.key')
                ->distinct()
                ->getQuery()
                ->getScalarResult();
        
        return array_map(function($r) {
            return $r['key'];
        }, $keys);
    }
    
}
