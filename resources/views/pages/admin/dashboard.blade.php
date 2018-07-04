@extends('layouts.admin.master')
@section('content')
 
@if(Request::is('admin/dashboard'))
  @include('pages.admin.dashboard-content')
  @section('title', trans('admin.dashboard') .' < '. get_site_title())
  @yield('dashboard-content')

@elseif(Request::is('admin/pages/list'))
  @include('pages.admin.cms.pages-list-content')
  @section('title', trans('admin.pages_list_title') .' < '. get_site_title())
  @yield('page-list-content')
  
@elseif(Request::is('admin/page/add'))
  @include('pages.admin.cms.add-page-content')
  @section('title', trans('admin.add_page_title') .' < '. get_site_title())
  @yield('add-page-content')  
  
@elseif(Request::is('admin/page/update/*'))
  @include('pages.admin.cms.update-page-content')
  @section('title', trans('admin.update_page_title') .' < '. get_site_title())
  @yield('update-page-content')    
  
@elseif(Request::is('admin/blog/add'))
  @include('pages.admin.cms.add-blog-content')
  @section('title', trans('admin.add_post_page_title') .' < '. get_site_title())
  @yield('add-blog-post-content')
  
@elseif(Request::is('admin/blog/update/*'))
  @include('pages.admin.cms.update-blog-content')
  @section('title', trans('admin.update_post_page_title') .' < '. get_site_title())
  @yield('update-blog-post-content')  
  
@elseif(Request::is('admin/blog/list'))
  @include('pages.admin.cms.blogs-list-content')
  @section('title', trans('admin.posts_list') .' < '. get_site_title())
  @yield('blogs-post-list-content')

@elseif(Request::is('admin/blog/comments-list'))
  @include('pages.admin.cms.blog-comments-list')
  @section('title', trans('admin.comments_list') .' < '. get_site_title())
  @yield('blogs-post-comments-list-content')  
  
@elseif(Request::is('admin/testimonial/add'))
  @include('pages.admin.cms.add-testimonial-content')
  @section('title', trans('admin.add_post_page_title') .' < '. get_site_title())
  @yield('add-testimonial-post-content')
  
@elseif(Request::is('admin/testimonial/update/*'))
  @include('pages.admin.cms.update-testimonial-content')
  @section('title', trans('admin.update_post_page_title') .' < '. get_site_title())
  @yield('update-testimonial-post-content')
  
@elseif(Request::is('admin/testimonial/list'))
  @include('pages.admin.cms.testimonial-list-content')
  @section('title', trans('admin.posts_list') .' < '. get_site_title())
  @yield('testimonial-post-list-content')  
  
@elseif(Request::is('admin/user/profile'))
  @include('pages.admin.users.user-profile')
  @section('title', trans('admin.user_profile') .' < '. get_site_title())
  @yield('user-profile-content')
  
@elseif(Request::is('admin/product/add'))
  @include('pages.admin.product.add-product-content')
  @section('title', trans('admin.add_product') .' < '. get_site_title())
  @yield('add-new-product-content')
  
@elseif(Request::is('admin/product/categories/list') || Request::is('admin/blog/categories/list'))
  @include('pages.admin.categories-list')
    @if(Request::is('admin/product/categories/list'))
      @section('title', trans('admin.product_categories_list') .' < '. get_site_title())
    @elseif(Request::is('admin/blog/categories/list'))
      @section('title', trans('admin.blog_categories_list') .' < '. get_site_title())
    @endif
  @yield('categories-list-content')
  
@elseif(Request::is('admin/product/tags/list'))
  @include('pages.admin.product.product-tags-list')
  @section('title', trans('admin.product_tags_list') .' < '. get_site_title())
  @yield('product-tags-list-content')
  
@elseif(Request::is('admin/product/attributes/list'))
  @include('pages.admin.product.product-attribute-list')
  @section('title', trans('admin.product_attributes_list') .' < '. get_site_title())
  @yield('product-attributes-list-content')
  
@elseif(Request::is('admin/product/colors/list'))
  @include('pages.admin.product.product-colors-list')
  @section('title', trans('admin.product_color_list') .' < '. get_site_title())
  @yield('product-colors-list-content')  
  
@elseif(Request::is('admin/product/sizes/list'))
  @include('pages.admin.product.product-sizes-list')
  @section('title', trans('admin.product_sizes_list') .' < '. get_site_title())
  @yield('product-sizes-list-content')    
 
@elseif(Request::is('admin/shipping-method/options'))
  @if(is_vendor_login())
    @include('pages.admin.shipping.vendor-shipping-options')
  @else
    @include('pages.admin.shipping.shipping-options')
  @endif
  
  @section('title', trans('admin.shipping_options') .' < '. get_site_title())
  @yield('shipping-options-content')

@elseif(Request::is('admin/shipping-method/flat-rate'))
  @if(is_vendor_login())
    @include('pages.admin.shipping.vendor-shipping-method-flat-rate')
  @else
    @include('pages.admin.shipping.shipping-method-flat-rate')
  @endif
 
  @section('title', trans('admin.update_flat_rate') .' < '. get_site_title())
  @yield('shipping-method-flat-rate-content')

@elseif(Request::is('admin/shipping-method/free-shipping'))
  @if(is_vendor_login())
    @include('pages.admin.shipping.vendor-shipping-method-free-shipping')
  @else
    @include('pages.admin.shipping.shipping-method-free-shipping')
  @endif
  
  @section('title', trans('admin.update_free_shipping') .' < '. get_site_title())
  @yield('shipping-method-free-shipping-content')

@elseif(Request::is('admin/shipping-method/local-delivery'))
  @if(is_vendor_login())
    @include('pages.admin.shipping.vendor-shipping-method-local-delivery')
  @else
    @include('pages.admin.shipping.shipping-method-local-delivery')
  @endif
  
  @section('title', trans('admin.update_local_delivery') .' < '. get_site_title())
  @yield('shipping-method-local-delivery-content')

@elseif(Request::is('admin/manufacturers/list'))
  @include('pages.admin.cms.manufacturers-list-content')
  @section('title', trans('admin.manufacturers_list') .' < '. get_site_title())
  @yield('manufacturers-list-content')

@elseif(Request::is('admin/manufacturers/add'))
  @include('pages.admin.cms.add-manufacturers-content')
  @section('title', trans('admin.add_manufacturers') .' < '. get_site_title())
  @yield('manufacturers-add-content')

@elseif(Request::is('admin/manufacturers/update/*'))
  @include('pages.admin.cms.update-manufacturers-content')
  @section('title', trans('admin.update_manufacturers') .' < '. get_site_title())
  @yield('manufacturers-update-content')

@elseif(Request::is('admin/settings/general'))
  @include('pages.admin.settings.general-content')
  @section('title', trans('admin.update_general_settings') .' < '. get_site_title())
  @yield('settings-general-content')

@elseif(Request::is('admin/settings/languages') || Request::is('admin/settings/languages/update/*'))
  @include('pages.admin.settings.languages-content')
  @section('title', trans('admin.manage_languages') .' < '. get_site_title())
  @yield('manage-languages-content')

@elseif(Request::is('admin/designer/clipart/categories/list'))
  @include('pages.admin.custom-designer.art-categories-list')
  @section('title', trans('admin.clipart_categories_list') .' < '. get_site_title())
  @yield('art-categories-lists-content')

@elseif(Request::is('admin/designer/clipart/list'))
  @include('pages.admin.custom-designer.clipart-list')
  @section('title', trans('admin.clipart_list') .' < '. get_site_title())
  @yield('clipart-lists-content')

@elseif(Request::is('admin/designer/clipart/category/add'))
  @include('pages.admin.custom-designer.add-art-category-content')
  @section('title', trans('admin.add_clipart_category') .' < '. get_site_title())
  @yield('art-new-category-content')

@elseif(Request::is('admin/designer/clipart/category/update/*'))
  @include('pages.admin.custom-designer.update-art-category-content')
  @section('title', trans('admin.update_clipart_category') .' < '. get_site_title())
  @yield('art-category-update-content')

@elseif(Request::is('admin/designer/clipart/add'))
  @include('pages.admin.custom-designer.add-new-art-content')
  @section('title', trans('admin.add_new_art') .' < '. get_site_title())
  @yield('add-new-art-content')

@elseif(Request::is('admin/designer/clipart/update/*'))
  @include('pages.admin.custom-designer.update-art-content')
  @section('title', trans('admin.update_art') .' < '. get_site_title())
  @yield('update-art-content')
  
@elseif(Request::is('admin/designer/shape/add'))
  @include('pages.admin.custom-designer.add-shape-content')
  @section('title', trans('admin.add_new_shape') .' < '. get_site_title())
  @yield('add-new-shape-content')

@elseif(Request::is('admin/designer/shape/update/*'))
  @include('pages.admin.custom-designer.update-shape-content')
  @section('title', trans('admin.update_shape') .' < '. get_site_title())
  @yield('update-shape-content')

@elseif(Request::is('admin/designer/shape/list'))
  @include('pages.admin.custom-designer.shape-list')
  @section('title', trans('admin.shape_list') .' < '. get_site_title())
  @yield('shape-list-content')

@elseif(Request::is('admin/designer/font/add'))
  @include('pages.admin.custom-designer.add-font-content')
  @section('title', trans('admin.add_new_font') .' < '. get_site_title())
  @yield('add-new-font-content')  
  
@elseif(Request::is('admin/designer/font/update/*'))
  @include('pages.admin.custom-designer.update-font-content')
  @section('title', trans('admin.update_font') .' < '. get_site_title())
  @yield('update-font-content')  

@elseif(Request::is('admin/designer/fonts/list'))
  @include('pages.admin.custom-designer.font-list')
  @section('title', trans('admin.fonts_list') .' < '. get_site_title())
  @yield('fonts-list-content')  

@elseif(Request::is('admin/product/update/*'))
  @include('pages.admin.product.update-product-content')
  @section('title', trans('admin.update_product') .' < '. get_site_title())
  @yield('update-product-content')

@elseif(Request::is('admin/product/list/*'))
  @include('pages.admin.product.product-list')
  @section('title', trans('admin.products_list') .' < '. get_site_title())
  @yield('product-list-content')
  
@elseif(Request::is('admin/product/comments-list'))
  @include('pages.admin.product.comments-list')
  @section('title', trans('admin.comments_list') .' < '. get_site_title())
  @yield('product-comments-list-content')  
  
@elseif(Request::is('admin/orders') || Request::is('admin/orders/current-date'))
  @include('pages.admin.orders.order-list') 
  @section('title', trans('admin.orders_list') .' < '. get_site_title())
  @yield('orders-list-content')
  
@elseif(Request::is('admin/orders/details/*'))
  @include('pages.admin.orders.order-details')  
  @section('title', trans('admin.orders_details') .' < '. get_site_title())
  @yield('orders-details-content')
  
@elseif(Request::is('admin/designer/settings'))
  @include('pages.admin.custom-designer.settings') 
  @section('title', trans('admin.designer_settings') .' < '. get_site_title())
  @yield('custom-designer-settings-content') 
  
@elseif(Request::is('admin/payment-method/options'))
  @if(is_vendor_login())
    @include('pages.admin.payment.vendor-payment-options')
  @else
    @include('pages.admin.payment.payment-options')
  @endif
  
  @section('title', trans('admin.update_payment_options') .' < '. get_site_title())
  @yield('payment-options-content')
  
@elseif(Request::is('admin/payment-method/direct-bank'))
  @if(is_vendor_login())
    @include('pages.admin.payment.vendor-payment-direct-bank')
  @else
    @include('pages.admin.payment.payment-direct-bank')
  @endif
   
  @section('title', trans('admin.update_bacs_payment') .' < '. get_site_title())
  @yield('payment-direct-bank-content')
  
@elseif(Request::is('admin/payment-method/cash-on-delivery'))
  @if(is_vendor_login())
    @include('pages.admin.payment.vendor-payment-cash-on-delivery')  
  @else
    @include('pages.admin.payment.payment-cash-on-delivery')  
  @endif
  
  @section('title', trans('admin.update_cod_payment') .' < '. get_site_title())
  @yield('payment-cash-on-delivery-content') 
  
@elseif(Request::is('admin/payment-method/paypal'))
  @if(is_vendor_login())
    @include('pages.admin.payment.vendor-payment-paypal')   
  @else
    @include('pages.admin.payment.payment-paypal') 
  @endif
  
  @section('title', trans('admin.update_paypal_payment') .' < '. get_site_title())
  @yield('payment-paypal-content')
  
@elseif(Request::is('admin/payment-method/stripe'))
  @if(is_vendor_login())
    @include('pages.admin.payment.vendor-payment-stripe') 
  @else
    @include('pages.admin.payment.payment-stripe') 
  @endif
  
  @section('title', trans('admin.update_stripe_payment') .' < '. get_site_title())
  @yield('payment-stripe-content')  
  
@elseif(Request::is('admin/reports'))
  @include('pages.admin.reports.reports-main')
  @section('title', trans('admin.reports') .' < '. get_site_title())
  @yield('reports-content') 
  
@elseif(Request::is('admin/users/roles/add'))
  @include('pages.admin.users.add-users-roles')
  @section('title', trans('admin.add_roles_page_title') .' < '. get_site_title())
  @yield('user-roles-add-content')
  
@elseif(Request::is('admin/users/roles/update/*'))
  @include('pages.admin.users.update-users-roles')
  @section('title', trans('admin.update_roles_page_title') .' < '. get_site_title())
  @yield('user-roles-update-content')  
  
@elseif(Request::is('admin/user/add'))
  @include('pages.admin.users.add-users')
  @section('title', trans('admin.add_user_page_title') .' < '. get_site_title())
  @yield('user-add-content')  

@elseif(Request::is('admin/user/update/*'))
  @include('pages.admin.users.update-users')
  @section('title', trans('admin.update_user_page_title') .' < '. get_site_title())
  @yield('user-update-content')
  
@elseif(Request::is('admin/users/list'))
  @include('pages.admin.users.user-list')
  @section('title', trans('admin.user_list_title') .' < '. get_site_title())
  @yield('user-list-content')  
  
@elseif(Request::is('admin/users/roles/list'))
  @include('pages.admin.users.user-role-list')
  @section('title', trans('admin.user_role_list_title') .' < '. get_site_title())
  @yield('user-role-list-content')    
  
@elseif(Request::is('admin/settings/appearance'))
  @include('pages.admin.settings.appearance-content')
  @section('title', trans('admin.appearance_page_title') .' < '. get_site_title())
  @yield('appearance-content')

@elseif(Request::is('admin/settings/emails'))
  @include('pages.admin.settings.emails')
  @section('title', trans('admin.emails_page_title') .' < '. get_site_title())
  @yield('emails-content')

@elseif(Request::is('admin/settings/emails/details/*'))
  @include('pages.admin.settings.email-content')
  @section('title', trans('admin.email_content_label') .' < '. get_site_title())
  @yield('email-details-content')  
  
@elseif(Request::is('admin/reports/sales-by-product-title') || Request::is('admin/reports/sales-by-month') || Request::is('admin/reports/sales-by-last-7-days') || Request::is('admin/reports/sales-by-custom-days') || Request::is('admin/reports/sales-by-payment-method'))
  @include('pages.admin.reports.reports-content')
  @section('title', trans('admin.reports') .' < '. get_site_title()) 
  @yield('reports-details') 

@elseif(Request::is('admin/coupon-manager/coupon/list'))
  @include('pages.admin.coupon.coupons-list')
  @section('title', trans('admin.coupon_manager_list_page_title') .' < '. get_site_title())
  @yield('coupon-manager-list-content')
  
@elseif(Request::is('admin/coupon-manager/coupon/add'))
  @include('pages.admin.coupon.coupon-content')
  @section('title', trans('admin.coupon_manager_page_title') .' < '. get_site_title())
  @yield('coupon-manager-content')
  
@elseif(Request::is('admin/coupon-manager/coupon/update/*'))
  @include('pages.admin.coupon.update-coupon-content')
  @section('title', trans('admin.update_coupon_page_title') .' < '. get_site_title())
  @yield('update-coupon-manager-content')
  
@elseif(Request::is('admin/manage/seo'))
  @if(is_vendor_login())
    @include('pages.admin.seo.vendor-seo-content')
  @else
    @include('pages.admin.seo.seo-content')
  @endif
  
  @section('title', trans('admin.manage_seo_page_title') .' < '. get_site_title())
  @yield('manage-seo-content')

@elseif(Request::is('admin/customer/request-product'))
  @include('pages.admin.request-product.request-product-content')
  @section('title', trans('admin.request_product_page_title') .' < '. get_site_title())
  @yield('request-product-content')  
		
@elseif(Request::is('admin/extra-features/product-compare-fields'))
  @include('pages.admin.extra-features.compare-products-content')
  @section('title', trans('admin.compare_products_page_title') .' < '. get_site_title())
  @yield('compare-products-content')
		
@elseif(Request::is('admin/extra-features/color-filter'))
  @include('pages.admin.extra-features.color-filter-content')
  @section('title', trans('admin.color_filter_page_title') .' < '. get_site_title())
  @yield('color-filter-content')
   
@elseif(Request::is('admin/subscription/custom'))
  @include('pages.admin.subscriptions.custom-subscriptions-content')
  @section('title', trans('admin.custom_subscriptions_page_title') .' < '. get_site_title())
  @yield('custom-subscriptions-content')
  
@elseif(Request::is('admin/subscription/mailchimp'))
  @include('pages.admin.subscriptions.mailchimp-subscriptions-content')
  @section('title', trans('admin.mailchimp_subscriptions_page_title') .' < '. get_site_title())
  @yield('mailchimp-subscriptions-content')
  
@elseif(Request::is('admin/subscription/settings'))
  @include('pages.admin.subscriptions.subscription-settings-content')
  @section('title', trans('admin.subscription_settings_page_title') .' < '. get_site_title())
  @yield('subscription-settings-content')
  
@elseif(Request::is('admin/vendors/list') || Request::is('admin/vendors/list/active') || Request::is('admin/vendors/list/pending'))
  @include('pages.admin.vendors.vendors-list')
  @section('title', trans('admin.vendors_list_page_title') .' < '. get_site_title())
  @yield('vendors-list-page-content')
  
@elseif(Request::is('admin/vendors/comments-list'))
  @include('pages.admin.vendors.comments-list')
  @section('title', trans('admin.comments_list') .' < '. get_site_title())
  @yield('vendors-comments-list-content')  
  
@elseif(Request::is('admin/vendors/package/create'))
  @include('pages.admin.vendors.vendors-package')
  @section('title', trans('admin.vendors_package_label') .' < '. get_site_title())
  @yield('vendors-package-content')
  
@elseif(Request::is('admin/vendors/package/update/*'))
  @include('pages.admin.vendors.update-vendors-package')
  @section('title', trans('admin.update_vendors_package_label') .' < '. get_site_title())
  @yield('update-vendors-package-content')  
  
@elseif(Request::is('admin/vendors/package/list'))
  @include('pages.admin.vendors.vendors-packages-list')
  @section('title', trans('admin.vendors_package_list_label') .' < '. get_site_title())
  @yield('vendors-packages-list-content')      
  
@elseif(Request::is('admin/vendor/settings'))
  @include('pages.admin.vendors.vendors-settings')
  @section('title', trans('admin.vendors_settings_page_title') .' < '. get_site_title())
  @yield('normal-vendors-settings-page-content')

@elseif(Request::is('admin/vendors/manage/packages'))
  @include('pages.admin.vendors.vendor-packages-select-option')
  @section('title', trans('admin.vendor_package_select') .' < '. get_site_title())
  @yield('vendor-package-select-page-content')
  
@elseif(Request::is('admin/vendors/withdraw') || Request::is('admin/status/vendors/withdraw/*'))
  @include('pages.admin.vendors.withdraw-content')
  @section('title', trans('admin.withdraw_title_label') .' < '. get_site_title())
  @yield('vendor-withdraw-page-content')
  
@elseif(Request::is('admin/vendors/withdraw/*'))
  @include('pages.admin.vendors.withdraw-request-update-content')
  @section('title', trans('admin.withdraw_title_label') .' < '. get_site_title())
  @yield('vendor-withdraw-page-update-content')

@elseif(Request::is('admin/vendors/reviews'))
  @include('pages.admin.vendors.vendor-reviews')
  @section('title', trans('admin.vendor_reviews_title_label') .' < '. get_site_title())
  @yield('vendor-reviews-page-content')

@elseif(Request::is('admin/vendors/announcement/list'))
  @include('pages.admin.vendors.vendor-announcement-list-content')
  @section('title', trans('admin.announcement_list_label') .' < '. get_site_title())
  @yield('vendor-announcement-list-page-content')
  
@elseif(Request::is('admin/vendors/announcement'))
  @include('pages.admin.vendors.vendor-announcement')
  @section('title', trans('admin.announcement_label') .' < '. get_site_title())
  @yield('vendor-announcement-page-content')
  
@elseif(Request::is('admin/vendors/announcement/*'))
  @include('pages.admin.vendors.update-vendor-announcement')
  @section('title', trans('admin.update_announcement_label') .' < '. get_site_title())
  @yield('vendor-announcement-page-update-content')  
  
@elseif(Request::is('admin/vendors/settings'))
  @include('pages.admin.vendors.vendor-settings')
  @section('title', trans('admin.settings') .' < '. get_site_title())
  @yield('vendors-settings-page-content')    
  
@elseif(Request::is('admin/vendor/notice-board'))
  @include('pages.admin.vendors.notice-board')
  @section('title', trans('admin.notice_board_label') .' < '. get_site_title())
  @yield('notice-board-content')
  
@elseif(Request::is('admin/vendor/notice-board/single/details/*'))
  @include('pages.admin.vendors.single-notice-board-details')
  @section('title', trans('admin.notice_details_label') .' < '. get_site_title())
  @yield('single-notice-board-content-details')

@elseif(Request::is('admin/vendors/earning-reports') || Request::is('admin/vendors/earning-reports/*'))
  @include('pages.admin.vendors.vendor-admin-reports')
  @section('title', trans('admin.earning_reports_label') .' < '. get_site_title())
  @yield('earning-reports-content')
  
@endif
@endsection