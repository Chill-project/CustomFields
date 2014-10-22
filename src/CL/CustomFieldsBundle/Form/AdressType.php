<?php

namespace CL\CustomFieldsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @internal Ne fonctionne pas encore
 */
class AdressType extends AbstractType
{
    

//    /**
//     * @param FormBuilderInterface $builder
//     * @param array $options
//     */
//    public function buildForm(FormBuilderInterface $builder, array $options)
//    {
//        $builder
//            ->add('data')
//        ;
//    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CL\CustomFieldsBundle\Entity\Adress',
            'class' => 'CL\CustomFieldsBundle\Entity\Adress'
        ));
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
