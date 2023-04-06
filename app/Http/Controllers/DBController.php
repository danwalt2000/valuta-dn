<?php
 
namespace App\Http\Controllers;
use Log;
use Config;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Psr\Http\Message\RequestInterface;
 
class DBController extends Controller
{
    public static function getPosts( 
            $table = 'ads',
            $get_or_count = 'get', 
            $sell_buy = '', 
            $currency = '', 
            $search = '', 
            $offset = 0,
            $rate = 0.01
        ){
        $sort = 'date';
        $limit = 20;

        $rate_limit = $rate;
        // по умолчанию отображаем только объявления с курсом
        if( !empty($_GET["rate"]) && "false" == $_GET["rate"] ){
            $rate_limit = 0;
        }

        // проверяем, чтобы get-параметры строго соответствовали значениям
        if(!empty($_GET["sort"]) &&  ( str_contains( "date", $_GET["sort"]) || str_contains( "rate", $_GET["sort"]) ) ){
            $sort = $_GET["sort"];
        } 
        $asc_desc = 'desc';
        if(!empty($_GET["order"]) && ( str_contains( "asc", $_GET["order"]) || str_contains( "desc", $_GET["order"]) ) ){
            $asc_desc = $_GET["order"];
        }
        
        // период, за который запрашиваются записи - измеряется в часах
        $time_range = 24;
        if(!empty($_GET["date"]) && 
            filter_var($_GET["date"], FILTER_VALIDATE_INT)!== false &&
            (24 == $_GET["date"] || 5 == $_GET["date"] || 168 == $_GET["date"]) ){
            $time_range = $_GET["date"];
        }

        // Тип объявления, строится по принципу: (купля/продажа)_валюта
        // купля/продажа берется из пути sellbuy. Варианты: all, sell, buy
        // Валюта берется из пути currency
        $query = '';
        if( !empty($sell_buy) || !empty($currency) ){
            if($sell_buy != "all"){
                $query = $sell_buy . '_';
            }
            $query .= $currency;
        }

        // строка поиска
        $search_clean = '';
        if( !empty($search) ){
            $search_clean = htmlspecialchars($search);
        }
        $skip = $offset * $limit;

        $cut_by_time = time() - $time_range * 60 * 60;
        
        return DB::table($table)
                ->where("date", ">", $cut_by_time)
                ->where('type', 'like', "%" . $query . "%")
                ->where('content', 'like', "%" . $search_clean . "%")
                ->where('rate', '>=', $rate_limit)
                ->orderBy($sort, $asc_desc)
                ->skip($skip)
                ->take($limit)
                ->$get_or_count();
    }

    public static function getPostByContent( $content ){
        $table = SessionController::getHost()["table"];
        return DB::table($table)->where( 'content_changed', $content )->first();
    }

    public static function getPostById( $id ){
        $table = SessionController::getHost()["table"];
        return DB::table($table)->where( 'vk_id', $id )->first();
    }
    
    public static function getPhone( $info ){
        $ad = DBController::getPostById( $info["postId"] ); 
        
        // бывает, что пользователь находится на странице так долго, 
        // что за это время сервер успевает обновить объявление и по id его уже не найти. 
        // В таком случае ищем в БД объявление по содержанию
        if( empty($ad) ) $ad = DBController::getPostByContent( $info["content"] );
        if( empty($ad) ) return;

        // если телефонов несколько, отдаем по индексу
        $phones = explode(",", $ad->phone);
        return $phones[$info["phoneIndex"]];
    }

    public static function storePosts( $table, $args )
    {
        DB::table($table)->updateOrInsert( [ 'content' => $args['content'] ], $args );
    }
}