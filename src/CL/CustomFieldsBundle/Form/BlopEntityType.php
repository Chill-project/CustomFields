<?php

namespace CL\CustomFieldsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use CL\CustomFieldsBundle\Form\Type\CustomFieldType;

class BlopEntityType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
   public function buildForm(FormBuilderInterface $builder, array $options)
   {
      $entityManager = $options['em'];

      $builder
         ->add('field1')
         ->add('field2')
         //->add('adress', new AdressType())
         ->add('customField', 'custom_field')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CL\CustomFieldsBundle\Entity\BlopEntity',
            'cascade_validation' => true
        ));

        // supprimer ça en definissant dans services
        $resolver->setRequired(array(
            'em',
        ));

        $resolver->setAllowedTypes(array(
            'em' => 'Doctrine\Common\Persistence\ObjectManager',
        ));

    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'cl_customfieldsbundle_blopentity';
    }
}
