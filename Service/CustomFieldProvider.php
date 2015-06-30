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
 * Receive all the services tagged with 'chill.custom_field'.
 * 
 * The services tagged with 'chill.custom_field' are services used to declare
 * a new custom field type. The tag must contain a 'type' variable (that must
 * be unique), this type is used to identify this custom field in the form
 * declration 
 * 
 * For example (in services.yml) :
 *  services:
 *      chill.icp2.type:
 *          tags:
 *              - { name: 'chill.custom_field', type: 'ICPC2' }
 * 
 * @author Julien Fastré <julien.fastre@champs-libres.coop>
 */
class CustomFieldProvider implements ContainerAwareInterface
{
    /** @var array $servicesByType The services indexes by the type */
    private $servicesByType = array();
    
    /** @var Container $container The container */
    private $container;
    
    /**
     * Add a new custom field to the provider
     * 
     * @param type $serviceName The name of the service (declared in service.yml)
     * @param type $type The type of the service (that is used in the form to 
     * add this type)
     */
    public function addCustomField($serviceName, $type)
    {
        $this->servicesByType[$type] = $serviceName;
    }
    
    /**
     * Get a custom field stored in the provider. The custom field is identified
     * by its type.
     * 
     * @param string $type The type of the wanted service
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

    /*
     * (non-PHPdoc)
     * @see \Symfony\Component\DependencyInjection\ContainerAwareInterface::setContainer()
     */
    public function setContainer(ContainerInterface $container = null)
    {
        if ($container === null) {
            throw new \LogicException('container should not be null');
        }
        
        $this->container = $container;
    }
    
    /**
     * Get all the custom fields known by the provider
     * 
     * @return array Array of the known custom fields indexed by the type.
     */
    public function getAllFields()
    {
        return $this->servicesByType;
    }
}
