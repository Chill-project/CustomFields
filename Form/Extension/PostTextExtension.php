<?php

/*
 * Copyright (C) 2015 Julien Fastré <julien.fastre@champs-libres.coop>
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

namespace Chill\CustomFieldsBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * This extension create the possibility to add some text
 * after the input. 
 * 
 * This can be used to print the units of the field, or some text.
 * 
 * This class must be extended by Extension class specifics to each input.
 *
 * @author Julien Fastré <julien.fastre@champs-libres.coop>
 */
abstract class PostTextExtension extends AbstractTypeExtension
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefined(array('post_text'));
    }
    
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (array_key_exists('post_text', $options)) {
            //set the post text variable to the view
            $view->vars['post_text'] = $options['post_text'];
        }
    }

}
