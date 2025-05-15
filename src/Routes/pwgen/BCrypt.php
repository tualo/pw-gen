<?php

namespace Tualo\Office\PWGen\Routes\pwgen;

use Exception;
use Tualo\Office\Basic\TualoApplication as App;
use Tualo\Office\Basic\Route as BasicRoute;
use Tualo\Office\Basic\IRoute;
use Tualo\Office\TualoPGP\TualoApplicationPGP;
use phpseclib\Net\SFTP;
use \PhpOffice\PhpSpreadsheet\Spreadsheet;
use \PhpOffice\PhpSpreadsheet\IOFactory;

use Ramsey\Uuid\Uuid;

class BCrypt implements IRoute
{

    public static function register()
    {
        BasicRoute::add('/pw-gen/bcrypt', function () {


            $db = App::get('session')->getDB();
            try {
                $input = json_decode(file_get_contents('php://input'), true);
                if (is_null($input)) throw new Exception("Error Processing Request", 1);
                if (!isset($input['passwords'])) throw new Exception("Error Processing Request", 1);

                foreach ($input['passwords'] as &$item) {
                    set_time_limit(30);
                    $options = [
                        'cost' => App::configuration('pw-gen', 'bcrypt_cost', 8)
                    ];
                    $item['pwhash'] =   password_hash($item['password'], PASSWORD_BCRYPT, $options);
                    // unset($item['password']);
                }
                App::result('data', $input['passwords']);
                App::result('success', true);
            } catch (Exception $e) {
                App::result('msg', $e->getMessage());
            }
            App::contenttype('application/json');
        }, ['post'], true);
    }
}
