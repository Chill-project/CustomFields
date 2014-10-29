<?php

namespace CL\CustomFieldsBundle\CustomFields;

use CL\CustomFieldsBundle\CustomFields\CustomFieldInterface;
use CL\CustomFieldsBundle\Entity\CustomField;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityManagerInterface;
use CL\CustomFieldsBundle\Form\DataTransformer\CustomFieldDataTransformer;
use CL\CustomFieldsBundle\Form\AdressType;

/**
 * Description of CustomFieldAddress
 *
 * @author Julien Fastré <julien.fastre@champs-libres.coop>
 */
class CustomFieldAddress implements CustomFieldInterface
{

    /**
     *
     * @var EntityManagerInterface
     */
    public $om;

    public function __construct(EntityManagerInterface $om)
    {
        $this->om = $om;
    }

    public function buildForm(FormBuilderInterface $builder, CustomField $customField)
    {
        $builder->add(
              $builder->create('address', 'entity', array(
                'class' => 'CLCustomFieldsBundle:Adress',
                 'multiple' => true,
                 'expanded' => true
                )
            )->addModelTransformer(new CustomFieldDataTransformer(
                            $this,
                            $customField)
                  )
        );
    }

    public function getName()
    {
        return 'CF address';
    }

    public function render($value, CustomField $customField)
    {
        
    }

    public function buildOptionsForm(FormBuilderInterface $builder)
    {
        return null;
    }

    public function deserialize($serialized, CustomField $customField)
    {
//        if ($serialized === NULL) {
//            return null;
//        }
//        
//        return $this->om->getRepository('CLCustomFieldsBundle:Adress')
//              ->find($serialized);
        
        return $this->om->getRepository('CLCustomFieldsBundle:Adress')
              ->findBy(array('id' => $serialized));
    }

    /**
     * 
     * @param \CL\CustomFieldsBundle\Entity\Adress $value
     * @param CustomField $customField
     * @return type
     */
    public function serialize($value, CustomField $customField)
    {
        $arrayId = array();
        
        foreach($value as $address) {
            $arrayId[] = $address->getId();
        }
        
        return $arrayId;
    }

}
