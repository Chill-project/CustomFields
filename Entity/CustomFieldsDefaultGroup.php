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
 * CustomFieldsDefaultGroup
 */
class CustomFieldsDefaultGroup
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $entity;

    /**
     * @var CustomFieldsGroup
     */
    private $customFieldsGroup;

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
     * Set entity
     *
     * @param string $entity
     *
     * @return CustomFieldsDefaultGroup
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

    /**
     * Set customFieldsGroup
     *
     * @param CustomFieldsGroup $customFieldsGroup
     *
     * @return CustomFieldsDefaultGroup
     */
    public function setCustomFieldsGroup($customFieldsGroup)
    {
        $this->customFieldsGroup = $customFieldsGroup;

        return $this;
    }

    /**
     * Get customFieldsGroup
     *
     * @return CustomFieldsGroup
     */
    public function getCustomFieldsGroup()
    {
        return $this->customFieldsGroup;
    }
}
