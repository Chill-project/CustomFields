<?php

namespace Chill\CustomFieldsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @internal Ne fonctionne pas encore
 */
class AdressType extends AbstractType
{
    

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('data', 'entity', array(
               
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
//        $resolver->setDefaults(array(
//            'data_class' => 'Chill\CustomFieldsBundle\Entity\Adress',
//            'class' => 'Chill\CustomFieldsBundle\Entity\Adress'
//        ));
    }
    
    public function getParent()
    {
        return 'entity';
    }
    
    /**
     * @return string
     */
    public function getName()
    {
        return 'adress';
    }
}
