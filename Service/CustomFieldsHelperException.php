<?php

namespace Chill\CustomFieldsBundle\Service;

class CustomFieldsHelperException extends \Exception
{
    public static function customFieldsGroupNotFound($entity) 
    {
        return new CustomFieldsRenderingException("The customFieldsGroups associated with $entity are not found");
    }
    
    public static function slugIsMissing()
    {
        return new CustomFieldsRenderingException("The slug is missing");
    }
}