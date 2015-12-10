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

namespace Chill\CustomFieldsBundle\Tests;

use Chill\CustomFieldsBundle\CustomFields\CustomFieldNumber;
use Symfony\Component\Form\FormBuilderInterface;
use Chill\CustomFieldsBundle\Entity\CustomField;
use Chill\CustomFieldsBundle\Entity\CustomFieldsGroup;
use Chill\CustomFieldsBundle\Form\CustomFieldsGroupType;

/**
 * Test CustomFieldsNumber
 *
 * @author Julien Fastré <julien.fastre@champs-libres.coop>
 */
class CustomFieldsNumberTest extends \Symfony\Bundle\FrameworkBundle\Test\WebTestCase
{
    /**
     *
     * @var CustomFieldNumber
     */
    private $customFieldNumber;
    
    /**
     *
     * @var FormBuilderInterface
     */
    private $formBuilder;
    
    public function setUp()
    {
        self::bootKernel();
        
        $this->customFieldNumber = self::$kernel->getContainer()
                ->get('chill.custom_field.number');
        
        $this->formBuilder = self::$kernel->getContainer()
                ->get('form.factory')
                ->createBuilder('form', null, array(
                    'csrf_protection' => false,
                    'csrf_field_name' => '_token'
                ));
        
        $request = new \Symfony\Component\HttpFoundation\Request();
        $request->setLocale('fr');
        
        self::$kernel->getContainer()
                ->get('request_stack')
                ->push($request);
    }
    
    /**
     * 
     * @param mixed[] $options
     * @return CustomField
     */
    private function createCustomFieldNumber($options)
    {
        return (new CustomField())
            ->setType('number')
            ->setActive(true)
            ->setOrdering(10)
            ->setSlug('default')
            ->setName(array('fr' => 'default'))
            ->setOptions($options);
    }
    
    public function testCreateValidForm()
    {
        $cf = $this->createCustomFieldNumber(array(
            'min' => null,
            'max' => null,
            'scale' => null,
            'post_text' => null
        ));
        
        $this->customFieldNumber->buildForm($this->formBuilder, $cf);
        
        $form = $this->formBuilder->getForm();
        
        $form->submit(array('default' => 10));
        
        $this->assertTrue($form->isSynchronized());
        $this->assertEquals(10, $form['default']->getData());  
    }
    
    public function testCreateInvalidFormValueGreaterThanMaximum()
    {
        $cf = $this->createCustomFieldNumber(array(
            'min' => null,
            'max' => 10,
            'scale' => null,
            'post_text' => null
        ));
        
        $this->customFieldNumber->buildForm($this->formBuilder, $cf);
        
        $form = $this->formBuilder->getForm();
        
        $form->submit(array('default' => 100));
        
        $this->assertTrue($form->isSynchronized());
        $this->assertFalse($form->isValid()); 
        $this->assertEquals(1, count($form['default']->getErrors()));
    }
    
    public function testCreateInvalidFormValueLowerThanMinimum()
    {
        $cf = $this->createCustomFieldNumber(array(
            'min' => 1000,
            'max' => null,
            'scale' => null,
            'post_text' => null
        ));
        
        $this->customFieldNumber->buildForm($this->formBuilder, $cf);
        
        $form = $this->formBuilder->getForm();
        
        $form->submit(array('default' => 100));
        
        $this->assertTrue($form->isSynchronized());
        $this->assertFalse($form->isValid()); 
        $this->assertEquals(1, count($form['default']->getErrors()));
    }
    
    public function testRequiredFieldIsFalse()
    {
        $cf = $this->createCustomFieldNumber(array(
            'min' => 1000,
            'max' => null,
            'scale' => null,
            'post_text' => null
        ));
        $cf->setRequired(false);
        
        $cfGroup = (new \Chill\CustomFieldsBundle\Entity\CustomFieldsGroup())
                ->addCustomField($cf);
        
        $form = static::$kernel->getContainer()->get('form.factory')
                ->createBuilder('custom_field', array(), array(
                    'group' => $cfGroup
                ))
            ->getForm();
        
        $this->assertFalse($form['default']->isRequired(),
                "The field should not be required");
    }
    
    public function testRequiredFieldIsTrue()
    {
        $cf = $this->createCustomFieldNumber(array(
            'min' => 1000,
            'max' => null,
            'scale' => null,
            'post_text' => null
        ));
        $cf->setRequired(true);
        
        $cfGroup = (new \Chill\CustomFieldsBundle\Entity\CustomFieldsGroup())
                ->addCustomField($cf);
        
        $form = static::$kernel->getContainer()->get('form.factory')
                ->createBuilder('custom_field', array(), array(
                    'group' => $cfGroup
                ))
            ->getForm();
        
        $this->assertTrue($form['default']->isRequired(),
                "The field should be required");
    }
    
    
}
