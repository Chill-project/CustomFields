<?php

namespace Chill\CustomFieldsBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Chill\CustomFieldsBundle\Entity\CustomFieldsGroup;

class CustomFieldsGroupToIdTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * Transforms an custom_field_group to a string (id)
     *
     * @param  CustomFieldsGroup|null $customFieldsGroup
     * @return string
     */
    public function transform($customFieldsGroup)
    {
        if (null === $customFieldsGroup) {
            return "";
        }
        
        if (!$customFieldsGroup instanceof CustomFieldsGroup) {
            throw new TransformationFailedException(sprintf('Transformation failed: '
                  . 'the expected type of the transforme function is an '
                  . 'object of type Chill\CustomFieldsBundle\Entity\CustomFieldsGroup, '
                  . '%s given (value : %s)', gettype($customFieldsGroup), 
                  $customFieldsGroup));
        }

        return $customFieldsGroup->getId();
    }

    /**
     * Transforms a string (id) to an object (CustomFieldsGroup).
     *
     * @param  string $id
     * @return CustomFieldsGroup|null
     * @throws TransformationFailedException if object (report) is not found.
     */
    public function reverseTransform($id)
    {
        if (!$id) {
            return null;
        }
        
        if ($id instanceof CustomFieldsGroup) {
            throw new TransformationFailedException(sprintf(
                  'The transformation failed: the expected argument on '
                  . 'reverseTransform is an object of type int,'
                  . 'Chill\CustomFieldsBundle\Entity\CustomFieldsGroup, '
                  . 'given', gettype($id)));
        }

        $customFieldsGroup = $this->om
            ->getRepository('ChillCustomFieldsBundle:customFieldsGroup')->find($id)
        ;

        if (null === $customFieldsGroup) {
            throw new TransformationFailedException(sprintf(
                'Le group avec le numéro "%s" ne peut pas être trouvé!',
                $id
            ));
        }

        return $customFieldsGroup;
    }
}