<?php

namespace Chill\CustomFieldsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Chill\CustomFieldsBundle\Service\CustomFieldProvider;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Doctrine\Common\Persistence\ObjectManager;
use Chill\CustomFieldsBundle\Form\DataTransformer\CustomFieldsGroupToIdTransformer;


class CustomFieldType extends AbstractType
{
    /**
     *
     * @var CustomFieldProvider
     */
    private $customFieldProvider;
    
    private $culture = 'fr';
    
    /**
     * @var ObjectManager
     */
    private $om;
    
    
    public function __construct(CustomFieldProvider $compiler,
          ObjectManager $om)
    {
        $this->customFieldProvider = $compiler;
        $this->om = $om;
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
            ->add('active', 'checkbox', array('required' => false));
        
        if ($options['group_widget'] === 'entity') {
            $builder->add('customFieldsGroup', 'entity', array(
               'class' => 'ChillCustomFieldsBundle:CustomFieldsGroup',
               'property' => 'name['.$this->culture.']'
            ));
        } elseif ($options['group_widget'] === 'hidden') {
            $builder->add('customFieldsGroup', 'hidden');
            $builder->get('customFieldsGroup')
                  ->addViewTransformer(new CustomFieldsGroupToIdTransformer($this->om));
        } else {
            throw new \LogicException('The value of group_widget is not handled');
        }
        
        $builder
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
                         ->setRequired(false)
                    )
            );
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Chill\CustomFieldsBundle\Entity\CustomField'
        )); 
        
        $resolver->setRequired(array('type', 'group_widget'))
              ->addAllowedValues(array(
                 'type' => array_keys($this->customFieldProvider->getAllFields()),
                 'group_widget' => array('hidden', 'entity')
              ))
              ->setDefault('group_widget', 'entity');
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'custom_field_choice';
    }
}
