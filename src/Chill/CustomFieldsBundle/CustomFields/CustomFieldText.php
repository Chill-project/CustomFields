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
    public function buildForm(FormBuilderInterface $builder, CustomField $customField)
    {
        $builder->add($customField->getSlug(), 'text', array(
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
      return null;
   }
}
