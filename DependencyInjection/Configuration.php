<?php

namespace Chill\CustomFieldsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from 
 * your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('chill_custom_fields');
        
        $classInfo = "The class which may receive custom fields";
        $nameInfo = "The name which will appears in the user interface. May be translatable";
        $optionsInfo = "Options available for custom fields groups referencing this class";
        $optionsFormType = "The name of the form to append";
        $optionsFormOptionsInfos = "the arguments to pass the form";
        $customizableEntitiesInfo = "A list of customizable entities";

        $rootNode
            ->children()
                ->arrayNode('customizables_entities')
                    ->info($customizableEntitiesInfo)
                    ->defaultValue(array())
                    ->prototype('array')
                    ->children()
                        ->scalarNode('class')->isRequired()->info($classInfo)
                        ->end()
                        ->scalarNode('name') ->isRequired()->info($nameInfo)
                        ->end()
                        ->arrayNode('options')
                            ->info($optionsInfo)
                            ->defaultValue(array())
                            ->useAttributeAsKey('key')
                            ->prototype('array')
                                ->children()
                                    ->scalarNode('form_type')
                                        ->isRequired()
                                        ->info($optionsFormType)
                                    ->end()
                                    ->variableNode('form_options')
                                        ->info($optionsFormOptionsInfos)
                                        ->defaultValue(array())
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
            ->booleanNode('show_empty_values_in_views')
                ->info('Show the empty value for custom fields in the views, timeline, ...')
                ->defaultValue(true)
            ->end()
        ->end()
        ;

        return $treeBuilder;
    }
}
