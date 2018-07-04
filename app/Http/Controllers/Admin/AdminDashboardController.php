<?php
namespace shopist\Http\Controllers\Admin;

use shopist\Http\Controllers\Controller;
use Validator;
use Request;
use Session;
use shopist\Models\Post;
use shopist\Models\AttributesList;
use shopist\Models\Option;
use shopist\Models\ArtList;
use shopist\Models\SaveCustomDesign;
use shopist\Library\GetFunction;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use shopist\Models\OrdersItem;
use shopist\Models\User;
use shopist\Models\VendorPackage;
use shopist\Models\VendorWithdraw;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Lang;
use shopist\Models\ManageLanguage;
use Illuminate\Support\Facades\Input;
use shopist\Models\Subscription;
use Illuminate\Support\Facades\URL;
use shopist\Models\Term;
use shopist\Models\PostExtra;
use shopist\Http\Controllers\ProductsController;
use shopist\Http\Controllers\OptionController;
use shopist\Http\Controllers\CMSController;
use shopist\Http\Controllers\DesignerElementsController;
use shopist\Http\Controllers\FeaturesController;
use shopist\Library\CommonFunction;
use shopist\Http\Controllers\OrderController;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use shopist\Http\Controllers\VendorsController;
use Illuminate\Support\Facades\App;


class AdminDashboardController extends Controller
{
  public $classGetFunction;
  public $currentUserData       =   array();
  public $settingsData          =   array();
  public $currency_symbol;
  public $dashboardData         =   array();
  public $carbonObject;
  public $adminJsLocalization;
  public $product;
	public $option;
  public $CMS;
  public $designer_elements;
  public $features;
  public $user;
  public $classCommonFunction;
  public $order;
  public $vendors;
  public $login_user_details = null;

  public function __construct(){
    $this->middleware('verifyLoginPage');
    $this->product             =  new ProductsController();
    $this->option              =  new OptionController();
    $this->CMS                 =  new CMSController();
    $this->designer_elements   =  new DesignerElementsController();
    $this->features            =  new FeaturesController();
    $this->user                =  new UserController();  
    $this->classCommonFunction  =   new CommonFunction();
    $this->order  =   new OrderController();
    $this->vendors  =   new VendorsController();

    $this->classGetFunction       =   new GetFunction();
    $this->settingsData           =   $this->option->getSettingsData();
    $this->currency_symbol        =   $this->classCommonFunction->get_currency_symbol( $this->settingsData['general_settings']['currency_options']['currency_name'] );
    
    $this->dashboardData['settings_data']   =   $this->settingsData;
    $this->dashboardData['currency_symbol'] =   $this->currency_symbol;
    $this->carbonObject = new Carbon();
    
    $this->adminJsLocalization = array('variations_exists_msg' => Lang::get('admin-js.variations_exists_msg'), 'create_new_category' => Lang::get('admin-js.create_new_category'), 'create_category' => Lang::get('admin-js.create_category'), 'create_new_product_tag' => Lang::get('admin-js.create_new_product_tag'), 'create_tag' => Lang::get('admin-js.create_tag'), 'create_new_product_variation' => Lang::get('admin-js.create_new_product_variation'), 'add_variation' => Lang::get('admin-js.add_variation'), 'work_at_edit_page' => Lang::get('admin-js.work_at_edit_page'), 'name_slug_field_are_required' => Lang::get('admin-js.name_slug_field_are_required'), 'slug_already_exists' => Lang::get('admin-js.slug_already_exists'), 'sku_field_require' => Lang::get('admin-js.sku_field_require'), 'sku_already_exists' => Lang::get('admin-js.sku_already_exists'),  'update_product_category' => Lang::get('admin-js.update_product_category'), 'update_category' => Lang::get('admin-js.update_category'), 'update_product_tag' => Lang::get('admin-js.update_product_tag'), 'update_tag' => Lang::get('admin-js.update_tag'), 'update_product_variation' => Lang::get('admin-js.update_product_variation'), 'update_variation_data' => Lang::get('admin-js.update_variation_data'), 'no_attributes_available' => Lang::get('admin-js.no_attributes_available'), 'are_you_sure' => Lang::get('admin-js.are_you_sure'), 'you_want_to_delete_this_item' => Lang::get('admin-js.you_want_to_delete_this_item'), 'yes_delete_it' => Lang::get('admin-js.yes_delete_it'), 'deleted' => Lang::get('admin-js.deleted'), 'your_selected_item_deleted' => Lang::get('admin-js.your_selected_item_deleted'), 'good_job' => Lang::get('admin-js.good_job'), 'selected_item_successfully_deleted' => Lang::get('admin-js.selected_item_successfully_deleted'), 'selected_item_successfully_saved' => Lang::get('admin-js.selected_item_successfully_saved'), 'selected_item_successfully_updated' => Lang::get('admin-js.selected_item_successfully_updated'),'upload_design_image' => Lang::get('admin-js.upload_design_image'), 'remove_image' => Lang::get('admin-js.remove_image'), 'upload_design_transparent_image' => Lang::get('admin-js.upload_design_transparent_image'), 'upload_design_title_icon' => Lang::get('admin-js.upload_design_title_icon'), 'no_result_found' => Lang::get('admin-js.no_result_found'), 'dropzone_file_exceeded_msg_1' => Lang::get('admin-js.dropzone_file_exceeded_msg_1'), 'dropzone_file_exceeded_msg_2' => Lang::get('admin-js.dropzone_file_exceeded_msg_2'), 'dropzone_file_exceeded_msg_3' => Lang::get('admin-js.dropzone_file_exceeded_msg_3'), 'please_upload_only_image_file' => Lang::get('admin-js.please_upload_only_image_file'), 'successfully_uploaded_your_image' => Lang::get('admin-js.successfully_uploaded_your_image'), 'total' => Lang::get('admin-js.total'), 'maxfilesexceeded_msg' => Lang::get('designer.maxfilesexceeded_msg'), 'file_larger' => Lang::get('designer.file_larger'), 'image_file_validation' => Lang::get('designer.image_file_validation'), 'good_job' => Lang::get('designer.good_job'), 'image_upload_success' => Lang::get('designer.image_upload_success'), 'admin_dashboard' => Lang::get('admin-js.eb-admin'), 'logout' => Lang::get('admin-js.logout'), 'appearance_running_template_text' => Lang::get('admin-js.appearance_running_template_text'), 'appearance_template_activated_text' => Lang::get('admin-js.appearance_template_activated_text'),  'text_add_btn_label' => Lang::get('admin-js.add_text_on_image_btn_text'), 'html_code_required_msg' => Lang::get('admin-js.html_code_required_msg'), 'html_code_added_msg' => Lang::get('admin-js.html_code_added_msg'), 'comment_enable_msg' => Lang::get('admin-js.comment_enable_msg'), 'comment_btn_enable_msg' => Lang::get('admin-js.comment_btn_enable_msg'), 'comment_enable_done_msg' => Lang::get('admin-js.comment_enable_done_msg'), 'comment_disable_msg' => Lang::get('admin-js.comment_disable_msg'), 'comment_btn_disable_msg' => Lang::get('admin-js.comment_btn_disable_msg'), 'comment_disable_done_msg' => Lang::get('admin-js.comment_disable_done_msg'), 'comment_disable_done_msg' => Lang::get('admin-js.comment_disable_done_msg'), 'more_compare_field_placeholder' => Lang::get('admin-js.more_compare_field_placeholder'), 'remove_text' => Lang::get('admin-js.remove_text'), 'color_filter_color_name_placeholder' => Lang::get('admin-js.color_filter_color_name_placeholder'), 'create_new_product_attrs' => Lang::get('admin-js.create_new_product_attrs'), 'create_attr' => Lang::get('admin-js.create_attr'), 'attr_name_values_are_required' => Lang::get('admin-js.attr_name_values_are_required'), 'update_product_attr' => Lang::get('admin-js.update_product_attr'), 'update_attr' => Lang::get('admin-js.update_attr'), 'colors_are_required' => Lang::get('admin-js.colors_are_required'), 'sizes_are_required' => Lang::get('admin-js.sizes_are_required'), 'create_new_product_color' => Lang::get('admin-js.create_new_product_color'), 'create_color' => Lang::get('admin-js.create_color'), 'create_new_product_size' => Lang::get('admin-js.create_new_product_size'), 'create_size' => Lang::get('admin-js.create_size'), 'update_product_color' => Lang::get('admin-js.update_product_color'), 'update_color' => Lang::get('admin-js.update_color'), 'update_product_size' => Lang::get('admin-js.update_product_size'), 'update_size' => Lang::get('admin-js.update_size'), 'downloadable_name_label' => Lang::get('admin-js.downloadable_name_label'), 'downloadable_file_url_label' => Lang::get('admin-js.downloadable_file_url_label'), 'downloadable_placeholder_file_name' => Lang::get('admin-js.downloadable_placeholder_file_name'), 'downloadable_file_label' => Lang::get('admin-js.downloadable_file_label'), 'downloadable_url_label' => Lang::get('admin-js.downloadable_url_label'), 'downloadable_choose_file_label' => Lang::get('admin-js.downloadable_choose_file_label'), 'downloadable_uploaded_file_url_placeholder' => Lang::get('admin-js.downloadable_uploaded_file_url_placeholder'), 'downloadable_online_file_url_placeholder' => Lang::get('admin-js.downloadable_online_file_url_placeholder'), 'downloadable_add_more_file_label' => Lang::get('admin-js.downloadable_add_more_file_label'), 'unknown_msg_label' => Lang::get('admin-js.unknown_msg_label'), 'remove_text' => Lang::get('admin-js.remove_text'), 'csv_extension_error_label' => Lang::get('admin-js.csv_extension_error_label'), 'csv_header_error_label' => Lang::get('admin-js.csv_header_error_label'), 'csv_saved_label' => Lang::get('admin-js.csv_saved_label'), 'choose_csv_file_label' => Lang::get('admin-js.choose_csv_file_label'), 'you_want_to_enable_vendor_this_item' => Lang::get('admin-js.you_want_to_enable_vendor_this_item'), 'you_want_to_disable_vendor_this_item' => Lang::get('admin-js.you_want_to_disable_vendor_this_item'), 'yes_enable_it' => Lang::get('admin-js.yes_enable_it'), 'yes_disable_it' => Lang::get('admin-js.yes_disable_it'), 'updated_label' => Lang::get('admin-js.updated_label'), 'vendor_enable_update_label' => Lang::get('admin-js.vendor_enable_update_label'), 'vendor_disable_update_label' => Lang::get('admin-js.vendor_disable_update_label'), 'vendor_package_title_label' => Lang::get('admin-js.vendor_package_title_label'), 'vendor_max_products_label' => Lang::get('admin-js.vendor_max_products_label'), 'vendor_show_map_on_store_page_label' => Lang::get('admin-js.vendor_show_map_on_store_page_label'), 'vendor_show_follow_btn_on_store_page_label' => Lang::get('admin-js.vendor_show_follow_btn_on_store_page_label'), 'vendor_show_share_btn_on_store_page_label' => Lang::get('admin-js.vendor_show_share_btn_on_store_page_label'), 'vendor_show_contact_form_on_store_page_label' => Lang::get('admin-js.vendor_show_contact_form_on_store_page_label'), 'vendor_payment_withdraw_options_label' => Lang::get('admin-js.vendor_payment_withdraw_options_label'), 'vendor_expired_type_label' => Lang::get('admin-js.vendor_expired_type_label'), 'vendor_commission_label' => Lang::get('admin-js.vendor_commission_label'), 'vendor_expired_date_label' => Lang::get('admin-js.vendor_expired_date_label'), 'yes_label' => Lang::get('admin-js.yes_label'), 'no_label' => Lang::get('admin-js.no_label'), 'mass_payment_label' => Lang::get('admin-js.mass_payment_label'), 'manually_payment_label' => Lang::get('admin-js.manually_payment_label'), 'custom_date_label' => Lang::get('admin-js.custom_date_label'), 'lifetime_label' => Lang::get('admin-js.lifetime_label'), 'order_label' => Lang::get('admin-js.order_label'), 'exceed_product_add' => Lang::get('admin-js.exceed_product_add'), 'min_withdraw_amount_label' => Lang::get('admin-js.min_withdraw_amount_label'), 'withdrawal_successfully_completed' => Lang::get('admin-js.withdrawal_successfully_completed'), 'withdrawal_successfully_cancelled' => Lang::get('admin-js.withdrawal_successfully_cancelled'), 'completed_it_label' => Lang::get('admin-js.completed_it_label'), 'cancelled_it_label' => Lang::get('admin-js.cancelled_it_label'), 'request_completed_msg' => Lang::get('admin-js.request_completed_msg'), 'request_cancelled_msg' => Lang::get('admin-js.request_cancelled_msg'), 'select_attr_values' => Lang::get('admin-js.select_attr_values'), 'exceed_hundred_products' => Lang::get('admin-js.exceed_hundred_products'));
    
  }
  
  /**
   * 
   * Manage admin dashboard
   *
   * @param request id
   * @return response
   */
  public function redirectToAdminDashboard($params = null){   
    $getRoutePrefix = trim(Request::route()->getPrefix(), '/');
    
    if( isset($getRoutePrefix) && $getRoutePrefix == 'admin' && !Session::has('shopist_admin_user_id') ){
      return redirect()->route('admin.login');
    }
    else{ 
      // language setting option
      $this->classCommonFunction->set_admin_lang();
      
      //check admin menu page permission
      if(!is_sufficient_permission()){
        return view('errors.no_permission');
      }
            
      if(is_vendor_login() && Session::has('shopist_admin_user_id')){
        if(is_vendor_expired(Session::get('shopist_admin_user_id'))){
          return view('errors.vendor_expired');
        }
          
        $get_user_store_data = get_user_account_details_by_user_id( Session::get('shopist_admin_user_id'));
        $this->login_user_details = json_decode(array_shift($get_user_store_data)['details']);
      }
      
      $this->currentUserData                =   get_current_admin_user_info();
      $this->dashboardData['user_data']     =   $this->currentUserData;
      $get_role_data = get_roles_details_by_role_id( $this->currentUserData['user_role_id'] );
      
      if(!empty($get_role_data)){
        $this->dashboardData['user_permission_list'] =   $get_role_data->permissions;
      }
      else{
        $this->dashboardData['user_permission_list'] = array();
      }
         
      $order_list_data         =  array();
      $order_data_by_id        =  array();
		
		if(Request::is('admin/product/categories/list') || Request::is('admin/blog/categories/list')){
      $search_value = '';
        
      if(isset($_GET['term_cat']) && $_GET['term_cat'] != ''){
        $search_value = $_GET['term_cat'];
      }
        
		  if(Request::is('admin/product/categories/list')){
				$this->dashboardData['cat_list_data']           =  $this->product->getTermData( 'product_cat', true, $search_value, -1 );
				$this->dashboardData['only_cat_name']           =  $this->product->get_categories_name('product_cat');
        $this->dashboardData['search_value']            =  $search_value;
        $this->dashboardData['action']                  =  route('admin.product_categories_list');
		  }
		  
		  if(Request::is('admin/blog/categories/list')){
        $this->dashboardData['cat_list_data']        =   $this->product->getTermData( 'blog_cat', true, $search_value, -1 );
        $this->dashboardData['only_cat_name']        =   $this->product->get_categories_name('blog_cat');
        $this->dashboardData['search_value']         =   $search_value;
        $this->dashboardData['action']               =   route('admin.blog_categories_list');
		  }
		}
		
		if(Request::is('admin/product/tags/list')){
      $search_value = '';
      
      if(isset($_GET['term_tag']) && $_GET['term_tag'] != ''){
        $search_value = $_GET['term_tag'];
      }
		  
      $this->dashboardData['tag_list_data']   =  $this->product->getTermData( 'product_tag', true, $search_value, -1 );
      $this->dashboardData['search_value']    =  $search_value;
		}
		
		if(Request::is('admin/product/attributes/list')){
      $search_value = '';
      
      if(isset($_GET['term_attrs']) && $_GET['term_attrs'] != ''){
        $search_value = $_GET['term_attrs'];
      }
      
		  $this->dashboardData['attribute_list_data']   =   $this->product->getTermData( 'product_attr', true, $search_value, -1 );
      $this->dashboardData['search_value']          =  $search_value;
		}
    
    if(Request::is('admin/product/colors/list')){
      $search_value = '';
      
      if(isset($_GET['term_colors']) && $_GET['term_colors'] != ''){
        $search_value = $_GET['term_colors'];
      }
      
		  $this->dashboardData['colors_list_data']   =   $this->product->getTermData( 'product_colors', true, $search_value, -1 );
      $this->dashboardData['search_value']       =   $search_value;
		}
    
    if(Request::is('admin/product/sizes/list')){
      $search_value = '';
      
      if(isset($_GET['term_sizes']) && $_GET['term_sizes'] != ''){
        $search_value = $_GET['term_sizes'];
      }
      
		  $this->dashboardData['sizes_list_data']   =   $this->product->getTermData( 'product_sizes', true, $search_value, -1 );
      $this->dashboardData['search_value']      =   $search_value;
		}
		
		if(Request::is('admin/product/add') || Request::is('admin/product/update/*')){
        $this->dashboardData['tabSettings']['btnCustomize']   =   'style=display:none;';
        $this->dashboardData['categories_lists']              =   $this->product->get_categories( 0, 'product_cat');
        $this->dashboardData['tags_lists']                    =   $this->product->getTermData( 'product_tag', false, null, 1 );
        $this->dashboardData['attrs_list_data']               =   $this->product->getTermData( 'product_attr', false, null, 1 );
        $this->dashboardData['colors_lists']                  =   $this->product->getTermData( 'product_colors', false, null, 1 );
        $this->dashboardData['sizes_lists']                   =   $this->product->getTermData( 'product_sizes', false, null, 1 );
        $this->dashboardData['manufacturer_lists']            =   $this->product->getTermData( 'product_brands', false, null, 1 );
        $this->dashboardData['available_user_roles']          =   get_available_user_roles();
        $this->dashboardData['vendors_list']                  =   $this->vendors->getAllVendors( false, null, 1 );
        
        $this->dashboardData['product_all_images_json']				=		json_encode(  
                                                                    array(                                                                                                                                          'product_image'            => '',
                                                                      'product_gallery_images'   => array(),
                                                                      'shop_banner_image'        => ''
                                                                    )
																																	);
		}
		
		if(Request::is('admin/manufacturers/list')){
      $search_value = '';
      if(isset($_GET['term_brand']) && $_GET['term_brand'] != ''){
        $search_value = $_GET['term_brand'];
      }
      
		  $this->dashboardData['manufacturerslist']   =   $this->product->getTermData( 'product_brands', true, $search_value, -1 );
      $this->dashboardData['search_value']        =   $search_value;
		}
    
    if(Request::is('admin/product/comments-list')){
		  $this->dashboardData['product_comments']   =   $this->product->getProductCommentsList();
		}
    
    if(Request::is('admin/vendors/comments-list')){
		  $this->dashboardData['vendor_comments']    =   $this->vendors->getVendorCommentsList();
      if(is_vendor_login()){
        $vendor_details = get_current_vendor_user_info();
        
        $this->dashboardData['vendor_name'] = $vendor_details['user_name'];
      }
		}
    	
		if(Request::is('admin/designer/clipart/categories/list')){
      $search_value = '';
      
      if(isset($_GET['term_art_cat']) && $_GET['term_art_cat'] != ''){
        $search_value = $_GET['term_art_cat'];
      }
      
		  $this->dashboardData['art_cat_lists_data']   =   $this->product->getTermData( 'designer_cat', true, $search_value, -1 );
      $this->dashboardData['search_value']         =   $search_value;
		}
		
		if(Request::is('admin/designer/clipart/list')){
      $search_value = '';
      
      if(isset($_GET['term_art_cat']) && $_GET['term_art_cat'] != ''){
        $search_value = $_GET['term_art_cat'];
      }
      
		  $this->dashboardData['art_lists']     =   $this->designer_elements->getArtListData(true, $search_value, -1);
      $this->dashboardData['search_value']  =   $search_value;
		}
		
		if(Request::is('admin/designer/clipart/add') ||  Request::is('admin/designer/clipart/update/*')){
		  $this->dashboardData['getArtCatByFilter']   =   $this->product->getTermData( 'designer_cat', false, null, 1 );
		}
		
		if(Request::is('admin/shipping-method/options') || Request::is('admin/shipping-method/flat-rate') || Request::is('admin/shipping-method/free-shipping') || Request::is('admin/shipping-method/local-delivery')){
      
      if(is_vendor_login()){
        $this->dashboardData['shipping_method_data'] = $this->login_user_details->shipping_method;
      }
      else{
        $unserialize_data = $this->option->getShippingMethodData();
        $this->dashboardData['shipping_method_data']   =   $unserialize_data;
      }
		}
		
    if(Request::is('admin/pages/list')){
      $search_value = '';
      
      if(isset($_GET['term_page']) && $_GET['term_page'] != ''){
        $search_value = $_GET['term_page'];
      }
      
      $this->dashboardData['pages_list']   = $this->CMS->get_pages( true, $search_value, -1 );
      $this->dashboardData['search_value'] = $search_value;
    }
    
    if(!empty($params) && Request::is('admin/page/update/*')){
      $get_page_data_by_slug = $this->CMS->get_page_by_slug( $params );
      
      if(!empty($get_page_data_by_slug)){
        $this->dashboardData['page_data_by_slug']  =  $get_page_data_by_slug;
      }
      else{
        return view('errors.no_data');
      }
		}
    
		if(Request::is('admin/blog/add') || ($params && Request::is('admin/blog/update/*'))){
		  $this->dashboardData['blog_categories_lists']     =   $this->product->get_categories( 0, 'blog_cat');
		}
		
		if($params && Request::is('admin/blog/update/*')){
		  $get_blog_details_by_slug = $this->CMS->get_blog_by_slug( $params );
		  
		  if(count($get_blog_details_by_slug) > 0){
        $get_object_id = $get_blog_details_by_slug['id'];
        $this->dashboardData['blog_details_by_slug']     =   $get_blog_details_by_slug;
        $this->dashboardData['selected_cat']    =   $this->CMS->create_blog_cat_id_array( $get_object_id );
		  }
		  else{
        return view('errors.no_data');
		  }
		}
		
		if(Request::is('admin/blog/list')){ 
      $search_value = '';
      
      if(isset($_GET['term_blog']) && $_GET['term_blog'] != ''){
        $search_value = $_GET['term_blog'];
      }
      
      $this->dashboardData['blogs_list_data']   = $this->CMS->get_blogs( true, $search_value, -1 );
      $this->dashboardData['search_value']      = $search_value;
		}
    
    if(Request::is('admin/blog/comments-list')){
		  $this->dashboardData['blog_comments']   =   $this->CMS->getBlogCommentsList();
		}
		
		if($params && Request::is('admin/product/list/*')){
      $search_value = '';
      
      if(isset($_GET['term_product']) && $_GET['term_product'] != ''){
        $search_value = $_GET['term_product'];
      }
     
		  $this->dashboardData['product_all_data']  =  $this->product->getPosts(true, $search_value, -1, $params);
      $this->dashboardData['search_value']      =  $search_value;
		}
		
		if(Request::is('admin/designer/settings') || Request::is('admin/product/update/*')){
		  $this->dashboardData['custom_designer_settings_data']  =  $this->option->getCustomDesignerSettingsData();
		}
		
		if(Request::is('admin/payment-method/options') || Request::is('admin/payment-method/direct-bank') || Request::is('admin/payment-method/cash-on-delivery') || Request::is('admin/payment-method/paypal') || Request::is('admin/payment-method/stripe')){
		  
      if(is_vendor_login()){
        $this->dashboardData['payment_method_data']  =  $this->login_user_details->payment_method;
      }
      else{
        $this->dashboardData['payment_method_data']  =  $this->option->getPaymentMethodData();
      }
		}
		
		if($params && Request::is('admin/product/attribute/update/*')){
		  $this->dashboardData['attr_update_data_by_id']        =   AttributesList :: where('attr_id', $params)->first();
		}
		
		if($params && Request::is('admin/manufacturers/update/*')){
      $getBrandsData = Term :: where('slug', $params)->first();
      
      if(!empty($getBrandsData)){
        $get_details_by_id =  $this->product->getTermDataById( $getBrandsData->term_id );
        $this->dashboardData['manufacturers_update_data']   =   array_shift($get_details_by_id);

        if($this->dashboardData['manufacturers_update_data']['brand_logo_img_url']){
          $this->dashboardData['manufacturers_logo_control']   =   array('sample_img' => 'style="display:none;"', 'manufacturers_logo' =>'style="display:block;"');
        }
        else {
          $this->dashboardData['manufacturers_logo_control']     =   array('sample_img' => 'style="display:block;"', 'manufacturers_logo' =>'style="display:none;"');
        }
      }
      else{
        return view('errors.no_data');
      }
		}
		
		if($params && Request::is('admin/designer/clipart/category/update/*')){
      $getArtCatData = Term :: where('slug', $params)->first();
      
      if(!empty($getArtCatData)){
        $get_details_by_id =  $this->product->getTermDataById( $getArtCatData->term_id );
        $this->dashboardData['art_cat_update_data_by_id']   =   array_shift($get_details_by_id);
      }
      else{
        return view('errors.no_data');
      }
		}
		
		if($params && Request::is('admin/designer/clipart/update/*')){
		   $this->dashboardData['art_update_data_by_id']           =   $this->designer_elements->getDesignerArtDataBySlug( $params );
       
       if(count($this->dashboardData['art_update_data_by_id']) > 0){
         $this->dashboardData['art_update_img_json']             =   '[{"id":"'. $this->dashboardData['art_update_data_by_id']['id'] .'","url":"'.       $this->dashboardData['art_update_data_by_id']['art_img_url'] .'"}]';
       }
       else{
         return view('errors.no_data');
       }
		}
		
		if($params && Request::is('admin/product/update/*')){
		  $get_post  =  Post :: where('post_slug', $params)->get()->toArray();
		  
		  if(count($get_post) > 0){
        $get_available_attribute =  array();
        $product_id = $get_post[0]['id'];
        
        $get_available_attribute	=  $this->product->getAllAttributes( $product_id );
        
			  $this->dashboardData['attrs_list_data_by_product']	 =   $get_available_attribute;
        $this->dashboardData['product_post_data']           =   $this->product->getProductDataById($product_id);
        
        $this->dashboardData['role_based_pricing_data']     =   get_role_based_pricing_by_product_id($product_id);
        $this->dashboardData['variation_data']          =   $this->classCommonFunction->get_variation_and_data_by_product_id( $product_id );
								
        $get_variation_data = $this->classCommonFunction->get_variation_by_product_id( $product_id );

        if(!empty($get_variation_data)){
          $this->dashboardData['variation_json']  =  json_encode($get_variation_data);
        }
        else{
          $this->dashboardData['variation_json'] = null;
        }
        
        
        $get_post_attr  =  PostExtra::where(['post_id' => $product_id, 'key_name' => '_attribute_post_data'])->first();
							
        if(!empty($get_post_attr)){
            $this->dashboardData['attribute_post_meta_by_id'] = json_decode($get_post_attr->key_value);
        }
        else{
            $this->dashboardData['attribute_post_meta_by_id'] = null;
        }
								
        
        if($this->dashboardData['product_post_data']['_product_custom_designer_settings']['enable_global_settings'] == 'yes'){
          if(count($this->dashboardData['custom_designer_settings_data'])>0){
            $this->dashboardData['designer_hf_data']   =  $this->dashboardData['custom_designer_settings_data']['general_settings'];
          }
        }
        elseif($this->dashboardData['product_post_data']['_product_custom_designer_settings']['enable_global_settings'] == 'no'){
          if(count($this->dashboardData['product_post_data']['_product_custom_designer_settings']) >0){
            $this->dashboardData['designer_hf_data']   =   $this->dashboardData['product_post_data']['_product_custom_designer_settings'];
          }
        }

        $get_data = SaveCustomDesign ::where('product_id', $product_id)->first();

        if(!empty($get_data) && $get_data->count() > 0){
         $this->dashboardData['design_json_data']  =   $get_data['design_data'];
        }
        else{
         $this->dashboardData['design_json_data']       =   '';
        }
        
        $this->dashboardData['art_cat_lists_data']  =   $this->product->getTermData( 'designer_cat', false, null, 1 );
								
        $generalTabActiveClass = '';
        $featureTabActiveClass = '';
        $customizeBtn          = 'style=display:none;';
        $tabSettings           = array();

        if($this->dashboardData['product_post_data']['_product_type'] == 'simple_product' || $this->dashboardData['product_post_data']['_product_type'] == 'downloadable_product'){
          $generalTabActiveClass = 'active';
        }
        elseif($this->dashboardData['product_post_data']['_product_type'] == 'customizable_product'){
          $generalTabActiveClass = 'active';
          $customizeBtn          = 'style=display:none;';
        }
        elseif($this->dashboardData['product_post_data']['_product_type'] == 'configurable_product'){
          $featureTabActiveClass = 'active';
        }

        $tabSettings['generalTab']   = $generalTabActiveClass;
        $tabSettings['featureTab']   = $featureTabActiveClass;
        $tabSettings['btnCustomize'] = $customizeBtn;

        $this->dashboardData['tabSettings'] = $tabSettings;

        
        $this->dashboardData['selected_cat'] = $this->product->getCatByObjectId( $product_id );
        $this->dashboardData['selected_tag'] = $this->product->getTagsByObjectId( $product_id );
        $this->dashboardData['selected_colors'] = $this->product->getColorsByObjectId( $product_id );
        $this->dashboardData['selected_sizes'] = $this->product->getSizesByObjectId( $product_id );
        $this->dashboardData['selected_brands'] = $this->product->getManufacturerByObjectId( $product_id );
        
        $this->dashboardData['crosssell_products'] = get_crosssell_products($product_id);
        
        $upsell_products = array();
        if(count(get_upsell_products($product_id)) > 0){
          foreach(get_upsell_products($product_id) as $upsell_ids){
            array_push($upsell_products, get_product_title($upsell_ids). ' #'.$upsell_ids);
          }
        }
        
        $this->dashboardData['upsell_products'] = json_encode( $upsell_products );
        
        $crosssell_products = array();
        if(count(get_crosssell_products($product_id)) > 0){
          foreach(get_crosssell_products($product_id) as $crosssell_ids){
            array_push($crosssell_products, get_product_title($crosssell_ids). ' #'.$crosssell_ids);
          }
        }
        
        $this->dashboardData['crosssell_products'] = json_encode( $crosssell_products );
		  }
		  else{
        return view('errors.no_data');
		  }
		}
		
		if(Request::is('admin/orders') || Request::is('admin/orders/current-date')){
		  if(Request::is('admin/orders')){
        $get_shop_order_data = $this->order->getOrderList('all_order'); 
		  }
		  elseif(Request::is('admin/orders/current-date')){
        $get_shop_order_data = $this->order->getOrderList('current_date_order');
		  }
     
      $currentPage = LengthAwarePaginator::resolveCurrentPage();
      $col = new Collection( $get_shop_order_data );
      $perPage = 10;
      $currentPageSearchResults = $col->slice(($currentPage - 1) * $perPage, $perPage)->all();
      $order_object = new LengthAwarePaginator($currentPageSearchResults, count($col), $perPage);

      $order_object->setPath( route('admin.shop_orders_list') );

      $this->dashboardData['orders_list_data']   =   $order_object;
		}
		
		if(Request::is('admin/orders/details/*') && $params){
      $order_id = 0;
      $get_post = Post::where(['id' => $params, 'post_type' => 'shop_order'])->first();
   
      if(!empty($get_post) && $get_post->parent_id > 0){
        $order_id = $get_post->parent_id;
      }
      else{
        $order_id = $params;
      }
      
      $get_post_by_order_id     = Post::where(['id' => $params])->first();
      $get_postmeta_by_order_id = PostExtra::where(['post_id' => $order_id])->get();
      $get_orders_items         = OrdersItem::where(['order_id' => $params])->first();
		  
		  if($get_post_by_order_id->count() > 0 && $get_postmeta_by_order_id ->count() > 0 && $get_orders_items->count() >0){
        $order_date_format = new Carbon( $get_post_by_order_id->created_at);

        $order_data_by_id = get_customer_order_billing_shipping_info( $order_id );
        $order_data_by_id['_order_id']    = $get_post_by_order_id->id;
        $order_data_by_id['_order_date']  = $order_date_format->toDayDateTimeString();

        foreach($get_postmeta_by_order_id as $postmeta_row_data){
          if($postmeta_row_data->key_name === '_order_shipping_method'){
            $order_data_by_id[$postmeta_row_data->key_name] = $this->classCommonFunction->get_shipping_label($postmeta_row_data->key_value);
          }
          elseif($postmeta_row_data->key_name == '_customer_user'){
            $user_data = unserialize($postmeta_row_data->key_value);
            if($user_data['user_mode'] == 'guest'){
              $order_data_by_id['_member']  = array('name' => 'Guest', 'url' => '');
            }
            elseif($user_data['user_mode'] == 'login'){
              $user_details_by_id = get_user_details($user_data['user_id']);
              $order_data_by_id['_member']  = array('name' => $user_details_by_id['user_display_name'], 'url' => $user_details_by_id['user_photo_url']);
            }
          }
          elseif($postmeta_row_data->key_name == '_order_currency' || $postmeta_row_data->key_name == '_customer_ip_address' || $postmeta_row_data->key_name == '_customer_user_agent' || $postmeta_row_data->key_name == '_order_shipping_cost' || $postmeta_row_data->key_name == '_order_shipping_method' || $postmeta_row_data->key_name == '_payment_method' || $postmeta_row_data->key_name == '_payment_method_title' || $postmeta_row_data->key_name == '_order_tax' || $postmeta_row_data->key_name == '_order_total' || $postmeta_row_data->key_name == '_order_notes' || $postmeta_row_data->key_name == '_order_status' || $postmeta_row_data->key_name == '_order_discount' || $postmeta_row_data->key_name == '_order_coupon_code' || $postmeta_row_data->key_name == '_is_order_coupon_applyed' || $postmeta_row_data->key_name == '_final_order_shipping_cost' || $postmeta_row_data->key_name == '_final_order_tax' || $postmeta_row_data->key_name == '_final_order_total' || $postmeta_row_data->key_name == '_final_order_discount'){
            $order_data_by_id[$postmeta_row_data->key_name] = $postmeta_row_data->key_value;
          }
        } 

        $order_data_by_id['_ordered_items']  = json_decode( $get_orders_items->order_data, TRUE );
        $order_data_by_id['_order_history']  = $this->order->getOrderDownloadHistory( $params ); 
		  }
      
		  $this->dashboardData['order_data_by_id']  =   $order_data_by_id;
		}
		
		if(Request::is('admin/user/profile')){
		  $get_user_data = User::where(['id' => Session::get('shopist_admin_user_id')])->first();
		  
		 
		  if($get_user_data->count() > 0)
		  {
			$this->dashboardData['user_profile_data']  = $get_user_data;
		  }
		}
		
		if(Request::is('admin/dashboard')){
		  $dashboard             = array();
		  $last2DaysData         = array();
		  $last2DaysProductsData = array();
		  $todaysTotal           = 0;
		  
      if(is_vendor_login() && Session::has('shopist_admin_user_id')){
        $totalProducts   =   Post::where(['post_author_id' => Session::get('shopist_admin_user_id'), 'post_type' => 'product', 'post_status' => 1])->get();
      }
      else{
        $totalProducts   =   Post::where(['post_type' => 'product', 'post_status' => 1])->get();
      }
		  
      if(is_vendor_login() && Session::has('shopist_admin_user_id')){
        $todayOrders  =  DB::table('posts')
                         ->whereDate('posts.created_at', '=', $this->carbonObject->today()->toDateString())
                         ->where(['posts.post_type' => 'shop_order', 'posts.parent_id' => 0, 'vendor_orders.vendor_id' => Session::get('shopist_admin_user_id')])
                         ->join('vendor_orders', 'posts.id', '=', 'vendor_orders.order_id')
                         ->select('posts.*') 
                         ->get();
      }
      else{
        $todayOrders  =  Post::whereDate('created_at', '=', $this->carbonObject->today()->toDateString())->where(['post_type' => 'shop_order', 'parent_id' => 0])->get();
      }
		  
      if(is_vendor_login() && Session::has('shopist_admin_user_id')){
        $totalOrders  =  DB::table('posts')
                         ->where(['posts.post_type' => 'shop_order', 'posts.parent_id' => 0, 'vendor_orders.vendor_id' => Session::get('shopist_admin_user_id')])
                         ->join('vendor_orders', 'posts.id', '=', 'vendor_orders.order_id')
                         ->select('posts.*') 
                         ->get();
      }
      else{
        $totalOrders  =  Post::where(['post_type' => 'shop_order', 'parent_id' => 0])->get();
      }
      
      if(is_vendor_login() && Session::has('shopist_admin_user_id')){
        $last2DaysOrders  =  DB::table('posts')
                             ->whereBetween('posts.created_at', array($this->carbonObject->yesterday()->toDateString(), $this->carbonObject->today()->toDateString().' 23:59:59')) 
                             ->where(['posts.post_type' => 'shop_order', 'posts.parent_id' => 0, 'vendor_orders.vendor_id' => Session::get('shopist_admin_user_id')])
                             ->join('vendor_orders', 'posts.id', '=', 'vendor_orders.order_id')
                             ->select('posts.*') 
                             ->orderBy('posts.created_at', 'DESC')
                             ->get();
      }
      else{
        $last2DaysOrders  =  Post::whereBetween('created_at', array($this->carbonObject->yesterday()->toDateString(), $this->carbonObject->today()->toDateString().' 23:59:59'))->where(['post_type' => 'shop_order', 'parent_id' => 0])->orderBy('created_at', 'DESC')->get();
      }
		  
      if(is_vendor_login() && Session::has('shopist_admin_user_id')){
        $lastProducts  =  DB::table('posts')
                          ->whereBetween('created_at', array($this->carbonObject->yesterday()->toDateString(), $this->carbonObject->today()->toDateString().' 23:59:59'))
                          ->where(['post_author_id' => Session::get('shopist_admin_user_id'), 'post_type' => 'product', 'parent_id' => 0, 'post_status' => 1])
                          ->orderBy('created_at', 'DESC')
                          ->take(5)
                          ->get();
      }
      else{
        $lastProducts  =  Post::whereBetween('created_at', array($this->carbonObject->yesterday()->toDateString(), $this->carbonObject->today()->toDateString().' 23:59:59'))->where(['post_type' => 'product', 'parent_id' => 0, 'post_status' => 1])->orderBy('created_at', 'DESC')->take(5)->get();
      }
		  
		 
		  if($totalProducts && $totalProducts->count() > 0){
        $dashboard['total_products'] = $totalProducts->count();
		  }
		  else{
        $dashboard['total_products'] = 0;
		  }
		  
		  if($todayOrders && $todayOrders->count() > 0){
        $dashboard['today_orders'] = $todayOrders->count();
        
        foreach($todayOrders as $rows){
          $get_order_total = PostExtra::where(['post_id' => $rows->id, 'key_name' => '_order_total'])->first();
           
          if(!empty($get_order_total->key_value)){
            $todaysTotal += $get_order_total->key_value;
          }
        }
        $dashboard['today_totals_sales'] = $todaysTotal;
		  }
		  else{
        $dashboard['today_orders'] = 0;
        $dashboard['today_totals_sales'] = $todaysTotal;
		  }
		  
		  if($totalOrders && $totalOrders->count() > 0){
        $dashboard['total_orders'] = $totalOrders->count();
		  }
		  else{
        $dashboard['total_orders'] = 0;
		  }
      
		  if($last2DaysOrders && $last2DaysOrders->count() > 0){
        foreach($last2DaysOrders as $rows){
          $order_data = array();
          $order_data['order_id']       =   $rows->id;
          $order_data['order_date']     =   $this->carbonObject->parse($rows->created_at)->toDayDateTimeString();

          $order_status                 =   PostExtra::where(['post_id' => $rows->id, 'key_name' => '_order_status'])->first();
          if(!empty($order_status->key_value)){
            $order_data['order_status'] =   $order_status->key_value;
          }

          $order_total                  =   PostExtra::where(['post_id' => $rows->id, 'key_name' => '_order_total'])->first();
          if(!empty($order_total->key_value)){
            $order_data['order_totals'] =   $order_total->key_value;
          }
          else{
            $order_data['order_totals'] =   0;
          }

          $order_currency               =   PostExtra::where(['post_id' => $rows->id, 'key_name' => '_order_currency'])->first();
          if(!empty($order_currency->key_value)){
            $order_data['order_currency'] =   $order_currency->key_value;
          }

          array_push($last2DaysData, $order_data);
        }

        $dashboard['latest_orders'] = $last2DaysData;
		  }
		  else{
			$dashboard['latest_orders'] = array();
		  }
      
		  if($lastProducts && $lastProducts->count() > 0){
        foreach($lastProducts as $rows){
          $products_data = array();
          
          $get_post_meta = PostExtra::where(['post_id' => $rows->id, 'key_name' => '_product_related_images_url'])->first();
          if(!empty($get_post_meta)){
            $parse_url = json_decode($get_post_meta->key_value);
          }
           
          $products_data['id'] = $rows->id;
          
          if(!empty($parse_url) && $parse_url->product_image && $parse_url->product_image != '/images/upload.png'){
            $products_data['img_url'] = $parse_url->product_image;
          }
          else{
            $products_data['img_url'] = default_placeholder_img_src();
          }

          $products_data['title'] = $rows->post_title;
          if(get_product_type($rows->id) == 'simple_product'){
            $products_data['price'] = get_current_currency_symbol().PostExtra::where(['post_id' => $rows->id, 'key_name' => '_product_price'])->first()->key_value;
          }
          elseif (get_product_type($rows->id) == 'configurable_product') {
            $products_data['price'] = get_product_variations_min_to_max_price_html(get_current_currency_symbol(), $rows->id);
          }
          elseif ((get_product_type($rows->id) == 'customizable_product') || (get_product_type($rows->id) == 'downloadable_product')) {
            if(count(get_product_variations($rows->id))>0){  
              $products_data['price'] = get_product_variations_min_to_max_price_html(get_current_currency_symbol(), $rows->id);
            }
            else{  
              $products_data['price'] = get_current_currency_symbol().PostExtra::where(['post_id' => $rows->id, 'key_name' => '_product_price'])->first()->key_value;
            }
          }
          else{
            $products_data['price'] = 0;
          }

          $products_data['description'] = $rows->post_content;

          array_push($last2DaysProductsData, $products_data);
        }
        
        $dashboard['latest_products'] = $last2DaysProductsData;
		  }
		  else{
        $dashboard['latest_products'] = array();
		  }
      
      $all_vendors			 =  $this->vendors->getAllVendors( false, null, -1);
      $active_vendors		 =  $this->vendors->getAllVendors( false, null, 1 );
      $pending_vendors   =  $this->vendors->getAllVendors( false, null, 0 );
      
      $dashboard['total_vendors']			= count($all_vendors);
      $dashboard['active_vendors']		= count($active_vendors);
      $dashboard['pending_vendors']   = count($pending_vendors);
      
      $dashboard['total_completed'] = count($this->vendors->getVendorWithdrawRequestDataAll('COMPLETED'));
      $dashboard['total_cancelled'] = count($this->vendors->getVendorWithdrawRequestDataAll('CANCELLED'));
      $dashboard['total_pending']   = count($this->vendors->getVendorWithdrawRequestDataAll('ON_HOLD'));

      
      $dashboard['dashbord_announcement']	 =  $this->vendors->getTopThreeVendorAnnouncementByVendorId( Session::get('shopist_admin_user_id') );
      
      //overview chart
      $start_date   =  date('Y-m-01');
      $current_date =  date('Y-m-d');
        
      $dashboard['overview_reports'] =  $this->vendors->getVendorReportsDataForDay(array('target' => 'day', 'start_date'=> $start_date, 'current_date' => $current_date));
      $dashboard['overview_reports_log'] =  $this->vendors->getVendorReportsDayLog(array('target' => 'day', 'start_date'=> $start_date, 'current_date' => $current_date));
      
      $order_total = 0;
      $total_earning = 0;
      if(count($dashboard['overview_reports_log']) > 0){
        foreach($dashboard['overview_reports_log'] as $report){
          $order_total += $report->order_total;
          $total_earning += ((float)$report->order_total - (float)$report->net_amount);
        }
      }
      
      $dashboard['overview_reports_total_details'] = array('number_of_order' => $dashboard['overview_reports_log']->count(), 'order_total' => price_html($order_total), 'total_earning' => price_html($total_earning));
      //overview chart end
        		
		  $this->dashboardData['dashboard_data'] = $dashboard;
		}
    
    
		
		if(Request::is('admin/reports/sales-by-product-title') || Request::is('admin/reports/sales-by-month') || Request::is('admin/reports/sales-by-last-7-days') || Request::is('admin/reports/sales-by-custom-days') || Request::is('admin/reports/sales-by-payment-method')){
		  $reports_data                    =  array();
		  $reports_data['report_details']  =  array();
		  $report_name                     =  '';
		  $reports_data['report_date']     =  array();
		  
		  $reports_data['report_currency_symbol'] = get_current_currency_symbol();
		  
		  if(Request::is('admin/reports/sales-by-product-title')){
			$productsTitleOrders    =   $this->classGetFunction->get_orders_by_date_range($this->carbonObject->today()->toDateString(), $this->carbonObject->today()->toDateString());
		  
			if($productsTitleOrders && $productsTitleOrders->count() > 0){
			  $reports_data['report_details']['gross_sales_by_product_title'] = $this->classGetFunction->get_reports_gross_products_title_data( $productsTitleOrders );
			}
			else{
			  $reports_data['report_details']['gross_sales_by_product_title'] = array();
			}
			
			$report_name = 'sales_by_product_title';
			$reports_data['report_date'] = $this->carbonObject->today()->toFormattedDateString().' - '.$this->carbonObject->today()->toFormattedDateString();
		  }
		  else if(Request::is('admin/reports/sales-by-month')){
        $yearOrders    =   $this->classGetFunction->get_orders_by_year();

        if($yearOrders && $yearOrders->count() > 0){
          $get_order_details = $this->classGetFunction->get_order_by_specific_date_range($yearOrders);
          $reports_data['report_details']['gross_sales_by_month'] =  $this->classGetFunction->get_report_data_order_details_by_month($get_order_details);
        }
        else{
          $reports_data['report_details']['gross_sales_by_month'] = array();
        }

        $report_name = 'sales_by_month';
        $reports_data['report_date'] = 'Jan 01, '.$this->carbonObject->today()->year.' - '.$this->carbonObject->today()->toFormattedDateString();
		  }
		  
		  else if(Request::is('admin/reports/sales-by-last-7-days')){
			$last7DaysOrders    =   $this->classGetFunction->get_orders_by_date_range($this->carbonObject->parse($this->carbonObject->today())->subWeeks(1)->toDateString(), $this->carbonObject->today()->toDateString());
			
			if($last7DaysOrders && $last7DaysOrders->count() >0){
			  $reports_data['report_details']['sales_order_by_last_7_days']['table_data']  = $this->classGetFunction->get_order_by_specific_date_range($last7DaysOrders);
			  $reports_data['report_details']['sales_order_by_last_7_days']['report_data'] = $this->classGetFunction->get_report_data_order_details($reports_data['report_details']['sales_order_by_last_7_days']['table_data']);
			}
			else{
			  $reports_data['report_details']['sales_order_by_last_7_days']['table_data'] = array();
			  $reports_data['report_details']['sales_order_by_last_7_days']['report_data'] = array();
			}
			
			$report_name = 'sales_by_last_7_days';
			$reports_data['report_date'] = $this->carbonObject->parse($this->carbonObject->today())->subWeeks(1)->toFormattedDateString().' - '.$this->carbonObject->today()->toFormattedDateString();
		  }
		  
		  else if(Request::is( 'admin/reports/sales-by-custom-days' )){
			$customDaysOrders    =   $this->classGetFunction->get_orders_by_date_range($this->carbonObject->today()->toDateString(), $this->carbonObject->today()->toDateString());
			
			if($customDaysOrders && $customDaysOrders->count() >0){
			  $reports_data['report_details']['sales_order_by_custom_days']['table_data']  = $this->classGetFunction->get_order_by_specific_date_range($customDaysOrders);
			  $reports_data['report_details']['sales_order_by_custom_days']['report_data'] = $this->classGetFunction->get_report_data_order_details($reports_data['report_details']['sales_order_by_custom_days']['table_data']);
			}
			else{
			  $reports_data['report_details']['sales_order_by_custom_days']['table_data'] = array();
			  $reports_data['report_details']['sales_order_by_custom_days']['report_data'] = array();
			}
			
			$report_name = 'sales_by_custom_days';
			$reports_data['report_date'] = $this->carbonObject->today()->toFormattedDateString().' - '.$this->carbonObject->today()->toFormattedDateString();
		  }
		  
		  else if(Request::is( 'admin/reports/sales-by-payment-method' )){
			$getOrders    =   $this->classGetFunction->get_orders_by_date_range($this->carbonObject->today()->toDateString(), $this->carbonObject->today()->toDateString());
			
			if($getOrders && $getOrders->count() > 0){
			  $reports_data['report_details']['gross_sales_by_payment_method'] = $this->classGetFunction->get_reports_payment_method_data( $getOrders );
			}
			else{
			  $reports_data['report_details']['gross_sales_by_payment_method'] = array();
			}
			
			$report_name = 'sales_by_payment_method';
			$reports_data['report_date'] = $this->carbonObject->today()->toFormattedDateString().' - '.$this->carbonObject->today()->toFormattedDateString();
		  }
		  
		  $reports_data['report_name'] = $report_name;
		  $this->dashboardData['report_data'] = $reports_data;
		}
		
		if( ( Request::is('admin/settings/languages') ) || ( Request::is('admin/settings/languages/update/*') && $params ) ){
		  $get_avaliable_lang = array();     
		  $get_avaliable_lang = get_available_languages_data();
		  
		  $this->dashboardData['lang_data'] = $get_avaliable_lang;
		  
		  if(Request::is('admin/settings/languages/update/*') && $params){
        $get_lang_by_id = array();     
        $get_lang_by_id = ManageLanguage::where(['id' => $params])->get()->toArray();

        $this->dashboardData['lang_data_by_id'] = array_shift($get_lang_by_id);
		  }
		}
		
		if(Request::is('admin/settings/appearance')){
      get_appearance_settings();
		  $templates_details        =   array(); 
		  $header_details           =   array();
		  $home_details             =   array();
		  $blog_details             =   array();
		  $product_details          =   array(); 
		  $single_product_details   =   array();
		  $selected_tab             =   '';
		  
		  $unserialize_appearance_data  =   current_appearance_settings();
				   
		  //header dir
		  $header_dir_list              =   \File::glob(base_path('resources/views/frontend-templates/header/*'), GLOB_ONLYDIR);
		  
		  if(count($header_dir_list) > 0){
        foreach($header_dir_list as $dir_name){
          array_push($header_details, basename($dir_name));
        }
		  }
      
		  //home dir
		  $home_dir_list              =   \File::glob(base_path('resources/views/frontend-templates/home/*'), GLOB_ONLYDIR);
		  
		  if(count($home_dir_list) > 0){
			foreach($home_dir_list as $dir_name){
			  array_push($home_details, basename($dir_name));
			}
		  }
		  
		  //blog dir
		  $blog_dir_list              =   \File::glob(base_path('resources/views/frontend-templates/blog/*'), GLOB_ONLYDIR);
		  
		  if(count($blog_dir_list) > 0){
			foreach($blog_dir_list as $dir_name){
			  array_push($blog_details, basename($dir_name));
			}
		  }
		  
		  //product dir
		  $product_dir_list           =   \File::glob(base_path('resources/views/frontend-templates/product/*'), GLOB_ONLYDIR);
		  
		  if(count($product_dir_list) > 0){
			foreach($product_dir_list as $dir_name){
			  array_push($product_details, basename($dir_name));
			}
		  }
		  
		  //single product dir
		  $single_product_dir_list    =   \File::glob(base_path('resources/views/frontend-templates/single-product/*'), GLOB_ONLYDIR);
		  
		  if(count($single_product_dir_list) > 0){
			foreach($single_product_dir_list as $dir_name){
			  array_push($single_product_details, basename($dir_name));
			}
		  }
		  
		  if(Session::has('appearance_active_tab_name')){
			$selected_tab = Session::get('appearance_active_tab_name');
			Session::forget('appearance_active_tab_name');
		  }
		 
		  $templates_details['header_details']            =   $header_details;
		  $templates_details['home_details']              =   $home_details;
		  $templates_details['blog_details']              =   $blog_details;
		  $templates_details['product_details']           =   $product_details;
		  $templates_details['single_product_details']    =   $single_product_details;
		  $templates_details['appearance_tab']            =   $unserialize_appearance_data;
		  $templates_details['current_tab']               =   $selected_tab;
      $templates_details['parent_cat']                =   get_product_parent_categories();
				 
		  $this->dashboardData['frontend_templates_details'] = $templates_details;
		}
    
    if(Request::is('admin/settings/emails/details/new-order')){
      $email_temp = array();
      $temp_dir_list    =   \File::glob(base_path('resources/views/emails/order-confirmation/*'), GLOB_ONLYDIR);
		  
		  if(count($temp_dir_list) > 0){
        foreach($temp_dir_list as $dir_name){
          array_push($email_temp, basename($dir_name));
        }
		  }
      
      $this->dashboardData['email_templates'] = $email_temp;
    }
		
		if(Request::is('admin/users/roles/update/*') && $params){  
		  $get_role_data = get_roles_details_by_role_id( $params );
		  
		  if(!empty($get_role_data)){
        $this->dashboardData['user_roles_details'] = $get_role_data;
		  }
		  else{
        return view('errors.no_data');
		  }
		}
		
		if(Request::is('admin/user/update/*') && $params){
		  $get_data = get_user_details( $params );
		  
		  if(count($get_data)>0){
        $this->dashboardData['user_edit_details'] = $get_data;
		  }
		  else{
        return view('errors.no_data');
		  }
		}
		
		if(Request::is('admin/users/list')){
		  $search_value = '';
      
      if(isset($_GET['term_user_name']) && $_GET['term_user_name'] != ''){
        $search_value = $_GET['term_user_name'];
      }
      
      $this->dashboardData['user_list_data'] = $this->user->getUserListData(true, $search_value, -1);
      $this->dashboardData['search_value']   = $search_value;
		}
		
		if(Request::is('admin/users/roles/list')){
      $search_value = '';
      
      if(isset($_GET['term_user_role']) && $_GET['term_user_role'] != ''){
        $search_value = $_GET['term_user_role'];
      }
		  $this->dashboardData['user_role_list_data'] = $this->user->getUserRoleListData(true, $search_value);
      $this->dashboardData['search_value']   = $search_value;
		}
    
    if(Request::is('admin/settings/general') || Request::is('admin/coupon-manager/coupon/add') || Request::is('admin/coupon-manager/coupon/update/*')){
      $this->dashboardData['user_role_list_data'] = get_available_user_roles();
    }
		
		if($params && Request::is('admin/testimonial/update/*')){
		  $get_post_details = get_testimonial_data_by_slug( $params );
      
		  if(count($get_post_details) > 0){
			 $this->dashboardData['testimonial_update_data'] = $get_post_details;
		  }
		  else{
        return view('errors.no_data');
		  }
		}
		
		if(Request::is('admin/testimonial/list')){
      $search_value = '';
      
      if(isset($_GET['term_testimonial']) && $_GET['term_testimonial'] != ''){
        $search_value = $_GET['term_testimonial'];
      }
      
		  $this->dashboardData['testimonial_list_data']   =   $this->CMS->get_testimonials( true, $search_value, -1 );
      $this->dashboardData['search_value']            =   $search_value;
		}
		
		if($params && Request::is('admin/coupon-manager/coupon/update/*')){
			$get_coupon_data_by_slug = $this->features->getCouponBySlug($params);
      
      if (count($get_coupon_data_by_slug) > 0) {
        $this->dashboardData['coupon_update_data'] = $get_coupon_data_by_slug;
      }
      else{
        return view('errors.no_data');
      }
		}
    
    if(Request::is('admin/coupon-manager/coupon/list')){
			$search_value = '';
      
      if(isset($_GET['term_coupon']) && $_GET['term_coupon'] != ''){
        $search_value = $_GET['term_coupon'];
      }		
		  $this->dashboardData['coupon_all_data'] =   $this->features->getCouponListData(true, $search_value, -1);
      $this->dashboardData['search_value']    =   $search_value;
		}
		
		if(Request::is('admin/manage/seo')){
      if(is_vendor_login()){
        $this->dashboardData['seo_data'] = $this->login_user_details->seo;
      }
      else{
        $this->dashboardData['seo_data'] = get_seo_data();
      }
		}
    
    if(Request::is('admin/customer/request-product')){
      if(is_vendor_login() && Session::has('shopist_admin_user_id')){
        $get_request_data = DB::table('request_products')
                            ->where(['posts.post_author_id' => Session::get('shopist_admin_user_id')])
                            ->join('posts', 'request_products.product_id', '=', 'posts.id')
                            ->select('request_products.*', 'posts.post_title', 'posts.post_slug')        
                            ->get()
                            ->toArray();
      }
      else{
        $get_request_data = DB::table('request_products')
                            ->join('posts', 'request_products.product_id', '=', 'posts.id')
                            ->select('request_products.*', 'posts.post_title', 'posts.post_slug')        
                            ->get()
                            ->toArray();
      }
      
		  $this->dashboardData['request_product_data'] = $get_request_data;
		}
		
    if(Request::is('admin/subscription/custom')){
		  $this->dashboardData['custom_subscriber_data'] = Subscription::get()->toArray();
		}
    
		if(Request::is('admin/subscription/mailchimp')){
		  $this->dashboardData['subscription_data'] = get_subscription_data();
		}
    
    if(Request::is('admin/subscription/settings')){
		  $this->dashboardData['subscription_settings_data'] = get_subscription_settings_data();
		}
		
		if(Request::is('admin/extra-features/product-compare-fields') || Request::is('admin/product/add') || Request::is('admin/product/update/*')){
      $this->dashboardData['fields_name'] = $this->option->getProductCompareData();
		}
    
    if(Request::is('admin/vendors/list')){
      $search_value = '';
      
      if(isset($_GET['term_vendors']) && $_GET['term_vendors'] != ''){
        $search_value = $_GET['term_vendors'];
      }
      
      $this->dashboardData['vendors_list_data']   =   $this->vendors->getAllVendors( true, $search_value, -1 );
      $this->dashboardData['search_value']        =   $search_value;
      
      $this->dashboardData['vendor_all']      = "class=active";
      $this->dashboardData['vendor_active']   = '';
      $this->dashboardData['vendor_pending']  = '';
    }
    
    if(Request::is('admin/vendors/list/*')){
      $search_value = '';
      $this->dashboardData['vendor_all']      = '';
      $this->dashboardData['vendor_active']   = '';
      $this->dashboardData['vendor_pending']  = '';
      
      if(isset($_GET['term_vendors']) && $_GET['term_vendors'] != ''){
        $search_value = $_GET['term_vendors'];
      }
      $this->dashboardData['search_value']  =   $search_value;
      
      if(Request::is('admin/vendors/list/active')){
        $this->dashboardData['vendor_active']      =  "class=active";
        $this->dashboardData['vendors_list_data']  =  $this->vendors->getAllVendors( true, $search_value, 1);
      }
      elseif(Request::is('admin/vendors/list/pending')){
        $this->dashboardData['vendor_pending']     =  "class=active";
        $this->dashboardData['vendors_list_data']  =  $this->vendors->getAllVendors( true, $search_value, 0);
      } 
    }
    
    if(Request::is('admin/vendor/settings')){
      $this->dashboardData['vendors_settings'] = null;
      
      if(is_vendor_login()){
        $vendor_details   = get_current_vendor_user_info();
        $get_user_details = json_decode(get_user_account_details_by_user_id( $vendor_details['user_id'] )[0]['details']);
        
        if(!empty($get_user_details)){
          if(!Session::has('update-target')){
            Session::flash('update-target', 'general');
            $this->dashboardData['update_target']   =  'general';
          }
          else{
            $this->dashboardData['update_target']   =  Session::get('update-target');
          }
        
          $this->dashboardData['vendors_settings'] = $get_user_details;
          $this->dashboardData['user_details']     = (object)$vendor_details;
        }
      }
    }
    
    if(Request::is('admin/vendors/package/list') || Request::is('admin/vendors/manage/packages')){
      $this->dashboardData['vendors_packages'] = array();
      $this->dashboardData['selected_package'] = '';
      $get_vendor_packages = VendorPackage::get()->toArray();
      
      if(count($get_vendor_packages) > 0){
        $this->dashboardData['vendors_packages'] = $get_vendor_packages;
        
        if(is_vendor_login()){
          $vendor_details   = get_current_vendor_user_info();
          $get_user_details = json_decode(get_user_account_details_by_user_id( $vendor_details['user_id'] )[0]['details']);
          
          if(!empty($get_user_details->package->package_name)){
            $this->dashboardData['selected_package'] = $get_user_details->package->package_name;
          }
        }
      }
    }
    
    if(Request::is('admin/vendors/withdraw')){
      if(is_vendor_login()){
        if(!Session::has('withdraw_request_update_target')){
          Session::flash('withdraw_request_update_target', 'default_withdrawal_account_label');
          $this->dashboardData['withdraw_update_target']   =  'default_withdrawal_account_label';
        }
        else{
          $this->dashboardData['withdraw_update_target']   =  Session::get('withdraw_request_update_target');
        }

        $this->dashboardData['withdraw_request_data']   =  $this->vendors->getVendorCurrentWithdrawRequest( Session::get('shopist_admin_user_id') );
        if(count($this->dashboardData['withdraw_request_data']) > 0){
          if(isset($this->dashboardData['withdraw_request_data']['payment_type']) && $this->dashboardData['withdraw_request_data']['payment_type'] == 'single_payment_with_custom_values'){
            $this->dashboardData['withdraw_request_data']['selected_payment_type'] = 'Single payment with custom values';
          }
          elseif(isset($this->dashboardData['withdraw_request_data']['payment_type']) && $this->dashboardData['withdraw_request_data']['payment_type'] == 'single_payment_with_all_earnings'){
            $this->dashboardData['withdraw_request_data']['selected_payment_type'] = 'Single payment with all earnings';
          }
          
          if(isset($this->dashboardData['withdraw_request_data']['payment_method']) && $this->dashboardData['withdraw_request_data']['payment_method'] == 'dbt'){
            $this->dashboardData['withdraw_request_data']['selected_payment_method'] = 'Direct bank transfer';
          }
          elseif(isset($this->dashboardData['withdraw_request_data']['payment_method']) && $this->dashboardData['withdraw_request_data']['payment_method'] == 'cod'){
            $this->dashboardData['withdraw_request_data']['selected_payment_method'] = 'Cash on delivery';
          }
          elseif(isset($this->dashboardData['withdraw_request_data']['payment_method']) && $this->dashboardData['withdraw_request_data']['payment_method'] == 'paypal'){
            $this->dashboardData['withdraw_request_data']['selected_payment_method'] = 'Paypal';
          }
          elseif(isset($this->dashboardData['withdraw_request_data']['payment_method']) && $this->dashboardData['withdraw_request_data']['payment_method'] == 'stripe'){
            $this->dashboardData['withdraw_request_data']['selected_payment_method'] = 'Stripe';
          }
        }
        
        $this->dashboardData['vendor_packages_data']  =  get_package_details_by_vendor_id( Session::get('shopist_admin_user_id') );
        $this->dashboardData['withdraw_total']        =  $this->vendors->getVendorWithdrawTotal( Session::get('shopist_admin_user_id') );
        $this->dashboardData['withdraw_history_data'] =  $this->vendors->getWithdrawHistoryData( Session::get('shopist_admin_user_id'), 'all' );
      }
      else{
        $this->dashboardData['withdraw_request_data_all']   =  $this->vendors->getVendorWithdrawRequestDataAll('all');
        $this->dashboardData['total_row'] = count($this->dashboardData['withdraw_request_data_all']);
        $this->dashboardData['total_completed'] = count($this->vendors->getVendorWithdrawRequestDataAll('COMPLETED'));
        $this->dashboardData['total_cancelled'] = count($this->vendors->getVendorWithdrawRequestDataAll('CANCELLED'));
        $this->dashboardData['total_pending'] = count($this->vendors->getVendorWithdrawRequestDataAll('ON_HOLD'));
      }
      
      $this->dashboardData['is_all']       = "class=active";
      $this->dashboardData['is_pending']   = '';
      $this->dashboardData['is_completed'] = '';
      $this->dashboardData['is_cancelled'] = '';
    }
    
    if($params && Request::is('admin/vendors/withdraw/*')){
      $this->dashboardData['withdraw_request_data_by_update_id'] = $this->vendors->getVendorCurrentWithdrawRequestById( $params );
    }
    
    if($params && Request::is('admin/status/vendors/withdraw/*')){
      $is_all = '';
      $is_pending = '';
      $is_completed = '';
      $is_cancelled = '';
      $status = 'all';
      
      if(isset($params) && $params == 'pending'){
        $is_pending = "class=active";
        $status = 'ON_HOLD';
      }
      elseif(isset($params) && $params == 'completed'){
        $is_completed = "class=active";
        $status = 'COMPLETED';
      }
      else{
        $is_cancelled = "class=active";
        $status = 'CANCELLED';
      }
      
      $this->dashboardData['is_all']       = $is_all;
      $this->dashboardData['is_pending']   = $is_pending;
      $this->dashboardData['is_completed'] = $is_completed;
      $this->dashboardData['is_cancelled'] = $is_cancelled;
      
      $this->dashboardData['withdraw_request_data_all']   =  $this->vendors->getVendorWithdrawRequestDataAll( $status );
      $this->dashboardData['total_row'] = count($this->vendors->getVendorWithdrawRequestDataAll('all'));
      $this->dashboardData['total_completed'] = count($this->vendors->getVendorWithdrawRequestDataAll('COMPLETED'));
      $this->dashboardData['total_cancelled'] = count($this->vendors->getVendorWithdrawRequestDataAll('CANCELLED'));
      $this->dashboardData['total_pending'] = count($this->vendors->getVendorWithdrawRequestDataAll('ON_HOLD'));
    }
    
    if(is_vendor_login()){
      $this->dashboardData['product_parm'] = Session::get('shopist_admin_user_id');
    }
    else{
      $this->dashboardData['product_parm'] = 'all';
    }
    
    if($params && Request::is('admin/vendors/package/update/*')){
      $get_vendor_package = VendorPackage::where(['id' => $params])->first();
      
      if(!empty($get_vendor_package)){
        $this->dashboardData['package_update_data']['package_type'] = $get_vendor_package->package_type;
        $this->dashboardData['package_update_data']['options'] = json_decode($get_vendor_package->options);
      }
      else{
        return view('errors.no_data');
      }
    }
    
    if(Request::is('admin/vendors/reviews')){
      $this->dashboardData['vendor_reviews'] =  $this->vendors->getVendorReviews( Session::get('shopist_admin_user_id') );
    }
    
    if($params && Request::is('admin/vendors/announcement/*')){
      $this->dashboardData['vendor_announcement'] =  $this->vendors->getVendorAnnouncement( $params  );
    }
    
    if(Request::is('admin/vendors/announcement/list')){
      $search_value = '';
      
      if(isset($_GET['term_announcement']) && $_GET['term_announcement'] != ''){
        $search_value = $_GET['term_announcement'];
      }
      
		  $this->dashboardData['announcement_list_data']  =   $this->vendors->getVendorAnnouncementList( true, $search_value, -1 );
      $this->dashboardData['search_value']            =   $search_value;
    }
    
    if(Request::is('admin/vendor/notice-board')){
      $this->dashboardData['vendor_all_notice'] =  $this->vendors->getVendorAnnouncementByVendorId( Session::get('shopist_admin_user_id') );
    }
    
    if($params && Request::is('admin/vendor/notice-board/single/details/*')){
      $this->dashboardData['vendor_single_details'] =  $this->vendors->getVendorAnnouncement( $params );
    }
    
    if(Request::is('admin/vendors/earning-reports') || Request::is('admin/vendors/earning-reports/by-day')){
      $start_date   =  date('Y-m-01');
      $current_date =  date('Y-m-d');
        
      if(isset($_GET['date_from']) && isset($_GET['date_to'])){
        $start_date   =  $_GET['date_from'];
        $current_date =  $_GET['date_to'];
      }
      
      $this->dashboardData['vendor_reports'] =  $this->vendors->getVendorReportsDataForDay(array('target' => 'day', 'start_date'=> $start_date, 'current_date' => $current_date));
      $this->dashboardData['vendor_reports_log'] =  $this->vendors->getVendorReportsDayLog(array('target' => 'day', 'start_date'=> $start_date, 'current_date' => $current_date));
      
      $order_total = 0;
      $total_earning = 0;
      if(count($this->dashboardData['vendor_reports_log']) > 0){
        foreach($this->dashboardData['vendor_reports_log'] as $report){
          $order_total += $report->order_total;
          $total_earning += ((float)$report->order_total - (float)$report->net_amount);
        }
      }
      
      $this->dashboardData['vendor_reports_total_details'] = array('number_of_order' => $this->dashboardData['vendor_reports_log']->count(), 'order_total' => $order_total, 'total_earning' => $total_earning);
      
      $this->dashboardData['tab_activation'] = array('day' => 'class="active"', 'year' => '', 'vendor' => ''); 
      $this->dashboardData['action_url'] = Request::url();
      $this->dashboardData['start_date']     = $start_date;
      $this->dashboardData['current_date']   = $current_date;
    }
    
    if(Request::is('admin/vendors/earning-reports/by-year')){
      $current_year = date("Y");
      
      if(isset($_GET['filter_year'])){
        $current_year = $_GET['filter_year'];
      }
        
      $this->dashboardData['tab_activation'] = array('day' => '', 'year' => 'class="active"', 'vendor' => ''); 
      $this->dashboardData['action_url'] = Request::url();
      $this->dashboardData['year'] = array(2013, 2014, 2015, 2016, 2017, 2018, 2019, 2020, 2021, 2022, 2023, 2024, 2025, 2026, 2027, 2028, 2029, 2030);
      $this->dashboardData['filter_year']  = $current_year;
      
      $this->dashboardData['vendor_reports'] =  $this->vendors->getVendorReportsDataForYear(array('target' => 'year', 'requested_year' => $current_year)); 
      $this->dashboardData['vendor_reports_log'] =  $this->vendors->getVendorReportsYearLog(array('target' => 'year', 'year' => $current_year));
      $order_total = 0;
      $total_earning = 0;
      if(count($this->dashboardData['vendor_reports_log']) > 0){
        foreach($this->dashboardData['vendor_reports_log'] as $report){
          $order_total += $report->order_total;
          $total_earning += ((float)$report->order_total - (float)$report->net_amount);
        }
      }
      
      $this->dashboardData['vendor_reports_total_details'] = array('number_of_order' => $this->dashboardData['vendor_reports_log']->count(), 'order_total' => $order_total, 'total_earning' => $total_earning);
    }
    
    if(Request::is('admin/vendors/earning-reports/by-vendor')){
      $start_date   =  date('Y-m-01');
      $current_date =  date('Y-m-d');
      $vendor_name  =  '';
      $vendor_id  =  null;
      
      if(isset($_GET['date_from']) && isset($_GET['date_to'])){
        $start_date   =  $_GET['date_from'];
        $current_date =  $_GET['date_to'];
      }
      
      if(isset($_GET['vendor_name'])){
        $vendor_name  =  $_GET['vendor_name'];
        $get_user = User::where(['name' => $vendor_name])->first();
        if(!empty($get_user)){
          $vendor_id = $get_user->id;
        }
      }
      
      $this->dashboardData['tab_activation'] = array('day' => '', 'year' => '', 'vendor' => 'class="active"'); 
      $this->dashboardData['action_url'] = Request::url();
      $this->dashboardData['all_vendor'] = $this->vendors->getAllVendors(false, null, -1);
      
      $this->dashboardData['start_date']     = $start_date;
      $this->dashboardData['current_date']   = $current_date;
      $this->dashboardData['vendor_name']    = $vendor_name;  
      
       $this->dashboardData['vendor_reports'] =  $this->vendors->getVendorReportsDataForVendor(array('target' => 'vendor', 'requested_start_date' => $start_date, 'requested_end_date' => $current_date, 'requested_vendor_id' => $vendor_id ));
       $this->dashboardData['vendor_reports_log'] =  $this->vendors->getVendorReportsVendorLog(array('target' => 'vendor', 'start_date'=> $start_date, 'current_date' => $current_date, 'vendor_id' => $vendor_id));
      
      $order_total = 0;
      $total_earning = 0;
      if(count($this->dashboardData['vendor_reports_log']) > 0){
        foreach($this->dashboardData['vendor_reports_log'] as $report){
          $order_total += $report->order_total;
          $total_earning += ((float)$report->order_total - (float)$report->net_amount);
        }
      }
      
      $this->dashboardData['vendor_reports_total_details'] = array('number_of_order' => $this->dashboardData['vendor_reports_log']->count(), 'order_total' => $order_total, 'total_earning' => $total_earning);
    }
    
    if(Request::is('admin/vendors/settings')){
      $this->dashboardData['vendor_settings_data'] = $this->option->getVendorSettingsData();
    }
				
    if(Request::is('admin/settings/emails/details/*')){
      $this->dashboardData['emails_notification_data'] = $this->option->getEmailsNotificationsData();
    }
    
    if($params && Request::is('admin/designer/shape/update/*')){
      $get_post  =  Post :: where('post_slug', $params)->first();
      
      if(!empty($get_post)){
        $this->dashboardData['designer_shape_data'] = $get_post;
      }
      else{
        return view('errors.no_data');
      }
    }
    
    if(Request::is('admin/designer/shape/list')){
      $search_value = '';
      
      if(isset($_GET['term_shape']) && $_GET['term_shape'] != ''){
        $search_value = $_GET['term_shape'];
      }
      
		  $this->dashboardData['designer_shape_list'] = $this->product->getShapeList(true, $search_value, -1);
      $this->dashboardData['search_value'] =   $search_value;
    }
    
    if($params && Request::is('admin/designer/font/update/*')){
      $get_post  =  Post :: where('post_slug', $params)->first();
      
      if(!empty($get_post)){
        $get_post_extra = PostExtra :: where('post_id', $get_post['id'])->first();
        
        if(!empty($get_post_extra)){
          $parse_file_name = explode('uploads/', $get_post_extra['key_value']);
          
          if(count($parse_file_name) > 0){
            $get_post->font_url = $parse_file_name[1];
          }
        }
        
        $this->dashboardData['designer_font_data'] = $get_post;
      }
      else{
        return view('errors.no_data');
      }
    }
    
    if(Request::is('admin/designer/fonts/list')){
      $search_value = '';
      
      if(isset($_GET['term_font']) && $_GET['term_font'] != ''){
        $search_value = $_GET['term_font'];
      }
      
		  $this->dashboardData['designer_font_list'] = $this->product->getFontsList(true, $search_value, -1);
      $this->dashboardData['search_value'] =   $search_value;
    }
    
		$this->dashboardData['current_path'] = Request::path();
    
		//attached admin Js Localization in dashboard content
		$this->dashboardData['admin_js_localization'] = json_encode( $this->adminJsLocalization );
		return view('pages.admin.dashboard', $this->dashboardData);
	}
  }
 
  /**
   * 
   * Redirect to order invoice
   *
   * @param null
   * @return void
   */
  public function redirectOrderInvoice( $params ){
    $order_id = 0;
    $get_post = Post::where(['id' => $params, 'post_type' => 'shop_order'])->first();
    

    if(!empty($get_post) && $get_post->parent_id > 0){
      $order_id = $get_post->parent_id;
    }
    else{
      $order_id = $params;
    }
    
    $get_post_by_order_id     = Post::where(['id' => $params])->first();
    $get_postmeta_by_order_id = PostExtra::where(['post_id' => $order_id])->get();
    $get_orders_items         = OrdersItem::where(['order_id' => $params])->first();

    if($get_post_by_order_id->count() > 0 && $get_postmeta_by_order_id ->count() > 0 && $get_orders_items->count() >0){
      $order_date_format = new Carbon( $get_post_by_order_id->created_at);

      $order_data_by_id = get_customer_order_billing_shipping_info( $order_id );
      $order_data_by_id['_order_id']    = $get_post_by_order_id->id;
      $order_data_by_id['_order_date']  = $order_date_format->toDayDateTimeString();

      foreach($get_postmeta_by_order_id as $postmeta_row_data){
        if($postmeta_row_data->key_name === '_order_shipping_method'){
          $order_data_by_id[$postmeta_row_data->key_name] = $this->classCommonFunction->get_shipping_label($postmeta_row_data->key_value);
        }
        elseif($postmeta_row_data->key_name == '_customer_user'){
          $user_data = unserialize($postmeta_row_data->key_value);
          if($user_data['user_mode'] == 'guest'){
            $order_data_by_id['_member']  = array('name' => 'Guest', 'url' => '');
          }
          elseif($user_data['user_mode'] == 'login'){
            $user_details_by_id = get_user_details($user_data['user_id']);
            $order_data_by_id['_member']  = array('name' => $user_details_by_id['user_display_name'], 'url' => $user_details_by_id['user_photo_url']);
          }
        }
        elseif($postmeta_row_data->key_name == '_order_currency' || $postmeta_row_data->key_name == '_customer_ip_address' || $postmeta_row_data->key_name == '_customer_user_agent' || $postmeta_row_data->key_name == '_order_shipping_cost' || $postmeta_row_data->key_name == '_order_shipping_method' || $postmeta_row_data->key_name == '_payment_method' || $postmeta_row_data->key_name == '_payment_method_title' || $postmeta_row_data->key_name == '_order_tax' || $postmeta_row_data->key_name == '_order_total' || $postmeta_row_data->key_name == '_order_notes' || $postmeta_row_data->key_name == '_order_status' || $postmeta_row_data->key_name == '_order_discount' || $postmeta_row_data->key_name == '_order_coupon_code' || $postmeta_row_data->key_name == '_is_order_coupon_applyed' || $postmeta_row_data->key_name == '_final_order_shipping_cost' || $postmeta_row_data->key_name == '_final_order_tax' || $postmeta_row_data->key_name == '_final_order_total' || $postmeta_row_data->key_name == '_final_order_discount'){
          $order_data_by_id[$postmeta_row_data->key_name] = $postmeta_row_data->key_value;
        }
      } 

      $order_data_by_id['_ordered_items']  = json_decode( $get_orders_items->order_data, TRUE );
    }
    
    return view('pages.admin.invoice.invoice', array('order_data_by_id' => $order_data_by_id));
  }

  /**
   * 
   * Save products attributes
   *
   * @param null
   * @return void
   */
  public function saveProductAttribute()
  {
    if( Request::isMethod('post') && Session::token() == Input::get('_token')){
      $data = Input::all();
      $rules = [
               'attrName'               => 'required',
               'attrValues'             => 'required'
               ];
        
      $validator = Validator:: make($data, $rules);
      
      if($validator->fails()){
        return redirect()-> back()
        ->withInput()
        ->withErrors( $validator );
      }
      else{
        $attr =       new AttributesList;
        
        $attr->attr_name    = Input::get('attrName');
        $attr->attr_values  = Input::get('attrValues');
        $attr->attr_status  = Input::get('attr_status');
        
        if($attr->save()){
          Session::flash('success-message', Lang::get('admin.successfully_saved_msg'));
          return redirect()->route('admin.update_attr_content', $attr->id);
        }
      }
    }
    else
    {
      return redirect()-> back();
    }
  }
  
  /**
   * 
   * Update products attributes
   *
   * @param id
   * @return void
   */
  public function updateAttrDetails($id)
  {
    if( Request::isMethod('post') && Session::token() == Input::get('_token') ){
      $data = Input::all();
      
      $rules = [
               'attrName'               => 'required',
               'attrValues'             => 'required'
               ];
        
      $validator = Validator:: make($data, $rules);
      
      if($validator->fails()){
        return redirect()-> back()
        ->withInput()
        ->withErrors( $validator );
      }
      else{
        $attr =       new AttributesList;
        
        $data = array(
                      'attr_name'        =>    Input::get('attrName'),
                      'attr_values'      =>    Input::get('attrValues'),
                      'attr_status'      =>    Input::get('attr_status')
        );
        
        if( $attr::where('attr_id', $id)->update($data)){
          Session::flash('success-message', Lang::get('admin.successfully_updated_msg'));
          return redirect()->route('admin.update_attr_content', $id);
        }
      }
    }
    else 
    {
      return redirect()-> back();
    }
  }
  
  /**
   * 
   * Update art data
   *
   * @param id
   * @return void
   */
  public function updateArtData($id)
  {
    if( Request::isMethod('post') && Session::token() == Input::get('_token') ){
      $data  = Input::all();
      
      $rules = [
                'inputSelectCategory'               => 'required'
               ];
        
      $validator = Validator:: make($data, $rules);
      
      if($validator->fails()){
        return redirect()-> back()
        ->withInput()
        ->withErrors( $validator );
      }
      else{
        $imgUrlArray = json_decode(Input::get('ht_art_all_uploaded_images'));
        
        if(count($imgUrlArray)>0){
          DB::table('art_lists')
            ->where('id', $id)
            ->update(['art_img_url'  =>  $imgUrlArray[0]->url, 'art_cat_id'   =>  Input::get('inputSelectCategory'), 'art_status'   =>  Input::get('inputArtStatus')]);
          
          if($id){
            Session::flash('success-message', Lang::get('admin.successfully_updated_msg'));
            return redirect()->route('admin.update_clipart_content', $id);
          }
        }
        else{
          $art =       new ArtList;
        
          $data = array(
                        'art_img_url'        =>    '',
                        'art_cat_id'         =>    Input::get('inputSelectCategory'),
                        'art_status'         =>    Input::get('inputArtStatus')
          );

          if( $art::where('id', $id)->update($data)){
            Session::flash('success-message', Lang::get('admin.successfully_updated_msg'));
            return redirect()->route('admin.update_clipart_content', $id);
          }
        }
      }
    }
    else 
    {
      return redirect()-> back();
    }
  }

  /**
   * 
   * Save shipping method
   *
   * @param null
   * @return void
   */
  public function saveShippingMethod()
  {
    if( Request::isMethod('post') && Session::token() == Input::get('_token') ){
      $get_return_shipping_data = array();
           
      if(Input::get('_shipping_method_name') == 'save_options'){
        $enable_shipping = (Input::has('inputEnableShipping')) ? true : false;
        $display_mode    = Input::get('inputDisplayMode');

        $get_return_shipping_data = $this->create_shipping_array_data('shipping_option', $enable_shipping, $display_mode, '', '');
      }
      elseif (Input::get('_shipping_method_name') == 'save_flat_rate') {
        $enable_method   = (Input::has('inputEnableFlatRate')) ? true : false;
        $method_title    = Input::get('inputFlatRateTitle');
        $method_cost     = Input::get('inputFlatRateCost');

        $get_return_shipping_data = $this->create_shipping_array_data('shipping_method_flat_rate', $enable_method, $method_title, $method_cost, '');
      }
      elseif (Input::get('_shipping_method_name') == 'save_free_shipping') {
        $enable_method   = (Input::has('inputEnableFreeShipping')) ? true : false;
        $method_title    = Input::get('inputFreeShippingTitle');
        $method_amount   = Input::get('inputFreeShippingOrderAmount');

        $get_return_shipping_data = $this->create_shipping_array_data('shipping_method_free_shipping', $enable_method, $method_title, $method_amount, '');
      }
      elseif (Input::get('_shipping_method_name') == 'save_local_delivery') {
        $enable_method   = (Input::has('inputEnableLocalDelivery')) ? true : false;
        $method_title    = Input::get('inputLocalDeliveryTitle');
        $fee_type        = Input::get('inputLocalDeliveryFeeType');
        $delivery_fee    = Input::get('inputLocalDeliveryDeliveryFee');

        $get_return_shipping_data = $this->create_shipping_array_data('shipping_method_local_delivery', $enable_method, $method_title, $fee_type, $delivery_fee);
      }
      
      $data = array(
                      'option_value'        => serialize($get_return_shipping_data)
      );
      
      if( Option::where('option_name', '_shipping_method_data')->update($data)){
        Session::flash('success-message', Lang::get('admin.successfully_updated_msg'));
        return redirect()->back();
      }
    }
    else 
    {
      return redirect()-> back();
    }
  }
  
  

  /**
   * 
   * Shipping data process
   *
   * @param shipping data
   * @return array
   */
  public function create_shipping_array_data($source, $parm1, $parm2, $parm3, $parm4)
  {
    $enable_shipping_option         =   '';
    $shipping_option_display_mode   =   '';
    $flat_rate_enable               =   '';
    $flat_rate_title                =   '';
    $flat_rate_cost                 =   '';
    $free_shipping_enable           =   '';
    $free_shipping_title            =   '';
    $free_shipping_order_amount     =   '';
    $local_delivery_enable          =   '';
    $local_delivery_title           =   '';
    $local_delivery_fee_type        =   '';
    $local_delivery_fee             =   '';
    
    $get_option = Option :: where('option_name', '_shipping_method_data')->first();
    $unserialize_data = unserialize($get_option->	option_value);
    
    if($source == 'shipping_option'){
      $enable_shipping_option         =   $parm1;
      $shipping_option_display_mode   =   $parm2;
    }
    else {
      $enable_shipping_option         =   $unserialize_data['shipping_option']['enable_shipping'];
      $shipping_option_display_mode   =   $unserialize_data['shipping_option']['display_mode'];
    }
    
    if($source == 'shipping_method_flat_rate'){
      $flat_rate_enable               =   $parm1;
      $flat_rate_title                =   $parm2;
      $flat_rate_cost                 =   $parm3;
    }
    else{
      $flat_rate_enable               =   $unserialize_data['flat_rate']['enable_option'];
      $flat_rate_title                =   $unserialize_data['flat_rate']['method_title'];
      $flat_rate_cost                 =   $unserialize_data['flat_rate']['method_cost'];
    }

    if($source == 'shipping_method_free_shipping'){
      $free_shipping_enable           =   $parm1;
      $free_shipping_title            =   $parm2;
      $free_shipping_order_amount     =   $parm3;
    }
    else{
      $free_shipping_enable           =   $unserialize_data['free_shipping']['enable_option'];
      $free_shipping_title            =   $unserialize_data['free_shipping']['method_title'];
      $free_shipping_order_amount     =   $unserialize_data['free_shipping']['order_amount'];
    }
    
    if($source == 'shipping_method_local_delivery'){
      $local_delivery_enable          =   $parm1;
      $local_delivery_title           =   $parm2;
      $local_delivery_fee_type        =   $parm3;
      $local_delivery_fee             =   $parm4;
    }
    else {
      $local_delivery_enable          =   $unserialize_data['local_delivery']['enable_option'];
      $local_delivery_title           =   $unserialize_data['local_delivery']['method_title'];
      $local_delivery_fee_type        =   $unserialize_data['local_delivery']['fee_type'];
      $local_delivery_fee             =   $unserialize_data['local_delivery']['delivery_fee'];
    }
    
    $shipping_method_array = array( 
        'shipping_option'  => array('enable_shipping' => $enable_shipping_option, 'display_mode' => $shipping_option_display_mode),
        'flat_rate'        => array('enable_option' => $flat_rate_enable, 'method_title' => $flat_rate_title, 'method_cost' => $flat_rate_cost),
        'free_shipping'    => array('enable_option' => $free_shipping_enable, 'method_title' => $free_shipping_title, 'order_amount' => $free_shipping_order_amount),
        'local_delivery'   => array('enable_option' => $local_delivery_enable, 'method_title' => $local_delivery_title, 'fee_type' => $local_delivery_fee_type, 'delivery_fee' => $local_delivery_fee)
    );
    
    return $shipping_method_array;
  }
  
  
  /**
   * 
   * Update order status
   *
   * @param order id
   * @return void
   */
  public function updateOrderStatus($order_id)
  {
    if( Request::isMethod('post') && Session::token() == Input::get('_token')){
      $environment = App::environment();
      $email_options = get_emails_option_data();
      
      $data = array(
                    'key_value' => Input::get('change_order_status')
      );
      
      if( PostExtra::where(['post_id' => $order_id, 'key_name' => '_order_status'])->update( $data )){
        $get_email = get_customer_order_billing_shipping_info( $order_id );
        
        if($environment === 'production' && $email_options['cancelled_order']['enable_disable'] == true && Input::get('change_order_status') == 'cancelled'){
          $this->classGetFunction->sendCustomMail( array('source' => 'cancelled_order', 'email' => $get_email['_billing_email'], 'order_id' => $order_id) );
        }
        elseif($environment === 'production' && $email_options['processed_order']['enable_disable'] == true && Input::get('change_order_status') == 'processing'){
          $this->classGetFunction->sendCustomMail( array('source' => 'processed_order', 'email' => $get_email['_billing_email'], 'order_id' => $order_id) );
        }
        elseif($environment === 'production' && $email_options['completed_order']['enable_disable'] == true && Input::get('change_order_status') == 'completed'){
          $this->classGetFunction->sendCustomMail( array('source' => 'completed_order', 'email' => $get_email['_billing_email'], 'order_id' => $order_id) );
        }
        return redirect()->back();
      }
      
    }
  }
  
  
  /**
   * 
   * Manage quick mail
   *
   * @param null
   * @return void
   */
  public function sendQuickMail()
  {
    if( Request::isMethod('post') && Session::token() == Input::get('_token') )
    {
      $input = Input::all();
      $rules = [
               'quickemailto'                  =>  'required',
               'quickmailsubject'              =>  'required',
               'quickmailbody'                 =>  'required',
               ];
      
      $validator = Validator:: make($input, $rules);
      
      if($validator->fails())
      {
        return redirect()-> back()
        ->withInput()
        ->withErrors( $validator );
      }
      else
      {
        $mailData = array();

        //load mailData Array
        $mailData['source']           =   'quick_mail';
        $mailData['data']             =   array('_mail_to' => Input::get('quickemailto'), '_subject' => Input::get('quickmailsubject'), '_message' => Input::get('quickmailbody'));

        $this->classGetFunction->sendCustomMail( $mailData );
        
        Session::flash('success-message', Lang::get('admin.mail_sent_msg'));
        return redirect()->back();
      }
    }
    else 
    {
      return redirect()-> back();
    }
  }
  
  /**
   * 
   * Clear design cache 
   *
   * @param null
   * @return void
   */
  public function clearDesignCache()
  {
    if(Session::has('shopist_admin_user_id'))
    {
      Artisan::call('cache:clear');
      Artisan::call('view:clear');
      die( Lang::get('admin.cache_cleared'). ' <a href="'. route('admin.dashboard') .'">'. Lang::get('admin.admin_dashboard') .'</a>');
    }
    else
    {
      die( Lang::get('admin.do_not_sufficient_permission') );
    }
  }
  
  
  /**
   * 
   * Save testimonial post data
   *
   * @param null
   * @return response
   */
  public function saveTestimonialPost($params = ''){
    if( Request::isMethod('post') && Session::token() == Input::get('_token') ){
      $data = Input::all();

      $rules =  [
                  'testimonial_post_title'  => 'required',
                ];

      $validator = Validator:: make($data, $rules);
      
      if($validator->fails()){
        return redirect()-> back()
        ->withInput()
        ->withErrors( $validator );
      }
      else{
        $post       =       new Post;
        $postMeta   =       new PostExtra;

        $post_slug  = '';
        $check_slug = Post::where(['post_slug' => string_slug_format( Input::get('testimonial_post_title') )])->orWhere('post_slug', 'like', '%' . string_slug_format( Input::get('testimonial_post_title') ) . '%')->get()->count();

        if($check_slug === 0){
          $post_slug = string_slug_format( Input::get('testimonial_post_title') );
        }
        elseif($check_slug > 0){
          $slug_count = $check_slug + 1;
          $post_slug = string_slug_format( Input::get('testimonial_post_title') ). '-' . $slug_count;
        }
        
        if(Input::get('hf_post_type') == 'add'){
          $post->post_author_id         =   Session::get('shopist_admin_user_id');
          $post->post_content           =   string_encode(Input::get('testimonial_description_editor'));
          $post->post_title             =   Input::get('testimonial_post_title');
          $post->post_slug              =   $post_slug;
          $post->parent_id              =   0;
          $post->post_status            =   Input::get('testimonial_post_visibility');
          $post->post_type              =   'testimonial';
          
          if($post->save()){
            if(PostExtra::insert(array(
                                array(
                                      'post_id'       =>  $post->id,
                                      'key_name'      =>  '_testimonial_image_url',
                                      'key_value'    =>  Input::get('image_url'),
                                      'created_at'    =>  date("y-m-d H:i:s", strtotime('now')),
                                      'updated_at'    =>  date("y-m-d H:i:s", strtotime('now'))
                                ),
                                array(
                                      'post_id'       =>  $post->id,
                                      'key_name'      =>  '_testimonial_client_name',
                                      'key_value'    =>  Input::get('testimonial_client_name'),
                                      'created_at'    =>  date("y-m-d H:i:s", strtotime('now')),
                                      'updated_at'    =>  date("y-m-d H:i:s", strtotime('now'))
                                ),
                                array(
                                      'post_id'       =>  $post->id,
                                      'key_name'      =>  '_testimonial_job_title',
                                      'key_value'    =>  Input::get('testimonial_job_title'),
                                      'created_at'    =>  date("y-m-d H:i:s", strtotime('now')),
                                      'updated_at'    =>  date("y-m-d H:i:s", strtotime('now'))
                                ),
                                array(
                                      'post_id'       =>  $post->id,
                                      'key_name'      =>  '_testimonial_company_name',
                                      'key_value'    =>  Input::get('testimonial_company_name'),
                                      'created_at'    =>  date("y-m-d H:i:s", strtotime('now')),
                                      'updated_at'    =>  date("y-m-d H:i:s", strtotime('now'))
                                ),
                                array(
                                      'post_id'       =>  $post->id,
                                      'key_name'      =>  '_testimonial_url',
                                      'key_value'    =>  Input::get('testimonial_url'),
                                      'created_at'    =>  date("y-m-d H:i:s", strtotime('now')),
                                      'updated_at'    =>  date("y-m-d H:i:s", strtotime('now'))
                                )
            ))){
              Session::flash('success-message', Lang::get('admin.successfully_saved_msg') );
              Session::flash('update-message', "");
              return redirect()->route('admin.update_testimonial_post_content', $post->post_slug);
            }
          }
        }
        elseif($params && Input::get('hf_post_type') == 'update'){
          
          $data = array(
                      'post_author_id'         =>  Session::get('shopist_admin_user_id'),
                      'post_content'           =>  string_encode(Input::get('testimonial_description_editor')),
                      'post_title'             =>  Input::get('testimonial_post_title'),
                      'post_status'            =>  Input::get('testimonial_post_visibility')
          );

          if(Post::where('post_slug', $params)->update($data)){
            
            $get_post                 =   Post :: where('post_slug', $params)->first()->toArray();
            
            $testimonial_image_url    = array(
                                            'key_value'    =>  Input::get('image_url')
            );
            
            $testimonial_client_name  = array(
                                            'key_value'    =>  Input::get('testimonial_client_name')
            );
            
            $testimonial_job_title    = array(
                                            'key_value'    =>  Input::get('testimonial_job_title')
            );
            
            $testimonial_company_name = array(
                                            'key_value'    =>  Input::get('testimonial_company_name')
            );
            
            $testimonial_url          = array(
                                            'key_value'    =>  Input::get('testimonial_url')
            );
            
            
            PostExtra::where(['post_id' => $get_post['id'], 'key_name' => '_testimonial_image_url'])->update($testimonial_image_url);
            PostExtra::where(['post_id' => $get_post['id'], 'key_name' => '_testimonial_client_name'])->update($testimonial_client_name);
            PostExtra::where(['post_id' => $get_post['id'], 'key_name' => '_testimonial_job_title'])->update($testimonial_job_title);
            PostExtra::where(['post_id' => $get_post['id'], 'key_name' => '_testimonial_company_name'])->update($testimonial_company_name);
            PostExtra::where(['post_id' => $get_post['id'], 'key_name' => '_testimonial_url'])->update($testimonial_url);
            
            Session::flash('success-message', Lang::get('admin.successfully_updated_msg'));
            return redirect()->route('admin.update_testimonial_post_content', $params);
          }
        }
      }
    }
  }
  
  /**
   * 
   * Save subscription data
   *
   * @param null
   * @return response
   */
  public function updateSubscriptionData(){
    if( Request::isMethod('post') && Session::token() == Input::get('_token') ){
      $get_subscription_option   =   Option :: where('option_name', '_subscription_data')->first();
      $subscription_data         =   unserialize($get_subscription_option->option_value);
      
      if(isset($subscription_data['mailchimp']['api_key'])){
        $subscription_data['mailchimp']['api_key'] = Input::get('inputMailchimpAPIKey'); 
      }
      if(isset($subscription_data['mailchimp']['list_id'])){
        $subscription_data['mailchimp']['list_id'] = Input::get('inputMailchimpListId'); 
      }
      
      $data = array(
                   'option_value'        => serialize($subscription_data)
      );
      
      if( Option::where('option_name', '_subscription_data')->update($data))
      {
        Session::flash('success-message', Lang::get('admin.successfully_updated_msg'));
        return redirect()->back();
      }
    } 
  }
  
  /**
   * 
   * Save subscription settings data
   *
   * @param null
   * @return response
   */
  public function updateSubscriptionSettings(){
    if( Request::isMethod('post') && Session::token() == Input::get('_token') ){
      $get_subscription_settings_option   =   Option :: where('option_name', '_subscription_settings_data')->first();
      
      if(!empty($get_subscription_settings_option)){
        $subscription_settings_data         =   unserialize($get_subscription_settings_option->option_value);
        
        $visibility           = (Input::has('subscriptions_visibility')) ? true : false;
        $cookie_visibility    = (Input::has('subscribe_popup_cookie_set')) ? true : false;
        
        if(isset($subscription_settings_data['subscription_visibility'])){
          $subscription_settings_data['subscription_visibility'] = $visibility; 
        }
        
        if(isset($subscription_settings_data['subscribe_type'])){
          $subscription_settings_data['subscribe_type'] = Input::get('subscriptions_type'); 
        }
        
        if(isset($subscription_settings_data['subscribe_options'])){
          $subscription_settings_data['subscribe_options'] = Input::get('subscribe_options'); 
        }
        
        if(isset($subscription_settings_data['popup_bg_color'])){
          $subscription_settings_data['popup_bg_color'] = Input::get('subscriptions_popup_bg_color'); 
        }
        
        if(isset($subscription_settings_data['popup_content'])){
          $subscription_settings_data['popup_content'] = string_encode(Input::get('subscription_content_editor')); 
        }
        
        $subscription_settings_data['popup_display_page'] = Input::get('popup_display'); 
        
        if(isset($subscription_settings_data['subscribe_btn_text'])){
          $subscription_settings_data['subscribe_btn_text'] = Input::get('subscribe_btn_text'); 
        }
        
        if(isset($subscription_settings_data['subscribe_popup_cookie_set_visibility'])){
          $subscription_settings_data['subscribe_popup_cookie_set_visibility'] = $cookie_visibility; 
        }
        
        if(isset($subscription_settings_data['subscribe_popup_cookie_set_text'])){
          $subscription_settings_data['subscribe_popup_cookie_set_text'] = Input::get('subscribe_popup_cookie_set_text'); 
        }

        $data = array(
                     'option_value'        => serialize($subscription_settings_data)
        );

        if( Option::where('option_name', '_subscription_settings_data')->update($data))
        {
          Session::flash('success-message', Lang::get('admin.successfully_updated_msg'));
          return redirect()->back();
        }
      } 
    }
  }
  
  
		/**
   * 
   * Save color filter data
   *
   * @param null
   * @return response
   */
		
		public function saveProductFilterColorData(){
				if( Request::isMethod('post') && Session::token() == Input::get('_token') ){
          $get_color_name            =  array();
          $get_color_code_name       =  array();
          $final_name_color_code     =  array();
          $input                     =  '';
          
          if(count(Input::get('product_filter_color_name')) > 0){
            $get_color_name = Input::get('product_filter_color_name');
          }
          
          if(count(Input::get('product_filter_color')) > 0){
            $get_color_code_name = Input::get('product_filter_color');
          }
          
          if(count($get_color_name) > 0 && count($get_color_code_name) > 0){
            foreach($get_color_name as $key => $val){
              array_push($final_name_color_code, array('key' => $key, 'color_name' => $get_color_name[$key], 'color_code' => $get_color_code_name[$key]));
            }
          }
          
          if(count($final_name_color_code) > 0){
            $input = json_encode($final_name_color_code);
          }
          
          $get_color_filter_field = Option::where(['option_name' => '_product_color_filter_data'])->get();
          if(!empty($get_color_filter_field) && $get_color_filter_field->count() > 0){
              $data = array(
                            'option_value' =>	 $input
                      );

              if(Option::where('option_name', '_product_color_filter_data')->update($data)){
                  Session::flash('success-message', Lang::get('admin.successfully_updated_msg'));
              }
          }
          else{
              if(Option::insert(
                  array(
                                  'option_name'  =>  '_product_color_filter_data',
                                  'option_value' =>	 $input,
                                  'created_at'   =>  date("y-m-d H:i:s", strtotime('now')),
                                  'updated_at'   =>  date("y-m-d H:i:s", strtotime('now'))
                  ))){
                  Session::flash('success-message', Lang::get('admin.successfully_saved_msg'));
              }
          }

          return redirect()-> back();
				}
		}		
    
  /**
  * Delete withdraw request
  *
  * @param delete_id
  * @return void
  */
    public function deleteVendorWithdrawRequest($parm){
    if($parm){
      if(VendorWithdraw::where('id', $parm)->delete()){
        return redirect()->back();
      }
    }
  }
}