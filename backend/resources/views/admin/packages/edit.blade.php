@extends('layouts.app')
@section('title', 'Edit Package — ' . $package->title)

@section('content')

<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h2"><i class="fas fa-edit me-2"></i>Edit Package</h1>
                <a href="{{ route('admin.packages.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Packages
                </a>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" id="global-error-alert">
            <strong>Please fix the following errors:</strong>
            <ul class="mb-0 mt-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form id="packageForm" method="POST"
          action="{{ route('admin.packages.update', $package->id) }}"
          enctype="multipart/form-data">
        @csrf
        @method('PUT')
        {{-- save_type is set dynamically by JS before submit --}}
        <input type="hidden" name="save_type" id="save_type_hidden" value="publish">

        {{-- ======================================================= --}}
        {{-- PACKAGE INFORMATION                                      --}}
        {{-- ======================================================= --}}
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Package Information</h5>
            </div>
            <div class="card-body">

                <div class="row">
                    {{-- Title --}}
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Package Name <span class="required-text">*</span></label>
                            <input type="text"
                                   class="form-control @error('title') is-invalid @enderror"
                                   id="title" name="title"
                                   value="{{ old('title', $package->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Slug --}}
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Slug <small class="text-muted">(fixed after creation)</small></label>
                            <input type="text"
                                   class="form-control bg-light @error('slug') is-invalid @enderror"
                                   id="slug" name="slug"
                                   value="{{ old('slug', $package->slug) }}" readonly>
                            @error('slug')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div class="invalid-feedback d-none" id="slug-error"></div>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="type" value="1">
                <input type="hidden" name="parent" value="">
                <input type="hidden" id="package_mode" name="package_mode" value="{{ old('package_mode', $package->package_mode) }}">

                {{-- Line 1: Duration & Festival --}}
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="duration_days" class="form-label">Duration (Days) <span class="required-text">*</span></label>
                            <input type="number"
                                   class="form-control @error('duration_days') is-invalid @enderror"
                                   id="duration_days" name="duration_days"
                                   min="1" max="60"
                                   value="{{ old('duration_days', $package->details?->duration_days ?? '') }}">
                            @error('duration_days')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="duration_nights" class="form-label">Duration (Nights) <span class="required-text">*</span></label>
                            <input type="number"
                                   class="form-control @error('duration_nights') is-invalid @enderror"
                                   id="duration_nights" name="duration_nights"
                                   min="0" max="60"
                                   value="{{ old('duration_nights', $package->details?->duration_nights ?? 0) }}">
                            @error('duration_nights')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="festival_id" class="form-label">Select Specific Festival</label>
                            <select name="festival_id" id="festival_id" class="form-select">
                                <option value="">— None —</option>
                                @foreach($festivals as $festival)
                                <option value="{{ $festival->id }}" {{ old('festival_id', $package->festival_id) == $festival->id ? 'selected' : '' }}>{{ $festival->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Line 2: Flags --}}
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label d-block">Top Trending?</label>
                            <input type="checkbox" name="is_top_trending" value="1"
                                   {{ old('is_top_trending', $package->is_top_trending) ? 'checked' : '' }}>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label d-block">Special Package?</label>
                            <input type="checkbox" name="is_special_package" value="1"
                                   {{ old('is_special_package', $package->is_special_package) ? 'checked' : '' }}>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label d-block">Is Festival Tour Packages?</label>
                            <input type="checkbox" name="is_festival_package" value="1"
                                   {{ old('is_festival_package', $package->is_festival_package) ? 'checked' : '' }}>
                        </div>
                    </div>
                </div>

                {{-- Price: only shown for Group Tour --}}
                <div class="row" id="price_wrapper"
                     style="{{ old('package_mode', $package->package_mode) === 'group_tour' ? '' : 'display:none;' }}">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="price" class="form-label">Price <span class="required-text">*</span></label>
                            <input type="number"
                                   class="form-control @error('price') is-invalid @enderror"
                                   id="price" name="price"
                                   value="{{ old('price', $package->price) }}"
                                   min="0" step="0.01"
                                   placeholder="Enter price (₹)">
                            @error('price')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Tour Highlights (Short Description) --}}
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="tour_highlights" class="form-label">Short Description</label>
                            <textarea
                                   class="form-control tinymce @error('tour_highlights') is-invalid @enderror"
                                   id="tour_highlights" name="tour_highlights" rows="3">{{ old('tour_highlights', $package->details?->tour_highlights ?? '') }}</textarea>
                            @error('tour_highlights')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>


            </div>
        </div>

        {{-- ======================================================= --}}
        {{-- GALLERY                                                  --}}
        {{-- ======================================================= --}}
        <div class="card mt-3">
            <div class="card-header bg-warning text-white">
                <h5 class="mb-0">Package Gallery</h5>
            </div>
            <div class="card-body">

                {{-- Primary Image --}}
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Primary Image</h6>
                    </div>
                    <div class="card-body">
                        {{-- Row 1: image + alt --}}
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <x-media-picker name="primary_image" label="" folder="packages" :value="$package->primary_image" />
                                @error('primary_image')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="primary_image_alt"
                                       value="{{ old('primary_image_alt', $package->primary_image_alt) }}"
                                       placeholder="Primary Image Alt Text">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Gallery Images --}}
                <div class="card mt-3">
                    <div class="card-header"><h6 class="mb-0">Gallery Images</h6></div>
                    <div class="card-body">
                        <x-media-gallery-picker name="gallery_images" picker-id="pkg_gallery" label="" folder="packages" />
                    </div>
                </div>

            </div>
        </div>

        {{-- ======================================================= --}}
        {{-- LOCATION                                                 --}}
        {{-- ======================================================= --}}
        <div class="card mt-3">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Location</h5>
            </div>
            <div class="card-body">

                <div class="row">
                    {{-- Source Location --}}
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header"><h6 class="mb-0">Source Location (Departure From)</h6></div>
                            <div class="card-body">
                                <input type="hidden" name="source_country_id" value="1">
                                <div class="mb-3">
                                    <label class="form-label">State <span class="required-text">*</span></label>
                                    <select class="form-select form-select-sm" id="source_state_id" required onchange="reloadCityBox('source'); this.classList.remove('is-invalid')">
                                        <option value="">-- Select State --</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Departure City <span class="required-text">*</span></label>
                                    <div class="bg-white">
                                        <input type="text" class="form-control form-control-sm mb-2"
                                               placeholder="Search city…" onkeyup="filterCheckboxes(this,'source_city_box')">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox"
                                                   onclick="toggleAllCheckboxes(this,'source_city_box')">
                                            <label class="form-check-label fw-semibold">Select All</label>
                                        </div>
                                        <div id="source_city_box" style="max-height:180px; overflow-y:auto;">
                                            <p class="text-muted small mb-0">Loading…</p>
                                        </div>
                                    </div>
                                    @error('source_location_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Destination Location --}}
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header"><h6 class="mb-0">Destination Location</h6></div>
                            <div class="card-body">
                                <input type="hidden" name="country_id" value="1">
                                <div class="mb-3">
                                    <label class="form-label">State <span class="required-text">*</span></label>
                                    <select class="form-select form-select-sm" id="destination_state_id" required onchange="reloadCityBox('destination'); this.classList.remove('is-invalid')">
                                        <option value="">-- Select State --</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Destination City <span class="required-text">*</span></label>
                                    <div class="bg-white">
                                        <input type="text" class="form-control form-control-sm mb-2"
                                               placeholder="Search city…" onkeyup="filterCheckboxes(this,'location_city_box')">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox"
                                                   onclick="toggleAllCheckboxes(this,'location_city_box')">
                                            <label class="form-check-label fw-semibold">Select All</label>
                                        </div>
                                        <div id="location_city_box" style="max-height:180px; overflow-y:auto;">
                                            <p class="text-muted small mb-0">Loading…</p>
                                        </div>
                                    </div>
                                    @error('location_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- ======================================================= --}}
        {{-- DESTINATION COVERED                                      --}}
        {{-- ======================================================= --}}
        <div class="card mt-3">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Destination Covered</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="destination_covered_description" class="form-label">Destination Covered Description</label>
                    <textarea class="form-control @error('destination_covered_description') is-invalid @enderror"
                              id="destination_covered_description" name="destination_covered_description" rows="5">{{ old('destination_covered_description', $package->details?->destination_covered_description) }}</textarea>
                    @error('destination_covered_description')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        {{-- ======================================================= --}}
        {{-- CATEGORIES                                               --}}
        {{-- ======================================================= --}}
        <div class="card mt-3">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Categories</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    {{-- Categories --}}
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <div class="border rounded p-2 bg-white">
                                <input type="text" class="form-control form-control-sm mb-2"
                                       placeholder="Search category…" onkeyup="filterCheckboxes(this,'category_box')">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox"
                                           onclick="toggleAllCheckboxes(this,'category_box')">
                                    <label class="form-check-label fw-semibold">Select All</label>
                                </div>
                                <div id="category_box" style="max-height:180px; overflow-y:auto;">
                                    @foreach ($categories as $c)
                                        <div class="form-check city-item">
                                            <input class="form-check-input city-checkbox category-checkbox"
                                                   type="checkbox" name="category_id[]"
                                                   value="{{ $c->id }}" id="category_{{ $c->id }}"
                                                   {{ in_array($c->id, old('category_id', $selectedCategoryIds ?? [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="category_{{ $c->id }}">{{ $c->name }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @error('category_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ======================================================= --}}
        {{-- GROUP TOUR: DEPARTURE DATES                             --}}
        {{-- ======================================================= --}}
        <div class="card mt-3 {{ old('package_mode', $package->package_mode) === 'group_tour' ? '' : 'd-none' }}"
             id="departure-section">
            <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Group Tour — Departure Dates &amp; Pricing</h5>
                <button type="button" class="btn btn-sm btn-light" onclick="addDepartureRow()">
                    <i class="fas fa-plus me-1"></i> Add Departure
                </button>
            </div>
            <div class="card-body">
                @error('departures')
                    <div class="alert alert-danger py-2">{{ $message }}</div>
                @enderror

                {{-- Header --}}
                <div class="row mb-2 fw-semibold d-none d-md-flex">
                    <div class="col-md-3">Departure Date <span class="required-text">*</span></div>
                    <div class="col-md-2">Price (₹) <span class="required-text">*</span></div>
                    <div class="col-md-2">Total Seats</div>
                    <div class="col-md-2">Booked</div>
                    <div class="col-md-2">Status</div>
                    <div class="col-md-1">Action</div>
                </div>

                <div id="departure-wrapper">
                    @php
                        $departuresSource = old('departures')
                            ? old('departures')
                            : (collect($groupDepartures)->map(fn($d) => [
                                'id'           => $d['id'],
                                'date'         => $d['departure_date'],
                                'price'        => $d['price'],
                                'total_seats'  => $d['total_seats'],
                                'booked_seats' => $d['booked_seats'],
                                'status'       => $d['status'],
                              ])->toArray());
                    @endphp

                    @foreach ($departuresSource as $i => $dep)
                        <div class="row departure-row mb-2 align-items-center border rounded p-2">
                            @if (!empty($dep['id']))
                                <input type="hidden" name="departures[{{ $i }}][id]" value="{{ $dep['id'] }}">
                            @endif
                            <div class="col-md-3 mb-2 mb-md-0">
                                <input type="date" name="departures[{{ $i }}][date]"
                                       class="form-control"
                                       value="{{ $dep['date'] ?? ($dep['departure_date'] ?? '') }}" required>
                            </div>
                            <div class="col-md-2 mb-2 mb-md-0">
                                <input type="number" name="departures[{{ $i }}][price]"
                                       class="form-control" placeholder="Price"
                                       value="{{ $dep['price'] ?? '' }}" min="0" step="0.01" required>
                            </div>
                            <div class="col-md-2 mb-2 mb-md-0">
                                <input type="number" name="departures[{{ $i }}][total_seats]"
                                       class="form-control" placeholder="Seats"
                                       value="{{ $dep['total_seats'] ?? 20 }}" min="1">
                            </div>
                            <div class="col-md-2 mb-2 mb-md-0">
                                <input type="number" class="form-control bg-light"
                                       value="{{ $dep['booked_seats'] ?? 0 }}" disabled
                                       title="Booked seats (read-only)">
                            </div>
                            <div class="col-md-2 mb-2 mb-md-0">
                                <select name="departures[{{ $i }}][status]" class="form-select">
                                    <option value="available" {{ ($dep['status'] ?? '') === 'available' ? 'selected' : '' }}>Available</option>
                                    <option value="soldout"   {{ ($dep['status'] ?? '') === 'soldout'   ? 'selected' : '' }}>Sold Out</option>
                                    <option value="cancelled" {{ ($dep['status'] ?? '') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-sm btn-danger" onclick="removeDepartureRow(this)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div id="departure-empty-msg" class="{{ count($departuresSource) ? 'd-none' : '' }}">
                    <p class="text-muted small mb-0">No departure dates. Click "Add Departure" above.</p>
                </div>
            </div>
        </div>

        <div class="row" id="itinerary_days">
            @foreach ($package->itineraries as $i => $it)
                <div class="col-md-6 day-card" id="day-{{ $i + 1 }}">
                    <div class="card mt-3">
                        <div class="card-header"><h6 class="mb-0">Day {{ $i + 1 }}</h6></div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Title <span class="required-text">*</span></label>
                                <input type="text" class="form-control"
                                       name="itineraries[{{ $i + 1 }}][title]"
                                       value="{{ old('itineraries.'.($i+1).'.title', $it->title) }}" required>
                            </div>
                            <div class="mb-3">
                                <textarea class="form-control tinymce"
                                          id="itinerary_details_{{ $i + 1 }}"
                                          name="itineraries[{{ $i + 1 }}][details]"
                                          rows="4">{{ old('itineraries.'.($i+1).'.details', $it->details) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Slug duplicate warning --}}
        <div class="card mt-3 d-none" id="package-exists-sce">
            <div class="card-body text-center">
                <div class="invalid-feedback d-block" id="package-exists"></div>
            </div>
        </div>

        {{-- Submit --}}
        <div class="card mt-3 mb-4">
            <div class="card-body text-center">
                <button type="button" class="btn btn-success btn-lg" id="submit-btn">
                    <i class="fas fa-save me-2"></i>Update Package
                </button>
                <button type="button" class="btn btn-warning btn-lg ms-2" id="draftBtn">
                    <i class="fas fa-file-alt me-2"></i>Save as Draft
                </button>
                <a href="{{ route('admin.packages.index') }}" class="btn btn-secondary btn-lg ms-2">
                    <i class="fas fa-times me-2"></i>Cancel
                </a>
            </div>
        </div>

    </form>
</div>

@endsection

@section('scripts')
<script>
// Pre-populated IDs from server
const preselectedSourceCities      = @json(old('source_location_id', $selectedSourceIds ?? []));
const preselectedDestinationCities = @json(old('location_id', $selectedDestinationIds ?? []));
const preselectedHighlights        = @json(old('location_highlights', $destinationHighlights ?? []));
const preselectedSourceStateId      = @json($selectedSourceStateId ?? null);
const preselectedDestinationStateId = @json($selectedDestinationStateId ?? null);
const packageId                    = {{ $package->id }};

// ================================================================
// DEPARTURE ROWS
// ================================================================
function addDepartureRow() {
    const index = document.querySelectorAll('.departure-row').length;
    const html  = `
    <div class="row departure-row mb-2 align-items-center border rounded p-2">
        <div class="col-md-3 mb-2 mb-md-0">
            <input type="date" name="departures[${index}][date]" class="form-control" required>
        </div>
        <div class="col-md-2 mb-2 mb-md-0">
            <input type="number" name="departures[${index}][price]" class="form-control" placeholder="Price (₹)" min="0" step="0.01" required>
        </div>
        <div class="col-md-2 mb-2 mb-md-0">
            <input type="number" name="departures[${index}][total_seats]" class="form-control" placeholder="Seats" value="20" min="1">
        </div>
        <div class="col-md-2 mb-2 mb-md-0">
            <input type="number" class="form-control bg-light" value="0" disabled title="Booked seats">
        </div>
        <div class="col-md-2 mb-2 mb-md-0">
            <select name="departures[${index}][status]" class="form-select">
                <option value="available">Available</option>
                <option value="soldout">Sold Out</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>
        <div class="col-md-1">
            <button type="button" class="btn btn-sm btn-danger" onclick="removeDepartureRow(this)">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    </div>`;

    document.getElementById('departure-wrapper').insertAdjacentHTML('beforeend', html);
    document.getElementById('departure-empty-msg').classList.add('d-none');
    reindexDepartureRows();
}

function removeDepartureRow(btn) {
    btn.closest('.departure-row').remove();
    reindexDepartureRows();
    if (!document.querySelectorAll('.departure-row').length) {
        document.getElementById('departure-empty-msg').classList.remove('d-none');
    }
}

function reindexDepartureRows() {
    document.querySelectorAll('#departure-wrapper .departure-row').forEach((row, i) => {
        row.querySelectorAll('input[name], select[name]').forEach(el => {
            el.name = el.name.replace(/departures\[\d+\]/, `departures[${i}]`);
        });
    });
}

// ================================================================
// PACKAGE MODE TOGGLE
// ================================================================
function toggleDepartureSection(mode) {
    document.getElementById('departure-section').classList.toggle('d-none', mode !== 'group_tour');
}

document.addEventListener('DOMContentLoaded', function () {
    const modeSelect = document.getElementById('package_mode');
    if (modeSelect) {
        // Existing departure section toggle
        modeSelect.addEventListener('change', () => {
            toggleDepartureSection(modeSelect.value);
            togglePriceField(modeSelect.value);       // ← NEW
        });

        // Run both on initial load
        toggleDepartureSection(modeSelect.value);
        togglePriceField(modeSelect.value);           // ← NEW
    }
});

// ================================================================
// LOCATION HELPERS
// ================================================================

function loadCities(country, element, selectedCities = [], callback = null, highlightsMap = {}, stateId = '') {
    const isDestination = element === 'destination';
    const box           = isDestination ? '#location_city_box' : '#source_city_box';
    const inputName     = isDestination ? 'location_id[]' : 'source_location_id[]';
    const className     = isDestination ? 'dest-city-checkbox' : 'source-city-checkbox';

    if (!country.value) { $(box).html('<p class="text-muted small mb-0">Please select country first</p>'); return; }
    if (!stateId && (!selectedCities || !selectedCities.length)) {
        $(box).html('<p class="text-muted small mb-0">Please select a state to view cities.</p>');
        return;
    }

    $(box).html('<p class="text-muted small mb-0">Loading…</p>');
    $.ajax({
        type: 'GET',
        url: '{{ route('admin.locations.index') }}',
        data: { country_id: country.value, state_id: stateId },
        success: function (res) {
            $(box).empty();
            if (!res.cities || !res.cities.length) {
                $(box).html('<p class="text-muted small mb-0">No cities found</p>'); return;
            }
            res.cities.forEach(city => {
                const checked = Array.isArray(selectedCities) && selectedCities.map(String).includes(String(city.id)) ? 'checked' : '';
                const highlightsHtml = isDestination ? `
                    <input type="text" class="form-control form-control-sm mt-1 dest-highlights-input ${checked ? '' : 'd-none'}"
                           name="location_highlights[${city.id}]"
                           placeholder="Highlights, pipe-separated (e.g. Red Fort | Jama Masjid)"
                           value="${(highlightsMap[city.id] || '').replace(/"/g, '&quot;')}">
                ` : '';
                $(box).append(`
                    <div class="form-check city-item">
                        <input class="form-check-input city-checkbox ${className}"
                               type="checkbox" name="${inputName}" value="${city.id}"
                               id="${element}_city_${city.id}" ${checked}
                               ${isDestination ? 'onchange="toggleHighlightsInput(this)"' : ''}>
                        <label class="form-check-label" for="${element}_city_${city.id}">${city.name}</label>
                        ${highlightsHtml}
                    </div>`);
            });
            if (typeof callback === 'function') callback();
        },
        error: function (xhr) {
            $(box).html('<p class="text-danger small mb-0">Failed to load cities (Error ' + xhr.status + '). Please refresh the page.</p>');
        }
    });
}

function toggleHighlightsInput(checkbox) {
    const input = checkbox.closest('.city-item').querySelector('.dest-highlights-input');
    if (input) input.classList.toggle('d-none', !checkbox.checked);
}

function reloadCityBox(element) {
    const isDestination = element === 'destination';
    const box = isDestination ? '#location_city_box' : '#source_city_box';
    const stateSelect = document.getElementById(element + '_state_id');
    const checked = $(box + ' input:checkbox:checked').map(function() { return this.value; }).get();
    const highlightsMap = {};
    if (isDestination) {
        $(box + ' .dest-highlights-input').each(function() {
            const m = $(this).attr('name').match(/\[(\d+)\]/);
            if (m) highlightsMap[m[1]] = $(this).val();
        });
    }
    loadCities({ value: 1 }, element, checked, null, highlightsMap, stateSelect.value);
}

function loadStatesForLocation() {
    $.ajax({
        type: 'GET',
        url: '{{ route('admin.get.states') }}',
        data: { country_id: 1 },
        success: function(res) {
            const options = (res.states || []).map(s => `<option value="${s.id}">${s.name}</option>`).join('');
            $('#source_state_id, #destination_state_id').append(options);
            if (preselectedSourceStateId) $('#source_state_id').val(preselectedSourceStateId);
            if (preselectedDestinationStateId) $('#destination_state_id').val(preselectedDestinationStateId);
        }
    });
}

function toggleAllCheckboxes(master, boxId) {
    document.querySelectorAll(`#${boxId} input[type="checkbox"]`).forEach(cb => {
        const item = cb.closest('.form-check');
        if (!item || item.style.display !== 'none') cb.checked = master.checked;
    });
}

function filterCheckboxes(input, boxId) {
    const term = input.value.toLowerCase();
    document.querySelectorAll(`#${boxId} .form-check`).forEach(item => {
        item.style.display = item.innerText.toLowerCase().includes(term) ? '' : 'none';
    });
}

// ================================================================
// ITINERARY ADD/REMOVE
// ================================================================
function generateItinerary() {
    const days   = parseInt($('#duration_days').val())   || 0;
    const nights = parseInt($('#duration_nights').val()) || 0;
    const total  = Math.max(days, nights);

    const $container = $('#itinerary_days');
    let existing     = $container.find('.day-card').length;

    if (total > existing) {
        for (let i = existing + 1; i <= total; i++) {
            $container.append(`
                <div class="col-md-6 day-card" id="day-${i}">
                    <div class="card mt-3">
                        <div class="card-header"><h6 class="mb-0">Day ${i}</h6></div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Title <span class="required-text">*</span></label>
                                <input type="text" class="form-control" name="itineraries[${i}][title]" required>
                            </div>
                            <div class="mb-3">
                                <textarea class="form-control tinymce"
                                          id="itinerary_details_${i}"
                                          name="itineraries[${i}][details]" rows="4"></textarea>
                            </div>
                        </div>
                    </div>
                </div>`);
        }
    } else if (total < existing) {
        for (let i = existing; i > total; i--) {
            if (typeof tinymce !== 'undefined') tinymce.remove(`#itinerary_details_${i}`);
            $(`#day-${i}`).remove();
        }
    }
}

// ================================================================
// POPULATE GALLERY (existing images, on page load)
// ================================================================
@php
    $existingGalleryImagesData = $package->images->map(fn ($img) => [
        'path' => $img->image_path,
        'url' => storage_link($img->image_path),
        'alt' => $img->image_alt,
    ]);
@endphp
const existingGalleryImages = @json($existingGalleryImagesData);

// ================================================================
// READY
// ================================================================
$(document).ready(function () {
    // Auto-scroll to validation errors on page load
    if ($('#global-error-alert').length) {
        $('html, body').animate({ scrollTop: $('#global-error-alert').offset().top - 80 }, 400);
    }

    $(document).on('change', '#duration_days',   generateItinerary);
    $(document).on('change', '#duration_nights', generateItinerary);

    // Auto-load India cities
    loadCities({ value: 1 }, 'source',      preselectedSourceCities);
    loadCities({ value: 1 }, 'destination', preselectedDestinationCities, null, preselectedHighlights);
    loadStatesForLocation();

    if (typeof window.setMediaGalleryItems === 'function') {
        window.setMediaGalleryItems('pkg_gallery', existingGalleryImages);
    }

});


// ================================================================
// FORM SUBMIT
// ================================================================
function submitForm(saveType) {
    tinymce.triggerSave();
    document.getElementById('save_type_hidden').value = saveType;

    if (saveType === 'draft') {
        document.getElementById('packageForm').submit();
        return;
    }

    let isError = false;

    const srcStateSelect = document.getElementById('source_state_id');
    if (!srcStateSelect.value) {
        isError = true;
        srcStateSelect.classList.add('is-invalid');
        Swal.fire('Missing Information', 'Please select a Source State before submitting.', 'warning');
        $('html,body').animate({ scrollTop: $(srcStateSelect).offset().top - 100 }, 500);
    } else if (!document.querySelectorAll('#source_city_box input[type="checkbox"]:checked').length) {
        isError = true;
        Swal.fire('Missing Information', 'Please select at least one Departure City before submitting.', 'warning');
        $('html,body').animate({ scrollTop: $('#source_city_box').offset().top - 100 }, 500);
    }

    const destStateSelect = document.getElementById('destination_state_id');
    if (!destStateSelect.value) {
        isError = true;
        destStateSelect.classList.add('is-invalid');
        Swal.fire('Missing Information', 'Please select a Destination State before submitting.', 'warning');
        $('html,body').animate({ scrollTop: $(destStateSelect).offset().top - 100 }, 500);
    } else if (!document.querySelectorAll('#location_city_box input[type="checkbox"]:checked').length) {
        isError = true;
        Swal.fire('Missing Information', 'Please select at least one Destination City before submitting.', 'warning');
        $('html,body').animate({ scrollTop: $('#location_city_box').offset().top - 100 }, 500);
    }


    const mode = document.getElementById('package_mode').value;
    if (mode === 'group_tour' && !document.querySelectorAll('#departure-wrapper .departure-row').length) {
        isError = true;
        Swal.fire('Missing Information', 'Please add at least one departure date for a Group Tour.', 'warning');
        $('html,body').animate({ scrollTop: $('#departure-section').offset().top - 100 }, 500);
    }

    if (isError) return;

    // Slug duplicate check (exclude current package)
    const $btn = $('#submit-btn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Checking…');
    $('#package-exists-sce').addClass('d-none');

    $.ajax({
        url: '{{ route('admin.packages.slug.duplicate_check') }}',
        data: {
            title:           $('#title').val(),
            duration_days:   $('#duration_days').val(),
            duration_nights: $('#duration_nights').val(),
            id:              packageId
        },
        success: function (res) {
            if (res.exists) {
                $btn.prop('disabled', false).html('<i class="fas fa-save me-2"></i>Update Package');
                $('#package-exists-sce').removeClass('d-none');
                $('#package-exists').text('A package with this title and duration already exists.');
            } else {
                $('#package-exists-sce').addClass('d-none');
                document.getElementById('packageForm').submit();
            }
        },
        error: function () { document.getElementById('packageForm').submit(); }
    });
}

document.getElementById('submit-btn').addEventListener('click', () => submitForm('publish'));
document.getElementById('draftBtn').addEventListener('click',   () => submitForm('draft'));

// ================================================================
// PRICE FIELD TOGGLE (shown only for group_tour)
// ================================================================
function togglePriceField(mode) {
    const wrapper = document.getElementById('price_wrapper');
    const input   = document.getElementById('price');

    if (!wrapper || !input) return;

    if (mode === 'group_tour') {
        wrapper.style.display = '';   // show
        input.required        = true;
    } else {
        wrapper.style.display = 'none'; // hide
        input.required        = false;
        input.value           = '';     // clear value so it isn't submitted
    }
}
</script>
@endsection