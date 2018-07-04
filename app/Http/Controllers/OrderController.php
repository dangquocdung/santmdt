<?php
namespace shopist\Http\Controllers;

use shopist\Http\Controllers\Controller;
use shopist\Models\Post;
use shopist\Models\PostExtra;
use shopist\Models\DownloadExtra;
use Illuminate\Support\Facades\DB;
use shopist\Library\CommonFunction;
use Carbon\Carbon;
use Session;


class OrderController extends Controller
{
  public $classCommonFunction;
  public $carbonObject;
  
  public function __construct(){
    $this->classCommonFunction  =  new CommonFunction();
    $this->carbonObject = new Carbon();
  }
  /**
   * 
   * Get order list
   *
   * @param all order or current date order
   * @return array
   */
  public function getOrderList( $order_track ){
    $order_data = array();
    
    if(is_vendor_login() && Session::has('shopist_admin_user_id')){
      if($order_track == 'all_order'){
        $get_order  = DB::table('posts')
                      ->where(['posts.post_type' => 'shop_order'])
                      ->where(['vendor_orders.vendor_id' => Session::get('shopist_admin_user_id')])
                      ->join('vendor_orders', 'vendor_orders.order_id', '=', 'posts.id')
                      ->orderBy('posts.id', 'desc')
                      ->select('posts.*')
                      ->get()
                      ->toArray();
      }
      elseif($order_track == 'current_date_order'){
        $get_order  = DB::table('posts')
                      ->where(['posts.post_type' => 'shop_order'])
                      ->where(['vendor_orders.vendor_id' => Session::get('shopist_admin_user_id')])
                      ->whereDate('posts.created_at', '=', $this->carbonObject->today()->toDateString())
                      ->join('vendor_orders', 'vendor_orders.order_id', '=', 'posts.id')
                      ->orderBy('posts.id', 'desc')
                      ->select('posts.*')
                      ->get()
                      ->toArray();
      }
      
      if(count($get_order) >0){
        $order_data = $this->manageAllOrders( $this->classCommonFunction->objToArray( $get_order ));
      }
    }
    else{
      if($order_track == 'all_order'){
        $get_order = Post::where(['parent_id' => 0, 'post_type' => 'shop_order'])->orderBy('id', 'DESC')->get()->toArray();
      }
      elseif($order_track == 'current_date_order'){
        $get_order = Post::whereDate('created_at', '=', $this->carbonObject->today()->toDateString())->where(['parent_id' => 0, 'post_type' => 'shop_order'])->get()->toArray();
      }
      
      if(count($get_order) >0){
        $order_data = $this->manageAllOrders( $get_order );
      }
    }
    
    return $order_data;
  }
  
  /**
   * 
   * Manage all orders 
   *
   * @param order array
   * @return array
   */
  public function manageAllOrders( $get_order ){
    $order_data = array();
   
    if(count($get_order) > 0){
      foreach($get_order as $order){
        $order_postmeta = array();
        $get_postmeta_by_order_id = PostExtra::where(['post_id' => $order['id']])->get();
        
        if($get_postmeta_by_order_id->count() > 0){
          $date_format = new Carbon( $order['created_at']);

          $order_postmeta['_post_id']    = $order['id'];
          $order_postmeta['_order_date'] = $date_format->toDayDateTimeString();


          foreach($get_postmeta_by_order_id as $postmeta_row){
            if( $postmeta_row->key_name == '_order_status' || $postmeta_row->key_name == '_order_total' || $postmeta_row->key_name == '_final_order_total' || $postmeta_row->key_name == '_order_currency' ){
            $order_postmeta[$postmeta_row->key_name] = $postmeta_row->key_value;
            }
          }
        }
        
        $get_sub_order = get_vendor_sub_order_by_order_id($order['id']);
        $order_postmeta['_sub_order'] = array();
        
        if(count( $get_sub_order ) > 0){ 
          foreach($get_sub_order as $sub_order){
            $sub_order_postmeta = array();
            $get_postmeta_by_sub_order_id = PostExtra::where(['post_id' => $sub_order['parent_id']])->get();
            
            if($get_postmeta_by_sub_order_id->count() > 0){
              $sub_order_date_format = new Carbon( $sub_order['created_at']);

              $sub_order_postmeta['_post_id']    = $sub_order['id'];
              $sub_order_postmeta['_order_date'] = $sub_order_date_format->toDayDateTimeString();


              foreach($get_postmeta_by_sub_order_id as $sub_order_postmeta_row){
                if( $sub_order_postmeta_row->key_name == '_order_status' || $sub_order_postmeta_row->key_name == '_order_total' || $sub_order_postmeta_row->key_name == '_final_order_total' || $sub_order_postmeta_row->key_name == '_order_currency' ){
                $sub_order_postmeta[$sub_order_postmeta_row->key_name] = $sub_order_postmeta_row->key_value;
                }
              }
            }
            array_push($order_postmeta['_sub_order'], $sub_order_postmeta);
          }
        }
        
        array_push($order_data, $order_postmeta);
      }
    }
    return $order_data;
  }
  
   /**
   * 
   * Get order download history
   *
   * @param order_id
   * @return array
   */
  public function getOrderDownloadHistory( $order_id){
    $order_data = array();
    $get_order_data = DB::table('download_extras')
                      ->select('file_name', 'file_url', DB::raw('count(*) as total'))
                      ->where('order_id', $order_id)
                      ->groupBy('file_name', 'file_url')
                      ->get()->toArray();
    
    
    if(count($get_order_data) > 0){
      $order_data = $get_order_data;
    }
    
    return $order_data;
  }
}