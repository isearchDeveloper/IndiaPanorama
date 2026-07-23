@section('title', 'Location Setting')
@extends('layouts.app')

@section('content')
<div class="container-fluid">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-0 fw-bold"><i class="fas fa-map-marked-alt me-2 text-primary"></i>Location Setting</h2>
        </div>
    </div>

    {{-- Tabs --}}
    <ul class="nav nav-tabs mb-4" id="locationTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ request('tab', 'regions') === 'regions' ? 'active' : '' }}"
                    id="tab-regions" data-bs-toggle="tab" data-bs-target="#pane-regions"
                    type="button" role="tab">
                <i class="fas fa-layer-group me-1"></i> Regions
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ request('tab') === 'states' ? 'active' : '' }}"
                    id="tab-states" data-bs-toggle="tab" data-bs-target="#pane-states"
                    type="button" role="tab">
                <i class="fas fa-map me-1"></i> States
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ request('tab') === 'cities' ? 'active' : '' }}"
                    id="tab-cities" data-bs-toggle="tab" data-bs-target="#pane-cities"
                    type="button" role="tab">
                <i class="fas fa-city me-1"></i> Cities / Locations
            </button>
        </li>
    </ul>

    <div class="tab-content" id="locationTabContent">

        {{-- ══════════════════════════════════════════════════════
             TAB 1 — REGIONS
        ══════════════════════════════════════════════════════ --}}
        <div class="tab-pane fade {{ request('tab', 'regions') === 'regions' ? 'show active' : '' }}"
             id="pane-regions" role="tabpanel">

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-layer-group me-2"></i>Regions</h5>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addRegionModal">
                        <i class="fas fa-plus me-1"></i> Add Region
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="90">Image</th>
                                    <th>Region Name</th>
                                    <th>Slug</th>
                                    <th>States</th>
                                    <th>Popular</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($regions as $i => $region)
                                <tr>
                                    <td><img src="{{ storage_link($region->details->banner_image ?? null) }}" width="70" style="object-fit:cover;aspect-ratio:1/1;border-radius:6px;"></td>
                                    <td class="fw-medium">{{ $region->name }}</td>
                                    <td><code>{{ $region->slug }}</code></td>
                                    <td>{{ $region->states_count ?? 0 }}</td>
                                    <td>
                                        <input type="checkbox" class="js-switch region-popular-toggle"
                                               data-id="{{ $region->id }}"
                                               data-url="{{ route('admin.regions.toggle-popular', $region->id) }}"
                                               {{ $region->is_popular ? 'checked' : '' }}>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-secondary btn-edit-region"
                                                data-id="{{ $region->id }}"
                                                data-fetch="{{ route('admin.regions.show', $region->id) }}"
                                                data-update="{{ route('admin.regions.update', $region->id) }}"
                                                title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-primary btn-region-page"
                                                data-id="{{ $region->id }}"
                                                data-fetch="{{ route('admin.regions.show', $region->id) }}"
                                                data-update="{{ route('admin.regions.update', $region->id) }}"
                                                title="Page Settings">
                                            <i class="fas fa-cog"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-info btn-region-meta"
                                                data-id="{{ $region->id }}"
                                                data-fetch="{{ route('admin.regions.show', $region->id) }}"
                                                data-update="{{ route('admin.regions.update', $region->id) }}"
                                                title="Meta">
                                            <i class="fas fa-globe"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-warning btn-region-faq"
                                                data-id="{{ $region->id }}"
                                                data-update="{{ route('admin.regions.faqUpdate', $region->id) }}"
                                                title="FAQs">
                                            <i class="fas fa-question-circle"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger btn-delete-region"
                                                data-id="{{ $region->id }}"
                                                data-url="{{ route('admin.regions.destroy', $region->id) }}"
                                                data-name="{{ $region->name }}"
                                                title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="text-center text-muted py-4">No regions found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($regions->lastPage() > 1)
                <div class="card-footer">
                    @include('admin.common.pagination', ['paginator' => $regions->appends(['tab' => 'regions'])])
                </div>
                @endif
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════
             TAB 2 — STATES
        ══════════════════════════════════════════════════════ --}}
        <div class="tab-pane fade {{ request('tab') === 'states' ? 'show active' : '' }}"
             id="pane-states" role="tabpanel">

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center gap-3 flex-wrap">
                    <h5 class="mb-0"><i class="fas fa-map me-2"></i>Indian States / UTs</h5>
                    <div class="d-flex gap-2 flex-grow-1 justify-content-end">
                        <input type="text" id="stateSearch" class="form-control form-control-sm" style="max-width:220px"
                               placeholder="Search state..." value="{{ request('search') }}">
                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addStateModal">
                            <i class="fas fa-plus me-1"></i> Add State
                        </button>
                    </div>
                </div>
                <div class="card-body p-0" id="stateListWrapper">
                    @include('admin.location-setting.state-list', ['states' => $states])
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════
             TAB 3 — CITIES
        ══════════════════════════════════════════════════════ --}}
        <div class="tab-pane fade {{ request('tab') === 'cities' ? 'show active' : '' }}"
             id="pane-cities" role="tabpanel">

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center gap-3 flex-wrap">
                    <h5 class="mb-0"><i class="fas fa-city me-2"></i>Cities / Locations</h5>
                    <div class="d-flex gap-2 flex-grow-1 justify-content-end">
                        <input type="text" id="citySearch" class="form-control form-control-sm" style="max-width:220px"
                               placeholder="Search city..." value="{{ $citySearch ?? '' }}">
                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addCityModal">
                            <i class="fas fa-plus me-1"></i> Add City
                        </button>
                    </div>
                </div>
                <div class="card-body p-0" id="cityListWrapper">
                    @include('admin.location-setting.city-table', ['locations' => $locations])
                </div>
            </div>
        </div>

    </div>{{-- end tab-content --}}
</div>
@endsection

@section('modal')

{{-- ═══ ADD REGION MODAL ═══ --}}
<div class="modal fade" id="addRegionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Add Region</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="addRegionForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Region Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="addRegionName" class="form-control" required>
                        <div class="text-danger small d-none" id="addRegionNameErr"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ═══ EDIT REGION MODAL ═══ --}}
<div class="modal fade" id="editRegionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Region</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editRegionForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" id="editRegionId">
                    <div class="mb-3">
                        <label class="form-label">Region Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="editRegionName" class="form-control" required>
                        <div class="text-danger small d-none" id="editRegionNameErr"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ═══ REGION PAGE SETTINGS MODAL ═══ --}}
<div class="modal fade" id="regionPageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="regionPageModalTitle">Region Page Settings</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="regionPageForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="page_setting" value="1">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Page Title</label>
                            <input type="text" name="title" id="rp_title" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Sub Title</label>
                            <input type="text" name="sub_title" id="rp_sub_title" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <x-media-picker name="banner_image" picker-id="region_banner" label="Banner Image (.webp only)" folder="region" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Banner Image Alt</label>
                            <input type="text" name="banner_image_alt" id="rp_banner_alt" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <x-media-picker name="home_image" picker-id="region_home" label="Featured Image (.webp only)" folder="region" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Featured Image Alt</label>
                            <input type="text" name="home_image_alt" id="rp_home_alt" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">About</label>
                            <textarea name="about" id="rp_about" class="form-control tinymce" rows="5"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ═══ REGION META MODAL ═══ --}}
<div class="modal fade" id="regionMetaModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="regionMetaModalTitle">Region SEO Meta</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="regionMetaForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="meta_setting" value="1">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Meta Title</label>
                            <input type="text" name="meta_title" id="rm_title" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Meta Description</label>
                            <input type="text" name="meta_description" id="rm_description" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Meta Keywords</label>
                            <input type="text" name="meta_keywords" id="rm_keywords" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">H1 Heading</label>
                            <input type="text" name="h1_heading" id="rm_h1" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Extra Meta Tags</label>
                            <textarea name="meta_details" id="rm_details" class="form-control" rows="4"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info text-white"><i class="fas fa-save me-1"></i>Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ═══ REGION FAQ MODAL ═══ --}}
<div class="modal fade" id="regionFaqModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="regionFaqTitle">Region FAQs</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="regionFaqForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body" id="regionFaqBody">
                    {{-- Rendered by JS --}}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning"><i class="fas fa-save me-1"></i>Save FAQs</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ═══ STATE PAGE SETTINGS MODAL ═══ --}}
<div class="modal fade" id="statePageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="statePageModalTitle">State Page Settings</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="statePageForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="page_setting" value="1">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Page Title</label>
                            <input type="text" name="title" id="sp_title" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Sub Title</label>
                            <input type="text" name="sub_title" id="sp_sub_title" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Banner Image Alt</label>
                            <input type="text" name="banner_image_alt" id="sp_banner_alt" class="form-control">
                        </div>
                        <div class="col-12">
                            <x-media-picker name="banner_image" picker-id="state_banner" label="Banner Image (.webp only)" folder="state" />
                        </div>
                        <div class="col-12">
                            <label class="form-label">About</label>
                            <textarea name="about" id="sp_about" class="form-control tinymce" rows="5"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ═══ STATE META MODAL ═══ --}}
<div class="modal fade" id="stateMetaModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="stateMetaModalTitle">State SEO Meta</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="stateMetaForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="meta_setting" value="1">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Meta Title</label>
                            <input type="text" name="meta_title" id="sm_title" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Meta Description</label>
                            <input type="text" name="meta_description" id="sm_description" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Meta Keywords</label>
                            <input type="text" name="meta_keywords" id="sm_keywords" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">H1 Heading</label>
                            <input type="text" name="h1_heading" id="sm_h1" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Extra Meta Tags</label>
                            <textarea name="meta_details" id="sm_details" class="form-control" rows="4"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info text-white"><i class="fas fa-save me-1"></i>Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ═══ STATE FAQ MODAL ═══ --}}
<div class="modal fade" id="stateFaqModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="stateFaqTitle">State FAQs</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="stateFaqForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body" id="stateFaqBody">
                    {{-- Rendered by JS --}}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning"><i class="fas fa-save me-1"></i>Save FAQs</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ═══ STATE BEST TIME TO VISIT MODAL ═══ --}}
<div class="modal fade" id="stateBestTimeModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="stateBestTimeTitle">Best Time to Visit</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="stateBestTimeForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body" id="stateBestTimeBody">
                    {{-- Rendered by JS --}}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-dark"><i class="fas fa-save me-1"></i>Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ═══ ADD STATE MODAL ═══ --}}
<div class="modal fade" id="addStateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Add State</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="addStateForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">State Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="addStateName" class="form-control" required
                               data-slug-check="states" data-slug-submit="#addStateSubmitBtn">
                        <div class="text-danger small d-none" id="addStateNameErr"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Region <span class="text-danger">*</span></label>
                        <select name="region_id" id="addStateRegion" class="form-select" required>
                            <option value="">— Select Region —</option>
                            @foreach($allRegions as $region)
                            <option value="{{ $region->id }}">{{ $region->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" id="addStateSubmitBtn"><i class="fas fa-save me-1"></i>Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ═══ EDIT STATE MODAL ═══ --}}
<div class="modal fade" id="editStateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit State</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editStateForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="editStateId">
                    <div class="mb-3">
                        <label class="form-label">State Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="editStateName" class="form-control" required>
                        <div class="text-danger small d-none" id="editStateNameErr"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Region</label>
                        <select name="region_id" id="editStateRegion" class="form-select">
                            <option value="">— Select Region —</option>
                            @foreach($allRegions as $region)
                            <option value="{{ $region->id }}">{{ $region->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning" id="editStateSubmitBtn"><i class="fas fa-save me-1"></i>Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ═══ ADD CITY MODAL ═══ --}}
<div class="modal fade" id="addCityModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Add City</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="addCityForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">City Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="addCityName" class="form-control" required
                               data-slug-check="locations" data-slug-submit="#addCitySubmitBtn">
                        <div class="text-danger small d-none" id="addCityNameErr"></div>
                    </div>
                    <input type="hidden" name="country_id" value="1">
                    <div class="mb-3">
                        <label class="form-label">State <span class="text-danger">*</span></label>
                        <select name="state_id" id="addCityState" class="form-select" required>
                            <option value="">— Select State —</option>
                            @foreach($allStates as $state)
                            <option value="{{ $state->id }}">{{ $state->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="addCitySubmitBtn"><i class="fas fa-save me-1"></i>Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ═══ EDIT CITY MODAL ═══ --}}
<div class="modal fade" id="editCityModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit City</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editCityForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="editCityId">
                    <div class="mb-3">
                        <label class="form-label">City Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="editCityName" class="form-control" required>
                        <div class="text-danger small d-none" id="editCityNameErr"></div>
                    </div>
                    <input type="hidden" name="country_id" value="1">
                    <div class="mb-3">
                        <label class="form-label">State <span class="text-danger">*</span></label>
                        <select name="state_id" id="editCityState" class="form-select" required>
                            <option value="">— Select State —</option>
                            @foreach($allStates as $state)
                            <option value="{{ $state->id }}">{{ $state->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning" id="updateCityBtn" data-url="">
                        <i class="fas fa-save me-1"></i>Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ═══ CITY PAGE SETTINGS MODAL ═══ --}}
<div class="modal fade" id="cityPageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="cityPageModalTitle">City Page Settings</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="cityPageForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="page_setting" value="1">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Page Title</label>
                            <input type="text" name="title" id="cp_title" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Sub Title</label>
                            <input type="text" name="sub_title" id="cp_sub_title" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Banner Alt Text</label>
                            <input type="text" name="banner_image_alt" id="cp_banner_alt" class="form-control">
                        </div>
                        <div class="col-12">
                            <x-media-picker name="banner_image" picker-id="city_banner" label="Banner Image (.webp only)" folder="location" />
                        </div>
                        <div class="col-12">
                            <label class="form-label">About</label>
                            <textarea name="about" id="cp_about" class="form-control tinymce" rows="5"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ═══ CITY META MODAL ═══ --}}
<div class="modal fade" id="cityMetaModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="cityMetaModalTitle">City SEO Meta</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="cityMetaForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="meta_setting" value="1">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Meta Title</label>
                            <input type="text" name="meta_title" id="cm_title" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Meta Description</label>
                            <input type="text" name="meta_description" id="cm_description" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Meta Keywords</label>
                            <input type="text" name="meta_keywords" id="cm_keywords" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">H1 Heading</label>
                            <input type="text" name="h1_heading" id="cm_h1" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Extra Meta Tags</label>
                            <textarea name="meta_details" id="cm_details" class="form-control" rows="4"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info text-white"><i class="fas fa-save me-1"></i>Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ═══ CITY FAQ MODAL ═══ --}}
<div class="modal fade" id="cityFaqModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="cityFaqTitle">City FAQs</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="cityFaqForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body" id="cityFaqBody"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning"><i class="fas fa-save me-1"></i>Save FAQs</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ═══ CITY BEST TIME TO VISIT MODAL ═══ --}}
<div class="modal fade" id="cityBestTimeModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="cityBestTimeTitle">Best Time to Visit</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="cityBestTimeForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body" id="cityBestTimeBody"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-dark"><i class="fas fa-save me-1"></i>Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
const STORAGE_URL = "{{ storage_base_url() }}";

// ── Modal helpers: safe for repeated open/close (avoids Bootstrap 5 duplicate-instance error) ──
function showModal(id) {
    bootstrap.Modal.getOrCreateInstance(document.getElementById(id)).show();
}
function hideModal(id) {
    bootstrap.Modal.getOrCreateInstance(document.getElementById(id)).hide();
}

// ── FAQ helpers ────────────────────────────────────────────────────────────
function faqHtml(faqs, faqTitle) {
    faqs = faqs || [];
    faqTitle = faqTitle || '';
    var idx = 0;
    var rows = faqs.length ? faqs.map(function(f) {
        var q = idx;
        var a = idx++;
        return '<tr>' +
            '<td><input type="text" name="faqs[' + q + '][question]" value="' + (f.question || '') + '" class="form-control" required></td>' +
            '<td><textarea name="faqs[' + a + '][answer]" class="form-control">' + (f.answer || '') + '</textarea></td>' +
            '<td><button type="button" class="btn btn-sm btn-outline-danger rm-faq"><i class="fas fa-trash"></i></button></td>' +
            '</tr>';
    }).join('') :
        '<tr>' +
        '<td><input type="text" name="faqs[0][question]" class="form-control" required></td>' +
        '<td><textarea name="faqs[0][answer]" class="form-control"></textarea></td>' +
        '<td></td></tr>';

    return '<div class="mb-3">' +
        '<label class="form-label">FAQ Section Title <span class="text-danger">*</span></label>' +
        '<input value="' + faqTitle + '" name="faq_title" class="form-control" required>' +
        '</div>' +
        '<table class="table" id="faqTable">' +
        '<thead><tr><th>Question</th><th>Answer</th>' +
        '<th><button type="button" class="btn btn-sm btn-outline-success" id="addFaqRow"><i class="fas fa-plus"></i></button></th>' +
        '</tr></thead><tbody>' + rows + '</tbody></table>';
}

function reindexFaq() {
    var i = 0;
    $('#faqTable tbody tr').each(function() {
        $(this).find('input[name*="[question]"]').attr('name', 'faqs[' + i + '][question]');
        $(this).find('textarea[name*="[answer]"]').attr('name', 'faqs[' + i + '][answer]');
        i++;
    });
}

function submitFaqAjax(formId, modalId) {
    reindexFaq();
    var form = document.getElementById(formId);
    var fd = new FormData(form);
    fd.append('_method', 'PUT');
    $.ajax({
        url: $(form).attr('action'),
        type: 'POST',
        data: fd,
        processData: false,
        contentType: false,
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    }).done(function() {
        toastr.success('FAQs saved!');
        hideModal(modalId);
    }).fail(function(xhr) {
        console.error('FAQ save failed:', xhr.status, xhr.responseText);
        toastr.error((xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to save FAQs.');
    });
}

$(document).on('click', '#addFaqRow', function() {
    var idx = $('#faqTable tbody tr').length;
    $('#faqTable tbody').append(
        '<tr>' +
        '<td><input type="text" name="faqs[' + idx + '][question]" class="form-control" required></td>' +
        '<td><textarea name="faqs[' + idx + '][answer]" class="form-control"></textarea></td>' +
        '<td><button type="button" class="btn btn-sm btn-outline-danger rm-faq"><i class="fas fa-trash"></i></button></td>' +
        '</tr>'
    );
});
$(document).on('click', '.rm-faq', function() { $(this).closest('tr').remove(); });

// ── Best Time to Visit helpers ──────────────────────────────────────────────
function bestTimeHtml(bestTimes, bestTimeTitle) {
    bestTimes = bestTimes || [];
    bestTimeTitle = bestTimeTitle || '';
    var rows = bestTimes.length ? bestTimes.map(function(b, i) {
        return '<tr>' +
            '<td><input type="text" name="best_times[' + i + '][month_range]" value="' + (b.month_range || '') + '" class="form-control" placeholder="e.g. September – November" required></td>' +
            '<td><textarea name="best_times[' + i + '][tagline]" class="form-control">' + (b.tagline || '') + '</textarea></td>' +
            '<td><button type="button" class="btn btn-sm btn-outline-danger rm-besttime"><i class="fas fa-trash"></i></button></td>' +
            '</tr>';
    }).join('') :
        '<tr>' +
        '<td><input type="text" name="best_times[0][month_range]" class="form-control" placeholder="e.g. September – November" required></td>' +
        '<td><textarea name="best_times[0][tagline]" class="form-control"></textarea></td>' +
        '<td></td></tr>';

    return '<div class="mb-3">' +
        '<label class="form-label">Section Title <span class="text-danger">*</span></label>' +
        '<input value="' + bestTimeTitle + '" name="best_time_title" class="form-control" placeholder="e.g. Best Time To Visit Kerala" required>' +
        '</div>' +
        '<table class="table" id="bestTimeTable">' +
        '<thead><tr><th>Months</th><th>Tagline</th>' +
        '<th><button type="button" class="btn btn-sm btn-outline-success" id="addBestTimeRow"><i class="fas fa-plus"></i></button></th>' +
        '</tr></thead><tbody>' + rows + '</tbody></table>';
}

function reindexBestTime() {
    var i = 0;
    $('#bestTimeTable tbody tr').each(function() {
        $(this).find('input[name*="[month_range]"]').attr('name', 'best_times[' + i + '][month_range]');
        $(this).find('textarea[name*="[tagline]"]').attr('name', 'best_times[' + i + '][tagline]');
        i++;
    });
}

function submitBestTimeAjax(formId, modalId) {
    reindexBestTime();
    var form = document.getElementById(formId);
    var fd = new FormData(form);
    fd.append('_method', 'PUT');
    $.ajax({
        url: $(form).attr('action'),
        type: 'POST',
        data: fd,
        processData: false,
        contentType: false,
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    }).done(function() {
        toastr.success('Best Time to Visit saved!');
        hideModal(modalId);
    }).fail(function(xhr) {
        console.error('Best Time save failed:', xhr.status, xhr.responseText);
        toastr.error((xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to save.');
    });
}

$(document).on('click', '#addBestTimeRow', function() {
    var idx = $('#bestTimeTable tbody tr').length;
    $('#bestTimeTable tbody').append(
        '<tr>' +
        '<td><input type="text" name="best_times[' + idx + '][month_range]" class="form-control" placeholder="e.g. September – November" required></td>' +
        '<td><textarea name="best_times[' + idx + '][tagline]" class="form-control"></textarea></td>' +
        '<td><button type="button" class="btn btn-sm btn-outline-danger rm-besttime"><i class="fas fa-trash"></i></button></td>' +
        '</tr>'
    );
});
$(document).on('click', '.rm-besttime', function() { $(this).closest('tr').remove(); });

// ── REGION CRUD ────────────────────────────────────────────────────────────
$('#addRegionForm').on('submit', function(e) {
    e.preventDefault();
    $('#addRegionNameErr').addClass('d-none');
    $.post("{{ route('admin.regions.store') }}", $(this).serialize())
        .done(function() { toastr.success('Region added!'); setTimeout(function() { location.reload(); }, 1000); })
        .fail(function(xhr) {
            var err = (xhr.responseJSON && xhr.responseJSON.errors && xhr.responseJSON.errors.name) ? xhr.responseJSON.errors.name[0] : 'Validation error.';
            $('#addRegionNameErr').text(err).removeClass('d-none');
        });
});

$(document).on('click', '.btn-edit-region', function() {
    var id  = $(this).data('id');
    var url = $(this).data('update');
    $.get($(this).data('fetch'))
        .done(function(d) {
            $('#editRegionId').val(id);
            $('#editRegionName').val(d.name);
            $('#editRegionForm').data('url', url);
            showModal('editRegionModal');
        })
        .fail(function(xhr) { console.error('Load region:', xhr); toastr.error('Failed to load region.'); });
});

$('#editRegionForm').on('submit', function(e) {
    e.preventDefault();
    $('#editRegionNameErr').addClass('d-none');
    var url = $(this).data('url');
    $.ajax({ url: url, type: 'PUT', data: $(this).serialize(),
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    }).done(function() { toastr.success('Region updated!'); setTimeout(function() { location.reload(); }, 1000); })
      .fail(function(xhr) {
        var err = (xhr.responseJSON && xhr.responseJSON.errors && xhr.responseJSON.errors.name) ? xhr.responseJSON.errors.name[0] : 'Error updating region.';
        $('#editRegionNameErr').text(err).removeClass('d-none');
    });
});

$(document).on('click', '.btn-delete-region', function() {
    var url  = $(this).data('url');
    var name = $(this).data('name');
    var row  = $(this).closest('tr');
    Swal.fire({ title: 'Delete Region?', text: '"' + name + '" will be removed.', icon: 'warning',
        showCancelButton: true, confirmButtonColor: '#dc3545', confirmButtonText: 'Yes, delete'
    }).then(function(r) {
        if (r.isConfirmed) {
            $.ajax({ url: url, type: 'DELETE',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            }).done(function() { row.remove(); toastr.success('Region deleted!'); })
              .fail(function() { toastr.error('Failed to delete region.'); });
        }
    });
});

$(document).on('change', '.region-popular-toggle', function() {
    var $cb = $(this);
    $.ajax({ url: $cb.data('url'), type: 'POST',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    }).done(function(r) { toastr.success(r.message); })
      .fail(function() { $cb.prop('checked', !$cb.prop('checked')); toastr.error('Failed to update popular status.'); });
});

// Region — Page Settings
$(document).on('click', '.btn-region-page', function() {
    var updateUrl = $(this).data('update');
    $.get($(this).data('fetch'))
        .done(function(d) {
            var aboutContent = (d.details && d.details.about) ? d.details.about : '';
            $('#regionPageModalTitle').text(d.name + ' — Page Settings');
            $('#rp_title').val((d.details && d.details.title) ? d.details.title : '');
            $('#rp_sub_title').val((d.details && d.details.sub_title) ? d.details.sub_title : '');
            $('#rp_banner_alt').val((d.details && d.details.banner_image_alt) ? d.details.banner_image_alt : '');
            if (typeof window.setMediaPickerValue === 'function') {
                window.setMediaPickerValue('region_banner', (d.details && d.details.banner_image) ? d.details.banner_image : '', (d.details && d.details.banner_image) ? (STORAGE_URL + d.details.banner_image) : null);
            }
            $('#rp_home_alt').val((d.details && d.details.home_image_alt) ? d.details.home_image_alt : '');
            if (typeof window.setMediaPickerValue === 'function') {
                window.setMediaPickerValue('region_home', (d.details && d.details.home_image) ? d.details.home_image : '', (d.details && d.details.home_image) ? (STORAGE_URL + d.details.home_image) : null);
            }
            // Set about textarea value (fallback when TinyMCE not yet initialised)
            $('#rp_about').val(aboutContent);
            // Set TinyMCE content once the modal is fully visible
            $('#regionPageModal').one('shown.bs.modal', function() {
                if (typeof tinymce !== 'undefined' && tinymce.get('rp_about')) {
                    tinymce.get('rp_about').setContent(aboutContent);
                }
            });
            $('#regionPageForm').attr('action', updateUrl);
            showModal('regionPageModal');
        })
        .fail(function(xhr) { console.error('Load region page:', xhr); toastr.error('Failed to load page settings.'); });
});

$('#regionPageForm').on('submit', function(e) {
    e.preventDefault();
    if (typeof tinymce !== 'undefined') tinymce.triggerSave();
    var fd = new FormData(this);
    fd.append('_method', 'PUT');
    $.ajax({ url: $(this).attr('action'), type: 'POST', data: fd, processData: false, contentType: false,
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    }).done(function() { toastr.success('Page settings saved!'); hideModal('regionPageModal'); })
      .fail(function(xhr) { console.error('Region page save:', xhr); toastr.error((xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to save.'); });
});

// Region — SEO Meta
$(document).on('click', '.btn-region-meta', function() {
    var updateUrl = $(this).data('update');
    $.get($(this).data('fetch'))
        .done(function(d) {
            $('#regionMetaModalTitle').text(d.name + ' — SEO Meta');
            $('#rm_title').val((d.meta && d.meta.meta_title) ? d.meta.meta_title : '');
            $('#rm_description').val((d.meta && d.meta.meta_description) ? d.meta.meta_description : '');
            $('#rm_keywords').val((d.meta && d.meta.meta_keywords) ? d.meta.meta_keywords : '');
            $('#rm_h1').val((d.meta && d.meta.h1_heading) ? d.meta.h1_heading : '');
            $('#rm_details').val((d.meta && d.meta.meta_details) ? d.meta.meta_details : '');
            $('#regionMetaForm').attr('action', updateUrl);
            showModal('regionMetaModal');
        })
        .fail(function(xhr) { console.error('Load region meta:', xhr); toastr.error('Failed to load meta settings.'); });
});

$('#regionMetaForm').on('submit', function(e) {
    e.preventDefault();
    $.ajax({ url: $(this).attr('action'), type: 'PUT', data: $(this).serialize(),
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    }).done(function() { toastr.success('Meta saved!'); hideModal('regionMetaModal'); })
      .fail(function(xhr) { console.error('Region meta save:', xhr); toastr.error((xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to save meta.'); });
});

// Region — FAQs
$(document).on('click', '.btn-region-faq', function() {
    var id        = $(this).data('id');
    var updateUrl = $(this).data('update');
    $('#regionFaqForm').attr('action', updateUrl);
    $.get("{{ route('admin.regions.index') }}", { id: id, faqs: true })
        .done(function(res) {
            $('#regionFaqTitle').text(res.region.name + ' FAQs');
            $('#regionFaqBody').html(faqHtml(res.region.faqs, res.region.faq_title));
            showModal('regionFaqModal');
        })
        .fail(function(xhr) { console.error('Load region FAQs:', xhr); toastr.error('Failed to load FAQs.'); });
});

$('#regionFaqForm').on('submit', function(e) {
    e.preventDefault();
    submitFaqAjax('regionFaqForm', 'regionFaqModal');
});

// ── STATE CRUD ─────────────────────────────────────────────────────────────
var statesApiBase = "{{ route('admin.location-setting.states.index') }}";

$('#addStateForm').on('submit', function(e) {
    e.preventDefault();
    $('#addStateNameErr').addClass('d-none');
    $.post(statesApiBase, $(this).serialize())
        .done(function() { toastr.success('State added!'); setTimeout(function() { location.reload(); }, 1000); })
        .fail(function(xhr) {
            var err = (xhr.responseJSON && xhr.responseJSON.errors && xhr.responseJSON.errors.name) ? xhr.responseJSON.errors.name[0] : 'Error adding state.';
            $('#addStateNameErr').text(err).removeClass('d-none');
        });
});

$(document).on('click', '.btn-edit-state', function() {
    var id = $(this).data('id');
    $.get(statesApiBase + '/' + id)
        .done(function(d) {
            $('#editStateId').val(d.id);
            $('#editStateName').val(d.name).attr('data-slug-exclude', d.id || 0);
            $('#editStateRegion').val(d.region_id || '');
            $('#editStateForm').data('url', statesApiBase + '/' + d.id);
            showModal('editStateModal');
        })
        .fail(function(xhr) { console.error('Load state:', xhr); toastr.error('Failed to load state data.'); });
});

$('#editStateForm').on('submit', function(e) {
    e.preventDefault();
    $('#editStateNameErr').addClass('d-none');
    var url = $(this).data('url');
    $.ajax({ url: url, type: 'PUT', data: $(this).serialize(),
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    }).done(function() { toastr.success('State updated!'); setTimeout(function() { location.reload(); }, 1000); })
      .fail(function(xhr) {
        var err = (xhr.responseJSON && xhr.responseJSON.errors && xhr.responseJSON.errors.name) ? xhr.responseJSON.errors.name[0] : 'Error updating state.';
        $('#editStateNameErr').text(err).removeClass('d-none');
    });
});

$(document).on('change', '.state-status-toggle', function() {
    var id  = $(this).data('id');
    var url = statesApiBase + '/' + id + '/toggle-status';
    $.ajax({ url: url, type: 'POST',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    }).done(function(r) { toastr.success(r.message); })
      .fail(function() { toastr.error('Failed to update status.'); });
});

$(document).on('click', '.btn-delete-state', function() {
    var id   = $(this).data('id');
    var name = $(this).data('name');
    var row  = $(this).closest('tr');
    Swal.fire({ title: 'Delete State?', text: '"' + name + '" will be removed.', icon: 'warning',
        showCancelButton: true, confirmButtonColor: '#dc3545', confirmButtonText: 'Yes, delete'
    }).then(function(r) {
        if (r.isConfirmed) {
            $.ajax({ url: statesApiBase + '/' + id, type: 'DELETE',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            }).done(function() { row.remove(); toastr.success('State deleted!'); })
              .fail(function() { toastr.error('Failed to delete state.'); });
        }
    });
});

// State search (live)
var stateTimer;
$('#stateSearch').on('input', function() {
    clearTimeout(stateTimer);
    var q = $(this).val();
    stateTimer = setTimeout(function() {
        showAjaxLoader($('#stateListWrapper'));
        $.get(statesApiBase, { search: q, ajax: 1 })
            .done(function(res) { $('#stateListWrapper').html(res.html); initSwitchery(); })
            .fail(function() { hideAjaxLoader($('#stateListWrapper')); toastr.error('State search failed.'); });
    }, 300);
});

// ── CITY CRUD ──────────────────────────────────────────────────────────────
$('#addCityForm').on('submit', function(e) {
    e.preventDefault();
    $('#addCityNameErr').addClass('d-none');
    $.post("{{ route('admin.location-setting.cities.store') }}", $(this).serialize())
        .done(function() { toastr.success('City added!'); setTimeout(function() { location.reload(); }, 1000); })
        .fail(function(xhr) {
            var err = (xhr.responseJSON && xhr.responseJSON.errors && xhr.responseJSON.errors.name) ? xhr.responseJSON.errors.name[0] : 'Error adding city.';
            $('#addCityNameErr').text(err).removeClass('d-none');
        });
});

$(document).on('click', '.btn-edit-city', function() {
    var upUrl = $(this).data('upurl');
    $.get($(this).data('url'))
        .done(function(d) {
            $('#editCityId').val(d.id);
            $('#editCityName').val(d.name).attr('data-slug-exclude', d.id || 0);
            $('#editCityState').val(d.state_id || '');
            $('#updateCityBtn').data('url', upUrl);
            showModal('editCityModal');
        })
        .fail(function(xhr) { console.error('Load city:', xhr); toastr.error('Failed to load city data.'); });
});

$('#editCityForm').on('submit', function(e) {
    e.preventDefault();
    $('#editCityNameErr').addClass('d-none');
    var url = $('#updateCityBtn').data('url');
    $.ajax({ url: url, type: 'PUT', data: $(this).serialize(),
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    }).done(function() { toastr.success('City updated!'); setTimeout(function() { location.reload(); }, 1000); })
      .fail(function(xhr) {
        var err = (xhr.responseJSON && xhr.responseJSON.errors && xhr.responseJSON.errors.name) ? xhr.responseJSON.errors.name[0] : 'Error updating city.';
        $('#editCityNameErr').text(err).removeClass('d-none');
    });
});

$(document).on('click', '.btn-delete-city', function() {
    var url  = $(this).data('url');
    var name = $(this).data('name');
    var row  = $(this).closest('tr');
    Swal.fire({ title: 'Delete City?', text: '"' + name + '" will be removed.', icon: 'warning',
        showCancelButton: true, confirmButtonColor: '#dc3545', confirmButtonText: 'Yes, delete'
    }).then(function(r) {
        if (r.isConfirmed) {
            $.ajax({ url: url, type: 'DELETE',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            }).done(function() { row.remove(); toastr.success('City deleted!'); })
              .fail(function() { toastr.error('Failed to delete city.'); });
        }
    });
});

$(document).on('change', '.city-status-toggle', function() {
    var url = $(this).data('url');
    $.ajax({ url: url, type: 'POST',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    }).done(function(r) { toastr.success(r.message); })
      .fail(function() { toastr.error('Failed to update status.'); });
});

// City — Page Settings
$(document).on('click', '.btn-city-page', function() {
    var updateUrl = $(this).data('upurl');
    $.get($(this).data('url'))
        .done(function(d) {
            var aboutContent = (d.details && d.details.about) ? d.details.about : '';
            $('#cityPageModalTitle').text(d.name + ' — Page Settings');
            $('#cp_title').val((d.details && d.details.title) ? d.details.title : '');
            $('#cp_sub_title').val((d.details && d.details.sub_title) ? d.details.sub_title : '');
            $('#cp_banner_alt').val((d.details && d.details.banner_image_alt) ? d.details.banner_image_alt : '');
            if (typeof window.setMediaPickerValue === 'function') {
                window.setMediaPickerValue('city_banner', (d.details && d.details.banner_image) ? d.details.banner_image : '', (d.details && d.details.banner_image) ? (STORAGE_URL + d.details.banner_image) : null);
            }
            $('#cp_about').val(aboutContent);
            $('#cityPageModal').one('shown.bs.modal', function() {
                if (typeof tinymce !== 'undefined' && tinymce.get('cp_about')) {
                    tinymce.get('cp_about').setContent(aboutContent);
                }
            });
            $('#cityPageForm').attr('action', updateUrl);
            showModal('cityPageModal');
        })
        .fail(function(xhr) { console.error('Load city page:', xhr); toastr.error('Failed to load page settings.'); });
});

$('#cityPageForm').on('submit', function(e) {
    e.preventDefault();
    if (typeof tinymce !== 'undefined') tinymce.triggerSave();
    var fd = new FormData(this);
    fd.append('_method', 'PUT');
    $.ajax({ url: $(this).attr('action'), type: 'POST', data: fd, processData: false, contentType: false,
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    }).done(function() { toastr.success('Page settings saved!'); hideModal('cityPageModal'); })
      .fail(function(xhr) { console.error('City page save:', xhr); toastr.error((xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to save.'); });
});

// City — SEO Meta
$(document).on('click', '.btn-city-meta', function() {
    var updateUrl = $(this).data('upurl');
    $.get($(this).data('url'))
        .done(function(d) {
            $('#cityMetaModalTitle').text(d.name + ' — SEO Meta');
            $('#cm_title').val((d.meta && d.meta.meta_title) ? d.meta.meta_title : '');
            $('#cm_description').val((d.meta && d.meta.meta_description) ? d.meta.meta_description : '');
            $('#cm_keywords').val((d.meta && d.meta.meta_keywords) ? d.meta.meta_keywords : '');
            $('#cm_h1').val((d.meta && d.meta.h1_heading) ? d.meta.h1_heading : '');
            $('#cm_details').val((d.meta && d.meta.meta_details) ? d.meta.meta_details : '');
            $('#cityMetaForm').attr('action', updateUrl);
            showModal('cityMetaModal');
        })
        .fail(function(xhr) { console.error('Load city meta:', xhr); toastr.error('Failed to load meta settings.'); });
});

$('#cityMetaForm').on('submit', function(e) {
    e.preventDefault();
    $.ajax({ url: $(this).attr('action'), type: 'PUT', data: $(this).serialize(),
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    }).done(function() { toastr.success('Meta saved!'); hideModal('cityMetaModal'); })
      .fail(function(xhr) { console.error('City meta save:', xhr); toastr.error((xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to save meta.'); });
});

// City — FAQs
$(document).on('click', '.btn-city-faq', function() {
    var id        = $(this).data('id');
    var updateUrl = $(this).data('faqurl');
    $('#cityFaqForm').attr('action', updateUrl);
    $.get("{{ route('admin.location-setting.index') }}", { faqs: true, id: id })
        .done(function(res) {
            $('#cityFaqTitle').text(res.location.name + ' FAQs');
            $('#cityFaqBody').html(faqHtml(res.location.faqs, res.location.faq_title));
            showModal('cityFaqModal');
        })
        .fail(function(xhr) { console.error('Load city FAQs:', xhr); toastr.error('Failed to load FAQs.'); });
});

$('#cityFaqForm').on('submit', function(e) {
    e.preventDefault();
    submitFaqAjax('cityFaqForm', 'cityFaqModal');
});

// City — Best Time to Visit
$(document).on('click', '.btn-city-besttime', function() {
    var id        = $(this).data('id');
    var updateUrl = $(this).data('besttimeurl');
    $('#cityBestTimeForm').attr('action', updateUrl);
    $.get("{{ route('admin.location-setting.index') }}", { best_times: true, id: id })
        .done(function(res) {
            $('#cityBestTimeTitle').text(res.location.name + ' — Best Time to Visit');
            $('#cityBestTimeBody').html(bestTimeHtml(res.location.best_times, res.location.best_time_title));
            showModal('cityBestTimeModal');
        })
        .fail(function(xhr) { console.error('Load city best times:', xhr); toastr.error('Failed to load Best Time to Visit.'); });
});

$('#cityBestTimeForm').on('submit', function(e) {
    e.preventDefault();
    submitBestTimeAjax('cityBestTimeForm', 'cityBestTimeModal');
});

// City search (live)
var cityTimer;
$('#citySearch').on('input', function() {
    clearTimeout(cityTimer);
    var q = $(this).val();
    cityTimer = setTimeout(function() {
        showAjaxLoader($('#cityListWrapper'));
        $.get("{{ route('admin.location-setting.index') }}", { city: q, tab: 'cities', ajax: 1 })
            .done(function(res) { $('#cityListWrapper').html(res.html); initSwitchery(); })
            .fail(function() { hideAjaxLoader($('#cityListWrapper')); toastr.error('City search failed.'); });
    }, 300);
});

// ── STATE — Page Settings ──────────────────────────────────────────────────
$(document).on('click', '.btn-state-page', function() {
    var updateUrl = $(this).data('update');
    $.get($(this).data('fetch'))
        .done(function(d) {
            var aboutContent = (d.details && d.details.about) ? d.details.about : '';
            $('#statePageModalTitle').text(d.name + ' — Page Settings');
            $('#sp_title').val((d.details && d.details.title) ? d.details.title : '');
            $('#sp_sub_title').val((d.details && d.details.sub_title) ? d.details.sub_title : '');
            $('#sp_banner_alt').val((d.details && d.details.banner_image_alt) ? d.details.banner_image_alt : '');
            if (typeof window.setMediaPickerValue === 'function') {
                window.setMediaPickerValue('state_banner', (d.details && d.details.banner_image) ? d.details.banner_image : '', (d.details && d.details.banner_image) ? (STORAGE_URL + d.details.banner_image) : null);
            }
            $('#sp_about').val(aboutContent);
            $('#statePageModal').one('shown.bs.modal', function() {
                if (typeof tinymce !== 'undefined' && tinymce.get('sp_about')) {
                    tinymce.get('sp_about').setContent(aboutContent);
                }
            });
            $('#statePageForm').attr('action', updateUrl);
            showModal('statePageModal');
        })
        .fail(function(xhr) { console.error('Load state page:', xhr); toastr.error('Failed to load page settings.'); });
});

$('#statePageForm').on('submit', function(e) {
    e.preventDefault();
    if (typeof tinymce !== 'undefined') tinymce.triggerSave();
    var fd = new FormData(this);
    fd.append('_method', 'PUT');
    $.ajax({ url: $(this).attr('action'), type: 'POST', data: fd, processData: false, contentType: false,
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    }).done(function() { toastr.success('Page settings saved!'); hideModal('statePageModal'); })
      .fail(function(xhr) { console.error('State page save:', xhr); toastr.error((xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to save.'); });
});

// ── STATE — SEO Meta ───────────────────────────────────────────────────────
$(document).on('click', '.btn-state-meta', function() {
    var updateUrl = $(this).data('update');
    $.get($(this).data('fetch'))
        .done(function(d) {
            $('#stateMetaModalTitle').text(d.name + ' — SEO Meta');
            $('#sm_title').val((d.meta && d.meta.meta_title) ? d.meta.meta_title : '');
            $('#sm_description').val((d.meta && d.meta.meta_description) ? d.meta.meta_description : '');
            $('#sm_keywords').val((d.meta && d.meta.meta_keywords) ? d.meta.meta_keywords : '');
            $('#sm_h1').val((d.meta && d.meta.h1_heading) ? d.meta.h1_heading : '');
            $('#sm_details').val((d.meta && d.meta.meta_details) ? d.meta.meta_details : '');
            $('#stateMetaForm').attr('action', updateUrl);
            showModal('stateMetaModal');
        })
        .fail(function(xhr) { console.error('Load state meta:', xhr); toastr.error('Failed to load meta settings.'); });
});

$('#stateMetaForm').on('submit', function(e) {
    e.preventDefault();
    $.ajax({ url: $(this).attr('action'), type: 'PUT', data: $(this).serialize(),
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    }).done(function() { toastr.success('Meta saved!'); hideModal('stateMetaModal'); })
      .fail(function(xhr) { console.error('State meta save:', xhr); toastr.error((xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to save meta.'); });
});

// ── STATE — FAQs ───────────────────────────────────────────────────────────
$(document).on('click', '.btn-state-faq', function() {
    var updateUrl = $(this).data('update');
    $('#stateFaqForm').attr('action', updateUrl);
    $.get($(this).data('fetch'))
        .done(function(d) {
            $('#stateFaqTitle').text(d.name + ' FAQs');
            $('#stateFaqBody').html(faqHtml(d.faqs, d.faq_title));
            showModal('stateFaqModal');
        })
        .fail(function(xhr) { console.error('Load state FAQs:', xhr); toastr.error('Failed to load FAQs.'); });
});

$('#stateFaqForm').on('submit', function(e) {
    e.preventDefault();
    submitFaqAjax('stateFaqForm', 'stateFaqModal');
});

// ── STATE — Best Time to Visit ─────────────────────────────────────────────
$(document).on('click', '.btn-state-besttime', function() {
    var updateUrl = $(this).data('update');
    $('#stateBestTimeForm').attr('action', updateUrl);
    $.get($(this).data('fetch'))
        .done(function(d) {
            $('#stateBestTimeTitle').text(d.name + ' — Best Time to Visit');
            $('#stateBestTimeBody').html(bestTimeHtml(d.best_times, d.best_time_title));
            showModal('stateBestTimeModal');
        })
        .fail(function(xhr) { console.error('Load state best times:', xhr); toastr.error('Failed to load Best Time to Visit.'); });
});

$('#stateBestTimeForm').on('submit', function(e) {
    e.preventDefault();
    submitBestTimeAjax('stateBestTimeForm', 'stateBestTimeModal');
});

// ── Active tab memory ──────────────────────────────────────────────────────
var urlTab = new URLSearchParams(location.search).get('tab') || 'regions';
var tabEl  = document.getElementById('tab-' + urlTab);
if (tabEl) bootstrap.Tab.getOrCreateInstance(tabEl).show();

document.querySelectorAll('#locationTabs button').forEach(function(btn) {
    btn.addEventListener('shown.bs.tab', function(e) {
        history.replaceState(null, '', '?tab=' + e.target.id.replace('tab-', ''));
    });
});

var _flash = "{{ addslashes(session('success') ?? '') }}";
if (_flash) toastr.success(_flash, 'Success');
</script>
@endsection
