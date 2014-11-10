<?php
namespace Chill\CustomFieldsBundle\Templating\Twig;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Chill\CustomFieldsBundle\Service\CustomFieldsHelper;
use Symfony\Component\DependencyInjection\Container;
use Chill\CustomFieldsBundle\Entity\CustomField;

/**
 *
 * Add three functions to Twig:
 * * chill_custom_field_widget : render the value of the custom field,
 * * chill_custom_field_label : render the label of the custom field,
 *
 * @author Julien Fastré <julien.fastre@champs-libres.coop>
 *        
 */
class CustomFieldRenderingTwig extends \Twig_Extension implements ContainerAwareInterface
{

    /**
     *
     * @var Container
     */
    private $container;
    
    private $defaultParams = array(
        'label_layout' => 'ChillCustomFieldsBundle:CustomField:render_label.html.twig'
    );
    
    /*
     * (non-PHPdoc)
     * @see \Symfony\Component\DependencyInjection\ContainerAwareInterface::setContainer()
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
    
    /*
     * (non-PHPdoc)
     * @see Twig_Extension::getFunctions()
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('chill_custom_field_widget', array(
                $this,
                'renderWidget'
            ), array(
                'is_safe' => array(
                    'html'
                )
            )),
            new \Twig_SimpleFunction('chill_custom_field_label', array(
                $this,
                'renderLabel'
            ), array(
                'is_safe' => array(
                    'html'
                )
            ))
        ];
    }
    
    
     /* (non-PHPdoc)
      * @see Twig_ExtensionInterface::getName()
      */
     public function getName() 
     {
         return 'chill_custom_fields_rendering';
     }


    /**
     * 
     * @param CustomField|object|string $customFieldOrClass either a customField OR a customizable_entity OR the FQDN of the entity
     * @param string $slug only necessary if the first argument is NOT a CustomField instance
     * @param array $params the parameters for rendering. Currently, 'label_layout' allow to choose a different label. Default is 'ChillCustomFieldsBundle:CustomField:render_label.html.twig'
     * @return string
     */
    public function renderLabel($customFieldOrClass, $slug = null, array $params = array())
    {
        $resolvedParams = array_merge($this->defaultParams, $params);
        
        $customField = ($customFieldOrClass instanceof CustomField) 
            ? $customFieldOrClass : $this->container->get('chill.custom_field.provider')
                ->getCustomField($customFieldOrClass, $slug);
        
        return $this->container->get('templating')
            ->render($resolvedParams['label_layout'], array('customField' => $customField));
    }
    
    /**
     * 
     * @param array $fields the array raw, as stored in the db
     * @param CustomField|object|string $customFieldOrClass either a customField OR a customizable_entity OR the FQDN of the entity
     * @param string $slug only necessary if the first argument is NOT a CustomField instance
     * @return string HTML representation of the custom field, as described in the CustomFieldInterface. Is HTML safe
     */
    public function renderWidget(array $fields, $customFieldOrClass, $slug = null)
    {
        return $this->container->get('chill.custom_field.helper')
            ->renderCustomField($fields, $customFieldOrClass, $slug);
    }
    
    
}