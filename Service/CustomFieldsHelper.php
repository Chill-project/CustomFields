<?php

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
    public function renderCustomField(array $fields, $classOrCustomField, $slug = null)
    {
        $customField = ($classOrCustomField instanceof CustomField) ? $classOrCustomField : $this->getCustomField($classOrCustomField, $slug);
        $slug = $customField->getSlug();
        $rawValue = (isset($fields[$slug])) ? $fields[$slug] : null;
        
        return $this->provider->getCustomFieldByType($customField->getType())
            ->render($rawValue, $customField);
    }
    
}