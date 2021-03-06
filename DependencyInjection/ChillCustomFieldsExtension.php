<?php

namespace Chill\CustomFieldsBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class ChillCustomFieldsExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        
        //add at least a blank array at 'customizable_entities' options
        //$customizable_entities = (isset($config['customizables_entities']) 
        //        && $config['customizables_entities'] !== FALSE) 
        //        ? $config['customizables_entities'] : array();
        
        $container->setParameter('chill_custom_fields.customizables_entities', 
                $config['customizables_entities']);
        $container->setParameter('chill_custom_fields.show_empty_values', 
                $config['show_empty_values_in_views']);
    }
    
     /* (non-PHPdoc)
      * @see \Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface::prepend()
      */
    public function prepend(ContainerBuilder $container) 
    {
        // add form layout to twig resources
        $twigConfig['form_themes'][] = 'ChillCustomFieldsBundle:Form:fields.html.twig';
        $container->prependExtensionConfig('twig', $twigConfig);
        
        //add routes for custom bundle
         $container->prependExtensionConfig('chill_main', array(
           'routing' => array(
              'resources' => array(
                 '@ChillCustomFieldsBundle/Resources/config/routing.yml'
              )
           )
        ));
    }
}
