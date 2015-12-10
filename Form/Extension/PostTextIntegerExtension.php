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

namespace Chill\CustomFieldsBundle\Form\Extension;

use Symfony\Component\Form\Extension\Core\Type\IntegerType;

/**
 * This class add the PostTextExtension to integer fields
 *
 * @author Julien Fastré <julien.fastre@champs-libres.coop>
 */
class PostTextIntegerExtension extends PostTextExtension
{
    public function getExtendedType()
    {
        // return IntegerType::class;  !! only for symfony 2.8
        return 'integer';
    }

}
