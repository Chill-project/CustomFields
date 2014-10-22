<?php

namespace CL\CustomFieldsBundle\CustomFields;

use CL\CustomFieldsBundle\CustomFields\CustomFieldInterface;
use CL\CustomFieldsBundle\Entity\CustomField;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityManagerInterface;
use CL\CustomFieldsBundle\Form\AdressType;

/**
 * Description of CustomFieldAddress
 *
 * @author Julien Fastré <julien.fastre@champs-libres.coop>
 */
class CustomFieldAddress implements CustomFieldInterface
{
    
    public $om;
    
    public function __construct(EntityManagerInterface $om)
    {
        $this->om = $om;
    }
    
    public function buildFormType(FormBuilderInterface $builder, CustomField $customField)
    {
        switch ($customField->getRelation())
        {
            case CustomField::ONE_TO_ONE :
                $builder->build(
                        $builder->create($customField->getSlug(), 
                                new AddressType()
                                )
                        );
                break;
            case CustomField::ONE_TO_MANY :
                $builder->build(
                        $builder->create($customField->getSlug(),
                                new AddressType(),
                                array(
                                    'multiple' => true
                                ))
                        );
                break;
        }
    }

    public function getName()
    {
        return 'CF address';
    }

    public function render($value, CustomField $customField)
    {
        
    }

    public function transformFromEntity($value, CustomField $customField)
    {
        
    }

    public function transformToEntity($value, CustomField $customField)
    {
        
    }

}
