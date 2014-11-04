<?php

namespace Chill\CustomFieldsBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Chill\CustomFieldsBundle\CustomFields\CustomFieldInterface;
use Chill\CustomFieldsBundle\Entity\CustomField;

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
     * @var \Chill\CustomFieldsBundle\Entity\CustomField
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
