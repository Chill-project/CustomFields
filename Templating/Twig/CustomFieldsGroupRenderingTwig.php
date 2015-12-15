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
 * * chill_custom_fields_group_widget : to render the value of a custom field
 * *   group
 *
 * @author Julien Fastré <julien.fastre@champs-libres.coop>
 * @author Marc Ducobu <marc.ducobu@champs-libres.coop>
 */
class CustomFieldsGroupRenderingTwig extends \Twig_Extension implements ContainerAwareInterface
{
    
    /** @var Container $container The container */
    private $container;
    
    /** @var array $defaultParams The default parameters */
    private $defaultParams = array(
        'layout' => 'ChillCustomFieldsBundle:CustomFieldsGroup:render.html.twig',
        'show_empty' => True
    );
    
    /**
     * 
     * @param boolean $showEmptyValues whether the empty values must be rendered
     */
    public function __construct($showEmptyValues)
    {
        $this->defaultParams['show_empty'] = $showEmptyValues;
    }
    
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
            new \Twig_SimpleFunction('chill_custom_fields_group_widget', array(
                $this,
                'renderWidget'
            ), array(
                'is_safe' => array(
                    'html'
                )
            )),
        ];
    }
    
    /* (non-PHPdoc)
     * @see Twig_ExtensionInterface::getName()
     */
    public function getName() 
    {
        return 'chill_custom_fields_group_rendering';
    }
    
    /**
     * Twig extension that is used to render the value of a custom field group.
     * 
     * The presentation of the value  is influenced by the document type.
     * 
     * @param array $fields The array raw, as stored in the db
     * @param CustomFieldsGroud $customFielsGroup The custom field group
     * @param string $documentType The type of the document (csv, html)
     * @param array $params The parameters for rendering : 
     *  - layout : allow to choose a different layout by default :
     *             ChillCustomFieldsBundle:CustomFieldsGroup:render.html.twig
     *  - show_empty : force show empty field
     * @return string HTML representation of the custom field group value, as described in 
     * the CustomFieldInterface. Is HTML safe
     */
    public function renderWidget(array $fields, $customFielsGroup, $documentType='html', array $params = array())
    {
        $resolvedParams = array_merge($this->defaultParams, $params);
        
        return $this->container->get('templating')
            ->render($resolvedParams['layout'], array(
            	'cFGroup' => $customFielsGroup,
            	'cFData' => $fields,
                'show_empty' => $resolvedParams['show_empty']));
    }
}