<?php

namespace Tualo\Office\PWGen\Middlewares;

use Tualo\Office\Basic\TualoApplication;
use Tualo\Office\Basic\IMiddleware;

class Middleware implements IMiddleware
{
    public static function register()
    {
        TualoApplication::use('pw-gen-blowfish', function () {
            try {

                TualoApplication::javascript('pw-gen-blowfish', './pw-gen/Blowfish.js', [], -500);
            } catch (\Exception $e) {
                TualoApplication::set('maintanceMode', 'on');
                TualoApplication::addError($e->getMessage());
            }
        }, -100); // should be one of the last
    }
}
