<?php

namespace Tualo\Office\PWGen\Routes;

use Tualo\Office\Basic\TualoApplication;
use Tualo\Office\Basic\Route as R;
use Tualo\Office\Basic\IRoute;


class JS extends \Tualo\Office\Basic\RouteWrapper
{
    public static function register()
    {


        R::add('/pw-gen/Blowfish.js', function () {
            $matches = [];
            $matches['file'] = 'Blowfish.js';
            if (file_exists(dirname(__DIR__, 1) . '/lib/' . $matches['file'] . '')) {
                $path_parts = pathinfo(dirname(__DIR__, 1) . '/lib/' . $matches['file'] . '');
                if ($path_parts['extension'] == 'js')   TualoApplication::contenttype('application/javascript');
                if ($path_parts['extension'] == 'css')   TualoApplication::contenttype('text/css');
                TualoApplication::etagFile((dirname(__DIR__, 1) . '/lib/' . $matches['file'] . ''));
            } else {
                TualoApplication::body("// hm, something is wrong " . $matches['file']);
            }
        }, ['get'], false);
    }
}
