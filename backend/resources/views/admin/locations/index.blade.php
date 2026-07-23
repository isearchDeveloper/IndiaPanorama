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
           
        </div>
    </div>
    
    <!-- Countries, Cities -->
    <div class="row mb-4">
        <div class="col-lg-4">
            <div class="row">
                <div class="col-lg-12 mb-4">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-list me-2"></i>Regions
                            </h5>
                        </div>
                        <div class="card-body">
                            @foreach($regions as $key => $r)
                            <div class="d-flex justify-content-between align-items-center py-2 {{$regions->count() > $key+1 ??'border-bottom' }} menu-row">
                                <div class="co-md-6">
                                    <span>{{$r->name}}</span>
                                    <small class="text-muted d-block">Packages: {{ $r->packages->count() }}</small>
                                    <small class="text-muted d-block">
                                    Author: {{$r->details->author_name ?? '---'}}
                                </small>
                                </div>
                                
                                <div class="co-md-6">
                                <button class="btn btn-sm btn-outline-primary add-region-details"
                                    data-id="{{ $r->id }}"
                                    data-url="{{ route('admin.regions.update',$r->id) }}"
                                    data-eurl="{{ route('admin.regions.show',$r->id) }}">
                                    <i class="fas fa-cog icon"></i>
                                    <span class="spinner-border spinner-border-sm d-none"></span>
                                </button>
                                <button class="btn btn-sm btn-outline-primary region-faqs"
                                    data-id="{{ $r->id }}"
                                    data-url="{{ route('admin.regions.faqUpdate',$r->id) }}">
                                    <i class="fa fa-question-circle icon"></i>
                                    <span class="spinner-border spinner-border-sm d-none"></span>
                                </button>
                                <button class="btn btn-sm btn-outline-primary region-meta"
                                    data-id="{{ $r->id }}"
                                    data-url="{{ route('admin.regions.show',$r->id) }}"
                                    data-upurl="{{ route('admin.regions.update',$r->id) }}">
                                    <i class="fa fa-globe icon"></i>
                                    <span class="spinner-border spinner-border-sm d-none"></span>
                                </button>
                                </div>
                            </div>
                            @endforeach
                            <hr>
                            @if($regions->lastPage() > 1)
                            @endif
                        </div>
                        @if($regions->lastPage() > 1)
                            <div>
                                @include('admin.common.pagination', ['paginator' => $regions])
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
                        <i class="fas fa-flag me-2"></i>Countries ({{ $countries->total() }})
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
                        <i class="fas fa-city me-2"></i>Cities ({{ $locations->total() }})
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
                        <div class="mb-3" id="state_block" style="display:none;">
                            <label class="form-label">Select State<span class="required-text">*</span></label>
                            <select class="form-select" id="state_id" name="state_id">
                                <option value="">Choose state...</option>
                            </select>
                        </div>
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

    <!-- Edit City Modal -->
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
                        <div class="mb-3" id="state_block_edit" style="display:none;">
                            <label class="form-label">Select State<span class="required-text">*</span></label>
                            <select class="form-select" id="state_id_edit" name="state_id">
                                <option value="">Choose state...</option>
                            </select>
                        </div>
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
                        <div class="row">
                            <div class="col-md-12">
                                <x-image-license-fields name="banner_license" label="Banner Image License Details" />
                            </div>
                        </div>
                        <div class="row" id="galleryPreview"></div>
                        <div class="row">
                            <div class="mb-3">
                                <label for="city_page_about" class="form-label">Page About<span class="required-text">*</span></label>
                                <textarea class="form-control tinymce" name="about" id="city_page_about" rows="5"></textarea>
                                <div class="text-danger d-none" id="city-page-about-error"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3">
                                <label class="form-label">Author Name</label>
                                <input type="text" class="form-control" name="author_name" id="city_author_name" required>
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
                        <div class="row">
                            <div class="col-md-12">
                                <x-image-license-fields name="banner_license" label="Banner Image License Details" />
                            </div>
                        </div>
                        <div class="row" id="galleryPreviewCountry"></div>
                        <div class="row">
                            <div class="mb-3">
                                <label for="country_page_about" class="form-label">Page About<span class="required-text">*</span></label>
                                <textarea class="form-control tinymce" name="about" id="country_page_about" rows="5"></textarea>
                                <div class="text-danger d-none" id="country-page-about-error"></div>
                            </div>
                            <div class="row">
                                <div class="mb-3">
                                    <label class="form-label">Author Name</label>
                                    <input type="text" class="form-control" name="author_name" id="country_author_name" required>
                                </div>
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
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3">
                                <label for="country_meta_details" class="form-label">Extra Meta Tag</label>
                                <textarea class="form-control" name="meta_details" id="country_meta_details" rows="5"></textarea>
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
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3">
                                <label for="location_meta_details" class="form-label">Extra Meta Tag</label>
                                <textarea class="form-control" name="meta_details" id="location_meta_details" rows="5"></textarea>
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
                            <select class="form-select" id="country_id_menu" name="country_id">
                                <option value="">Choose a country...</option>
                                @php
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
                            <select class="form-select" id="location_id_menu" name="location_id">
                                <option value="">Choose a city...</option>
                                @php
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
let faqIndex = 0;

function setTinyContent(editorId, content = '') {
    if (tinymce.get(editorId)) {
        tinymce.get(editorId).setContent(content);
    } else {
        tinymce.init({
            selector: '#' + editorId,
            height: 300,
            menubar: false,
            plugins: 'link lists code',
            toolbar: 'undo redo | bold italic | bullist numlist | link | code',
            setup: function (editor) {
                editor.on('init', function () {
                    editor.setContent(content);
                });
            }
        });
    }
}

function modalDismis(){
    $('#addContinentModal').modal('hide');
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
        $('.submit-btn').attr('disabled', true);
        $('.text-danger').text('');
        var name = $('#country_name').val();
        var code = $('#country_code').val();
        if (name) {
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
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        if (errors.name) { $('#country-name-error').text(errors.name[0]); $('#country-name-error').show(); }
                    }
                }
            });
        }
    });

    $('#addCity').on('submit', function(e) {
        e.preventDefault();
        $('.submit-btn').attr('disabled', true);
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
                setTimeout(function(){ location.reload(); }, 1500);
            },
            error: function(xhr) {
                $('.submit-btn').removeAttr('disabled');
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    if (errors.name) $('#city-name-error').text(errors.name[0]);
                    if (errors.country_id) $('#country-id-city-error').text(errors.country_id[0]);
                    $('.text-danger').show();
                }
            }
        });
    });

    // =============================================
    // EDIT CITY - with state fix (no if data.state_id condition)
    // =============================================
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
                    $('#is_top_trending_edit').prop('checked', true);
                } else {
                    $('#is_top_trending_edit').prop('checked', false);
                }
                $('#update-btn').attr('data-url', dataupUrl);

                // Reset state block first
                $('#state_block_edit').hide();
                $('#state_id_edit').html('<option value="">Choose state...</option>');

                // Load states based on country_id (no state_id condition — fixes the bug)
                $.ajax({
                    url: "{{ route('admin.get.states') }}",
                    type: "GET",
                    data: { country_id: data.country_id },
                    success: function(res) {
                        if (res.states.length > 0) {
                            let isIndia = $('#country_id_city_edit').find('option:selected').text().toLowerCase().includes('india');
                            if (isIndia) {
                                $('#state_block_edit').show();
                                $('#state_id_edit').prop('required', true);
                                $.each(res.states, function(key, state) {
                                    $('#state_id_edit').append(
                                        `<option value="${state.id}" ${state.id == data.state_id ? 'selected' : ''}>${state.name}</option>`
                                    );
                                });
                            }
                        }
                    }
                });

                $('#editCityModal').modal('show');
            },
            error: function(jqXHR, textStatus, errorThrown) {
                Swal.fire({ icon: 'error', title: 'Error', text: 'An error occurred while fetching city details.' });
            },
            complete: function() {
                btn.find('.spinner-border').addClass('d-none');
                btn.find('.icon').removeClass('d-none');
            }
        });
    });

    $('#editCity').on('submit', function(e) {
        e.preventDefault();
        $('#update-btn').attr('disabled', true);
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
                setTimeout(function(){ location.reload(); }, 1500);
            },
            error: function(xhr) {
                $('#update-btn').removeAttr('disabled');
                if (xhr.status === 422) {
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
                $('#city_author_name').val(data.author_name ?? '');
                
                if (data.details != null) {
                    $('#city_page_title').val(data.details.title);
                    $('#city_page_sub_title').val(data.details.sub_title);
                    $('#city_page_banner_alt').val(data.details.banner_image_alt);
                    setTinyContent('city_page_about', data.details.about);
                    $('#galleryPreview').html(`
                        <div class="card mb-3">
                            <img src="${s3BaseUrl+data.details.banner_image}" class="card-img-top img-fluid" style="height:150px; object-fit:cover;">
                        </div>
                    `);
                    $('#city_page_banner').prop('required', false);
                }
                if (typeof window.populateImageLicenseBlock === 'function') {
                    window.populateImageLicenseBlock('banner_license', data.banner_license);
                }
                $('#cityPageSetting').attr('action', dataUrl);
                $('#cityPageSettingModal').modal('show');
                setTinyContent('city_page_about', data.details?.about ?? '');
            },
            error: function() {
                Swal.fire({ icon: 'error', title: 'Error', text: 'An error occurred while fetching city details.' });
            },
            complete: function() {
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
                col.innerHTML = `<div class="card"><img src="${e.target.result}" class="card-img-top img-fluid" style="height:150px; object-fit:cover;"></div>`;
                preview.innerHTML = "";
                preview.appendChild(col);
            };
            reader.readAsDataURL(file);
        } else {
            toastr.error("Only .webp images are allowed!", 'Error');
            input.value = "";
        }
    });

    document.getElementById('cityPageSetting').addEventListener('submit', function(event) {
        tinymce.triggerSave();
        event.preventDefault();
        $('#update-page-setting-btn').prop('disabled', true);
        let isError = 0;
        let description = document.getElementById('city_page_about');
        let author = document.getElementById('city_author_name').value.trim();

if (!author) {
    $('#update-page-setting-btn').prop('disabled', false);
    $('#city_author_name').addClass('is-invalid');

    toastr.error('Author name is required!', 'Error');
    return;
} else {
    $('#city_author_name').removeClass('is-invalid');
}
        if (!description.value.trim()) {
            $('#update-page-setting-btn').prop('disabled', false);
            isError = 1;
            $('#city-page-about-error').removeClass('d-none');
            $('#city-page-about-error').text('Page about required');
            $('html, body').animate({ scrollTop: $("#city-page-about-error").offset().top - 100 }, 600);
        }
        if (!isError) {
            $('#city-page-about-error').addClass('d-none');
            this.submit();
        }
    });

    $(document).on('click', '.city-details', function() {
        let btn = $(this);
        let dataUrl = $(this).data('url').trim();
        btn.find('.spinner-border').removeClass('d-none');
        btn.find('.icon').addClass('d-none');
        $.ajax({
            type: "GET",
            dataType: "json",
            url: dataUrl,
            success: function(data) {
                $('#page_name').text(data.name + (data.is_top_trending ? ' (Top Trending City)' : ''));
                if (data.details != null) {
                    $('#page-title').append(data.details.title);
                    $('#page-sub-title').append(data.details.sub_title);
                    $('#page-about').html(data.details.about);
                    $('#page-banner-image').attr('src', s3BaseUrl + data.details.banner_image);
                }
                $('#viewPageSettingModal').modal('show');
            },
            error: function() {
                Swal.fire({ icon: 'error', title: 'Error', text: 'An error occurred while fetching city details.' });
            },
            complete: function() {
                btn.find('.spinner-border').addClass('d-none');
                btn.find('.icon').removeClass('d-none');
            }
        });
    });

    $(document).on('click', '.add-country-details', function() {
        let btn = $(this);
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
                if (data.details != null) {
                    $('#country_page_title').val(data.details.title);
                    $('#country_page_sub_title').val(data.details.sub_title);
                    $('#country_page_banner_alt').val(data.details.banner_image_alt);
                    setTinyContent('country_page_about', data.details.about || '');
                    $('#galleryPreviewCountry').html(`<div class="card mb-3"><img src="${s3BaseUrl+data.details.banner_image}" class="card-img-top img-fluid" style="height:150px; object-fit:cover;"></div>`);
                    $('#country_page_banner').prop('required', false);
                }
                if (typeof window.populateImageLicenseBlock === 'function') {
                    window.populateImageLicenseBlock('banner_license', data.banner_license);
                }
                $('#country_author_name').val(data.details?.author_name ?? '');
                $('#countryPageSetting').attr('action', dataUrl);
                $('#countryPageSettingModal').modal('show');
            },
            error: function() {
                Swal.fire({ icon: 'error', title: 'Error', text: 'An error occurred while fetching country details.' });
            },
            complete: function() {
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
                col.innerHTML = `<div class="card"><img src="${e.target.result}" class="card-img-top img-fluid" style="height:150px; object-fit:cover;"></div>`;
                preview.innerHTML = "";
                preview.appendChild(col);
            };
            reader.readAsDataURL(file);
        } else {
            toastr.error("Only .webp images are allowed!", 'Error');
            input.value = "";
        }
    });

    document.getElementById('countryPageSetting').addEventListener('submit', function(event) {
        tinymce.triggerSave();
        event.preventDefault();
        $('#update-page-setting-btn').prop('disabled', true);
        let isError = 0;
        let author = document.getElementById('country_author_name').value.trim();

        if (!author) {
            $('#update-country-page-setting-btn').prop('disabled', false);
            toastr.error('Author name is required!', 'Error');
            return;
        }
        let description = document.getElementById('country_page_about');
        
        if (!description.value.trim()) {
            $('#update-country-page-setting-btn').prop('disabled', false);
            isError = 1;
            $('#country-page-about-error').removeClass('d-none');
            $('#country-page-about-error').text('Page about required');
            $('html, body').animate({ scrollTop: $("#country-page-about-error").offset().top - 100 }, 600);
        }
        if (!isError) {
            $('#country-page-about-error').addClass('d-none');
            this.submit();
        }
    });

    $(document).on('click', '.country-details', function() {
        let btn = $(this);
        let dataUrl = $(this).data('url').trim();
        btn.find('.spinner-border').removeClass('d-none');
        btn.find('.icon').addClass('d-none');
        $.ajax({
            type: "GET",
            dataType: "json",
            url: dataUrl,
            success: function(data) {
                $('#page_name').text(data.name);
                if (data.details != null) {
                    $('#page-title').append(data.details.title);
                    $('#page-sub-title').append(data.details.sub_title);
                    $('#page-about').html(data.details.about);
                    $('#page-banner-image').attr('src', s3BaseUrl + data.details.banner_image);
                }
                $('#viewPageSettingModal').modal('show');
            },
            error: function() {
                Swal.fire({ icon: 'error', title: 'Error', text: 'An error occurred while fetching country details.' });
            },
            complete: function() {
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
        $('#faqForm').attr('action', dataUrl);
        $.ajax({
            type: "GET",
            dataType: "json",
            url: "{{ route('admin.countries.index') }}",
            data: { id: id, faqs: 'list' },
            success: function(data) {
                let faqs = data.country.faqs || [];
                $('#faqModal .modal-title').text(data.country.name + "'s FAQ");
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
                    <tbody>`;
                if (faqs.length > 0) {
                    $.each(faqs, function(index, faq) {
                        body += `<tr class="b-none">
                            <td><input type="text" name="faqs[${faqIndex}][question]" value="${faq.question}" class="form-control" required /></td>
                            <td><textarea name="faqs[${faqIndex}][answer]" class="form-control">${faq.answer ?? ''}</textarea></td>
                            <td><button type="button" class="btn btn-sm btn-outline-danger removeFaqRow"><i class="fas fa-trash"></button></td>
                        </tr>`;
                        faqIndex++;
                    });
                } else {
                    body += `<tr class="b-none">
                        <td><input type="text" name="faqs[${faqIndex}][question]" class="form-control" required/></td>
                        <td><textarea name="faqs[${faqIndex}][answer]" class="form-control"></textarea></td>
                        <td></td>
                    </tr>`;
                    faqIndex++;
                }
                body += `</tbody></table>`;
                $('#faqModal .modal-body').html(body);
                $('#faqModal').modal('show');
            },
            error: function() {
                Swal.fire({ icon: 'error', title: 'Error', text: 'An error occurred while fetching package FAQs.' });
            },
            complete: function() {
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
        let dataUrl = btn.data('url');
        $('#faqForm').attr('action', dataUrl);
        $.ajax({
            type: "GET",
            dataType: "json",
            url: "{{ route('admin.locations.index') }}",
            data: { id: id, faqs: 'list' },
            success: function(data) {
                let faqs = data.location.faqs || [];
                $('#faqModal .modal-title').text(data.location.name + "'s FAQ");
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
                    <tbody>`;
                if (faqs.length > 0) {
                    $.each(faqs, function(index, faq) {
                        body += `<tr class="b-none">
                            <td><input type="text" name="faqs[${faqIndex}][question]" value="${faq.question}" class="form-control" required /></td>
                            <td><textarea name="faqs[${faqIndex}][answer]" class="form-control">${faq.answer ?? ''}</textarea></td>
                            <td><button type="button" class="btn btn-sm btn-outline-danger removeFaqRow"><i class="fas fa-trash"></button></td>
                        </tr>`;
                        faqIndex++;
                    });
                } else {
                    body += `<tr class="b-none">
                        <td><input type="text" name="faqs[${faqIndex}][question]" class="form-control" required/></td>
                        <td><textarea name="faqs[${faqIndex}][answer]" class="form-control"></textarea></td>
                        <td></td>
                    </tr>`;
                    faqIndex++;
                }
                body += `</tbody></table>`;
                $('#faqModal .modal-body').html(body);
                $('#faqModal').modal('show');
            },
            error: function() {
                Swal.fire({ icon: 'error', title: 'Error', text: 'An error occurred while fetching package FAQs.' });
            },
            complete: function() {
                btn.find('.spinner-border').addClass('d-none');
                btn.find('.icon').removeClass('d-none');
            }
        });
    });

    $(document).on('click', '#addFaqRow', function() {
        let row = `<tr class="b-none">
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
        let dataUrl = $(this).data('url').trim();
        let dataupUrl = $(this).data('upurl').trim();
        btn.find('.spinner-border').removeClass('d-none');
        btn.find('.icon').addClass('d-none');
        $.ajax({
            type: "GET",
            dataType: "json",
            url: dataUrl,
            success: function(data) {
                $('#country-meta-title').text('# ' + data.name + '-Meta Info');
                if (data.meta != null) {
                    $('#country_meta_title').val(data.meta.meta_title);
                    $('#country_meta_description').val(data.meta.meta_description);
                    $('#country_meta_keywords').val(data.meta.meta_keywords);
                    $('#country_h1_heading').val(data.meta.h1_heading);
                    $('#country_meta_details').val(data.meta.meta_details);
                }
                $('#countryMeta').attr('action', dataupUrl);
                $('#countryMetaModal').modal('show');
            },
            error: function() {
                Swal.fire({ icon: 'error', title: 'Error', text: 'An error occurred while fetching city details.' });
            },
            complete: function() {
                btn.find('.spinner-border').addClass('d-none');
                btn.find('.icon').removeClass('d-none');
            }
        });
    });

    $(document).on('click', '.location-meta', function() {
        let btn = $(this);
        let dataUrl = $(this).data('url').trim();
        let dataupUrl = $(this).data('upurl').trim();
        btn.find('.spinner-border').removeClass('d-none');
        btn.find('.icon').addClass('d-none');
        $.ajax({
            type: "GET",
            dataType: "json",
            url: dataUrl,
            success: function(data) {
                $('#location-title').text('# ' + data.name + '-Meta Info');
                if (data.meta != null) {
                    $('#location_meta_title').val(data.meta.meta_title);
                    $('#location_meta_description').val(data.meta.meta_description);
                    $('#location_meta_keywords').val(data.meta.meta_keywords);
                    $('#location_h1_heading').val(data.meta.h1_heading);
                    $('#location_meta_details').val(data.meta.meta_details);
                }
                $('#locationMeta').attr('action', dataupUrl);
                $('#locationMetaModal').modal('show');
            },
            error: function() {
                Swal.fire({ icon: 'error', title: 'Error', text: 'An error occurred while fetching city details.' });
            },
            complete: function() {
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
            data: {'status': menu_status},
            success: function(data) {
                Swal.fire({ icon: 'success', title: 'Success', text: data.message });
            },
            error: function() {
                Swal.fire({ icon: 'error', title: 'Error', text: 'An error occurred while processing your request.' });
            }
        });
    });

    $(document).on("click", ".delete-menu", function(e) {
        e.preventDefault();
        let itemUrl = $(this).data("url");
        let row = $(this).closest(".menu-row");
        Swal.fire({
            title: "Are you sure?", text: "This item will be deleted!", icon: "warning",
            showCancelButton: true, confirmButtonColor: "#e3342f", cancelButtonColor: "#6c757d",
            confirmButtonText: "Yes, delete it", cancelButtonText: "Cancel",
            customClass: { popup: 'rounded-2xl shadow-lg', confirmButton: 'px-4 py-2 text-white', cancelButton: 'px-4 py-2 text-white' }
        }).then((result) => {
            if (result.isConfirmed) {
                let btn = $(this);
                btn.find('.spinner-border').removeClass('d-none');
                btn.find('.icon').addClass('d-none');
                $.ajax({
                    type: "DELETE", url: itemUrl,
                    success: function(res) {
                        if (res.status) {
                            Swal.fire("Deleted!", "The item has been deleted successfully.", "success");
                            row.remove();
                        }
                    },
                    error: function() { toastr.error('Menu Location not deleted!', 'Failed'); },
                    complete: function() {
                        btn.find('.spinner-border').addClass('d-none');
                        btn.find('.icon').removeClass('d-none');
                    }
                });
            }
        });
    });
});

// Delete country
$(document).on("click", ".delete-country", function(e) {
    e.preventDefault();
    let itemUrl = $(this).data("url");
    let row = $(this).closest(".location-row");
    Swal.fire({
        title: "Are you sure?", text: "This item will be deleted!", icon: "warning",
        showCancelButton: true, confirmButtonColor: "#e3342f", cancelButtonColor: "#6c757d",
        confirmButtonText: "Yes, delete it", cancelButtonText: "Cancel",
        customClass: { popup: 'rounded-2xl shadow-lg', confirmButton: 'px-4 py-2 text-white', cancelButton: 'px-4 py-2 text-white' }
    }).then((result) => {
        if (result.isConfirmed) {
            let btn = $(this);
            btn.find('.spinner-border').removeClass('d-none');
            btn.find('.icon').addClass('d-none');
            $.ajax({
                type: "DELETE", url: itemUrl,
                success: function(res) {
                    if (res.status) {
                        Swal.fire("Deleted!", "The item has been deleted successfully.", "success");
                        row.remove();
                    }
                },
                error: function() { toastr.error('Location not deleted!', 'Failed'); },
                complete: function() {
                    btn.find('.spinner-border').addClass('d-none');
                    btn.find('.icon').removeClass('d-none');
                }
            });
        }
    });
});

// Delete city
$(document).on("click", ".delete-city", function(e) {
    e.preventDefault();
    let itemUrl = $(this).data("url");
    let row = $(this).closest(".city-row");
    Swal.fire({
        title: "Are you sure?", text: "This item will be deleted!", icon: "warning",
        showCancelButton: true, confirmButtonColor: "#e3342f", cancelButtonColor: "#6c757d",
        confirmButtonText: "Yes, delete it", cancelButtonText: "Cancel",
        customClass: { popup: 'rounded-2xl shadow-lg', confirmButton: 'px-4 py-2 text-white', cancelButton: 'px-4 py-2 text-white' }
    }).then((result) => {
        if (result.isConfirmed) {
            let btn = $(this);
            btn.find('.spinner-border').removeClass('d-none');
            btn.find('.icon').addClass('d-none');
            $.ajax({
                type: "DELETE", url: itemUrl,
                success: function(res) {
                    if (res.status) {
                        Swal.fire("Deleted!", "The item has been deleted successfully.", "success");
                        row.remove();
                    }
                },
                error: function() { toastr.error('Location not deleted!', 'Failed'); },
                complete: function() {
                    btn.find('.spinner-border').addClass('d-none');
                    btn.find('.icon').removeClass('d-none');
                }
            });
        }
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
        const fullUrl = new URL(url, window.location.origin);
        if (keyword) {
            fullUrl.searchParams.set('country', keyword);
        } else {
            fullUrl.searchParams.delete('country');
        }
        window.history.pushState({}, '', fullUrl.toString());
        $.ajax({
            url: url, type: 'GET', data: { country: keyword }, dataType: 'html',
            beforeSend: function() { countryList.addClass('loading'); },
            success: function(response) { countryList.html(response); },
            complete: function() { countryList.removeClass('loading'); },
            error: function(xhr, status, error) { console.error('Error:', error); }
        });
    }

    countrySearchInput.on('input', function() {
        clearTimeout(timer);
        timer = setTimeout(() => { fetchCountries(); }, 300);
    });
});

document.addEventListener('DOMContentLoaded', function () {
    const searchInput = $('#citySearch');
    const cityList = $('#cityList');
    let timer;

    function fetchCities(url = "{{ route('admin.locations.index') }}") {
        const keyword = searchInput.val().trim();
        const fullUrl = new URL(url, window.location.origin);
        if (keyword) {
            fullUrl.searchParams.set('city', keyword);
        } else {
            fullUrl.searchParams.delete('city');
        }
        window.history.pushState({}, '', fullUrl.toString());
        $.ajax({
            url: url, type: 'GET', data: { city: keyword }, dataType: 'html',
            beforeSend: function() { cityList.addClass('loading'); },
            success: function(response) { cityList.html(response); },
            complete: function() { cityList.removeClass('loading'); },
            error: function(xhr, status, error) { console.error('Error:', error); }
        });
    }

    searchInput.on('input', function() {
        clearTimeout(timer);
        timer = setTimeout(() => { fetchCities(); }, 300);
    });
});

// Regions
$(document).on('click', '.add-region-details', function () {
    let btn = $(this);
    let fetchUrl = btn.data('eurl');
    let updateUrl = btn.data('url');
    btn.find('.spinner-border').removeClass('d-none');
    btn.find('.icon').addClass('d-none');
    $.get(fetchUrl, function (data) {
        $('#city-title').text(data.name);
        $('#city_author_name').val(data.details?.author_name ?? '');
        if (data.details) {
            $('#city_page_title').val(data.details.title);
            $('#city_page_sub_title').val(data.details.sub_title);
            $('#city_page_banner_alt').val(data.details.banner_image_alt);
            setTinyContent('city_page_about', data.details.about);
            $('#galleryPreview').html(`<img src="${s3BaseUrl + data.details.banner_image}" class="img-fluid rounded" style="height:150px;object-fit:cover;">`);
            $('#city_page_banner').prop('required', false);
        }
        
        $('#cityPageSetting').attr('action', updateUrl);
        $('#cityPageSettingModal').modal('show');
    }).always(() => {
        btn.find('.spinner-border').addClass('d-none');
        btn.find('.icon').removeClass('d-none');
    });
});

// Regions FAQ
$(document).on('click', '.region-faqs', function () {
    let btn = $(this);
    let id = btn.data('id');
    let actionUrl = btn.data('url');
    btn.find('.spinner-border').removeClass('d-none');
    btn.find('.icon').addClass('d-none');
    $('#faqForm').attr('action', actionUrl);
    $.get("{{ route('admin.regions.index') }}", { id, faqs: true }, function (res) {
        let faqs = res.region.faqs || [];
        $('#faqModal .modal-title').text(res.region.name + "'s FAQ");
        renderFaqTable(faqs, res.region.faq_title);
        $('#faqModal').modal('show');
    }).always(() => {
        btn.find('.spinner-border').addClass('d-none');
        btn.find('.icon').removeClass('d-none');
    });
});

// Regions Meta
$(document).on('click', '.region-meta', function () {
    let btn = $(this);
    let fetchUrl = btn.data('url');
    let updateUrl = btn.data('upurl');
    btn.find('.spinner-border').removeClass('d-none');
    btn.find('.icon').addClass('d-none');
    $.get(fetchUrl, function (data) {
        $('#location-title').text('# ' + data.name + ' - Meta');
        if (data.meta) {
            $('#location_meta_title').val(data.meta.meta_title);
            $('#location_meta_description').val(data.meta.meta_description);
            $('#location_meta_keywords').val(data.meta.meta_keywords);
            $('#location_h1_heading').val(data.meta.h1_heading);
            $('#location_meta_details').val(data.meta.meta_details);
        }
        $('#locationMeta').attr('action', updateUrl);
        $('#locationMetaModal').modal('show');
    }).always(() => {
        btn.find('.spinner-border').addClass('d-none');
        btn.find('.icon').removeClass('d-none');
    });
});

$('#cityPageSettingModal').on('hidden.bs.modal', function () {
    
    if (tinymce.get('city_page_about')) {
        tinymce.get('city_page_about').remove();
    }
    $('#city_author_name').val('');
    $('#cityPageSetting')[0].reset();
    $('#galleryPreview').html('');
});

function renderFaqTable(faqs = [], faqTitle = '') {
    let faqIndex = 0;
    let body = `
        <div class="col-md-12">
            <div class="mb-3">
                <label class="form-label">Faq Title<span class="required-text">*</span></label>
                <input value="${faqTitle ?? ''}" class="form-control" name="faq_title" required>
            </div>
        </div>
        <table class="table" id="faqTable">
            <thead>
                <tr>
                    <th>Question<span class="required-text">*</span></th>
                    <th>Answer<span class="required-text">*</span></th>
                    <th><button type="button" class="btn btn-sm btn-outline-success" id="addFaqRow"><i class="fas fa-plus"></i></button></th>
                </tr>
            </thead>
            <tbody>`;
    if (faqs.length > 0) {
        faqs.forEach(faq => {
            body += `<tr>
                <td><input type="text" name="faqs[${faqIndex}][question]" value="${faq.question ?? ''}" class="form-control" required></td>
                <td><textarea name="faqs[${faqIndex}][answer]" class="form-control">${faq.answer ?? ''}</textarea></td>
                <td><button type="button" class="btn btn-sm btn-outline-danger removeFaqRow"><i class="fas fa-trash"></i></button></td>
            </tr>`;
            faqIndex++;
        });
    } else {
        body += `<tr>
            <td><input type="text" name="faqs[${faqIndex}][question]" class="form-control" required></td>
            <td><textarea name="faqs[${faqIndex}][answer]" class="form-control"></textarea></td>
            <td></td>
        </tr>`;
    }
    body += `</tbody></table>`;
    $('#faqModal .modal-body').html(body);
}

function reindexFaqTable() {
    let index = 0;
    $('#faqTable tbody tr').each(function () {
        $(this).find('input[name*="[question]"]').attr('name', `faqs[${index}][question]`);
        $(this).find('textarea[name*="[answer]"]').attr('name', `faqs[${index}][answer]`);
        index++;
    });
}

$('#faqForm').on('submit', function () {
    reindexFaqTable();
});

// =============================================
// ADD CITY - country change → load states
// =============================================
$(document).on('change', '#country_id_city', function () {
    let countryId = $(this).val();
    let selectedText = $(this).find('option:selected').text().toLowerCase();
    let isIndia = selectedText.includes('india');
    
    $('#state_block').hide();
    $('#state_id').html('<option value="">Choose state...</option>');
    $('#state_id').prop('required', false); // pehle required hata do
    
    if (countryId) {
        $.ajax({
            url: "{{ route('admin.get.states') }}",
            type: "GET",
            data: { country_id: countryId },
            success: function (res) {
                if (res.states.length > 0 && isIndia) {
                    $('#state_block').show();
                    $('#state_id').prop('required', true);
                    $.each(res.states, function (key, state) {
                        $('#state_id').append(`<option value="${state.id}">${state.name}</option>`);
                    });
                }
            }
        });
    }
});

// =============================================
// EDIT CITY - country change → load states
// =============================================
$(document).on('change', '#country_id_city_edit', function () {
    let countryId = $(this).val();
    let selectedText = $(this).find('option:selected').text().toLowerCase();
    let isIndia = selectedText.includes('india');
    
    $('#state_block_edit').hide();
    $('#state_id_edit').html('<option value="">Choose state...</option>');
    $('#state_id_edit').prop('required', false); // pehle required hata do
    
    if (countryId) {
        $.ajax({
            url: "{{ route('admin.get.states') }}",
            type: "GET",
            data: { country_id: countryId },
            success: function (res) {
                if (res.states.length > 0 && isIndia) {
                    $('#state_block_edit').show();
                    $('#state_id_edit').prop('required', true); // sirf India ke liye required
                    $.each(res.states, function (key, state) {
                        $('#state_id_edit').append(`<option value="${state.id}">${state.name}</option>`);
                    });
                }
            }
        });
    }
});
</script>

@endsection
