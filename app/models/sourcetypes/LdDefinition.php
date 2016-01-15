<?php

/**
 * Linked Data definition model
 *
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class LdDefinition extends SourceType
{

    protected $table = 'lddefinitions';

    protected $fillable = array('endpoint', 'endpoint_user', 'endpoint_password', 'description');

    /**
     * Relationship with the Definition model.
     */
    public function definition()
    {
        return $this->morphOne('Definition', 'source');
    }
}
