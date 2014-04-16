<?php

namespace Tdt\Core\Repositories;

use Tdt\Core\Repositories\Interfaces\LdDefinitionRepositoryInterface;

class LdDefinitionRepository extends BaseDefinitionRepository implements LdDefinitionRepositoryInterface
{

    public function __construct(\LdDefinition $model)
    {
        $this->model = $model;
    }

    protected $rules = array(
        'endpoint' => 'required',
        'description' => 'required',
    );

    public function getCreateParameters()
    {
        return array(
            'endpoint' => array(
                'required' => true,
                'name' => 'SPARQL endpoint',
                'description' => 'The uri of the Linked Data end-point (e.g. http://foobar:8890/sparql-auth)',
                'type' => 'string',
            ),
            'description' => array(
                'required' => true,
                'name' => 'Description',
                'description' => 'The descriptive or informational string that provides some context for you published dataset.',
                'type' => 'string',
            ),
            'endpoint_user' => array(
                'required' => false,
                'name' => 'SPARQL endpoint user',
                'description' => 'Username of the user that has sufficient rights to query the Linked Data endpoint.',
                'type' => 'string',
            ),
            'endpoint_password' => array(
                'required' => false,
                'name' => "SPARQL endpoint user's password",
                'description' => 'Password of the provided user to query a Linked Data endpoint.',
                'type' => 'string',
            ),
        );
    }
}
