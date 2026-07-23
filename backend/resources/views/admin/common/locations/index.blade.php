@section('title','Locations')
@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <h1 class="h2 mb-4">
                <i class="fas fa-globe me-2"></i>Location Management
            </h1>
        </div>
    </div>
    
    <!-- Action Buttons -->
    <div class="row mb-4">
        <div class="col-12">
            <button class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#addCountryModal">
                <i class="fas fa-plus me-2"></i>Add Country
            </button>
            <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#addCityModal">
                <i class="fas fa-plus me-2"></i>Add City
            </button>
            <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#addMegaMenuModal">
                <i class="fas fa-plus me-2"></i>Add Location For Mega Menu
            </button>
        </div>
    </div>
    
    <!-- Countries, Cities -->
    <div class="row mb-4">
        <div class="col-lg-4">
            <div class="row">
                <div class="col-lg-12 mb-4">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-list me-2"></i>Countries For Mega Menu
                            </h5>
                        </div>
                        <div class="card-body">
                            @foreach($menu_locations as $key => $l)
                            <div class="d-flex justify-content-between align-items-center py-2 {{$menu_locations->count() > $key+1 ??'border-bottom' }} menu-row">
                                <div class="co-md-6">
                                    <span>{{$l->related->name}}</span>
                                    <small class="text-muted d-block">Packages: {{ $l->related->packages->count() }}</small>
                                </div>
                                <div class="co-md-6">
                                    <input id="status_{{$l->id }}" type="checkbox" data-id="{{$l->id }}" data-url="{{ route('admin.locations.mega.menu.update',$l->id) }}" class="js-switch menu-status" <?php echo $l->is_active == 1 ? 'checked' : '' ?>>

                                    <button class="btn btn-sm btn-outline-danger delete-menu" data-id="{{ $l->id}}" data-url="{{ route('admin.locations.mega.menu.delete',$l->id) }}">
                                        <i class="fas fa-trash icon"></i>
                                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                    </button>
                                </div>
                            </div>
                            @endforeach
                            <hr>
                            @if($menu_locations->lastPage() > 1)
                            @endif
                        </div>
                        @if($menu_locations->lastPage() > 1)
                            <div>
                                @include('admin.common.pagination', ['paginator' => $menu_locations])
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Countries -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-flag me-2"></i>Countries
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <input type="text" value="{{$country}}" id="countrySearch" class="form-control" placeholder="Search country...">
                    </div>

                    <div id="countryList">
                        @include('admin.locations.country-list', ['countries' => $countries])
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Cities -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-city me-2"></i>Cities
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <input type="text" value="{{$location}}" id="citySearch" class="form-control" placeholder="Search city...">
                    </div>
                    <div id="cityList">
                        @include('admin.locations.city-list', ['locations' => $locations])
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('modal')
    <!-- Add Country Modal -->
    <div class="modal fade" id="addCountryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-plus me-2"></i>Add New Country
                    </h5>
                    <button type="button" class="btn-close btn-close-white close-modal" data-bs-dismiss="modal"></button>
                </div>
                <form id="addCountry">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="country_name" class="form-label">Country Name<span class="required-text">*</span></label>
                            <input type="text" class="form-control" id="country_name" name="name" required>
                            <div class="text-danger" id="country-name-error" style="display:none"></div>
                        </div>
                        <div class="mb-3">
                            <label for="country_code" class="form-label">Country Code</label>
                            <input type="text" class="form-control" id="country_code" name="code" maxlength="3">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary close-modal" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success submit-btn">
                            <i class="fas fa-save me-2"></i>Save Country
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add City Modal -->
    <div class="modal fade" id="addCityModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-plus me-2"></i>Add New City
                    </h5>
                    <button type="button" class="btn-close btn-close-white close-modal" data-bs-dismiss="modal"></button>
                </div>
                <form id="addCity">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="city_name" class="form-label">City Name<span class="required-text">*</span></label>
                            <input type="text" class="form-control" id="city_name" name="name" required>
                            <div class="text-danger" id="city-name-error" style="display:none"></div>
                        </div>
                        <div class="mb-3">
                            <label for="country_id_city" class="form-label">Select Country<span class="required-text">*</span></label>
                            <select class="form-select" id="country_id_city" name="country_id" required>
                                <option value="">Choose a country...</option>
                                @foreach($all_countries as $key => $c)
                                <option value="{{$c->id}}">{{$c->name}}</option>
                                @endforeach
                            </select>
                            <div class="text-danger" id="country-id-city-error" style="display:none"></div>
                        </div>
                        <!-- <div class="mb-3">
                            <label for="is_top_trending" class="form-label">Top Trending City?</label>
                            <input type="checkbox"  id="is_top_trending" value="1" name="is_top_trending" >
                        </div> -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondaryclose-modal close-modal" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning submit-btn">
                            <i class="fas fa-save me-2"></i>Save City
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editCityModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-edit me-2"></i>Edit City
                    </h5>
                    <button type="button" class="btn-close btn-close-white close-modal" data-bs-dismiss="modal"></button>
                </div>
                <form id="editCity">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="city_id">
                        <div class="mb-3">
                            <label for="city_name_edit" class="form-label">City Name<span class="required-text">*</span></label>
                            <input type="text" class="form-control" id="city_name_edit" name="name" required>
                            <div class="text-danger" id="city-name-edit-error" style="display:none"></div>
                        </div>
                        <div class="mb-3">
                            <label for="country_id_city_edit" class="form-label">Select Country<span class="required-text">*</span></label>
                            <select class="form-select" id="country_id_city_edit" name="country_id" required>
                                <option value="">Choose a country...</option>
                                @foreach($all_countries as $key => $c)
                                <option value="{{$c->id}}">{{$c->name}}</option>
                                @endforeach
                            </select>
                            <div class="text-danger" id="country-id-city-edit-error" style="display:none"></div>
                        </div>
                        <!-- <div class="mb-3">
                            <label for="is_top_trending_edit" class="form-label">Top Trending City?</label>
                            <input type="checkbox"  id="is_top_trending_edit" value="1" name="is_top_trending" >
                        </div> -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondaryclose-modal close-modal" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning" id="update-btn" data-url="">
                            <i class="fas fa-save me-2"></i>Update City
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="cityPageSettingModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title" id="city-title">Edit City</h5>
                    <button type="button" class="btn-close btn-close-white close-modal" data-bs-dismiss="modal"></button>
                </div>
                <form id="cityPageSetting" method="POST" action="" enctype="multipart/form-data">
                @csrf 
                @method('PUT')
                    <div class="modal-body">
                        <input type="hidden" name="page_setting">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="city_page_title" class="form-label">Page Title<span class="required-text">*</span></label>
                                    <input type="text" class="form-control" id="city_page_title" name="title" required>
                                    <div class="text-danger d-none" id="city-page-title-error"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="city_page_sub_title" class="form-label">Page Sub Title</label>
                                    <input type="text" class="form-control" id="city_page_sub_title" name="sub_title">
                                    <div class="text-danger d-none" id="city-page-sub-title-error"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3">
                                <label for="city_page_banner" class="form-label">Page Banner<span class="required-text">*</span>(accept only .webp)</label>
                                <input type="file" class="form-control" id="city_page_banner" name="banner_image" accept=".webp,image/webp" required>
                                <div class="text-danger d-none" id="city-page-banner-error"></div>
                            </div>
                            <div class="mb-3">
                                <label for="city_page_banner_alt" class="form-label">Banner image Alt</label>
                                <input type="text" class="form-control" id="city_page_banner_alt" name="banner_image_alt" required>
                                <div class="text-danger d-none" id="city-page-banner-alt-error"></div>
                            </div>
                        </div>
                        <div class="row" id="galleryPreview"></div>
                        <div class="row">
                            <div class="mb-3">
                                <label for="city_page_about" class="form-label">Page About<span class="required-text">*</span></label>
                                <textarea class="form-control tinymce" name="about" id="city_page_about" rows="5" id="about"></textarea>
                                <div class="text-danger d-none" id="city-page-about-error"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondaryclose-modal close-modal" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning" id="update-page-setting-btn" data-url="">
                            <i class="fas fa-save me-2"></i>Update Setting
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="countryPageSettingModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title" id="country-title">Edit Country</h5>
                    <button type="button" class="btn-close btn-close-white close-modal" data-bs-dismiss="modal"></button>
                </div>
                <form id="countryPageSetting" method="POST" action="" enctype="multipart/form-data">
                @csrf 
                @method('PUT')
                    <div class="modal-body">
                        <input type="hidden" name="page_setting">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="country_page_title" class="form-label">Page Title<span class="required-text">*</span></label>
                                    <input type="text" class="form-control" id="country_page_title" name="title" required>
                                    <div class="text-danger d-none" id="country-page-title-error"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="country_page_sub_title" class="form-label">Page Sub Title</label>
                                    <input type="text" class="form-control" id="country_page_sub_title" name="sub_title">
                                    <div class="text-danger d-none" id="country-page-sub-title-error"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3">
                                <label for="country_page_banner" class="form-label">Page Banner<span class="required-text">*</span>(accept only .webp)</label>
                                <input type="file" class="form-control" id="country_page_banner" accept=".webp,image/webp" name="banner_image" required>
                                <div class="text-danger d-none" id="country-page-banner-error"></div>
                            </div>
                            <div class="mb-3">
                                <label for="country_page_banner_alt" class="form-label">Banner image Alt</label>
                                <input type="text" class="form-control" id="country_page_banner_alt" name="banner_image_alt" required>
                                <div class="text-danger d-none" id="country-page-banner-alt-error"></div>
                            </div>
                        </div>
                        <div class="row" id="galleryPreviewCountry"></div>
                        <div class="row">
                            <div class="mb-3">
                                <label for="country_page_about" class="form-label">Page About<span class="required-text">*</span></label>
                                <textarea class="form-control tinymce"  name="about" id="country_page_about" rows="5" id="about"></textarea>
                                <div class="text-danger d-none" id="country-page-about-error"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondaryclose-modal close-modal" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning" id="update-country-page-setting-btn" data-url="">
                            <i class="fas fa-save me-2"></i>Update Setting
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="viewPageSettingModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="page_name"></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <p id="page-title"><strong>Title:</strong> </p>
                        </div>
                        <div class="col-md-12">
                            <p id="page-sub-title"><strong>Sub Title:</strong> </p>
                            
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <h6 class="fw-bold">Banner Image</h6>
                        <div class="col-md-12 text-center">
                            <div class="card mb-3">
                                <img id="page-banner-image" src="" class="card-img-top img-fluid" style="height:150px; object-fit:cover;">
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <h6 class="fw-bold">About</h6>
                            <p id="page-about"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="faqModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title" id="city-title">Faqs</h5>
                    <button type="button" class="btn-close btn-close-white close-modal" data-bs-dismiss="modal"></button>
                </div>
                <form id="faqForm" method="POST" action="" enctype="multipart/form-data">
                @csrf 
                @method('PUT')
                    <div class="modal-body"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondaryclose-modal close-modal" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning" id="update-page-setting-btn" data-url="">
                            <i class="fas fa-save me-2"></i>Save Faqs
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="countryMetaModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title" id="country-meta-title">Edit Meta</h5>
                    <button type="button" class="btn-close btn-close-white close-modal" data-bs-dismiss="modal"></button>
                </div>
                <form id="countryMeta" method="POST" action="" enctype="multipart/form-data">
                @csrf 
                @method('PUT')
                    <div class="modal-body">
                        <input type="hidden" name="meta_setting">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="country_meta_title" class="form-label">Meta Title</label>
                                    <input type="text" class="form-control" id="country_meta_title" name="meta_title">
                                    <div class="text-danger d-none" id="country-meta-title-error"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="country_meta_description" class="form-label">Meta Description</label>
                                    <input type="text" class="form-control" id="country_meta_description" name="meta_description">
                                    <div class="text-danger d-none" id="country-meta-description-error"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="country_meta_keywords" class="form-label">Meta Keywords</label>
                                    <input type="text" class="form-control" id="country_meta_keywords" name="meta_keywords">
                                    <div class="text-danger d-none" id="country-meta-keyords-error"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="country_h1_heading" class="form-label">H1 Heading</label>
                                    <input type="text" class="form-control" id="country_h1_heading" name="h1_heading">
                                    <div class="text-danger d-none" id="country-meta-keyords-error"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3">
                                <label for="country_meta_details" class="form-label">Extra Meta Tag</label>
                                <textarea class="form-control"  name="meta_details" id="country_meta_details" rows="5" id="meta_details"></textarea>
                                <div class="text-danger d-none" id="country-meta-details-error"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondaryclose-modal close-modal" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning" id="update-country-meta-btn">
                            <i class="fas fa-save me-2"></i>Update Meta Setting
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="locationMetaModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title" id="location-title">Edit Meta</h5>
                    <button type="button" class="btn-close btn-close-white close-modal" data-bs-dismiss="modal"></button>
                </div>
                <form id="locationMeta" method="POST" action="" enctype="multipart/form-data">
                @csrf 
                @method('PUT')
                    <div class="modal-body">
                        <input type="hidden" name="meta_setting">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="location_meta_title" class="form-label">Meta Title</label>
                                    <input type="text" class="form-control" id="location_meta_title" name="meta_title">
                                    <div class="text-danger d-none" id="location-meta-title-error"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="location_meta_description" class="form-label">Meta Description</label>
                                    <input type="text" class="form-control" id="location_meta_description" name="meta_description">
                                    <div class="text-danger d-none" id="location-meta-description-error"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="location_meta_keywords" class="form-label">Meta Keywords</label>
                                    <input type="text" class="form-control" id="location_meta_keywords" name="meta_keywords">
                                    <div class="text-danger d-none" id="location-meta-keyords-error"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="location_h1_heading" class="form-label">H1 Heading</label>
                                    <input type="text" class="form-control" id="location_h1_heading" name="h1_heading">
                                    <div class="text-danger d-none" id="location-meta-keyords-error"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3">
                                <label for="location_meta_details" class="form-label">Extra Meta Tag</label>
                                <textarea class="form-control"  name="meta_details" id="location_meta_details" rows="5" id="meta_details"></textarea>
                                <div class="text-danger d-none" id="location-meta-details-error"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondaryclose-modal close-modal" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning" id="update-location-meta-btn">
                            <i class="fas fa-save me-2"></i>Update Meta Setting
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addMegaMenuModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-plus me-2"></i>Add Country/City For Mega Menu
                    </h5>
                    <button type="button" class="btn-close btn-close-white close-modal" data-bs-dismiss="modal"></button>
                </div>
                <form id="addMegaMenu" method="POST" action="{{route('admin.locations.add.mega.menu')}}" enctype="multipart/form-data">
                    @csrf 
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="location_type_menu" class="form-label">Location Type<span class="required-text">*</span></label>
                            <select class="form-select" id="location_type_menu" name="type" required>
                                <option value="">Select Location Type</option>
                                <option value="Country">Country</option>
                                <option value="Location">City</option>
                            </select>
                            <div class="text-danger" id="location-type-menu-error" style="display:none"></div>
                        </div>
                        <div class="mb-3" id="country_menu_block" style="display:none">
                            <label for="country_id_menu" class="form-label">Select Country<span class="required-text">*</span></label>
                            <select class="form-select" id="country_id_menu" name="country_id" >
                                <option value="">Choose a country...</option>
                                @php
                                    // Get all related country IDs from active MegaMenuLocations
                                    $countryIds = $menu_locations
                                        ->where('type', 'App\\Models\\Country')
                                        ->pluck('location_id')
                                        ->toArray();
                                @endphp

                                @foreach($all_countries as $c)
                                    @if($c->id != 1 && $c->id != 10 && !in_array($c->id, $countryIds) && $c->packages->count()>0)
                                        <option value="{{ $c->id }}">{{ $c->name }}({{$c->packages->count()}})</option>
                                    @endif
                                @endforeach

                            </select>
                            <div class="text-danger" id="country-id-menu-error" style="display:none"></div>
                        </div>
                        <div class="mb-3" id="location_menu_block" style="display:none">
                            <label for="location_id_menu" class="form-label">Select City<span class="required-text">*</span></label>
                            <select class="form-select" id="location_id_menu" name="location_id" >
                                <option value="">Choose a city...</option>
                                @php
                                    // Get all related country IDs from active MegaMenuLocations
                                    $locationIds = $menu_locations
                                        ->where('type', 'App\\Models\\Location')
                                        ->pluck('location_id')
                                        ->toArray();
                                @endphp
                                @foreach($all_locations as $key => $l)
                                    @if($l->country->id != 1 && $l->country->id !=10 && !in_array($l->id, $locationIds) && $l->packages->count()>0)
                                        <option value="{{$l->id}}">{{$l->name}}({{$l->packages->count()}})</option>
                                    @endIf
                                @endforeach
                            </select>
                            <div class="text-danger" id="location-id-menu-error" style="display:none"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondaryclose-modal close-modal" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning submit-btn">
                            <i class="fas fa-save me-2"></i>Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    function modalDismis(){
        $('#addCountryModal').modal('hide');
        $('#addCityModal').modal('hide');
        $('#addCountry')[0].reset();
        $('#addCity')[0].reset();
        $('.text-danger').text('');
        $('.text-danger').hide();
    }

    $(document).ready(function () {
        let faqIndex = 0;
        @if(session('success'))
            toastr.success("{{ session('success') }}", 'Success');
        @endif

        $(document).on('click', '.close-modal', function() {
            modalDismis();
        });


        $(document).on('change', '#location_type_menu', function() {
            let type = $(this).val();

            if (type === 'Country') {
                $('#country_menu_block').show();
                $('#country_id_menu').prop('required', true);

                $('#location_menu_block').hide();
                $('#location_id_menu').prop('required', false);
            } else {
                $('#location_menu_block').show();
                $('#location_id_menu').prop('required', true);

                $('#country_menu_block').hide();
                $('#country_id_menu').prop('required', false);
            }
        });


        
        $('#addCountry').on('submit', function(e) {
            e.preventDefault();
            $('.submit-btn').attr('disabled',true);
            $('.text-danger').text('');
            var name = $('#country_name').val();
            var code = $('#country_code').val();
            if(name){
                $.ajax({
                    type: "POST",
                    url: "{{ route('admin.countries.store') }}",
                    data: {'name': name, 'code': code},
                    success: function(response) {
                        $('#addCountry')[0].reset();
                        $('.submit-btn').removeAttr('disabled');
                        $('#addCountryModal').modal('hide');
                        toastr.success('Country added successfully!', 'Success');
                        $('.text-danger').hide();
                        setTimeout(function(){ location.reload(); }, 1500);
                    },
                    error: function(xhr) {
                        $('.submit-btn').removeAttr('disabled');
                        if(xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            if (errors.name) { $('#country-name-error').text(errors.name[0]); $('#country-name-error').show(); }
                        }
                    }
                });
            }
        });

        $('#addCity').on('submit', function(e) {
            e.preventDefault();
            $('.submit-btn').attr('disabled',true);
            $('.text-danger').text('');
            $.ajax({
                type: "POST",
                url: "{{ route('admin.locations.store') }}",
                data: $(this).serialize(),
                success: function(response) {
                    $('#addCity')[0].reset();
                    $('.submit-btn').removeAttr('disabled');
                    $('#addCityModal').modal('hide');
                    toastr.success('City added successfully!', 'Success');
                    $('.text-danger').hide();
                    setTimeout(function(){
                        location.reload();
                    }, 1500);
                },
                error: function(xhr) {
                    $('.submit-btn').removeAttr('disabled');
                    if(xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        if (errors.name) $('#city-name-error').text(errors.name[0]);
                        if (errors.country_id) $('#country-id-city-error').text(errors.country_id[0]);
                        $('.text-danger').show();
                    }
                }
            });
        });

        $(document).on('click', '.edit-city', function() {
            let btn = $(this);
            let id = btn.data('id');
            let dataUrl = btn.data('url').trim();
            let dataupUrl = btn.data('upurl').trim();
            $('#city_id').val(id);
            btn.find('.spinner-border').removeClass('d-none');
            btn.find('.icon').addClass('d-none');
            $.ajax({
                type: "GET",
                dataType: "json",
                url: dataUrl,
                success: function(data) {
                    $('#city_name_edit').val(data.name);
                    $('#country_id_city_edit').val(data.country_id);
                    if (data.is_top_trending == 1) {
                        $('#is_top_trending_edit').prop('checked', true);   // check
                    } else {
                        $('#is_top_trending_edit').prop('checked', false);  // uncheck
                    }
                    $('#update-btn').attr('data-url',dataupUrl);
                    $('#editCityModal').modal('show');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while fetching city details.'
                    });
                },
                complete: function () {
                    // Hide loader, show button text back
                    btn.find('.spinner-border').addClass('d-none');
                    btn.find('.icon').removeClass('d-none');
                }
            });
        });

        $('#editCity').on('submit', function(e) {
            e.preventDefault();
            $('#update-btn').attr('disabled',true);
            $('.text-danger').text('');
            $.ajax({
                type: "PUT",
                url: $('#update-btn').data('url'),
                data: $(this).serialize(),
                success: function(response) {
                    $('#editCity')[0].reset();
                    $('#update-btn').removeAttr('disabled');
                    $('#editCityModal').modal('hide');
                    toastr.success('City updated successfully!', 'Success');
                    $('.text-danger').hide();
                    setTimeout(function(){
                        location.reload();
                    }, 1500);
                },
                error: function(xhr) {
                    $('#update-btn').removeAttr('disabled');
                    if(xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        if (errors.name) $('#city-name-edit-error').text(errors.name[0]);
                        if (errors.country_id) $('#country-id-city-edit-error').text(errors.country_id[0]);
                        $('.text-danger').show();
                    }
                }
            });
        });

        $(document).on('click', '.add-city-details', function() {
            let btn = $(this);
            let id = $(this).data('id');
            let dataUrl = $(this).data('url').trim();
            let dataeUrl = $(this).data('eurl').trim();
            btn.find('.spinner-border').removeClass('d-none');
            btn.find('.icon').addClass('d-none');
            $.ajax({
                type: "GET",
                dataType: "json",
                url: dataeUrl,
                success: function(data) {
                    $('#city-title').text(data.name);
                    if(data.details != null){
                        $('#city_page_title').val(data.details.title);
                        $('#city_page_sub_title').val(data.details.sub_title);
                        $('#city_page_banner_alt').val(data.details.banner_image_alt);
                        tinymce.get('city_page_about') ? tinymce.get('city_page_about').setContent(data.details.about || '') : $('#city_page_about').val(data.details.about);
                        $('#galleryPreview').html(`<div class="card mb-3">
                            <img src="${s3BaseUrl+data.details.banner_image}" class="card-img-top img-fluid" style="height:150px; object-fit:cover;">
                        </div>`);
                        $('#city_page_banner').prop('required',false);
                    }
                    $('#cityPageSetting').attr('action',dataUrl);
                    $('#cityPageSettingModal').modal('show');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while fetching city details.'
                    });
                },
                complete: function () {
                    // Hide loader, show button text back
                    btn.find('.spinner-border').addClass('d-none');
                    btn.find('.icon').removeClass('d-none');
                }
            });
        });

        document.getElementById('city_page_banner').addEventListener('change', function(event) {
            let input = event.target;
            let file = event.target.files[0];
            let preview = document.getElementById('galleryPreview');

            if (file && file.type === 'image/webp') {
                let reader = new FileReader();
                reader.onload = function(e) {
                    let col = document.createElement('div');
                    col.classList.add('col-md-12', 'mb-3');
                    col.innerHTML = `
                        <div class="card">
                            <img src="${e.target.result}" class="card-img-top img-fluid" 
                                style="height:150px; object-fit:cover;">
                        </div>
                    `;
                    preview.innerHTML = ""; // Clear old preview
                    preview.appendChild(col);
                };
                reader.readAsDataURL(file);
            } else {
                toastr.error("Only .webp images are allowed!", 'Error');
                input.value = ""; // reset invalid file
            }
        });

        document.getElementById('cityPageSetting').addEventListener('submit', function(event) {
            tinymce.triggerSave();
            event.preventDefault(); // stop form first
            $('#update-page-setting-btn').prop('disabled',true);
            let isError = 0;
            let description = document.getElementById('city_page_about');
            if(!description.value.trim()) {
                $('#update-page-setting-btn').prop('disabled',false);
                isError = 1;
                $('#city-page-about-error').removeClass('d-none');
                $('#city-page-about-error').text('Page about required');
                $('html, body').animate({
                    scrollTop: $("#city-page-about-error").offset().top - 100   // adjust -100 for some spacing from top
                }, 600);

            }

            if(!isError){
                $('#city-page-about-error').addClass('d-none');
                this.submit();
            }
        });

        $(document).on('click', '.city-details', function() {
            let btn = $(this);
            let id = $(this).data('id');
            let dataUrl = $(this).data('url').trim();
            btn.find('.spinner-border').removeClass('d-none');
            btn.find('.icon').addClass('d-none');
            $.ajax({
                type: "GET",
                dataType: "json",
                url: dataUrl,
                success: function(data) {
                    $('#page_name').text(data.name+ (data.is_top_trending ? ' (Top Trending City)' :''));
                    if(data.details != null){
                        $('#page-title').append(data.details.title);
                        $('#page-sub-title').append(data.details.sub_title);
                        $('#page-about').html(data.details.about);
                        $('#page-banner-image').attr('src',s3BaseUrl+data.details.banner_image);
                    }
                    $('#viewPageSettingModal').modal('show');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while fetching city details.'
                    });
                },
                complete: function () {
                    // Hide loader, show button text back
                    btn.find('.spinner-border').addClass('d-none');
                    btn.find('.icon').removeClass('d-none');
                }
            });
        });

        $(document).on('click', '.add-country-details', function() {
            let btn = $(this);
            let id = $(this).data('id');
            let dataUrl = $(this).data('url').trim();
            let dataeUrl = $(this).data('eurl').trim();
            btn.find('.spinner-border').removeClass('d-none');
            btn.find('.icon').addClass('d-none');
            $.ajax({
                type: "GET",
                dataType: "json",
                url: dataeUrl,
                success: function(data) {
                    $('#country-title').text(data.name);
                    if(data.details != null){
                        $('#country_page_title').val(data.details.title);
                        $('#country_page_sub_title').val(data.details.sub_title);
                        $('#country_page_banner_alt').val(data.details.banner_image_alt);
                        tinymce.get('country_page_about') ? tinymce.get('country_page_about').setContent(data.details.about || '') : $('#country_page_about').val(data.details.about);
                        $('#galleryPreviewCountry').html(`<div class="card mb-3">
                            <img src="${s3BaseUrl+data.details.banner_image}" class="card-img-top img-fluid" style="height:150px; object-fit:cover;">
                        </div>`);
                        $('#country_page_banner').prop('required',false);
                    }
                    $('#countryPageSetting').attr('action',dataUrl);
                    $('#countryPageSettingModal').modal('show');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while fetching country details.'
                    });
                },
                complete: function () {
                    // Hide loader, show button text back
                    btn.find('.spinner-border').addClass('d-none');
                    btn.find('.icon').removeClass('d-none');
                }
            });
        });

        document.getElementById('country_page_banner').addEventListener('change', function(event) {
            let input = event.target;
            let file = event.target.files[0];
            let preview = document.getElementById('galleryPreviewCountry');

            if (file && file.type === 'image/webp') {
                let reader = new FileReader();
                reader.onload = function(e) {
                    let col = document.createElement('div');
                    col.classList.add('col-md-12', 'mb-3');
                    col.innerHTML = `
                        <div class="card">
                            <img src="${e.target.result}" class="card-img-top img-fluid" 
                                style="height:150px; object-fit:cover;">
                        </div>
                    `;
                    preview.innerHTML = ""; // Clear old preview
                    preview.appendChild(col);
                };
                reader.readAsDataURL(file);
            } else {
                toastr.error("Only .webp images are allowed!", 'Error');
                input.value = ""; // reset invalid file
            }
        });

        document.getElementById('countryPageSetting').addEventListener('submit', function(event) {
            tinymce.triggerSave();
            event.preventDefault(); // stop form first
            $('#update-page-setting-btn').prop('disabled',true);
            let isError = 0;
            let description = document.getElementById('country_page_about');
            if(!description.value.trim()) {
                $('#update-country-page-setting-btn').prop('disabled',false);
                isError = 1;
                $('#country-page-about-error').removeClass('d-none');
                $('#country-page-about-error').text('Page about required');
                $('html, body').animate({
                    scrollTop: $("#country-page-about-error").offset().top - 100   // adjust -100 for some spacing from top
                }, 600);
            }
            if(!isError){
                $('#country-page-about-error').addClass('d-none');
                this.submit();
            }
        });

        $(document).on('click', '.country-details', function() {
            let btn = $(this);
            let id = $(this).data('id');
            let dataUrl = $(this).data('url').trim();
            btn.find('.spinner-border').removeClass('d-none');
            btn.find('.icon').addClass('d-none');
            $.ajax({
                type: "GET",
                dataType: "json",
                url: dataUrl,
                success: function(data) {
                    $('#page_name').text(data.name);
                    if(data.details != null){
                        $('#page-title').append(data.details.title);
                        $('#page-sub-title').append(data.details.sub_title);
                        $('#page-about').html(data.details.about);
                        //tinymce.get('page-about').setContent(data.details.about);
                        $('#page-banner-image').attr('src',s3BaseUrl+data.details.banner_image);
                    }
                    $('#viewPageSettingModal').modal('show');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while fetching country details.'
                    });
                },
                complete: function () {
                    // Hide loader, show button text back
                    btn.find('.spinner-border').addClass('d-none');
                    btn.find('.icon').removeClass('d-none');
                }
            });
        });

        $(document).on('click', '.country-faqs', function() {
            faqIndex = 0;
            let btn = $(this);
            btn.find('.spinner-border').removeClass('d-none');
            btn.find('.icon').addClass('d-none');
            let id = btn.data('id');
            let dataUrl = btn.data('url');
            
            $('#faqForm').attr('action',dataUrl);
            $.ajax({
                type: "GET",
                dataType: "json",
                url: "{{ route('admin.countries.index') }}", // better to point this to a dedicated faq route
                data: { id: id, faqs: 'list' },
                success: function(data) {
                    let faqs = data.country.faqs || [];
                    $('#faqModal .modal-title').text(data.country.name+"'s FAQ");

                    let body = `
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="faq_title" class="form-label">Faq Title<span class="required-text">*</span></label>
                            <input value="${data.country.faq_title ? data.country.faq_title : ''}" class="form-control" name="faq_title" id="faq_title" placeholder="" required>
                        </div>
                    </div>
                    <table class="table" id="faqTable">
                        <thead>
                            <tr>
                                <th>Question<span class="required-text">*</span></th>
                                <th>Answer<span class="required-text">*</span></th>
                                <th><button type="button" class="btn btn-sm btn-outline-success" id="addFaqRow"><i class="fas fa-plus"></button></th>
                            </tr>
                        </thead>
                        <tbody>
                    `;

                    if (faqs.length > 0) {
                        $.each(faqs, function(index, faq) {
                            body += `
                                <tr class="b-none">
                                    <td><input type="text" name="faqs[${faqIndex}][question]" value="${faq.question}" class="form-control" required /></td>
                                    <td><textarea name="faqs[${faqIndex}][answer]" class="form-control">${faq.answer ?? ''}</textarea></td>
                                    <td><button type="button" class="btn btn-sm btn-outline-danger removeFaqRow"><i class="fas fa-trash"></button></td>
                                </tr>
                            `;
                            faqIndex++;
                        });
                    } else {
                        body += `
                            <tr class="b-none">
                                <td><input type="text" name="faqs[${faqIndex}][question]" class="form-control" required/></td>
                                <td><textarea name="faqs[${faqIndex}][answer]" class="form-control"></textarea></td>
                                <td></td>
                            </tr>
                        `;
                        faqIndex++;
                    }

                    body += `</tbody></table>`;

                    $('#faqModal .modal-body').html(body);

                    // open modal
                    $('#faqModal').modal('show');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while fetching package FAQs.'
                    });
                },
                complete: function () {
                    // Hide loader, show button text back
                    btn.find('.spinner-border').addClass('d-none');
                    btn.find('.icon').removeClass('d-none');
                }
            });
        });

        $(document).on('click', '.location-faqs', function() {
            faqIndex = 0;
            let btn = $(this);
            btn.find('.spinner-border').removeClass('d-none');
            btn.find('.icon').addClass('d-none');
            let id = btn.data('id');
            let title = btn.data('title');
            let dataUrl = btn.data('url');
            
            $('#faqForm').attr('action',dataUrl);
            $.ajax({
                type: "GET",
                dataType: "json",
                url: "{{ route('admin.locations.index') }}", // better to point this to a dedicated faq route
                data: { id: id, faqs: 'list' },
                success: function(data) {
                    let faqs = data.location.faqs || [];
                    $('#faqModal .modal-title').text(data.location.name+"'s FAQ");

                    let body = `
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="faq_title" class="form-label">Faq Title<span class="required-text">*</span></label>
                            <input value="${data.location.faq_title ? data.location.faq_title : ''}" class="form-control" name="faq_title" id="faq_title" placeholder="" required>
                        </div>
                    </div>
                    <table class="table" id="faqTable">
                        <thead>
                            <tr>
                                <th>Question</th>
                                <th>Answer</th>
                                <th><button type="button" class="btn btn-sm btn-outline-success" id="addFaqRow"><i class="fas fa-plus"></button></th>
                            </tr>
                        </thead>
                        <tbody>
                    `;

                    if (faqs.length > 0) {
                        $.each(faqs, function(index, faq) {
                            body += `
                                <tr class="b-none">
                                    <td><input type="text" name="faqs[${faqIndex}][question]" value="${faq.question}" class="form-control" required /></td>
                                    <td><textarea name="faqs[${faqIndex}][answer]" class="form-control">${faq.answer ?? ''}</textarea></td>
                                    <td><button type="button" class="btn btn-sm btn-outline-danger removeFaqRow"><i class="fas fa-trash"></button></td>
                                </tr>
                            `;
                            faqIndex++;
                        });
                    } else {
                        body += `
                            <tr class="b-none">
                                <td><input type="text" name="faqs[${faqIndex}][question]" class="form-control" required/></td>
                                <td><textarea name="faqs[${faqIndex}][answer]" class="form-control"></textarea></td>
                                <td></td>
                            </tr>
                        `;
                        faqIndex++;
                    }

                    body += `</tbody></table>`;

                    $('#faqModal .modal-body').html(body);

                    // open modal
                    $('#faqModal').modal('show');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while fetching package FAQs.'
                    });
                },
                complete: function () {
                    // Hide loader, show button text back
                    btn.find('.spinner-border').addClass('d-none');
                    btn.find('.icon').removeClass('d-none');
                }
            });
        });

        $(document).on('click', '#addFaqRow', function() {
            let row = `
                <tr class="b-none">
                    <td><input type="text" name="faqs[${faqIndex}][question]" class="form-control" required /></td>
                    <td><textarea name="faqs[${faqIndex}][answer]" class="form-control"></textarea></td>
                    <td><button type="button" class="btn btn-sm btn-outline-danger removeFaqRow"><i class="fas fa-trash"></button></td>
                </tr>`;
            faqIndex++;
            $('#faqTable tbody').append(row);
            
        });

        $(document).on('click', '.removeFaqRow', function() {
            $(this).closest('tr').remove();
        });


        $(document).on('click', '.country-meta', function() {
            let btn = $(this);
            let id = $(this).data('id');
            let dataUrl = $(this).data('url').trim();
            let dataupUrl = $(this).data('upurl').trim();
            btn.find('.spinner-border').removeClass('d-none');
            btn.find('.icon').addClass('d-none');
            $.ajax({
                type: "GET",
                dataType: "json",
                url: dataUrl,
                success: function(data) {
                    $('#country-meta-title').text('# '+data.name+'-Meta Info');
                    if(data.meta != null){
                        $('#country_meta_title').val(data.meta.meta_title);
                        $('#country_meta_description').val(data.meta.meta_description);
                        $('#country_meta_keywords').val(data.meta.meta_keywords);
                        $('#country_h1_heading').val(data.meta.h1_heading);
                        $('#country_meta_details').val(data.meta.meta_details);
                    }
                    $('#countryMeta').attr('action',dataupUrl);
                    $('#countryMetaModal').modal('show');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while fetching city details.'
                    });
                },
                complete: function () {
                    // Hide loader, show button text back
                    btn.find('.spinner-border').addClass('d-none');
                    btn.find('.icon').removeClass('d-none');
                }
            });
        });

        $(document).on('click', '.location-meta', function() {
            let btn = $(this);
            let id = $(this).data('id');
            let dataUrl = $(this).data('url').trim();
            let dataupUrl = $(this).data('upurl').trim();
            btn.find('.spinner-border').removeClass('d-none');
            btn.find('.icon').addClass('d-none');
            $.ajax({
                type: "GET",
                dataType: "json",
                url: dataUrl,
                success: function(data) {
                    $('#location-title').text('# '+data.name+'-Meta Info');
                    //$('#city-title').text(data.name);
                    if(data.meta != null){
                        $('#location_meta_title').val(data.meta.meta_title);
                        $('#location_meta_description').val(data.meta.meta_description);
                        $('#location_meta_keywords').val(data.meta.meta_keywords);
                        $('#location_h1_heading').val(data.meta.h1_heading);
                        $('#location_meta_details').val(data.meta.meta_details);
                    }
                    $('#locationMeta').attr('action',dataupUrl);
                    $('#locationMetaModal').modal('show');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while fetching city details.'
                    });
                },
                complete: function () {
                    // Hide loader, show button text back
                    btn.find('.spinner-border').addClass('d-none');
                    btn.find('.icon').removeClass('d-none');
                }
            });
        });

        $(document).on('change', '.menu-status', function() {
            let menu_status = $(this).prop('checked') === true ? 1 : 0;
            $.ajax({
                type: "PUT",
                dataType: "json",
                url: $(this).data('url'),
                data: {
                    'status': menu_status
                },
                success: function(data) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: data.message
                    });
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while processing your request.'
                    });
                }

            });
        });

        $(document).on("click", ".delete-menu", function(e) {
            e.preventDefault();
            let itemId = $(this).data("id");
            let itemUrl = $(this).data("url");
            let row = $(this).closest(".menu-row"); // parent <tr>
            Swal.fire({
                title: "Are you sure?",
                text: "This item will be deleted!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#e3342f",
                cancelButtonColor: "#6c757d",
                confirmButtonText: "Yes, delete it",
                cancelButtonText: "Cancel",
                customClass: {
                    popup: 'rounded-2xl shadow-lg',  // Rounded + shadow
                    confirmButton: 'px-4 py-2 text-white',
                    cancelButton: 'px-4 py-2 text-white'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    let btn = $(this);
                    btn.find('.spinner-border').removeClass('d-none');
                    btn.find('.icon').addClass('d-none');
                    $.ajax({
                        type: "DELETE",
                        url: itemUrl,
                        success: function(res) {
                            if(res.status){
                                Swal.fire(
                                    "Deleted!",
                                    "The item has been deleted successfully.",
                                    "success"
                                );
                                row.remove();
                                //window.location.href = "{{ route('admin.locations.index') }}";
                            }
                        },
                        error: function(xhr) {
                            toastr.error('Menu Location not deleted!', 'Failed');
                        },
                        complete: function () {
                            // Hide loader, show button text back
                            btn.find('.spinner-border').addClass('d-none');
                            btn.find('.icon').removeClass('d-none');
                        }
                    });
                    
                }
            });
        });
    });
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const countrySearchInput = $('#countrySearch');
    const countryList = $('#countryList');
    let timer;

    function fetchCountries(url = "{{ route('admin.locations.index') }}") {
        const keyword = countrySearchInput.val().trim();

        // Create URL object to safely manipulate query params
        const fullUrl = new URL(url, window.location.origin);

        // ✅ Add or remove `keyword` param based on input
        if (keyword) {
            fullUrl.searchParams.set('country', keyword);
        } else {
            fullUrl.searchParams.delete('country');
        }

        // ✅ Update browser address bar without reloading
        window.history.pushState({}, '', fullUrl.toString());

        $.ajax({
            url: url,
            type: 'GET',
            data: { country: keyword },
            dataType: 'html',
            beforeSend: function() {
                countryList.addClass('loading');
            },
            success: function(response) {
                countryList.html(response);
            },
            complete: function() {
                countryList.removeClass('loading');
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    }

    // ✅ Pagination links (handles both normal + search)
    // $(document).on('click', '.pagination a', function(e) {
    //     e.preventDefault();
    //     const url = $(this).attr('href');
    //     window.history.pushState({}, '', url);
    //     fetchCountries(url);
    // });

    // ✅ Debounced search input (waits 300ms after typing stops)
    countrySearchInput.on('input', function() {
        clearTimeout(timer);
        timer = setTimeout(() => {
            fetchCountries();
        }, 300);
    });
});

document.addEventListener('DOMContentLoaded', function () {
    const searchInput = $('#citySearch');
    const cityList = $('#cityList');
    let timer;

    function fetchCities(url = "{{ route('admin.locations.index') }}") {
        const keyword = searchInput.val().trim();

        // Create URL object to safely manipulate query params
        const fullUrl = new URL(url, window.location.origin);

        // ✅ Add or remove `keyword` param based on input
        if (keyword) {
            fullUrl.searchParams.set('city', keyword);
        } else {
            fullUrl.searchParams.delete('city');
        }

        // ✅ Update browser address bar without reloading
        window.history.pushState({}, '', fullUrl.toString());

        $.ajax({
            url: url,
            type: 'GET',
            data: { city: keyword },
            dataType: 'html',
            beforeSend: function() {
                cityList.addClass('loading');
            },
            success: function(response) {
                cityList.html(response);
            },
            complete: function() {
                cityList.removeClass('loading');
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    }

    // ✅ Pagination links (handles both normal + search)
    // $(document).on('click', '.pagination a', function(e) {
    //     e.preventDefault();
    //     const url = $(this).attr('href');
    //     window.history.pushState({}, '', url);
    //     fetchCities(url);
    // });

    // ✅ Debounced search input (waits 300ms after typing stops)
    searchInput.on('input', function() {
        clearTimeout(timer);
        timer = setTimeout(() => {
            fetchCities();
        }, 300);
    });
});
</script>



@endsection