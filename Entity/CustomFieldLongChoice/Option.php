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

namespace Chill\CustomFieldsBundle\Entity\CustomFieldLongChoice;

/**
 * 
 *
 * @author Julien Fastré <julien.fastre@champs-libres.coop>
 */
class Option
{
    /**
     *
     * @var int
     */
    private $id;
    
    /**
     *
     * @var string
     */
    private $key;
    
    /**
     * a json representation of text (multilingual)
     *
     * @var array
     */
    private $text;
    
    /**
     *
     * @var \Doctrine\Common\Collections\Collection
     */
    private $children;
    
    /**
     *
     * @var Option
     */
    private $parent;
    
    /**
     *
     * @var string
     */
    private $internalKey = '';
    
    /**
     *
     * @var boolean
     */
    private $active = true;
    
    public function getId()
    {
        return $this->id;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function getText()
    {
        return $this->text;
    }

    public function getChildren()
    {
        return $this->children;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }

    public function setText(array $text)
    {
        $this->text = $text;
        return $this;
    }

    public function setParent(Option $parent = null)
    {
        $this->parent = $parent;
        $this->key = $parent->getKey();
        return $this;
    }
    
    /**
     * 
     * @return boolean
     */
    public function hasParent()
    {
        return $this->parent === NULL ? false : true;
    }
    
    public function getInternalKey()
    {
        return $this->internalKey;
    }

    public function isActive()
    {
        return $this->active;
    }
    
    public function getActive()
    {
        return $this->isActive();
    }

    public function setInternalKey($internal_key)
    {
        $this->internalKey = $internal_key;
        return $this;
    }

    public function setActive($active)
    {
        $this->active = $active;
        return $this;
    }


}
