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

namespace Chill\CustomFieldsBundle\CustomFields;

use Chill\CustomFieldsBundle\CustomFields\CustomFieldInterface;
use Chill\CustomFieldsBundle\Entity\CustomField;

/**
 * 
 *
 * @author Julien Fastré <julien.fastre@champs-libres.coop>
 */
abstract class AbstractCustomField implements CustomFieldInterface
{
    public function isEmptyValue($value, CustomField $customField)
    {
        return (empty($value) and $value !== FALSE);
    }
    
}
