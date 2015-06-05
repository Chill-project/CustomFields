<?php

namespace Chill\CustomFieldsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Chill\CustomFieldsBundle\Service\CustomFieldProvider;
use Chill\CustomFieldsBundle\Entity\CustomField;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class CustomFieldType extends AbstractType
{
    /**
     *
     * @var CustomFieldProvider
     */
    private $customFieldProvider;
    
    private $culture = 'fr';
    
    
    public function __construct(CustomFieldProvider $compiler)
    {
        $this->customFieldProvider = $compiler;
    }
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $customFieldsList = array();
        
        foreach ($this->customFieldProvider->getAllFields() as $key => $field) {
            $customFieldsList[$key] = $field->getName();
        }
        
        $builder
            ->add('name', 'translatable_string')
            ->add('active')
            ->add('customFieldsGroup', 'entity', array(
               'class' => 'ChillCustomFieldsBundle:CustomFieldsGroup',
               'property' => 'name['.$this->culture.']'
            ))
            ->add('ordering', 'number')
            ->add('type', 'hidden', array('data' => $options['type']))
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) 
            {
                $customField = $event->getData();
                $form = $event->getForm();

                // check if the customField object is "new"
                // If no data is passed to the form, the data is "null".
                // This should be considered a new "customField"
                if (!$customField || null === $customField->getId()) {
                    $form->add('slug', 'text');
                }
            });
        
        
        $builder->add(
              $this->customFieldProvider
                    ->getCustomFieldByType($options['type'])
                    ->buildOptionsForm(
                        $builder
                          ->create('options', null, array('compound' => true))
                    )
              );
        
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function configureOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Chill\CustomFieldsBundle\Entity\CustomField'
        )); 
        
        $resolver->setRequired(array('type'))
              ->addAllowedValues(array('type' => 
                 array_keys($this->customFieldProvider->getAllFields())
              ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'custom_field_choice';
    }
}
