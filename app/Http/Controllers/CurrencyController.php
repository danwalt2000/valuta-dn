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
    public $path = [];
    
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
    public $currencies_loc = [
       "dollar" => "доллара",
       "euro" => "евро",
       "hrn" => "гривны",
       "cashless" => "безнала руб."
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
        $this->path = $this->parseUri();
        $this->to_view = [
            'ads' => $this->db_ads,
            'ads_count'      => $this->posts->getPosts("count"),
            'currencies'     => $this->currencies,
            'currencies_loc' => $this->currencies_loc,
            'path'           => $this->path,
            'date_sort'      => $this->date_sort,
            'h1'             => $this->getH1(),
            'search'         => ''    
        ];
    }

    public function getH1(){
        $path = $this->path;
        $result = "Объявления о ";
        if( $path['sell_buy'] == 'sell'){
            $result .= "продаже ";
        } elseif( $path['sell_buy'] == 'buy'){
            $result .= "покупке ";
        } else{
            $result .= "продаже/покупке ";
        }
        foreach($this->currencies_loc as $name => $title){
            if($path["currency"] == ''){
                $result .= "валюты";
                break;
            }
            if($path["currency"] == $name ){
                $result .= $title;
            }
        }
        return $result . " в Донецке";
    }

    public function parseUri(){
        $url = explode("?", \Request::getRequestUri());
        $path = $url[0];
        $query = '';
        $hours = 24;
        $sort = "date_desc";

        if( !empty($url[1]) ){
            $query = $url[1];
            $hours_pattern = "/(?<=(date\=))[\d+.-]+/";
            $sort_type_pattern = "/((?<=(sort\=))[\w+.]+)/";
            $order_pattern = "/((?<=(order\=))[\w+.]+)/";
            preg_match($hours_pattern, $url[1], $hours_matches);
            preg_match($sort_type_pattern, $url[1], $sort_matches);
            preg_match($order_pattern, $url[1], $order_matches);
            if(!empty($hours_matches[0])) $hours = $hours_matches[0];
            if(!empty($sort_matches[0]) && !empty($order_matches[0])) $sort = $sort_matches[0] . "_" . $order_matches[0];
        }
        $path_parts = [ 
            "sell_buy" => "all", 
            "currency" => "", 
            "query"    => $query,    // строка get-параметров
            "hours"    => $hours,    // количество часов для фильтрации
            "sort"     => $sort      // тип сортировки для подсветки активных чипсов
        ];
        
        if( $path !== "/" && $path !== "/s" ){
            $path_array = explode("/", $path);
            $path_parts["sell_buy"] =  $path_array[2];
            $path_parts["currency"] = empty($path_array[3]) ? '' : $path_array[3];
        }
        return $path_parts;
    }

    public function ajaxPost(Request $request){
        $input = $request->all();
          
        Log::info($input);
     
        return response()->json(['success'=>'Got Simple Ajax Request.']);
    }
    public function show( $sell_buy = "all", $currency = '' )
    {
        $this->to_view['ads'] = $this->posts->getPosts( "get", $sell_buy, $currency );
        $this->to_view['ads_count'] = $this->posts->getPosts("count", $sell_buy, $currency);
        return view('currency', $this->to_view);
    }
    public function search()
    {
        if( !empty($_GET["search"]) ){
            $search = $_GET["search"];
        }
        $this->to_view['search'] = $search;
        $this->to_view['ads'] = $this->posts->getPosts( "get", "all", "", $search );
        $this->to_view['ads_count'] = $this->posts->getPosts( "count", "all", "", $search );
        return view('search', $this->to_view);
    }

    public function index()
    {
        // $this->to_view['ads'] = GetAdsController::getPosts( "-87785879" );
        // $this->to_view['ads'] = DBController::getPhone( ["postId" => 1558558, "phoneIndex" => "0"] );
        // $this->to_view['ads'] = DBController::getPhone( ["postId" => 58, "phoneIndex" => 0] );
        return view('currency', $this->to_view);
    }
}