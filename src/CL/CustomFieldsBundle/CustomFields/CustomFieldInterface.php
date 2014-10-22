<?php

namespace CL\CustomFieldsBundle\CustomFields;

use Symfony\Component\Form\FormBuilderInterface;
use CL\CustomFieldsBundle\Entity\CustomField;

/**
 *
 * @author Julien Fastré <julien.fastre@champs-libres.coop>
 */
interface CustomFieldInterface
{

    /**
     * 
     * @param \CL\CustomFieldsBundle\CustomField\FormBuilderInterface $builder
     * @param \CL\CustomFieldsBundle\CustomField\CustomField $customField
     * @return \Symfony\Component\Form\FormTypeInterface the form type
     */
    public function buildFormType(FormBuilderInterface $builder, CustomField $customField);

    /**
     * 
     * @param type $value
     * @param \CL\CustomFieldsBundle\CustomField\CustomField $customField
     */
    public function transformToEntity($value, CustomField $customField);

    /**
     * 
     * @param type $value
     * @param \CL\CustomFieldsBundle\CustomField\CustomField $customField
     */
    public function transformFromEntity($value, CustomField $customField);

    /**
     * 
     * @param type $value
     * @param \CL\CustomFieldsBundle\CustomField\CustomField $customField
     */
    public function render($value, CustomField $customField);
    
    public function getName();
}
