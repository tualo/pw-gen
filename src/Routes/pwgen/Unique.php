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

class Unique implements IRoute
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
                $c = $db->singleRow('select count(*) as c,database() d from `' . $matches['tablename'] . '`', []);
                if ($c['c'] == 0) $c['c'] = 1000;

                $counts = $c['c'];

                $c['c'] = 1000;



                $sum = 0;
                $db->direct('drop table if exists temp_random_list');
                while ($sum < $counts) {
                    $db->direct('call createRandomList(8,"1234567890",{c},"pwgen_id",true)', $c);
                    $db->moreResults();
                    $sum += $c['c'];
                }

                App::result('wahlschein', $db->direct('select temp_random_list.*,rand() r from temp_random_list  order by r', []));
                set_time_limit(120);

                $sum = 0;
                $db->direct('drop table if exists temp_random_list');
                while ($sum < $counts) {
                    $db->direct('call createRandomList(8,"ABCDEFGHJKLMNPRSTUVXYZabcdefghijkmpstuvxyz123456789",{c},"pwgen_user",true)', $c);
                    $db->moreResults();
                    $sum += $c['c'];
                }

                App::result('username', $db->direct('select temp_random_list.*,rand() r from temp_random_list order by r', []));
                set_time_limit(120);

                $sum = 0;
                $db->direct('drop table if exists temp_random_list');
                while ($sum < $counts) {
                    $db->direct('call createRandomList(8,"ABCDEFGHJKLMNPRSTUVXYZabcdefghijkmpstuvxyz123456789",{c},"pw",true)', $c);
                    $db->moreResults();
                    $sum += $c['c'];
                }

                set_time_limit(120);
                App::result('password', $db->direct('select temp_random_list.*,rand() r from temp_random_list  order by r', []));
                App::result('success', true);
            } catch (Exception $e) {
                App::result('msg', $e->getMessage());
            }
        }, ['post', 'get']);

        BasicRoute::add('/pw-gen/(?P<tablename>\w+)/unique', function () {
            $session = App::get('session');
            $db = $session->getDB();

            try {
                App::result('wahlschein', []);
                App::result('username', []);

                try {
                    App::result('wahlschein', $db->direct('select wahlscheinnummer from wahlschein FOR SYSTEM_TIME ALL', [], 'wahlscheinnummer'));
                    App::result('username', $db->direct('select username from wahlschein FOR SYSTEM_TIME ALL', [], 'username'));
                } catch (Exception $e) {
                }
                App::result('success', true);
            } catch (Exception $e) {
                App::result('msg', $e->getMessage());
            }
            App::contenttype('application/json');
        }, ['post', 'get']);
    }
}
