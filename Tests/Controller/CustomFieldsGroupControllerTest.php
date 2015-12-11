<?php

namespace Chill\CustomFieldsBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

class CustomFieldsGroupControllerTest extends WebTestCase
{
    
    public function testCompleteScenario()
    {
        self::bootKernel(array('environment' => 'test_customizable_entities_test_not_empty_config'));
        // Create a new client to browse the application
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'olala',
        ));

        //create the entity
        $this->createCustomFieldsGroup($client);

        // Edit the entity
        $this->editCustomFieldsGroup($client);
    }

    private function createCustomFieldsGroup(Client &$client)
    {
        // Create a new entry in the database
        $crawler = $client->request('GET', '/fr/admin/customfieldsgroup/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 
                "Unexpected HTTP status code for GET /customfieldsgroup/");

        $crawler = $client->click($crawler->selectLink('Créer un nouveau groupe')->link());
        
        // Fill in the form and submit it
        $form = $crawler->selectButton('Créer')->form(array(
            'custom_fields_group[name][fr]'  => 'Test',
            'custom_fields_group[entity]'    => 'Chill\PersonBundle\Entity\Person'
        ));

        $crawler = $client->submit($form);
        
        $crawler = $client->followRedirect();

        // Check data in the show view
        $this->assertGreaterThan(0, $crawler->filter('td:contains("Test")')->count(), 
                'Missing element td:contains("Test")');
    }
    
    private function editCustomFieldsGroup(Client $client)
    {
        $crawler = $client->request('GET', '/fr/admin/customfieldsgroup/');
        $links = $crawler->selectLink('modifier');
        $crawler = $client->click($links->last()->link());

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        $form = $crawler->selectButton('Update')->form(array(
            'custom_fields_group[name][fr]'  => 'Foo',
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check the element contains an attribute with value equals "Foo"
        $this->assertGreaterThan(0, $crawler->filter('[value="Foo"]')->count(), 
                'Missing element [value="Foo"]');
    }
}
