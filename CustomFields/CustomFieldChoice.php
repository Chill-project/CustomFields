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

use Chill\CustomFieldsBundle\CustomFields\CustomFieldInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Chill\CustomFieldsBundle\Entity\CustomField;
use Chill\CustomFieldsBundle\Form\Type\ChoicesType;
use Symfony\Component\HttpFoundation\RequestStack;
use Chill\CustomFieldsBundle\Form\Type\ChoicesListType;
use Chill\CustomFieldsBundle\Form\DataTransformer\CustomFieldDataTransformer;
use Chill\CustomFieldsBundle\Form\Type\ChoiceWithOtherType;
use Symfony\Bridge\Twig\TwigEngine;
use Chill\MainBundle\Templating\TranslatableStringHelper;

/**
 * 
 *
 * @author Julien Fastré <julien.fastre@champs-libres.coop>
 * @author Marc Ducobu <marc@champs-libes.coop>
 */
class CustomFieldChoice implements CustomFieldInterface
{
	const ALLOW_OTHER = 'other';
	const MULTIPLE = 'multiple';
	const EXPANDED = 'expanded';
	const CHOICES = 'choices';
	
	/**
	 * 
	 * @var RequestStack
	 */
	private $requestStack;
	
	private $defaultLocale;
	
	/**
	 * 
	 * @var TwigEngine
	 */
	private $templating;

    /**
     * @var TranslatableStringHelper Helper that find the string in current locale from an array of translation
     */
    private $translatableStringHelper;
	
	public function __construct(RequestStack $requestStack, $defaultLocale, TwigEngine $templating,
        TranslatableStringHelper $translatableStringHelper)
	{
	    $this->requestStack = $requestStack;
	    $this->defaultLocale = $defaultLocale;
	    $this->templating = $templating;
        $this->translatableStringHelper = $translatableStringHelper;
	}
	
    public function buildForm(FormBuilderInterface $builder, CustomField $customField)
    {
        //prepare choices
        $locale = $this->requestStack->getCurrentRequest()->getLocale();
        $choices = array();
        foreach($customField->getOptions()[self::CHOICES] as $persistedChoices) {
            if ($persistedChoices['active']){
                $choices[$persistedChoices['slug']] = $this->translatableStringHelper->localize($persistedChoices['name']);
            }
        }
        
        //prepare $options
        $options = array(
                'multiple' => $customField->getOptions()[self::MULTIPLE],
                'choices' => $choices,
                'required' => false,
                'label' =>  $this->translatableStringHelper->localize($customField->getName())
            );
        
        //if allow_other = true
        if ($customField->getOptions()[self::ALLOW_OTHER] === 1) {
            $builder->add(
                $builder->create($customField->getSlug(), new ChoiceWithOtherType(), $options)
                         ->addModelTransformer(new CustomFieldDataTransformer($this, $customField)));
            
        } else { //if allow_other = false
            //we add the 'expanded' to options
            $options['expanded'] = $customField->getOptions()[self::EXPANDED];
            
            $builder->add(
                     $builder->create($customField->getSlug(), 'choice', $options)
                         ->addModelTransformer(new CustomFieldDataTransformer($this, $customField))
            );
            
        }
        
    }

    public function buildOptionsForm(FormBuilderInterface $builder)
    {
        $builder->add(self::MULTIPLE, 'choice', array(
        	'expanded' => true,
        	'multiple' => false,
            'choices' => array(
                1 => 'Multiple',
                0 => 'Unique'
            ),
            'empty_data' => 0
            ))
            ->add(self::EXPANDED, 'choice', array(
                'expanded' => true,
                'multiple' => false,
                'choices' => array(
                    1 => 'Expanded',
                    0 => 'Non expanded'
                ),
                'empty_data' => 0
            ))
            ->add(self::ALLOW_OTHER, 'choice', array(
                'label' => 'Allow other',
                'choices' => array(
                    0 => 'No',
                    1 => 'Yes'
                ),
                'empty_data' => 0,
                'expanded' => true,
                'multiple' => false
            ))
            ->add(self::CHOICES, new ChoicesType(), array(
                'type' => new ChoicesListType($this->defaultLocale),
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

    public function render($value, CustomField $customField)
    {
        //extract the data. They are under a _choice key if they are stored with allow_other
        $data = (isset($value['_choices'])) ? $value['_choices'] : $value;
        
        $selected = (is_array($data)) ? $data : array($data);
        
        $choices = $customField->getOptions()[self::CHOICES];
        
        if (in_array('_other', $selected)){
            $choices[] = array('name' => $value['_other'], 'slug' => '_other');
        }
        
        return $this->templating
            ->render('ChillCustomFieldsBundle:CustomFieldsRendering:choice.html.twig',
                array(
                    'choices' => $choices, 
                    'selected' => $selected,
                    'multiple' => $customField->getOptions()[self::MULTIPLE],
                    'expanded' => $customField->getOptions()[self::EXPANDED],
                    'locale' => $this->defaultLocale
                )
            );
    }

    public function serialize($value, CustomField $customField)
    {
        return $value;
    }
}
