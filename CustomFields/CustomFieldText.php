<?php



namespace Chill\CustomFieldsBundle\CustomFields;

use Chill\CustomFieldsBundle\CustomFields\CustomFieldInterface;
use Chill\CustomFieldsBundle\Entity\CustomField;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * 
 *
 * @author Julien Fastré <julien.fastre@champs-libres.coop>
 */
class CustomFieldText implements CustomFieldInterface
{
    
    const MAX_LENGTH = 'maxLength';
    
    /**
     * Create a form according to the maxLength option
     * 
     * if maxLength < 256 THEN the form type is 'text'
     * if not, THEN the form type is textarea
     * 
     * @param FormBuilderInterface $builder
     * @param CustomField $customField
     */
    public function buildForm(FormBuilderInterface $builder, CustomField $customField)
    {
        $type = ($customField->getOptions()[self::MAX_LENGTH] < 256) ? 'text' 
              : 'textarea';
        
        $builder->add($customField->getSlug(), $type, array(
            'label' => $customField->getLabel()
        ));
    }

    public function render($value, CustomField $customField)
    {
        
    }

    public function serialize($value, CustomField $customField)
    {
        return $value;
    }

    public function deserialize($serialized, CustomField $customField)
    {
        return $serialized;
    }

    public function getName()
    {
        return 'text field';
    }

    public function buildOptionsForm(FormBuilderInterface $builder)
    {
       return $builder
             ->add(self::MAX_LENGTH, 'integer', array('empty_data' => 256))
          ;
    }
}
