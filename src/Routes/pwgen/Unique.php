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

class Unique extends \Tualo\Office\Basic\RouteWrapper
{

    public static function register()
    {
        BasicRoute::add('/pw-gen/(?P<tablename>\w+)/new_unique', function ($matches) {
            $session = App::get('session');
            $db = $session->getDB();
            App::contenttype('application/json');
            set_time_limit(120);
            try {
                App::result('recordid', []);
                App::result('username', []);
                App::result('password', []);


                //                $con -> options(MYSQLI_OPT_CONNECT_TIMEOUT, 10);

                ini_set('memory_limit', '18G');
                set_time_limit(120);
                $c = $db->singleRow('select count(*) as c,database() d from `' . $matches['tablename'] . '`', []);
                if ($c['c'] == 0) $c['c'] = 1000;

                $counts = $c['c'];

                $c['c'] = 1000;
                $c['tablename'] = $matches['tablename'];



                $sum = 0;
                $db->direct('drop table if exists temp_random_list');
                while ($sum < $counts) {
                    $db->direct('call createPWGenRandom({tablename},12,"1234567890",{c},"pwgen_id",true)', $c);
                    $db->moreResults();
                    $sum += $c['c'];
                }
                set_time_limit(120);

                App::result('recordid', $db->direct('select temp_random_list.*,rand() r from temp_random_list  order by r', []));
                set_time_limit(120);

                $sum = 0;
                $db->direct('drop table if exists temp_random_list');
                while ($sum < $counts) {
                    $db->direct('call createPWGenRandom({tablename},7,"ABCDEFGHJKLMNPRSTUVXYZ123456789",{c},"pwgen_user",true)', $c);
                    $db->moreResults();
                    $sum += $c['c'];
                }
                set_time_limit(120);

                App::result('username', $db->direct('select temp_random_list.*,rand() r from temp_random_list order by r', []));
                set_time_limit(120);

                $sum = 0;
                $db->direct('drop table if exists temp_random_list');
                while ($sum < $counts) {
                    $db->direct('call createPWGenRandom({tablename},5,"ABCDEFGHJKLMNPRSTUVXYZ123456789",{c},"pw",false)', $c);
                    $db->moreResults();
                    $sum += $c['c'];
                }

                set_time_limit(120);
                App::result('password', $db->direct('select temp_random_list.*,rand() r from temp_random_list  order by r', []));
                App::result('success', true);
            } catch (Exception $e) {
                App::result('msg', $e->getMessage());
                App::result('last_sql', $db->last_sql);
            }
        }, ['get']);

        BasicRoute::add('/pw-gen/(?P<tablename>\w+)/unique', function ($matches) {
            $session = App::get('session');
            $db = $session->getDB();

            try {
                App::result('recordid', []);
                App::result('username', []);

                try {
                    App::result('recordid', $db->direct('select pwgen_id from `' . $matches['tablename'] . '` FOR SYSTEM_TIME ALL', [], 'pwgen_id'));
                    App::result('username', $db->direct('select pwgen_user from `' . $matches['tablename'] . '` FOR SYSTEM_TIME ALL', [], 'pwgen_user'));
                } catch (Exception $e) {
                }
                App::result('success', true);
            } catch (Exception $e) {
                App::result('msg', $e->getMessage());
            }
            App::contenttype('application/json');
        }, ['get']);
    }
}
