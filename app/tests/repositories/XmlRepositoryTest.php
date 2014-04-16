<?php

use Symfony\Component\HttpFoundation\Request;

class XmlDefinitionRepositoryTest extends TestCase
{

    // This array holds the names of the files that can be used
    // to test the json definitions.
    private $test_data = array(
        'persons',
    );

    public function test_put()
    {

        // Publish each CSV file in the test json data folder.
        foreach ($this->test_data as $file) {

            // Set the definition parameters.
            $input = array(
                'description' => "A xml publication from the $file xml file.",
                'uri' => 'file://' . __DIR__ . "/../data/xml/$file.xml",
            );

            // Test the XmlDefinitionRepository
            $xml_repository = \App::make('Tdt\Core\Repositories\Interfaces\XmlDefinitionRepositoryInterface');

            $xml_definition = $xml_repository->store($input);

            // Check for properties
            foreach ($input as $property => $value) {
                $this->assertEquals($value, $xml_definition[$property]);
            }
        }
    }

    public function test_get()
    {

        $xml_repository = $xml_repository = \App::make('Tdt\Core\Repositories\Interfaces\XmlDefinitionRepositoryInterface');

        $all = $xml_repository->getAll();

        $this->assertEquals(count($this->test_data), count($all));

        foreach ($all as $xml_definition) {

            // Test the getById
            $xml_definition_clone = $xml_repository->getById($xml_definition['id']);

            $this->assertEquals($xml_definition, $xml_definition_clone);
        }

        // Test against the properties we've stored
        foreach ($this->test_data as $file) {

            $xml_definition = array_shift($all);

            $this->assertEquals($xml_definition['description'], "A xml publication from the $file xml file.");

            $this->assertEquals($xml_definition['uri'], 'file://' . __DIR__ . "/../data/xml/$file.xml");
        }
    }

    public function test_update()
    {

        $xml_repository = \App::make('Tdt\Core\Repositories\Interfaces\XmlDefinitionRepositoryInterface');

        $all = $xml_repository->getAll();

        foreach ($all as $xml_definition) {

            $updated_description = 'An updated description for object with description: ' . $xml_definition['description'];

            $updated_definition = $xml_repository->update($xml_definition['id'], array('description' => $updated_description));

            $this->assertEquals($updated_definition['description'], $updated_description);
        }
    }

    public function test_delete()
    {

        $xml_repository = \App::make('Tdt\Core\Repositories\Interfaces\XmlDefinitionRepositoryInterface');

        $all = $xml_repository->getAll();

        foreach ($all as $xml_definition) {

            $result = $xml_repository->delete($xml_definition['id']);

            $this->assertTrue($result);
        }
    }

    public function test_help_functions()
    {

        $xml_repository = \App::make('Tdt\Core\Repositories\Interfaces\XmlDefinitionRepositoryInterface');

        $this->assertTrue(is_array($xml_repository->getCreateParameters()));
        $this->assertTrue(is_array($xml_repository->getAllParameters()));
    }
}