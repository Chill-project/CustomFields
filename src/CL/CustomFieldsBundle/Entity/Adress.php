<?php

namespace CL\CustomFieldsBundle\Entity;

/**
 * Adress
 */
class Adress
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $data;


    public function __toString()
    {
        return $this->data . '(id:' .$this->id .')';
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
     * Set data
     *
     * @param string $data
     *
     * @return Adress
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data
     *
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }
}
