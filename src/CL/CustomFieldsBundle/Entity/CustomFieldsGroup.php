<?php

namespace CL\CustomFieldsBundle\Entity;

/**
 * CustomFieldGroup
 */
class CustomFieldsGroup
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var array
     */
    private $name;

    /**
     * @var string
     */
    private $entity;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $customFields;

        /**
     * Constructor
     */
    public function __construct()
    {
        $this->customFields = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add customField
     *
     * @param \CL\CustomFieldsBundle\Entity\CustomField $customField
     *
     * @return CustomFieldsGroup
     */
    public function addCustomField(\CL\CustomFieldsBundle\Entity\CustomField $customField)
    {
        $this->customFields[] = $customField;

        return $this;
    }

    /**
     * Remove customField
     *
     * @param \CL\CustomFieldsBundle\Entity\CustomField $customField
     */
    public function removeCustomField(\CL\CustomFieldsBundle\Entity\CustomField $customField)
    {
        $this->customFields->removeElement($customField);
    }
    
    /**
     * 
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCustomFields()
    {
       return $this->customFields;
    }

    
    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param array $name
     *
     * @return CustomFieldsGroup
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return array
     */
    public function getName($language = null)
    {
       //TODO set this in a service, PLUS twig function
       if ($language) {
          if (isset($this->name[$language])) {
             return $this->name[$language];
          } else {
             foreach ($this->name as $name) {
                if (!empty($name)) {
                   return $name;
                }
             }
          }
          
          return '';
          
       } else {
          return $this->name;
       }
    }

    /**
     * Set entity
     *
     * @param string $entity
     *
     * @return CustomFieldsGroup
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * Get entity
     *
     * @return string
     */
    public function getEntity()
    {
        return $this->entity;
    }

}
