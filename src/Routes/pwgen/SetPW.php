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
        BasicRoute::add('/pwgen/set', function () {
            try {
                $postdata = file_get_contents("php://input");
                $session = App::get('session');
                $db = $session->getDB();
                if (isset($postdata)) {
                    $postdata = json_decode($postdata, true);
                }
                foreach ($postdata as $row) {
                    $sql = '
                    update wahlschein set 
                        pwhash={pwhash},
                        username={username},
                        wahlscheinnummer={wahlscheinnummer},
                        wahlscheinstatus={wahlscheinstatus} 
                    where 
                        id = {id}
                        and stimmzettel={stimmzettel}
                        and wahlscheinstatus in ("16|0","17|0")';
                    $db->direct($sql, $row);
                }
                App::result('success', true);
            } catch (Exception $e) {
                App::result('msg', $e->getMessage());
            }
            App::contenttype('application/json');
        }, ['post', 'get']);

        BasicRoute::add('/pwgen/setpw', function () {
            $session = App::get('session');
            $db = $session->getDB();
            try {


                $USE_TMG = $db->singleValue('select daten from setup where id = "USE_TMG" and rolle="_default_" ', [], 'daten');
                if ($USE_TMG == '1') {
                    throw new \Exception("TMG ist nicht implementiert!");
                    /*
                    $old = $db->singleRow('select wahlscheinnummer from wahlschein where id={id}', $_REQUEST, '');
                    $tmg_result = ['success' => false];
                    $hash = password_hash($_REQUEST['password'], PASSWORD_BCRYPT);
                    if ($old['wahlscheinnummer'] != '') {
                        include_once __DIR__ . '/classes/tmg.php';
                        $tmg_v01 = new tmg_v01($db);
                        $tmg_result = $tmg_v01->changeUserAccess(

                            $old['wahlscheinnummer'],
                            $_REQUEST['wahlscheinnummer'],
                            $_REQUEST['username'],
                            $hash

                        );
                    } else {
                        $tmg_result['success'] = true;
                    }

                    App::result('tmg_result', $tmg_result);

                    if ($tmg_result['success']) {

                        $usql = 'update wahlschein set pwhash={pwhash}, username={username}, wahlscheinnummer={wahlscheinnummer},wahlscheinstatus="1|0" where id={id}';
                        $h = [

                            'username' => $_REQUEST['username'],
                            'wahlscheinnummer' => $_REQUEST['wahlscheinnummer'],
                            'pwhash' => $hash,
                            'id' => $_REQUEST['id']
                        ];
                        $db->direct($usql, $h);
                        App::result('last', $db->last_sql);
                    } else {
                        throw new \Exception("TMG Fehler " . $tmg_result['pure']);
                    }
                    */
                } else {


                    $prefix = $_REQUEST['prefix'] . '_';
                    $sql = 'update wahlschein set pwhash={pwhash},username={username},wahlscheinnummer={wahlscheinnummer},wahlscheinstatus="1|0" where 
                        wahlberechtigte in ( select ridx from wahlberechtigte where identnummer={identnummer}  ) 
                        and stimmzettel={stimmzettel}
                        and wahlscheinstatus in ("16|0","17|0")';
                    $h = [

                        'username' => $_REQUEST['username'],
                        'wahlscheinnummer' => $_REQUEST['wahlscheinnummer'],
                        'stimmzettel' => $_REQUEST['stimmzettel'],
                        'pwhash' => password_hash($_REQUEST['password'], PASSWORD_BCRYPT),
                        'identnummer' => $_REQUEST['identnummer']
                    ];
                    $db->direct($sql, $h);
                }
                App::result('success', true);
            } catch (Exception $e) {
                App::result('msg', $e->getMessage());
            }
            App::contenttype('application/json');
        }, ['post', 'get']);
    }
}
