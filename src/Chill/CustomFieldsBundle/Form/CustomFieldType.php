<?php

namespace Chill\CustomFieldsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Chill\CustomFieldsBundle\Service\CustomFieldProvider;
use Chill\CustomFieldsBundle\Entity\CustomField;

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
            ->add('name', 'text')
            ->add('active')
            ->add('customFieldsGroup', 'entity', array(
               'class' => 'ChillCustomFieldsBundle:CustomFieldsGroup',
               'property' => 'name['.$this->culture.']'
            ))
            ->add('ordering', 'number')
        ;
        
        //add options field
        $optionsType = $this->customFieldProvider
              ->getCustomFieldByType($options['type'])
              ->buildOptionsForm($builder);
        if ($optionsType) {
            $builder->add('options', $optionsType);
        }
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
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
