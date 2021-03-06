<?php

/*
 * Chill is a software for social workers
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

namespace Chill\CustomFieldsBundle\CustomFields;

use Chill\CustomFieldsBundle\Form\Type\ChoicesListType;
use Chill\CustomFieldsBundle\Form\Type\ChoicesType;
use Chill\CustomFieldsBundle\Form\Type\ChoiceWithOtherType;
use Chill\CustomFieldsBundle\CustomFields\CustomFieldInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Chill\CustomFieldsBundle\Entity\CustomField;
use Symfony\Component\HttpFoundation\RequestStack;
use Chill\CustomFieldsBundle\Form\DataTransformer\CustomFieldDataTransformer;
use Symfony\Bridge\Twig\TwigEngine;
use Chill\MainBundle\Templating\TranslatableStringHelper;
use Symfony\Component\Translation\Translator;

/**
 * 
 *
 * @author Julien Fastré <julien.fastre@champs-libres.coop>
 * @author Marc Ducobu <marc@champs-libes.coop>
 */
class CustomFieldChoice extends AbstractCustomField
{
    const ALLOW_OTHER = 'other';
    const OTHER_VALUE_LABEL = 'otherValueLabel';
    const MULTIPLE = 'multiple';
    const EXPANDED = 'expanded';
    const CHOICES = 'choices';
	
    /**
     * 
     * @var RequestStack
     */
    private $requestStack;

    private $defaultLocales;

    /**
     * 
     * @var TwigEngine
     */
    private $templating;

    /**
     * @var TranslatableStringHelper Helper that find the string in current locale from an array of translation
     */
    private $translatableStringHelper;
	
    public function __construct(
        RequestStack $requestStack, 
        Translator $translator, 
        TwigEngine $templating,
        TranslatableStringHelper $translatableStringHelper)
    {
        $this->requestStack = $requestStack;
        $this->defaultLocales = $translator->getFallbackLocales();
        $this->templating = $templating;
        $this->translatableStringHelper = $translatableStringHelper;
    }
	
    public function buildForm(FormBuilderInterface $builder, CustomField $customField)
    {
        //prepare choices
        $locale = $this->requestStack->getCurrentRequest()->getLocale();
        $choices = array();
        $customFieldOptions = $customField->getOptions();

        foreach($customFieldOptions[self::CHOICES] as $persistedChoices) {
            if ($persistedChoices['active']){
                $choices[$persistedChoices['slug']] = $this->translatableStringHelper->localize($persistedChoices['name']);
            }
        }
        
        //prepare $options
        $options = array(
            'multiple' => $customFieldOptions[self::MULTIPLE],
            'choices' => $choices,
            'required' => $customField->isRequired(),
            'label' =>  $this->translatableStringHelper->localize($customField->getName()));

        //if allow_other = true
        if ($customFieldOptions[self::ALLOW_OTHER] == true) {
            $otherValueLabel = null;
            if(array_key_exists(self::OTHER_VALUE_LABEL, $customFieldOptions)) {
                $otherValueLabel = $this->translatableStringHelper->localize(
                    $customFieldOptions[self::OTHER_VALUE_LABEL]
                );
            }

            $builder->add(
                $builder
                    ->create(
                        $customField->getSlug(),
                        new ChoiceWithOtherType($otherValueLabel),
                        $options)
                    ->addModelTransformer(new CustomFieldDataTransformer($this, $customField)));
            
        } else { //if allow_other = false
            //we add the 'expanded' to options
            $options['expanded'] = $customFieldOptions[self::EXPANDED];
            
            $builder->add(
                $builder->create($customField->getSlug(), 'choice', $options)
                    ->addModelTransformer(new CustomFieldDataTransformer($this, $customField))
            );
        }
    }

    public function buildOptionsForm(FormBuilderInterface $builder)
    {
        $builder
            ->add(self::MULTIPLE, 'choice', array(
                'expanded' => true,
                'multiple' => false,
                'choices' => array(
                    1 => 'Multiple',
                    0 => 'Unique'),
                'empty_data' => 0,
                'label' => 'Multiplicity'
                ))
            ->add(self::EXPANDED, 'choice', array(
                'expanded' => true,
                'multiple' => false,
                'choices' => array(
                    1 => 'Expanded',
                    0 => 'Non expanded'),
                'empty_data' => 0,
                'label' => 'Choice display'
                ))
            ->add(self::ALLOW_OTHER, 'choice', array(
                'label' => 'Allow other',
                'choices' => array(
                    0 => 'No',
                    1 => 'Yes'),
                'empty_data' => 0,
                'expanded' => true,
                'multiple' => false
            ))
            ->add(self::OTHER_VALUE_LABEL, 'translatable_string', array(
                'label' => 'Other value label (empty if use by default)'))
            ->add(self::CHOICES, new ChoicesType(), array(
                'type' => new ChoicesListType($this->defaultLocales),
                'allow_add' => true
            ));
            
            return $builder;
    }

    public function deserialize($serialized, CustomField $customField)
    {
        return $serialized;
    }

    public function getName()
    {
        return 'Choices';
    }
    
    public function isEmptyValue($value, CustomField $customField)
    {
        if ($value === NULL) {
            return true;
        }
        
        // if only one choice...
        if (is_string($value)) {
            return empty($value);
        }
        
        // if multiple choice OR multiple/single choice with other
        if (is_array($value))
        {
            // if allow other
            if (isset($value['_choices'])) {
                if ($value['_choices'] === NULL) {
                    return true;
                }
                if (is_string($value['_choices'])) {
                    return empty($value);
                }
                if (is_array($value['_choices'])){
                    return count($value['_choices']) > 0;
                }
            } else { // we do not have 'allow other'
                return count($value) > .0;
            }
        }
        
        throw \LogicException("This case is not expected.");
    }

    /**
     * 
     * @internal this function is able to receive data whichever is the value of "other", "multiple"
     * @param mixed $value
     * @param CustomField $customField
     * @return string html representation
     */
    public function render($value, CustomField $customField, $documentType = 'html')
    {
        //extract the data. They are under a _choice key if they are stored with allow_other
        $data = (isset($value['_choices'])) ? $value['_choices'] : $value;
        $selected = (is_array($data)) ? $data : array($data);       
        $choices = $customField->getOptions()[self::CHOICES];
        
        if (in_array('_other', $selected)){
            $choices[] = array('name' => $value['_other'], 'slug' => '_other');
        }
        
        $template = 'ChillCustomFieldsBundle:CustomFieldsRendering:choice.html.twig';
        if($documentType == 'csv') {
            $template = 'ChillCustomFieldsBundle:CustomFieldsRendering:choice.csv.twig';
        }

        return $this->templating
            ->render($template,
                array(
                    'choices' => $choices, 
                    'selected' => $selected,
                    'multiple' => $customField->getOptions()[self::MULTIPLE],
                    'expanded' => $customField->getOptions()[self::EXPANDED],
                    'locales' => $this->defaultLocales
                )
            );
    }

    public function serialize($value, CustomField $customField)
    {
        return $value;
    }
}
