<?php

namespace CL\CustomFieldsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BlopEntity2Type extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $em = $options['em'];

        $customFields = $em
            ->getRepository('CLCustomFieldsBundle:CustomField')
            ->findAll();

        foreach ($customFields as $cf) {
            if($cf->getType() === 'ManyToOne(Adress)') {
                $builder->add($cf->getLabel(), 'entity', array(
                    'class' => 'CLCustomFieldsBundle:Adress',
                    'property' => 'data'
                ));
            } else if ($cf->getType() === 'ManyToOnePersist(Adress)') {
                $builder->add($cf->getLabel(), new AdressType());
            } else if($cf->getType() === 'ManyToMany(Adress)') {
                $builder->add($cf->getLabel(), 'entity', array(
                    'class' => 'CLCustomFieldsBundle:Adress',
                    'property' => 'data',
                    'multiple' => true
                ));
            } else if ($cf->getType() === 'text') {
                $builder->add($cf->getLabel(), 'text');
            }
        }     
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CL\CustomFieldsBundle\Entity\BlopEntity2'
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
        return 'cl_customfieldsbundle_blopentity2';
    }
}
