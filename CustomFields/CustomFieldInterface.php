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
     * Return a form type to edit the custom field. This form is shown to the 
     * user. 
     *
     * @param \Chill\CustomFieldsBundle\CustomField\FormBuilderInterface $builder
     * @param \Chill\CustomFieldsBundle\CustomField\CustomField $customField
     * @return \Symfony\Component\Form\FormTypeInterface the form type
     */
    public function buildForm(FormBuilderInterface $builder, CustomField $customField);

    /**
     * Transform the value into a format that can be stored in DB
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
     * Return a repsentation of the value of the CustomField.
     * 
     * @param mixed $value the raw value, **not deserialized** (= as stored in the db)
     * @param \Chill\CustomFieldsBundle\CustomField\CustomField $customField
     * @return string an html representation of the value
     */
    public function render($value, CustomField $customField, $documentType = 'html');
    
    public function getName();
    
    /**
     * Return a formType which allow to edit option for the custom type.
     * This FormType is shown in admin
     * 
     * @param \Chill\CustomFieldsBundle\CustomField\FormBuilderInterface $builder
     * @return \Symfony\Component\Form\FormTypeInterface|null the form type
     */
    public function buildOptionsForm(FormBuilderInterface $builder);
    
    /**
     * Return if the value can be considered as empty
     * 
     * @param mixed $value the value passed throug the deserialize function
     * @param CustomField $customField
     */
    public function isEmptyValue($value, CustomField $customField);
}
