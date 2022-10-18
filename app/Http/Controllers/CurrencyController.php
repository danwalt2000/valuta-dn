<?php
 
namespace App\Http\Controllers;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Psr\Http\Message\RequestInterface;
use Log;
use App\Http\Controllers\GetAdsController;
use App\Http\Controllers\ScheduleController;
use App\Models\Ads;
 
class CurrencyController extends Controller
{
    /**
     * Show the profile for a given user.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    
    public $ads = [];
    public $db_ads = [];
    public $get_posts;
    public $to_view = [];
    public $posts;
    public $parsed_url = [];
    
    public $publics = [
        "obmenvalut_donetsk"    => ["id" => "-87785879",  "time" => "everyFiveMinutes"],    // 5
        "obmen_valut_donetsk"   => ["id" => "-92215147",  "time" => "everyFiveMinutes"],    // 5
        "obmenvalyut_dpr"       => ["id" => "-153734109", "time" => "everyThirtyMinutes"],  // 30
        "club156050748"         => ["id" => "-156050748", "time" => "everyThirtyMinutes"],  // 20
        "obmen_valut_dnr"       => ["id" => "-193547744", "time" => "hourly"],              // 60
        "donetsk_obmen_valyuta" => ["id" => "-174075254", "time" => "hourly"]               //60
    ];
    public $currencies = [
       "dollar" => "Доллар $",
       "euro" => "Евро €",
       "hrn" => "Гривна ₴",
       "cashless" => "Безнал руб. ₽"
    ];
    public $date_sort = [
        1   => "1 час",
        5   => "5 часов",
        24  => "24 часа",
        168 => "7 дней",
        720 => "30 дней"
    ];

    public function __construct()
    {
        $this->posts = new DBController;
        $this->db_ads = $this->posts->getPosts();
        $this->to_view = [
            'ads' => $this->db_ads,
            'ads_count' => $this->posts->getPosts("count"),
            'currencies' => $this->currencies,
            'path' => $this->parseUri(),
            'date_sort' => $this->date_sort
        ];
    }

    public function parseUri(){
        $url = explode("?", \Request::getRequestUri());
        $path = $url[0];
        $query = '';
        $hours = 24;

        if( !empty($url[1]) ){
            $query = $url[1];
            // var_dump($url[1]);
            $hours_pattern = "/(?<=(date\=))[\d+.-]+/";
            preg_match($hours_pattern, $url[1], $matches);
            $hours = $matches[0];
        }
        $path_parts = [ 
            "sell_buy" => "all", 
            "currency" => "", 
            "query"    => $query,
            "hours"    => $hours    // количество часов для фильтрации
        ];
        
        if( $path !== "/" ){
            $path_array = explode("/", $path);
            $path_parts["sell_buy"] =  $path_array[2];
            $path_parts["currency"] = empty($path_array[3]) ? '' : $path_array[3];
        }
        return $path_parts;
    }

    public function show( $sell_buy = "all", $currency = '' )
    {
        $this->to_view['ads'] = $this->posts->getPosts( "get", $sell_buy, $currency );
        $this->to_view['ads_count'] = $this->posts->getPosts("count", $sell_buy, $currency);
        return view('currency', $this->to_view);
    }

    public function index()
    {
        // $this->to_view['ads'] = GetAdsController::getPosts( "-87785879" );
        return view('currency', $this->to_view);
    }
}