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

class SetPW implements IRoute
{

    public static function register()
    {
        BasicRoute::add('/pw-gen/(?P<tablename>\w+)/set', function ($matches) {
            try {
                $postdata = file_get_contents("php://input");
                $session = App::get('session');
                $db = $session->getDB();
                if (isset($postdata)) {
                    $postdata = json_decode($postdata, true);
                }
                foreach ($postdata as $row) {
                    $sql = '
                    update `' . $matches['tablename'] . '` set 
                        pwgen_hash={pwgen_hash},
                        pwgen_id={pwgen_id},
                        pwgen_user={pwgen_user} 
                    where 
                        id = {id}
                    ';
                    $db->direct($sql, $row);
                }
                App::result('success', true);
            } catch (Exception $e) {
                App::result('msg', $e->getMessage());
            }
            App::contenttype('application/json');
        }, ['post', 'get']);
    }
}
