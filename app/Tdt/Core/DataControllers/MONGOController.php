<?php namespace Tdt\Core\Datacontrollers;

use Tdt\Core\Datasets\Data;
use Tdt\Core\Pager;
use MongoClient;

/**
 * Mongo controller
 *
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class MONGOController extends ADataController
{
    public function readData($source_definition, $rest_parameters = [])
    {
        list($limit, $offset) = Pager::calculateLimitAndOffset();

        $collection = $this->getCollection($source_definition);

        // Parse the parameters from the query string (prefixed by q.)
        $all_parameters = \Input::get();

        $query = [];

        foreach ($all_parameters as $key => $val) {
            if (substr($key, 0, 2) == 'q_') {
                $key = str_replace('q_', '', $key);
                $query[$key] = $val;
            }
        }

        $total_objects = $collection->count($query);

        $cursor = $collection->find($query)->skip($offset)->limit($limit);

        $results = [];

        foreach ($cursor as $result) {
            unset($result['_id']);

            $results[] = $result;
        }

        $paging = Pager::calculatePagingHeaders($limit, $offset, $total_objects);

        $data_result = new Data();
        $data_result->data = $results;
        $data_result->paging = $paging;
        $data_result->preferred_formats = $this->getPreferredFormats();

        return $data_result;
    }

    /**
     * Create and return a MongoCollection
     *
     * @param array $source_definition The configuration for the mongo resource
     *
     * @return \MongoCollection
     */
    private function getCollection($source_definition)
    {
        $prefix = '';
        $auth = [];

        if (!empty($source_definition['username'])) {
            $auth['username'] = $source_definition['username'];
            $auth['password'] = $source_definition['password'];
        }

        $conn_string = 'mongodb://' . $source_definition['host'] . ':' . $source_definition['port'] . '/' . $source_definition['database'];

        try {
            $client = new MongoClient($conn_string, $auth);
        } catch (\MongoConnectionException $ex) {
            \App::abort(500, 'Could not create a connection with the MongoDB, please check if the configuration is still ok.');
        }

        $mongoCollection = $client->selectCollection($source_definition['database'], $source_definition['mongo_collection']);

        return $mongoCollection;
    }
}
