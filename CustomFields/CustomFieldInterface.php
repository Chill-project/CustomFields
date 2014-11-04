<?php

namespace Chill\CustomFieldsBundle\CustomFields;

use Symfony\Component\Form\FormBuilderInterface;
use Chill\CustomFieldsBundle\Entity\CustomField;

/**
 *
 * @author Julien Fastré <julien.fastre@champs-libres.coop>
 */
interface CustomFieldInterface
{

    /**
     * 
     * @param \Chill\CustomFieldsBundle\CustomField\FormBuilderInterface $builder
     * @param \Chill\CustomFieldsBundle\CustomField\CustomField $customField
     * @return \Symfony\Component\Form\FormTypeInterface the form type
     */
    public function buildForm(FormBuilderInterface $builder, CustomField $customField);

    /**
     * transform the value into a format that can be stored in DB
     * 
     * @param mixed $value
     * @param \Chill\CustomFieldsBundle\CustomField\CustomField $customField
     */
    public function serialize($value, CustomField $customField);

    /**
     * Transform the representation of the value, stored in db, into the
     * value which may be used in the process.
     * 
     * @param mixed $value
     * @param \Chill\CustomFieldsBundle\CustomField\CustomField $customField
     */
    public function deserialize($serialized, CustomField $customField);

    /**
     * 
     * @param type $value
     * @param \Chill\CustomFieldsBundle\CustomField\CustomField $customField
     */
    public function render($value, CustomField $customField);
    
    public function getName();
    
    /**
     * return a formType which allow to edit option for the custom type.
     * This FormType is shown in admin
     * 
     * @param \Chill\CustomFieldsBundle\CustomField\FormBuilderInterface $builder
     * @return \Symfony\Component\Form\FormTypeInterface|null the form type
     */
    public function buildOptionsForm(FormBuilderInterface $builder);
}
