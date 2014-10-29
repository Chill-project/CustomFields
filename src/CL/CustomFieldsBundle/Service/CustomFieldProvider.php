<?php


namespace CL\CustomFieldsBundle\Service;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Receive all service tagged with 'chill.custom_field'
 *
 * @author Julien Fastré <julien.fastre@champs-libres.coop>
 */
class CustomFieldProvider implements ContainerAwareInterface
{
    private $servicesByType = array();
    
    /**
     *
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;
    
    public function addCustomField($serviceName, $type)
    {
        $this->servicesByType[$type] = $serviceName;
    }
    
    /**
     * 
     * @param string $type
     * @return CustomFieldInterface 
     */
    public function getCustomFieldByType($type)
    {
        if (isset($this->servicesByType[$type])) {
            return $this->servicesByType[$type];
        } else {
            throw new \LogicException('the custom field with type '.$type.' '
                    . 'is not found');
        }
    }

    public function setContainer(ContainerInterface $container = null)
    {
        if ($container === null) {
            throw new \LogicException('container should not be null');
        }
        
        $this->container = $container;
    }
    
    public function getAllFields()
    {
        return $this->servicesByType;
    }

}
