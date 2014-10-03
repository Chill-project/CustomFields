<?php

namespace CL\CustomFieldsBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Doctrine\Common\Persistence\ObjectManager;

class JsonCustomFieldToArrayTransformer implements DataTransformerInterface {
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;

        $customFields = $this->om
            ->getRepository('CLCustomFieldsBundle:CustomField')
            ->findAll();
   
        $customFieldsLablels = array_map(
            function($e) { return $e->getLabel(); },
            $customFields);

        $customFieldsByLabel = array_combine($customFieldsLablels, $customFields);

        /*
        echo "<br> - - <br>";

        var_dump($customFields);

        echo "<br> - - <br>";

        var_dump($customFieldsLablels);

        echo "<br> - - <br>";

        var_dump($customFieldsByLabel);
        */

        $this->customField = $customFieldsByLabel;
    }

    public function transform($customFieldsJSON)
    {
        echo $customFieldsJSON;
        $customFieldsArray = json_decode($customFieldsJSON,true);

        /*
        echo "<br> - - <br>";

        var_dump($customFieldsArray);

        echo "<br> - - <br>";
        */


        $customFieldsArrayRet = array();

        foreach ($customFieldsArray as $key => $value) {
            $traited = false;
            if(array_key_exists($key, $this->customField)) {
                /*
                echo "<br> - - - - <br>";   
                echo $value; 
                echo "<br> - - - - <br>";
                */

                if($this->customField[$key]->getType() === 'ManyToOne(Address)') {
                    $customFieldsArrayRet[$key] = $this->om
                            ->getRepository('CLCustomFieldsBundle:Adress')
                            ->findOneById($value);

                    $traited = true;
                }
            }

            if(! $traited) {
                $customFieldsArrayRet[$key] = $value;
            }
        }

        var_dump($customFieldsArray);

        return $customFieldsArrayRet;
    }

    public function reverseTransform($customFieldsArray)
    {
        /*
        echo "<br> - - - - <br>";

        var_dump($customFieldsArray);

        echo "<br> - - - - <br>";
        */

        $customFieldsArrayRet = array();

        foreach ($customFieldsArray as $key => $value) {
            $traited = false;
            if(array_key_exists($key, $this->customField)) {
                if($this->customField[$key]->getType() === 'ManyToOne(Address)') {
                    $customFieldsArrayRet[$key] = $value->getId();

                    /*
                    echo "<br> - - - - <br>";                    
                    echo $value->getId();
                    echo "<br> - - - - <br>";
                    */

                    $traited = true;
                }
            }

            if(! $traited) {
                $customFieldsArrayRet[$key] = $value;
            }
        }

        var_dump($customFieldsArrayRet);

        return json_encode($customFieldsArrayRet);
    }
}