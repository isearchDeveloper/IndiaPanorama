@extends('layouts.app')
@section('title', 'Create Package')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h2"><i class="fas fa-plus me-2"></i>Create New Package</h1>
                <a href="{{ route('admin.packages.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Packages
                </a>
            </div>
        </div>
    </div>

    @if ($errors->has('general'))
    <div class="alert alert-danger">{{ $errors->first('general') }}</div>
    @endif

    <form id="packageForm" method="POST" action="{{ route('admin.packages.store') }}" enctype="multipart/form-data">
        @csrf

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
                            <label for="title" class="form-label">Package Name <span class="required-text">*</span></label>
                            <input type="text"
                                class="form-control @error('title') is-invalid @enderror"
                                id="title" name="title"
                                value="{{ old('title') }}" required>
                            @error('title')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Slug (read-only, auto-generated) --}}
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="slug" class="form-label">Slug <span class="required-text">*</span>
                                <small class="text-muted">(auto-generated)</small>
                            </label>
                            <input type="text"
                                class="form-control bg-light @error('slug') is-invalid @enderror"
                                id="slug" name="slug"
                                value="{{ old('slug') }}"
                                placeholder="Auto generated from title + days/nights"
                                readonly>
                            @error('slug')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div class="invalid-feedback d-none" id="slug-error"></div>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="type" value="1">

                <input type="hidden" id="package_mode" name="package_mode" value="normal">

                <div class="row">
                    {{-- Parent Category --}}
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="parent" class="form-label">Parent Category <span class="required-text">*</span></label>
                            <select class="form-select @error('parent') is-invalid @enderror" id="parent" name="parent">
                                <option value="">Loading…</option>
                            </select>
                            @error('parent')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Duration --}}
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="duration_days" class="form-label">Duration (Days) <span class="required-text">*</span></label>
                            <input type="number"
                                class="form-control @error('duration_days') is-invalid @enderror"
                                id="duration_days" name="duration_days"
                                min="1" max="60"
                                value="{{ old('duration_days') }}">
                            @error('duration_days')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="duration_nights" class="form-label">Duration (Nights) <span class="required-text">*</span></label>
                            <input type="number"
                                class="form-control @error('duration_nights') is-invalid @enderror"
                                id="duration_nights" name="duration_nights"
                                min="0" max="60"
                                value="{{ old('duration_nights', 0) }}"
                                readonly>
                            @error('duration_nights')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Line 2: Flags --}}
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label d-block">Top Trending?</label>
                            <input type="checkbox" name="is_top_trending" value="1"
                                {{ old('is_top_trending') ? 'checked' : '' }}>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label d-block">Special Package?</label>
                            <input type="checkbox" name="is_special_package" value="1"
                                {{ old('is_special_package') ? 'checked' : '' }}>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label d-block">Is Festival Tour Packages?</label>
                            <input type="checkbox" name="is_festival_package" value="1"
                                {{ old('is_festival_package') ? 'checked' : '' }}>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="festival_id" class="form-label">Select Specific Festival</label>
                            <select name="festival_id" id="festival_id" class="form-select">
                                <option value="">— None —</option>
                                @foreach($festivals as $festival)
                                <option value="{{ $festival->id }}" {{ old('festival_id') == $festival->id ? 'selected' : '' }}>{{ $festival->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Price: only shown for Group Tour --}}
                <div class="row" id="price_wrapper" style="display:none;">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="price" class="form-label">Price <span class="required-text">*</span></label>
                            <input type="number"
                                class="form-control @error('price') is-invalid @enderror"
                                id="price" name="price"
                                value="{{ old('price') }}"
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
                                id="tour_highlights" name="tour_highlights" rows="3">{{ old('tour_highlights') }}</textarea>
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
                        <h6 class="mb-0">Primary Image <span class="required-text">*</span></h6>
                    </div>
                    <div class="card-body">
                        {{-- Row 1: image + alt --}}
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <x-media-picker name="primary_image" label="" folder="packages" />
                                @error('primary_image')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <input type="text" class="form-control"
                                    id="primary_image_alt" name="primary_image_alt"
                                    value="{{ old('primary_image_alt') }}"
                                    placeholder="Primary Image Alt Text">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Gallery Images --}}
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="mb-0">Gallery Images</h6>
                    </div>
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
                            <div class="card-header">
                                <h6 class="mb-0">Source Location (Departure From)</h6>
                            </div>
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
                                            <p class="text-muted mb-0 small">Loading…</p>
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
                            <div class="card-header">
                                <h6 class="mb-0">Destination Location</h6>
                            </div>
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
                                            <p class="text-muted mb-0 small">Loading…</p>
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
                        id="destination_covered_description" name="destination_covered_description" rows="5">{{ old('destination_covered_description') }}</textarea>
                    @error('destination_covered_description')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        {{-- ======================================================= --}}
        {{-- THEMES & CATEGORIES                                      --}}
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
                                    <input class="form-check-input" type="checkbox" id="select_all_category"
                                        onclick="toggleAllCheckboxes(this,'category_box')">
                                    <label class="form-check-label fw-semibold" for="select_all_category">Select All</label>
                                </div>
                                <div id="category_box" style="max-height:180px; overflow-y:auto;">
                                    @foreach ($categories as $c)
                                    <div class="form-check category-item">
                                        <input class="form-check-input category-checkbox"
                                            type="checkbox" name="category_id[]"
                                            value="{{ $c->id }}" id="cat_{{ $c->id }}"
                                            {{ in_array($c->id, old('category_id', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="cat_{{ $c->id }}">{{ $c->name }}</label>
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
        {{-- GROUP TOUR: DEPARTURE DATES (shown only for group_tour) --}}
        {{-- ======================================================= --}}
        <div class="card mt-3 {{ old('package_mode') === 'group_tour' ? '' : 'd-none' }}" id="departure-section">
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
                    <div class="col-md-3">Status</div>
                    <div class="col-md-2">Action</div>
                </div>

                <div id="departure-wrapper">
                    @if (old('package_mode') === 'group_tour' && old('departures'))
                    @foreach (old('departures') as $i => $dep)
                    <div class="row departure-row mb-2 align-items-center border rounded p-2">
                        <div class="col-md-3 mb-2 mb-md-0">
                            <input type="date" name="departures[{{ $i }}][date]"
                                class="form-control @error('departures.'.$i.'.date') is-invalid @enderror"
                                value="{{ $dep['date'] ?? '' }}" min="{{ date('Y-m-d') }}" required>
                            @error('departures.'.$i.'.date')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-2 mb-2 mb-md-0">
                            <input type="number" name="departures[{{ $i }}][price]"
                                class="form-control @error('departures.'.$i.'.price') is-invalid @enderror"
                                placeholder="Price" value="{{ $dep['price'] ?? '' }}"
                                min="0" step="0.01" required>
                            @error('departures.'.$i.'.price')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-2 mb-2 mb-md-0">
                            <input type="number" name="departures[{{ $i }}][total_seats]"
                                class="form-control" placeholder="Seats"
                                value="{{ $dep['total_seats'] ?? 20 }}" min="1">
                        </div>
                        <div class="col-md-3 mb-2 mb-md-0">
                            <select name="departures[{{ $i }}][status]" class="form-select">
                                <option value="available" {{ ($dep['status'] ?? '') === 'available' ? 'selected' : '' }}>Available</option>
                                <option value="soldout" {{ ($dep['status'] ?? '') === 'soldout'   ? 'selected' : '' }}>Sold Out</option>
                                <option value="cancelled" {{ ($dep['status'] ?? '') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-sm btn-danger" onclick="removeDepartureRow(this)">
                                <i class="fas fa-trash"></i> Remove
                            </button>
                        </div>
                    </div>
                    @endforeach
                    @endif
                </div>

                <div id="departure-empty-msg" class="{{ (old('package_mode') === 'group_tour' && old('departures')) ? 'd-none' : '' }}">
                    <p class="text-muted mb-0"><small>No departure dates added yet. Click "Add Departure" above.</small></p>
                </div>
            </div>
        </div>

        <div class="row" id="itinerary_days"></div>

        {{-- ======================================================= --}}
        {{-- FORM ACTIONS                                             --}}
        {{-- ======================================================= --}}
        <div class="card mt-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 text-right">
                        <a href="{{ route('admin.packages.index') }}" class="btn btn-secondary btn-lg ms-2">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                        <button type="button" class="btn btn-warning btn-lg ms-2" id="draftBtn">
                            <i class="fas fa-file-alt me-2"></i>Save as Draft
                        </button>
                        <button type="button" class="btn btn-primary btn-lg" id="submitBtn">
                            <i class="fas fa-save me-2"></i>Create Package
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Slug/duplicate warning --}}
        <div class="card mt-3 d-none" id="package-exists-sce">
            <div class="card-body text-center">
                <div class="invalid-feedback d-block" id="package-exists"></div>
            </div>
        </div>

    </form>
</div>

@endsection

@section('scripts')
<script>
    // ================================================================
    // DEPARTURE ROWS
    // ================================================================
    function addDepartureRow() {
        const index = document.querySelectorAll('.departure-row').length;
        const today = "{{ date('Y - m - d ') }}";
        const html = `
    <div class="row departure-row mb-2 align-items-center border rounded p-2">
        <div class="col-md-3 mb-2 mb-md-0">
            <input type="date" name="departures[${index}][date]" class="form-control" min="${today}" required>
        </div>
        <div class="col-md-2 mb-2 mb-md-0">
            <input type="number" name="departures[${index}][price]" class="form-control" placeholder="Price (₹)" min="0" step="0.01" required>
        </div>
        <div class="col-md-2 mb-2 mb-md-0">
            <input type="number" name="departures[${index}][total_seats]" class="form-control" placeholder="Seats" value="20" min="1">
        </div>
        <div class="col-md-3 mb-2 mb-md-0">
            <select name="departures[${index}][status]" class="form-select">
                <option value="available">Available</option>
                <option value="soldout">Sold Out</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-sm btn-danger" onclick="removeDepartureRow(this)">
                <i class="fas fa-trash"></i> Remove
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
            row.querySelectorAll('input, select').forEach(el => {
                if (el.name) el.name = el.name.replace(/departures\[\d+\]/, `departures[${i}]`);
            });
        });
    }

    // ================================================================
    // PACKAGE MODE TOGGLE
    // ================================================================
    function toggleDepartureSection(mode) {
        const section = document.getElementById('departure-section');
        section.classList.toggle('d-none', mode !== 'group_tour');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const modeSelect = document.getElementById('package_mode');
        if (modeSelect) {
            // Existing departure section toggle
            modeSelect.addEventListener('change', () => {
                toggleDepartureSection(modeSelect.value);
                togglePriceField(modeSelect.value); // ← NEW
            });

            // Run both on initial load
            toggleDepartureSection(modeSelect.value);
            togglePriceField(modeSelect.value); // ← NEW
        }
    });

    // ================================================================
    // LOCATION HELPERS
    // ================================================================

    function loadCities(country, element, selectedCities = [], callback = null, highlightsMap = {}, stateId = '') {
        const isDestination = element === 'destination';
        const box = isDestination ? '#location_city_box' : '#source_city_box';
        const inputName = isDestination ? 'location_id[]' : 'source_location_id[]';
        const className = isDestination ? 'dest-city-checkbox' : 'source-city-checkbox';

        if (!country.value) {
            $(box).html('<p class="text-muted small mb-0">Please select country first</p>');
            return;
        }
        if (!stateId && (!selectedCities || !selectedCities.length)) {
            $(box).html('<p class="text-muted small mb-0">Please select a state to view cities.</p>');
            return;
        }

        $(box).html('<p class="text-muted small mb-0">Loading…</p>');
        $.ajax({
            type: 'GET',
            url: "{{ route('admin.locations.index') }}",
            data: {
                country_id: country.value,
                state_id: stateId
            },
            success: function(res) {
                $(box).empty();
                if (!res.cities || !res.cities.length) {
                    $(box).html('<p class="text-muted small mb-0">No cities found</p>');
                    return;
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
            url: "{{ route('admin.get.states') }}",
            data: { country_id: 1 },
            success: function(res) {
                const options = (res.states || []).map(s => `<option value="${s.id}">${s.name}</option>`).join('');
                $('#source_state_id, #destination_state_id').append(options);
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
    // ITINERARY GENERATOR
    // ================================================================
    function generateItinerary() {
        const days = parseInt($('#duration_days').val()) || 0;
        const nights = parseInt($('#duration_nights').val()) || 0;
        const total = Math.max(days, nights);

        const $container = $('#itinerary_days');
        let existing = $container.find('.day-card').length;

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
                                <label class="form-label">Details</label>
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

        createSlug();
    }

    // ================================================================
    // SLUG AUTO-GENERATION
    // ================================================================
    function createSlug() {
        const title = ($('#title').val() || '').toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '').trim().replace(/\s+/g, '-');
        const days = parseInt($('#duration_days').val()) || 0;
        const nights = parseInt($('#duration_nights').val()) || 0;
        let slug = title;

        if (days > 0) slug += `-${days}-${days === 1 ? 'day' : 'days'}`;
        if (nights > 0) slug += `-${nights}-${nights === 1 ? 'night' : 'nights'}`;

        $('#slug').val(slug);

        if (window.SlugChecker) {
            SlugChecker.runCheck(slug, 'packages', 0, '#submitBtn, #draftBtn', document.getElementById('title'));
        }
    }

    // ================================================================
    // READY
    // ================================================================
    $(document).ready(function() {
        createSlug();

        // Clear any stale "category id is required" message as soon as the
        // admin (re)checks a category box, so a fixed-but-not-yet-resubmitted
        // form doesn't keep showing a leftover error from a previous attempt.
        $(document).on('change', '#category_box input[type="checkbox"], #select_all_category', function() {
            if (document.querySelectorAll('#category_box input[name="category_id[]"]:checked').length) {
                $('#category_box').removeClass('is-invalid')
                    .closest('.mb-3').find('.invalid-feedback').remove();
            }
        });

        $(document).on('input', '#title', createSlug);
        $(document).on('input', '#duration_days', function() {
            const days = parseInt($(this).val()) || 0;
            if (days > 0) $('#duration_nights').prop('readonly', false).prop('required', true);
            else $('#duration_nights').prop('readonly', true).val(0);
            generateItinerary();
        });
        $(document).on('change', '#duration_nights', generateItinerary);

        // Auto-load parent categories for India Tour Package (type=1)
        $.ajax({
            url: '{{ route('admin.packages.parent.category') }}',
            data: {
                type: 1
            },
            success: function(res) {
                const oldParent = '{{ old('parent ') }}';
                $('#parent').empty().append('<option value="">Select Parent</option>');
                (res.data || []).forEach(item => {
                    const sel = String(item.id) === String(oldParent) ? 'selected' : '';
                    $('#parent').append(`<option value="${item.id}" ${sel}>${item.name}</option>`);
                });
            }
        });

        // Auto-load India cities on page ready
        const oldSrcCity = @json(old('source_location_id', []));
        const oldDestCity = @json(old('location_id', []));
        loadCities({
            value: 1
        }, 'source', oldSrcCity);
        loadCities({
            value: 1
        }, 'destination', oldDestCity);
        loadStatesForLocation();
    });

    // ================================================================
    // FORM SUBMIT
    // ================================================================
    function submitForm(saveType) {
        tinymce.triggerSave();

        document.getElementById('packageForm').querySelectorAll('input[name="save_type"]').forEach(el => el.remove());
        const inp = document.createElement('input');
        inp.type = 'hidden';
        inp.name = 'save_type';
        inp.value = saveType;
        document.getElementById('packageForm').appendChild(inp);

        if (saveType === 'draft') {
            document.getElementById('packageForm').submit();
            return;
        }

        // Client-side pre-validation
        let isError = false;

        const srcStateSelect = document.getElementById('source_state_id');
        if (!srcStateSelect.value) {
            isError = true;
            srcStateSelect.classList.add('is-invalid');
            Swal.fire('Missing Information', 'Please select a Source State before submitting.', 'warning');
            $('html,body').animate({
                scrollTop: $(srcStateSelect).offset().top - 100
            }, 500);
        } else if (!document.querySelectorAll('#source_city_box input[type="checkbox"]:checked').length) {
            isError = true;
            Swal.fire('Missing Information', 'Please select at least one Departure City before submitting.', 'warning');
            $('html,body').animate({
                scrollTop: $('#source_city_box').offset().top - 100
            }, 500);
        }

        const destStateSelect = document.getElementById('destination_state_id');
        if (!destStateSelect.value) {
            isError = true;
            destStateSelect.classList.add('is-invalid');
            Swal.fire('Missing Information', 'Please select a Destination State before submitting.', 'warning');
            $('html,body').animate({
                scrollTop: $(destStateSelect).offset().top - 100
            }, 500);
        } else if (!document.querySelectorAll('#location_city_box input[type="checkbox"]:checked').length) {
            isError = true;
            Swal.fire('Missing Information', 'Please select at least one Destination City before submitting.', 'warning');
            $('html,body').animate({
                scrollTop: $('#location_city_box').offset().top - 100
            }, 500);
        }

        const days = parseInt($('#duration_days').val()) || 0;
        const nights = parseInt($('#duration_nights').val()) || 0;
        if (days > 1 && nights <= 0) {
            isError = true;
            $('#duration_nights').addClass('is-invalid');
            Swal.fire('Missing Information', 'Duration (Nights) is required when Days > 1.', 'warning');
            $('html,body').animate({
                scrollTop: $('#duration_nights').offset().top - 100
            }, 500);
        }

        const mode = document.getElementById('package_mode').value;
        if (mode === 'group_tour' && !document.querySelectorAll('#departure-wrapper .departure-row').length) {
            isError = true;
            Swal.fire('Missing Information', 'Please add at least one departure date for a Group Tour.', 'warning');
            $('html,body').animate({
                scrollTop: $('#departure-section').offset().top - 100
            }, 500);
        }

        if (isError) return;

        // Slug duplicate check
        const $btn = $('#submitBtn').prop('disabled', true).text('Checking…');
        $('#package-exists-sce').addClass('d-none');

        $.ajax({
            url: '{{ route('admin.packages.slug.duplicate_check') }}',
            data: {
                title: $('#title').val(),
                duration_days: $('#duration_days').val(),
                duration_nights: $('#duration_nights').val()
            },
            success: function(res) {
                if (res.exists) {
                    $btn.prop('disabled', false).text('Create Package');
                    $('#package-exists-sce').removeClass('d-none');
                    $('#package-exists').text('A package with this title and duration already exists. Please modify the title or duration.');
                } else {
                    $('#package-exists-sce').addClass('d-none');
                    document.getElementById('packageForm').submit();
                }
            },
            error: function() {
                document.getElementById('packageForm').submit();
            }
        });
    }

    document.getElementById('submitBtn').addEventListener('click', () => submitForm('publish'));
    document.getElementById('draftBtn').addEventListener('click', () => submitForm('draft'));

    // ================================================================
    // PRICE FIELD TOGGLE (shown only for group_tour)
    // ================================================================
    function togglePriceField(mode) {
        const wrapper = document.getElementById('price_wrapper');
        const input = document.getElementById('price');

        if (!wrapper || !input) return;

        if (mode === 'group_tour') {
            wrapper.style.display = ''; // show
            input.required = true;
        } else {
            wrapper.style.display = 'none'; // hide
            input.required = false;
            input.value = ''; // clear value so it isn't submitted
        }
    }
</script>

@endsection