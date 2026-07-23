@section('title', 'Tourist Activity Pages')
@extends('layouts.app')

@push('style')
<style>
    .btn-outline-orange { color: #1d4ed8; background-color: transparent; border: 1px solid #2563eb; }
    .btn-outline-orange:hover { color: #fff; background-color: #2563eb; border-color: #2563eb; }
    .btn-outline-purple { color: #6d28d9; background-color: transparent; border: 1px solid #7c3aed; }
    .btn-outline-purple:hover { color: #fff; background-color: #7c3aed; border-color: #7c3aed; }
    .modal-header-orange { background: linear-gradient(135deg, #2563eb, #1d4ed8); color: #fff; }
    .modal-header-purple { background: linear-gradient(135deg, #8b5cf6, #6d28d9); color: #fff; }
    .badge.bg-orange { background-color: #2563eb !important; color: #fff; }
    .badge.bg-purple { background-color: #7c3aed !important; color: #fff; }
</style>
@endpush

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-0 fw-bold"><i class="fas fa-person-hiking me-2 text-primary"></i>Tourist Activity Pages</h2>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.tourist-activities.setting.index') }}" class="btn btn-outline-purple">
                <i class="fas fa-globe me-2"></i>Root Page Setting
            </a>
            <a href="{{ route('admin.tourist-activities.index') }}" class="btn btn-outline-orange">
                <i class="fas fa-list me-2"></i>Manage Activities
            </a>
        </div>
    </div>

    <div class="mb-3">
        <div class="position-relative" style="max-width:320px;">
            <i class="fas fa-search position-absolute" style="left:12px; top:50%; transform:translateY(-50%); color:#94a3b8; font-size:.85rem;"></i>
            <input type="text" id="tacSearch" class="form-control" style="padding-left:32px;"
                   placeholder="Search state or city..." value="{{ request('search') }}">
        </div>
    </div>

    <div id="tacTableWrapper">
        @include('admin.tourist-activities.pages._table')
    </div>

</div>
@endsection

@section('modal')
{{-- ══════════════ BANNER & SETTINGS MODAL ══════════════ --}}
<div class="modal fade" id="tacSettingsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header modal-header-orange">
                <h5 class="modal-title" id="tacSettingsModalTitle"><i class="fas fa-cog me-2"></i>Banner & Settings</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="tacSettingsForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="section" value="banner_settings">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" id="tps-title" class="form-control" placeholder="e.g. Kerala Tourist Activities">
                        </div>
                        <div class="col-md-6">
                            <x-media-picker name="banner_image" picker-id="tacp_banner" label="Banner Image" folder="tourist-activities/pages" />
                            <div class="text-danger small mt-1 tps-banner-error" style="display:none"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Banner Image Alt</label>
                            <input type="text" name="banner_image_alt" id="tps-banner-alt" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Short Description</label>
                            <textarea name="short_description" id="tps-short-desc" class="form-control tinymce" rows="4"></textarea>
                        </div>
                        <div class="row g-3" id="tps-about-wrap" style="display:none;">
                            <div class="col-md-6">
                                <x-media-picker name="about_image" picker-id="tacp_about" label="About Image" folder="tourist-activities/pages" />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">About Image Alt</label>
                                <input type="text" name="about_image_alt" id="tps-about-alt" class="form-control">
                            </div>
                        </div>
                        <div class="col-12" id="tps-aic-wrap" style="display:none;">
                            <label class="form-label">"Activities in City" Sub Title <small class="text-muted">(city pages only)</small></label>
                            <textarea name="activities_in_city_sub_title" id="tps-aic-subtitle" class="form-control" rows="2" placeholder="e.g. India is home to renowned wildlife destinations such as Ranthambore National Park."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn" style="background:#2563eb;color:#fff;"><i class="fas fa-save me-1"></i>Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════ POPULAR EXPERIENCE MODAL ══════════════ --}}
<div class="modal fade" id="tacExperiencesModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-compass me-2"></i>Popular Experience</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="tacExperiencesForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="section" value="experiences">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Section Title</label>
                        <input type="text" name="experiences_title" id="tac-exp-title" class="form-control" placeholder="e.g. Popular Experience in Kerala">
                    </div>
                    <table class="table align-middle mb-2" id="tacExperiencesTable">
                        <thead><tr><th style="min-width:160px;">Icon</th><th>Title</th><th>Description</th><th width="40"></th></tr></thead>
                        <tbody></tbody>
                    </table>
                    <button type="button" class="btn btn-sm btn-outline-success" id="tacAddExperienceRow"><i class="fas fa-plus me-1"></i>Add Card</button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success"><i class="fas fa-save me-1"></i>Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════ EXPLORE WATERFALLS MODAL ══════════════ --}}
<div class="modal fade" id="tacWaterfallsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-water me-2"></i>Explore Waterfalls</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="tacWaterfallsForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="section" value="waterfalls">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Section Title</label>
                        <input type="text" name="waterfalls_title" id="tac-wf-title" class="form-control" placeholder="e.g. Explore Waterfalls in Munnar">
                    </div>
                    <table class="table align-middle mb-2" id="tacWaterfallsTable">
                        <thead><tr><th style="min-width:160px;">Image</th><th>Label</th><th width="40"></th></tr></thead>
                        <tbody></tbody>
                    </table>
                    <button type="button" class="btn btn-sm btn-outline-success" id="tacAddWaterfallRow"><i class="fas fa-plus me-1"></i>Add Waterfall</button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info"><i class="fas fa-save me-1"></i>Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════ TOP THINGS TO DO MODAL ══════════════ --}}
<div class="modal fade" id="tacThingsToDoModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-list-check me-2"></i>Top Things To Do</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="tacThingsToDoForm">
                @csrf
                <input type="hidden" name="section" value="things_to_do">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Section Title</label>
                        <input type="text" name="things_to_do_title" id="tac-ttd-title" class="form-control" placeholder="e.g. Top Things to Do in Munnar">
                    </div>
                    <table class="table align-middle mb-2" id="tacThingsToDoTable">
                        <thead><tr><th>Title</th><th>Description</th><th width="140">Duration & Timing</th><th width="140">Best For</th><th width="140">Approx. Cost</th><th width="40"></th></tr></thead>
                        <tbody></tbody>
                    </table>
                    <button type="button" class="btn btn-sm btn-outline-success" id="tacAddThingToDoRow"><i class="fas fa-plus me-1"></i>Add Item</button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════ FAQ MODAL ══════════════ --}}
<div class="modal fade" id="tacFaqModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="tacFaqModalTitle"><i class="fas fa-question-circle me-2"></i>FAQs</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="tacFaqForm">
                @csrf
                <input type="hidden" name="section" value="faqs">
                <div class="modal-body">
                    <div class="row g-3 mb-2">
                        <div class="col-md-6">
                            <label class="form-label">Section Title</label>
                            <input type="text" name="faq_title" id="tacf-faq-title" class="form-control" placeholder="Frequently Asked Questions">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Section Sub Title</label>
                            <input type="text" name="faq_sub_title" id="tacf-faq-sub-title" class="form-control">
                        </div>
                    </div>
                    <table class="table" id="tacFaqTable">
                        <thead><tr><th>Question</th><th>Answer</th><th width="40"></th></tr></thead>
                        <tbody></tbody>
                    </table>
                    <button type="button" class="btn btn-sm btn-outline-success" id="tacAddFaqRow"><i class="fas fa-plus me-1"></i>Add FAQ</button>
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
<div class="modal fade" id="tacMetaModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header modal-header-purple">
                <h5 class="modal-title" id="tacMetaModalTitle"><i class="fas fa-globe me-2"></i>SEO Meta</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="tacMetaForm">
                @csrf
                <input type="hidden" name="section" value="meta">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Meta Title</label>
                            <input type="text" name="meta_title" id="tacm-meta-title" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Meta Description</label>
                            <input type="text" name="meta_description" id="tacm-meta-desc" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Meta Keywords</label>
                            <input type="text" name="meta_keywords" id="tacm-meta-keywords" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">H1 Heading</label>
                            <input type="text" name="h1_heading" id="tacm-h1-heading" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Extra Meta Tags</label>
                            <textarea name="meta_details" id="tacm-meta-details" class="form-control" rows="4"></textarea>
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
let tacPageExpPickerSeq = 0;

function tacExperienceRow(title, description, icon) {
    title = (title || '').replace(/"/g, '&quot;');
    const pickerId = 'tac_page_exp_' + (tacPageExpPickerSeq++);
    const pickerHtml = typeof window.mediaPickerFieldHtml === 'function'
        ? window.mediaPickerFieldHtml('page_experience_icons[]', pickerId, '', 'tourist-activities/page-experiences')
        : '';
    const row = `
        <tr>
            <td>${pickerHtml}</td>
            <td><input type="text" name="titles[]" class="form-control" value="${title || ''}" placeholder="e.g. Houseboat Cruises"></td>
            <td>
                <textarea name="descriptions[]" class="form-control">${description || ''}</textarea>
            </td>
            <td class="text-end" style="width:1%;"><button type="button" class="btn btn-sm btn-outline-danger rm-tac-exp-row"><i class="fas fa-trash"></i></button></td>
        </tr>
    `;
    if (icon && typeof window.setMediaPickerValue === 'function') {
        setTimeout(function () { window.setMediaPickerValue(pickerId, icon, s3BaseUrl + icon); }, 0);
    }
    return row;
}

let tacWfPickerSeq = 0;

function tacWaterfallRow(label, image) {
    label = (label || '').replace(/"/g, '&quot;');
    const pickerId = 'tac_wf_' + (tacWfPickerSeq++);
    const pickerHtml = typeof window.mediaPickerFieldHtml === 'function'
        ? window.mediaPickerFieldHtml('waterfall_images[]', pickerId, '', 'tourist-activities/page-waterfalls')
        : '';
    const row = `
        <tr>
            <td>${pickerHtml}</td>
            <td>
                <input type="text" name="labels[]" class="form-control" value="${label || ''}" placeholder="e.g. Athirappilly Falls">
            </td>
            <td class="text-end" style="width:1%;"><button type="button" class="btn btn-sm btn-outline-danger rm-tac-wf-row"><i class="fas fa-trash"></i></button></td>
        </tr>
    `;
    if (image && typeof window.setMediaPickerValue === 'function') {
        setTimeout(function () { window.setMediaPickerValue(pickerId, image, s3BaseUrl + image); }, 0);
    }
    return row;
}

function tacThingToDoRow(idx, title, description, durationTiming, bestFor, approximateCost) {
    title = (title || '').replace(/"/g, '&quot;');
    return '<tr>' +
        '<td><input type="text" name="things_to_do[' + idx + '][title]" value="' + title + '" class="form-control" placeholder="e.g. Tea Plantation Tour"></td>' +
        '<td><textarea name="things_to_do[' + idx + '][description]" class="form-control">' + (description || '') + '</textarea></td>' +
        '<td><input type="text" name="things_to_do[' + idx + '][duration_timing]" value="' + (durationTiming || '') + '" class="form-control" placeholder="e.g. 2-3 hours"></td>' +
        '<td><input type="text" name="things_to_do[' + idx + '][best_for]" value="' + (bestFor || '') + '" class="form-control" placeholder="e.g. Nature lovers"></td>' +
        '<td><input type="text" name="things_to_do[' + idx + '][approximate_cost]" value="' + (approximateCost || '') + '" class="form-control" placeholder="e.g. ₹300-₹800 per person"></td>' +
        '<td><button type="button" class="btn btn-sm btn-outline-danger rm-row"><i class="fas fa-trash"></i></button></td></tr>';
}

function tacFaqRow(idx, question, answer) {
    question = (question || '').replace(/"/g, '&quot;');
    return '<tr>' +
        '<td><input type="text" name="faqs[' + idx + '][question]" value="' + question + '" class="form-control" required></td>' +
        '<td><textarea name="faqs[' + idx + '][answer]" class="form-control">' + (answer || '') + '</textarea></td>' +
        '<td><button type="button" class="btn btn-sm btn-outline-danger rm-row"><i class="fas fa-trash"></i></button></td>' +
        '</tr>';
}

function tacReindex(tableSel, prefix, fields) {
    $(tableSel + ' tbody tr').each(function (i) {
        let $row = $(this);
        fields.forEach(function (f) {
            $row.find('[name$="[' + f + ']"]').attr('name', prefix + '[' + i + '][' + f + ']');
        });
    });
}

function tacShowError(xhr, fallback) {
    if (xhr && xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
        toastr.error(Object.values(xhr.responseJSON.errors)[0][0]);
    } else {
        toastr.error(fallback);
    }
}

$(document).ready(function () {

    let tacSearchTimer;
    $('#tacSearch').on('input', function () {
        clearTimeout(tacSearchTimer);
        let q = $(this).val();
        tacSearchTimer = setTimeout(function () {
            showAjaxLoader($('#tacTableWrapper'));
            $.get('{{ route("admin.tourist-activity-pages.index") }}', { search: q, ajax: 1 })
                .done(function (res) {
                    $('#tacTableWrapper').html(res.html);
                    if (typeof window.initSwitchery === 'function') window.initSwitchery();
                })
                .fail(function () { hideAjaxLoader($('#tacTableWrapper')); toastr.error('Search failed.'); });
        }, 300);
    });

    // ── Settings Modal ──────────────────────────────────────────────────
    $(document).on('click', '.btn-tac-settings', function () {
        let updateUrl = $(this).data('update');
        $.get($(this).data('fetch')).done(function (d) {
            $('#tps-title').val(d.title || '');
            $('#tps-banner-alt').val(d.banner_image_alt || '');
            let shortDesc = d.short_description || '';
            $('#tps-short-desc').val(shortDesc);
            if (typeof window.setMediaPickerValue === 'function') {
                window.setMediaPickerValue('tacp_banner', d.banner_image, d.banner_image ? (s3BaseUrl + d.banner_image) : null);
            }
            $('#tps-about-wrap').toggle(!!d.state_id);
            $('#tps-about-alt').val(d.about_image_alt || '');
            if (typeof window.setMediaPickerValue === 'function') {
                window.setMediaPickerValue('tacp_about', d.about_image, d.about_image ? (s3BaseUrl + d.about_image) : null);
            }
            $('#tps-aic-wrap').toggle(!!d.location_id);
            $('#tps-aic-subtitle').val(d.activities_in_city_sub_title || '');
            $('#tacSettingsForm').attr('data-url', updateUrl);
            $('#tacSettingsModal').one('shown.bs.modal', function () {
                if (typeof tinymce !== 'undefined' && tinymce.get('tps-short-desc')) {
                    tinymce.get('tps-short-desc').setContent(shortDesc);
                }
            });
            bootstrap.Modal.getOrCreateInstance(document.getElementById('tacSettingsModal')).show();
        }).fail(function () { toastr.error('Failed to load settings.'); });
    });

    $('#tacSettingsForm').on('submit', function (e) {
        e.preventDefault();
        if (typeof tinymce !== 'undefined') tinymce.triggerSave();
        let fd = new FormData(this);
        fd.append('_method', 'PUT');
        $.ajax({
            url: $(this).attr('data-url'), type: 'POST', data: fd,
            processData: false, contentType: false,
        }).done(function (r) {
            toastr.success(r.message || 'Saved!');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('tacSettingsModal')).hide();
        }).fail(function (xhr) { tacShowError(xhr, 'Failed to save settings.'); });
    });

    // ── Popular Experience Modal ─────────────────────────────────────────
    $(document).on('click', '.btn-tac-experiences', function () {
        let updateUrl = $(this).data('update');
        $.get($(this).data('fetch')).done(function (d) {
            $('#tac-exp-title').val(d.experiences_title || '');
            let items = d.experiences || [];
            $('#tacExperiencesTable tbody').html(
                items.length ? items.map((e) => tacExperienceRow(e.title, e.description, e.icon)).join('') : tacExperienceRow('', '', '')
            );
            $('#tacExperiencesForm').attr('data-url', updateUrl);
            bootstrap.Modal.getOrCreateInstance(document.getElementById('tacExperiencesModal')).show();
        }).fail(function () { toastr.error('Failed to load.'); });
    });
    $('#tacAddExperienceRow').on('click', function () {
        $('#tacExperiencesTable tbody').append(tacExperienceRow('', '', ''));
    });
    $(document).on('click', '.rm-tac-exp-row', function () {
        $(this).closest('tr').remove();
    });
    $('#tacExperiencesForm').on('submit', function (e) {
        e.preventDefault();
        let fd = new FormData(this);
        fd.append('_method', 'PUT');
        $.ajax({
            url: $(this).attr('data-url'), type: 'POST', data: fd,
            processData: false, contentType: false,
        }).done(function (r) {
            toastr.success(r.message || 'Saved!');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('tacExperiencesModal')).hide();
        }).fail(function (xhr) { tacShowError(xhr, 'Failed to save.'); });
    });

    // ── Explore Waterfalls Modal ──────────────────────────────────────────
    $(document).on('click', '.btn-tac-waterfalls', function () {
        let updateUrl = $(this).data('update');
        $.get($(this).data('fetch')).done(function (d) {
            $('#tac-wf-title').val(d.waterfalls_title || '');
            let items = d.waterfalls || [];
            $('#tacWaterfallsTable tbody').html(
                items.length ? items.map((w) => tacWaterfallRow(w.label, w.image)).join('') : tacWaterfallRow('', '')
            );
            $('#tacWaterfallsForm').attr('data-url', updateUrl);
            bootstrap.Modal.getOrCreateInstance(document.getElementById('tacWaterfallsModal')).show();
        }).fail(function () { toastr.error('Failed to load.'); });
    });
    $('#tacAddWaterfallRow').on('click', function () {
        $('#tacWaterfallsTable tbody').append(tacWaterfallRow('', ''));
    });
    $(document).on('click', '.rm-tac-wf-row', function () {
        $(this).closest('tr').remove();
    });
    $('#tacWaterfallsForm').on('submit', function (e) {
        e.preventDefault();
        let fd = new FormData(this);
        fd.append('_method', 'PUT');
        $.ajax({
            url: $(this).attr('data-url'), type: 'POST', data: fd,
            processData: false, contentType: false,
        }).done(function (r) {
            toastr.success(r.message || 'Saved!');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('tacWaterfallsModal')).hide();
        }).fail(function (xhr) { tacShowError(xhr, 'Failed to save.'); });
    });

    // ── Top Things To Do Modal ────────────────────────────────────────────
    $(document).on('click', '.btn-tac-things-to-do', function () {
        let updateUrl = $(this).data('update');
        $.get($(this).data('fetch')).done(function (d) {
            $('#tac-ttd-title').val(d.things_to_do_title || '');
            let items = d.things_to_do || [];
            $('#tacThingsToDoTable tbody').html(
                items.length ? items.map((t, i) => tacThingToDoRow(i, t.title, t.description, t.duration_timing, t.best_for, t.approximate_cost)).join('') : tacThingToDoRow(0, '', '', '', '', '')
            );
            $('#tacThingsToDoForm').attr('data-url', updateUrl);
            bootstrap.Modal.getOrCreateInstance(document.getElementById('tacThingsToDoModal')).show();
        }).fail(function () { toastr.error('Failed to load.'); });
    });
    $('#tacAddThingToDoRow').on('click', function () {
        let idx = $('#tacThingsToDoTable tbody tr').length;
        $('#tacThingsToDoTable tbody').append(tacThingToDoRow(idx, '', '', '', '', ''));
    });
    $(document).on('click', '#tacThingsToDoTable .rm-row', function () {
        $(this).closest('tr').remove();
        tacReindex('#tacThingsToDoTable', 'things_to_do', ['title', 'description', 'duration_timing', 'best_for', 'approximate_cost']);
    });
    $('#tacThingsToDoForm').on('submit', function (e) {
        e.preventDefault();
        tacReindex('#tacThingsToDoTable', 'things_to_do', ['title', 'description', 'duration_timing', 'best_for', 'approximate_cost']);
        $.ajax({ url: $(this).attr('data-url'), type: 'PUT', data: $(this).serialize() })
            .done(function (r) {
                toastr.success(r.message || 'Saved!');
                bootstrap.Modal.getOrCreateInstance(document.getElementById('tacThingsToDoModal')).hide();
            }).fail(function (xhr) { tacShowError(xhr, 'Failed to save.'); });
    });

    // ── FAQ Modal ────────────────────────────────────────────────────────
    $(document).on('click', '.btn-tac-faq', function () {
        let updateUrl = $(this).data('update');
        $.get($(this).data('fetch')).done(function (d) {
            $('#tacf-faq-title').val(d.faq_title || '');
            $('#tacf-faq-sub-title').val(d.faq_sub_title || '');
            let faqs = d.faqs || [];
            $('#tacFaqTable tbody').html(
                faqs.length ? faqs.map((f, i) => tacFaqRow(i, f.question, f.answer)).join('') : tacFaqRow(0, '', '')
            );
            $('#tacFaqForm').attr('data-url', updateUrl);
            bootstrap.Modal.getOrCreateInstance(document.getElementById('tacFaqModal')).show();
        }).fail(function () { toastr.error('Failed to load FAQs.'); });
    });

    $('#tacAddFaqRow').on('click', function () {
        let idx = $('#tacFaqTable tbody tr').length;
        $('#tacFaqTable tbody').append(tacFaqRow(idx, '', ''));
    });

    $('#tacFaqForm').on('submit', function (e) {
        e.preventDefault();
        tacReindex('#tacFaqTable', 'faqs', ['question', 'answer']);
        $.ajax({ url: $(this).attr('data-url'), type: 'PUT', data: $(this).serialize() })
            .done(function (r) {
                toastr.success(r.message || 'Saved!');
                bootstrap.Modal.getOrCreateInstance(document.getElementById('tacFaqModal')).hide();
            }).fail(function (xhr) { tacShowError(xhr, 'Failed to save FAQs.'); });
    });

    $(document).on('click', '#tacFaqTable .rm-row', function () {
        $(this).closest('tr').remove();
        tacReindex('#tacFaqTable', 'faqs', ['question', 'answer']);
    });

    // ── SEO Meta Modal ───────────────────────────────────────────────────
    $(document).on('click', '.btn-tac-meta', function () {
        let updateUrl = $(this).data('update');
        $.get($(this).data('fetch')).done(function (d) {
            $('#tacm-meta-title').val(d.meta_title || '');
            $('#tacm-meta-desc').val(d.meta_description || '');
            $('#tacm-meta-keywords').val(d.meta_keywords || '');
            $('#tacm-h1-heading').val(d.h1_heading || '');
            $('#tacm-meta-details').val(d.meta_details || '');
            $('#tacMetaForm').attr('data-url', updateUrl);
            bootstrap.Modal.getOrCreateInstance(document.getElementById('tacMetaModal')).show();
        }).fail(function () { toastr.error('Failed to load meta.'); });
    });

    $('#tacMetaForm').on('submit', function (e) {
        e.preventDefault();
        $.ajax({ url: $(this).attr('data-url'), type: 'PUT', data: $(this).serialize() })
            .done(function (r) {
                toastr.success(r.message || 'Saved!');
                bootstrap.Modal.getOrCreateInstance(document.getElementById('tacMetaModal')).hide();
            }).fail(function (xhr) { tacShowError(xhr, 'Failed to save meta.'); });
    });

    $(document).on('change', '.tac-page-status', function () {
        $.ajax({ url: $(this).data('url'), type: 'POST', data: { _token: '{{ csrf_token() }}' } })
            .done(function () { toastr.success('Status updated'); })
            .fail(function () { toastr.error('Failed to update status.'); });
    });

    $(document).on('change', '.tac-page-featured', function () {
        $.ajax({ url: $(this).data('url'), type: 'POST', data: { _token: '{{ csrf_token() }}' } })
            .done(function () { toastr.success('Featured flag updated'); })
            .fail(function () { toastr.error('Failed to update featured flag.'); });
    });

    $(document).on('click', '.delete-tac-page', function () {
        let btn = $(this);
        let url = btn.data('url');
        let row = btn.closest('tr');

        Swal.fire({
            title: 'Are you sure?',
            text: 'This will delete all content for this page!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e3342f',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it',
        }).then((result) => {
            if (result.isConfirmed) {
                btn.find('.spinner-border').removeClass('d-none');
                btn.find('.icon').addClass('d-none');
                $.ajax({ url: url, type: 'DELETE', data: { _token: '{{ csrf_token() }}' } })
                    .done(function (res) {
                        if (res.status) { row.remove(); toastr.success('Page deleted.'); }
                    })
                    .fail(function () { toastr.error('Delete failed.'); })
                    .always(function () {
                        btn.find('.spinner-border').addClass('d-none');
                        btn.find('.icon').removeClass('d-none');
                    });
            }
        });
    });

});
</script>
@endsection
