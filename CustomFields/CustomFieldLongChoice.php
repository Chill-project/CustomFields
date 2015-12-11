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
use Symfony\Component\Form\FormBuilderInterface;
use Chill\CustomFieldsBundle\Entity\CustomField;
use Chill\CustomFieldsBundle\EntityRepository\CustomFieldLongChoice\OptionRepository;
use Chill\MainBundle\Templating\TranslatableStringHelper;
use Chill\CustomFieldsBundle\Entity\CustomFieldLongChoice\Option;
use Chill\CustomFieldsBundle\Form\DataTransformer\CustomFieldDataTransformer;
use Symfony\Bridge\Twig\TwigEngine;

/**
 * 
 *
 * @author Julien Fastré <julien.fastre@champs-libres.coop>
 */
class CustomFieldLongChoice implements CustomFieldInterface
{
    /**
     *
     * @var OptionRepository
     */
    private $optionRepository;
    
    /**
     *
     * @var TranslatableStringHelper
     */
    private $translatableStringHelper;
    
    /**
     * @var TwigEngine
     */
    private $templating;
    
    const KEY = 'key';
    
    public function __construct(OptionRepository $optionRepository,
        TranslatableStringHelper $translatableStringHelper, 
        TwigEngine $twigEngine)
    {
        $this->optionRepository = $optionRepository;
        $this->translatableStringHelper = $translatableStringHelper;
        $this->templating = $twigEngine;
    }
    
    public function buildForm(FormBuilderInterface $builder, CustomField $customField)
    {
        $options = $customField->getOptions();
        $entries = $this->optionRepository->findFilteredByKey($options[self::KEY],
                false, true);
        //create a local copy of translatable string helper
        $translatableStringHelper = $this->translatableStringHelper;
        $builder->add($customField->getSlug(), 'select2_choice', array(
            'choices' => $entries,
            'choice_label' => function(Option $option) use ($translatableStringHelper) {
                return $translatableStringHelper->localize($option->getText());
            },
            'choice_value' => function ($key) use ($entries) {
                if ($key === NULL) {
                    return null;
                }
                return $key->getId();
            },
            'choices_as_values' => true,
            'multiple' => false,
            'expanded' => false,
            'group_by' => function(Option $option) use ($translatableStringHelper) {
                if ($option->hasParent()) {
                    return $translatableStringHelper->localize($option->getParent()->getText());
                } else {
                    return $translatableStringHelper->localize($option->getText());
                }
            },
            'label' => $translatableStringHelper->localize($customField->getName())
        ));
        $builder->get($customField->getSlug())
                ->addModelTransformer(new CustomFieldDataTransformer($this, $customField));
        
    }

    public function buildOptionsForm(FormBuilderInterface $builder)
    {
        //create a selector between different keys
        $keys = $this->optionRepository->getKeys();
        $choices = array();
        foreach ($keys as $key) {
            $choices[$key] = $key;
        }
        
        return $builder->add(self::KEY, 'choice', array(
            'choices' => $choices,
            'label' => 'Options key'
        ));
        
    }

    public function deserialize($serialized, \Chill\CustomFieldsBundle\Entity\CustomField $customField)
    {
        if ($serialized === NULL) {
            return NULL;
        }
        
        
        return $this->optionRepository->find($serialized);
    }

    public function getName()
    {
        return 'Long Choice';
    }

    public function render($value, \Chill\CustomFieldsBundle\Entity\CustomField $customField, $documentType = 'html')
    {
        $option = $this->deserialize($value, $customField);
        $template = 'ChillCustomFieldsBundle:CustomFieldsRendering:choice_long.'
                .$documentType.'.twig';
        
        return $this->templating
            ->render($template, array(
                'values' => array($option)
                ));
    }

    public function serialize($value, \Chill\CustomFieldsBundle\Entity\CustomField $customField)
    {
        if (!$value instanceof Option) {
            throw new \LogicException('the value should be an instance of '
                    . 'Chill\CustomFieldsBundle\Entity\CustomFieldLongChoice\Option, '
                    . is_object($value) ? get_class($value) : gettype($value).' given');
        }
        
        // we place the id in array, to allow in the future multiple select
        return $value->getId();
    }

}
