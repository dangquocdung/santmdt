@section('vendors-products-page-content')
<style type="text/css">
  #store_details .dropdown-menu{
    height: 220px !important;
    background-color: #F9F9FA !important;
  }
</style>
<div id="vendor_products_content">
  <div class="row">
    <div class="products-list-top clearfix">
      <div class="col-xs-4 col-md-4">
        <div class="product-views pull-left">
          @if($vendor_products['selected_view'] == 'grid')
              <a class="active" href="{{ $vendor_products['action_url_grid_view'] }}" data-toggle="tooltip" data-placement="top" title="{{ trans('frontend.grid_label') }}"><i class="fa fa-th"></i></a> 
          @else  
              <a href="{{ $vendor_products['action_url_grid_view'] }}" data-toggle="tooltip" data-placement="top" title="{{ trans('frontend.grid_label') }}"><i class="fa fa-th"></i></a> 
          @endif

          @if($vendor_products['selected_view'] == 'list')
              <a class="active" href="{{ $vendor_products['action_url_list_view'] }}" data-toggle="tooltip" data-placement="top" title="{{ trans('frontend.list_label') }}"><i class="fa fa-th-list"></i></a>
          @else  
              <a href="{{ $vendor_products['action_url_list_view'] }}" data-toggle="tooltip" data-placement="top" title="{{ trans('frontend.list_label') }}"><i class="fa fa-th-list"></i></a>
          @endif
        </div>
      </div>
      <div class="col-xs-8 col-md-8">
        <div class="sort-filter-option">
          <div class="sort-label">{{ trans('frontend.sort_filter_label') }} </div> 
            <div class="sort-options">
              <select class="form-control select2 sort-by-filter">
                  @if($vendor_products['sort_by'] == 'all')  
                  <option selected="selected" value="all">{{ trans('frontend.sort_filter_all_label') }}</option>
                  @else
                  <option value="all">{{ trans('frontend.sort_filter_all_label') }}</option>
                  @endif

                  @if($vendor_products['sort_by'] == 'alpaz')  
                  <option selected="selected" value="alpaz">{{ trans('frontend.sort_filter_alpaz_label') }}</option>
                  @else
                  <option value="alpaz">{{ trans('frontend.sort_filter_alpaz_label') }}</option>
                  @endif

                  @if($vendor_products['sort_by'] == 'alpza')  
                  <option selected="selected" value="alpza">{{ trans('frontend.sort_filter_alpza_label') }}</option>
                  @else
                  <option value="alpza">{{ trans('frontend.sort_filter_alpza_label') }}</option>
                  @endif

                  @if($vendor_products['sort_by'] == 'low-high')  
                  <option selected="selected" value="low-high">{{ trans('frontend.sort_filter_low_high_label') }}</option>
                  @else
                  <option value="low-high">{{ trans('frontend.sort_filter_low_high_label') }}</option>
                  @endif

                  @if($vendor_products['sort_by'] == 'high-low')  
                  <option selected="selected" value="high-low">{{ trans('frontend.sort_filter_high_low_label') }}</option>
                  @else
                  <option value="high-low">{{ trans('frontend.sort_filter_high_low_label') }}</option>
                  @endif

                  @if($vendor_products['sort_by'] == 'old-new')  
                  <option selected="selected" value="old-new">{{ trans('frontend.sort_filter_old_new_label') }}</option>
                  @else
                  <option value="old-new">{{ trans('frontend.sort_filter_old_new_label') }}</option>
                  @endif

                  @if($vendor_products['sort_by'] == 'new-old')
                  <option selected="selected" value="new-old">{{ trans('frontend.sort_filter_new_old_label') }}</option>
                  @else
                  <option value="new-old">{{ trans('frontend.sort_filter_new_old_label') }}</option>
                  @endif
              </select>
          </div>		
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="products-list">
        <br>  
      @include('includes.frontend.vendor-products')
      @yield('vendor-products-content')
    </div>
  </div>      
</div>
@endsection 