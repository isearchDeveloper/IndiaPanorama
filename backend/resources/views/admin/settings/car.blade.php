@section('title','Car')
@extends('layouts.app')
@section('content')
<style>
    /* Section card headers on this page were rendering at Bootstrap's default h5 size/weight,
       making them look like oversized page titles instead of section labels. */
    .card-header h5.mb-0 {
        font-size: 15px;
        font-weight: 600;
    }
</style>
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h2">
                    <i class="fas fa-cog me-2"></i>Car Page Settings
                </h1>
            </div>
        </div>
    </div>
    <!-- Action Buttons -->
    <div class="row mb-4">
        <div class="col-12 d-flex flex-wrap gap-2">
            <button class="btn text-white" style="background:#2563eb;" onclick="addCarCategory();">
                <i class="fas fa-plus me-2"></i>Add Car Category
            </button>
            <button class="btn text-white" style="background:#16a34a;" onclick="addCar();">
                <i class="fas fa-plus me-2"></i>Add Car
            </button>
            <button class="btn text-white" style="background:#7c3aed;" onclick="addRoute();">
                <i class="fas fa-plus me-2"></i>Add Route
            </button>
            <button class="btn text-white" style="background:#0891b2;" onclick="addCity();">
                <i class="fas fa-plus me-2"></i>Add City
            </button>
            <button class="btn text-white" style="background:#b45309;" onclick="addPackage();">
                <i class="fas fa-plus me-2"></i>Add Package
            </button>
            <button class="btn text-white" style="background:#be123c;" onclick="addDestination();">
                <i class="fas fa-plus me-2"></i>Add Destination
            </button>
        </div>
    </div>

    <!-- Inquiry Type Tabs -->
    <div class="row mb-3">
        <div class="col-12">
            <ul class="nav nav-pills gap-2">
                <li class="nav-item">
                    <a class="nav-link tab_link {{ (session('active_tab') && session('active_tab') == 'car_content') ? 'active' :'' }} {{ !session('active_tab') ? 'active' : ''  }}" data-tab="car_content_tab">
                        <i class="fas fa-align-left me-1"></i>Page Setting
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link tab_link {{ (session('active_tab') && session('active_tab') == 'category_list') ? 'active' :'' }}" data-tab="car_category_tab">
                        <i class="fas fa-list me-1"></i>Car Categories
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link tab_link {{ (session('active_tab') && session('active_tab') == 'car_list') ? 'active' :'' }}" data-tab="car_list_tab">
                        <i class="fas fa-car me-1"></i>Car List
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link tab_link {{ (session('active_tab') && session('active_tab') == 'route_list') ? 'active' :'' }}" data-tab="route_list_tab">
                        <i class="fas fa-road me-1"></i>Route List
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link tab_link {{ (session('active_tab') && session('active_tab') == 'city_list') ? 'active' :'' }}" data-tab="city_list_tab">
                        <i class="fas fa-location-dot me-1"></i>City List
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link tab_link {{ (session('active_tab') && session('active_tab') == 'package_list') ? 'active' :'' }}" data-tab="package_list_tab">
                        <i class="fas fa-suitcase me-1"></i>Package List
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link tab_link {{ (session('active_tab') && session('active_tab') == 'destination_list') ? 'active' :'' }}" data-tab="destination_list_tab">
                        <i class="fas fa-map-marker-alt me-1"></i>Destination List
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link tab_link {{ (session('active_tab') && session('active_tab') == 'global_setting') ? 'active' :'' }}" data-tab="global_setting_tab">
                        <i class="fas fa-share-alt me-1"></i>Global Setting
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="row tab_details {{ (session('active_tab') && session('active_tab') != '' && session('active_tab') != 'car_content') ? 'd-none' :'' }}" id="car_content_tab">
        <div class="col-12">
            <div class="card">
                <div class="card-header text-white" style="background:#2563eb;">
                    <h5 class="mb-0">
                        <i class="fas fa-cog me-2"></i>Page Details
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th>Image</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($page_details)
                                <tr>
                                    <td>
                                        <strong>{{ $page_details->title }}</strong>
                                    </td>
                                    <td>
                                        {!! \Illuminate\Support\Str::words($page_details->description, 10, '...') !!}
                                    </td>
                                    <td>
                                        <div class="card mb-3">
                                            <img id="page-banner-image" src="{{ storage_link($page_details->banner_image) }}" class="card-img-top img-fluid" style="height:50px; object-fit:cover;">
                                        </div>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editPageModal" title="Edit Page — title, description &amp; banner image">
                                            <i class="fas fa-edit icon"></i>
                                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                        </button>
                                        <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#carContentSettingModal" title="Page Content — long description">
                                            <i class="fas fa-cog icon"></i>
                                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                        </button>
                                        <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#carGalleryModal" title="Gallery — photos shown in the About section">
                                            <i class="fas fa-images icon"></i>
                                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                        </button>
                                        <button class="btn btn-sm btn-outline-primary page-faqs" data-id="{{ $page_details->id }}" data-url="{{ route('admin.page.faqUpdate', $page_details->id) }}" title="FAQs shown at the bottom of the page">
                                            <i class="fa fa-question-circle icon"></i>
                                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                        </button>
                                        <button class="btn btn-sm btn-outline-primary page-meta" data-id="{{ $page_details->id }}" data-url="{{ route('admin.page-meta.show.meta', $page_details->id) }}" title="SEO Meta Tags — not visible on the page, used for search engines">
                                            <i class="fas fa-globe icon"></i>
                                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                        </button>
                                    </td>
                                </tr>
                                @else
                                <tr><td colspan="4" class="text-muted text-center">Page record not found (ID 6). Please seed the pages table.</td></tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="carGalleryModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header text-white" style="background:#2563eb;">
                    <h5 class="modal-title"><i class="fas fa-images me-2"></i>About Gallery Images</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @error('gallery_images')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                    @error('gallery_images.*.path')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                    <div class="row mb-3" id="galleryImagesWrapper">
                        @foreach($car_content->galleryImages as $image)
                        <div class="col-md-3 mb-3" id="gallery-image-{{ $image->id }}">
                            <div class="gallery-img-wrap">
                                <img src="{{ storage_link($image->image) }}" class="img-fluid w-100" style="height:120px;object-fit:cover;border-radius:4px;">
                                <button type="button" class="gallery-remove-btn delete-gallery-image" data-id="{{ $image->id }}" data-url="{{ route('admin.car-rental-content.gallery.delete', $image->id) }}" title="Remove"><i class="fas fa-times"></i></button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <form id="addGalleryImageForm" method="POST" action="{{ route('admin.car-rental-content.gallery.add') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" class="form-control" name="gallery_title" value="{{ $car_content->gallery_title }}" placeholder="Gallery">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="gallery_description" rows="2">{{ $car_content->gallery_description }}</textarea>
                        </div>
                        <hr>
                        <div class="mb-2">
                            <x-media-gallery-picker name="gallery_images" picker-id="car_rental_gallery_add" label="" folder="car-rental" />
                        </div>
                        <button type="submit" class="btn btn-success"><i class="fas fa-save me-1"></i>Save</button>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="carContentSettingModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header text-white" style="background:#2563eb;">
                    <h5 class="modal-title"><i class="fas fa-cog me-2"></i>Page Content Settings</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="carContentSettingForm" method="POST" action="{{ route('admin.car-rental-content.text') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Long Description</label>
                            <textarea class="form-control tinymce" name="short_description" id="car_short_description" rows="4">{{ $car_content->short_description }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-warning text-white"><i class="fas fa-save me-1"></i>Save</button>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="row tab_details {{ (session('active_tab') && session('active_tab') == 'category_list') ? '' :'d-none' }}" id="car_category_tab">
        <div class="col-12">
            <div class="card">
                <div class="card-header text-white" style="background:#2563eb;">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>Car Categories @if(count($categories) > 0 )({{count($categories)}}) @endif
                    </h5>

                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($categories as $category)
                                <tr>
                                    <td>
                                        <strong class="text-primary d-block">{{ $category->name }}</strong>
                                    </td>
                                    <td>
                                        <input id="status_{{$category->id }}" type="checkbox" data-id="{{$category->id }}" data-url="{{ route('admin.car-categories.update',$category->id) }}" class="js-switch category-status" <?php echo $category->is_active == 1 ? 'checked' : '' ?>>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-success category-edit" data-id="{{$category->id}}" data-url="{{ route('admin.car-categories.show',$category->id) }}" data-udurl="{{ route('admin.car-categories.update',$category->id) }}">
                                            <i class="fas fa-edit icon"></i>
                                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger delete-category" data-id="{{ $category->id }}" data-url="{{ route('admin.car-categories.destroy',$category->id) }}">
                                            <i class="fas fa-trash icon"></i>
                                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row tab_details {{ (session('active_tab') && session('active_tab') == 'car_list') ? '' :'d-none' }}" id="car_list_tab">
        <div class="col-12">
            <div class="card">
                <div class="card-header text-white" style="background:#16a34a;">
                    <h5 class="mb-0">
                        <i class="fas fa-car me-2"></i>Cars @if(count($cars) > 0 )({{count($cars)}}) @endif
                    </h5>
                </div>
                <div class="card-body">

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Fuel Type</th>
                                    <th>Image</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cars as $car)
                                <tr>
                                    <td>
                                        <strong class="text-primary d-block">{{ $car->title }}</strong>
                                        <small class="text-muted">
                                            Seats : {{ $car->seats }}
                                        </small>
                                    </td>
                                    <td>
                                        {{ $car->category?->name ?? '—' }}
                                    </td>
                                    <td>
                                        {{ $car->fuel_type}}
                                    </td>
                                    <td>
                                        <div class="card mb-3">
                                            <img id="page-banner-image" src="{{ storage_link($car->primary_image) }}" class="card-img-top img-fluid" style="height:50px; object-fit:cover;">
                                        </div>
                                    </td>
                                    <td>
                                        <input id="status_{{$car->id }}" type="checkbox" data-id="{{$car->id }}" data-url="{{ route('admin.cars.update',$car->id) }}" class="js-switch car-status" <?php echo $car->is_active == 1 ? 'checked' : '' ?>>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-success car-edit" data-id="{{$car->id}}" data-url="{{ route('admin.cars.show',$car->id) }}" data-udurl="{{ route('admin.cars.update',$car->id) }}">
                                            <i class="fas fa-edit icon"></i>
                                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                        </button>
                                        <button class="btn btn-sm btn-outline-primary car-settings" data-id="{{$car->id}}" data-name="{{$car->title}}" data-udurl="{{ route('admin.cars.update',$car->id) }}" data-highlighturl="{{ route('admin.cars.highlight-tags',$car->id) }}" data-galleryaddurl="{{ route('admin.cars.gallery.add',$car->id) }}" title="Page Banner, Short Description, Gallery, Highlights &amp; Vehicle Specifications">
                                            <i class="fas fa-cog icon"></i>
                                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                        </button>
                                        <button class="btn btn-sm btn-outline-warning car-amenities" data-id="{{$car->id}}" data-name="{{$car->title}}" data-udurl="{{ route('admin.cars.amenities',$car->id) }}" title="Features &amp; Amenities shown on this car's detail page">
                                            <i class="fas fa-list-check icon"></i>
                                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger delete-car" data-id="{{ $car->id }}" data-url="{{ route('admin.cars.destroy',$car->id) }}">
                                            <i class="fas fa-trash icon"></i>
                                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row tab_details {{ (session('active_tab') && session('active_tab') == 'route_list') ? '' :'d-none' }}" id="route_list_tab">
        <div class="col-12">
            <div class="card">
                <div class="card-header text-white" style="background:#7c3aed;">
                    <h5 class="mb-0">
                        <i class="fas fa-road me-2"></i>Routes ({{ $car_routes->total() }})
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <input type="text" id="routeSearch" class="form-control" placeholder="Search Route (Source / Destination / URL)..." value="{{ request('route_search') }}">
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th>Source City</th>
                                    <th>Destination City</th>
                                    <th>Url</th>
                                    <th>Status</th>
                                    <th>Popular?</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="routeTableBody">
                                @foreach($car_routes as $route)
                                <tr>
                                    <td>
                                        {{ $route->from_location }}
                                    </td>
                                    <td>
                                        {{ $route->to_location }}
                                    </td>
                                    <td>
                                        {{ $route->slug }}
                                    </td>
                                    <td>
                                        <input id="status_{{$route->id }}" type="checkbox" data-id="{{$route->id }}" data-url="{{ route('admin.car-routes.update',$route->id) }}" class="js-switch route-status" <?php echo $route->is_active == 1 ? 'checked' : '' ?>>
                                    </td>
                                    <td>
                                        <input id="popular_{{$route->id }}" type="checkbox" data-id="{{$route->id }}" data-url="{{ route('admin.car-routes.update',$route->id) }}" class="js-switch route-popular" <?php echo $route->is_popular == 1 ? 'checked' : '' ?>>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-success route-edit" data-id="{{$route->id}}" data-url="{{ route('admin.car-routes.show',$route->id) }}" data-udurl="{{ route('admin.car-routes.update',$route->id) }}" title="Edit Route — cities, page title, banner &amp; short description">
                                            <i class="fas fa-edit icon"></i>
                                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                        </button>
                                        <button class="btn btn-sm btn-outline-primary add-route-page-details" data-id="{{$route->id}}" data-url="{{ route('admin.car-routes.update',$route->id) }}" data-eurl="{{ route('admin.car-routes-page.show.page',$route->id) }}" title="About Section shown further down this route's page">
                                            <i class="fas fa-cog icon"></i>
                                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                        </button>
                                        <button class="btn btn-sm btn-outline-success route-car" data-id="{{$route->id}}" data-url="{{ route('admin.car-routes.cars',$route->id) }}" title="Choose which cars/vehicles show on this route's fleet section">
                                            <i class="fas fa-car icon"></i>
                                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                        </button>
                                        <button class="btn btn-sm btn-outline-primary route-faqs" data-id="{{$route->id}}" data-url="{{ route('admin.car-routes.faqUpdate',$route->id) }}" title="FAQs shown at the bottom of this route's page">
                                            <i class="fa fa-question-circle icon"></i>
                                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                        </button>
                                        <button class="btn btn-sm btn-outline-primary route-highlights" data-id="{{$route->id}}" data-url="{{ route('admin.car-routes.highlightsUpdate',$route->id) }}" title="Route Highlights — the highlight cards (e.g. waterfalls, viewpoints) shown on this route's page">
                                            <i class="fa fa-map-signs icon"></i>
                                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                        </button>
                                        <button class="btn btn-sm btn-outline-primary car-route-meta" data-id="{{$route->id}}" data-upurl="{{ route('admin.car-routes.update',$route->id) }}" data-url="{{ route('admin.car-routes-meta.show.meta',$route->id) }}" title="SEO Meta Tags — not visible on the page, used for search engines">
                                            <i class="fa fa-globe icon"></i>
                                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger delete-route" data-id="{{ $route->id }}" data-url="{{ route('admin.car-routes.destroy',$route->id) }}" title="Delete Route">
                                            <i class="fas fa-trash icon"></i>
                                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                        </button>

                                        <a href="{{ config('app.frontend_url') }}/car-rental/{{ $route->slug }}" title="preview" class="btn btn-sm btn-outline-primary" target="_blank">
                                            <i class="fa fa-tv" aria-hidden="true"></i>
                                        </a>



                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div id="routePagination">
                        @include('admin.common.pagination', [
                        'paginator' => $car_routes->appends(['active_tab' => 'route_list', 'route_search' => request('route_search')])
                        ])
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row tab_details {{ (session('active_tab') && session('active_tab') == 'city_list') ? '' :'d-none' }}" id="city_list_tab">
        <div class="col-12">
            <div class="card">
                <div class="card-header text-white" style="background:#0891b2;">
                    <h5 class="mb-0">
                        <i class="fas fa-location-dot me-2"></i>City ({{ $car_city->total() }})
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <input type="text" id="citySearch" class="form-control" placeholder="Search City..." value="{{ request('city_search') }}">
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th>City</th>
                                    <th>Url</th>
                                    <th>Status</th>
                                    <th>Popular?</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="cityTableBody">
                                @foreach($car_city as $city)
                                <tr>
                                    <td>
                                        {{ $city->location }}
                                    </td>
                                    <td>
                                        car-rental/{{ $city->slug }}
                                    </td>
                                    <td>
                                        <input id="status_{{$city->id }}" type="checkbox" data-id="{{$city->id }}" data-url="{{ route('admin.car-city.update',$city->id) }}" class="js-switch city-status" <?php echo $city->is_active == 1 ? 'checked' : '' ?>>
                                    </td>
                                    <td>
                                        <input id="popular_{{$city->id }}" type="checkbox" data-id="{{$city->id }}" data-url="{{ route('admin.car-city.update',$city->id) }}" class="js-switch city-popular" <?php echo $city->is_popular == 1 ? 'checked' : '' ?>>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-success city-edit" data-id="{{$city->id}}" data-url="{{ route('admin.car-city.show',$city->id) }}" data-udurl="{{ route('admin.car-city.update',$city->id) }}" title="Edit City — location, thumbnail &amp; page title">
                                            <i class="fas fa-edit icon"></i>
                                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                        </button>
                                        <button class="btn btn-sm btn-outline-primary add-city-page-details" data-id="{{$city->id}}" data-url="{{ route('admin.car-city.show',$city->id) }}" data-udurl="{{ route('admin.car-city.update',$city->id) }}" title="Page Settings — short description &amp; Why Choose Us">
                                            <i class="fas fa-cog icon"></i>
                                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                        </button>
                                        <button class="btn btn-sm btn-outline-success city-gallery" data-id="{{$city->id}}" data-name="{{$city->location}}" data-geturl="{{ route('admin.car.city.features-benefits') }}?id={{$city->id}}" data-addurl="{{ route('admin.car-city.gallery.add', $city->id) }}" title="Gallery — photos shown on this city's page">
                                            <i class="fas fa-images icon"></i>
                                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                        </button>
                                        <button class="btn btn-sm btn-outline-warning city-features-benefits" data-id="{{$city->id}}" data-name="{{$city->location}}" data-geturl="{{ route('admin.car.city.features-benefits') }}?id={{$city->id}}" data-featuresurl="{{ route('admin.car-city.features', $city->id) }}" data-benefitsurl="{{ route('admin.car-city.benefits', $city->id) }}" title="Features &amp; Benefits shown on this city's page">
                                            <i class="fas fa-list-check icon"></i>
                                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                        </button>
                                        <button class="btn btn-sm btn-outline-primary city-highlights" data-id="{{$city->id}}" data-url="{{ route('admin.car-city.highlightsUpdate',$city->id) }}" title="City Highlights — the highlight cards shown on this city's page">
                                            <i class="fas fa-star icon"></i>
                                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                        </button>
                                        <button class="btn btn-sm btn-outline-success city-car" data-id="{{$city->id}}" data-url="{{ route('admin.car-city.cars',$city->id) }}" title="Choose which cars/vehicles show on this city's fleet section">
                                            <i class="fas fa-car icon"></i>
                                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                        </button>
                                        <button class="btn btn-sm btn-outline-primary city-faqs" data-id="{{$city->id}}" data-url="{{ route('admin.car-city.faqUpdate',$city->id) }}" title="FAQs shown at the bottom of this city's page">
                                            <i class="fa fa-question-circle icon"></i>
                                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                        </button>
                                        <button class="btn btn-sm btn-outline-primary car-city-meta" data-id="{{$city->id}}" data-upurl="{{ route('admin.car-city.update',$city->id) }}" data-url="{{ route('admin.car-city-meta.show.meta',$city->id) }}" title="SEO Meta Tags — not visible on the page, used for search engines">
                                            <i class="fa fa-globe icon"></i>
                                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger delete-city" data-id="{{ $city->id }}" data-url="{{ route('admin.car-city.destroy',$city->id) }}" title="Delete City">
                                            <i class="fas fa-trash icon"></i>


                                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                        </button>
                                        <a href="{{ config('app.frontend_url') }}/car-rental/{{ $city->slug }}" title="preview" class="btn btn-sm btn-outline-primary" target="_blank">
                                            <i class="fa fa-tv" aria-hidden="true"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div id="cityPagination">
                        @include('admin.common.pagination', [
                        'paginator' => $car_city->appends(['active_tab' => 'city_list', 'city_search' => request('city_search')])
                        ])
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ======================================================= --}}
    {{-- PACKAGE LIST TAB                                         --}}
    {{-- ======================================================= --}}
    <div class="row tab_details {{ (session('active_tab') && session('active_tab') == 'package_list') ? '' :'d-none' }}" id="package_list_tab">
        <div class="col-12">
            <div class="card">
                <div class="card-header text-white" style="background:#b45309;">
                    <h5 class="mb-0">
                        <i class="fas fa-suitcase me-2"></i>Packages ({{ $car_packages->total() }})
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <input type="text" id="packageSearch" class="form-control" placeholder="Search Package..." value="{{ request('package_search') }}">
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Url</th>
                                    <th>Status</th>
                                    <th>Popular?</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="packageTableBody">
                                @foreach($car_packages as $package)
                                <tr>
                                    <td>{{ $package->name }}</td>
                                    <td>car-rental/{{ $package->slug }}</td>
                                    <td>
                                        <input id="package_status_{{$package->id }}" type="checkbox" data-id="{{$package->id }}" data-url="{{ route('admin.car-packages.update',$package->id) }}" class="js-switch package-status" <?php echo $package->is_active == 1 ? 'checked' : '' ?>>
                                    </td>
                                    <td>
                                        <input id="package_popular_{{$package->id }}" type="checkbox" data-id="{{$package->id }}" data-url="{{ route('admin.car-packages.update',$package->id) }}" class="js-switch package-popular" <?php echo $package->is_popular == 1 ? 'checked' : '' ?>>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-success package-edit" data-id="{{$package->id}}" data-url="{{ route('admin.car-packages.show',$package->id) }}" data-udurl="{{ route('admin.car-packages.update',$package->id) }}" title="Edit Package — name, banner &amp; short description">
                                            <i class="fas fa-edit icon"></i>
                                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                        </button>
                                        <button class="btn btn-sm btn-outline-primary add-package-page-details" data-id="{{$package->id}}" data-url="{{ route('admin.car-packages.update',$package->id) }}" data-eurl="{{ route('admin.car-packages-page.show.page',$package->id) }}" title="About Section shown further down this package's page">
                                            <i class="fas fa-cog icon"></i>
                                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                        </button>
                                        <button class="btn btn-sm btn-outline-success package-stops" data-id="{{$package->id}}" data-url="{{ route('admin.car-packages.stopsUpdate',$package->id) }}" title="Tour Stops — the route highlights grouped by stop, shown on this package's page">
                                            <i class="fas fa-map-pin icon"></i>
                                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                        </button>
                                        <button class="btn btn-sm btn-outline-warning package-amenities" data-id="{{$package->id}}" data-name="{{$package->name}}" data-udurl="{{ route('admin.car-packages.amenitiesUpdate',$package->id) }}" title="Features & Amenities shown on this package's page">
                                            <i class="fas fa-list-check icon"></i>
                                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                        </button>
                                        <button class="btn btn-sm btn-outline-success package-car" data-id="{{$package->id}}" data-url="{{ route('admin.car-packages.cars',$package->id) }}" title="Choose which cars/vehicles show on this package's fleet section">
                                            <i class="fas fa-car icon"></i>
                                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                        </button>
                                        <button class="btn btn-sm btn-outline-primary package-faqs" data-id="{{$package->id}}" data-url="{{ route('admin.car-packages.faqUpdate',$package->id) }}" title="FAQs shown at the bottom of this package's page">
                                            <i class="fa fa-question-circle icon"></i>
                                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                        </button>
                                        <button class="btn btn-sm btn-outline-primary car-package-meta" data-id="{{$package->id}}" data-upurl="{{ route('admin.car-packages.update',$package->id) }}" data-url="{{ route('admin.car-packages-meta.show.meta',$package->id) }}" title="SEO Meta Tags — not visible on the page, used for search engines">
                                            <i class="fa fa-globe icon"></i>
                                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger delete-package" data-id="{{ $package->id }}" data-url="{{ route('admin.car-packages.destroy',$package->id) }}" title="Delete Package">
                                            <i class="fas fa-trash icon"></i>
                                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                        </button>
                                        <a href="{{ config('app.frontend_url') }}/car-rental/{{ $package->slug }}" title="preview" class="btn btn-sm btn-outline-primary" target="_blank">
                                            <i class="fa fa-tv" aria-hidden="true"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div id="packagePagination">
                        @include('admin.common.pagination', [
                        'paginator' => $car_packages->appends(['active_tab' => 'package_list', 'package_search' => request('package_search')])
                        ])
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ======================================================= --}}
    {{-- DESTINATION LIST TAB                                     --}}
    {{-- ======================================================= --}}
    <div class="row tab_details {{ (session('active_tab') && session('active_tab') == 'destination_list') ? '' :'d-none' }}" id="destination_list_tab">
        <div class="col-12">
            <div class="card">
                <div class="card-header text-white" style="background:#be123c;">
                    <h5 class="mb-0">
                        <i class="fas fa-map-marker-alt me-2"></i>Destinations ({{ $car_destinations->total() }})
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <input type="text" id="destinationSearch" class="form-control" placeholder="Search Destination..." value="{{ request('destination_search') }}">
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th>Name</th>
                                    <th>City</th>
                                    <th>Url</th>
                                    <th>Status</th>
                                    <th>Popular?</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="destinationTableBody">
                                @foreach($car_destinations as $destination)
                                <tr>
                                    <td>{{ $destination->name }}</td>
                                    <td>{{ $destination->location->name ?? '-' }}</td>
                                    <td>car-rental/{{ $destination->slug }}</td>
                                    <td>
                                        <input id="destination_status_{{$destination->id }}" type="checkbox" data-id="{{$destination->id }}" data-url="{{ route('admin.car-destinations.update',$destination->id) }}" class="js-switch destination-status" <?php echo $destination->is_active == 1 ? 'checked' : '' ?>>
                                    </td>
                                    <td>
                                        <input id="destination_popular_{{$destination->id }}" type="checkbox" data-id="{{$destination->id }}" data-url="{{ route('admin.car-destinations.update',$destination->id) }}" class="js-switch destination-popular" <?php echo $destination->is_popular == 1 ? 'checked' : '' ?>>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-success destination-edit" data-id="{{$destination->id}}" data-url="{{ route('admin.car-destinations.show',$destination->id) }}" data-udurl="{{ route('admin.car-destinations.update',$destination->id) }}">
                                            <i class="fas fa-edit icon"></i>
                                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                        </button>
                                        <button class="btn btn-sm btn-outline-primary add-destination-page-details" data-id="{{$destination->id}}" data-url="{{ route('admin.car-destinations.update',$destination->id) }}" data-eurl="{{ route('admin.car-destinations-page.show.page',$destination->id) }}">
                                            <i class="fas fa-cog icon"></i>
                                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                        </button>
                                        <button class="btn btn-sm btn-outline-success destination-highlights" data-id="{{$destination->id}}" data-url="{{ route('admin.car-destinations.highlightsUpdate',$destination->id) }}" title="Highlights">
                                            <i class="fa fa-map-signs icon"></i>
                                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                        </button>
                                        <button class="btn btn-sm btn-outline-success destination-car" data-id="{{$destination->id}}" data-url="{{ route('admin.car-destinations.cars',$destination->id) }}">
                                            <i class="fas fa-car icon"></i>
                                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                        </button>
                                        <button class="btn btn-sm btn-outline-primary destination-faqs" data-id="{{$destination->id}}" data-url="{{ route('admin.car-destinations.faqUpdate',$destination->id) }}">
                                            <i class="fa fa-question-circle icon"></i>
                                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                        </button>
                                        <button class="btn btn-sm btn-outline-primary car-destination-meta" data-id="{{$destination->id}}" data-upurl="{{ route('admin.car-destinations.update',$destination->id) }}" data-url="{{ route('admin.car-destinations-meta.show.meta',$destination->id) }}">
                                            <i class="fa fa-globe icon"></i>
                                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger delete-destination" data-id="{{ $destination->id }}" data-url="{{ route('admin.car-destinations.destroy',$destination->id) }}">
                                            <i class="fas fa-trash icon"></i>
                                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                        </button>
                                        <a href="{{ config('app.frontend_url') }}/car-rental/{{ $destination->slug }}" title="preview" class="btn btn-sm btn-outline-primary" target="_blank">
                                            <i class="fa fa-tv" aria-hidden="true"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div id="destinationPagination">
                        @include('admin.common.pagination', [
                        'paginator' => $car_destinations->appends(['active_tab' => 'destination_list', 'destination_search' => request('destination_search')])
                        ])
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ======================================================= --}}
    {{-- PAGE CONTENT TAB                                         --}}
    {{-- ======================================================= --}}
    {{-- ======================================================= --}}
    {{-- ======================================================= --}}
    {{-- GLOBAL SETTING TAB                                       --}}
    {{-- ======================================================= --}}
    <div class="row tab_details {{ (session('active_tab') && session('active_tab') == 'global_setting') ? '' :'d-none' }}" id="global_setting_tab">
        <div class="col-12">
            <div class="card">
                <div class="card-header text-white" style="background:#475569;">
                    <h5 class="mb-0"><i class="fas fa-share-alt me-2"></i>Global Why Choose Us</h5>
                </div>
                <div class="card-body">
                    <form id="whyChooseStatsForm" method="POST" action="{{ route('admin.car-rental-content.why-choose-stats') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Why Choose Title</label>
                            <input type="text" class="form-control" name="why_choose_title" value="{{ $car_content->why_choose_title }}" placeholder="Why Choose Indian Panorama Car Rental">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Why Choose Description</label>
                            <textarea class="form-control tinymce" name="why_choose_description" id="car_why_choose_description" rows="3">{{ $car_content->why_choose_description }}</textarea>
                        </div>

                        <hr>
                        <h6 class="text-muted">Why Choose Us — Stat Cards</h6>
                        <table class="table align-middle mb-2" id="whyChooseStatsTable">
                            <thead><tr><th style="min-width:220px;">Icon</th><th>Label</th><th class="text-end" style="width:1%;"><button type="button" class="btn btn-sm btn-outline-success" id="addWhyChooseStatRow"><i class="fas fa-plus"></i></button></th></tr></thead>
                            <tbody id="whyChooseStatsTableBody">
                                @forelse($car_content->whyChooseStats as $stat)
                                <tr>
                                    <td>
                                        <x-media-picker name="why_choose_icons[{{ $loop->index }}]" :value="$stat->icon" folder="car-rental/why-choose" picker-id="why_choose_{{ $loop->index }}" label="" />
                                    </td>
                                    <td>
                                        <input type="text" name="labels[{{ $loop->index }}]" class="form-control" value="{{ $stat->label }}" placeholder="e.g. 100+ Fleet Size">
                                    </td>
                                    <td class="text-end" style="width:1%;"><button type="button" class="btn btn-sm btn-outline-danger rm-why-choose-stat-row"><i class="fas fa-trash"></i></button></td>
                                </tr>
                                @empty
                                <tr>
                                    <td>
                                        <x-media-picker name="why_choose_icons[0]" value="" folder="car-rental/why-choose" picker-id="why_choose_0" label="" />
                                    </td>
                                    <td>
                                        <input type="text" name="labels[0]" class="form-control" placeholder="e.g. 100+ Fleet Size">
                                    </td>
                                    <td class="text-end" style="width:1%;"><button type="button" class="btn btn-sm btn-outline-danger rm-why-choose-stat-row"><i class="fas fa-trash"></i></button></td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <button type="submit" class="btn btn-warning text-white"><i class="fas fa-save me-1"></i>Save</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-12 mt-4">
            <div class="card">
                <div class="card-header text-white" style="background:#475569;">
                    <h5 class="mb-0"><i class="fas fa-share-alt me-2"></i>Global Popular Locations</h5>
                </div>
                <div class="card-body">
                    <form id="globalPopularLocationsForm" method="POST" action="{{ route('admin.car-rental-content.text') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" class="form-control" name="popular_locations_title" value="{{ $car_content->popular_locations_title }}" placeholder="Popular Car Rental Locations In India">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Subtitle</label>
                            <input type="text" class="form-control" name="popular_locations_description" value="{{ $car_content->popular_locations_description }}">
                        </div>
                        <button type="submit" class="btn btn-warning text-white"><i class="fas fa-save me-1"></i>Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- ======================================================= --}}
    {{-- PACKAGE MODALS                                           --}}
    {{-- ======================================================= --}}
    <div class="modal fade" id="addPackageModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header text-white" style="background:#b45309;">
                    <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Add New Package</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="addPackageForm" method="POST" action="{{ route('admin.car-packages.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="add_package_name" class="form-label">Package Name</label>
                            <input type="text" class="form-control" id="add_package_name" name="name" required placeholder="e.g. Golden Triangle Tour">
                        </div>
                        <div class="invalid-feedback package-name-error"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary rounded-md" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning rounded-md" id="add-package-submit-btn">
                            <i class="fas fa-plus me-2"></i>Add Package
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editPackageModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header text-white" style="background:#b45309;">
                    <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Package</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="editPackageForm" method="POST" action="" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_package_name" class="form-label">Package Name</label>
                            <input type="text" class="form-control" id="edit_package_name" name="name" required>
                        </div>
                        <div class="invalid-feedback package-name-error"></div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <x-media-picker name="banner_image" picker-id="car_package_banner" label="Page Banner" folder="car_package" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="package_page_banner_alt" class="form-label">Banner Image Alt</label>
                                    <input type="text" class="form-control" id="package_page_banner_alt" name="banner_image_alt" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="package_page_about" class="form-label">Short Description</label>
                            <textarea class="form-control tinymce" name="description" id="package_page_about" rows="4"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary rounded-md" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning rounded-md" id="edit-package-submit-btn">
                            <i class="fas fa-edit me-2"></i>Update Package
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="packagePageSettingModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header text-white" style="background:#b45309;">
                    <h5 class="modal-title" id="package-title">About Section</h5>
                    <button type="button" class="btn-close btn-close-white close-modal" data-bs-dismiss="modal"></button>
                </div>
                <form id="packagePageSetting" method="POST" action="" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <input type="hidden" name="page_setting">

                        <div class="mb-3">
                            <label for="package_about_title" class="form-label">About Section Title</label>
                            <input type="text" class="form-control" id="package_about_title" name="about_title">
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <x-media-picker name="about_image" picker-id="car_package_about" label="About Section Image" folder="car_package" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="package_about_image_alt" class="form-label">About Image Alt</label>
                                    <input type="text" class="form-control" id="package_about_image_alt" name="about_image_alt">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="package_about_description" class="form-label">About Description</label>
                            <textarea class="form-control" id="package_about_description" name="about_description" rows="4"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="package_duration_text" class="form-label">Duration</label>
                                    <input type="text" class="form-control" id="package_duration_text" name="duration_text" placeholder="e.g. 4 Nights / 5 Days">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="package_best_season" class="form-label">Best Time To Visit</label>
                                    <input type="text" class="form-control" id="package_best_season" name="best_season" placeholder="e.g. October - March">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="package_ideal_for" class="form-label">Ideal For</label>
                                    <input type="text" class="form-control" id="package_ideal_for" name="ideal_for" placeholder="e.g. Families, Couples, Friends & Groups">
                                </div>
                            </div>
                        </div>
                        <p class="text-muted small">Note: the "Route" stat on the public page is generated automatically from the Tour Stops list below.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondaryclose-modal close-modal" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning" id="update-package-page-setting-btn">
                            <i class="fas fa-save me-2"></i>Update Setting
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="packageAmenitiesModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header text-white" style="background:#b45309;">
                    <h5 class="modal-title"><i class="fas fa-list-check me-2"></i>Features &amp; Amenities — <span id="package-amenities-title"></span></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="packageAmenitiesForm" method="POST" action="" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <table class="table align-middle mb-0" id="packageAmenitiesTable">
                            <thead>
                                <tr>
                                    <th style="min-width:220px;">Icon</th>
                                    <th>Label</th>
                                    <th>Tagline</th>
                                    <th class="text-end" style="width:1%;"><button type="button" class="btn btn-sm btn-outline-success" id="addPackageAmenityRow"><i class="fas fa-plus"></i></button></th>
                                </tr>
                            </thead>
                            <tbody id="packageAmenitiesTableBody"></tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning"><i class="fas fa-save me-2"></i>Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="carPackageMetaModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header text-white" style="background:#b45309;">
                    <h5 class="modal-title" id="car-package-title">Edit Meta Info</h5>
                    <button type="button" class="btn-close btn-close-white close-modal" data-bs-dismiss="modal"></button>
                </div>
                <form id="carPackageMeta" method="POST" action="" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <input type="hidden" name="meta_setting">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Meta Title</label>
                                    <input type="text" class="form-control" id="car_package_meta_title" name="meta_title">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Meta Description</label>
                                    <input type="text" class="form-control" id="car_package_meta_description" name="meta_description">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Meta Keywords</label>
                                    <input type="text" class="form-control" id="car_package_meta_keywords" name="meta_keywords">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">H1 Heading</label>
                                    <input type="text" class="form-control" id="car_package_h1_heading" name="h1_heading">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Extra Meta Tag</label>
                            <textarea class="form-control" name="meta_details" id="car_package_meta_details" rows="5"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondaryclose-modal close-modal" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning" id="update-car-package-meta-btn">
                            <i class="fas fa-save me-2"></i>Update Meta Setting
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="packageCarModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header text-white" style="background:#b45309;">
                    <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Sync Cars (<span id="packageCar"></span>)</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="packageCarForm" method="POST" action="{{ route('admin.car-packages.syncCars') }}">
                    @csrf
                    <input type="hidden" id="package_id" name="package_id">
                    <div class="modal-body">
                        <div id="package-car-list"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary rounded-md" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning rounded-md" id="package-car-submit-btn">
                            <i class="fas fa-save me-2"></i>Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="packageStopsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header text-white" style="background:#b45309;">
                    <h5 class="modal-title" id="stops-title">Tour Stops</h5>
                    <button type="button" class="btn-close btn-close-white close-modal" data-bs-dismiss="modal"></button>
                </div>
                <form id="stopsForm" method="POST" action="" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondaryclose-modal close-modal" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning" id="update-stops-btn">
                            <i class="fas fa-save me-2"></i>Save Stops
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ======================================================= --}}
    {{-- DESTINATION MODALS                                       --}}
    {{-- ======================================================= --}}
    <div class="modal fade" id="addDestinationModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header text-white" style="background:#be123c;">
                    <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Add New Destination</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="addDestinationForm" method="POST" action="{{ route('admin.car-destinations.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="add_destination_name" class="form-label">Destination Name</label>
                            <input type="text" class="form-control" id="add_destination_name" name="name" required placeholder="e.g. Jaipur Palaces">
                        </div>
                        <div class="mb-3">
                            <label for="add_destination_state" class="form-label">State</label>
                            <select class="form-select" id="add_destination_state" name="state_id">
                                <option value="">Select State</option>
                                @foreach($states as $state)
                                <option value="{{ $state->id }}">{{ $state->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="add_destination_location" class="form-label">City</label>
                            <select class="form-select" id="add_destination_location" name="location_id">
                                <option value="">Select City</option>
                                @foreach($locations as $loc)
                                <option value="{{ $loc->id }}" data-state="{{ $loc->state_id }}">{{ $loc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary rounded-md" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning rounded-md" id="add-destination-submit-btn">
                            <i class="fas fa-plus me-2"></i>Add Destination
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editDestinationModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header text-white" style="background:#be123c;">
                    <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Destination</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="editDestinationForm" method="POST" action="" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_destination_name" class="form-label">Destination Name</label>
                            <input type="text" class="form-control" id="edit_destination_name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_destination_state" class="form-label">State</label>
                            <select class="form-select" id="edit_destination_state" name="state_id">
                                <option value="">Select State</option>
                                @foreach($states as $state)
                                <option value="{{ $state->id }}">{{ $state->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_destination_location" class="form-label">City</label>
                            <select class="form-select" id="edit_destination_location" name="location_id">
                                <option value="">Select City</option>
                                @foreach($locations as $loc)
                                <option value="{{ $loc->id }}" data-state="{{ $loc->state_id }}">{{ $loc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary rounded-md" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning rounded-md" id="edit-destination-submit-btn">
                            <i class="fas fa-edit me-2"></i>Update Destination
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="destinationPageSettingModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header text-white" style="background:#be123c;">
                    <h5 class="modal-title" id="destination-title">Set Destination Page Details</h5>
                    <button type="button" class="btn-close btn-close-white close-modal" data-bs-dismiss="modal"></button>
                </div>
                <form id="destinationPageSetting" method="POST" action="" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <input type="hidden" name="page_setting">
                        <div class="mb-3">
                            <label for="destination_page_title" class="form-label">Page Title<span class="required-text">*</span></label>
                            <input type="text" class="form-control" id="destination_page_title" name="title" required>
                        </div>
                        <div class="row" id="galleryPreviewDestination"></div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <x-media-picker name="banner_image" picker-id="car_destination_banner" label="Page Banner" folder="car_destination" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="destination_page_banner_alt" class="form-label">Banner Image Alt</label>
                                    <input type="text" class="form-control" id="destination_page_banner_alt" name="banner_image_alt" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="destination_page_about" class="form-label">Page Description</label>
                            <textarea class="form-control tinymce" name="description" id="destination_page_about" rows="4"></textarea>
                        </div>

                        <hr>
                        <h6 class="mb-3">About Destination Section</h6>
                        <div class="mb-3">
                            <label for="destination_about_title" class="form-label">About Section Title</label>
                            <input type="text" class="form-control" id="destination_about_title" name="about_title">
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <x-media-picker name="about_image" picker-id="car_destination_about" label="About Section Image" folder="car_destination" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="destination_about_image_alt" class="form-label">About Image Alt</label>
                                    <input type="text" class="form-control" id="destination_about_image_alt" name="about_image_alt">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="destination_about_description" class="form-label">About Description</label>
                            <textarea class="form-control" id="destination_about_description" name="about_description" rows="4"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="destination_distance_text" class="form-label">Distance</label>
                                    <input type="text" class="form-control" id="destination_distance_text" name="distance_text" placeholder="e.g. 35 KM">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="destination_duration_text" class="form-label">Duration</label>
                                    <input type="text" class="form-control" id="destination_duration_text" name="duration_text" placeholder="e.g. Full Day">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="destination_ideal_for" class="form-label">Ideal For</label>
                                    <input type="text" class="form-control" id="destination_ideal_for" name="ideal_for">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="destination_best_season" class="form-label">Best Season</label>
                                    <input type="text" class="form-control" id="destination_best_season" name="best_season">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondaryclose-modal close-modal" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning" id="update-destination-page-setting-btn">
                            <i class="fas fa-save me-2"></i>Update Setting
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="carDestinationMetaModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header text-white" style="background:#be123c;">
                    <h5 class="modal-title" id="car-destination-title">Edit Meta Info</h5>
                    <button type="button" class="btn-close btn-close-white close-modal" data-bs-dismiss="modal"></button>
                </div>
                <form id="carDestinationMeta" method="POST" action="" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <input type="hidden" name="meta_setting">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Meta Title</label>
                                    <input type="text" class="form-control" id="car_destination_meta_title" name="meta_title">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Meta Description</label>
                                    <input type="text" class="form-control" id="car_destination_meta_description" name="meta_description">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Meta Keywords</label>
                                    <input type="text" class="form-control" id="car_destination_meta_keywords" name="meta_keywords">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">H1 Heading</label>
                                    <input type="text" class="form-control" id="car_destination_h1_heading" name="h1_heading">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Extra Meta Tag</label>
                            <textarea class="form-control" name="meta_details" id="car_destination_meta_details" rows="5"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondaryclose-modal close-modal" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning" id="update-car-destination-meta-btn">
                            <i class="fas fa-save me-2"></i>Update Meta Setting
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="destinationCarModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header text-white" style="background:#be123c;">
                    <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Sync Cars (<span id="destinationCar"></span>)</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="destinationCarForm" method="POST" action="{{ route('admin.car-destinations.syncCars') }}">
                    @csrf
                    <input type="hidden" id="destination_id" name="destination_id">
                    <div class="modal-body">
                        <div id="destination-car-list"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary rounded-md" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning rounded-md" id="destination-car-submit-btn">
                            <i class="fas fa-save me-2"></i>Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
@section('modal')

<div class="modal fade" id="editPageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white" style="background:#2563eb;">
                <h5 class="modal-title">
                    <i class="fas fa-pencil me-2"></i>Edit Car Page
                </h5>
                <button type="button" class="btn-close btn-close-white close-modal" data-bs-dismiss="modal"></button>
            </div>
            <form id="editPage" method="POST" action="{{ $page_details ? route('admin.page.update', $page_details->id) : '#' }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_page_title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="edit_page_title" name="title" value="{{ old('title', $page_details?->title) }}" required>
                    </div>
                    <div class="mb-3">
                        <input type="text" class="form-control mb-2" id="edit_banner_image_alt" name="banner_image_alt" required placeholder="Banner Image Alt Text" value="{{ old('banner_image_alt', $page_details?->banner_image_alt) }}">
                        <div class="text-danger d-none" id="edit-banner-image-alt-error"></div>
                        <x-media-picker name="banner_image" picker-id="page_banner" label="Image" folder="page" :value="$page_details?->banner_image" />
                        <div class="text-danger d-none" id="edit-banner-image-error"></div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control tinymce"
                            id="description" name="description" rows="5">
                        {{ old('description', $page_details?->description) }}
                        </textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondaryclose-modal close-modal" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning" id="submit-btn">
                        <i class="fas fa-save me-2"></i>Update Setting
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="faqModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white" style="background:#2563eb;">
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

<div class="modal fade" id="highlightsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white" style="background:#7c3aed;">
                <h5 class="modal-title" id="highlights-title">Route Highlights</h5>
                <button type="button" class="btn-close btn-close-white close-modal" data-bs-dismiss="modal"></button>
            </div>
            <form id="highlightsForm" method="POST" action="" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondaryclose-modal close-modal" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning" id="update-highlights-btn" data-url="">
                        <i class="fas fa-save me-2"></i>Save Highlights
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="pageMetaModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white" style="background:#2563eb;">
                <h5 class="modal-title" id="page-title">Edit Meta Info</h5>
                <button type="button" class="btn-close btn-close-white close-modal" data-bs-dismiss="modal"></button>
            </div>
            <form id="pageMeta" method="POST" action="{{ $page_details ? route('admin.page.update', $page_details->id) : '#' }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" name="meta_setting">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="page_meta_title" class="form-label">Meta Title</label>
                                <input type="text" class="form-control" id="page_meta_title" name="meta_title">
                                <div class="text-danger d-none" id="page-meta-title-error"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="page_meta_description" class="form-label">Meta Description</label>
                                <input type="text" class="form-control" id="page_meta_description" name="meta_description">
                                <div class="text-danger d-none" id="page-meta-description-error"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="page_meta_keywords" class="form-label">Meta Keywords</label>
                                <input type="text" class="form-control" id="page_meta_keywords" name="meta_keywords">
                                <div class="text-danger d-none" id="page-meta-keyords-error"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="page_h1_heading" class="form-label">H1 Heading</label>
                                <input type="text" class="form-control" id="page_h1_heading" name="h1_heading">
                                <div class="text-danger d-none" id="page-meta-keyords-error"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3">
                            <label for="page_meta_details" class="form-label">Extra Meta Tag</label>
                            <textarea class="form-control" name="meta_details" id="page_meta_details" rows="5" id="meta_details"></textarea>
                            <div class="text-danger d-none" id="page-meta-details-error"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondaryclose-modal close-modal" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning" id="update-page-meta-btn">
                        <i class="fas fa-save me-2"></i>Update Meta Setting
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-white" style="background:#2563eb;">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>Add New Car Category
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="addCategoryForm" method="POST" action="{{ route('admin.car-categories.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="add_category_title" class="form-label">Name<span class="required-text">*</span></label>
                        <input type="text" class="form-control" id="add_category_title" name="name" required>
                        <div class="invalid-feedback title-error"></div>
                    </div>
                    <div class="mb-3">
                        <x-media-picker name="icon" picker-id="car_category_icon_add" label="Icon" folder="car-categories" />
                    </div>
                    <div class="mb-3">
                        <input type="text" class="form-control" name="icon_alt" placeholder="Icon Alt Text">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded-md" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning rounded-md" id="add-submit-btn">
                        <i class="fas fa-plus me-2"></i>Add Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-white" style="background:#2563eb;">
                <h5 class="modal-title">
                    <i class="fas fa-pencil me-2"></i>Edit Car Category
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <input type="hidden" id="category_id">
            <form id="editCategoryForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_category_title" class="form-label">Name<span class="required-text">*</span></label>
                        <input type="text" class="form-control" id="edit_category_title" name="name" required>
                        <div class="invalid-feedback title-error"></div>
                    </div>
                    <div class="mb-3">
                        <x-media-picker name="icon" picker-id="car_category_icon_edit" label="Icon" folder="car-categories" />
                    </div>
                    <div class="mb-3">
                        <input type="text" class="form-control" id="edit_category_icon_alt" name="icon_alt" placeholder="Icon Alt Text">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded-md" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning rounded-md" id="edit-submit-btn">
                        <i class="fas fa-save me-2"></i>Update Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="addCarModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white" style="background:#16a34a;">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>Add New Car
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="addCarForm" method="POST" action="{{ route('admin.cars.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="add_car_title" class="form-label">Title<span class="required-text">*</span></label>
                        <input type="text" class="form-control" id="add_car_title" name="title" required>
                        <div class="invalid-feedback title-error"></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="add_category_id" class="form-label">Category</label>
                            <select class="form-select" id="add_category_id" name="category_id" required>
                                <option value="">Select</option>
                                @foreach($categories as $c)
                                <option data-slug="{{$c->slug}}" value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback category-error"></div>
                        </div>
                        <div class="col-md-4">
                            <label for="add_fuel_type" class="form-label">Fuel Type</label>
                            <select class="form-select" id="add_fuel_type" name="fuel_type">
                                <option value="">Select</option>
                                <option value="Petrol">Petrol</option>
                                <option value="Diesel">Diesel</option>
                                <option value="CNG">CNG</option>
                                <option value="Electric">Electric</option>
                            </select>
                            <div class="invalid-feedback fuel_type-error"></div>
                        </div>
                        <div class="col-md-4">
                            <label for="add_car_seats" class="form-label">No. Of Seats</label>
                            <input type="text" class="form-control" id="add_car_seats" name="seats" placeholder="e.g. 6 or 6,7,8" required>
                            <div class="invalid-feedback seats-error"></div>
                        </div>
                    </div>
                    <div class="mb-2">
                        <x-media-picker name="primary_image" picker-id="car_primary_add" label="Primary Image" folder="cars" />
                        <div class="invalid-feedback primary-image-error"></div>
                    </div>
                    <div class="mb-3">
                        <input type="text" class="form-control mb-2" id="add_primary_image_alt" name="primary_image_alt"
                            value="" required placeholder="Primary Image Alt Text">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded-md" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success rounded-md" id="add-car-submit-btn">
                        <i class="fas fa-plus me-2"></i>Add Car
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editCarModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white" style="background:#16a34a;">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i>Edit Car
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <input type="hidden" id="car_id" name="id">
            <form id="editCarForm" method="POST" action="" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_car_title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="edit_car_title" name="title" required>
                        <div class="invalid-feedback title-error"></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="edit_category_id" class="form-label">Category</label>
                            <select class="form-select" id="edit_category_id" name="category_id" required>
                                <option value="">Select</option>
                                @foreach($categories as $c)
                                <option data-slug="{{$c->slug}}" value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback category-error"></div>
                        </div>
                        <div class="col-md-4">
                            <label for="edit_fuel_type" class="form-label">Fuel Type</label>
                            <select class="form-select" id="edit_fuel_type" name="fuel_type">
                                <option value="">Select</option>
                                <option value="Petrol">Petrol</option>
                                <option value="Diesel">Diesel</option>
                                <option value="CNG">CNG</option>
                                <option value="Electric">Electric</option>
                            </select>
                            <div class="invalid-feedback fuel_type-error"></div>
                        </div>
                        <div class="col-md-4">
                            <label for="edit_car_seats" class="form-label">No. Of Seats</label>
                            <input type="text" class="form-control" id="edit_car_seats" name="seats" placeholder="e.g. 6 or 6,7,8" required>
                            <div class="invalid-feedback seats-error"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <input type="text" class="form-control mb-2" id="edit_primary_image_alt" name="primary_image_alt" value="" required placeholder="Primary Image Alt Text">
                        <x-media-picker name="primary_image" picker-id="car_primary_edit" label="Primary Image" folder="cars" />
                        <div class="invalid-feedback primary-image-error"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded-md" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning rounded-md" id="edit-car-submit-btn">
                        <i class="fas fa-edit me-2"></i>Update Car
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="carPageSettingModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white" style="background:#16a34a;">
                <h5 class="modal-title">Car Page Settings — <span id="car-settings-title"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="carPageSettingForm" method="POST" action="" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="page_setting" value="1">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <x-media-picker name="banner_image" picker-id="car_page_banner" label="Page Banner" folder="cars" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="car_banner_image_alt" class="form-label">Banner Image Alt</label>
                                <input type="text" class="form-control" id="car_banner_image_alt" name="banner_image_alt">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="car_page_about" class="form-label">Short Description</label>
                        <textarea class="form-control tinymce" name="description" id="car_page_about" rows="4"></textarea>
                    </div>

                    <hr>
                    <h6 class="text-muted">Vehicle Specifications <small>(title &amp; subtitle shown above the spec list)</small></h6>
                    <div class="mb-3">
                        <label for="car_specs_title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="car_specs_title" name="specs_title" placeholder="Vehicle Specifications">
                    </div>
                    <div class="mb-3">
                        <label for="car_specs_description" class="form-label">Subtitle</label>
                        <textarea class="form-control" id="car_specs_description" name="specs_description" rows="2" placeholder="Travel in comfort with well-maintained vehicles..."></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="car_vehicle_type" class="form-label">Vehicle Type</label>
                                <input type="text" class="form-control" id="car_vehicle_type" name="vehicle_type" placeholder="e.g. MPV">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="car_transmission" class="form-label">Transmission</label>
                                <input type="text" class="form-control" id="car_transmission" name="transmission" placeholder="e.g. Manual / Automatic">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="car_luggage_capacity" class="form-label">Luggage Capacity</label>
                                <input type="text" class="form-control" id="car_luggage_capacity" name="luggage_capacity" placeholder="e.g. Up to 4 Bags">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="car_mileage" class="form-label">Mileage</label>
                                <input type="text" class="form-control" id="car_mileage" name="mileage" placeholder="e.g. 18-20 km/l (Approx.)">
                            </div>
                        </div>
                    </div>
                </form>

                <hr>
                <h6 class="text-muted">Gallery <small>(title &amp; description shown above the gallery images)</small></h6>
                <div class="row mb-3" id="carGalleryImagesWrapper"></div>
                <form id="addCarGalleryImageForm" method="POST" action="">
                    @csrf
                    <div class="mb-3">
                        <label for="car_gallery_title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="car_gallery_title" name="gallery_title" placeholder="Gallery">
                    </div>
                    <div class="mb-3">
                        <label for="car_gallery_description" class="form-label">Description</label>
                        <textarea class="form-control" id="car_gallery_description" name="gallery_description" rows="2"></textarea>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-8">
                            <x-media-picker name="image" picker-id="car_gallery_add_image" label="" folder="cars/gallery" />
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="carGalleryImageAlt" name="image_alt" placeholder="Image alt text">
                        </div>
                    </div>
                </form>

                <hr>
                <h6 class="text-muted">Highlights <small>(short tags shown alongside the gallery, e.g. "Spacious &amp; Comfortable")</small></h6>
                <form id="carHighlightTagsForm" method="POST" action="" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <table class="table align-middle mb-2" id="carHighlightTagsTable">
                        <thead><tr><th width="110">Icon</th><th>Highlight</th><th class="text-end" style="width:1%;"><button type="button" class="btn btn-sm btn-outline-success" id="addCarHighlightTagRow"><i class="fas fa-plus"></i></button></th></tr></thead>
                        <tbody id="carHighlightTagsTableBody"></tbody>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-warning text-white" id="carSettingsSaveAll"><i class="fas fa-save me-1"></i>Save</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="carAmenitiesModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white" style="background:#16a34a;">
                <h5 class="modal-title"><i class="fas fa-list-check me-2"></i>Features &amp; Amenities — <span id="car-amenities-title"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="carAmenitiesForm" method="POST" action="" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <table class="table align-middle mb-0" id="carAmenitiesTable">
                        <thead>
                            <tr>
                                <th style="min-width:220px;">Icon</th>
                                <th>Label</th>
                                <th>Tagline</th>
                                <th class="text-end" style="width:1%;"><button type="button" class="btn btn-sm btn-outline-success" id="addCarAmenityRow"><i class="fas fa-plus"></i></button></th>
                            </tr>
                        </thead>
                        <tbody id="carAmenitiesTableBody"></tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning"><i class="fas fa-save me-2"></i>Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="addRouteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-white" style="background:#7c3aed;">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>Add New Route
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="addRouteForm" method="POST" action="{{ route('admin.car-routes.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="add_from_location" class="form-label">Source City</label>
                        <select class="form-select from_location" id="add_from_location" name="from_location" required>
                            <option value="">Select</option>
                            @foreach($locations as $c)
                            <option value="{{ $c->name }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="add_to_location" class="form-label">Destination City</label>
                        <select class="form-select to_location" id="add_to_location" name="to_location" required>
                            <option value="">Select</option>
                            @foreach($locations as $c)
                            <option value="{{ $c->name }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="invalid-feedback location-error"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded-md" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning rounded-md" id="add-route-submit-btn">
                        <i class="fas fa-plus me-2"></i>Add Route
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editRouteModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white" style="background:#7c3aed;">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i>Edit Route
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editRouteForm" method="POST" action="" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" id="id" name="id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_from_location" class="form-label">Source City</label>
                        <select class="form-select from_location" id="edit_from_location" name="from_location" required>
                            <option value="">Select</option>
                            @foreach($locations as $c)
                            <option value="{{ $c->name }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_to_location" class="form-label">Destination City</label>
                        <select class="form-select to_location" id="edit_to_location" name="to_location" required>
                            <option value="">Select</option>
                            @foreach($locations as $c)
                            <option value="{{ $c->name }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="invalid-feedback location-error"></div>
                    <div class="mb-3">
                        <label for="route_page_title" class="form-label">Page Title<span class="required-text">*</span></label>
                        <input type="text" class="form-control" id="route_page_title" name="title" required>
                        <div class="text-danger d-none" id="route-page-title-error"></div>
                    </div>
                    <div class="row">
                        <div class="mb-3">
                            <x-media-picker name="banner_image" picker-id="car_route_banner" label="Page Banner" folder="car_route" />
                            <div class="text-danger d-none" id="route-page-banner-error"></div>
                        </div>
                        <div class="mb-3">
                            <label for="route_page_banner_alt" class="form-label">Banner image Alt</label>
                            <input type="text" class="form-control" id="route_page_banner_alt" name="banner_image_alt" required>
                            <div class="text-danger d-none" id="route-page-banner-alt-error"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="route_page_about" class="form-label">Short Description</label>
                        <textarea class="form-control tinymce" name="description" id="route_page_about" rows="5"></textarea>
                        <div class="text-danger d-none" id="route-page-about-error"></div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_route_display_label" class="form-label">Popular Locations Display Label <small class="text-muted">(shown in "Popular Car Rental Locations", optional)</small></label>
                        <input type="text" class="form-control" id="edit_route_display_label" name="display_label" placeholder="e.g. Golden Triangle Car Rental">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded-md" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning rounded-md" id="edit-route-submit-btn">
                        <i class="fas fa-edit me-2"></i>Update Route
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="routeCarModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white" style="background:#7c3aed;">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i>Sync Car Route (<span id="routeCar"></span>)
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="routeCarForm" method="POST" action="{{ route('admin.car-routes.syncCars') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="route_id" name="route_id">
                <div class="modal-body">
                    <div id="car-list"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded-md" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning rounded-md" id="route-car-submit-btn">
                        <i class="fas fa-save me-2"></i>Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="carRouteMetaModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white" style="background:#7c3aed;">
                <h5 class="modal-title" id="car-route-title">Edit Meta Info</h5>
                <button type="button" class="btn-close btn-close-white close-modal" data-bs-dismiss="modal"></button>
            </div>
            <form id="carRouteMeta" method="POST" action="" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" name="meta_setting">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="car_route_meta_title" class="form-label">Meta Title</label>
                                <input type="text" class="form-control" id="car_route_meta_title" name="meta_title">
                                <div class="text-danger d-none" id="car-route-meta-title-error"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="car_route_meta_description" class="form-label">Meta Description</label>
                                <input type="text" class="form-control" id="car_route_meta_description" name="meta_description">
                                <div class="text-danger d-none" id="car-route-meta-description-error"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="car_route_meta_keywords" class="form-label">Meta Keywords</label>
                                <input type="text" class="form-control" id="car_route_meta_keywords" name="meta_keywords">
                                <div class="text-danger d-none" id="car-route-meta-keyords-error"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="car_route_h1_heading" class="form-label">H1 Heading</label>
                                <input type="text" class="form-control" id="car_route_h1_heading" name="h1_heading">
                                <div class="text-danger d-none" id="car-route-meta-keyords-error"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3">
                            <label for="car_route_meta_details" class="form-label">Extra Meta Tag</label>
                            <textarea class="form-control" name="meta_details" id="car_route_meta_details" rows="5" id="meta_details"></textarea>
                            <div class="text-danger d-none" id="car-route-meta-details-error"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondaryclose-modal close-modal" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning" id="update-car-route-meta-btn">
                        <i class="fas fa-save me-2"></i>Update Meta Setting
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="routePageSettingModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white" style="background:#7c3aed;">
                <h5 class="modal-title" id="route-title">About Section</h5>
                <button type="button" class="btn-close btn-close-white close-modal" data-bs-dismiss="modal"></button>
            </div>
            <form id="routePageSetting" method="POST" action="" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" name="page_setting">

                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="route_about_title" class="form-label">About Section Title</label>
                                <input type="text" class="form-control" id="route_about_title" name="about_title" placeholder="e.g. About Cochin to Munnar Road Trip">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <x-media-picker name="about_image" picker-id="car_route_about" label="About Section Image" folder="car_route" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="route_about_image_alt" class="form-label">About Image Alt</label>
                                <input type="text" class="form-control" id="route_about_image_alt" name="about_image_alt">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3">
                            <label for="route_about_description" class="form-label">About Description</label>
                            <textarea class="form-control tinymce" id="route_about_description" name="about_description" rows="4"></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="route_distance_text" class="form-label">Distance</label>
                                <input type="text" class="form-control" id="route_distance_text" name="distance_text" placeholder="e.g. 130 KM">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="route_duration_text" class="form-label">Duration</label>
                                <input type="text" class="form-control" id="route_duration_text" name="duration_text" placeholder="e.g. 3.5 - 4 Hours">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="route_number" class="form-label">Route / Highway</label>
                                <input type="text" class="form-control" id="route_number" name="route_number" placeholder="e.g. NH 85">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="route_best_season" class="form-label">Best Season</label>
                                <input type="text" class="form-control" id="route_best_season" name="best_season" placeholder="e.g. Sept - May">
                            </div>
                        </div>
                    </div>
                    <p class="text-muted small mb-0">Note: the highlight cards (e.g. "Cheyyappara Waterfalls") are managed separately via the <strong>Route Highlights</strong> button on this route's row.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondaryclose-modal close-modal" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning" id="update-route-page-setting-btn" data-url="">
                        <i class="fas fa-save me-2"></i>Update Setting
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="addCityModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-white" style="background:#0891b2;">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>Add New City
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="addCityForm" method="POST" action="{{ route('admin.car-city.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="add_location" class="form-label">City</label>
                        <!-- <input type="text" class="form-control location" id="add_location" name="location" required> -->
                        <select class="form-select location" id="add_location" name="location" required>
                            <option value="">Select</option>
                            @foreach($locations as $c)
                            <option value="{{ $c->name }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <x-media-picker name="thumbnail_image" picker-id="car_city_thumbnail_add" label="Thumbnail Image" folder="car_city" />
                        <div class="invalid-feedback thumbnail-image-error"></div>
                        <input type="text" class="form-control" id="add_city_thumbnail_alt"
                            name="thumbnail_alt" value="" required placeholder="Thumbnail Image Alt Text">
                    </div>
                    <div class="invalid-feedback location-error"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded-md" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning rounded-md" id="add-city-submit-btn">
                        <i class="fas fa-plus me-2"></i>Add City
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editCityModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-white" style="background:#0891b2;">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i>Edit City
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editCityForm" method="POST" action="" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" id="cityID" name="id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_location" class="form-label">City</label>
                        <!-- <input type="text" class="form-control location" id="edit_location" name="location" required> -->
                        <select class="form-select location" id="edit_location" name="location" required>
                            <option value="">Select</option>
                            @foreach($locations as $c)
                            <option value="{{ $c->name }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="city_page_title" class="form-label">Page Title<span class="required-text">*</span> <small class="text-muted">(shown at the top of this city's page)</small></label>
                        <input type="text" class="form-control" id="city_page_title" name="title" required>
                        <div class="text-danger d-none" id="city-page-title-error"></div>
                    </div>
                    <div class="mb-3">
                        <x-media-picker name="thumbnail_image" picker-id="car_city_thumbnail_edit" label="Thumbnail Image" folder="car_city" />
                        <div class="invalid-feedback thumbnail-image-error"></div>
                        <input type="text" class="form-control" id="edit_city_thumbnail_alt"
                            name="thumbnail_alt" value="" required placeholder="Thumbnail Image Alt Text">
                    </div>
                    <div class="invalid-feedback location-error"></div>
                    <div class="mb-3">
                        <label for="edit_city_display_label" class="form-label">Popular Locations Display Label <small class="text-muted">(shown in "Popular Car Rental Locations", optional)</small></label>
                        <input type="text" class="form-control" id="edit_city_display_label" name="display_label" placeholder="e.g. Car Rental In Chennai">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded-md" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning rounded-md" id="edit-city-submit-btn">
                        <i class="fas fa-edit me-2"></i>Update City
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="cityPageSettingModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white" style="background:#0891b2;">
                <h5 class="modal-title" id="city-page-setting-title">City Page Settings</h5>
                <button type="button" class="btn-close btn-close-white close-modal" data-bs-dismiss="modal"></button>
            </div>
            <form id="cityPageSetting" method="POST" action="" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" name="page_setting" value="1">

                    <div class="mb-3">
                        <label for="city_page_about" class="form-label">Short Description</label>
                        <textarea class="form-control tinymce" name="description" id="city_page_about" rows="5"></textarea>
                        <div class="text-danger d-none" id="city-page-about-error"></div>
                    </div>

                    <hr>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" role="switch" id="city_why_choose_enabled" name="why_choose_enabled" value="1">
                        <label class="form-check-label" for="city_why_choose_enabled">Show "Why Choose Us" section on this city's page</label>
                    </div>
                    <small class="text-muted">Its title, subtitle &amp; stat labels are managed once in the <strong>Global Setting</strong> tab and shared across every city.</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondaryclose-modal close-modal" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning" id="update-city-page-setting-btn">
                        <i class="fas fa-save me-2"></i>Update Setting
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="cityCarModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white" style="background:#0891b2;">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i>Sync Car Route (<span id="cityCar"></span>)
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="cityCarForm" method="POST" action="{{ route('admin.car-city.syncCars') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="city_id" name="city_id">
                <div class="modal-body">
                    <div id="city-car-list"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded-md" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning rounded-md" id="city-car-submit-btn">
                        <i class="fas fa-save me-2"></i>Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="carCityMetaModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white" style="background:#0891b2;">
                <h5 class="modal-title" id="car-city-title">Edit Meta Info</h5>
                <button type="button" class="btn-close btn-close-white close-modal" data-bs-dismiss="modal"></button>
            </div>
            <form id="carCityMeta" method="POST" action="" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" name="meta_setting">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="car_city_meta_title" class="form-label">Meta Title</label>
                                <input type="text" class="form-control" id="car_city_meta_title" name="meta_title">
                                <div class="text-danger d-none" id="car-city-meta-title-error"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="car_city_meta_description" class="form-label">Meta Description</label>
                                <input type="text" class="form-control" id="car_city_meta_description" name="meta_description">
                                <div class="text-danger d-none" id="car-city-meta-description-error"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="car_city_meta_keywords" class="form-label">Meta Keywords</label>
                                <input type="text" class="form-control" id="car_city_meta_keywords" name="meta_keywords">
                                <div class="text-danger d-none" id="car-city-meta-keyords-error"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="car_city_h1_heading" class="form-label">H1 Heading</label>
                                <input type="text" class="form-control" id="car_city_h1_heading" name="h1_heading">
                                <div class="text-danger d-none" id="car-city-meta-keyords-error"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3">
                            <label for="car_city_meta_details" class="form-label">Extra Meta Tag</label>
                            <textarea class="form-control" name="meta_details" id="car_city_meta_details" rows="5" id="meta_details"></textarea>
                            <div class="text-danger d-none" id="car-city-meta-details-error"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondaryclose-modal close-modal" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning" id="update-car-city-meta-btn">
                        <i class="fas fa-save me-2"></i>Update Meta Setting
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="cityGalleryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white" style="background:#0891b2;">
                <h5 class="modal-title"><i class="fas fa-images me-2"></i>Gallery Images — <span id="city-gallery-title"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                @error('gallery_images')
                <div class="alert alert-danger">{{ $message }}</div>
                @enderror
                @error('gallery_images.*.path')
                <div class="alert alert-danger">{{ $message }}</div>
                @enderror
                <div class="row mb-3" id="cityGalleryImagesWrapper"></div>
                <form id="addCityGalleryImageForm" method="POST" action="">
                    @csrf
                    <div class="mb-3">
                        <label for="city_gallery_title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="city_gallery_title" name="gallery_title" placeholder="Gallery">
                    </div>
                    <div class="mb-3">
                        <label for="city_gallery_description" class="form-label">Description</label>
                        <textarea class="form-control" id="city_gallery_description" name="gallery_description" rows="2"></textarea>
                    </div>
                    <hr>
                    <div class="mb-2">
                        <x-media-gallery-picker name="gallery_images" picker-id="car_city_gallery_add" label="" folder="car-city" />
                    </div>
                    <button type="submit" class="btn btn-success"><i class="fas fa-save me-1"></i>Save</button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="cityFeaturesBenefitsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white" style="background:#0891b2;">
                <h5 class="modal-title"><i class="fas fa-list-check me-2"></i>Features &amp; Benefits — <span id="city-fb-title"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="border rounded p-3 h-100">
                            <form id="cityFeaturesForm" method="POST" action="">
                                @csrf
                                @method('PUT')
                                <div class="mb-3">
                                    <label class="form-label">Features Title</label>
                                    <input type="text" class="form-control" name="features_title" id="city_features_title" placeholder="Features">
                                </div>
                                <table class="table align-middle mb-0" id="cityFeaturesTable">
                                    <thead><tr><th>Item</th><th class="text-end" style="width:1%;"><button type="button" class="btn btn-sm btn-outline-success" id="addCityFeatureRow"><i class="fas fa-plus"></i></button></th></tr></thead>
                                    <tbody id="cityFeaturesTableBody"></tbody>
                                </table>
                            </form>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="border rounded p-3 h-100">
                            <form id="cityBenefitsForm" method="POST" action="">
                                @csrf
                                @method('PUT')
                                <div class="mb-3">
                                    <label class="form-label">Benefits Title</label>
                                    <input type="text" class="form-control" name="benefits_title" id="city_benefits_title" placeholder="Benefits">
                                </div>
                                <table class="table align-middle mb-0" id="cityBenefitsTable">
                                    <thead><tr><th>Item</th><th class="text-end" style="width:1%;"><button type="button" class="btn btn-sm btn-outline-success" id="addCityBenefitRow"><i class="fas fa-plus"></i></button></th></tr></thead>
                                    <tbody id="cityBenefitsTableBody"></tbody>
                                </table>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-warning text-white" id="cityFeaturesBenefitsSaveAll"><i class="fas fa-save me-1"></i>Save</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    let faqIndex = 0;
    let highlightIndex = 0;
    let packageAmenityIndex = 0;
    let carAmenityIndex = 0;
    let selectedFiles = [];
    let imageCounter = 0;

    function activeTab(ele) {
        // Remove active from all nav links
        let nav = document.querySelectorAll('.tab_link');
        nav.forEach(function(element) {
            element.classList.remove('active');
            if (element.getAttribute('data-tab') === ele) {
                element.classList.add('active');
            }
        });

        // Hide all tabs
        let tab = document.querySelectorAll('.tab_details');
        tab.forEach(function(element) {
            element.classList.add('d-none');
        });

        let selectTab = document.getElementById(ele);
        if (selectTab) {
            selectTab.classList.remove('d-none');
        }
    }


    function addCarCategory() {
        activeTab('car_category_tab');
        let oldInput = document.querySelector('.static-input');
        if (oldInput) {
            oldInput.remove();
        }
        let dealModal = new bootstrap.Modal(document.getElementById('addCategoryModal'));
        dealModal.show();
    }

    function addCar() {
        activeTab('car_list_tab');
        let oldInput = document.querySelector('.static-input');
        if (oldInput) {
            oldInput.remove();
        }
        let dealModal = new bootstrap.Modal(document.getElementById('addCarModal'));
        dealModal.show();
    }

    function addRoute() {
        activeTab('route_list_tab');
        let oldInput = document.querySelector('.static-input');
        if (oldInput) {
            oldInput.remove();
        }
        let dealModal = new bootstrap.Modal(document.getElementById('addRouteModal'));
        dealModal.show();
    }

    function addCity() {
        activeTab('city_list_tab');
        let oldInput = document.querySelector('.static-input');
        if (oldInput) {
            oldInput.remove();
        }
        let dealModal = new bootstrap.Modal(document.getElementById('addCityModal'));
        dealModal.show();
    }


    function addPackage() {
        activeTab('package_list_tab');
        document.getElementById('addPackageForm').reset();
        let dealModal = new bootstrap.Modal(document.getElementById('addPackageModal'));
        dealModal.show();
    }

    function addDestination() {
        activeTab('destination_list_tab');
        document.getElementById('addDestinationForm').reset();
        let dealModal = new bootstrap.Modal(document.getElementById('addDestinationModal'));
        dealModal.show();
    }

    const CAR_SETTINGS_TAB_KEY = 'admin_car_settings_active_tab';

    $(document).ready(function() {
        initSwitches();
        @if(session('success'))
        toastr.success("{{ session('success') }} ", 'Success');
        @endif

        @if($errors->any())
        @foreach($errors->all() as $validationError)
        toastr.error("{{ addslashes($validationError) }}", 'Error');
        @endforeach
        @endif

        @if(session('open_gallery_modal') || $errors->has('gallery_images') || $errors->has('gallery_images.*.path'))
        new bootstrap.Modal(document.getElementById('carGalleryModal')).show();
        @endif

        @if(session('open_city_gallery_modal'))
        $('.city-gallery[data-id="{{ session('open_city_gallery_modal') }}"]').trigger('click');
        @endif

        // Keep the user on whichever tab they were on across plain page refreshes.
        // Right after a save, the server flashes `active_tab` in session and Blade
        // already marks the matching nav link active — just remember that choice.
        // On a plain refresh there's no fresh flash data, so fall back to what we
        // remembered last time.
        @if(session('active_tab'))
        {
            let activeLink = document.querySelector('.tab_link.active');
            if (activeLink) {
                localStorage.setItem(CAR_SETTINGS_TAB_KEY, activeLink.getAttribute('data-tab'));
            }
        }
        @else
        {
            let savedTab = localStorage.getItem(CAR_SETTINGS_TAB_KEY);
            if (savedTab) {
                activeTab(savedTab);
            }
        }
        @endif

        $(document).on('click', '.tab_link', function() {
            $('.tab_link').removeClass('active');
            $(this).addClass('active');
            $('.tab_details').addClass('d-none');
            let ele = $(this).data('tab');
            $('#' + ele).removeClass('d-none');
            localStorage.setItem(CAR_SETTINGS_TAB_KEY, ele);
        });


        document.getElementById('editPage').addEventListener('submit', function(event) {
            tinymce.triggerSave();
            event.preventDefault();
            $('.invalid-feedback').addClass('d-none');
            $('#submit-btn').prop('disabled', true);
            let isError = 0;
            let description = document.getElementById('description');
            if (!description.value.trim()) {
                $('.invalid-feedback').removeClass('d-none');
                $('.invalid-feedback').addClass('d-block');
                $('.invalid-feedback').text('Descriptions required');
                $('#submit-btn').prop('disabled', false);
            } else {
                this.submit();
            }

        });

        $(document).on('click', '.page-faqs', function() {
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
                url: "{{ route('admin.page.faq') }}", // better to point this to a dedicated faq route
                data: {
                    id: id
                },
                success: function(data) {
                    $('#faqModal .modal-title').text(data.page.title + ' Page FAQ');
                    let faqs = data.page.faqs || [];
                    let body = `
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="faq_title" class="form-label">Faq Title<span class="required-text">*</span></label>
                            <input value="${data.page.faq_title ? data.page.faq_title : ''}" class="form-control" name="faq_title" id="faq_title" placeholder="" required>
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
                complete: function() {
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

        $(document).on('click', '#addHighlightRow', function() {
            let row = `
                <tr class="b-none">
                    <td><input type="text" name="highlights[${highlightIndex}][title]" class="form-control" required /></td>
                    <td><textarea name="highlights[${highlightIndex}][description]" class="form-control"></textarea></td>
                    <td><button type="button" class="btn btn-sm btn-outline-danger removeHighlightRow"><i class="fas fa-trash"></button></td>
                </tr>`;
            highlightIndex++;
            $('#highlightsTable tbody').append(row);
        });

        $(document).on('click', '.removeHighlightRow', function() {
            $(this).closest('tr').remove();
        });

        $(document).on('click', '.route-highlights', function() {
            highlightIndex = 0;
            let btn = $(this);
            btn.find('.spinner-border').removeClass('d-none');
            btn.find('.icon').addClass('d-none');
            let id = btn.data('id');
            let dataUrl = btn.data('url');

            $('#highlightsForm').attr('action', dataUrl);
            $.ajax({
                type: "GET",
                dataType: "json",
                url: "{{ route('admin.car.routes.highlights') }}",
                data: { id: id },
                success: function(data) {
                    $('#highlightsModal .modal-title').text(data.car_route.from_location + ' to ' + data.car_route.to_location + ' Route Highlights');
                    let highlights = data.car_route.highlights || [];
                    let body = `
                    <table class="table" id="highlightsTable">
                        <thead>
                            <tr>
                                <th>Label</th>
                                <th>Tagline</th>
                                <th><button type="button" class="btn btn-sm btn-outline-success" id="addHighlightRow"><i class="fas fa-plus"></button></th>
                            </tr>
                        </thead>
                        <tbody>
                    `;

                    if (highlights.length > 0) {
                        $.each(highlights, function(index, h) {
                            body += `
                                <tr class="b-none">
                                    <td><input type="text" name="highlights[${highlightIndex}][title]" value="${h.title}" class="form-control" required /></td>
                                    <td><textarea name="highlights[${highlightIndex}][description]" class="form-control">${h.description ?? ''}</textarea></td>
                                    <td><button type="button" class="btn btn-sm btn-outline-danger removeHighlightRow"><i class="fas fa-trash"></button></td>
                                </tr>
                            `;
                            highlightIndex++;
                        });
                    } else {
                        body += `
                            <tr class="b-none">
                                <td><input type="text" name="highlights[${highlightIndex}][title]" class="form-control" required/></td>
                                <td><textarea name="highlights[${highlightIndex}][description]" class="form-control"></textarea></td>
                                <td></td>
                            </tr>
                        `;
                        highlightIndex++;
                    }

                    body += `</tbody></table>`;

                    $('#highlightsModal .modal-body').html(body);
                    $('#highlightsModal').modal('show');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while fetching route highlights.'
                    });
                },
                complete: function() {
                    btn.find('.spinner-border').addClass('d-none');
                    btn.find('.icon').removeClass('d-none');
                }
            });
        });

        $(document).on('click', '.page-meta', function() {
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
                    $('#page-title').text('# ' + data.title + '-Meta Info');
                    if (data.meta != null) {
                        $('#page_meta_title').val(data.meta.meta_title);
                        $('#page_meta_description').val(data.meta.meta_description);
                        $('#page_meta_keywords').val(data.meta.meta_keywords);
                        $('#page_h1_heading').val(data.meta.h1_heading);
                        $('#page_meta_details').val(data.meta.meta_details);
                    }
                    $('#pageMetaModal').modal('show');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while fetching city details.'
                    });
                },
                complete: function() {
                    // Hide loader, show button text back
                    btn.find('.spinner-border').addClass('d-none');
                    btn.find('.icon').removeClass('d-none');
                }
            });
        });

        document.getElementById('addCategoryForm').addEventListener('submit', function(event) {
            event.preventDefault();
            $('.invalid-feedback').hide();
            $('#add-submit-btn').prop('disabled', true);
            let form = this;
            let title = document.getElementById('add_category_title').value.trim();
            $.ajax({
                type: "GET",
                url: "{{ route('admin.car-categories.slug.duplicate_check') }}",
                data: {
                    'title': title
                },
                success: function(res) {
                    if (res.exists) {
                        $('.title-errorr').show();
                        $('.title-error').text('This category already exists');
                        $('.invalid-feedback').show();
                        $('#add-submit-btn').prop('disabled', false);
                    } else {
                        $('.title-errorr').hide();
                        form.submit();
                    }
                },
                error: function(err) {
                    form.submit();
                }
            });
        });


        $(document).on('change', '.category-status', function() {
            let car_status = $(this).prop('checked') === true ? 1 : 0;
            $.ajax({
                type: "PUT",
                dataType: "json",
                url: $(this).data('url'),
                data: {
                    'status': car_status
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

        $(document).on('click', '.category-edit', function() {
            let btn = $(this);
            btn.find('.spinner-border').removeClass('d-none');
            btn.find('.icon').addClass('d-none');
            let id = btn.data('id');
            let dataurl = btn.data('url');
            let updateurl = btn.data('udurl');

            $.ajax({
                type: "GET",
                dataType: "json",
                url: dataurl,
                data: {
                    id: id
                },
                success: function(data) {
                    $('#editCategoryForm').attr('action', updateurl);
                    $('#category_id').val(data.id);
                    $('#edit_category_title').val(data.name);
                    $('#edit_category_icon_alt').val(data.icon_alt);
                    if (typeof window.setMediaPickerValue === 'function') {
                        window.setMediaPickerValue('car_category_icon_edit', data.icon, data.icon ? (s3BaseUrl + data.icon) : null);
                    }
                    $('#editCategoryModal').modal('show');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    // Note: The original code used a Swal.fire() which is a SweetAlert2 library function.
                    // If you don't have this library, you would use a standard alert or a custom modal.
                    console.error('An error occurred while fetching category details:', textStatus, errorThrown);
                    //alert('An error occurred while fetching category details.');
                },
                complete: function() {
                    // Hide loader, show button text back
                    btn.find('.spinner-border').addClass('d-none');
                    btn.find('.icon').removeClass('d-none');
                }
            });
        });

        document.getElementById('editCategoryForm').addEventListener('submit', function(event) {
            event.preventDefault();
            $('.invalid-feedback').hide();
            $('#edit-submit-btn').prop('disabled', true);

            let form = this;
            let title = document.getElementById('edit_category_title').value.trim();
            let id = document.getElementById('category_id').value.trim();
            $.ajax({
                type: "GET",
                url: "{{ route('admin.car-categories.slug.duplicate_check') }}",
                data: {
                    'id': id,
                    'name': title
                },
                success: function(res) {
                    if (res.exists) {
                        $('.invalid-feedback').show();
                        $('.title-error').text('This category already exists');
                        $('.invalid-feedback').show();
                        $('#edit-submit-btn').prop('disabled', false);
                    } else {
                        $('.invalid-feedback').hide();
                        form.submit();
                    }
                },
                error: function(err) {
                    //form.submit();
                }
            });

        });

        $(document).on("click", ".delete-category", function(e) {
            e.preventDefault();
            let itemId = $(this).data("id");
            let itemUrl = $(this).data("url");
            let row = $(this).closest("tr"); // parent <tr>
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
                    popup: 'rounded-2xl shadow-lg', // Rounded + shadow
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
                            if (res.success) {
                                row.remove();
                                Swal.fire(
                                    "Deleted!",
                                    "The item has been deleted successfully.",
                                    "success"
                                );
                            }
                        },
                        error: function(xhr) {
                            toastr.error('Category not deleted!', 'Failed');
                        },
                        complete: function() {
                            // Hide loader, show button text back
                            btn.find('.spinner-border').addClass('d-none');
                            btn.find('.icon').removeClass('d-none');
                        }
                    });

                }
            });
        });

        document.getElementById('addCarForm').addEventListener('submit', function(event) {
            event.preventDefault();
            $('.invalid-feedback').hide();
            $('#add-car-submit-btn').prop('disabled', true);
            let form = this;
            let title = document.getElementById('add_car_title').value.trim();
            $.ajax({
                type: "GET",
                url: "{{ route('admin.cars.slug.duplicate_check') }}",
                data: {
                    'title': title
                },
                success: function(res) {
                    if (res.exists) {
                        $('.title-errorr').show();
                        $('.title-error').text('This title already exists');
                        $('.invalid-feedback').show();
                        $('#add-car-submit-btn').prop('disabled', false);
                    } else {
                        $('.title-errorr').hide();
                        form.submit();
                    }
                },
                error: function(err) {
                    form.submit();
                }
            });
        });


        $(document).on('change', '.car-status', function() {
            let car_status = $(this).prop('checked') === true ? 1 : 0;
            $.ajax({
                type: "PUT",
                dataType: "json",
                url: $(this).data('url'),
                data: {
                    'status': car_status
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

        $(document).on("click", ".delete-car", function(e) {
            e.preventDefault();
            let itemId = $(this).data("id");
            let itemUrl = $(this).data("url");
            let row = $(this).closest("tr"); // parent <tr>
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
                    popup: 'rounded-2xl shadow-lg', // Rounded + shadow
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
                            if (res.success) {
                                row.remove();
                                Swal.fire(
                                    "Deleted!",
                                    "The item has been deleted successfully.",
                                    "success"
                                );
                            }
                        },
                        error: function(xhr) {
                            toastr.error('Car not deleted!', 'Failed');
                        },
                        complete: function() {
                            // Hide loader, show button text back
                            btn.find('.spinner-border').addClass('d-none');
                            btn.find('.icon').removeClass('d-none');
                        }
                    });

                }
            });
        });

        $(document).on('click', '.car-edit', function() {
            let btn = $(this);
            btn.find('.spinner-border').removeClass('d-none');
            btn.find('.icon').addClass('d-none');
            let id = btn.data('id');
            let dataurl = btn.data('url');
            let updateurl = btn.data('udurl');

            $.ajax({
                type: "GET",
                dataType: "json",
                url: dataurl,
                data: {
                    id: id
                },
                success: function(data) {
                    $('#editCarForm').attr('action', updateurl);
                    $('#car_id').val(data.id);
                    $('#edit_car_title').val(data.title);
                    $('#edit_category_id').val(data.category_id);
                    $('#edit_fuel_type').val(data.fuel_type);
                    $('#edit_car_seats').val(data.seats);
                    $('#edit_primary_image_alt').val(data.primary_image_alt);
                    if (typeof window.setMediaPickerValue === 'function') {
                        window.setMediaPickerValue('car_primary_edit', data.primary_image, data.primary_image ? (s3BaseUrl + data.primary_image) : null);
                    }
                    $('#editCarModal').modal('show');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    // Note: The original code used a Swal.fire() which is a SweetAlert2 library function.
                    // If you don't have this library, you would use a standard alert or a custom modal.
                    console.error('An error occurred while fetching hotel details:', textStatus, errorThrown);
                    //alert('An error occurred while fetching hotel details.');
                },
                complete: function() {
                    // Hide loader, show button text back
                    btn.find('.spinner-border').addClass('d-none');
                    btn.find('.icon').removeClass('d-none');
                }
            });
        });

        function renderCarGalleryImages(images) {
            const wrapper = $('#carGalleryImagesWrapper');
            wrapper.empty();
            (images || []).forEach(function(img) {
                appendCarGalleryImage(img);
            });
        }

        function appendCarGalleryImage(img) {
            $('#carGalleryImagesWrapper').append(`
                <div class="col-md-3 mb-3" id="car-gallery-image-${img.id}">
                    <div class="gallery-img-wrap">
                        <img src="${s3BaseUrl + img.image}" class="img-fluid w-100" style="height:100px;object-fit:cover;border-radius:4px;">
                        <button type="button" class="gallery-remove-btn delete-car-gallery-image" data-id="${img.id}" data-url="{{ url('/admin/cars/gallery') }}/${img.id}" title="Remove"><i class="fas fa-times"></i></button>
                    </div>
                </div>
            `);
        }

        let carHighlightPickerSeq = 0;

        function carHighlightTagRow(text, icon) {
            const pickerId = 'car_highlight_' + (carHighlightPickerSeq++);
            const pickerHtml = typeof window.mediaPickerFieldHtml === 'function'
                ? window.mediaPickerFieldHtml('highlight_icons[0]', pickerId, '', 'cars/highlights')
                : '';
            const row = `
                <tr>
                    <td>${pickerHtml}</td>
                    <td>
                        <input type="text" name="items[0]" class="form-control" value="${text || ''}" placeholder="e.g. Spacious & Comfortable">
                    </td>
                    <td class="text-end" style="width:1%;"><button type="button" class="btn btn-sm btn-outline-danger rm-car-highlight-row"><i class="fas fa-trash"></i></button></td>
                </tr>
            `;
            if (icon && typeof window.setMediaPickerValue === 'function') {
                setTimeout(function() { window.setMediaPickerValue(pickerId, icon, s3BaseUrl + icon); }, 0);
            }
            return row;
        }

        function reindexCarHighlightRows() {
            $('#carHighlightTagsTableBody > tr').each(function(i) {
                $(this).find('[name^="items["]').attr('name', `items[${i}]`);
                $(this).find('.media-picker-value').attr('name', `highlight_icons[${i}]`);
            });
        }

        $(document).on('click', '.car-settings', function() {
            let btn = $(this);
            btn.find('.spinner-border').removeClass('d-none');
            btn.find('.icon').addClass('d-none');
            $.ajax({
                type: "GET",
                dataType: "json",
                url: "{{ route('admin.car.settings') }}",
                data: { id: btn.data('id') },
                success: function(data) {
                    let car = data.car;
                    $('#car-settings-title').text(btn.data('name'));
                    $('#carPageSettingForm').attr('action', btn.data('udurl'));
                    $('#addCarGalleryImageForm').attr('action', btn.data('galleryaddurl'));
                    $('#carHighlightTagsForm').attr('action', btn.data('highlighturl'));

                    if (typeof window.setMediaPickerValue === 'function') {
                        window.setMediaPickerValue('car_page_banner', car.banner_image, car.banner_image ? (s3BaseUrl + car.banner_image) : null);
                    }
                    $('#car_banner_image_alt').val(car.banner_image_alt);
                    tinymce.get('car_page_about') ? tinymce.get('car_page_about').setContent(car.description || '') : $('#car_page_about').val(car.description);

                    $('#car_specs_title').val(car.specs_title);
                    $('#car_specs_description').val(car.specs_description);
                    $('#car_vehicle_type').val(car.vehicle_type);
                    $('#car_transmission').val(car.transmission);
                    $('#car_luggage_capacity').val(car.luggage_capacity);
                    $('#car_mileage').val(car.mileage);
                    $('#car_gallery_title').val(car.gallery_title);
                    $('#car_gallery_description').val(car.gallery_description);
                    if (typeof window.setMediaPickerValue === 'function') {
                        window.setMediaPickerValue('car_gallery_add_image', '', null);
                    }
                    $('#carGalleryImageAlt').val('');

                    renderCarGalleryImages(car.gallery_images);

                    let tags = car.highlight_tags || [];
                    let body = $('#carHighlightTagsTableBody').empty();
                    if (tags.length) {
                        tags.forEach(t => body.append(carHighlightTagRow(t.text, t.icon)));
                    } else {
                        body.append(carHighlightTagRow('', ''));
                    }
                    reindexCarHighlightRows();

                    $('#carPageSettingModal').modal('show');
                },
                error: function() {
                    toastr.error('Failed to load car settings.');
                },
                complete: function() {
                    btn.find('.spinner-border').addClass('d-none');
                    btn.find('.icon').removeClass('d-none');
                }
            });
        });

        $(document).on('click', '#addCarHighlightTagRow', function(e) {
            e.preventDefault();
            $('#carHighlightTagsTableBody').append(carHighlightTagRow('', ''));
            reindexCarHighlightRows();
        });

        $(document).on('click', '.rm-car-highlight-row', function() {
            $(this).closest('tr').remove();
            reindexCarHighlightRows();
        });

        // ── The Car Page Settings modal used to have a separate Save button per section
        // (specs, gallery, highlights), which confused admins. These three helpers each
        // submit one section's form and return a promise; the single footer Save button
        // (#carSettingsSaveAll) below chains all three into one click. ──
        function submitCarPageSettingForm() {
            tinymce.triggerSave();
            let form = document.getElementById('carPageSettingForm');
            return $.ajax({
                type: 'POST',
                url: $(form).attr('action'),
                data: new FormData(form),
                processData: false,
                contentType: false
            });
        }

        function submitCarHighlightTagsForm() {
            let form = document.getElementById('carHighlightTagsForm');
            return $.ajax({
                type: 'POST',
                url: $(form).attr('action'),
                data: new FormData(form),
                processData: false,
                contentType: false
            });
        }

        function submitCarGalleryImageForm() {
            // gallery_title/gallery_description live in this form and save unconditionally
            // server-side, so it must always submit — not just when a new image is attached.
            let form = document.getElementById('addCarGalleryImageForm');
            return $.ajax({
                type: 'POST',
                url: $(form).attr('action'),
                data: $(form).serialize(),
                dataType: 'json'
            }).done(function(res) {
                if (res.image) {
                    appendCarGalleryImage(res.image);
                }
                if (typeof window.setMediaPickerValue === 'function') {
                    window.setMediaPickerValue('car_gallery_add_image', '', null);
                }
                $('#carGalleryImageAlt').val('');
            });
        }

        $(document).on('click', '#carSettingsSaveAll', function() {
            let btn = $(this).prop('disabled', true);
            submitCarPageSettingForm()
                .then(submitCarGalleryImageForm)
                .then(submitCarHighlightTagsForm)
                .then(function() {
                    toastr.success('Car page settings saved.');
                }, function(xhr) {
                    toastr.error((xhr && xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to save car page settings.');
                })
                .always(function() { btn.prop('disabled', false); });
        });

        $(document).on('click', '.delete-car-gallery-image', function() {
            let btn = $(this);
            let id = btn.data('id');
            Swal.fire({
                title: 'Delete Image?', icon: 'warning', showCancelButton: true,
                confirmButtonColor: '#dc3545', confirmButtonText: 'Yes, delete'
            }).then(function(r) {
                if (r.isConfirmed) {
                    $.ajax({ url: btn.data('url'), type: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
                    }).done(function() {
                        $('#car-gallery-image-' + id).remove();
                        toastr.success('Image removed.');
                    }).fail(function() { toastr.error('Failed to remove image.'); });
                }
            });
        });

        function carAmenityRow(label, description, icon) {
            let i = carAmenityIndex;
            const pickerId = 'car_amenity_' + i;
            const pickerHtml = typeof window.mediaPickerFieldHtml === 'function'
                ? window.mediaPickerFieldHtml(`amenities[${i}][icon]`, pickerId, '', 'cars/amenities')
                : '';
            let row = `
                <tr>
                    <td>${pickerHtml}</td>
                    <td><input type="text" name="amenities[${i}][label]" class="form-control" value="${label || ''}" placeholder="e.g. Air Conditioning"></td>
                    <td>
                        <input type="text" name="amenities[${i}][description]" class="form-control" value="${description || ''}" placeholder="e.g. Dual AC for a cool and comfortable ride.">
                    </td>
                    <td class="text-end" style="width:1%;"><button type="button" class="btn btn-sm btn-outline-danger rm-car-amenity-row"><i class="fas fa-trash"></i></button></td>
                </tr>
            `;
            carAmenityIndex++;
            if (icon && typeof window.setMediaPickerValue === 'function') {
                setTimeout(function () { window.setMediaPickerValue(pickerId, icon, s3BaseUrl + icon); }, 0);
            }
            return row;
        }

        $(document).on('click', '.car-amenities', function() {
            let btn = $(this);
            btn.find('.spinner-border').removeClass('d-none');
            btn.find('.icon').addClass('d-none');
            $.ajax({
                type: "GET",
                dataType: "json",
                url: "{{ route('admin.car.amenities') }}",
                data: { id: btn.data('id') },
                success: function(data) {
                    $('#car-amenities-title').text(btn.data('name'));
                    $('#carAmenitiesForm').attr('action', btn.data('udurl'));

                    carAmenityIndex = 0;
                    let amenities = data.car.amenities || [];
                    let body = $('#carAmenitiesTableBody').empty();
                    if (amenities.length) {
                        amenities.forEach(a => body.append(carAmenityRow(a.label, a.description, a.icon)));
                    } else {
                        body.append(carAmenityRow('', '', ''));
                    }

                    $('#carAmenitiesModal').modal('show');
                },
                error: function() {
                    toastr.error('Failed to load features & amenities.');
                },
                complete: function() {
                    btn.find('.spinner-border').addClass('d-none');
                    btn.find('.icon').removeClass('d-none');
                }
            });
        });

        $(document).on('click', '#addCarAmenityRow', function(e) {
            e.preventDefault();
            $('#carAmenitiesTableBody').append(carAmenityRow('', '', ''));
        });

        $(document).on('click', '.rm-car-amenity-row', function() {
            $(this).closest('tr').remove();
        });

        $(document).on('submit', '#carAmenitiesForm', function(e) {
            e.preventDefault();
            let form = this;
            let btn = $(form).find('button[type="submit"]').prop('disabled', true);
            $.ajax({
                type: 'POST',
                url: $(form).attr('action'),
                data: new FormData(form),
                processData: false,
                contentType: false,
                success: function() {
                    toastr.success('Features & Amenities saved.');
                    $('#carAmenitiesModal').modal('hide');
                },
                error: function(xhr) {
                    toastr.error((xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to save features & amenities.');
                },
                complete: function() { btn.prop('disabled', false); }
            });
        });

        document.getElementById('editCarForm').addEventListener('submit', function(event) {
            event.preventDefault();
            $('.invalid-feedback').hide();
            $('#edit-car-submit-btn').prop('disabled', true);
            let form = this;
            let title = document.getElementById('edit_car_title').value.trim();
            let id = document.getElementById('car_id').value.trim();
            $.ajax({
                type: "GET",
                url: "{{ route('admin.cars.slug.duplicate_check') }}",
                data: {
                    'id': id,
                    'title': title
                },
                success: function(res) {
                    if (res.exists) {
                        $('.title-errorr').show();
                        $('.title-error').text('This title already exists');
                        $('.invalid-feedback').show();
                        $('#edit-car-submit-btn').prop('disabled', false);
                    } else {
                        $('.title-errorr').hide();
                        form.submit();
                    }
                },
                error: function(err) {
                    //form.submit();
                }
            });
        });
    });
</script>
<script>
    $(document).ready(function() {

        let previousFrom = null;

        /*$('.from_location').on('change', function() {
            const selectedFrom = $(this).val();
            const $cityTo = $('.to_location');

            // Step 1: Restore previously hidden option (if any)
            if (previousFrom) {
                $cityTo.find(`option[value="${previousFrom}"]`).show();
            }

            // Step 2: Hide the newly selected option
            if (selectedFrom) {
                $cityTo.find(`option[value="${selectedFrom}"]`).hide();

                // If destination is same as source, reset destination
                if ($cityTo.val() === selectedFrom) {
                    $cityTo.val('');
                }
            }

            // Step 3: Remember current selection
            previousFrom = selectedFrom;
        });*/

        $(document).on('change', '.from_location, .to_location', function() {
            let $form = $(this).closest('form');
            let from = $form.find('.from_location').val();
            let to = $form.find('.to_location').val();
            let sameCity = from && to && from === to;

            $form.find('.location-error').text(sameCity ? 'Source and destination city cannot be the same' : '');
            sameCity ? $form.find('.invalid-feedback').show() : $form.find('.invalid-feedback').hide();
            $form.find('button[type="submit"]').prop('disabled', sameCity);
        });

        document.getElementById('addRouteForm').addEventListener('submit', function(event) {
            event.preventDefault();
            $('.invalid-feedback').hide();

            if ($('#add_from_location').val() && $('#add_from_location').val() === $('#add_to_location').val()) {
                $('.location-error').text('Source and destination city cannot be the same');
                $('.invalid-feedback').show();
                return;
            }

            $('#add-route-submit-btn').prop('disabled', true);
            let form = this;
            $.ajax({
                type: "GET",
                url: "{{ route('admin.car-routes.slug.duplicate_check') }}",
                data: $(form).serialize(),
                success: function(res) {
                    if (res.exists) {
                        $('.location-errorr').show();
                        $('.location-error').text('This route already exists');
                        $('.invalid-feedback').show();
                        $('#add-route-submit-btn').prop('disabled', false);
                    } else {
                        $('.location-errorr').hide();
                        form.submit();
                    }
                },
                error: function(err) {
                    form.submit();
                }
            });
        });


        $(document).on('change', '.route-status', function() {
            let route_status = $(this).prop('checked') === true ? 1 : 0;
            $.ajax({
                type: "PUT",
                dataType: "json",
                url: $(this).data('url'),
                data: {
                    'status': route_status
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

        $(document).on('change', '.route-popular', function() {
            let route_popular = $(this).prop('checked') === true ? 1 : 0;
            $.ajax({
                type: "PUT",
                dataType: "json",
                url: $(this).data('url'),
                data: {
                    'is_popular': route_popular
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

        $(document).on('click', '.route-edit', function() {
            let btn = $(this);
            btn.find('.spinner-border').removeClass('d-none');
            btn.find('.icon').addClass('d-none');
            let id = btn.data('id');
            let dataurl = btn.data('url');
            let updateurl = btn.data('udurl');

            $.ajax({
                type: "GET",
                dataType: "json",
                url: dataurl,
                data: {
                    id: id
                },
                success: function(data) {
                    $('#editRouteForm').attr('action', updateurl);
                    $('#id').val(data.id);
                    $('#edit_from_location').val(data.from_location);
                    $('#edit_to_location').val(data.to_location);
                    $('#edit_route_display_label').val(data.display_label);

                    if (data.details) {
                        $('#route_page_title').val(data.details.title);
                        $('#route_page_banner_alt').val(data.details.banner_image_alt);
                        tinymce.get('route_page_about') ? tinymce.get('route_page_about').setContent(data.details.description || '') : $('#route_page_about').val(data.details.description);
                        if (typeof window.setMediaPickerValue === 'function') {
                            window.setMediaPickerValue('car_route_banner', data.details.banner_image, data.details.banner_image ? (s3BaseUrl + data.details.banner_image) : null);
                        }
                    } else {
                        $('#route_page_title').val('');
                        tinymce.get('route_page_about') ? tinymce.get('route_page_about').setContent('') : $('#route_page_about').val('');
                        if (typeof window.setMediaPickerValue === 'function') {
                            window.setMediaPickerValue('car_route_banner', '', null);
                        }
                    }
                    //filterDestinationCities();
                    $('#editRouteModal').modal('show');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    // Note: The original code used a Swal.fire() which is a SweetAlert2 library function.
                    // If you don't have this library, you would use a standard alert or a custom modal.
                    console.error('An error occurred while fetching route details:', textStatus, errorThrown);
                    //alert('An error occurred while fetching route details.');
                },
                complete: function() {
                    // Hide loader, show button text back
                    btn.find('.spinner-border').addClass('d-none');
                    btn.find('.icon').removeClass('d-none');
                }
            });
        });

        document.getElementById('editRouteForm').addEventListener('submit', function(event) {
            event.preventDefault();
            tinymce.triggerSave();
            $('.invalid-feedback').hide();

            if ($('#edit_from_location').val() && $('#edit_from_location').val() === $('#edit_to_location').val()) {
                $('.location-error').text('Source and destination city cannot be the same');
                $('.invalid-feedback').show();
                return;
            }

            $('#edit-route-submit-btn').prop('disabled', true);

            let form = this;
            $.ajax({
                type: "GET",
                url: "{{ route('admin.car-routes.slug.duplicate_check') }}",
                data: $(form).serialize(),
                success: function(res) {
                    if (res.exists) {
                        $('.invalid-feedback').show();
                        $('.location-error').text('This route already exists');
                        $('.invalid-feedback').show();
                        $('#edit-route-submit-btn').prop('disabled', false);
                    } else {
                        $('.invalid-feedback').hide();
                        form.submit();
                    }
                },
                error: function(err) {
                    //form.submit();
                }
            });

        });

        $(document).on("click", ".delete-route", function(e) {
            e.preventDefault();
            let itemId = $(this).data("id");
            let itemUrl = $(this).data("url");
            let row = $(this).closest("tr"); // parent <tr>
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
                    popup: 'rounded-2xl shadow-lg', // Rounded + shadow
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
                            if (res.success) {
                                row.remove();
                                Swal.fire(
                                    "Deleted!",
                                    "The item has been deleted successfully.",
                                    "success"
                                );
                            }
                        },
                        error: function(xhr) {
                            toastr.error('Route not deleted!', 'Failed');
                        },
                        complete: function() {
                            // Hide loader, show button text back
                            btn.find('.spinner-border').addClass('d-none');
                            btn.find('.icon').removeClass('d-none');
                        }
                    });

                }
            });
        });

        $(document).on('click', '.route-car', function() {
            let btn = $(this);
            btn.find('.spinner-border').removeClass('d-none');
            btn.find('.icon').addClass('d-none');
            let id = btn.data('id');
            let dataurl = btn.data('url');

            $.ajax({
                type: "GET",
                dataType: "json",
                url: dataurl,
                data: {
                    id: id
                },
                success: function(res) {
                    $('#routeCar').text(res.route.slug);
                    $('#route_id').val(res.route.id);
                    const allCars = res.all_car;
                    let assignedCars = res.route.cars.map(c => c.id); // already assigned
                    let html = '<div class="row">';
                    allCars.forEach((car, index) => {
                        const checked = assignedCars.includes(car.id) ? 'checked' : '';
                        html += `
                            <div class="col-md-4 col-sm-4 col-6"> <!-- Adjust columns as needed -->
                                <div class="form-check">
                                    <input type="checkbox" name="car_ids[]" class="form-check-input car-checkbox" value="${car.id}" ${checked}>
                                    <label class="form-check-label">${car.title} (${car.category?.name ?? 'No Category'})</label>
                                </div>
                            </div>
                        `;
                    });
                    html += '</div>';
                    $('#car-list').html(html);
                    $('#routeCarModal').modal('show');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    // Note: The original code used a Swal.fire() which is a SweetAlert2 library function.
                    // If you don't have this library, you would use a standard alert or a custom modal.
                    console.error('An error occurred while fetching route details:', textStatus, errorThrown);
                    //alert('An error occurred while fetching route details.');
                },
                complete: function() {
                    // Hide loader, show button text back
                    btn.find('.spinner-border').addClass('d-none');
                    btn.find('.icon').removeClass('d-none');
                }
            });
        });

        $(document).on('click', '.car-route-meta', function() {
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
                    $('#car-route-title').text('# Route (' + data.slug + '_-Meta Info');
                    if (data.meta != null) {
                        $('#car_route_meta_title').val(data.meta.meta_title);
                        $('#car_route_meta_description').val(data.meta.meta_description);
                        $('#car_route_meta_keywords').val(data.meta.meta_keywords);
                        $('#car_route_h1_heading').val(data.meta.h1_heading);
                        $('#car_route_meta_details').val(data.meta.meta_details);
                    }
                    $('#carRouteMeta').attr('action', dataupUrl);
                    $('#carRouteMetaModal').modal('show');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while fetching route details.'
                    });
                },
                complete: function() {
                    // Hide loader, show button text back
                    btn.find('.spinner-border').addClass('d-none');
                    btn.find('.icon').removeClass('d-none');
                }
            });
        });

        $(document).on('click', '.add-route-page-details', function() {
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
                    $('#route-title').text(data.from_location + ' to ' + data.to_location);
                    $('#route_about_title').val('');
                    $('#route_about_image_alt').val('');
                    tinymce.get('route_about_description') ? tinymce.get('route_about_description').setContent('') : $('#route_about_description').val('');
                    $('#route_distance_text').val('');
                    $('#route_duration_text').val('');
                    $('#route_number').val('');
                    $('#route_best_season').val('');
                    if (typeof window.setMediaPickerValue === 'function') {
                        window.setMediaPickerValue('car_route_about', '', null);
                    }
                    if (data.details != null) {
                        $('#route_about_title').val(data.details.about_title);
                        $('#route_about_image_alt').val(data.details.about_image_alt);
                        tinymce.get('route_about_description') ? tinymce.get('route_about_description').setContent(data.details.about_description || '') : $('#route_about_description').val(data.details.about_description);
                        $('#route_distance_text').val(data.details.distance_text);
                        $('#route_duration_text').val(data.details.duration_text);
                        $('#route_number').val(data.details.route_number);
                        $('#route_best_season').val(data.details.best_season);
                        if (typeof window.setMediaPickerValue === 'function') {
                            window.setMediaPickerValue('car_route_about', data.details.about_image, data.details.about_image ? (s3BaseUrl + data.details.about_image) : null);
                        }
                    }
                    $('#routePageSetting').attr('action', dataUrl);
                    $('#routePageSettingModal').modal('show');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while fetching country details.'
                    });
                },
                complete: function() {
                    // Hide loader, show button text back
                    btn.find('.spinner-border').addClass('d-none');
                    btn.find('.icon').removeClass('d-none');
                }
            });
        });

        $(document).on('click', '.route-faqs', function() {
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
                url: "{{ route('admin.car.routes.faq') }}", // better to point this to a dedicated faq route
                data: {
                    id: id
                },
                success: function(data) {
                    $('#faqModal .modal-title').text(data.car_route.from_location + ' to ' + data.car_route.to_location + ' Page FAQ');
                    let faqs = data.car_route.faqs || [];
                    let body = `
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="faq_title" class="form-label">Section Title<span class="required-text">*</span></label>
                                <input value="${data.car_route.faq_title ? data.car_route.faq_title : ''}" class="form-control" name="faq_title" id="faq_title" placeholder="" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="faq_sub_title" class="form-label">Section Sub Title</label>
                                <input value="${data.car_route.faq_sub_title ? data.car_route.faq_sub_title : ''}" class="form-control" name="faq_sub_title" id="faq_sub_title" placeholder="">
                            </div>
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
                complete: function() {
                    // Hide loader, show button text back
                    btn.find('.spinner-border').addClass('d-none');
                    btn.find('.icon').removeClass('d-none');
                }
            });
        });

    });

    // function filterDestinationCities() {
    //     let selectedSource = $('#edit_from_location').val();

    //     $('#edit_to_location option').each(function() {
    //         let option = $(this);

    //         // if this option value equals selected source, hide it
    //         if (option.val() == selectedSource) {
    //             option.prop('disabled', true).hide();
    //         } else {
    //             option.prop('disabled', false).show();
    //         }
    //     });
    // }
</script>

<script>
    $(document).ready(function() {

        let previousFrom = null;

        document.getElementById('addCityForm').addEventListener('submit', function(event) {
            event.preventDefault();
            $('.invalid-feedback').hide();
            $('#add-city-submit-btn').prop('disabled', true);
            let form = this;
            $.ajax({
                type: "GET",
                url: "{{ route('admin.car-city.slug.duplicate_check') }}",
                data: $(form).serialize(),
                success: function(res) {
                    if (res.exists) {
                        $('.location-errorr').show();
                        $('.location-error').text('This city already exists');
                        $('.invalid-feedback').show();
                        $('#add-city-submit-btn').prop('disabled', false);
                    } else {
                        $('.location-errorr').hide();
                        form.submit();
                    }
                },
                error: function(err) {
                    form.submit();
                }
            });
        });


        $(document).on('change', '.city-status', function() {
            let city_status = $(this).prop('checked') === true ? 1 : 0;
            $.ajax({
                type: "PUT",
                dataType: "json",
                url: $(this).data('url'),
                data: {
                    'status': city_status
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

        $(document).on('change', '.city-popular', function() {
            let city_popular = $(this).prop('checked') === true ? 1 : 0;
            $.ajax({
                type: "PUT",
                dataType: "json",
                url: $(this).data('url'),
                data: {
                    'is_popular': city_popular
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

        $(document).on('click', '.city-edit', function() {
            let btn = $(this);
            btn.find('.spinner-border').removeClass('d-none');
            btn.find('.icon').addClass('d-none');
            let id = btn.data('id');
            let dataurl = btn.data('url');
            let updateurl = btn.data('udurl');

            $.ajax({
                type: "GET",
                dataType: "json",
                url: dataurl,
                data: {
                    id: id
                },
                success: function(data) {
                    $('#editCityForm').attr('action', updateurl);
                    $('#cityID').val(data.id);
                    if (typeof window.setMediaPickerValue === 'function') {
                        window.setMediaPickerValue('car_city_thumbnail_edit', data.thumbnail_image_path, data.thumbnail_image || null);
                    }
                    $('#edit_location').val(data.location);
                    $('#edit_city_thumbnail_alt').val(data.thumbnail_alt);
                    $('#edit_city_display_label').val(data.display_label);
                    $('#city_page_title').val(data.details ? data.details.title : '');

                    $('#editCityModal').modal('show');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    // Note: The original code used a Swal.fire() which is a SweetAlert2 library function.
                    // If you don't have this library, you would use a standard alert or a custom modal.
                    console.error('An error occurred while fetching city details:', textStatus, errorThrown);
                    //alert('An error occurred while fetching city details.');
                },
                complete: function() {
                    // Hide loader, show button text back
                    btn.find('.spinner-border').addClass('d-none');
                    btn.find('.icon').removeClass('d-none');
                }
            });
        });

        $(document).on('click', '.add-city-page-details', function() {
            let btn = $(this);
            btn.find('.spinner-border').removeClass('d-none');
            btn.find('.icon').addClass('d-none');
            $.ajax({
                type: "GET",
                dataType: "json",
                url: btn.data('url'),
                success: function(data) {
                    $('#city-page-setting-title').text(data.location);
                    $('#cityPageSetting').attr('action', btn.data('udurl'));

                    let aboutContent = data.details ? (data.details.description || '') : '';
                    tinymce.get('city_page_about') ? tinymce.get('city_page_about').setContent(aboutContent) : $('#city_page_about').val(aboutContent);

                    $('#city_why_choose_enabled').prop('checked', !!data.why_choose_enabled);

                    $('#cityPageSettingModal').modal('show');
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while fetching city details.'
                    });
                },
                complete: function() {
                    btn.find('.spinner-border').addClass('d-none');
                    btn.find('.icon').removeClass('d-none');
                }
            });
        });

        document.getElementById('editCityForm').addEventListener('submit', function(event) {
            event.preventDefault();
            $('.invalid-feedback').hide();
            $('#edit-city-submit-btn').prop('disabled', true);

            let form = this;
            $.ajax({
                type: "GET",
                url: "{{ route('admin.car-city.slug.duplicate_check') }}",
                data: $(form).serialize(),
                success: function(res) {
                    if (res.exists) {
                        $('.invalid-feedback').show();
                        $('.location-error').text('This city already exists');
                        $('.invalid-feedback').show();
                        $('#edit-city-submit-btn').prop('disabled', false);
                    } else {
                        $('.invalid-feedback').hide();
                        form.submit();
                    }
                },
                error: function(err) {
                    //form.submit();
                }
            });

        });

        $(document).on("click", ".delete-city", function(e) {
            e.preventDefault();
            let itemId = $(this).data("id");
            let itemUrl = $(this).data("url");
            let row = $(this).closest("tr"); // parent <tr>
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
                    popup: 'rounded-2xl shadow-lg', // Rounded + shadow
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
                            if (res.success) {
                                row.remove();
                                Swal.fire(
                                    "Deleted!",
                                    "The item has been deleted successfully.",
                                    "success"
                                );
                            }
                        },
                        error: function(xhr) {
                            toastr.error('Route not deleted!', 'Failed');
                        },
                        complete: function() {
                            // Hide loader, show button text back
                            btn.find('.spinner-border').addClass('d-none');
                            btn.find('.icon').removeClass('d-none');
                        }
                    });

                }
            });
        });

        $(document).on('click', '.city-car', function() {
            let btn = $(this);
            btn.find('.spinner-border').removeClass('d-none');
            btn.find('.icon').addClass('d-none');
            let id = btn.data('id');
            let dataurl = btn.data('url');

            $.ajax({
                type: "GET",
                dataType: "json",
                url: dataurl,
                data: {
                    id: id
                },
                success: function(res) {
                    $('#cityCar').text(res.city.slug);
                    $('#city_id').val(res.city.id);
                    const allCars = res.all_car;
                    let assignedCars = res.city.cars.map(c => c.id); // already assigned
                    let html = '<div class="row">';
                    allCars.forEach((car, index) => {
                        const checked = assignedCars.includes(car.id) ? 'checked' : '';
                        html += `
                            <div class="col-md-4 col-sm-4 col-6"> <!-- Adjust columns as needed -->
                                <div class="form-check">
                                    <input type="checkbox" name="car_ids[]" class="form-check-input car-checkbox" value="${car.id}" ${checked}>
                                    <label class="form-check-label">${car.title} (${car.category?.name ?? 'No Category'})</label>
                                </div>
                            </div>
                        `;
                    });
                    html += '</div>';
                    $('#city-car-list').html(html);
                    $('#cityCarModal').modal('show');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    // Note: The original code used a Swal.fire() which is a SweetAlert2 library function.
                    // If you don't have this library, you would use a standard alert or a custom modal.
                    console.error('An error occurred while fetching city details:', textStatus, errorThrown);
                    //alert('An error occurred while fetching city details.');
                },
                complete: function() {
                    // Hide loader, show button text back
                    btn.find('.spinner-border').addClass('d-none');
                    btn.find('.icon').removeClass('d-none');
                }
            });
        });

        $(document).on('click', '.car-city-meta', function() {
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
                    $('#car-city-title').text('# Route (' + data.slug + '_-Meta Info');
                    if (data.meta != null) {
                        $('#car_city_meta_title').val(data.meta.meta_title);
                        $('#car_city_meta_description').val(data.meta.meta_description);
                        $('#car_city_meta_keywords').val(data.meta.meta_keywords);
                        $('#car_city_h1_heading').val(data.meta.h1_heading);
                        $('#car_city_meta_details').val(data.meta.meta_details);
                    }
                    $('#carCityMeta').attr('action', dataupUrl);
                    $('#carCityMetaModal').modal('show');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while fetching city details.'
                    });
                },
                complete: function() {
                    // Hide loader, show button text back
                    btn.find('.spinner-border').addClass('d-none');
                    btn.find('.icon').removeClass('d-none');
                }
            });
        });

        function renderCityGalleryImages(images) {
            const wrapper = $('#cityGalleryImagesWrapper');
            wrapper.empty();
            (images || []).forEach(function(img) {
                wrapper.append(`
                    <div class="col-md-3 mb-3" id="city-gallery-image-${img.id}">
                        <div class="gallery-img-wrap">
                            <img src="${s3BaseUrl + img.image}" class="img-fluid w-100" style="height:120px;object-fit:cover;border-radius:4px;">
                            <button type="button" class="gallery-remove-btn delete-city-gallery-image" data-id="${img.id}" data-url="{{ url('/admin/car-city/gallery') }}/${img.id}" title="Remove"><i class="fas fa-times"></i></button>
                        </div>
                    </div>
                `);
            });
        }

        $(document).on('click', '.city-gallery', function() {
            let btn = $(this);
            btn.find('.spinner-border').removeClass('d-none');
            btn.find('.icon').addClass('d-none');
            $.ajax({
                type: 'GET',
                dataType: 'json',
                url: btn.data('geturl'),
                success: function(data) {
                    $('#city-gallery-title').text(btn.data('name'));
                    renderCityGalleryImages(data.car_city.gallery_images);
                    $('#addCityGalleryImageForm').attr('action', btn.data('addurl'));
                    $('#city_gallery_title').val(data.car_city.details ? (data.car_city.details.gallery_title || '') : '');
                    $('#city_gallery_description').val(data.car_city.details ? (data.car_city.details.gallery_description || '') : '');
                    if (typeof window.resetMediaGalleryPicker === 'function') {
                        window.resetMediaGalleryPicker('car_city_gallery_add');
                    }
                    $('#cityGalleryModal').modal('show');
                },
                error: function() {
                    toastr.error('Failed to load gallery images.');
                },
                complete: function() {
                    btn.find('.spinner-border').addClass('d-none');
                    btn.find('.icon').removeClass('d-none');
                }
            });
        });

        $(document).on('click', '.delete-city-gallery-image', function() {
            let btn = $(this);
            let id = btn.data('id');
            Swal.fire({
                title: 'Delete Image?', icon: 'warning', showCancelButton: true,
                confirmButtonColor: '#dc3545', confirmButtonText: 'Yes, delete'
            }).then(function(r) {
                if (r.isConfirmed) {
                    $.ajax({ url: btn.data('url'), type: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
                    }).done(function() {
                        $('#city-gallery-image-' + id).remove();
                        toastr.success('Image removed.');
                    }).fail(function() { toastr.error('Failed to remove image.'); });
                }
            });
        });

        function cityFeatureBenefitRow(text, idx, tableBodySelector) {
            return `
                <tr>
                    <td><input type="text" name="items[]" class="form-control" value="${text || ''}"></td>
                    <td class="text-end" style="width:1%;"><button type="button" class="btn btn-sm btn-outline-danger rm-city-fb-row"><i class="fas fa-trash"></i></button></td>
                </tr>
            `;
        }

        $(document).on('click', '.city-features-benefits', function() {
            let btn = $(this);
            btn.find('.spinner-border').removeClass('d-none');
            btn.find('.icon').addClass('d-none');
            $.ajax({
                type: 'GET',
                dataType: 'json',
                url: btn.data('geturl'),
                success: function(data) {
                    const city = data.car_city;
                    $('#city-fb-title').text(btn.data('name'));
                    $('#cityFeaturesForm').attr('action', btn.data('featuresurl'));
                    $('#cityBenefitsForm').attr('action', btn.data('benefitsurl'));
                    $('#city_features_title').val(city.features_title);
                    $('#city_benefits_title').val(city.benefits_title);

                    const fBody = $('#cityFeaturesTableBody').empty();
                    if (city.features && city.features.length) {
                        city.features.forEach(f => fBody.append(cityFeatureBenefitRow(f.text)));
                    } else {
                        fBody.append(cityFeatureBenefitRow('', 0));
                    }

                    const bBody = $('#cityBenefitsTableBody').empty();
                    if (city.benefits && city.benefits.length) {
                        city.benefits.forEach(b => bBody.append(cityFeatureBenefitRow(b.text)));
                    } else {
                        bBody.append(cityFeatureBenefitRow('', 0));
                    }

                    $('#cityFeaturesBenefitsModal').modal('show');
                },
                error: function() {
                    toastr.error('Failed to load features & benefits.');
                },
                complete: function() {
                    btn.find('.spinner-border').addClass('d-none');
                    btn.find('.icon').removeClass('d-none');
                }
            });
        });

        $(document).on('click', '#addCityFeatureRow', function(e) {
            e.preventDefault();
            $('#cityFeaturesTableBody').append(cityFeatureBenefitRow(''));
        });
        $(document).on('click', '#addCityBenefitRow', function(e) {
            e.preventDefault();
            $('#cityBenefitsTableBody').append(cityFeatureBenefitRow(''));
        });
        $(document).on('click', '.rm-city-fb-row', function() {
            $(this).closest('tr').remove();
        });

        $(document).on('click', '#cityFeaturesBenefitsSaveAll', function() {
            const btn = $(this).prop('disabled', true);
            const formSelectors = ['#cityFeaturesForm', '#cityBenefitsForm'];
            const csrfToken = $('meta[name="csrf-token"]').attr('content');

            const requests = formSelectors.map(function(sel) {
                const $form = $(sel);
                return $.ajax({
                    type: 'POST',
                    url: $form.attr('action'),
                    data: $form.serialize(),
                    headers: { 'X-CSRF-TOKEN': csrfToken }
                });
            });

            $.when.apply($, requests).done(function() {
                toastr.success('Saved successfully.');
            }).fail(function() {
                toastr.error('Some changes failed to save. Please try again.');
            }).always(function() {
                btn.prop('disabled', false);
            });
        });

        $(document).on('click', '.city-faqs', function() {
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
                url: "{{ route('admin.car.city.faq') }}", // better to point this to a dedicated faq route
                data: {
                    id: id
                },
                success: function(data) {
                    $('#faqModal .modal-title').text(data.car_city.location);
                    let faqs = data.car_city.faqs || [];
                    let body = `
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="faq_title" class="form-label">Section Title<span class="required-text">*</span></label>
                                <input value="${data.car_city.faq_title ? data.car_city.faq_title : ''}" class="form-control" name="faq_title" id="faq_title" placeholder="" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="faq_sub_title" class="form-label">Section Sub Title</label>
                                <input value="${data.car_city.faq_sub_title ? data.car_city.faq_sub_title : ''}" class="form-control" name="faq_sub_title" id="faq_sub_title" placeholder="">
                            </div>
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
                complete: function() {
                    // Hide loader, show button text back
                    btn.find('.spinner-border').addClass('d-none');
                    btn.find('.icon').removeClass('d-none');
                }
            });
        });

    });
    // ===================== ROUTE SEARCH AJAX =====================
    const routeSearchUrl = "{{ route('admin.page-settings.car') }}";

    function loadRoutes(search, page) {
        $.ajax({
            url: routeSearchUrl,
            type: 'GET',
            data: {
                route_search: search,
                active_tab: 'route_list',
                page: page || 1
            },
            success: function(html) {
                let parsed = $(html);
                let newTbody = parsed.find('#routeTableBody').html();
                let newPagination = parsed.find('#routePagination').html();
                if (newTbody !== undefined) $('#routeTableBody').html(newTbody);
                if (newPagination !== undefined) $('#routePagination').html(newPagination);
                initSwitches();

            }
        });
    }

    function reinitRouteSwitches() {
        $('#routeTableBody .js-switch').each(function() {
            if (!$(this).data('switchery-init')) {
                new Switchery(this);
                $(this).data('switchery-init', true);
            }
        });
    }

    let routeSearchTimer;
    $('#routeSearch').on('keyup input', function() {
        clearTimeout(routeSearchTimer);
        let val = $(this).val();
        routeSearchTimer = setTimeout(function() {
            loadRoutes(val, 1);
        }, 400);
    });

    $(document).on('click', '#routePagination a', function(e) {
        e.preventDefault();
        let href = $(this).attr('href');
        if (!href) return;
        let page = new URL(href, window.location.origin).searchParams.get('page') || 1;
        loadRoutes($('#routeSearch').val(), page);
    });

    // ===================== CITY SEARCH AJAX =====================
    const citySearchUrl = "{{ route('admin.page-settings.car') }}";

    function loadCities(search, page) {
        $.ajax({
            url: citySearchUrl,
            type: 'GET',
            data: {
                city_search: search,
                active_tab: 'city_list',
                page: page || 1
            },
            success: function(html) {
                let parsed = $(html);
                let newTbody = parsed.find('#cityTableBody').html();
                let newPagination = parsed.find('#cityPagination').html();
                if (newTbody !== undefined) $('#cityTableBody').html(newTbody);
                if (newPagination !== undefined) $('#cityPagination').html(newPagination);
                initSwitches();
            }
        });
    }

    function reinitCitySwitches() {
        $('#cityTableBody .js-switch').each(function() {
            if (!$(this).data('switchery-init')) {
                new Switchery(this);
                $(this).data('switchery-init', true);
            }
        });
    }

    let citySearchTimer;
    $('#citySearch').on('keyup input', function() {
        clearTimeout(citySearchTimer);
        let val = $(this).val();
        citySearchTimer = setTimeout(function() {
            loadCities(val, 1);
        }, 400);
    });

    $(document).on('click', '#cityPagination a', function(e) {
        e.preventDefault();
        let href = $(this).attr('href');
        if (!href) return;
        let page = new URL(href, window.location.origin).searchParams.get('page') || 1;
        loadCities($('#citySearch').val(), page);
    });

    function initSwitches() {

        // ❌ remove old switch UI (duplicate fix)
        document.querySelectorAll('.switchery').forEach(el => el.remove());

        // ❌ reset old instances
        document.querySelectorAll('.js-switch').forEach(el => {
            el.switchery = null;
        });

        // ✅ re-init
        document.querySelectorAll('.js-switch').forEach(el => {
            new Switchery(el, {
                size: 'small'
            });
        });
    }

    // ── Checklist repeater ──────────────────────────────────────────────────
    $(document).on('click', '#addChecklistRow', function() {
        $('#checklistTable tbody').append(
            '<tr><td><input type="text" name="items[]" class="form-control"></td>' +
            '<td><button type="button" class="btn btn-sm btn-outline-danger rm-checklist"><i class="fas fa-trash"></i></button></td></tr>'
        );
    });
    $(document).on('click', '.rm-checklist', function() {
        $(this).closest('tr').remove();
    });

    // ── About gallery images ────────────────────────────────────────────────
    $(document).on('click', '.delete-gallery-image', function() {
        let btn = $(this);
        let id = btn.data('id');
        Swal.fire({
            title: 'Delete Image?', icon: 'warning', showCancelButton: true,
            confirmButtonColor: '#dc3545', confirmButtonText: 'Yes, delete'
        }).then(function(r) {
            if (r.isConfirmed) {
                $.ajax({ url: btn.data('url'), type: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
                }).done(function() {
                    $('#gallery-image-' + id).remove();
                    toastr.success('Image removed.');
                }).fail(function() { toastr.error('Failed to remove image.'); });
            }
        });
    });

    // ── Why choose us stats — add/remove rows locally, then Save persists them all at once ──
    let whyChoosePickerSeq = 1000;
    function whyChooseStatRow(label) {
        const pickerId = 'why_choose_' + (whyChoosePickerSeq++);
        const pickerHtml = typeof window.mediaPickerFieldHtml === 'function'
            ? window.mediaPickerFieldHtml('why_choose_icons[0]', pickerId, '', 'car-rental/why-choose')
            : '';
        return `
            <tr>
                <td>${pickerHtml}</td>
                <td>
                    <input type="text" name="labels[0]" class="form-control" value="${label || ''}" placeholder="e.g. 100+ Fleet Size">
                </td>
                <td class="text-end" style="width:1%;"><button type="button" class="btn btn-sm btn-outline-danger rm-why-choose-stat-row"><i class="fas fa-trash"></i></button></td>
            </tr>
        `;
    }
    function reindexWhyChooseRows() {
        $('#whyChooseStatsTableBody > tr').each(function(i) {
            $(this).find('[name^="labels["]').attr('name', `labels[${i}]`);
            $(this).find('.media-picker-value').attr('name', `why_choose_icons[${i}]`);
        });
    }
    $(document).on('click', '#addWhyChooseStatRow', function(e) {
        e.preventDefault();
        $('#whyChooseStatsTableBody').append(whyChooseStatRow(''));
        reindexWhyChooseRows();
    });
    $(document).on('click', '.rm-why-choose-stat-row', function() {
        $(this).closest('tr').remove();
        reindexWhyChooseRows();
    });

    $(document).on('click', '.delete-amenity', function() {
        let btn = $(this);
        let id = btn.data('id');
        Swal.fire({
            title: 'Delete Amenity?', icon: 'warning', showCancelButton: true,
            confirmButtonColor: '#dc3545', confirmButtonText: 'Yes, delete'
        }).then(function(r) {
            if (r.isConfirmed) {
                $.ajax({ url: btn.data('url'), type: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
                }).done(function() {
                    $('#amenity-' + id).remove();
                    toastr.success('Amenity removed.');
                }).fail(function() { toastr.error('Failed to remove amenity.'); });
            }
        });
    });


    // ═══════════════════════════════════════════════════════════════════
    // PACKAGE LIST
    // ═══════════════════════════════════════════════════════════════════
    document.getElementById('addPackageForm').addEventListener('submit', function(event) {
        event.preventDefault();
        $('.package-name-error').hide();
        $('#add-package-submit-btn').prop('disabled', true);
        let form = this;
        $.ajax({
            type: "GET",
            url: "{{ route('admin.car-packages.slug.duplicate_check') }}",
            data: $(form).serialize(),
            success: function(res) {
                if (res.exists) {
                    $('.package-name-error').text('This package already exists').show();
                    $('#add-package-submit-btn').prop('disabled', false);
                } else {
                    form.submit();
                }
            },
            error: function() { form.submit(); }
        });
    });

    $(document).on('change', '.package-status', function() {
        $.ajax({
            type: "PUT", dataType: "json", url: $(this).data('url'), data: { 'status': $(this).prop('checked') ? 1 : 0 },
            success: function(data) { Swal.fire({ icon: 'success', title: 'Success', text: data.message }); },
            error: function() { Swal.fire({ icon: 'error', title: 'Error', text: 'An error occurred.' }); }
        });
    });

    $(document).on('change', '.package-popular', function() {
        $.ajax({
            type: "PUT", dataType: "json", url: $(this).data('url'), data: { 'is_popular': $(this).prop('checked') ? 1 : 0 },
            success: function(data) { Swal.fire({ icon: 'success', title: 'Success', text: data.message }); },
            error: function() { Swal.fire({ icon: 'error', title: 'Error', text: 'An error occurred.' }); }
        });
    });

    $(document).on('click', '.package-edit', function() {
        let btn = $(this);
        $.ajax({
            type: "GET", dataType: "json", url: btn.data('url'), data: { id: btn.data('id') },
            success: function(data) {
                $('#editPackageForm').attr('action', btn.data('udurl'));
                $('#edit_package_name').val(data.name);
                $('#package_page_banner_alt').val(data.banner_image_alt);
                tinymce.get('package_page_about') ? tinymce.get('package_page_about').setContent(data.description || '') : $('#package_page_about').val(data.description);
                if (typeof window.setMediaPickerValue === 'function') {
                    window.setMediaPickerValue('car_package_banner', data.banner_image, data.banner_image ? (s3BaseUrl + data.banner_image) : null);
                }
                $('#editPackageModal').modal('show');
            },
            error: function() { toastr.error('Failed to load package.'); }
        });
    });

    document.getElementById('editPackageForm').addEventListener('submit', function(event) {
        event.preventDefault();
        tinymce.triggerSave();
        this.submit();
    });

    document.getElementById('whyChooseStatsForm').addEventListener('submit', function(event) {
        event.preventDefault();
        tinymce.triggerSave();
        this.submit();
    });

    $(document).on('click', '.delete-package', function(e) {
        e.preventDefault();
        let itemUrl = $(this).data('url');
        let row = $(this).closest('tr');
        Swal.fire({ title: 'Are you sure?', text: 'This package will be deleted!', icon: 'warning', showCancelButton: true, confirmButtonText: 'Yes, delete it' }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({ type: "DELETE", url: itemUrl,
                    success: function(res) { if (res.success) { row.remove(); Swal.fire('Deleted!', 'Package deleted successfully.', 'success'); } },
                    error: function() { toastr.error('Package not deleted!', 'Failed'); }
                });
            }
        });
    });

    $(document).on('click', '.add-package-page-details', function() {
        let btn = $(this);
        $('#package_about_title, #package_about_image_alt, #package_about_description, #package_duration_text, #package_best_season, #package_ideal_for').val('');
        $.ajax({
            type: "GET", dataType: "json", url: btn.data('eurl'),
            success: function(data) {
                $('#package-title').text(data.name);
                $('#package_about_title').val(data.about_title);
                $('#package_about_image_alt').val(data.about_image_alt);
                $('#package_about_description').val(data.about_description);
                $('#package_duration_text').val(data.duration_text);
                $('#package_best_season').val(data.best_season);
                $('#package_ideal_for').val(data.ideal_for);
                if (typeof window.setMediaPickerValue === 'function') {
                    window.setMediaPickerValue('car_package_about', data.about_image, data.about_image ? (s3BaseUrl + data.about_image) : null);
                }
                $('#packagePageSetting').attr('action', btn.data('url'));
                $('#packagePageSettingModal').modal('show');
            },
            error: function() { toastr.error('Failed to load package page details.'); }
        });
    });

    $(document).on('click', '.car-package-meta', function() {
        let btn = $(this);
        $.ajax({
            type: "GET", dataType: "json", url: btn.data('url'),
            success: function(data) {
                $('#car-package-title').text(data.name + ' - Meta Info');
                $('#car_package_meta_title').val(data.meta_title);
                $('#car_package_meta_description').val(data.meta_description);
                $('#car_package_meta_keywords').val(data.meta_keywords);
                $('#car_package_h1_heading').val(data.h1_heading);
                $('#car_package_meta_details').val(data.meta_details);
                $('#carPackageMeta').attr('action', btn.data('upurl'));
                $('#carPackageMetaModal').modal('show');
            },
            error: function() { toastr.error('Failed to load package meta.'); }
        });
    });

    $(document).on('click', '.package-car', function() {
        let btn = $(this);
        $.ajax({
            type: "GET", dataType: "json", url: btn.data('url'),
            success: function(res) {
                $('#packageCar').text(res.package.name);
                $('#package_id').val(res.package.id);
                let assignedCars = res.package.cars.map(c => c.id);
                let html = '<div class="row">';
                res.all_car.forEach(function(car) {
                    let checked = assignedCars.includes(car.id) ? 'checked' : '';
                    html += `<div class="col-md-4 col-sm-4 col-6"><div class="form-check">
                        <input type="checkbox" name="car_ids[]" class="form-check-input" value="${car.id}" ${checked}>
                        <label class="form-check-label">${car.title} (${car.category?.name ?? 'No Category'})</label>
                    </div></div>`;
                });
                html += '</div>';
                $('#package-car-list').html(html);
                $('#packageCarModal').modal('show');
            },
            error: function() { toastr.error('Failed to load package cars.'); }
        });
    });

    let packageFaqIndex = 0;
    $(document).on('click', '.package-faqs', function() {
        faqIndex = 0;
        let btn = $(this);
        $('#faqForm').attr('action', btn.data('url'));
        $.ajax({
            type: "GET", dataType: "json", url: "{{ route('admin.car.packages.faq') }}", data: { id: btn.data('id') },
            success: function(data) {
                $('#faqModal .modal-title').text(data.car_package.name);
                let faqs = data.car_package.faqs || [];
                let body = `
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Section Title<span class="required-text">*</span></label>
                            <input value="${data.car_package.faq_title ? data.car_package.faq_title : ''}" class="form-control" name="faq_title" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Section Sub Title</label>
                            <input value="${data.car_package.faq_sub_title ? data.car_package.faq_sub_title : ''}" class="form-control" name="faq_sub_title">
                        </div>
                    </div>
                </div>
                <table class="table" id="faqTable">
                    <thead><tr><th>Question</th><th>Answer</th><th><button type="button" class="btn btn-sm btn-outline-success" id="addFaqRow"><i class="fas fa-plus"></button></th></tr></thead>
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
                    body += `<tr class="b-none"><td><input type="text" name="faqs[${faqIndex}][question]" class="form-control" required/></td><td><textarea name="faqs[${faqIndex}][answer]" class="form-control"></textarea></td><td></td></tr>`;
                    faqIndex++;
                }
                body += `</tbody></table>`;
                $('#faqModal .modal-body').html(body);
                $('#faqModal').modal('show');
            },
            error: function() { toastr.error('Failed to load package FAQs.'); }
        });
    });

    let stopIndex = 0;
    $(document).on('click', '.package-stops', function() {
        stopIndex = 0;
        let btn = $(this);
        $('#stopsForm').attr('action', btn.data('url'));
        $.ajax({
            type: "GET", dataType: "json", url: "{{ route('admin.car.packages.stops') }}", data: { id: btn.data('id') },
            success: function(data) {
                $('#stops-title').text(data.car_package.name + ' - Tour Stops');
                let stops = data.car_package.stops || [];
                let body = `<p class="text-muted small">Stops are listed here in route order (e.g. Delhi, Agra, Jaipur). The "Attractions" box lists one place per line — these show as bullet points grouped under the stop name on the public page.</p>
                <table class="table" id="stopsTable">
                    <thead><tr><th>Stop Name</th><th>Description</th><th>Attractions (one per line)</th><th><button type="button" class="btn btn-sm btn-outline-success" id="addStopRow"><i class="fas fa-plus"></button></th></tr></thead>
                    <tbody>`;
                if (stops.length > 0) {
                    $.each(stops, function(index, s) {
                        let attractionsText = (s.attractions || []).join("\\n");
                        body += `<tr class="b-none">
                            <td><input type="text" name="stops[${stopIndex}][name]" value="${s.name}" class="form-control" required /></td>
                            <td><textarea name="stops[${stopIndex}][description]" class="form-control">${s.description ?? ''}</textarea></td>
                            <td><textarea name="stops[${stopIndex}][attractions]" class="form-control" rows="3">${attractionsText}</textarea></td>
                            <td><button type="button" class="btn btn-sm btn-outline-danger removeStopRow"><i class="fas fa-trash"></button></td>
                        </tr>`;
                        stopIndex++;
                    });
                } else {
                    body += `<tr class="b-none"><td><input type="text" name="stops[${stopIndex}][name]" class="form-control" required/></td><td><textarea name="stops[${stopIndex}][description]" class="form-control"></textarea></td><td><textarea name="stops[${stopIndex}][attractions]" class="form-control" rows="3"></textarea></td><td></td></tr>`;
                    stopIndex++;
                }
                body += `</tbody></table>`;
                $('#stopsForm .modal-body').html(body);
                $('#packageStopsModal').modal('show');
            },
            error: function() { toastr.error('Failed to load tour stops.'); }
        });
    });

    $(document).on('click', '#addStopRow', function() {
        let row = `<tr class="b-none">
            <td><input type="text" name="stops[${stopIndex}][name]" class="form-control" required /></td>
            <td><textarea name="stops[${stopIndex}][description]" class="form-control"></textarea></td>
            <td><textarea name="stops[${stopIndex}][attractions]" class="form-control" rows="3"></textarea></td>
            <td><button type="button" class="btn btn-sm btn-outline-danger removeStopRow"><i class="fas fa-trash"></button></td>
        </tr>`;
        stopIndex++;
        $('#stopsTable tbody').append(row);
    });

    $(document).on('click', '.removeStopRow', function() {
        $(this).closest('tr').remove();
    });

    function packageAmenityRow(label, description, icon) {
        let i = packageAmenityIndex;
        const pickerId = 'package_amenity_' + i;
        const pickerHtml = typeof window.mediaPickerFieldHtml === 'function'
            ? window.mediaPickerFieldHtml(`amenities[${i}][icon]`, pickerId, '', 'car-packages/amenities')
            : '';
        let row = `
            <tr>
                <td>${pickerHtml}</td>
                <td><input type="text" name="amenities[${i}][label]" class="form-control" value="${label || ''}" placeholder="e.g. Air Conditioning"></td>
                <td>
                    <input type="text" name="amenities[${i}][description]" class="form-control" value="${description || ''}" placeholder="e.g. Dual AC for a cool and comfortable ride.">
                </td>
                <td class="text-end" style="width:1%;"><button type="button" class="btn btn-sm btn-outline-danger rm-package-amenity-row"><i class="fas fa-trash"></i></button></td>
            </tr>
        `;
        packageAmenityIndex++;
        if (icon && typeof window.setMediaPickerValue === 'function') {
            setTimeout(function () { window.setMediaPickerValue(pickerId, icon, s3BaseUrl + icon); }, 0);
        }
        return row;
    }

    $(document).on('click', '.package-amenities', function() {
        let btn = $(this);
        btn.find('.spinner-border').removeClass('d-none');
        btn.find('.icon').addClass('d-none');
        $.ajax({
            type: "GET",
            dataType: "json",
            url: "{{ route('admin.car.packages.amenities') }}",
            data: { id: btn.data('id') },
            success: function(data) {
                $('#package-amenities-title').text(btn.data('name'));
                $('#packageAmenitiesForm').attr('action', btn.data('udurl'));

                packageAmenityIndex = 0;
                let amenities = data.car_package.amenities || [];
                let body = $('#packageAmenitiesTableBody').empty();
                if (amenities.length) {
                    amenities.forEach(a => body.append(packageAmenityRow(a.label, a.description, a.icon)));
                } else {
                    body.append(packageAmenityRow('', '', ''));
                }

                $('#packageAmenitiesModal').modal('show');
            },
            error: function() {
                toastr.error('Failed to load features & amenities.');
            },
            complete: function() {
                btn.find('.spinner-border').addClass('d-none');
                btn.find('.icon').removeClass('d-none');
            }
        });
    });

    $(document).on('click', '#addPackageAmenityRow', function(e) {
        e.preventDefault();
        $('#packageAmenitiesTableBody').append(packageAmenityRow('', '', ''));
    });

    $(document).on('click', '.rm-package-amenity-row', function() {
        $(this).closest('tr').remove();
    });

    // ═══════════════════════════════════════════════════════════════════
    // DESTINATION LIST
    // ═══════════════════════════════════════════════════════════════════
    document.getElementById('addDestinationForm').addEventListener('submit', function(event) {
        event.preventDefault();
        $('#add-destination-submit-btn').prop('disabled', true);
        let form = this;
        $.ajax({
            type: "GET",
            url: "{{ route('admin.car-destinations.slug.duplicate_check') }}",
            data: $(form).serialize(),
            success: function(res) {
                if (res.exists) {
                    toastr.error('This destination already exists');
                    $('#add-destination-submit-btn').prop('disabled', false);
                } else {
                    form.submit();
                }
            },
            error: function() { form.submit(); }
        });
    });

    $(document).on('change', '.destination-status', function() {
        $.ajax({
            type: "PUT", dataType: "json", url: $(this).data('url'), data: { 'status': $(this).prop('checked') ? 1 : 0 },
            success: function(data) { Swal.fire({ icon: 'success', title: 'Success', text: data.message }); },
            error: function() { Swal.fire({ icon: 'error', title: 'Error', text: 'An error occurred.' }); }
        });
    });

    $(document).on('change', '.destination-popular', function() {
        $.ajax({
            type: "PUT", dataType: "json", url: $(this).data('url'), data: { 'is_popular': $(this).prop('checked') ? 1 : 0 },
            success: function(data) { Swal.fire({ icon: 'success', title: 'Success', text: data.message }); },
            error: function() { Swal.fire({ icon: 'error', title: 'Error', text: 'An error occurred.' }); }
        });
    });

    $(document).on('click', '.destination-edit', function() {
        let btn = $(this);
        $.ajax({
            type: "GET", dataType: "json", url: btn.data('url'), data: { id: btn.data('id') },
            success: function(data) {
                $('#editDestinationForm').attr('action', btn.data('udurl'));
                $('#edit_destination_name').val(data.name);
                $('#edit_destination_state').val(data.state_id);
                $('#edit_destination_location').val(data.location_id);
                $('#editDestinationModal').modal('show');
            },
            error: function() { toastr.error('Failed to load destination.'); }
        });
    });

    document.getElementById('editDestinationForm').addEventListener('submit', function(event) {
        event.preventDefault();
        this.submit();
    });

    $(document).on('click', '.delete-destination', function(e) {
        e.preventDefault();
        let itemUrl = $(this).data('url');
        let row = $(this).closest('tr');
        Swal.fire({ title: 'Are you sure?', text: 'This destination will be deleted!', icon: 'warning', showCancelButton: true, confirmButtonText: 'Yes, delete it' }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({ type: "DELETE", url: itemUrl,
                    success: function(res) { if (res.success) { row.remove(); Swal.fire('Deleted!', 'Destination deleted successfully.', 'success'); } },
                    error: function() { toastr.error('Destination not deleted!', 'Failed'); }
                });
            }
        });
    });

    $(document).on('click', '.add-destination-page-details', function() {
        let btn = $(this);
        $('#destination_about_title, #destination_about_image_alt, #destination_about_description, #destination_distance_text, #destination_duration_text, #destination_ideal_for, #destination_best_season').val('');
        $.ajax({
            type: "GET", dataType: "json", url: btn.data('eurl'),
            success: function(data) {
                $('#destination-title').text(data.name);
                $('#destination_page_title').val(data.title);
                $('#destination_page_banner_alt').val(data.banner_image_alt);
                tinymce.get('destination_page_about') ? tinymce.get('destination_page_about').setContent(data.description || '') : $('#destination_page_about').val(data.description);
                if (typeof window.setMediaPickerValue === 'function') {
                    window.setMediaPickerValue('car_destination_banner', data.banner_image, data.banner_image ? (s3BaseUrl + data.banner_image) : null);
                    window.setMediaPickerValue('car_destination_about', data.about_image, data.about_image ? (s3BaseUrl + data.about_image) : null);
                }
                $('#destination_about_title').val(data.about_title);
                $('#destination_about_image_alt').val(data.about_image_alt);
                $('#destination_about_description').val(data.about_description);
                $('#destination_distance_text').val(data.distance_text);
                $('#destination_duration_text').val(data.duration_text);
                $('#destination_ideal_for').val(data.ideal_for);
                $('#destination_best_season').val(data.best_season);
                $('#destinationPageSetting').attr('action', btn.data('url'));
                $('#destinationPageSettingModal').modal('show');
            },
            error: function() { toastr.error('Failed to load destination page details.'); }
        });
    });

    $(document).on('click', '.car-destination-meta', function() {
        let btn = $(this);
        $.ajax({
            type: "GET", dataType: "json", url: btn.data('url'),
            success: function(data) {
                $('#car-destination-title').text(data.name + ' - Meta Info');
                $('#car_destination_meta_title').val(data.meta_title);
                $('#car_destination_meta_description').val(data.meta_description);
                $('#car_destination_meta_keywords').val(data.meta_keywords);
                $('#car_destination_h1_heading').val(data.h1_heading);
                $('#car_destination_meta_details').val(data.meta_details);
                $('#carDestinationMeta').attr('action', btn.data('upurl'));
                $('#carDestinationMetaModal').modal('show');
            },
            error: function() { toastr.error('Failed to load destination meta.'); }
        });
    });

    $(document).on('click', '.destination-car', function() {
        let btn = $(this);
        $.ajax({
            type: "GET", dataType: "json", url: btn.data('url'),
            success: function(res) {
                $('#destinationCar').text(res.destination.name);
                $('#destination_id').val(res.destination.id);
                let assignedCars = res.destination.cars.map(c => c.id);
                let html = '<div class="row">';
                res.all_car.forEach(function(car) {
                    let checked = assignedCars.includes(car.id) ? 'checked' : '';
                    html += `<div class="col-md-4 col-sm-4 col-6"><div class="form-check">
                        <input type="checkbox" name="car_ids[]" class="form-check-input" value="${car.id}" ${checked}>
                        <label class="form-check-label">${car.title} (${car.category?.name ?? 'No Category'})</label>
                    </div></div>`;
                });
                html += '</div>';
                $('#destination-car-list').html(html);
                $('#destinationCarModal').modal('show');
            },
            error: function() { toastr.error('Failed to load destination cars.'); }
        });
    });

    $(document).on('click', '.destination-faqs', function() {
        faqIndex = 0;
        let btn = $(this);
        $('#faqForm').attr('action', btn.data('url'));
        $.ajax({
            type: "GET", dataType: "json", url: "{{ route('admin.car.destinations.faq') }}", data: { id: btn.data('id') },
            success: function(data) {
                $('#faqModal .modal-title').text(data.car_destination.name);
                let faqs = data.car_destination.faqs || [];
                let body = `
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Section Title<span class="required-text">*</span></label>
                            <input value="${data.car_destination.faq_title ? data.car_destination.faq_title : ''}" class="form-control" name="faq_title" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Section Sub Title</label>
                            <input value="${data.car_destination.faq_sub_title ? data.car_destination.faq_sub_title : ''}" class="form-control" name="faq_sub_title">
                        </div>
                    </div>
                </div>
                <table class="table" id="faqTable">
                    <thead><tr><th>Question</th><th>Answer</th><th><button type="button" class="btn btn-sm btn-outline-success" id="addFaqRow"><i class="fas fa-plus"></button></th></tr></thead>
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
                    body += `<tr class="b-none"><td><input type="text" name="faqs[${faqIndex}][question]" class="form-control" required/></td><td><textarea name="faqs[${faqIndex}][answer]" class="form-control"></textarea></td><td></td></tr>`;
                    faqIndex++;
                }
                body += `</tbody></table>`;
                $('#faqModal .modal-body').html(body);
                $('#faqModal').modal('show');
            },
            error: function() { toastr.error('Failed to load destination FAQs.'); }
        });
    });

    $(document).on('click', '.destination-highlights', function() {
        highlightIndex = 0;
        let btn = $(this);
        $('#highlightsForm').attr('action', btn.data('url'));
        $.ajax({
            type: "GET", dataType: "json", url: "{{ route('admin.car.destinations.highlights') }}", data: { id: btn.data('id') },
            success: function(data) {
                $('#highlightsModal .modal-title').text(data.car_destination.name + ' Highlights');
                let highlights = data.car_destination.highlights || [];
                let body = `<table class="table" id="highlightsTable">
                    <thead><tr><th>Title</th><th>Description</th><th><button type="button" class="btn btn-sm btn-outline-success" id="addHighlightRow"><i class="fas fa-plus"></button></th></tr></thead>
                    <tbody>`;
                if (highlights.length > 0) {
                    $.each(highlights, function(index, h) {
                        body += `<tr class="b-none">
                            <td><input type="text" name="highlights[${highlightIndex}][title]" value="${h.title}" class="form-control" required /></td>
                            <td><textarea name="highlights[${highlightIndex}][description]" class="form-control">${h.description ?? ''}</textarea></td>
                            <td><button type="button" class="btn btn-sm btn-outline-danger removeHighlightRow"><i class="fas fa-trash"></button></td>
                        </tr>`;
                        highlightIndex++;
                    });
                } else {
                    body += `<tr class="b-none"><td><input type="text" name="highlights[${highlightIndex}][title]" class="form-control" required/></td><td><textarea name="highlights[${highlightIndex}][description]" class="form-control"></textarea></td><td></td></tr>`;
                    highlightIndex++;
                }
                body += `</tbody></table>`;
                $('#highlightsModal .modal-body').html(body);
                $('#highlightsModal').modal('show');
            },
            error: function() { toastr.error('Failed to load destination highlights.'); }
        });
    });

    $(document).on('click', '.city-highlights', function() {
        highlightIndex = 0;
        let btn = $(this);
        $('#highlightsForm').attr('action', btn.data('url'));
        $.ajax({
            type: "GET", dataType: "json", url: "{{ route('admin.car.city.highlights') }}", data: { id: btn.data('id') },
            success: function(data) {
                $('#highlightsModal .modal-title').text(data.car_city.location + ' Highlights');
                let highlights = data.car_city.highlights || [];
                let body = `<table class="table" id="highlightsTable">
                    <thead><tr><th>Title</th><th>Description</th><th><button type="button" class="btn btn-sm btn-outline-success" id="addHighlightRow"><i class="fas fa-plus"></button></th></tr></thead>
                    <tbody>`;
                if (highlights.length > 0) {
                    $.each(highlights, function(index, h) {
                        body += `<tr class="b-none">
                            <td><input type="text" name="highlights[${highlightIndex}][title]" value="${h.title}" class="form-control" required /></td>
                            <td><textarea name="highlights[${highlightIndex}][description]" class="form-control">${h.description ?? ''}</textarea></td>
                            <td><button type="button" class="btn btn-sm btn-outline-danger removeHighlightRow"><i class="fas fa-trash"></button></td>
                        </tr>`;
                        highlightIndex++;
                    });
                } else {
                    body += `<tr class="b-none"><td><input type="text" name="highlights[${highlightIndex}][title]" class="form-control" required/></td><td><textarea name="highlights[${highlightIndex}][description]" class="form-control"></textarea></td><td></td></tr>`;
                    highlightIndex++;
                }
                body += `</tbody></table>`;
                $('#highlightsModal .modal-body').html(body);
                $('#highlightsModal').modal('show');
            },
            error: function() { toastr.error('Failed to load city highlights.'); }
        });
    });
</script>
@endsection
