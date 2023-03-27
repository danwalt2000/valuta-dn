<?php

use Illuminate\Support\Facades\Facade;
use Illuminate\Http\Request;

function getHost(){
    $current_domain = 'valuta-dn.loc';
    $current_full_host = 'valuta-dn.loc';
    
    if( !empty( $_SERVER['SERVER_NAME'] ) ){
        $current_domain = $_SERVER['SERVER_NAME']; 
        $current_full_host = $_SERVER['HTTP_HOST'];
    }
    
    $domain = str_contains($current_domain, "valuta-dn") ? 'valuta-dn' : 'kursivalut';
    $locale = $domain == 'valuta-dn' ? 'donetsk' : 'moscow';
    if( $current_domain != $current_full_host){
        $locale = explode('.', $current_full_host)[0];
    }
    return [
        'domain' => $domain,
        'locale' => $locale,
    ];
}

return [
    'domain' => getHost()['domain'],
    'table'  => getHost()['locale'],

    'locales'        => [
        'donetsk'       => [
            'title'          => 'Донецк',
            'name'           => 'donetsk',
            'domain'         => 'valuta-dn',
            'h1_keyword'     => ' в Донецке и ДНР',
            'currencies'     => ['dollar', 'euro', 'hrn', 'cashless'],
            'publics'        => [
                "obmenvalut_donetsk"    => ["id" => "-87785879",  "time" => "everyFiveMinutes", "domain" => "vk"],    // 5
                "obmen_valut_donetsk"   => ["id" => "-92215147",  "time" => "everyFiveMinutes", "domain" => "vk"],    // 5
                "obmenvalyut_dpr"       => ["id" => "-153734109", "time" => "everyThirtyMinutes", "domain" => "vk"],  // 30
                "club156050748"         => ["id" => "-156050748", "time" => "everyThirtyMinutes", "domain" => "vk"],  // 30
                "kursvalut_donetsk"     => ["id" => "-63859238",  "time" => "everyThirtyMinutes", "domain" => "vk"],  // 30
                "obmen_valut_dnr"       => ["id" => "-193547744", "time" => "hourly", "domain" => "vk"],              // 60
                "donetsk_obmen_valyuta" => ["id" => "-174075254", "time" => "hourly", "domain" => "vk"],              // 60
                "obmenvalut_dnr"        => ["id" => "-172375183", "time" => "hourly", "domain" => "vk"],              // 60
                "valutoobmen"           => ["id" => "-75586957",  "time" => "hourly", "domain" => "vk"],              // 60
    
                "1154050282" => ["id" => "obmenkadn",             "time" => "everyFiveMinutes", "domain" => "tg"],    // 5
                "1161871204" => ["id" => "obmenkadonetck",        "time" => "everyTenMinutes", "domain" => "tg"],     // 10
                "1345575332" => ["id" => "obmen_valut_donetsk_1", "time" => "everyTenMinutes", "domain" => "tg"],     // 10
                "1265653325" => ["id" => "obmenvalutdon",         "time" => "everyFifteenMinutes", "domain" => "tg"], // 15
                "1295018924" => ["id" => "obmen77market",         "time" => "everyFifteenMinutes", "domain" => "tg"], // 15
                "1204646240" => ["id" => "valut_don",             "time" => "everyThirtyMinutes", "domain" => "tg"],  // 30
            ]
        ],
        'lugansk'       => [
            'title'          => 'Луганск',
            'name'           => 'lugansk',
            'model'          => 'Lugansk',
            'domain'         => 'valuta-dn',
            'h1_keyword'     => ' в Луганске и ЛНР',
            'currencies'     => ['dollar', 'euro', 'hrn', 'cashless'],
            'publics'        => [
                "1304227894" => ["id" => "obmenka_lugansk",       "time" => "everyFiveMinutes", "domain" => "tg"],    // 5
                "1629996803" => ["id" => "obmen_lugansk_obmen",   "time" => "everyFiveMinutes", "domain" => "tg"]     // 10
            ]
        ],
        'mariupol'       => [
            'title'          => 'Мариуполь',
            'name'           => 'mariupol',
            'model'          => 'Mariupol',
            'domain'         => 'valuta-dn',
            'h1_keyword'     => ' в Мариуполе и ДНР',
            'currencies'     => ['dollar', 'euro', 'hrn', 'cashless'],
            'publics'        => [
                "obmenvalut_mariupol"    => ["id" => "-212955319",  "time" => "everyFiveMinutes", "domain" => "vk"],    // 30
    
                "1784051014" => ["id" => "obmenmariupolya",             "time" => "everyFiveMinutes", "domain" => "tg"]    // 30
            ]
        ]
    ]
];
