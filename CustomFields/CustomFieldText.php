<?php

/*
 * Chill is a software for social workers
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

namespace Chill\CustomFieldsBundle\CustomFields;

use Chill\CustomFieldsBundle\CustomFields\CustomFieldInterface;
use Chill\CustomFieldsBundle\Entity\CustomField;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Chill\MainBundle\Templating\TranslatableStringHelper;

/**
 * @author Julien Fastré <julien.fastre@champs-libres.coop>
 * @author Marc Ducobu <marc@champs-libres.coop>
 */
class CustomFieldText implements CustomFieldInterface
{
    
    private $requestStack;
    
    /**
     * 
     * @var TwigEngine
     */
    private $templating;
    
    /**
     * @var TranslatableStringHelper Helper that find the string in current locale from an array of translation
     */
    private $translatableStringHelper;

    
    public function __construct(RequestStack $requestStack, TwigEngine $templating,
        TranslatableStringHelper $translatableStringHelper)
    {
        $this->requestStack = $requestStack;
        $this->templating = $templating;
        $this->translatableStringHelper = $translatableStringHelper;
    }
    
    const MAX_LENGTH = 'maxLength';
    const MULTIPLE_CF_INLINE ='multipleCFInline';
    
    /**
     * Create a form according to the maxLength option
     * 
     * if maxLength < 256 THEN the form type is 'text'
     * if not, THEN the form type is textarea
     * 
     * @param FormBuilderInterface $builder
     * @param CustomField $customField
     */
    public function buildForm(FormBuilderInterface $builder, CustomField $customField)
    {
        $options =  $customField->getOptions();

        $type = ($options[self::MAX_LENGTH] < 256) ? 'text' 
              : 'textarea';

        $attrArray = array();

        if(array_key_exists(self::MULTIPLE_CF_INLINE, $options) and
            $options[self::MULTIPLE_CF_INLINE]) {
            $attrArray['class'] = 'multiple-cf-inline';   
        }
         
        $builder->add($customField->getSlug(), $type, array(
            'label' => $this->translatableStringHelper->localize($customField->getName()),
            'required' => false,
            'attr' => $attrArray
        ));
    }

    public function render($value, CustomField $customField)
    {
        return $this->templating
            ->render('ChillCustomFieldsBundle:CustomFieldsRendering:text.html.twig', array('text' => $value));
    }

    public function serialize($value, CustomField $customField)
    {
        return $value;
    }

    public function deserialize($serialized, CustomField $customField)
    {
        return $serialized;
    }

    public function getName()
    {
        return 'text field';
    }

    public function buildOptionsForm(FormBuilderInterface $builder)
    {
        return $builder
            ->add(self::MAX_LENGTH, 'integer', array('empty_data' => 256))
            ->add(self::MULTIPLE_CF_INLINE, 'choice',  array(
                'choices'  => array('1' => 'True', '0' => 'False')))
        ;
    }
}
