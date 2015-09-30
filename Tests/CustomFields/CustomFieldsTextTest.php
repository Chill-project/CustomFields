<?php

/*
 * Chill is a software for social workers
 * Copyright (C) 2014 Champs-Libres.coop
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

namespace Chill\CustomFieldsBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Chill\CustomFieldsBundle\Entity\CustomField;
use Chill\CustomFieldsBundle\CustomFields\CustomFieldText;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\DomCrawler\Crawler;
use Chill\CustomFieldsBundle\Tests\CustomFieldTestHelper;

/**
 * 
 *
 * @author Julien Fastré <julien.fastre@champs-libres.coop>
 */
class CustomFieldsTextTest extends WebTestCase
{
    /**
     *
     * @var \Chill\CustomFieldsBundle\Service\CustomFieldProvider 
     */
    private $customFieldProvider;
    
    public function setUp()
    {
        static::bootKernel();
        $this->customFieldProvider = static::$kernel->getContainer()
            ->get('chill.custom_field.provider');
    }
    
    public function testCustomFieldsTextExists()
    {
        $customField = $this->customFieldProvider->getCustomFieldByType('text');
        
        $this->assertInstanceOf('Chill\CustomFieldsBundle\CustomFields\CustomFieldInterface',
              $customField);
        $this->assertInstanceOf('Chill\CustomFieldsBundle\CustomFields\CustomFieldText',
              $customField);
    }
    
    public function testPublicFormRenderingLengthLessThan256()
    {
        $customField = new CustomField();
        $customField->setType('text')
            ->setOptions(array(CustomFieldText::MAX_LENGTH => 255))
            ->setSlug('slug')
            ->setOrdering(10)
            ->setActive(true)
            ->setName(array('en' => 'my label'));
        
        $crawler = CustomFieldTestHelper::getCrawlerForField($customField, $this, static::$kernel);
        
        $this->assertCount(1, $crawler->filter("input[type=text]"));
        $this->assertCount(1, $crawler->filter("label:contains('my label')"));   
    }
    
    public function testPublicFormRenderingLengthMoreThan25()
    {
        $customField = new CustomField();
        $customField->setType('text')
            ->setOptions(array(CustomFieldText::MAX_LENGTH => 256))
            ->setSlug('slug')
            ->setOrdering(10)
            ->setActive(true)
            ->setName(array('en' => 'my label'));
        
        $crawler = CustomFieldTestHelper::getCrawlerForField($customField, $this, static::$kernel);
        
        $this->assertCount(1, $crawler->filter("textarea"));
        $this->assertCount(1, $crawler->filter("label:contains('my label')"));        
    }
    
    public function testFormTextNew()
    {
        $client = static::createClient();
        
        $crawler = $client->request('GET', '/fr/admin/customfield/new?type=text');
        
        $this->assertTrue($client->getResponse()->isSuccessful());
        
        $form = $crawler->selectButton('custom_field_choice_submit')->form();
        $this->assertTrue($form->has('custom_field_choice[options][maxLength]'));
    }
}
