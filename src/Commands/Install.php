<?php

namespace Tualo\Office\PWGen\Commands;

use Garden\Cli\Cli;
use Garden\Cli\Args;
use phpseclib3\Math\BigInteger\Engines\PHP;
use Tualo\Office\Basic\ICommandline;
use Tualo\Office\ExtJSCompiler\Helper;
use Tualo\Office\Basic\TualoApplication as App;
use Tualo\Office\Basic\PostCheck;
use Tualo\Office\Basic\CommandLineInstallSQL;


class InstallDDL extends CommandLineInstallSQL  implements ICommandline
{
    public static function getDir(): string
    {
        return dirname(__DIR__, 1);
    }
    public static $shortName  = 'pwgen-ddl';
    public static $files = [


        'install/ddl/addcommand'                    => 'setup addcommand',

        'install/ddl/votemanager_setup'             => 'setup votemanager_setup',


        'install/ddl/reportfiles_typen'             => 'setup reportfiles_typen',
        'install/ddl/reportfiles'                   => 'setup reportfiles',


        'install/ddl/wahltyp'                       => 'setup wahltyp',
        'install/ddl/wahltyp.data'                  => 'setup wahltyp.data',

        'install/ddl/abgabetyp'                     => 'setup abgabetyp',
        'install/ddl/abgabetyp.data'                => 'setup abgabetyp.data',

        'install/ddl/wahlscheinstatus'              => 'setup wahlscheinstatus',
        'install/ddl/wahlscheinstatus.data'         => 'setup wahlscheinstatus.data',

        'install/ddl/wahlscheinstatus_grund'        => 'setup wahlscheinstatus_grund',
        'install/ddl/wahlscheinstatus_grund.data'   => 'setup wahlscheinstatus_grund.data',

        'install/ddl/wahlgruppe'                    => 'setup wahlgruppe',
        'install/ddl/wahlbezirk'                    => 'setup wahlbezirk',
        'install/ddl/stimmzettel'                   => 'setup stimmzettel',
        'install/ddl/stimmzettelgruppen'            => 'setup stimmzettelgruppen',
        'install/ddl/kandidaten'                    => 'setup kandidaten',

        'install/ddl/kandidaten_bilder_typen'       => 'setup kandidaten_bilder_typen',
        'install/ddl/kandidaten_bilder'             => 'setup kandidaten_bilder',
        'install/ddl/counting.ddl'                  => 'setup counting.ddl',
        'install/ddl/counting.ddl.trigger'          => 'setup counting.ddl.trigger',

        'install/ddl/onlinestimmzettel'             => 'setup onlinestimmzettel',
        'install/ddl/onlinekandidaten'              => 'setup onlinekandidaten',

        'install/ddl/ruecklauffelder'               => 'setup ruecklauffelder',

        'install/ddl/wahlberechtigte'               => 'setup wahlberechtigte',
        'install/ddl/wahlzeichnungsberechtigter'    => 'setup wahlzeichnungsberechtigter',

        'install/ddl/wm_tanboegen'                  => 'setup wm_tanboegen',
        'install/ddl/wm_tannummer'                  => 'setup wm_tannummer',

        // 'install/ddl/wm_berichte'                   => 'setup wm_berichte',
        // 'install/ddl/wm_berichte.data'              => 'setup wm_berichte.data',

        'install/ddl/wahlschein'                    => 'setup wahlschein',


        'install/ddl/briefwahlstimmzettel'          => 'setup briefwahlstimmzettel',
        'install/ddl/ballotbox_decrypted_sum'       => 'setup ballotbox_decrypted_sum',

        'install/ddl/briefwahlkandidaten'           => 'setup briefwahlkandidaten',
        'install/proc/proc_briefwahlkandidaten'     => 'setup proc_briefwahlkandidaten',


        'install/view/view_kandidaten_stimmenanzahl' => 'setup view_kandidaten_stimmenanzahl',

        'install/ddl/wzbruecklauffelder'            => 'setup wzbruecklauffelder',
        'install/ddl/wzbruecklauffelder.data'       => 'setup wzbruecklauffelder.data',

        'install/ddl/view_ohne_wahlberechtigten'    => 'setup view_ohne_wahlberechtigten',

        'install/ddl/blocked_voters'                => 'setup blocked_voters',
        'install/ddl/view_voter_credentials'        => 'setup view_voter_credentials',

        'install/proc/voterCredential'              => 'setup voterCredential',

        'install/ddl/wahlberechtigte_anlage'              => 'setup wahlberechtigte_anlage',

        'install/ddl/wahlscheinstatus_online_erlaubt'              => 'setup wahlscheinstatus_online_erlaubt',
        'install/ddl/wahlscheinstatus_offline_erlaubt'              => 'setup wahlscheinstatus_offline_erlaubt',
        'install/ddl/abgabetyp_offline_erlaubt'              => 'setup abgabetyp_offline_erlaubt',

        // 'install/proc/check_wahlscheinstatus_online_erlaubt'              => 'setup check_wahlscheinstatus_online_erlaubt',     


        'install/proc/rebuild_view_voters_by_username_api'              => 'setup rebuild_view_voters_by_username_api',

        'install/view/view_kandidaten_sitze_vergeben'              => 'setup view_kandidaten_sitze_vergeben',
        'install/view/view_readtable_kandidaten'              => 'setup view_readtable_kandidaten',
        'install/view/view_readtable_kandidaten_bilder'              => 'setup view_readtable_kandidaten_bilder',



        'install/proc/getBallotpaper'              => 'setup getBallotpaper',

        'install/proc/proc_clone_vm_data'              => 'setup proc_clone_vm_data',

    ];
}
