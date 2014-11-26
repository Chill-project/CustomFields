<?php
namespace Chill\CustomFieldsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Return a choice widget with an "other" option
 * 
 * @author Julien Fastré <julien.fastre@champs-libres.coop>
 *
 */
class ChoiceWithOtherType extends AbstractType
{
    private $otherValueLabel = 'Other value';

    public function __construct($otherValueLabel = Null) {
        if($otherValueLabel) {
            $this->otherValueLabel = $otherValueLabel;
        }
    }

    /* (non-PHPdoc)
     * @see \Symfony\Component\Form\AbstractType::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options) 
    {
        //add an 'other' entry in choices array
        $options['choices']['_other'] = $this->otherValueLabel;
        //ChoiceWithOther must always be expanded
        $options['expanded'] = true;
        
        $builder
            ->add('_other', 'text', array('required' => false))
            ->add('_choices', 'choice', $options)
        ;    
    }

    /* (non-PHPdoc)
     * @see \Symfony\Component\Form\AbstractType::setDefaultOptions()
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) 
    {
        $resolver
            ->setRequired(array('choices'))
            ->setAllowedTypes(array('choices' => array('array')))
            ->setDefaults(array('multiple' => false))
        ;
    }

    public function getName()
    {
        return 'choice_with_other';
    }
}