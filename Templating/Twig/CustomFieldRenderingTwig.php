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
     * @param CustomField|object|string $customFieldOrClass
     * @param string $slug
     * @param array $params the parameters for rendering
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
    
    public function renderWidget(array $fields, $customFieldOrClass, $slug = null)
    {
        return $this->container->get('chill.custom_field.helper')
            ->renderCustomField($fields, $customFieldOrClass, $slug);
    }
    
    
}