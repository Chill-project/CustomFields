<?php

/*
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

namespace Chill\CustomFieldsBundle\Tests\Config;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Test the option Customizables_entities
 *
 * @author Julien Fastré <julien.fastre@champs-libres.coop>
 */
class ConfigCustomizablesEntitiesTest extends KernelTestCase
{
    public function testEmptyConfig()
    {
        self::bootKernel(array('environment' => 'test'));
        $customizableEntities = static::$kernel->getContainer()
                ->getParameter('chill_custom_fields.customizables_entities');
        
        $this->assertInternalType('array', $customizableEntities);
        $this->assertCount(0, $customizableEntities);
    }
}
