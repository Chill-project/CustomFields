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

class CustomFieldTitle implements CustomFieldInterface
{
    const TYPE = 'type';
    const TYPE_TITLE = 'title';
    const TYPE_SUBTITLE = 'subtitle';

    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }
 
    public function buildForm(FormBuilderInterface $builder, CustomField $customField)
    {
        $builder->add($customField->getSlug(), 'custom_field_title', array(
            'label' => false,
            'attr' => array(
                'class' => 'no-label',
                'title' => $customField->getName()[$this->requestStack->getCurrentRequest()->getLocale()],
                self::TYPE => $customField->getOptions()[self::TYPE ]
            )
        ));
    }

    public function render($value, CustomField $customField)
    {
       return $value; 
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
        return 'title';
    }

    public function buildOptionsForm(FormBuilderInterface $builder)
    {
        return $builder->add(self::TYPE, 'choice',
            array('choices' => array(
                self::TYPE_TITLE => self::TYPE_TITLE,
                self::TYPE_SUBTITLE => self::TYPE_SUBTITLE
            ))
        );
    }
}
