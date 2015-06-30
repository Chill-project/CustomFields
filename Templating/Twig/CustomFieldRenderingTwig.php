<?php

/*
 * Chill is a software for social workers
 *
 * Copyright (C) 2014-2015, Champs Libres Cooperative SCRLFS, 
 * <http://www.champs-libres.coop>, <info@champs-libres.coop>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Chill\CustomFieldsBundle\Templating\Twig;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Container;
use Chill\CustomFieldsBundle\Entity\CustomField;

/**
 * Add the following Twig Extension :
 * * chill_custom_field_widget : to render the value of the custom field,
 * * chill_custom_field_label : to render the label of the custom field,
 *
 * @author Julien Fastré <julien.fastre@champs-libres.coop>
 */
class CustomFieldRenderingTwig extends \Twig_Extension implements ContainerAwareInterface
{

    /** @var Container $container The container */
    private $container;
    
    /** @var array $defaultParams The default parameters */
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
     * Twig Extension that is used to render the label of a custom field.
     * 
     * @param CustomField|object|string $customFieldOrClass Either a customField OR a customizable_entity OR the FQDN of the entity
     * @param string $slug The slug ONLY necessary if the first argument is NOT a CustomField instance
     * @param array $params The parameters for rendering. Currently, 'label_layout' allow to choose a different label. Default is 'ChillCustomFieldsBundle:CustomField:render_label.html.twig'
     * @return string HTML representation of the custom field label.
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
     * Twig extension that is used to render the value of a custom field.
     * 
     * The presentation of the value  is influenced by the document type.
     * 
     * @param array $fields The array raw, as stored in the db
     * @param CustomField|object|string $customFieldOrClass Either a customField OR a customizable_entity OR the FQDN of the entity
     * @param string $documentType The type of the document (csv, html)
     * @param string $slug The slug of the custom field ONLY necessary if the first argument is NOT a CustomField instance
     * @return string HTML representation of the custom field value, as described in the CustomFieldInterface. Is HTML safe
     */
    public function renderWidget(array $fields, $customFieldOrClass, $documentType='html', $slug = null)
    {
        return $this->container->get('chill.custom_field.helper')
            ->renderCustomField($fields, $customFieldOrClass, $documentType, $slug);
    }
}