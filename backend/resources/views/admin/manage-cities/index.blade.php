@section('title','Manage Cities')
@extends('layouts.app')

@push('style')
<style>
    .btn-outline-orange { color: #1d4ed8; background-color: transparent; border: 1px solid #2563eb; }
    .btn-outline-orange:hover { color: #fff; background-color: #2563eb; border-color: #2563eb; }
    .btn-outline-purple { color: #6d28d9; background-color: transparent; border: 1px solid #7c3aed; }
    .btn-outline-purple:hover { color: #fff; background-color: #7c3aed; border-color: #7c3aed; }
    .modal-header-orange { background: linear-gradient(135deg, #2563eb, #1d4ed8); color: #fff; }
    .modal-header-purple { background: linear-gradient(135deg, #8b5cf6, #6d28d9); color: #fff; }
</style>
@endpush

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-0 fw-bold"><i class="fas fa-city me-2 text-primary"></i>Manage Cities</h2>
        </div>
    </div>

    {{-- Tabs --}}
    <ul class="nav nav-tabs mb-4" id="manageCityTabs" role="tablist">
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

    <div class="tab-content" id="manageCityTabContent">

        {{-- ══════════════════════════════════════════════════════
             TAB 1 — REGIONS
        ══════════════════════════════════════════════════════ --}}
        <div class="tab-pane fade {{ request('tab', 'regions') === 'regions' ? 'show active' : '' }}"
             id="pane-regions" role="tabpanel">
            <div class="card">
                <div class="card-header"><h5 class="mb-0"><i class="fas fa-layer-group me-2"></i>Regions</h5></div>
                @include('admin.manage-cities._table', ['rows' => $regions, 'tab' => 'regions'])
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
                    <div class="d-flex gap-2 flex-wrap">
                        <form method="GET" class="d-flex gap-2">
                            <input type="hidden" name="tab" value="states">
                            <input type="text" name="search" class="form-control form-control-sm" style="max-width:220px"
                                   placeholder="Search state…" value="{{ request('search') }}">
                            <button class="btn btn-sm btn-primary"><i class="fas fa-search"></i></button>
                        </form>
                        <button type="button" class="btn btn-sm btn-outline-orange" data-bs-toggle="modal" data-bs-target="#addStateGuideModal">
                            <i class="fas fa-plus me-1"></i> Add State
                        </button>
                    </div>
                </div>
                @include('admin.manage-cities._table', ['rows' => $states, 'tab' => 'states'])
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
                    <div class="d-flex gap-2 flex-wrap">
                        <form method="GET" class="d-flex gap-2">
                            <input type="hidden" name="tab" value="cities">
                            <input type="text" name="search" class="form-control form-control-sm" style="max-width:220px"
                                   placeholder="Search city…" value="{{ request('search') }}">
                            <button class="btn btn-sm btn-primary"><i class="fas fa-search"></i></button>
                        </form>
                        <button type="button" class="btn btn-sm btn-outline-orange" data-bs-toggle="modal" data-bs-target="#addCityGuideModal">
                            <i class="fas fa-plus me-1"></i> Add City
                        </button>
                    </div>
                </div>
                @include('admin.manage-cities._table', ['rows' => $cities, 'tab' => 'cities'])
            </div>
        </div>

    </div>{{-- end tab-content --}}
</div>

@endsection

@section('modal')

{{-- ══════════════ ADD STATE (TO CITY GUIDE) MODAL ══════════════ --}}
<div class="modal fade" id="addStateGuideModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header modal-header-orange">
                <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Add State to City Guide</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="addStateGuideForm">
                @csrf
                <div class="modal-body">
                    @if($missingStates->isEmpty())
                        <p class="text-muted mb-0">Every state already has a City Guide entry.</p>
                    @else
                        <label class="form-label fw-semibold">State</label>
                        <select name="state_id" class="form-select" required>
                            <option value="">— Select State —</option>
                            @foreach($missingStates as $state)
                            <option value="{{ $state->id }}">{{ $state->name }}</option>
                            @endforeach
                        </select>
                        <div class="form-text">Only states already created in Location Setting that don't yet have a City Guide entry are listed here.</div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    @if($missingStates->isNotEmpty())
                    <button type="submit" class="btn" style="background:#2563eb;color:#fff;"><i class="fas fa-plus me-1"></i>Add</button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════ ADD CITY (TO CITY GUIDE) MODAL ══════════════ --}}
<div class="modal fade" id="addCityGuideModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header modal-header-orange">
                <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Add City to City Guide</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="addCityGuideForm">
                @csrf
                <div class="modal-body">
                    @if($missingLocations->isEmpty())
                        <p class="text-muted mb-0">Every city already has a City Guide entry.</p>
                    @else
                        <label class="form-label fw-semibold">City</label>
                        <select name="location_id" class="form-select" required>
                            <option value="">— Select City —</option>
                            @foreach($missingLocations->groupBy(fn($l) => $l->state->name ?? '—') as $stateName => $locs)
                            <optgroup label="{{ $stateName }}">
                                @foreach($locs as $loc)
                                <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                                @endforeach
                            </optgroup>
                            @endforeach
                        </select>
                        <div class="form-text">Only cities already created in Location Setting that don't yet have a City Guide entry are listed here.</div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    @if($missingLocations->isNotEmpty())
                    <button type="submit" class="btn" style="background:#2563eb;color:#fff;"><i class="fas fa-plus me-1"></i>Add</button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════ SETTINGS MODAL ══════════════ --}}
<div class="modal fade" id="settingsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header modal-header-orange">
                <h5 class="modal-title"><i class="fas fa-cog me-2"></i><span id="settings-modal-title">Banner &amp; Settings</span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="settingsForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        {{-- State / City fields --}}
                        <div id="settings-full-fields" class="col-12">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Title</label>
                                    <input type="text" name="title" id="s-title" class="form-control form-control-lg">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Tagline <small class="text-muted fw-normal">(short, max 60 characters — shown on destination cards)</small></label>
                                    <input type="text" name="sub_title" id="s-sub-title" class="form-control" maxlength="60">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Banner Text</label>
                                    <textarea name="banner_text" id="s-banner-text" class="form-control" rows="3"></textarea>
                                </div>
                                <div class="col-md-6">
                                    <x-media-picker name="banner_image" picker-id="banner_image_settings" label="Banner Image" folder="manage_cities" />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Banner Image Alt</label>
                                    <input type="text" name="banner_image_alt" id="s-banner-alt" class="form-control">
                                </div>
                            </div>
                        </div>

                        {{-- Region-only field --}}
                        <div id="settings-region-fields" class="col-12 d-none">
                            <label class="form-label fw-semibold">Short Description</label>
                            <textarea name="short_description" id="s-short-desc" class="tinymce no-char-limit" style="width:100%;min-height:180px"></textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold" id="s-about-label">Short Description</label>
                            <textarea name="about" id="s-about" class="tinymce no-char-limit" style="width:100%;min-height:220px"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn" style="background:#2563eb;color:#fff;" id="settings-save-btn">
                        <i class="fas fa-floppy-disk me-1"></i> Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════ EDIT (BASIC) MODAL — Region only ══════════════ --}}
<div class="modal fade" id="editBasicModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header modal-header-orange">
                <h5 class="modal-title"><i class="fas fa-pen-to-square me-2"></i><span id="edit-basic-modal-title">Edit</span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editBasicForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Title</label>
                            <input type="text" name="title" id="eb-title" class="form-control form-control-lg">
                        </div>
                        <div class="col-md-6">
                            <x-media-picker name="banner_image" picker-id="banner_image_editbasic" label="Banner Image" folder="manage_cities" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Banner Image Alt</label>
                            <input type="text" name="banner_image_alt" id="eb-banner-alt" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Banner Text</label>
                            <textarea name="banner_text" id="eb-banner-text" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn" style="background:#2563eb;color:#fff;" id="edit-basic-save-btn">
                        <i class="fas fa-floppy-disk me-1"></i> Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════ QUICK FACTS MODAL ══════════════ --}}
<div class="modal fade" id="quickFactsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white" style="background:#2196f3;">
                <h5 class="modal-title"><i class="fas fa-bars me-2"></i><span id="qf-modal-title">Quick Facts</span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-0 px-1 py-2 rounded mb-2" style="background:#f0f4f8;">
                    <div class="col-5 ps-2"><span class="text-uppercase fw-semibold small text-secondary">Label</span></div>
                    <div class="col-6 ps-2"><span class="text-uppercase fw-semibold small text-secondary">Text</span></div>
                </div>
                <div id="qf-rows"></div>
                <button type="button" class="btn btn-outline-success btn-sm mt-2" id="qf-add-row">
                    <i class="fas fa-plus me-1"></i> Add Fact
                </button>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn text-white" style="background:#2196f3;" id="qf-save-btn">
                    <i class="fas fa-floppy-disk me-1"></i> Save
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════ FAQS MODAL ══════════════ --}}
<div class="modal fade" id="faqsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background:#f59e0b;">
                <h5 class="modal-title"><i class="fas fa-question-circle me-2"></i><span id="faq-modal-title">FAQs</span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold text-secondary">Section Title</label>
                    <input type="text" id="faq-section-title" class="form-control form-control-lg">
                </div>
                <div class="row g-0 px-1 py-2 rounded mb-2" style="background:#f0f4f8;">
                    <div class="col-5 ps-2"><span class="text-uppercase fw-semibold small text-secondary">Question</span></div>
                    <div class="col-6 ps-2"><span class="text-uppercase fw-semibold small text-secondary">Answer</span></div>
                </div>
                <div id="faq-rows"></div>
                <button type="button" class="btn btn-outline-success btn-sm mt-2" id="faq-add-row">
                    <i class="fas fa-plus me-1"></i> Add FAQ
                </button>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn text-white" style="background:#f59e0b;" id="faq-save-btn">
                    <i class="fas fa-floppy-disk me-1"></i> Save FAQs
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════ META MODAL ══════════════ --}}
<div class="modal fade" id="metaModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header modal-header-purple">
                <h5 class="modal-title"><i class="fas fa-globe me-2"></i><span id="meta-modal-title">SEO Meta</span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="metaForm">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Meta Title</label>
                            <input type="text" name="meta_title" id="m-meta-title" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Meta Description</label>
                            <input type="text" name="meta_description" id="m-meta-desc" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Meta Keywords</label>
                            <textarea name="meta_keywords" id="m-meta-keys" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">H1 Heading</label>
                            <textarea name="h1_heading" id="m-h1" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Extra Meta Tags</label>
                            <textarea name="meta_details" id="m-meta-details" class="form-control" rows="4"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn" style="background:#7c3aed;color:#fff;" id="meta-save-btn">
                        <i class="fas fa-floppy-disk me-1"></i> Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
const CSRF = '{{ csrf_token() }}';
const BASE = '{{ url("admin/city-pages") }}';

function apiUrl(id, suffix) { return `${BASE}/${id}/${suffix}`; }

// ══════════════════════════════════════════════════════════════════════════
// ADD STATE / CITY TO CITY GUIDE
// ══════════════════════════════════════════════════════════════════════════
$('#addStateGuideForm').on('submit', function (e) {
    e.preventDefault();
    const $btn = $(this).find('button[type="submit"]').prop('disabled', true);
    $.post('{{ route("admin.city-pages.store-state") }}', $(this).serialize())
        .done(r => { toastr.success(r.message); location.href = '{{ route("admin.city-pages.index") }}?tab=states'; })
        .fail(x => { toastr.error(x.responseJSON?.message || 'Failed to add state.'); $btn.prop('disabled', false); });
});

$('#addCityGuideForm').on('submit', function (e) {
    e.preventDefault();
    const $btn = $(this).find('button[type="submit"]').prop('disabled', true);
    $.post('{{ route("admin.city-pages.store-city") }}', $(this).serialize())
        .done(r => { toastr.success(r.message); location.href = '{{ route("admin.city-pages.index") }}?tab=cities'; })
        .fail(x => { toastr.error(x.responseJSON?.message || 'Failed to add city.'); $btn.prop('disabled', false); });
});

// ── Status Toggle ─────────────────────────────────────────────────────────
$(document).on('change', '.toggle-status', function () {
    const id = $(this).data('id'), $cb = $(this);
    $.post(apiUrl(id, 'toggle-status'), { _token: CSRF })
        .done(() => toastr.success('Status updated.'))
        .fail(() => { $cb.prop('checked', !$cb.prop('checked')); toastr.error('Failed to update status.'); });
});

// ── Popular Toggle ────────────────────────────────────────────────────────
$(document).on('change', '.toggle-popular', function () {
    const id = $(this).data('id'), $cb = $(this);
    $.post(apiUrl(id, 'toggle-popular'), { _token: CSRF })
        .done(() => toastr.success('Popular status updated.'))
        .fail(() => { $cb.prop('checked', !$cb.prop('checked')); toastr.error('Failed to update popular status.'); });
});

// ══════════════════════════════════════════════════════════════════════════
// SETTINGS MODAL
// ══════════════════════════════════════════════════════════════════════════
let settingsId = null;

$(document).on('click', '.btn-settings', function () {
    settingsId = $(this).data('id');
    $.get(apiUrl(settingsId, 'settings')).done(d => {
        const isRegion = d.type === 'Region';

        $('#settings-modal-title').text((isRegion ? 'Description — ' : 'Banner & Settings — ') + d.display_name);

        // Toggle field groups: State/City get the full field set; Region gets short/long description only.
        $('#settings-full-fields').toggleClass('d-none', isRegion);
        $('#settings-full-fields input, #settings-full-fields textarea').prop('disabled', isRegion);
        $('#settings-region-fields').toggleClass('d-none', !isRegion);
        $('#settings-region-fields textarea').prop('disabled', !isRegion);
        $('#s-about-label').text(isRegion ? 'Long Description' : 'Short Description');

        $('#s-title').val(d.title || '');
        $('#s-sub-title').val(d.sub_title || '');
        $('#s-banner-text').val(d.banner_text || '');
        $('#s-banner-alt').val(d.banner_image_alt || '');
        $('#s-short-desc').val(d.short_description || '');
        if (typeof window.setMediaPickerValue === 'function') {
            window.setMediaPickerValue('banner_image_settings', d.banner_image_path, d.banner_image);
        }
        const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('settingsModal'));
        modal.show();
        document.getElementById('settingsModal').addEventListener('shown.bs.modal', function onShown() {
            this.removeEventListener('shown.bs.modal', onShown);
            if (typeof window.initTinyMCEOn === 'function') window.initTinyMCEOn($('#s-about'));
            setTimeout(() => { tinymce.get('s-about')?.setContent(d.about || ''); }, 100);
            if (isRegion) {
                if (typeof window.initTinyMCEOn === 'function') window.initTinyMCEOn($('#s-short-desc'));
                setTimeout(() => { tinymce.get('s-short-desc')?.setContent(d.short_description || ''); }, 100);
            }
        });
    }).fail(() => toastr.error('Failed to load settings.'));
});

$('#settingsForm').on('submit', function (e) {
    e.preventDefault();
    const $btn = $('#settings-save-btn').prop('disabled', true);
    tinymce.triggerSave();
    const fd = new FormData(this);
    $.ajax({ url: apiUrl(settingsId, 'settings'), method: 'POST', data: fd, processData: false, contentType: false })
        .done(r => { toastr.success(r.message); bootstrap.Modal.getOrCreateInstance(document.getElementById('settingsModal')).hide(); })
        .fail(x => toastr.error(x.responseJSON?.message || 'Error saving settings.'))
        .always(() => $btn.prop('disabled', false));
});

// ══════════════════════════════════════════════════════════════════════════
// EDIT (BASIC) MODAL — Region only: Title, Banner Image, Banner Alt, Banner Text
// ══════════════════════════════════════════════════════════════════════════
let editBasicId = null;

$(document).on('click', '.btn-edit-basic', function () {
    editBasicId = $(this).data('id');
    $.get(apiUrl(editBasicId, 'settings')).done(d => {
        $('#edit-basic-modal-title').text('Edit — ' + d.display_name);
        $('#eb-title').val(d.title || '');
        $('#eb-banner-alt').val(d.banner_image_alt || '');
        $('#eb-banner-text').val(d.banner_text || '');
        if (typeof window.setMediaPickerValue === 'function') {
            window.setMediaPickerValue('banner_image_editbasic', d.banner_image_path, d.banner_image);
        }
        bootstrap.Modal.getOrCreateInstance(document.getElementById('editBasicModal')).show();
    }).fail(() => toastr.error('Failed to load details.'));
});

$('#editBasicForm').on('submit', function (e) {
    e.preventDefault();
    const $btn = $('#edit-basic-save-btn').prop('disabled', true);
    const fd = new FormData(this);
    $.ajax({ url: apiUrl(editBasicId, 'settings'), method: 'POST', data: fd, processData: false, contentType: false })
        .done(r => { toastr.success(r.message); bootstrap.Modal.getOrCreateInstance(document.getElementById('editBasicModal')).hide(); })
        .fail(x => toastr.error(x.responseJSON?.message || 'Error saving.'))
        .always(() => $btn.prop('disabled', false));
});

// ══════════════════════════════════════════════════════════════════════════
// QUICK FACTS MODAL
// ══════════════════════════════════════════════════════════════════════════
let qfId = null;

function qfRowHtml(label, value) {
    label = (label || '').replace(/"/g, '&quot;');
    value = (value || '').replace(/"/g, '&quot;');
    return `<div class="qf-row d-flex gap-2 mb-2 align-items-center">
        <div style="flex:5"><input type="text" class="form-control qf-label" placeholder="e.g. Best Season" value="${label}"></div>
        <div style="flex:6"><input type="text" class="form-control qf-value" placeholder="e.g. November to February" value="${value}"></div>
        <button type="button" class="btn btn-outline-danger qf-del-row" style="min-width:38px"><i class="fas fa-trash"></i></button>
    </div>`;
}

$(document).on('click', '.btn-quick-facts', function () {
    qfId = $(this).data('id');
    $.get(apiUrl(qfId, 'quick-facts')).done(d => {
        $('#qf-modal-title').text('Quick Facts — ' + d.display_name);
        const $wrap = $('#qf-rows').empty();
        if (d.facts.length) { d.facts.forEach(f => $wrap.append(qfRowHtml(f.label, f.value))); }
        else { $wrap.append(qfRowHtml('', '')); }
        bootstrap.Modal.getOrCreateInstance(document.getElementById('quickFactsModal')).show();
    }).fail(() => toastr.error('Failed to load quick facts.'));
});

$(document).on('click', '#qf-add-row', () => $('#qf-rows').append(qfRowHtml('', '')));
$(document).on('click', '.qf-del-row', function () { $(this).closest('.qf-row').remove(); });

$('#qf-save-btn').on('click', function () {
    const $btn = $(this).prop('disabled', true);
    const facts = [];
    $('#qf-rows .qf-row').each(function () {
        facts.push({ label: $(this).find('.qf-label').val(), value: $(this).find('.qf-value').val() });
    });
    $.ajax({ url: apiUrl(qfId, 'quick-facts'), method: 'POST', data: { _token: CSRF, facts }, traditional: false })
        .done(r => { toastr.success(r.message); bootstrap.Modal.getOrCreateInstance(document.getElementById('quickFactsModal')).hide(); })
        .fail(x => toastr.error(x.responseJSON?.message || 'Error saving.'))
        .always(() => $btn.prop('disabled', false));
});

// ══════════════════════════════════════════════════════════════════════════
// FAQS MODAL
// ══════════════════════════════════════════════════════════════════════════
let faqId = null;

function faqRowHtml(question, answer) {
    question = (question || '').replace(/"/g, '&quot;');
    return `<div class="faq-row d-flex gap-2 mb-2 align-items-start">
        <div style="flex:5"><input type="text" class="form-control faq-question" placeholder="Enter question…" value="${question}"></div>
        <div style="flex:6"><textarea class="form-control faq-answer" rows="2" placeholder="Enter answer…">${answer || ''}</textarea></div>
        <button type="button" class="btn btn-outline-danger faq-del-row" style="min-width:38px"><i class="fas fa-trash"></i></button>
    </div>`;
}

$(document).on('click', '.btn-faqs', function () {
    faqId = $(this).data('id');
    $.get(apiUrl(faqId, 'faqs')).done(d => {
        $('#faq-modal-title').text('FAQs — ' + d.display_name);
        $('#faq-section-title').val(d.faq_title || '');
        const $wrap = $('#faq-rows').empty();
        if (d.faqs.length) { d.faqs.forEach(f => $wrap.append(faqRowHtml(f.question, f.answer))); }
        else { $wrap.append(faqRowHtml('', '')); }
        bootstrap.Modal.getOrCreateInstance(document.getElementById('faqsModal')).show();
    }).fail(() => toastr.error('Failed to load FAQs.'));
});

$(document).on('click', '#faq-add-row', () => $('#faq-rows').append(faqRowHtml('', '')));
$(document).on('click', '.faq-del-row', function () { $(this).closest('.faq-row').remove(); });

$('#faq-save-btn').on('click', function () {
    const $btn = $(this).prop('disabled', true);
    const faqs = [];
    $('#faq-rows .faq-row').each(function () {
        faqs.push({ question: $(this).find('.faq-question').val(), answer: $(this).find('.faq-answer').val() });
    });
    $.ajax({ url: apiUrl(faqId, 'faqs'), method: 'POST', data: { _token: CSRF, faqs, faq_title: $('#faq-section-title').val() }, traditional: false })
        .done(r => { toastr.success(r.message); bootstrap.Modal.getOrCreateInstance(document.getElementById('faqsModal')).hide(); })
        .fail(x => toastr.error(x.responseJSON?.message || 'Error saving.'))
        .always(() => $btn.prop('disabled', false));
});

// ══════════════════════════════════════════════════════════════════════════
// META MODAL
// ══════════════════════════════════════════════════════════════════════════
let metaId = null;

$(document).on('click', '.btn-meta', function () {
    metaId = $(this).data('id');
    $.get(apiUrl(metaId, 'meta')).done(d => {
        $('#meta-modal-title').text(d.display_name + ' — SEO Meta');
        $('#m-meta-title').val(d.meta_title || '');
        $('#m-meta-desc').val(d.meta_description || '');
        $('#m-meta-keys').val(d.meta_keywords || '');
        $('#m-h1').val(d.h1_heading || '');
        $('#m-meta-details').val(d.meta_details || '');
        bootstrap.Modal.getOrCreateInstance(document.getElementById('metaModal')).show();
    }).fail(() => toastr.error('Failed to load meta.'));
});

$('#metaForm').on('submit', function (e) {
    e.preventDefault();
    const $btn = $('#meta-save-btn').prop('disabled', true);
    $.post(apiUrl(metaId, 'meta'), $(this).serialize())
        .done(r => { toastr.success(r.message); bootstrap.Modal.getOrCreateInstance(document.getElementById('metaModal')).hide(); })
        .fail(x => toastr.error(x.responseJSON?.message || 'Error saving.'))
        .always(() => $btn.prop('disabled', false));
});

// ── Active tab memory ──────────────────────────────────────────────────────
var urlTab = new URLSearchParams(location.search).get('tab') || 'regions';
var tabEl  = document.getElementById('tab-' + urlTab);
if (tabEl) bootstrap.Tab.getOrCreateInstance(tabEl).show();

document.querySelectorAll('#manageCityTabs button').forEach(function(btn) {
    btn.addEventListener('shown.bs.tab', function(e) {
        history.replaceState(null, '', '?tab=' + e.target.id.replace('tab-', ''));
    });
});
</script>
@endsection
