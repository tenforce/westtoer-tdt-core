<?php

use Tdt\Core\Definitions\DefinitionController;
use Tdt\Core\Datasets\DatasetController;

use Symfony\Component\HttpFoundation\Request;

class XmlTest extends TestCase
{

    // This array holds the names of the files that can be used
    // to test the xml definitions.
    private $test_data = array(
                'persons',
            );

    public function test_put_api()
    {

        // Publish each xml file in the test xml data folder.
        foreach ($this->test_data as $file) {

            // Set the definition parameters.
            $data = array(
                'description' => "A xml publication from the $file xml file.",
                'uri' => __DIR__ . "/../data/xml/$file.xml",
                'type' => 'xml'
            );

            // Set the headers.
            $headers = array(
                'Content-Type' => 'application/tdt.definition+json'
            );

            $this->updateRequest('PUT', $headers, $data);

            // Put the definition controller to the test!
            $controller = \App::make('Tdt\Core\Definitions\DefinitionController');
            $response = $controller->handle("xml/$file");

            // Check if the creation of the definition succeeded.
            $this->assertEquals(200, $response->getStatusCode());
        }
    }

    public function test_get_api()
    {

        // Request the data for each of the test xml files.
        foreach ($this->test_data as $file) {

            $file = 'xml/'. $file .'.json';
            $this->updateRequest('GET');

            $controller = \App::make('Tdt\Core\Datasets\DatasetController');

            $response = $controller->handle($file);
            $this->assertEquals(200, $response->getStatusCode());
        }
    }

    public function test_update_api()
    {
        foreach($this->test_data as $file){

            $updated_description = 'An updated description for ' . $file;

            $identifier = 'xml/' . $file;

            // Set the fields that we're going to update
            $data = array(
                'description' => 'An updated description',
            );

            // Set the correct headers
            $headers = array('Content-Type' => 'application/tdt.definition+json');

            $this->updateRequest('PATCH', $headers, $data);

            // Test the patch function on the definition controller
            $controller = \App::make('Tdt\Core\Definitions\DefinitionController');

            $response = $controller->handle($identifier);
            $this->assertEquals(200, $response->getStatusCode());
        }
    }

    public function test_delete_api()
    {
        // Delete the published definition for each test xml file.
        foreach ($this->test_data as $file) {

            $this->updateRequest('DELETE');

            $controller = \App::make('Tdt\Core\Definitions\DefinitionController');

            $response = $controller->handle("xml/$file");
            $this->assertEquals(200, $response->getStatusCode());
        }

        // Check if everything is deleted properly.
        $definitions_count = Definition::all()->count();
        $xml_count = XmlDefinition::all()->count();

        $this->assertTrue($xml_count == 0);
        $this->assertTrue($definitions_count == 0);
    }
}
