<?php

/*
 * Chill is a software for social workers
 *
 * Copyright (C) 2014-2015, Champs Libres Cooperative SCRLFS, 
 * <http://www.champs-libres.coop>, <info@champs-libres.coop>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
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
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Chill\MainBundle\Templating\TranslatableStringHelper;

/**
 * Create a custom field number.
 * 
 * This number may have a min and max value, and a precision.
 * 
 * @author Julien Fastré <julien.fastre@champs-libres.coop>
 * @author Marc Ducobu <marc@champs-libres.coop>
 */
class CustomFieldNumber implements CustomFieldInterface
{
    /**
     * key for the minimal value of the field
     */
    const MIN = 'min';
    const MAX = 'max';
    const SCALE = 'scale';
    const POST_TEXT = 'post_text';
    
    /**
     *
     * @var TwigEngine
     */
    private $templating = NULL;
    
    /**
     *
     * @var TranslatableStringHelper
     */
    private $translatableStringHelper = NULL;
    
    public function __construct(TwigEngine $templating, TranslatableStringHelper $translatableStringHelper)
    {
        $this->templating = $templating;
        $this->translatableStringHelper = $translatableStringHelper;
    }
    
    public function buildForm(FormBuilderInterface $builder, CustomField $customField)
    {
        $options = $customField->getOptions();
        
        //select the type depending to the SCALE
        $type = ($options[self::SCALE] === 0 or $options[self::SCALE] === NULL)?
                'integer' : 'number';
        
        $fieldOptions = $this->prepareFieldOptions($customField, $type);
        
        $builder->add($customField->getSlug(), $type, $fieldOptions);
    }
    
    /**
     * prepare the options'form field
     * 
     * @param CustomField $customField
     * @param string $type
     * @return mixed[]
     */
    private function prepareFieldOptions(CustomField $customField, $type)
    {
        $options = $customField->getOptions();
        
        /**
         * @var mixed[] the formField options
         */
        $fieldOptions = array();
        
        // add required 
        $fieldOptions['required'] = False;
        
        //add label
        $fieldOptions['label'] = $this->translatableStringHelper->localize($customField->getName());
        
        // add constraints if required
        if ($options[self::MIN] !== NULL) {
            $fieldOptions['constraints'][] = new GreaterThanOrEqual(array('value' => $options[self::MIN]));
        }
        if ($options[self::MAX] !== NULL) {
            $fieldOptions['constraints'][] = new LessThanOrEqual(array('value' => $options[self::MAX]));
        }
        
        // add precision to options if required
        if ($type === 'number') {
            $fieldOptions['scale'] = $options[self::SCALE];
        }
        
        if (!empty($options[self::POST_TEXT])) {
            $fieldOptions['post_text'] = $options[self::POST_TEXT];
        }
        
        return $fieldOptions;
    }

    public function buildOptionsForm(FormBuilderInterface $builder)
    {
        return $builder
                ->add(self::MIN, 'number', array(
                    'scale' => 2,
                    'label' => 'Greater or equal than',
                    'required' => false
                ))
                ->add(self::MAX, 'number', array(
                    'scale' => 2,
                    'label' => 'Lesser or equal than',
                    'required' => false
                ))
                ->add(self::SCALE, 'integer', array(
                    'scale' => 0,
                    'label' => 'Precision',
                    'constraints' => array(
                        new GreaterThanOrEqual(array('value' => 0))
                    )
                ))
                ->add(self::POST_TEXT, 'text', array(
                    'label' => 'Text after the field'
                ))
                ;
                
    }

    public function deserialize($serialized, CustomField $customField)
    {
        return $serialized;
    }

    public function getName()
    {
        return 'Number';
    }

    public function render($value, CustomField $customField, $documentType = 'html')
    {
        $template = 'ChillCustomFieldsBundle:CustomFieldsRendering:number.'
                .$documentType.'.twig';
        $options = $customField->getOptions();

        return $this->templating
            ->render($template, array(
                'number' => $value, 
                'scale' => $options[self::SCALE],
                'post' => $options[self::POST_TEXT]
                ));
    }

    public function serialize($value, CustomField $customField)
    {
        return $value;
    }

}
