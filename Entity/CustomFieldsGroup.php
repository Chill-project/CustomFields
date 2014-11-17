<?php

/*
 * Chill is a software for social workers
 *
 * Copyright (C) 2014, Champs Libres Cooperative SCRLFS, <http://www.champs-libres.coop>
 *
 * This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 *  License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Chill\CustomFieldsBundle\Entity;

/**
 * CustomFieldGroup
 */
class CustomFieldsGroup
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var array
     */
    private $name;

    /**
     * @var string
     */
    private $entity;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $customFields;

        /**
     * Constructor
     */
    public function __construct()
    {
        $this->customFields = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add customField
     *
     * @param \Chill\CustomFieldsBundle\Entity\CustomField $customField
     *
     * @return CustomFieldsGroup
     */
    public function addCustomField(\Chill\CustomFieldsBundle\Entity\CustomField $customField)
    {
        $this->customFields[] = $customField;

        return $this;
    }

    /**
     * Remove customField
     *
     * @param \Chill\CustomFieldsBundle\Entity\CustomField $customField
     */
    public function removeCustomField(\Chill\CustomFieldsBundle\Entity\CustomField $customField)
    {
        $this->customFields->removeElement($customField);
    }
    
    /**
     * 
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCustomFields()
    {
       return $this->customFields;
    }

    
    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param array $name
     *
     * @return CustomFieldsGroup
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return array
     */
    public function getName($language = null)
    {
       //TODO set this in a service, PLUS twig function
       if ($language) {
          if (isset($this->name[$language])) {
             return $this->name[$language];
          } else {
             foreach ($this->name as $name) {
                if (!empty($name)) {
                   return $name;
                }
             }
          }
          
          return '';
          
       } else {
          return $this->name;
       }
    }

    /**
     * Set entity
     *
     * @param string $entity
     *
     * @return CustomFieldsGroup
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * Get entity
     *
     * @return string
     */
    public function getEntity()
    {
        return $this->entity;
    }

}
