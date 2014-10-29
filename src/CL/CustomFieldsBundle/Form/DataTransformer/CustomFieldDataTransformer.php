<?php

namespace CL\CustomFieldsBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use CL\CustomFieldsBundle\CustomFields\CustomFieldInterface;
use CL\CustomFieldsBundle\Entity\CustomField;

/**
 * 
 *
 * @author Julien Fastré <julien.fastre@champs-libres.coop>
 */
class CustomFieldDataTransformer implements DataTransformerInterface
{
    private $customFieldDefinition;
    
    /**
     *
     * @var \CL\CustomFieldsBundle\Entity\CustomField
     */
    private $customField;
    
    public function __construct(CustomFieldInterface $customFieldDefinition,
            CustomField $customField)
    {
        $this->customFieldDefinition = $customFieldDefinition;
        $this->customField = $customField;
    }
    
    public function reverseTransform($value)
    {
        return $this->customFieldDefinition->serialize($value, 
                $this->customField);
    }

    public function transform($value)
    {
        return $this->customFieldDefinition->deserialize($value, 
                $this->customField);
    }

}
