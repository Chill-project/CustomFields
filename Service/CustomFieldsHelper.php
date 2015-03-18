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

use Doctrine\ORM\EntityManagerInterface;
use Chill\CustomFieldsBundle\Service\CustomFieldProvider;
use Chill\CustomFieldsBundle\Entity\CustomFieldsGroup;
use Chill\CustomFieldsBundle\Entity\CustomField;
use Symfony\Bundle\TwigBundle\TwigEngine;

/**
 * 
 * @author Julien Fastré <julien.fastre@champs-libres.coop>
 *
 */
class CustomFieldsHelper
{
    /**
     * 
     * @var EntityManagerInterface
     */
    private $em;
    
    /**
     * 
     * @var CustomFieldProvider
     */
    private $provider;
    
    
    /**
     * 
     * @var array
     */
    private $cache = array();
    
    
    public function __construct(EntityManagerInterface $em, CustomFieldProvider $provider)
    {
        $this->em = $em;
        $this->provider = $provider;
    }
    
    /**
     * 
     * @param object|string $class
     */
    private function _cacheCustomFields($class)
    {
        $customFieldsGroups = $this->em->getRepository('ChillCustomFieldsBundle:CustomFieldsGroup')
            ->findBy(array('entity' => (is_string($class)) ? $class : get_class($class)));
        
        if (!$customFieldsGroups) {
            throw CustomFieldsHelperException::customFieldsGroupNotFound((is_string($class)) ? $class : get_class($class));
        }
        
        foreach ($customFieldsGroup as $cfGroup) {
            $this->_cacheCustomFields($cfGroup);
        }
    }
    /**
     * 
     * @param CustomFieldsGroup $group
     */
    private function _cacheCustomFieldsGroup(CustomFieldsGroup $group)
    {
        foreach ($group->getCustomFields() as $field) {
            $this->cache[$group->getEntity()][$field->getSlug()] = $field;
        }
    }
    
    /**
     * 
     * @param object|string $class
     * @param string $slug If the slug is null, throw a proper CustomFieldsHelperException
     * @return CustomField
     * @throws CustomFieldsHelperException if $slug is null
     */
    public function getCustomField($class, $slug = null)
    {
        if (!$slug) {
            throw CustomFieldsHelperException::slugIsMissing();
        }
        
        $resolveClass = (is_string($class)) ? $class : get_class($class);
        if (!$this->cache[$resolveClass][$slug]) {
            $this->_cacheCustomFields($resolveClass);
        }
        
        return $this->cache[$resolveClass][$slug];
    }
    
    /**
     * 
     * @param array $fields
     * @param object|string $class
     * @param string $slug
     * @return mixed|null null if the slug is not recorded
     */
    private function getCustomFieldValue(array $fields, $class, $slug)
    {
        return (isset($fields[$slug])) ? $this->provider
            ->getCustomFieldByType($this->getCustomField($class, $slug)->getType())
            ->deserialize($fields[$slug]) 
            : null;
    }
    
    /**
     * 
     * @param array $fields the **raw** array, as stored in the db
     * @param CustomField|object|string $classOrCustomField the object OR the get_class($object) string OR The CustomField
     * @param string slug The Slug to render, if CustomField is not Given
     * @param string $slug the slug you want to render. The html is be safe.
     * @throws CustomFieldsHelperException if slug is missing
     */
    public function renderCustomField(array $fields, $classOrCustomField, $type='html', $slug = null)
    {
        $customField = ($classOrCustomField instanceof CustomField) ? $classOrCustomField : $this->getCustomField($classOrCustomField, $slug);
        $slug = $customField->getSlug();
        $rawValue = (isset($fields[$slug])) ? $fields[$slug] : null;
        
        return $this->provider->getCustomFieldByType($customField->getType())
            ->render($rawValue, $customField, $type);
    }
    
}