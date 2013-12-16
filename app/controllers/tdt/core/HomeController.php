<?php

namespace tdt\core;
use tdt\core\auth\Auth;

/**
 * HomeController
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Michiel Vancoillie <michiel@okfn.be>
 */
class HomeController extends \Controller {

    public static function handle($uri){

        Auth::requirePermissions('dataset.view');

        $definitions = \Definition::all();

        $view = \View::make('home')->with('title', 'The Datatank')
                                  ->with('definitions', $definitions);

        return \Response::make($view);
    }
}
