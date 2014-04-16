<?php

/**
 * License model
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class License extends Eloquent
{

    protected $boolean_values = array('domain_software','domain_content', 'domain_data', 'is_generic', 'is_okd_compliant', 'is_osi_compliant');

    protected $fillable = array(
                            'domain_content',
                            'domain_data',
                            'domain_software',
                            'family',
                            'license_id',
                            'is_generic',
                            'is_okd_compliant',
                            'is_osi_compliant',
                            'maintainer',
                            'status',
                            'title',
                            'url'
                        );
}
