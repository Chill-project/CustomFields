<?php

/*
 * Chill is a software for social workers
 *
 * Copyright (C) 2014-2015, Champs Libres Cooperative SCRLFS, 
 * <http://www.champs-libres.coop>, <info@champs-libres.coop>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Chill\CustomFieldsBundle\Service;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Receive all service tagged with 'chill.custom_field'
 *
 * @author Julien Fastré <julien.fastre@champs-libres.coop>
 */
class CustomFieldProvider implements ContainerAwareInterface
{
    private $servicesByType = array();
    
    /**
     *
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;
    
    public function addCustomField($serviceName, $type)
    {
        $this->servicesByType[$type] = $serviceName;
    }
    
    /**
     * 
     * @param string $type
     * @return CustomFieldInterface 
     */
    public function getCustomFieldByType($type)
    {
        if (isset($this->servicesByType[$type])) {
            return $this->servicesByType[$type];
        } else {
            throw new \LogicException('the custom field with type '.$type.' '
                    . 'is not found');
        }
    }

    public function setContainer(ContainerInterface $container = null)
    {
        if ($container === null) {
            throw new \LogicException('container should not be null');
        }
        
        $this->container = $container;
    }
    
    public function getAllFields()
    {
        return $this->servicesByType;
    }

}
