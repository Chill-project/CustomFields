<?php

namespace Chill\CustomFieldsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;


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
        $entities = array();
        $customizableEntities = array();
        
        foreach($this->customizableEntities as $key => $definition) {
            $entities[$definition['class']] = $this->translator->trans($definition['name']);
            $customizableEntities[$definition['class']] = $definition;
        }
        
        $builder
            ->add('name', 'translatable_string')
            ->add('entity', 'choice', array(
                'choices' => $entities
            ))
        ;

        $builder->addEventListener(FormEvents::POST_SET_DATA, function(FormEvent $event) 
                use ($customizableEntities, $builder){
                    $form = $event->getForm();
                    $group = $event->getData();
                    
                    if (count($customizableEntities[$group->getEntity()]['options']) > 0) {
                        $optionBuilder = $builder
                                ->getFormFactory()
                                ->createBuilderForProperty(
                                        'Chill\CustomFieldsBundle\Entity\CustomFieldsGroup', 
                                        'options'
                                        )
                                ->create('options', null, array(
                                        'compound' => true, 
                                        'auto_initialize' => false,
                                        'required' => false)
                                    );
                    }
                    
                    foreach($customizableEntities[$group->getEntity()]['options'] as $key => $option) {
                                $optionBuilder
                                    ->add($key, $option['form_type'], $option['form_options'])
                            ;
                    }
                    if (isset($optionBuilder) && $optionBuilder->count() > 0) {
                        $form->add($optionBuilder->getForm());
                    }
                    
                });
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
