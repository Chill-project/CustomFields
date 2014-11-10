<?php
namespace Chill\CustomFieldsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;


/**
 *
 * @author Julien Fastré <julien.fastre@champs-libres.coop>
 *        
 */
class ChoicesType extends AbstractType
{    
    public function getName()
    {
        return 'cf_choices';
    }
    
    public function getParent()
    {
        return 'collection';
    }

     
}