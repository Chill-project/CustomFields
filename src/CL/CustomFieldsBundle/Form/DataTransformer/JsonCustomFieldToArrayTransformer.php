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
        echo "<br> - 1 - <br>";

        var_dump($customFields);

        echo "<br> - 2 - <br>";

        var_dump($customFieldsLablels);

        echo "<br> - 3 - <br>";
    
        var_dump($customFieldsByLabel);
        */

        $this->customField = $customFieldsByLabel;
    }

    public function transform($customFieldsJSON)
    {
        echo $customFieldsJSON;

        if($customFieldsJSON === null) { // lors de la creation
            $customFieldsArray = array();
        } else {
            $customFieldsArray = json_decode($customFieldsJSON,true);
        }

        /*

        echo "<br> - 4 - <br>";

        var_dump($customFieldsArray);

        echo "<br> - 5 - <br>";
        ¨*/

        $customFieldsArrayRet = array();

        foreach ($customFieldsArray as $key => $value) {
            $traited = false;
            if(array_key_exists($key, $this->customField)) {
                $type = $this->customField[$key]->getType();
                if(strpos($type,'ManyToOne') === 0) {
                    if(strpos($type,'ManyToOnePersist') ===0)  {
                        $entityClass = substr($type, 17, -1);
                    } else {
                        $entityClass = substr($type, 10, -1);
                    }
                
                    $customFieldsArrayRet[$key] = $this->om
                        ->getRepository('CLCustomFieldsBundle:' . $entityClass)
                        ->findOneById($value);
                    $traited = true;
                }
            }

            if(! $traited) {
                $customFieldsArrayRet[$key] = $value;
            }
        }

        //var_dump($customFieldsArray);

        return $customFieldsArrayRet;
    }

    public function reverseTransform($customFieldsArray)
    {
        /*
        echo "<br> - - 7 - <br>";

        var_dump(array_keys($customFieldsArray));

        echo "<br> - - 8 - <br>";

        var_dump(array_keys($this->customField));

        echo "<br> - - 9 - <br>";
        */

        $customFieldsArrayRet = array();

        foreach ($customFieldsArray as $key => $value) {
            $traited = false;
            if(array_key_exists($key, $this->customField)) {
                $type = $this->customField[$key]->getType();
                if(strpos($type,'ManyToOne') === 0) {
                    // pour le manytoone() faire
                    // un update du form en js ? : http://symfony.com/fr/doc/current/cookbook/form/form_collections.html
                    //
                    //$entityClass = substr($type, 10, -1);
                    //echo $entityClasss;
                    if(strpos($type, 'ManyToOnePersist') === 0) {
                        // PEUT ETRE A FAIRE SI SEULEMENT $value->getId() ne renvoie rien... 
                        //
                        //
                        $this->om->persist($value); // pas bon ici
                        // LE PERSIST NE SERT QUE LA PREMIERE FOIS
                        // plutot le mettre dans une var temporaire de adress
                        // et faire le persist qd fait sur l'obj parent
                        // regarder : http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/events.html
                        // ou : http://symfony.com/doc/current/cookbook/doctrine/event_listeners_subscribers.html
                        // dans yml : 
                        //  lifecycleCallbacks:
                        //  prePersist: [ doStuffOnPrePersist, doOtherStuffOnPrePersist ]
                        $this->om->flush(); // sinon l'id pose pbm
                    }

                    $customFieldsArrayRet[$key] = $value->getId();
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