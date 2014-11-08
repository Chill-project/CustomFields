<?php

namespace Chill\CustomFieldsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Translation\TranslatorInterface;


class CustomFieldsGroupType extends AbstractType
{
    
    private $customizableEntities;
    
    /**
     *
     * @var \Symfony\Component\Translation\TranslatorInterface
     */
    private $translator;
    
    public function __construct(array $customizableEntities, TranslatorInterface $translator)
    {
        $this->customizableEntities = $customizableEntities;
        $this->translator = $translator;
    }
    
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //prepare translation
        $customizableEntites = array();
        
        foreach($this->customizableEntities as $key => $definition) {
            $customizableEntites[$definition['class']] = $this->translator->trans($definition['name']);
        }
        
        $builder
            ->add('name', 'translatable_string')
            ->add('entity', 'choice', array(
                'choices' => $customizableEntites
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Chill\CustomFieldsBundle\Entity\CustomFieldsGroup'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'custom_fields_group';
    }
}
