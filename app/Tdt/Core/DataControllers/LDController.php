<?php

namespace Tdt\Core\DataControllers;

use Tdt\Core\Datasets\Data;
use Tdt\Core\Pager;

/**
* LD Controller
* @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
* @license AGPLv3
* @author Jan Vansteenlandt <jan@okfn.be>
* @author Michiel Vancoillie <michiel@okfn.be>
*/
class LDController extends SPARQLController
{

    /**
     * We create a publication of the linked data within a graph that matches the name of the request uri.
     * The graph that will be published is the one that is stored in the graph model after the loading of triples
     * using the input package.
     */
    public function readData($source_definition, $rest_parameters = array())
    {

        list($limit, $offset) = Pager::calculateLimitAndOffset();

        $uri = \Request::root();

        // Fetch the necessary parameters
        $endpoint = $source_definition['endpoint'];
        $endpoint_user = $source_definition['endpoint_user'];
        $endpoint_password = $source_definition['endpoint_password'];

        // Construct the graph name
        $graph_name = $uri . '/' . $source_definition['datatank_identifier'];

        $subject_uri = $graph_name;

        // Add the REST parameters to the subject uri
        // If a REST parameter is present, only get triples with the subject
        // matching the full URI, if not present, use the base uri as a base, and allow
        // all matches that the identifier "substrings"
        if (!empty($rest_parameters)) {
            $subject_uri .= '/' . implode('/', $rest_parameters);
            $subject_uri .= '$';
        } else {
            $subject_uri .= '*';
        }

        // Retrieve the graph instance, if not found then abo`the process
        $graph = \Graph::whereRaw('graph_name like ?', array($graph_name))->first();

        // If no graph exists, abort the process
        if (empty($graph)) {
            \App::abort('404', 'No graph with the name ' . $graph_name . ' could be found.');
        }

        // Construct a query that will tell us how many triples there are in the graph
        $count_query = "SELECT count(?s) AS ?count WHERE
                        { GRAPH <$graph->graph_id> { ?s ?p ?o . FILTER ( regex(?s, \"$subject_uri\", \"i\") )}}";

        // Execute the count query for paging purposes
        $count_query = urlencode($count_query);
        $count_query = str_replace("+", "%20", $count_query);

        $count_uri = $endpoint . '?query=' . $count_query . '&format=' . urlencode("application/rdf+xml");
        $response = $this->executeUri($count_uri, $endpoint_user, $endpoint_password);

        // Parse the triple response and retrieve the form them containing our count result
        $parser = \ARC2::getRDFXMLParser();
        $parser->parse('', $response);

        $triples = $parser->triples;

        // Get the results#value, in order to get a count of all the results
        // This will be used for paging purposes
        $count = 0;
        foreach ($triples as $triple) {
            if (!empty($triple['p']) && preg_match('/.*sparql-results#value/', $triple['p'])) {
                $count = $triple['o'];
            }
        }

        // Calculate page link headers, previous, next and last based on the count from the previous query
        $paging = Pager::calculatePagingHeaders($limit, $offset, $count);

        // Construct the query to retrieve the triples from the graph
        $query = 'CONSTRUCT { ?s ?p ?o } ';
        $query .= "WHERE { GRAPH <$graph->graph_id> { ";
        $query .= '?s ?p ?o .';
        $query .= " FILTER ( regex(?s, \"$subject_uri\", \"i\"))";
        $query .= '}  } ORDER BY asc(?s)';

        // Apply paging parameters to the query
        if (!empty($offset)) {
            $query = $query . " OFFSET $offset ";
        }

        if (!empty($limit)) {
            $query = $query . " LIMIT $limit";
        }

        // Prepare the query with proper encoding for the request
        $query = urlencode($query);
        $query = str_replace("+", "%20", $query);

        $query = $endpoint . '?query=' . $query . '&format=' . urlencode("application/rdf+xml");

        $response = $this->executeUri($query, $endpoint_user, $endpoint_password);

        // Parse the triple response and retrieve the triples from them
        // EasyRdf sometimes hits the fan and goes awol when encoutering
        // certain characters, ARC2 (same reason why we used it in tdt/input) doesn't seem to have these problems

        $graph = new \EasyRdf_Graph();

        try {

            $parser = new \EasyRdf_Parser_RdfXml();

            $parser->parse($graph, $response, 'rdfxml', null);

        } catch (\EasyRdf_Parser_Exception $ex) {

            $result = \ARC2::getRDFXMLParser();
            $result->parse('', $response);

            // Parse the triple response and retrieve the triples from them
            $ser = \ARC2::getTurtleSerializer();

            $triples = $ser->getSerializedTriples($result->getTriples());

            $parser = new \EasyRdf_Parser_Turtle();

            $parser->parse($graph, $triples, 'turtle', null);
        }

        $is_semantic = true;

        // Return the result with the ARC2 graph as the data property
        $data_result = new Data();
        $data_result->paging = $paging;
        $data_result->is_semantic = true;
        $data_result->data = $graph;
        $data_result->semantic = array();

        return $data_result;
    }


    /**
     * Execute a query using cURL and return the result.
     * This function will abort upon error.
     */
    private function executeUri($uri, $user = '', $password = '')
    {

        // Check if curl is installed on this machine
        if (!function_exists('curl_init')) {
            \App::abort(500, "cURL is not installed as an executable on this server, this is necessary to execute the SPARQL query properly.");
        }

        // Initiate the curl statement
        $ch = curl_init();

        // If credentials are given, put the HTTP auth header in the cURL request
        if (!empty($user)) {
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            curl_setopt($ch, CURLOPT_USERPWD, $user . ":" . $password);
        }

        // Set the request uri
        curl_setopt($ch, CURLOPT_URL, $uri);

        // Request for a string result instead of having the result being outputted
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Execute the request
        $response = curl_exec($ch);

        if (!$response) {
            $curl_err = curl_error($ch);
            \App::abort(500, "Something went wrong while executhing query. The request we put together was: $uri.");
        }

        $response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // According to the SPARQL 1.1 spec, a SPARQL endpoint can only return 200,400,500 reponses
        if ($response_code == '400') {
            \App::abort(500, "The SPARQL endpoint returned a 400 error. The uri that was used to make the SPARQL request is $uri.");
        } else if ($response_code == '500') {
            \App::abort(500, "The SPARQL endpoint returned a 500 error. The uri that was used to make the SPARQL request is $uri.");
        }
        curl_close($ch);

        return $response;
    }
}
