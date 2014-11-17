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
 * CustomField
 */
class CustomField
{
    /**
     * @var integer
     */
    private $id;

    
    private $slug;

    /**
     * @var string
     */
    private $type;

    /**
     * @var boolean
     */
    private $active = true;
    
    /**
     *
     * @var array
     */
    private $options = array();
    
    /**
     * @var array
     */
    private $name;

    /**
     * @var float
     */
    private $ordering;
    
    
    const ONE_TO_ONE = 1;
    const ONE_TO_MANY = 2;

    /**
     * @var CustomFieldsGroup
     */
    private $customFieldGroup;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
    
    function getSlug()
    {
        return $this->slug;
    }

    function getOptions()
    {
        return $this->options;
    } 


    /**
     * Set type
     *
     * @param string $type
     *
     * @return CustomField
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    
    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return CustomField
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Get customFieldGroup
     *
     * @return CustomFieldsGroup
     */
    public function getCustomFieldsGroup()
    {
        return $this->customFieldGroup;
    }

    /**
     * Set customFieldGroup
     *
     * @param \Chill\CustomFieldsBundle\Entity\CustomFieldsGroup $customFieldGroup
     *
     * @return CustomField
     */
    public function setCustomFieldsGroup(\Chill\CustomFieldsBundle\Entity\CustomFieldsGroup $customFieldGroup = null)
    {
        $this->customFieldGroup = $customFieldGroup;

        return $this;
    }

    /**
     * Set name
     *
     * @param array $name
     *
     * @return CustomField
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
    public function getName($locale = null)
    {
        if ($locale) {
          if (isset($this->name[$locale])) {
             return $this->name[$locale];
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
       };
    }

    /**
     * Set order
     *
     * @param float $order
     *
     * @return CustomField
     */
    public function setOrdering($order)
    {
        $this->ordering = $order;

        return $this;
    }

    /**
     * Get order
     *
     * @return float
     */
    public function getOrdering()
    {
        return $this->ordering;
    }

    /**
     * Set options
     *
     * @param array $options
     *
     * @return CustomField
     */
    public function setOptions(array $options)
    {
        $this->options = $options;

        return $this;
    }

    
    public function setSlug($slug)
    {
        $this->slug = $slug;
        return $this;
    }


}
