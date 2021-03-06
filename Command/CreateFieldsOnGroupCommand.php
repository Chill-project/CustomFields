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

namespace Chill\CustomFieldsBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Chill\CustomFieldsBundle\Entity\CustomFieldsGroup;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Exception\ParseException;
use Chill\CustomFieldsBundle\Entity\CustomField;

/**
 * Class for the command 'chill:custom_fields:populate_group' that
 * Create custom fields from a yml file
 *
 * @author Julien Fastré <julien.fastre@champs-libres.coop>
 * @author Marc Ducobu <marc.ducobu@champs-libres.coop>
 */
class CreateFieldsOnGroupCommand extends ContainerAwareCommand
{
    const ARG_PATH = 'path';
    const ARG_DELETE = 'delete';
    
    protected function configure()
    {
        $this->setName('chill:custom_fields:populate_group')
            ->setDescription('Create custom fields from a yml file')
            ->addArgument(self::ARG_PATH, InputOption::VALUE_REQUIRED,
                'Path to description file')
            ->addOption(self::ARG_DELETE, null, InputOption::VALUE_NONE,
                'If set, delete existing fields');
    }

    /**
     * Delete the existing custom fields for a given customFieldGroup
     *
     * @param CustomFieldsGroup $customFieldsGroup : The custom field group
     */
    protected function deleteFieldsForCFGroup($customFieldsGroup)
    {
        $em = $this->getContainer()
            ->get('doctrine.orm.default_entity_manager');

        foreach ($customFieldsGroup->getCustomFields() as $field) {
            $em->remove($field);
        }
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getHelperSet()->get('dialog');
        $em = $this->getContainer()
            ->get('doctrine.orm.default_entity_manager');
        
        $customFieldsGroups = $em
            ->getRepository('ChillCustomFieldsBundle:CustomFieldsGroup')
            ->findAll();
        
        if (count($customFieldsGroups) === 0) {
            $output->writeln('<error>There aren\'t any CustomFieldsGroup recorded'
                . ' Please create at least one.</error>');
        } 
        
        $table = $this->getHelperSet()->get('table');
        $table->setHeaders(array_merge(array('id', 'entity'), $this->getContainer()
                ->getParameter('chill_main.available_languages')))
            ->setRows($this->_prepareRows($customFieldsGroups));
        $table->render($output);
        
        $customFieldsGroup = $dialog->askAndValidate($output, 
            "Enter the customfieldGroup's id on which the custom fields "
            . "should be added :",
            function($answer) use ($customFieldsGroups) {
                foreach ($customFieldsGroups as $customFieldsGroup) {
                    if ($answer == $customFieldsGroup->getId()) {
                        return $customFieldsGroup;
                    }
                }
              
                throw new \RunTimeException('The id does not match an existing '
                    . 'CustomFieldsGroup');
            }
        );
          
        if ($input->getOption(self::ARG_DELETE)) {
            $this->deleteFieldsForCFGroup($customFieldsGroup);
        }
            
        $fieldsInput = $this->_parse($input->getArgument(self::ARG_PATH), 
            $output);
        
        $fields = $this->_addFields($customFieldsGroup, $fieldsInput, $output);
    }
    
    private function _prepareRows ($customFieldsGroups) 
    {
        $rows = array();
        $languages = $this->getContainer()
            ->getParameter('chill_main.available_languages');
        //gather entitites and create an array to access them easily
        $customizableEntities = array();
        foreach ($this->getContainer()
                ->getParameter('chill_custom_fields.customizables_entities') as $entry) {
            $customizableEntities[$entry['class']] = $entry['name'];
        }
        
        array_walk($customFieldsGroups, 
            function(CustomFieldsGroup $customFieldGroup, $key)
                use ($languages, &$rows, $customizableEntities) {
                    //set id and entity
                    $row = array(
                        $customFieldGroup->getId(),
                        $customizableEntities[$customFieldGroup->getEntity()]
                    );
                    
                    foreach ($languages as $lang) {
                        //todo replace with service to find lang when available
                        $row[] = (isset($customFieldGroup->getName()[$lang])) ?
                                $customFieldGroup->getName()[$lang] :
                            'Not available in this language';
                    }
                    $rows[] = $row;
                }
        );
              
        return $rows;
    }
    
    private function _parse($path, OutputInterface $output)
    {
        $parser = new Parser();
        
        if (!file_exists($path)) {
            throw new \RunTimeException("file does not exist");
        }
        
        try {
            $values = $parser->parse(file_get_contents($path));
        } catch (ParseException $ex) {
            throw new \RunTimeException("The yaml file is not valid", 0, $ex);
        }
        
        return $values;
    }
    
    private function _addFields(CustomFieldsGroup $group, $values, OutputInterface $output)
    {
        $cfProvider = $this->getContainer()->get('chill.custom_field.provider');
        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');

        $languages = $this->getContainer()
            ->getParameter('chill_main.available_languages');
        
        foreach($values['fields'] as $slug => $field) {
            //check the cf type exists
            $cfType = $cfProvider->getCustomFieldByType($field['type']);
            if ($cfType === NULL) {
                throw new \RunTimeException('the type '.$field['type'].' '
                    . 'does not exists');
            }
            
            $cf = new CustomField();
            $cf->setSlug($slug)
                ->setName($field['name'])
                ->setOptions(isset($field['options']) ? $field['options'] : array() )
                ->setOrdering($field['ordering'])
                ->setType($field['type'])
                ->setCustomFieldsGroup($group);
            
            //add to table
            $names = array();
            foreach ($languages as $lang) {
                //todo replace with service to find lang when available
                $names[] = (isset($cf->getName()[$lang])) ?
                    $cf->getName()[$lang] :
                        'Not available in this language';
            }
            
            if ($this->getContainer()->get('validator')->validate($cf)) {
                $em->persist($cf);
                $output->writeln("<info>Adding Custom Field of type "
                    .$cf->getType()."\t with slug ".$cf->getSlug().
                    "\t and names : ".implode($names, ', ')."</info>");
            } else {
                throw new \RunTimeException("Error in field ".$slug);
            }
        }
        
        $em->flush();
    }
}
