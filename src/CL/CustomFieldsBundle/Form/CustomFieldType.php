<?php

namespace CL\CustomFieldsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use CL\CustomFieldsBundle\CustomFields\CustomFieldCompiler;
use CL\CustomFieldsBundle\Entity\CustomField;

class CustomFieldType extends AbstractType
{
    /**
     *
     * @var \CL\CustomFieldsBundle\CustomFields\CustomFieldCompiler
     */
    private $customFieldCompiler;
    
    
    public function __construct(CustomFieldCompiler $compiler)
    {
        $this->customFieldCompiler = $compiler;
    }
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $customFieldsList = array();
        
        foreach ($this->customFieldCompiler->getAllFields() as $key => $field) {
            $customFieldsList[$key] = $field->getName();
        }
        
        $builder
            ->add('label')
            ->add('type', 'choice', array(
                'choices' => $customFieldsList,
                'expanded' => false,
                'multiple' => false
            ))
            ->add('active')
            ->add('relation', 'choice', array(
                'choices' => array(
                    CustomField::ONE_TO_ONE => 'one to one ',
                    CustomField::ONE_TO_MANY => 'one to many'
                )
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CL\CustomFieldsBundle\Entity\CustomField'
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
