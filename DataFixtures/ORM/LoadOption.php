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

namespace Chill\CustomFieldsBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Chill\CustomFieldsBundle\Entity\CustomFieldLongChoice\Option;

/**
 * Load some Options
 * 
 *
 * @author Julien Fastré <julien.fastre@champs-libres.coop>
 */
class LoadOption extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     *
     * @var \Faker\Generator
     */
    public $fakerFr;
    
    /**
     *
     * @var \Faker\Generator
     */
    public $fakerEn;
    
    /**
     *
     * @var \Faker\Generator
     */
    public $fakerNl;
    
    public function __construct()
    {
        $this->fakerFr = \Faker\Factory::create('fr_FR');
        $this->fakerEn = \Faker\Factory::create('en_EN');
        $this->fakerNl = \Faker\Factory::create('nl_NL');
    }
    
    public function getOrder()
    {
        return 1000;
    }

    public function load(\Doctrine\Common\Persistence\ObjectManager $manager)
    {
        echo "Loading Options \n";
        // load companies
        $this->loadingCompanies($manager);
        $this->loadingWords($manager);
        
        
        $manager->flush();
        
    }
    
    private function loadingWords(\Doctrine\Common\Persistence\ObjectManager $manager)
    {
        echo "Loading some words...\n";
        
        $parents = array(
            array(
                'fr' => 'Categorie 1',
                'nl' => 'Categorie 1',
                'en' => 'Category 1'
            ), 
            array(
                'fr' => 'Categorie 2',
                'nl' => 'Categorie 2',
                'en' => 'Category 2'
            )
        );
        
        foreach ($parents as $text) {
            $parent = (new Option())
                ->setText($text)
                ->setKey('word')
                ;
            $manager->persist($parent);
            //Load children
            $expected_nb_children = rand(10, 50);
            for ($i=0; $i < $expected_nb_children; $i++) {
                $manager->persist($this->createChildOption($parent, array(
                    'fr' => $this->fakerFr->word,
                    'nl' => $this->fakerNl->word,
                    'en' => $this->fakerEn->word
                )));
            }
        }
    }
    
    private function loadingCompanies(\Doctrine\Common\Persistence\ObjectManager $manager)
    {     
        echo "Loading companies \n";
        $companiesParents = array(
            array(
                'fr' => 'Grandes Entreprises',
                'nl' => 'Grotes Bedrijven',
                'en' => 'Big Companies'
            ),
            array(
                'fr' => 'Moyennes Entreprises',
                'nl' => 'Middelbare Bedrijven',
                'en' => 'Middle Companies'
            ),
            array(
                'fr' => 'Petites Entreprises',
                'nl' => 'Kleine Bedrijven',
                'en' => 'Little Companies'
            )
        );

        
        foreach ($companiesParents as $text) {
            $parent = (new Option())
                ->setText($text)
                ->setKey('company')
                ;
            $manager->persist($parent);
            //Load children
            $expected_nb_children = rand(10, 50);
            for ($i=0; $i < $expected_nb_children; $i++) {
                $companyName = $this->fakerFr->company;
                $manager->persist($this->createChildOption($parent, array(
                    'fr' => $companyName,
                    'nl' => $companyName,
                    'en' => $companyName
                )));
            }
        }
    }
    
    private $counter = 0;
    
    /**
     * 
     * @param Option $parent
     * @param array $text
     * @return Option
     */
    private function createChildOption(Option $parent, array $text)
    {
        $this->counter ++;
        
        return (new Option())
                ->setText($text)
                ->setParent($parent)
                ->setActive(true)
                ->setInternalKey($parent->getKey().'-'.$this->counter);
                ; 
    }

}
