@section('title', 'Manage Attraction')
@extends('layouts.app')

@push('style')
<style>
    .btn-outline-orange { color: #1d4ed8; background-color: transparent; border: 1px solid #2563eb; }
    .btn-outline-orange:hover { color: #fff; background-color: #2563eb; border-color: #2563eb; }
    .btn-outline-purple { color: #6d28d9; background-color: transparent; border: 1px solid #7c3aed; }
    .btn-outline-purple:hover { color: #fff; background-color: #7c3aed; border-color: #7c3aed; }
    .modal-header-orange { background: linear-gradient(135deg, #2563eb, #1d4ed8); color: #fff; }
    .modal-header-purple { background: linear-gradient(135deg, #8b5cf6, #6d28d9); color: #fff; }
    #ta-gallery-grid img { width: 100%; height: 90px; object-fit: cover; border-radius: 6px; }
</style>
@endpush

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-0 fw-bold"><i class="fas fa-mountain-sun me-2 text-primary"></i>Manage Attraction</h2>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.tourist-attraction-pages.index') }}" class="btn btn-outline-orange">
                <i class="fas fa-map me-2"></i>State / City Pages
            </a>
            <a href="{{ route('admin.tourist-attractions.setting.index') }}" class="btn btn-outline-purple">
                <i class="fas fa-globe me-2"></i>Root Page Setting
            </a>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTaModal">
                <i class="fas fa-plus me-2"></i>Add Attraction
            </button>
        </div>
    </div>

    <div class="mb-3">
        <div class="position-relative" style="max-width:320px;">
            <i class="fas fa-search position-absolute" style="left:12px; top:50%; transform:translateY(-50%); color:#94a3b8; font-size:.85rem;"></i>
            <input type="text" id="taSearch" class="form-control" style="padding-left:32px;"
                   placeholder="Search attraction..." value="{{ request('search') }}">
        </div>
    </div>

    <div class="tab-list mb-3">
        <ul>
            <li><a href="javascript:void(0);" class="tab-link @if($status == 'all') active @endif" data-status="all">All ({{ $allCount }})</a></li>
            <li><a href="javascript:void(0);" class="tab-link @if($status == 'active') active @endif" data-status="active">Active ({{ $activeCount }})</a></li>
            <li><a href="javascript:void(0);" class="tab-link @if($status == 'inactive') active @endif" data-status="inactive">Inactive ({{ $inactiveCount }})</a></li>
        </ul>
    </div>

    <div id="taTableWrapper">
        @include('admin.tourist-attractions._table')
    </div>

</div>
@endsection

@section('modal')

{{-- ══════════════ ADD ATTRACTION MODAL ══════════════ --}}
<div class="modal fade" id="addTaModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Add Tourist Attraction</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="addTaForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Attraction Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="add-ta-name" class="form-control" placeholder="e.g. Tea Gardens in Munnar" required
                                   data-slug-check="tourist-attractions" data-slug-submit="#add-ta-submit-btn" data-slug-suffix="tourist-attractions">
                            <div class="text-danger small mt-1 name-error" style="display:none"></div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Slug <small class="text-muted">(auto-generated)</small></label>
                            <input type="text" name="slug" id="add-ta-slug" class="form-control bg-light"
                                   placeholder="Auto generated from attraction name" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">State <span class="text-danger">*</span></label>
                            <select name="state_id" id="add-ta-state" class="form-select" required>
                                <option value="">— Select State —</option>
                                @foreach($states as $state)
                                <option value="{{ $state->id }}">{{ $state->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">City <span class="text-danger">*</span></label>
                            <select name="location_id" id="add-ta-location" class="form-select" required>
                                <option value="">— Select City —</option>
                                @foreach($locations as $loc)
                                <option value="{{ $loc->id }}" data-state="{{ $loc->state_id }}">{{ $loc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Tagline <small class="text-muted">(short subtitle under banner title)</small></label>
                            <input type="text" name="tagline" id="add-ta-tagline" class="form-control" placeholder="e.g. Discover Endless Emerald Landscapes">
                        </div>
                        <div class="col-md-6">
                            <x-media-picker name="banner_image" picker-id="ta_banner_add" label="Banner Image" folder="tourist-attractions" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Banner Image Alt</label>
                            <input type="text" name="banner_image_alt" id="add-ta-banner-alt" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Short Description</label>
                            <textarea name="short_description" id="add-ta-short-desc" class="form-control tinymce" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="add-ta-submit-btn"><i class="fas fa-save me-1"></i>Create</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════ EDIT CORE INFO MODAL ══════════════ --}}
<div class="modal fade" id="taEditModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Core Info</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="taEditForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" value="PUT">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Attraction Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="edit-ta-name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">State <span class="text-danger">*</span></label>
                            <select name="state_id" id="edit-ta-state" class="form-select" required>
                                @foreach($states as $state)
                                <option value="{{ $state->id }}">{{ $state->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">City <span class="text-danger">*</span></label>
                            <select name="location_id" id="edit-ta-location" class="form-select" required>
                                @foreach($locations as $loc)
                                <option value="{{ $loc->id }}" data-state="{{ $loc->state_id }}">{{ $loc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Tagline</label>
                            <input type="text" name="tagline" id="edit-ta-tagline" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <x-media-picker name="banner_image" picker-id="ta_banner_edit" label="Banner Image" folder="tourist-attractions" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Banner Image Alt</label>
                            <input type="text" name="banner_image_alt" id="edit-ta-banner-alt" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Short Description</label>
                            <textarea name="short_description" id="edit-ta-short-desc" class="form-control tinymce" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" id="edit-ta-submit-btn"><i class="fas fa-save me-1"></i>Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════ QUICK INFO MODAL ══════════════ --}}
<div class="modal fade" id="taQuickInfoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="taQuickInfoModalTitle"><i class="fas fa-list-ul me-2"></i>Quick Information</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="taQuickInfoForm">
                @csrf
                <input type="hidden" name="section" value="quick_info">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Location</label>
                        <input type="text" name="location_text" id="ta-location-text" class="form-control" placeholder="e.g. Munnar, Kerala">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Recommended Duration</label>
                        <input type="text" name="duration_text" id="ta-duration-text" class="form-control" placeholder="e.g. 2 - 4 Hours">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Best For</label>
                        <input type="text" name="best_for" id="ta-best-for" class="form-control" placeholder="e.g. Nature Lovers & Photography">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Best Time to Visit</label>
                        <input type="text" name="best_season" id="ta-best-season" class="form-control" placeholder="e.g. September - March">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info"><i class="fas fa-save me-1"></i>Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════ WHY VISIT + HIGHLIGHTS MODAL ══════════════ --}}
<div class="modal fade" id="taWhyVisitModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background:#2563eb;color:#fff;">
                <h5 class="modal-title" id="taWhyVisitModalTitle"><i class="fas fa-star me-2"></i>Why Visit & Highlights</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="taWhyVisitForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="section" value="why_visit">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Section Title</label>
                        <input type="text" name="why_visit_title" id="ta-wv-title" class="form-control" placeholder="e.g. Why Visit Munnar Tea Gardens?">
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <x-media-picker name="why_visit_image" picker-id="ta_wv_image" label="Image" folder="tourist-attractions" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Image Alt</label>
                            <input type="text" name="why_visit_image_alt" id="ta-wv-image-alt" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="why_visit_description" id="ta-wv-desc" class="form-control" rows="3"></textarea>
                    </div>
                    <hr>
                    <label class="form-label d-block">Highlights <small class="text-muted">(bullet points)</small></label>
                    <table class="table" id="taHighlightsTable">
                        <thead><tr><th>Highlight</th><th width="40"></th></tr></thead>
                        <tbody></tbody>
                    </table>
                    <button type="button" class="btn btn-sm btn-outline-success" id="taAddHighlightRow"><i class="fas fa-plus me-1"></i>Add Highlight</button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn" style="background:#2563eb;color:#fff;"><i class="fas fa-save me-1"></i>Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════ ACTIVITIES (THINGS TO DO) MODAL ══════════════ --}}
<div class="modal fade" id="taActivitiesModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="taActivitiesModalTitle"><i class="fas fa-hiking me-2"></i>Things To Do</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="taActivitiesForm">
                @csrf
                <input type="hidden" name="section" value="activities">
                <div class="modal-body">
                    <table class="table" id="taActivitiesTable">
                        <thead><tr><th>Title</th><th>Description</th><th width="40"></th></tr></thead>
                        <tbody></tbody>
                    </table>
                    <button type="button" class="btn btn-sm btn-outline-success" id="taAddActivityRow"><i class="fas fa-plus me-1"></i>Add Activity</button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success"><i class="fas fa-save me-1"></i>Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════ GALLERY MODAL ══════════════ --}}
<div class="modal fade" id="taGalleryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title" id="taGalleryModalTitle"><i class="fas fa-images me-2"></i>Gallery</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-2" id="ta-gallery-grid"></div>
                <hr>
                <form id="taGalleryAddForm">
                    @csrf
                    <div class="row g-2">
                        <div class="col-12">
                            <x-media-gallery-picker name="gallery_images" picker-id="ta_gallery_add" label="" folder="tourist-attractions/gallery" />
                        </div>
                    </div>
                    <div class="text-end mt-2">
                        <button type="submit" class="btn btn-secondary d-none" id="ta-gallery-save-btn"><i class="fas fa-save me-1"></i>Save</button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════ FAQ MODAL ══════════════ --}}
<div class="modal fade" id="taFaqModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="taFaqModalTitle"><i class="fas fa-question-circle me-2"></i>FAQs</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="taFaqForm">
                @csrf
                <input type="hidden" name="section" value="faqs">
                <div class="modal-body">
                    <div class="row g-3 mb-2">
                        <div class="col-md-6">
                            <label class="form-label">Section Title</label>
                            <input type="text" name="faq_title" id="taf-faq-title" class="form-control" placeholder="FAQ's">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Section Sub Title</label>
                            <input type="text" name="faq_sub_title" id="taf-faq-sub-title" class="form-control">
                        </div>
                    </div>
                    <table class="table" id="taFaqTable">
                        <thead><tr><th>Question</th><th>Answer</th><th width="40"></th></tr></thead>
                        <tbody></tbody>
                    </table>
                    <button type="button" class="btn btn-sm btn-outline-success" id="taAddFaqRow"><i class="fas fa-plus me-1"></i>Add FAQ</button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning"><i class="fas fa-save me-1"></i>Save FAQs</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════ SEO META MODAL ══════════════ --}}
<div class="modal fade" id="taMetaModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header modal-header-purple">
                <h5 class="modal-title" id="taMetaModalTitle"><i class="fas fa-globe me-2"></i>SEO Meta</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="taMetaForm">
                @csrf
                <input type="hidden" name="section" value="meta">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Meta Title</label>
                            <input type="text" name="meta_title" id="tam-meta-title" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Meta Description</label>
                            <input type="text" name="meta_description" id="tam-meta-desc" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Meta Keywords</label>
                            <input type="text" name="meta_keywords" id="tam-meta-keywords" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">H1 Heading</label>
                            <input type="text" name="h1_heading" id="tam-h1-heading" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Extra Meta Tags</label>
                            <textarea name="meta_details" id="tam-meta-details" class="form-control" rows="4"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn" style="background:#7c3aed;color:#fff;"><i class="fas fa-save me-1"></i>Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
function taHighlightRow(idx, text) {
    text = (text || '').replace(/"/g, '&quot;');
    return '<tr><td><input type="text" name="highlights[' + idx + ']" value="' + text + '" class="form-control" placeholder="e.g. Rolling tea-covered hills"></td>' +
        '<td><button type="button" class="btn btn-sm btn-outline-danger rm-row"><i class="fas fa-trash"></i></button></td></tr>';
}

function taActivityRow(idx, title, description) {
    title = (title || '').replace(/"/g, '&quot;');
    return '<tr><td><input type="text" name="activities[' + idx + '][title]" value="' + title + '" class="form-control" placeholder="e.g. Plantation Walks"></td>' +
        '<td><textarea name="activities[' + idx + '][description]" class="form-control">' + (description || '') + '</textarea></td>' +
        '<td><button type="button" class="btn btn-sm btn-outline-danger rm-row"><i class="fas fa-trash"></i></button></td></tr>';
}

function taFaqRow(idx, question, answer) {
    question = (question || '').replace(/"/g, '&quot;');
    return '<tr><td><input type="text" name="faqs[' + idx + '][question]" value="' + question + '" class="form-control" required></td>' +
        '<td><textarea name="faqs[' + idx + '][answer]" class="form-control">' + (answer || '') + '</textarea></td>' +
        '<td><button type="button" class="btn btn-sm btn-outline-danger rm-row"><i class="fas fa-trash"></i></button></td></tr>';
}

function taReindex(tableSel, prefix, fields) {
    $(tableSel + ' tbody tr').each(function (i) {
        let $row = $(this);
        if (fields) {
            fields.forEach(function (f) {
                $row.find('[name$="[' + f + ']"]').attr('name', prefix + '[' + i + '][' + f + ']');
            });
        } else {
            $row.find('input[name^="' + prefix + '"]').attr('name', prefix + '[' + i + ']');
        }
    });
}

function taFilterLocations(stateSelectId, locationSelectId) {
    let stateId = $('#' + stateSelectId).val();
    let $location = $('#' + locationSelectId);
    let selectedOpt = $location.find('option:selected');

    $location.find('option').each(function () {
        let opt = $(this);
        if (!opt.val()) return;
        opt.toggle(!stateId || opt.data('state') == stateId);
    });

    // If the currently selected city no longer belongs to the chosen state, clear it
    // so a stale, hidden selection can't be submitted with a mismatched state_id.
    if (stateId && selectedOpt.val() && selectedOpt.data('state') != stateId) {
        $location.val('');
    }
}

$(document).ready(function () {

    let taSearchTimer;
    let taCurrentStatus = '{{ $status }}';

    function taFetchAttractions() {
        let q = $('#taSearch').val();
        showAjaxLoader($('#taTableWrapper'));
        $.get('{{ route("admin.tourist-attractions.index") }}', { search: q, status: taCurrentStatus, ajax: 1 })
            .done(function (res) {
                $('#taTableWrapper').html(res.html);
                if (typeof window.initSwitchery === 'function') window.initSwitchery();
            })
            .fail(function () { hideAjaxLoader($('#taTableWrapper')); toastr.error('Search failed.'); });
    }

    $('#taSearch').on('input', function () {
        clearTimeout(taSearchTimer);
        taSearchTimer = setTimeout(taFetchAttractions, 300);
    });

    $(document).on('click', '.tab-link', function () {
        $('.tab-link').removeClass('active');
        $(this).addClass('active');
        taCurrentStatus = $(this).data('status');
        taFetchAttractions();
    });

    $('#add-ta-state').on('change', function () { taFilterLocations('add-ta-state', 'add-ta-location'); });
    $('#edit-ta-state').on('change', function () { taFilterLocations('edit-ta-state', 'edit-ta-location'); });

    // ── Live slug preview (matches the slug the server will actually save) ──
    $('#add-ta-name').on('input', function () {
        var name = $(this).val().trim();
        $('#add-ta-slug').val(name && window.SlugChecker ? SlugChecker.makeSlug(name + ' tourist-attractions') : '');
    });

    // ── Add Attraction ──────────────────────────────────────────────────
    $('#addTaModal').on('hidden.bs.modal', function () {
        $('#addTaForm')[0].reset();
        $('.name-error').hide().text('');
        $('#add-ta-slug').val('');
        if (typeof window.setMediaPickerValue === 'function') {
            window.setMediaPickerValue('ta_banner_add', '', null);
        }
    });

    $('#addTaForm').on('submit', function (e) {
        e.preventDefault();
        if (!$('#addTaForm input[name=banner_image]').val()) {
            toastr.warning('Please choose a Banner Image.');
            return;
        }
        if (typeof tinymce !== 'undefined') tinymce.triggerSave();
        let btn = $('#add-ta-submit-btn');
        btn.prop('disabled', true);
        $('.name-error').hide().text('');
        let fd = new FormData(this);
        $.ajax({ url: '{{ route("admin.tourist-attractions.store") }}', type: 'POST', data: fd, processData: false, contentType: false })
            .done(function () {
                toastr.success('Attraction created!');
                bootstrap.Modal.getOrCreateInstance(document.getElementById('addTaModal')).hide();
                $('#taSearch').trigger('input');
            })
            .fail(function (xhr) {
                btn.prop('disabled', false);
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    let errs = xhr.responseJSON.errors;
                    if (errs.name) {
                        $('.name-error').text(errs.name[0]).show();
                    } else {
                        toastr.error(window.firstErrorMessage(errs, 'Failed to create attraction.'));
                    }
                } else {
                    toastr.error('Failed to create attraction.');
                }
            });
    });

    // ── Edit Core Info ───────────────────────────────────────────────────
    $(document).on('click', '.btn-ta-edit', function () {
        let updateUrl = $(this).data('update');
        $.get($(this).data('fetch')).done(function (d) {
            $('#edit-ta-name').val(d.name || '').attr('data-slug-exclude', d.id || 0);
            $('#edit-ta-state').val(d.state_id);
            taFilterLocations('edit-ta-state', 'edit-ta-location');
            $('#edit-ta-location').val(d.location_id);
            $('#edit-ta-tagline').val(d.tagline || '');
            $('#edit-ta-banner-alt').val(d.banner_image_alt || '');
            let editShortDesc = d.short_description || '';
            let editShortEditor = tinymce.get('edit-ta-short-desc');
            if (editShortEditor) { editShortEditor.setContent(editShortDesc); } else { $('#edit-ta-short-desc').val(editShortDesc); }
            if (typeof window.setMediaPickerValue === 'function') {
                window.setMediaPickerValue('ta_banner_edit', d.banner_image, d.banner_image ? (s3BaseUrl + d.banner_image) : null);
            }
            $('#taEditForm').attr('data-url', updateUrl);
            bootstrap.Modal.getOrCreateInstance(document.getElementById('taEditModal')).show();
        }).fail(function () { toastr.error('Failed to load attraction.'); });
    });

    $('#taEditForm').on('submit', function (e) {
        e.preventDefault();
        if (typeof tinymce !== 'undefined') tinymce.triggerSave();
        let fd = new FormData(this);
        $.ajax({ url: $(this).attr('data-url'), type: 'POST', data: fd, processData: false, contentType: false })
            .done(function (r) {
                toastr.success(r.message || 'Saved!');
                bootstrap.Modal.getOrCreateInstance(document.getElementById('taEditModal')).hide();
                $('#taSearch').trigger('input');
            }).fail(function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    window.showFormErrors(xhr.responseJSON.errors, { scope: '#taEditForm', fallback: 'Failed to save.' });
                } else {
                    toastr.error('Failed to save.');
                }
            });
    });


    // ── Quick Info ────────────────────────────────────────────────────
    $(document).on('click', '.btn-ta-quick-info', function () {
        let updateUrl = $(this).data('update');
        $.get($(this).data('fetch')).done(function (d) {
            $('#ta-location-text').val(d.location_text || '');
            $('#ta-duration-text').val(d.duration_text || '');
            $('#ta-best-for').val(d.best_for || '');
            $('#ta-best-season').val(d.best_season || '');
            $('#taQuickInfoForm').attr('data-url', updateUrl);
            bootstrap.Modal.getOrCreateInstance(document.getElementById('taQuickInfoModal')).show();
        }).fail(function () { toastr.error('Failed to load.'); });
    });
    $('#taQuickInfoForm').on('submit', function (e) {
        e.preventDefault();
        $.ajax({ url: $(this).attr('data-url'), type: 'PUT', data: $(this).serialize() })
            .done(function (r) { toastr.success(r.message || 'Saved!'); bootstrap.Modal.getOrCreateInstance(document.getElementById('taQuickInfoModal')).hide(); })
            .fail(function () { toastr.error('Failed to save.'); });
    });

    // ── Why Visit + Highlights ────────────────────────────────────────
    $(document).on('click', '.btn-ta-why-visit', function () {
        let updateUrl = $(this).data('update');
        $.get($(this).data('fetch')).done(function (d) {
            $('#ta-wv-title').val(d.why_visit_title || '');
            $('#ta-wv-image-alt').val(d.why_visit_image_alt || '');
            $('#ta-wv-desc').val(d.why_visit_description || '');
            if (typeof window.setMediaPickerValue === 'function') {
                window.setMediaPickerValue('ta_wv_image', d.why_visit_image, d.why_visit_image ? (s3BaseUrl + d.why_visit_image) : null);
            }
            let items = d.highlights || [];
            $('#taHighlightsTable tbody').html(
                items.length ? items.map((h, i) => taHighlightRow(i, h.text)).join('') : taHighlightRow(0, '')
            );
            $('#taWhyVisitForm').attr('data-url', updateUrl);
            bootstrap.Modal.getOrCreateInstance(document.getElementById('taWhyVisitModal')).show();
        }).fail(function () { toastr.error('Failed to load.'); });
    });
    $('#taAddHighlightRow').on('click', function () {
        let idx = $('#taHighlightsTable tbody tr').length;
        $('#taHighlightsTable tbody').append(taHighlightRow(idx, ''));
    });
    $(document).on('click', '#taHighlightsTable .rm-row', function () {
        $(this).closest('tr').remove();
        taReindex('#taHighlightsTable', 'highlights', null);
    });
    $('#taWhyVisitForm').on('submit', function (e) {
        e.preventDefault();
        taReindex('#taHighlightsTable', 'highlights', null);
        let fd = new FormData(this);
        fd.append('_method', 'PUT');
        $.ajax({ url: $(this).attr('data-url'), type: 'POST', data: fd, processData: false, contentType: false })
            .done(function (r) { toastr.success(r.message || 'Saved!'); bootstrap.Modal.getOrCreateInstance(document.getElementById('taWhyVisitModal')).hide(); })
            .fail(function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    window.showFormErrors(xhr.responseJSON.errors, { scope: '#taWhyVisitForm', fallback: 'Failed to save.' });
                } else {
                    toastr.error('Failed to save.');
                }
            });
    });

    // ── Activities (Things To Do) ──────────────────────────────────────
    $(document).on('click', '.btn-ta-activities', function () {
        let updateUrl = $(this).data('update');
        $.get($(this).data('fetch')).done(function (d) {
            let items = d.activities || [];
            $('#taActivitiesTable tbody').html(
                items.length ? items.map((a, i) => taActivityRow(i, a.title, a.description)).join('') : taActivityRow(0, '', '')
            );
            $('#taActivitiesForm').attr('data-url', updateUrl);
            bootstrap.Modal.getOrCreateInstance(document.getElementById('taActivitiesModal')).show();
        }).fail(function () { toastr.error('Failed to load.'); });
    });
    $('#taAddActivityRow').on('click', function () {
        let idx = $('#taActivitiesTable tbody tr').length;
        $('#taActivitiesTable tbody').append(taActivityRow(idx, '', ''));
    });
    $(document).on('click', '#taActivitiesTable .rm-row', function () {
        $(this).closest('tr').remove();
        taReindex('#taActivitiesTable', 'activities', ['title', 'description']);
    });
    $('#taActivitiesForm').on('submit', function (e) {
        e.preventDefault();
        taReindex('#taActivitiesTable', 'activities', ['title', 'description']);
        $.ajax({ url: $(this).attr('data-url'), type: 'PUT', data: $(this).serialize() })
            .done(function (r) { toastr.success(r.message || 'Saved!'); bootstrap.Modal.getOrCreateInstance(document.getElementById('taActivitiesModal')).hide(); })
            .fail(function () { toastr.error('Failed to save.'); });
    });

    // ── Gallery ───────────────────────────────────────────────────────
    function taGalleryItemHtml(img) {
        let alt = (img.image_alt || '').replace(/"/g, '&quot;');
        return '<div class="col-3" id="ta-gallery-item-' + img.id + '">' +
            '<div class="gallery-img-wrap"><img src="' + s3BaseUrl + img.image + '" class="img-fluid w-100" style="height:90px;object-fit:cover;border-radius:6px;">' +
            '<button type="button" class="gallery-remove-btn delete-ta-gallery-image" data-id="' + img.id + '" title="Remove"><i class="fas fa-times"></i></button></div>' +
            '<input type="text" class="form-control form-control-sm mt-1 ta-gallery-alt-input" data-id="' + img.id + '" value="' + alt + '" placeholder="Alt text...">' +
            '</div>';
    }

    $('#taGalleryModal').on('hidden.bs.modal', function () { taResetGalleryPending(); });

    function taResetGalleryPending() {
        if (typeof window.resetMediaGalleryPicker === 'function') {
            window.resetMediaGalleryPicker('ta_gallery_add');
        }
        $('#ta-gallery-save-btn').addClass('d-none');
    }

    $(document).on('click', '.btn-ta-gallery', function () {
        let addUrl = $(this).data('add');
        $.get($(this).data('fetch')).done(function (d) {
            let images = d.gallery_images || [];
            let html = images.map(taGalleryItemHtml).join('');
            $('#ta-gallery-grid').html(html || '<p class="text-muted">No images yet.</p>');
            $('#taGalleryAddForm').attr('data-url', addUrl);
            taResetGalleryPending();
            bootstrap.Modal.getOrCreateInstance(document.getElementById('taGalleryModal')).show();
        }).fail(function () { toastr.error('Failed to load gallery.'); });
    });

    // Show the Save button only once the media-gallery-picker has at least one staged item.
    let $taGalleryGrid = $('.media-gallery-picker[data-field-name="ta_gallery_add"] .mgp-grid');
    function taSyncGallerySaveBtn() {
        $('#ta-gallery-save-btn').toggleClass('d-none', $taGalleryGrid.children('.mgp-item').length === 0);
    }
    if ($taGalleryGrid.length) {
        new MutationObserver(taSyncGallerySaveBtn).observe($taGalleryGrid[0], { childList: true });
    }

    $('#taGalleryAddForm').on('submit', function (e) {
        e.preventDefault();
        if (!$taGalleryGrid.children('.mgp-item').length) return;

        let fd = new FormData(this);
        let $btn = $('#ta-gallery-save-btn').prop('disabled', true);
        $.ajax({ url: $('#taGalleryAddForm').attr('data-url'), type: 'POST', data: fd, processData: false, contentType: false })
            .done(function (r) {
                toastr.success(r.message || 'Image(s) added.');
                $('#ta-gallery-grid p.text-muted').remove();
                (r.images || []).forEach(function (img) {
                    $('#ta-gallery-grid').append(taGalleryItemHtml(img));
                });
                taResetGalleryPending();
            }).fail(function (xhr) {
                toastr.error((xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to add image(s).');
            })
            .always(function () { $btn.prop('disabled', false); });
    });

    $(document).on('click', '.delete-ta-gallery-image', function () {
        let id = $(this).data('id');
        $.ajax({ url: '{{ url("admin/tourist-attractions/gallery") }}/' + id, type: 'DELETE' })
            .done(function () { $('#ta-gallery-item-' + id).remove(); toastr.success('Image removed.'); })
            .fail(function () { toastr.error('Failed to remove image.'); });
    });

    $(document).on('blur', '.ta-gallery-alt-input', function () {
        let $input = $(this), id = $input.data('id'), val = $input.val();
        if (val === $input.data('saved')) return;
        $.ajax({
            url: '{{ url("admin/tourist-attractions/gallery") }}/' + id + '/alt',
            type: 'POST', data: { _token: '{{ csrf_token() }}', image_alt: val },
        }).done(function () { $input.data('saved', val); toastr.success('Alt text saved.'); })
            .fail(function () { toastr.error('Failed to save alt text.'); });
    });

    // ── FAQs ──────────────────────────────────────────────────────────
    $(document).on('click', '.btn-ta-faq', function () {
        let updateUrl = $(this).data('update');
        $.get($(this).data('fetch')).done(function (d) {
            $('#taf-faq-title').val(d.faq_title || '');
            $('#taf-faq-sub-title').val(d.faq_sub_title || '');
            let faqs = d.faqs || [];
            $('#taFaqTable tbody').html(
                faqs.length ? faqs.map((f, i) => taFaqRow(i, f.question, f.answer)).join('') : taFaqRow(0, '', '')
            );
            $('#taFaqForm').attr('data-url', updateUrl);
            bootstrap.Modal.getOrCreateInstance(document.getElementById('taFaqModal')).show();
        }).fail(function () { toastr.error('Failed to load FAQs.'); });
    });
    $('#taAddFaqRow').on('click', function () {
        let idx = $('#taFaqTable tbody tr').length;
        $('#taFaqTable tbody').append(taFaqRow(idx, '', ''));
    });
    $(document).on('click', '#taFaqTable .rm-row', function () {
        $(this).closest('tr').remove();
        taReindex('#taFaqTable', 'faqs', ['question', 'answer']);
    });
    $('#taFaqForm').on('submit', function (e) {
        e.preventDefault();
        taReindex('#taFaqTable', 'faqs', ['question', 'answer']);
        $.ajax({ url: $(this).attr('data-url'), type: 'PUT', data: $(this).serialize() })
            .done(function (r) { toastr.success(r.message || 'Saved!'); bootstrap.Modal.getOrCreateInstance(document.getElementById('taFaqModal')).hide(); })
            .fail(function () { toastr.error('Failed to save FAQs.'); });
    });

    // ── SEO Meta ──────────────────────────────────────────────────────
    $(document).on('click', '.btn-ta-meta', function () {
        let updateUrl = $(this).data('update');
        $.get($(this).data('fetch')).done(function (d) {
            $('#tam-meta-title').val(d.meta_title || '');
            $('#tam-meta-desc').val(d.meta_description || '');
            $('#tam-meta-keywords').val(d.meta_keywords || '');
            $('#tam-h1-heading').val(d.h1_heading || '');
            $('#tam-meta-details').val(d.meta_details || '');
            $('#taMetaForm').attr('data-url', updateUrl);
            bootstrap.Modal.getOrCreateInstance(document.getElementById('taMetaModal')).show();
        }).fail(function () { toastr.error('Failed to load meta.'); });
    });
    $('#taMetaForm').on('submit', function (e) {
        e.preventDefault();
        $.ajax({ url: $(this).attr('data-url'), type: 'PUT', data: $(this).serialize() })
            .done(function (r) { toastr.success(r.message || 'Saved!'); bootstrap.Modal.getOrCreateInstance(document.getElementById('taMetaModal')).hide(); })
            .fail(function () { toastr.error('Failed to save meta.'); });
    });

    // ── Status / Popular toggles ─────────────────────────────────────
    $(document).on('change', '.ta-status', function () {
        $.ajax({ url: $(this).data('url'), type: 'POST', data: { _token: '{{ csrf_token() }}' } })
            .done(function () { toastr.success('Status updated'); })
            .fail(function () { toastr.error('Failed to update status.'); });
    });
    $(document).on('change', '.ta-popular', function () {
        $.ajax({ url: $(this).data('url'), type: 'POST', data: { _token: '{{ csrf_token() }}' } })
            .done(function () { toastr.success('Popular flag updated'); })
            .fail(function () { toastr.error('Failed to update popular flag.'); });
    });

    // ── Delete ────────────────────────────────────────────────────────
    $(document).on('click', '.delete-ta', function () {
        let btn = $(this);
        let url = btn.data('url');
        let row = btn.closest('tr');
        Swal.fire({
            title: 'Are you sure?', text: 'This will delete the attraction and all its content!', icon: 'warning',
            showCancelButton: true, confirmButtonColor: '#e3342f', cancelButtonColor: '#6c757d', confirmButtonText: 'Yes, delete it',
        }).then((result) => {
            if (result.isConfirmed) {
                btn.find('.spinner-border').removeClass('d-none');
                btn.find('.icon').addClass('d-none');
                $.ajax({ url: url, type: 'DELETE', data: { _token: '{{ csrf_token() }}' } })
                    .done(function (res) { if (res.status) { row.remove(); toastr.success('Attraction deleted.'); } })
                    .fail(function () { toastr.error('Delete failed.'); })
                    .always(function () { btn.find('.spinner-border').addClass('d-none'); btn.find('.icon').removeClass('d-none'); });
            }
        });
    });

});
</script>
@endsection
