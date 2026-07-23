@section('title', 'Manage Experience Categories')
@extends('layouts.app')

@push('style')
<style>
    .btn-outline-purple { color: #6d28d9; background-color: transparent; border: 1px solid #7c3aed; }
    .btn-outline-purple:hover { color: #fff; background-color: #7c3aed; border-color: #7c3aed; }
    .modal-header-purple { background: linear-gradient(135deg, #8b5cf6, #6d28d9); color: #fff; }

    /* Perfect For icon picker — small fixed thumbnail, not a full-width banner preview */
    #pfTable .media-picker-block { width: 90px; }
    #pfTable .media-picker-preview img { width: 90px; height: 60px; max-height: none; max-width: none; object-fit: cover; }
    #pfTable .media-picker-choose { width: 90px; font-size: .75rem; padding: 4px 6px; white-space: nowrap; }
</style>
@endpush

@section('content')
<div class="container-fluid">

    <div class="mb-4">
        <h2 class="h4 fw-bold mb-3"><i class="fas fa-compass me-2 text-primary"></i>Manage Experience Categories</h2>
        <div class="d-flex align-items-center justify-content-end gap-2 flex-wrap">
            <a href="{{ route('admin.experiences.setting.index') }}" class="btn btn-outline-purple">
                <i class="fas fa-compass me-2"></i>Experience Setting
            </a>
            <a href="{{ route('admin.experience-pages.index') }}" class="btn btn-outline-dark">
                <i class="fas fa-map me-2"></i>Manage States
            </a>
            <a href="{{ route('admin.experiences.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-map-marker-alt me-2"></i>Manage Experiences
            </a>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                <i class="fas fa-plus me-2"></i>Add Category
            </button>
        </div>
    </div>

    <div class="mb-3">
        <div class="position-relative" style="max-width:320px;">
            <i class="fas fa-search position-absolute" style="left:12px; top:50%; transform:translateY(-50%); color:#94a3b8; font-size:.85rem;"></i>
            <input type="text" id="ecSearch" class="form-control" style="padding-left:32px;"
                   placeholder="Search category..." value="{{ request('search') }}">
        </div>
    </div>

    <div class="tab-list mb-3">
        <ul>
            <li><a href="javascript:void(0);" class="tab-link @if($status == 'all') active @endif" data-status="all">All ({{ $allCount }})</a></li>
            <li><a href="javascript:void(0);" class="tab-link @if($status == 'active') active @endif" data-status="active">Active ({{ $activeCount }})</a></li>
            <li><a href="javascript:void(0);" class="tab-link @if($status == 'inactive') active @endif" data-status="inactive">Inactive ({{ $inactiveCount }})</a></li>
        </ul>
    </div>

    <div id="ecTableWrapper">
        @include('admin.experiences.categories._table')
    </div>

</div>
@endsection

@section('modal')
{{-- ══════════════ ADD CATEGORY MODAL ══════════════ --}}
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Add Category</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="addCategoryForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Category Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <x-media-picker name="image" picker-id="ac_image" label="Banner Image" folder="experiences/categories" />
                        <div class="text-danger small mt-1 add-category-error" style="display:none"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Banner Image Alt</label>
                        <input type="text" name="image_alt" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" id="ac-description" class="form-control tinymce" rows="4"></textarea>
                    </div>
                    <div class="mb-3">
                        <x-media-picker name="intro_image" picker-id="ac_intro_image" label="Intro Image (shown alongside the description on the category page)" folder="experiences/categories" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Add Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════ EDIT CATEGORY MODAL ══════════════ --}}
<div class="modal fade" id="editCategoryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Category</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editCategoryForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" value="PUT">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Category Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="ec-name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <x-media-picker name="image" picker-id="ec_image" label="Banner Image" folder="experiences/categories" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Banner Image Alt</label>
                        <input type="text" name="image_alt" id="ec-image-alt" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" id="ec-description" class="form-control tinymce" rows="4"></textarea>
                    </div>
                    <div class="mb-3">
                        <x-media-picker name="intro_image" picker-id="ec_intro_image" label="Intro Image" folder="experiences/categories" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════ QUICK INFO MODAL ══════════════ --}}
<div class="modal fade" id="categoryQuickInfoModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title"><i class="fas fa-list me-2"></i><span id="qi-modal-title">Category — Quick Info</span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="categoryQuickInfoForm">
                @csrf
                <input type="hidden" name="section" value="quick_info">
                <div class="modal-body">
                    <table class="table" id="qiTable">
                        <thead><tr><th>Label</th><th>Value</th><th width="40"></th></tr></thead>
                        <tbody></tbody>
                    </table>
                    <button type="button" class="btn btn-sm btn-outline-success" id="qiAddRow"><i class="fas fa-plus me-1"></i>Add Row</button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-secondary"><i class="fas fa-save me-1"></i>Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════ PERFECT FOR MODAL ══════════════ --}}
<div class="modal fade" id="categoryPerfectForModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-star me-2"></i><span id="pf-modal-title">Category — Perfect For</span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="categoryPerfectForForm">
                @csrf
                <input type="hidden" name="section" value="perfect_for">
                <div class="modal-body">
                    <table class="table align-middle" id="pfTable">
                        <thead><tr><th style="min-width:110px;">Icon</th><th>Title</th><th>Description</th><th width="40"></th></tr></thead>
                        <tbody></tbody>
                    </table>
                    <button type="button" class="btn btn-sm btn-outline-success" id="pfAddRow"><i class="fas fa-plus me-1"></i>Add Row</button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success"><i class="fas fa-save me-1"></i>Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════ POPULAR CITIES MODAL ══════════════ --}}
<div class="modal fade" id="categoryPopularCitiesModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header text-white" style="background:#2563eb;">
                <h5 class="modal-title"><i class="fas fa-city me-2"></i><span id="pc-modal-title">Category — Popular Cities</span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="categoryPopularCitiesForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="section" value="popular_cities">
                <div class="modal-body">
                    <table class="table align-middle" id="pcTable">
                        <thead><tr><th>Title</th><th>State</th><th>City</th><th>Tag</th><th width="40"></th></tr></thead>
                        <tbody></tbody>
                    </table>
                    <button type="button" class="btn btn-sm btn-outline-success" id="pcAddRow"><i class="fas fa-plus me-1"></i>Add Row</button>
                    <p class="text-muted small mt-2 mb-0">Note: image upload for these cards isn't supported from this quick table yet — set title/description/state/city/tag here. "Tours" count is computed automatically from live Experience items.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn text-white" style="background:#2563eb;"><i class="fas fa-save me-1"></i>Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════ FAQ MODAL ══════════════ --}}
<div class="modal fade" id="categoryFaqModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title"><i class="fas fa-question-circle me-2"></i><span id="cf-modal-title">Category — FAQs</span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="categoryFaqForm">
                @csrf
                <input type="hidden" name="section" value="faqs">
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Section Title</label>
                            <input type="text" name="faq_title" id="cf-title" class="form-control" placeholder="Frequently Asked Questions">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Section Sub Title</label>
                            <input type="text" name="faq_sub_title" id="cf-sub-title" class="form-control">
                        </div>
                    </div>
                    <table class="table" id="categoryFaqTable">
                        <thead><tr><th>Question</th><th>Answer</th><th width="40"></th></tr></thead>
                        <tbody></tbody>
                    </table>
                    <button type="button" class="btn btn-sm btn-outline-success" id="categoryAddFaqRow"><i class="fas fa-plus me-1"></i>Add FAQ</button>
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
<div class="modal fade" id="categoryMetaModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header modal-header-purple">
                <h5 class="modal-title"><i class="fas fa-globe me-2"></i><span id="cm-modal-title">Category — SEO Meta</span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="categoryMetaForm">
                @csrf
                <input type="hidden" name="section" value="meta">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Meta Title</label>
                            <input type="text" name="meta_title" id="cm-meta-title" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Meta Description</label>
                            <input type="text" name="meta_description" id="cm-meta-desc" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Meta Keywords</label>
                            <input type="text" name="meta_keywords" id="cm-meta-keywords" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">H1 Heading</label>
                            <input type="text" name="h1_heading" id="cm-h1-heading" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Extra Meta Tags</label>
                            <textarea name="meta_details" id="cm-meta-details" class="form-control" rows="4"></textarea>
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
let allStatesForCategory = @json($allStates ?? []);
let allLocationsForCategory = @json($allLocations ?? []);

function ecFaqRow(idx, question, answer) {
    question = (question || '').replace(/"/g, '&quot;');
    return '<tr>' +
        '<td><input type="text" name="faqs[' + idx + '][question]" value="' + question + '" class="form-control" required></td>' +
        '<td><textarea name="faqs[' + idx + '][answer]" class="form-control">' + (answer || '') + '</textarea></td>' +
        '<td><button type="button" class="btn btn-sm btn-outline-danger rm-row"><i class="fas fa-trash"></i></button></td>' +
        '</tr>';
}

function qiRow(idx, label, value) {
    return '<tr>' +
        '<td><input type="text" name="quick_info[' + idx + '][label]" value="' + (label || '') + '" class="form-control" placeholder="e.g. Location"></td>' +
        '<td><input type="text" name="quick_info[' + idx + '][value]" value="' + (value || '') + '" class="form-control" placeholder="e.g. India Wide Nature Destinations"></td>' +
        '<td><button type="button" class="btn btn-sm btn-outline-danger rm-row"><i class="fas fa-trash"></i></button></td>' +
        '</tr>';
}

let pfPickerSeq = 0;
function pfRow(idx, title, description, icon) {
    const pickerId = 'pf_icon_' + (pfPickerSeq++);
    const pickerHtml = typeof window.mediaPickerFieldHtml === 'function'
        ? window.mediaPickerFieldHtml('perfect_for[' + idx + '][icon]', pickerId, '', 'experiences/categories/perfect-for')
        : '';
    const row = '<tr>' +
        '<td>' + pickerHtml + '</td>' +
        '<td><input type="text" name="perfect_for[' + idx + '][title]" value="' + (title || '') + '" class="form-control" placeholder="e.g. Wildlife Lovers"></td>' +
        '<td><input type="text" name="perfect_for[' + idx + '][description]" value="' + (description || '') + '" class="form-control"></td>' +
        '<td><button type="button" class="btn btn-sm btn-outline-danger rm-row"><i class="fas fa-trash"></i></button></td>' +
        '</tr>';
    if (icon && typeof window.setMediaPickerValue === 'function') {
        setTimeout(function () { window.setMediaPickerValue(pickerId, icon, s3BaseUrl + icon); }, 0);
    }
    return row;
}

function pcStateOptions(selectedId) {
    return allStatesForCategory.map(s => '<option value="' + s.id + '"' + (s.id == selectedId ? ' selected' : '') + '>' + s.name + '</option>').join('');
}
function pcCityOptions(stateId, selectedId) {
    return allLocationsForCategory.filter(l => l.state_id == stateId)
        .map(l => '<option value="' + l.id + '"' + (l.id == selectedId ? ' selected' : '') + '>' + l.name + '</option>').join('');
}
function pcRow(idx, row) {
    row = row || {};
    return '<tr>' +
        '<td><input type="text" name="popular_cities[' + idx + '][title]" value="' + (row.title || '') + '" class="form-control" placeholder="e.g. Munnar Hill Experiences"></td>' +
        '<td><select name="popular_cities[' + idx + '][state_id]" class="form-select pc-state-select">' +
            '<option value="">Select</option>' + pcStateOptions(row.state_id) +
        '</select></td>' +
        '<td><select name="popular_cities[' + idx + '][location_id]" class="form-select pc-city-select">' +
            '<option value="">Select</option>' + pcCityOptions(row.state_id, row.location_id) +
        '</select></td>' +
        '<td><input type="text" name="popular_cities[' + idx + '][popular_tag]" value="' + (row.popular_tag || '') + '" class="form-control" placeholder="e.g. 12 Tours"></td>' +
        '<td><button type="button" class="btn btn-sm btn-outline-danger rm-row"><i class="fas fa-trash"></i></button></td>' +
        '</tr>';
}

function ecReindex(tableSel, prefix, fields) {
    $(tableSel + ' tbody tr').each(function (i) {
        let $row = $(this);
        fields.forEach(function (f) {
            $row.find('[name$="[' + f + ']"]').attr('name', prefix + '[' + i + '][' + f + ']');
        });
    });
}

$(document).ready(function () {

    // ── Search (live) + status tabs ─────────────────────────────────────────
    let ecSearchTimer;
    let ecCurrentStatus = '{{ $status }}';

    function ecFetchCategories() {
        let q = $('#ecSearch').val();
        showAjaxLoader($('#ecTableWrapper'));
        $.get('{{ route("admin.experience-categories.index") }}', { search: q, status: ecCurrentStatus, ajax: 1 })
            .done(function (res) {
                $('#ecTableWrapper').html(res.html);
                if (typeof window.initSwitchery === 'function') window.initSwitchery();
            })
            .fail(function () { hideAjaxLoader($('#ecTableWrapper')); toastr.error('Search failed.'); });
    }

    $('#ecSearch').on('input', function () {
        clearTimeout(ecSearchTimer);
        ecSearchTimer = setTimeout(ecFetchCategories, 300);
    });

    $(document).on('click', '.tab-link', function () {
        $('.tab-link').removeClass('active');
        $(this).addClass('active');
        ecCurrentStatus = $(this).data('status');
        ecFetchCategories();
    });

    // ── Add Category ────────────────────────────────────────────────────────
    $('#addCategoryForm').on('submit', function (e) {
        e.preventDefault();
        $('.add-category-error').hide().text('');
        if (typeof tinymce !== 'undefined') tinymce.triggerSave();
        let fd = new FormData(this);
        $.ajax({
            url: '{{ route("admin.experience-categories.store") }}', type: 'POST', data: fd,
            processData: false, contentType: false,
        }).done(function (r) {
            toastr.success(r.message || 'Category added.');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('addCategoryModal')).hide();
            document.getElementById('addCategoryForm').reset();
            if (typeof window.setMediaPickerValue === 'function') {
                window.setMediaPickerValue('ac_image', '', null);
                window.setMediaPickerValue('ac_intro_image', '', null);
            }
            $('#ecSearch').trigger('input');
        }).fail(function (xhr) {
            if (xhr.status === 422 && xhr.responseJSON.errors) {
                $('.add-category-error').text(Object.values(xhr.responseJSON.errors)[0][0]).show();
            } else {
                toastr.error('Failed to add category.');
            }
        });
    });

    // ── Edit Category ───────────────────────────────────────────────────────
    $(document).on('click', '.btn-edit-category', function () {
        let id = $(this).data('id');
        $.get('{{ url("admin/experience-categories") }}/' + id).done(function (d) {
            $('#editCategoryForm').attr('data-id', d.id);
            $('#ec-name').val(d.name || '');
            $('#ec-image-alt').val(d.image_alt || '');
            let description = d.description || '';
            $('#ec-description').val(description);
            if (typeof window.setMediaPickerValue === 'function') {
                window.setMediaPickerValue('ec_image', d.image, d.image ? (s3BaseUrl + d.image) : null);
                window.setMediaPickerValue('ec_intro_image', d.intro_image, d.intro_image ? (s3BaseUrl + d.intro_image) : null);
            }
            $('#editCategoryModal').one('shown.bs.modal', function () {
                if (typeof tinymce !== 'undefined' && tinymce.get('ec-description')) {
                    tinymce.get('ec-description').setContent(description);
                }
            });
            bootstrap.Modal.getOrCreateInstance(document.getElementById('editCategoryModal')).show();
        }).fail(function () { toastr.error('Failed to load category.'); });
    });

    $('#editCategoryForm').on('submit', function (e) {
        e.preventDefault();
        if (typeof tinymce !== 'undefined') tinymce.triggerSave();
        let id = $(this).attr('data-id');
        let fd = new FormData(this);
        fd.append('_method', 'PUT');
        $.ajax({
            url: '{{ url("admin/experience-categories") }}/' + id, type: 'POST', data: fd,
            processData: false, contentType: false,
        }).done(function (r) {
            toastr.success(r.message || 'Category updated.');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('editCategoryModal')).hide();
            $('#ecSearch').trigger('input');
        }).fail(function () { toastr.error('Failed to update category.'); });
    });

    // ── Quick Info Modal ─────────────────────────────────────────────────────
    $(document).on('click', '.btn-category-quick-info', function () {
        let id = $(this).data('id');
        $.get('{{ url("admin/experience-categories") }}/' + id).done(function (d) {
            $('#categoryQuickInfoForm').attr('data-id', id);
            $('#qi-modal-title').text(d.name + ' — Quick Info');
            let rows = d.quick_infos || [];
            $('#qiTable tbody').html(rows.length ? rows.map((r, i) => qiRow(i, r.label, r.value)).join('') : qiRow(0, '', ''));
            bootstrap.Modal.getOrCreateInstance(document.getElementById('categoryQuickInfoModal')).show();
        }).fail(function () { toastr.error('Failed to load quick info.'); });
    });

    $('#qiAddRow').on('click', function () {
        let idx = $('#qiTable tbody tr').length;
        $('#qiTable tbody').append(qiRow(idx, '', ''));
    });

    $(document).on('click', '#qiTable .rm-row', function () {
        $(this).closest('tr').remove();
        ecReindex('#qiTable', 'quick_info', ['label', 'value']);
    });

    $('#categoryQuickInfoForm').on('submit', function (e) {
        e.preventDefault();
        ecReindex('#qiTable', 'quick_info', ['label', 'value']);
        let id = $(this).attr('data-id');
        $.ajax({
            url: '{{ url("admin/experience-categories") }}/' + id + '/section', type: 'PUT', data: $(this).serialize(),
        }).done(function (r) {
            toastr.success(r.message || 'Saved!');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('categoryQuickInfoModal')).hide();
        }).fail(function () { toastr.error('Failed to save quick info.'); });
    });

    // ── Perfect For Modal ────────────────────────────────────────────────────
    $(document).on('click', '.btn-category-perfect-for', function () {
        let id = $(this).data('id');
        $.get('{{ url("admin/experience-categories") }}/' + id).done(function (d) {
            $('#categoryPerfectForForm').attr('data-id', id);
            $('#pf-modal-title').text(d.name + ' — Perfect For');
            let rows = d.perfect_fors || [];
            $('#pfTable tbody').html(rows.length ? rows.map((r, i) => pfRow(i, r.title, r.description, r.icon)).join('') : pfRow(0, '', '', ''));
            bootstrap.Modal.getOrCreateInstance(document.getElementById('categoryPerfectForModal')).show();
        }).fail(function () { toastr.error('Failed to load perfect for.'); });
    });

    $('#pfAddRow').on('click', function () {
        let idx = $('#pfTable tbody tr').length;
        $('#pfTable tbody').append(pfRow(idx, '', '', ''));
    });

    $(document).on('click', '#pfTable .rm-row', function () {
        $(this).closest('tr').remove();
        ecReindex('#pfTable', 'perfect_for', ['icon', 'title', 'description']);
    });

    $('#categoryPerfectForForm').on('submit', function (e) {
        e.preventDefault();
        ecReindex('#pfTable', 'perfect_for', ['icon', 'title', 'description']);
        let id = $(this).attr('data-id');
        $.ajax({
            url: '{{ url("admin/experience-categories") }}/' + id + '/section', type: 'PUT', data: $(this).serialize(),
        }).done(function (r) {
            toastr.success(r.message || 'Saved!');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('categoryPerfectForModal')).hide();
        }).fail(function () { toastr.error('Failed to save perfect for.'); });
    });

    // ── Popular Cities Modal ─────────────────────────────────────────────────
    $(document).on('click', '.btn-category-popular-cities', function () {
        let id = $(this).data('id');
        $.get('{{ url("admin/experience-categories") }}/' + id).done(function (d) {
            $('#categoryPopularCitiesForm').attr('data-id', id);
            $('#pc-modal-title').text(d.name + ' — Popular Cities');
            let rows = d.popular_cities || [];
            $('#pcTable tbody').html(rows.length ? rows.map((r, i) => pcRow(i, r)).join('') : pcRow(0, {}));
            bootstrap.Modal.getOrCreateInstance(document.getElementById('categoryPopularCitiesModal')).show();
        }).fail(function () { toastr.error('Failed to load popular cities.'); });
    });

    $('#pcAddRow').on('click', function () {
        let idx = $('#pcTable tbody tr').length;
        $('#pcTable tbody').append(pcRow(idx, {}));
    });

    $(document).on('change', '.pc-state-select', function () {
        let $city = $(this).closest('tr').find('.pc-city-select');
        $city.html('<option value="">Select</option>' + pcCityOptions($(this).val(), null));
    });

    $(document).on('click', '#pcTable .rm-row', function () {
        $(this).closest('tr').remove();
        ecReindex('#pcTable', 'popular_cities', ['title', 'state_id', 'location_id', 'popular_tag']);
    });

    $('#categoryPopularCitiesForm').on('submit', function (e) {
        e.preventDefault();
        ecReindex('#pcTable', 'popular_cities', ['title', 'state_id', 'location_id', 'popular_tag']);
        let id = $(this).attr('data-id');
        let fd = new FormData(this);
        fd.append('_method', 'PUT');
        $.ajax({
            url: '{{ url("admin/experience-categories") }}/' + id + '/section', type: 'POST', data: fd,
            processData: false, contentType: false,
        }).done(function (r) {
            toastr.success(r.message || 'Saved!');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('categoryPopularCitiesModal')).hide();
        }).fail(function () { toastr.error('Failed to save popular cities.'); });
    });

    // ── FAQ Modal ────────────────────────────────────────────────────────────
    $(document).on('click', '.btn-category-faq', function () {
        let id = $(this).data('id');
        $.get('{{ url("admin/experience-categories") }}/' + id).done(function (d) {
            $('#categoryFaqForm').attr('data-id', id);
            $('#cf-modal-title').text(d.name + ' — FAQs');
            $('#cf-title').val(d.faq_title || '');
            $('#cf-sub-title').val(d.faq_sub_title || '');
            let faqs = d.faqs || [];
            $('#categoryFaqTable tbody').html(faqs.length ? faqs.map((f, i) => ecFaqRow(i, f.question, f.answer)).join('') : ecFaqRow(0, '', ''));
            bootstrap.Modal.getOrCreateInstance(document.getElementById('categoryFaqModal')).show();
        }).fail(function () { toastr.error('Failed to load FAQs.'); });
    });

    $('#categoryAddFaqRow').on('click', function () {
        let idx = $('#categoryFaqTable tbody tr').length;
        $('#categoryFaqTable tbody').append(ecFaqRow(idx, '', ''));
    });

    $(document).on('click', '#categoryFaqTable .rm-row', function () {
        $(this).closest('tr').remove();
        ecReindex('#categoryFaqTable', 'faqs', ['question', 'answer']);
    });

    $('#categoryFaqForm').on('submit', function (e) {
        e.preventDefault();
        ecReindex('#categoryFaqTable', 'faqs', ['question', 'answer']);
        let id = $(this).attr('data-id');
        $.ajax({
            url: '{{ url("admin/experience-categories") }}/' + id + '/section', type: 'PUT', data: $(this).serialize(),
        }).done(function (r) {
            toastr.success(r.message || 'Saved!');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('categoryFaqModal')).hide();
        }).fail(function () { toastr.error('Failed to save FAQs.'); });
    });

    // ── SEO Meta Modal ───────────────────────────────────────────────────────
    $(document).on('click', '.btn-category-meta', function () {
        let id = $(this).data('id');
        $.get('{{ url("admin/experience-categories") }}/' + id).done(function (d) {
            $('#categoryMetaForm').attr('data-id', id);
            $('#cm-modal-title').text(d.name + ' — SEO Meta');
            $('#cm-meta-title').val(d.meta_title || '');
            $('#cm-meta-desc').val(d.meta_description || '');
            $('#cm-meta-keywords').val(d.meta_keywords || '');
            $('#cm-h1-heading').val(d.h1_heading || '');
            $('#cm-meta-details').val(d.meta_details || '');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('categoryMetaModal')).show();
        }).fail(function () { toastr.error('Failed to load meta.'); });
    });

    $('#categoryMetaForm').on('submit', function (e) {
        e.preventDefault();
        let id = $(this).attr('data-id');
        $.ajax({
            url: '{{ url("admin/experience-categories") }}/' + id + '/section', type: 'PUT', data: $(this).serialize(),
        }).done(function (r) {
            toastr.success(r.message || 'Saved!');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('categoryMetaModal')).hide();
        }).fail(function () { toastr.error('Failed to save meta.'); });
    });

    // ── Status toggle ────────────────────────────────────────────────────────
    $(document).on('change', '.category-status', function () {
        $.ajax({
            url: $(this).data('url'), type: 'POST', data: { _token: '{{ csrf_token() }}' },
            success: function () { toastr.success('Status updated'); },
            error: function () { toastr.error('Failed to update status.'); }
        });
    });

    // ── Delete ───────────────────────────────────────────────────────────────
    $(document).on('click', '.delete-category', function () {
        let btn = $(this);
        let row = btn.closest('tr');
        Swal.fire({
            title: 'Are you sure?',
            text: 'This will permanently delete this category and all its subcategories and experiences!',
            icon: 'warning', showCancelButton: true,
            confirmButtonColor: '#e3342f', cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it',
        }).then((result) => {
            if (result.isConfirmed) {
                btn.find('.spinner-border').removeClass('d-none');
                btn.find('.icon').addClass('d-none');
                $.ajax({
                    url: btn.data('url'), type: 'DELETE', data: { _token: '{{ csrf_token() }}' },
                    success: function (res) {
                        if (res.status) { row.remove(); toastr.success('Category deleted.'); }
                    },
                    error: function () {
                        toastr.error('Failed to delete category.');
                        btn.find('.spinner-border').addClass('d-none');
                        btn.find('.icon').removeClass('d-none');
                    }
                });
            }
        });
    });

});
</script>
@endsection
