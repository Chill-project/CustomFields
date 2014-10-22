<?php

namespace CL\CustomFieldsBundle\Entity;

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
    
    private $options = array();
    
    /**
     *
     * @var int 
     */
    private $relation = 1;
    
    const ONE_TO_ONE = 1;
    const ONE_TO_MANY = 2;


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
}

