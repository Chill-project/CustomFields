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
    /**
     * Test that the config does work if the option 
     * chill_custom_fields.customizables_entities IS NOT present
     * 
     * In this case, parameter 'chill_custom_fields.customizables_entities'
     * should be an empty array in container
     */
    public function testNotPresentInConfig()
    {
        self::bootKernel(array('environment' => 'test'));
        $customizableEntities = static::$kernel->getContainer()
                ->getParameter('chill_custom_fields.customizables_entities');
        
        $this->assertInternalType('array', $customizableEntities);
        $this->assertCount(0, $customizableEntities);
    }
    
    /**
     * Test that the 'chill_custom_fields.customizables_entities' is filled
     * correctly with a minimal configuration.
     * 
     * @internal use a custom config environment
     */
    public function testNotEmptyConfig()
    {
        self::bootKernel(array('environment' => 'test_customizable_entities_test_not_empty_config'));
        $customizableEntities = static::$kernel->getContainer()
                ->getParameter('chill_custom_fields.customizables_entities');
        
        $this->assertInternalType('array', $customizableEntities);
        $this->assertCount(1, $customizableEntities);
        
        foreach($customizableEntities as $key => $config) {
            $this->assertInternalType('array', $config);
            $this->assertArrayHasKey('name', $config);
            $this->assertArrayHasKey('class', $config);
        }
    }
}
