<?php

namespace CL\CustomFieldsBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\Common\Collections\ArrayCollection;
use CL\CustomFieldsBundle\Entity\Adress;


// ATTENTION QD NOUVEL OBJ cree sans appel a doctrine 
// on n'a pas la config de custom fields 
// ET DONC ON NE SAIT PAS QUELS VARIABLES SONT VIA __GET
// ET __SET SONT DECRITES PAR CUSTOM FIELDS

// IDEE : Travailler avec Lifecycle
// set et get pour JSON Field
//  - dans un tableau special
// postLoad (après que l'élément sort du EM) :
//   - récupération des customs fields (ok!)
//   - json -> obj dans des choses qui existent deja
// preFlush avant mise a jour de la db
//   - met a jour les donnees liees au json et le json
// perPersist idem mais pour le persist

/**
 * BlopEntity2
 */
class BlopEntity2
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var array
     */
    private $customFieldData;
    private $customFieldDataArray = array(); // customField apres json_decode
    private $customFieldDataUnfolded = array(); // mise des entity de customFieldDataArray
    
    private $customFieldConfigs = array();

    private $customFieldConfigsLoaded = false;

    // CHARGE DE LA DB LA CONFIG DES CUSTOM FIELDS
    public function loadCustomFieldConfig(LifecycleEventArgs $args)
    {
        $em = $args->getObjectManager();

        $customFields = $em
            ->getRepository('CLCustomFieldsBundle:CustomField')
            ->findAll();
   
        $customFieldsLablels = array_map(
            function($e) { return $e->getLabel(); },
            $customFields);

        $this->customFieldConfigs = array_combine($customFieldsLablels, $customFields);
        $this->customFieldConfigsLoaded = true;
    }

    // A PARTIR DU JSON CREE LES OBJETS (MIS DANS customFieldDataUnfolded)
    public function unfoldCustomFieldData(LifecycleEventArgs $args)
    {
        $em = $args->getObjectManager();

        $customFieldDataArray = json_decode($this->customFieldData,true);
        $customFieldDataUnfolded = array();

        foreach ($this->customFieldConfigs as $key => $cfConfig) {
            $type = $cfConfig->getType();
            if(strpos($type,'ManyToMany') === 0) {
                $fieldUnfolded = new ArrayCollection();

                if(array_key_exists($key, $customFieldDataArray)) {
                    $entityClass = substr($type, 11, -1);

                    foreach ($customFieldDataArray[$key] as $idEntity) {
                        $fieldUnfolded->add($em
                            ->getRepository('CLCustomFieldsBundle:' . $entityClass)
                            ->findOneById($idEntity));
                    }
                }

                $customFieldDataUnfolded[$key] = $fieldUnfolded;
            } else if(strpos($type,'ManyToOne') === 0) {
                $entityClass = 'Adress'; // substr($type,10,-1);
                if(array_key_exists($key, $customFieldDataArray)) {
                    $customFieldDataUnfolded[$key] = $em
                        ->getRepository('CLCustomFieldsBundle:' . $entityClass)
                        ->findOneById($customFieldDataArray[$key]);
                } else {
                    // TODO : doit tjs avoir un id
                    $em
                        ->getRepository('CLCustomFieldsBundle:' . $entityClass)
                        ->findOneById(1); 
                }
            }
            else if ($type === 'text') {
                if(array_key_exists($key, $customFieldDataArray)) {
                    $customFieldDataUnfolded[$key] = $customFieldDataArray[$key];
                } else {
                    $customFieldDataUnfolded[$key] = '';   
                }
            }
        }

        $this->customFieldDataUnfolded = $customFieldDataUnfolded;
    }

    // AVANT PERSIST LES ELEMENTS QUI N'ONT PAS D'ID DOIVENT EN AVOIR UN (OM->PERSIST(OBJ))
    // PUIS PASSAGE DES OBJETS (SE TROUVANT DANS customFieldDataUnfolded) VERS
    // LE JSON (SE TROUVANT DANS customFieldData)
    public function prePersist(LifecycleEventArgs $args)
    {
        $em = $args->getObjectManager();

        $this->loadCustomFieldConfig($args);

        foreach ($this->customFieldDataUnfolded as $key => $unfoldedData) {
            $type = $this->customFieldConfigs[$key]->getType();
            if(strpos($type,'ManyToMany') === 0) {
                foreach ($this->customFieldDataUnfolded[$key] as $entity) {
                    if(! $entity->getId()) {
                        $em->persist($entity);
                    }
                }
            } else if(strpos($type,'ManyToOne') === 0) {
                if(! $this->customFieldDataUnfolded[$key]->getId()) {                    
                    $em->persist($this->customFieldDataUnfolded[$key]);
                }
            }
        }

        $this->customFieldDataUnfoldedToCustomField();
    }

    // PUIS PASSAGE DES OBJETS (SE TROUVANT DANS customFieldDataUnfolded) VERS
    // LE JSON (SE TROUVANT DANS customFieldData)
    public function preFlush(PreFlushEventArgs $args)
    {
        $this->customFieldDataUnfoldedToCustomField();
    }

    // PUIS PASSAGE DES OBJETS (SE TROUVANT DANS customFieldDataUnfolded) VERS
    // LE JSON (SE TROUVANT DANS customFieldData)
    public function customFieldDataUnfoldedToCustomField()
    {
        // MISE A JOUR DE customFieldDataArray
        foreach ($this->customFieldConfigs as $key => $cfConfig) {
            $type = $cfConfig->getType();
            if(strpos($type,'ManyToMany') === 0) {
                $arrayMapRet = array();
                foreach ($this->customFieldDataUnfolded[$key] as $entity) {
                    $arrayMapRet[] = $entity->getId();
                }
                $this->customFieldDataArray[$key] = $arrayMapRet; // array_map(function($e) { $e->getId(); }, $this->customFieldDataUnfolded[$key]);
            } else if(strpos($type,'ManyToOne') === 0) {
                if(array_key_exists($key, $this->customFieldDataUnfolded)) {
                    $this->customFieldDataArray[$key] = $this->customFieldDataUnfolded[$key]->getId();
                } else {
                    // normalement $this->customFieldDataArray[$key] ne doit pas exister
                    if(array_key_exists($key, $this->customFieldDataArray)) {
                        throw new Exception("Error Processing Request", 1);
                    }
                    //retirer de $this->customFieldDataArray[$key]
                }
            } else if ($type === 'text') {
                $this->customFieldDataArray[$key] = $this->customFieldDataUnfolded[$key];
            }
        }

        // MISE A JOUR DE CustomFieldData
        $this->setCustomFieldData(json_encode($this->customFieldDataArray));
    }

    public function __set($fieldName, $value) {
        $setMethodName = 'set' . ucfirst($fieldName);

        if(method_exists($this, $setMethodName)) {
            return $this->{$setMethodName}($value);
        }

        if(array_key_exists($fieldName, $this->customFieldConfigs)) {
            $this->customFieldDataUnfolded[$fieldName] = $value;
        } else if (!$this->customFieldConfigsLoaded) { // nouvel object pas eu d'appel doctrine avant
            $this->customFieldDataUnfolded[$fieldName] = $value;
        } else {
            throw new Exception("Error Processing Request", 1);
        }
    }

    public function __get($fieldName) {
        $getMethodName = 'get' . ucfirst($fieldName);

        if(method_exists($this, $getMethodName)) {
            return $this->{$getMethodName}();
        }

        if(array_key_exists($fieldName, $this->customFieldDataUnfolded)) {
            return $this->customFieldDataUnfolded[$fieldName];
        } else if (!$this->customFieldConfigsLoaded) { // nouvel object pas eu d'appel doctrine avant
            return null;
        } else if(array_key_exists($fieldName, $this->customFieldConfigs)) { // pas init
            return null;
        } else {
            throw new Exception("Error Processing Request", 1);
        }
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
     * Set customField
     *
     * @param array $customField
     *
     * @return BlopEntity2
     */
    public function setCustomFieldData($customFieldData)
    {
        $this->customFieldData = $customFieldData;
        return $this;
    }

    /**
     * Get customField
     *
     * @return array
     */
    public function getCustomFieldData()
    {
        return $this->customFieldData;
    }
}

