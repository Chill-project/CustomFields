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

/**
 * Helpers for manipulating custom fields.
 * 
 * Herlpers for getting a certain custom field, for getting the raw value
 * of a custom field and for rendering the value of a custom field.
 * 
 * @author Julien Fastré <julien.fastre@champs-libres.coop>
 *
 */
class CustomFieldsHelper
{
    /** @var EntityManagerInterface $em The entity manager */
    private $em;
    
    /** @var CustomFieldProvider $provider Provider of all the declared custom 
     * fields */
    private $provider;
    
    /**  @var array $cache Matrix to cache all the custom fields. The array
     * is indexed by the EntityClass then the slug  */
    private $cache = array();
    
    /**
     * Constructor
     * 
     * @param EntityManagerInterface $em The entity manager
     * @param CustomFieldProvider $provider The customfield provider that 
     * contains all the declared custom fields
     */
    public function __construct(EntityManagerInterface $em,
        CustomFieldProvider $provider)
    {
        $this->em = $em;
        $this->provider = $provider;
    }
    
    /**
     * Set in cache all the custom fields of a given class containing some
     * custom fields.
     * 
     * @param object|string $class The given class.
     * @todo check if this fucntions has to call _cacheCustomFieldsGroup instead of 
     * _cacheCustomFields ?
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
     * Set in cache of the custom fields of a customfield Group.
     * 
     * @param CustomFieldsGroup $group The given CustomFieldsGroup
     */
    private function _cacheCustomFieldsGroup(CustomFieldsGroup $group)
    {
        foreach ($group->getCustomFields() as $field) {
            $this->cache[$group->getEntity()][$field->getSlug()] = $field;
        }
    }
    
    /**
     * Return a requested customField
     * 
     * @param object|string $class The requested class
     * @param string $slug The slug. BEWARE If the slug is null, throw a proper CustomFieldsHelperException
     * @return CustomField The requested CustomField
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
     * Return the stored/raw value of a custom field.
     * 
     * The method return null if the slug is not recorded.
     * 
     * @param array $fields  the **raw** array, as stored in the db
     * @param object|string $class
     * @param string $slug
     * @return mixed|null The value or null if the slug is not recorded
     */
    private function getCustomFieldValue(array $fields, $class, $slug)
    {
        return (isset($fields[$slug])) ? $this->provider
            ->getCustomFieldByType($this->getCustomField($class, $slug)->getType())
            ->deserialize($fields[$slug]) 
            : null;
    }
    
    public function isEmptyValue(array $fields, $classOrCustomField, $slug = null)
    {
        $customField = ($classOrCustomField instanceof CustomField) ? $classOrCustomField : $this->getCustomField($classOrCustomField, $slug);
        $slug = $customField->getSlug();
        $rawValue = (isset($fields[$slug])) ? $fields[$slug] : null;
        
        $customFieldType =  $this->provider->getCustomFieldByType($customField->getType());
        
        return $customFieldType->isEmptyValue($rawValue, $customField);
    }
    
    /**
     * Render the value of a custom field
     * 
     * @param array $fields the **raw** array, as stored in the db
     * @param CustomField|object|string $classOrCustomField the object OR the get_class($object) string OR The CustomField
     * @param string $documentType The type of document in which the rendered value is displayed ('html' or 'csv').
     * @param string $slug The slug of the custom field to render.
     * @param boolean $showIfEmpty If the widget must be rendered if the value is empty. An empty value is all values described as http://php.net/manual/fr/function.empty.php, except `FALSE`
     * @throws CustomFieldsHelperException if slug is missing
     * @return The representation of the value the customField.
     */
    public function renderCustomField(array $fields, $classOrCustomField, $documentType='html', $slug = null, $showIfEmpty = true)
    {
        $customField = ($classOrCustomField instanceof CustomField) ? $classOrCustomField : $this->getCustomField($classOrCustomField, $slug);
        $slug = $customField->getSlug();
        $rawValue = (isset($fields[$slug])) ? $fields[$slug] : null;
        $customFieldType =  $this->provider->getCustomFieldByType($customField->getType());
        
        return $customFieldType->render($rawValue, $customField, $documentType);
    }
}