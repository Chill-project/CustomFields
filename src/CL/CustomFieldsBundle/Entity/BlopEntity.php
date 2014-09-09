<?php

namespace CL\CustomFieldsBundle\Entity;

/**
 * BlopEntity
 */
class BlopEntity
{
   /**
   * @var integer
   */
   private $id;

   /**
   * @var string
   */
   private $field1;

   /**
   * @var string
   */
   private $field2;

   /**
   * @var array
   */
   private $customField;


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
   * Set field1
   *
   * @param string $field1
   *
   * @return BlopEntity
   */
   public function setField1($field1)
   {
      $this->field1 = $field1;

      return $this;
   }

   /**
   * Get field1
   *
   * @return string
   */
   public function getField1()
   {
      return $this->field1;
   }

   /**
   * Set field2
   *
   * @param string $field2
   *
   * @return BlopEntity
   */
   public function setField2($field2)
   {
      $this->field2 = $field2;

      return $this;
   }

   /**
   * Get field2
   *
   * @return string
   */
   public function getField2()
   {
      return $this->field2;
   }

   /**
   * Set customField
   *
   * @param array $customField
   *
   * @return BlopEntity
   */
   public function setCustomField($customField)
   {
      $this->customField = $customField;

      return $this;
   }

   /**
   * Get customField
   *
   * @return array
   */
   public function getCustomField()
   {
      return $this->customField;
   }

   public function cfGet($key)
   {
      echo "<br> -1- <br>";
      echo gettype($this->customField);
      echo "<br> -2- <br>";
      echo $this->customField;
      echo "<br> -3- <br>";
      var_dump(json_decode($this->customField));
      echo "<br> -4- <br>";
      echo json_last_error_msg();

      $customFieldArray = json_decode($this->customField, true);

      if(array_key_exists($key, $customFieldArray)) {
         return $customFieldArray[$key];
      } else {
         return null;
      }
   }

   public function cfSet($key, $value)
   {
      echo "-";
      $customFieldArray = json_decode($this->customField, true);
      $customFieldArray[$key] = $value;
      $this->setCustomField(json_encode($customFieldArray));
      var_dump($customFieldArray);
   }
}

