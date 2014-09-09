<?php

namespace CL\CustomFieldsBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;


class JsonCustomFieldToArrayTransformer implements DataTransformerInterface {
   public function transform($jsonCustomField)
   {
      return json_decode($jsonCustomField,true);
   }

   public function reverseTransform($array)
   {
      return json_encode($array);
   }
}
