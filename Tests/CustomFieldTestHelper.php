<?php

/*
 * Chill is a software for social workers
 * Copyright (C) 2014 Champs-Libres Coopérative <info@champs-libres.coop>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Chill\CustomFieldsBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Chill\CustomFieldsBundle\Entity\CustomField;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Give useful method to prepare tests regarding custom fields
 *
 * @author Julien Fastré <julien.fastre@champs-libres.coop>
 */
class CustomFieldTestHelper
{
    /**
     * Prepare a crawler containing the rendering of a customField
     * 
     * @internal This method will mock a customFieldGroup containing $field, and
     * rendering the customfield, using Type\CustomFieldType, to a simple form row
     * 
     * @param CustomField $field
     * @param KernelTestCase $testCase
     * @param KernelInterface $kernel
     * @param type $locale
     * @return Crawler
     */
    public static function getCrawlerForField(CustomField $field, KernelTestCase $testCase, KernelInterface $kernel, $locale = 'en')
    {
        //check a kernel is accessible
        
        
        $customFieldsGroup = $testCase->getMock('Chill\CustomFieldsBundle\Entity\CustomFieldsGroup');
        $customFieldsGroup->expects($testCase->once())
              ->method('getCustomFields')
              ->will($testCase->returnValue(array($field)))
              ;
        
        $request = $testCase->getMock('Symfony\Component\HttpFoundation\Request');
        $request->expects($testCase->any())
              ->method('getLocale')
              ->will($testCase->returnValue($locale))
              ;
        $kernel->getContainer()->get('request_stack')->push($request);
        
        $builder = $kernel->getContainer()->get('form.factory')->createBuilder();
        $form = $builder->add('tested', 'custom_field', 
              array('group' => $customFieldsGroup))
              ->getForm()
              ;
        
        $kernel->getContainer()->get('twig.loader')
                ->addPath(__DIR__.'/Fixtures/App/app/Resources/views/', 
                      $namespace = 'test');
        $content = $kernel
                ->getContainer()->get('templating')
                ->render('@test/CustomField/simple_form_render.html.twig', array(
                   'form' => $form->createView(),
                   'inputKeys' => array('tested')
                ));

        $crawler = new Crawler();
        $crawler->addHtmlContent($content);
        
        return $crawler;
    }
}
