<?php



namespace CL\CustomFieldsBundle\CustomFields;

use CL\CustomFieldsBundle\CustomFields\CustomFieldInterface;
use CL\CustomFieldsBundle\Entity\CustomField;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * 
 *
 * @author Julien Fastré <julien.fastre@champs-libres.coop>
 */
class CustomFieldText implements CustomFieldInterface
{
    public function buildFormType(FormBuilderInterface $builder, CustomField $customField)
    {
        $builder->add($customField->getSlug(), 'text', array(
            'label' => $customField->getLabel()
        ));
    }

    public function render($value, CustomField $customField)
    {
        
    }

    public function transformFromEntity($value, CustomField $customField)
    {
        return $value;
    }

    public function transformToEntity($value, CustomField $customField)
    {
        return $value;
    }

    public function getName()
    {
        return 'text field';
    }

}
