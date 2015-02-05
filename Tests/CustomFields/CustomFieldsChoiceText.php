<?php

/*
 * Chill is a software for social workers
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

namespace Chill\CustomFieldsBundle\Tests\CustomFields;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Behat\MinkBundle\Test\MinkTestCase;

/**
 * Test CustomFieldsChoice
 *
 * @author Julien Fastré <julien.fastre@champs-libres.coop>
 */
class CustomFieldsChoiceText extends MinkTestCase
{
    public function testMink()
    {
        $session = $this->getMink()->getSession();
        
        $session->visit('http://localhost:8000/login');
        
        var_dump($session->getPage()->getContent());
        
        $this->assertTrue(
              $session->getPage()->hasContent('Bienvenue à Chill')
              );
    }
}
