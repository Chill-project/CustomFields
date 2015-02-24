<?php

/*
 * Copyright (C) 2015 Champs-Libres Cooperative <info@champs-libres.coop>
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

namespace Chill\CustomFieldsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Chill\MainBundle\Templating\TranslatableStringHelper;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Extension\Core\ChoiceList\SimpleChoiceList;

/**
 * This type create a Choice field with custom fields as choices.
 * 
 * This type can only be associated with a customFieldsGroup type. The field 
 * is populated when the data (a customFieldsGroup entity) is associated with
 * the form
 *
 * @author Julien Fastré <julien.fastre@champs-libres.coop>
 */
class LinkedCustomFieldsType extends AbstractType
{
    /**
     *
     * @var TranslatableStringHelper
     */
    private $translatableStringHelper;
    
    /**
     * The name for the choice field
     * 
     * Extracted from builder::getName
     * 
     * @var string
     */
    private $choiceName = 'choice';
    
    /**
     * the option of the form.
     * 
     * @internal options are stored at the class level to be reused by appendChoice, after data are setted
     * @var array
     */
    private $options = array();
    
    public function __construct(TranslatableStringHelper $helper)
    {
        $this->translatableStringHelper = $helper;
    }
    
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->choiceName = $builder->getName();
        $this->options = $options;
        
        $builder->addEventListener(FormEvents::POST_SET_DATA, 
                array($this, 'appendChoice'))
            ;
    }
    
    public function getParent()
    {
        return 'choice';
    }
    
    /**
     * append Choice on POST_SET_DATA event
     * 
     * Choices are extracted from custom_field_group (the data associated
     * with the root form)
     * 
     * @param FormEvent $event
     * @return void
     */
    public function appendChoice(FormEvent $event)
    {
        $rootForm = $this->getRootForm($event->getForm());
        $group = $rootForm->getData();
        
        if ($group === NULL) {
            return;
        }

        $choices = array();
        foreach($group->getCustomFields() as $customFields) {
            $choices[$customFields->getSlug()] = 
                    $this->translatableStringHelper
                        ->localize($customFields->getName());
        }
        
        $options = array_merge($this->options, array(
                'choice_list' => new SimpleChoiceList($choices),
            ));
        
        $event->getForm()->getParent()->add($this->choiceName, 'choice', 
                $options);
    }
    
    /**
     * Return the root form (i.e. produced from CustomFieldsGroupType::getForm)
     * 
     * @param FormInterface $form
     * @return FormInterface
     */
    private function getRootForm(FormInterface $form)
    {
        if ($form->getParent() === NULL) {
            return $form;
        } else {
            return $this->getRootForm($form->getParent());
        }
    }
    
    public function getName()
    {
        return 'custom_fields_group_linked_custom_fields';
    }

}
