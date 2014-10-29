<?php

namespace Chill\CustomFieldsBundle\Entity;

/**
 * CustomField
 */
class CustomField
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $label;
    
    private $slug;

    /**
     * @var string
     */
    private $type;

    /**
     * @var boolean
     */
    private $active;
    
    /**
     *
     * @var array
     */
    private $options = array();
    
    /**
     * @var array
     */
    private $name;

    /**
     * @var float
     */
    private $order;
    
    /**
     *
     * @var int 
     */
    private $relation = 1;
    
    const ONE_TO_ONE = 1;
    const ONE_TO_MANY = 2;

    /**
     * @var CustomFieldsGroup
     */
    private $customFieldGroup;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
    
    function getSlug()
    {
        return $this->slug;
    }

        /**
     * Set label
     *
     * @param string $label
     *
     * @return CustomField
     */
    public function setLabel($label)
    {
        $this->label = $label;
        
        if ($this->slug === NULL) {
            $this->slug = preg_replace('/[^A-Za-z0-9-]+/', '-', $label);
        }

        return $this;
    }

    function getOptions()
    {
        return $this->options;
    } 
    
    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return CustomField
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
    
    function getRelation()
    {
        return $this->relation;
    }

    function setRelation($relation)
    {
        $this->relation = $relation;
        
        return $this;
    }

    
    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return CustomField
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Get customFieldGroup
     *
     * @return CustomFieldsGroup
     */
    public function getCustomFieldsGroup()
    {
        return $this->customFieldGroup;
    }

    /**
     * Set customFieldGroup
     *
     * @param \Chill\CustomFieldsBundle\Entity\CustomFieldsGroup $customFieldGroup
     *
     * @return CustomField
     */
    public function setCustomFieldsGroup(\Chill\CustomFieldsBundle\Entity\CustomFieldsGroup $customFieldGroup = null)
    {
        $this->customFieldGroup = $customFieldGroup;

        return $this;
    }

    /**
     * Set name
     *
     * @param array $name
     *
     * @return CustomField
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set order
     *
     * @param float $order
     *
     * @return CustomField
     */
    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get order
     *
     * @return float
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set options
     *
     * @param array $options
     *
     * @return CustomField
     */
    public function setOptions(array $options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Set customFieldGroup
     *
     * @param \Chill\CustomFieldsBundle\Entity\CustomFieldsGroup $customFieldGroup
     *
     * @return CustomField
     */
    public function setCustomFieldGroup(\Chill\CustomFieldsBundle\Entity\CustomFieldsGroup $customFieldGroup = null)
    {
        $this->customFieldGroup = $customFieldGroup;

        return $this;
    }

    /**
     * Get customFieldGroup
     *
     * @return \Chill\CustomFieldsBundle\Entity\CustomFieldsGroup
     */
    public function getCustomFieldGroup()
    {
        return $this->customFieldGroup;
    }
}
