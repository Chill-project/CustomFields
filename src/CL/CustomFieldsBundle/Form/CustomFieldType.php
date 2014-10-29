<?php

namespace CL\CustomFieldsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use CL\CustomFieldsBundle\Service\CustomFieldProvider;
use CL\CustomFieldsBundle\Entity\CustomField;

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
            ->add('label')
            ->add('active')
            ->add('customFieldsGroup', 'entity', array(
               'class' => 'CLCustomFieldsBundle:CustomFieldsGroup',
               'property' => 'name['.$this->culture.']'
            ))
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
            'data_class' => 'CL\CustomFieldsBundle\Entity\CustomField'
        )); 
        $resolver->addAllowedTypes(array('type' => 'string'));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'custom_field_choice';
    }
}
