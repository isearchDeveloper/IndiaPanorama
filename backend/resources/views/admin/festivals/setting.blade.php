@section('title', 'Festival Setting')
@extends('layouts.app')

@push('style')
<style>
    .btn-outline-orange {
        color: #1d4ed8;
        background-color: transparent;
        border: 1px solid #2563eb;
    }
    .btn-outline-orange:hover {
        color: #fff;
        background-color: #2563eb;
        border-color: #2563eb;
    }
    .btn-outline-purple {
        color: #6d28d9;
        background-color: transparent;
        border: 1px solid #7c3aed;
    }
    .btn-outline-purple:hover {
        color: #fff;
        background-color: #7c3aed;
        border-color: #7c3aed;
    }
    .modal-header-orange {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        color: #fff;
    }
    .modal-header-purple {
        background: linear-gradient(135deg, #8b5cf6, #6d28d9);
        color: #fff;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-0 fw-bold"><i class="fas fa-drum me-2 text-primary"></i>Festival Setting</h2>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.festival-state-pages.index') }}" class="btn btn-outline-orange">
                <i class="fas fa-map-marked-alt me-1"></i>Festivals By State
            </a>
            <a href="{{ route('admin.festival.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to Manage Festival
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th width="110">Status</th>
                            <th width="180">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="fw-medium">{{ $setting->title ?: 'Festivals' }}</td>
                            <td>
                                <input type="checkbox"
                                       class="js-switch"
                                       id="festival-setting-status"
                                       {{ $setting->is_active ? 'checked' : '' }}>
                            </td>
                            <td class="text-nowrap">
                                <button class="btn btn-sm btn-outline-orange btn-fs-settings" title="Banner & Settings">
                                    <i class="fas fa-cog"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-info btn-fs-stats" title="Stats">
                                    <i class="fas fa-chart-simple"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-success btn-fs-why-experience" title="Why Experience Festivals">
                                    <i class="fas fa-heart"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-warning btn-fs-faq" title="FAQs">
                                    <i class="fas fa-question-circle"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-purple btn-fs-meta" title="SEO Meta">
                                    <i class="fas fa-globe"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection

@section('modal')
{{-- ══════════════ BANNER & SETTINGS MODAL ══════════════ --}}
<div class="modal fade" id="fsSettingsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header modal-header-orange">
                <h5 class="modal-title"><i class="fas fa-cog me-2"></i>Festivals — Banner & Settings</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="fsSettingsForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="section" value="banner_settings">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" id="fs-title" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Banner Text</label>
                            <textarea name="banner_text" id="fs-banner-text" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="col-md-6">
                            <x-media-picker name="banner_image" picker-id="festival_setting_banner" label="Banner Image" folder="festivals" />
                            <div class="text-danger small mt-1 fs-banner-error" style="display:none"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Banner Image Alt</label>
                            <input type="text" name="banner_image_alt" id="fs-banner-alt" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Short Description</label>
                            <textarea name="short_description" id="fs-short-desc" class="form-control tinymce" rows="4"></textarea>
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

{{-- ══════════════ STATS MODAL ══════════════ --}}
<div class="modal fade" id="fsStatsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-chart-simple me-2"></i>Festivals — Stats</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="fsStatsForm">
                @csrf
                <input type="hidden" name="section" value="why_choose">
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Title</label>
                            <input type="text" name="why_choose_title" id="fs-stats-title" class="form-control" placeholder="Section title (optional)">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Sub Title</label>
                            <textarea name="why_choose_sub_title" id="fs-stats-sub-title" class="form-control" rows="1"></textarea>
                        </div>
                    </div>
                    <table class="table" id="fsStatsTable">
                        <thead><tr><th>Stat</th><th>Label</th><th width="40"></th></tr></thead>
                        <tbody></tbody>
                    </table>
                    <button type="button" class="btn btn-sm btn-outline-success" id="fsAddStatsRow"><i class="fas fa-plus me-1"></i>Add Stat</button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info text-white"><i class="fas fa-save me-1"></i>Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════ WHY EXPERIENCE FESTIVALS MODAL ══════════════ --}}
<div class="modal fade" id="fsWhyExperienceModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-heart me-2"></i>Festivals — Why Experience Festivals</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="fsWhyExperienceForm">
                @csrf
                <input type="hidden" name="section" value="why_experience">
                <div class="row g-3 mb-3 px-3 pt-3">
                    <div class="col-md-6">
                        <label class="form-label">Title</label>
                        <input type="text" name="why_experience_title" id="fs-why-experience-title" class="form-control" placeholder="Why Experience Festivals With Indian Panorama">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Sub Title</label>
                        <textarea name="why_experience_sub_title" id="fs-why-experience-sub-title" class="form-control" rows="1"></textarea>
                    </div>
                </div>
                <div class="modal-body">
                    <table class="table" id="fsWhyExperienceTable">
                        <thead><tr><th>Title</th><th>Tagline</th><th width="40"></th></tr></thead>
                        <tbody></tbody>
                    </table>
                    <button type="button" class="btn btn-sm btn-outline-success" id="fsAddWhyExperienceRow"><i class="fas fa-plus me-1"></i>Add Item</button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success"><i class="fas fa-save me-1"></i>Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════ FAQ MODAL ══════════════ --}}
<div class="modal fade" id="fsFaqModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title"><i class="fas fa-question-circle me-2"></i>Festivals — FAQs</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="fsFaqForm">
                @csrf
                <input type="hidden" name="section" value="faqs">
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Section Title</label>
                            <input type="text" name="faq_title" id="fs-faq-title" class="form-control" placeholder="Frequently Asked Questions">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Section Sub Title</label>
                            <input type="text" name="faq_sub_title" id="fs-faq-sub-title" class="form-control">
                        </div>
                    </div>
                    <table class="table" id="fsFaqTable">
                        <thead><tr><th>Question</th><th>Answer</th><th width="40"></th></tr></thead>
                        <tbody></tbody>
                    </table>
                    <button type="button" class="btn btn-sm btn-outline-success" id="fsAddFaqRow"><i class="fas fa-plus me-1"></i>Add FAQ</button>
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
<div class="modal fade" id="fsMetaModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header modal-header-purple">
                <h5 class="modal-title"><i class="fas fa-globe me-2"></i>Festivals — SEO Meta</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="fsMetaForm">
                @csrf
                <input type="hidden" name="section" value="meta">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Meta Title</label>
                            <input type="text" name="meta_title" id="fs-meta-title" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Meta Description</label>
                            <input type="text" name="meta_description" id="fs-meta-desc" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Meta Keywords</label>
                            <input type="text" name="meta_keywords" id="fs-meta-keywords" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">H1 Heading</label>
                            <input type="text" name="h1_heading" id="fs-h1-heading" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Extra Meta Tags</label>
                            <textarea name="meta_details" id="fs-meta-details" class="form-control" rows="4"></textarea>
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
function fsFaqRow(idx, question, answer) {
    question = (question || '').replace(/"/g, '&quot;');
    return '<tr>' +
        '<td><input type="text" name="faqs[' + idx + '][question]" value="' + question + '" class="form-control" required></td>' +
        '<td><textarea name="faqs[' + idx + '][answer]" class="form-control">' + (answer || '') + '</textarea></td>' +
        '<td><button type="button" class="btn btn-sm btn-outline-danger rm-row"><i class="fas fa-trash"></i></button></td>' +
        '</tr>';
}

function fsReindex(tableSel, prefix, fields) {
    $(tableSel + ' tbody tr').each(function (i) {
        let $row = $(this);
        fields.forEach(function (f) {
            $row.find('[name$="[' + f + ']"]').attr('name', prefix + '[' + i + '][' + f + ']');
        });
    });
}

function fsStatsRow(idx, stat, label) {
    stat  = (stat  || '').replace(/"/g, '&quot;');
    label = (label || '').replace(/"/g, '&quot;');
    return '<tr>' +
        '<td><input type="text" name="highlights[' + idx + '][stat]" value="' + stat + '" class="form-control" placeholder="500+" required></td>' +
        '<td><input type="text" name="highlights[' + idx + '][label]" value="' + label + '" class="form-control" placeholder="Festivals Covered"></td>' +
        '<td><button type="button" class="btn btn-sm btn-outline-danger rm-row"><i class="fas fa-trash"></i></button></td>' +
        '</tr>';
}

function fsWhyExperienceRow(idx, title, tagline) {
    title   = (title   || '').replace(/"/g, '&quot;');
    tagline = (tagline || '').replace(/"/g, '&quot;');
    return '<tr>' +
        '<td><input type="text" name="why_experiences[' + idx + '][title]" value="' + title + '" class="form-control" placeholder="Authentic Cultural Experiences" required></td>' +
        '<td><input type="text" name="why_experiences[' + idx + '][tagline]" value="' + tagline + '" class="form-control" placeholder="Participate in local traditions with genuine cultural interactions"></td>' +
        '<td><button type="button" class="btn btn-sm btn-outline-danger rm-row"><i class="fas fa-trash"></i></button></td>' +
        '</tr>';
}

$(document).ready(function () {

    const fsDataUrl   = '{{ route("admin.festival.setting.data") }}';
    const fsUpdateUrl = '{{ route("admin.festival.setting.update-section") }}';

    // ── Status toggle ──────────────────────────────────────────────────
    $('#festival-setting-status').on('change', function () {
        $.ajax({
            url: '{{ route("admin.festival.setting.toggle-status") }}',
            type: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function () { toastr.success('Status updated'); },
            error: function () { toastr.error('Failed to update status.'); }
        });
    });

    // ── Settings Modal ─────────────────────────────────────────────────
    $('.btn-fs-settings').on('click', function () {
        $.get(fsDataUrl).done(function (d) {
            $('#fs-title').val(d.title || '');
            $('#fs-banner-text').val(d.banner_text || '');
            $('#fs-banner-alt').val(d.banner_image_alt || '');
            let shortDesc = d.short_description || '';
            $('#fs-short-desc').val(shortDesc);
            if (typeof window.setMediaPickerValue === 'function') {
                window.setMediaPickerValue('festival_setting_banner', d.banner_image, d.banner_image ? (s3BaseUrl + d.banner_image) : null);
            }
            $('#fsSettingsModal').one('shown.bs.modal', function () {
                if (typeof tinymce !== 'undefined' && tinymce.get('fs-short-desc')) {
                    tinymce.get('fs-short-desc').setContent(shortDesc);
                }
            });
            bootstrap.Modal.getOrCreateInstance(document.getElementById('fsSettingsModal')).show();
        }).fail(function () { toastr.error('Failed to load settings.'); });
    });

    $('#fsSettingsForm').on('submit', function (e) {
        e.preventDefault();
        if (typeof tinymce !== 'undefined') tinymce.triggerSave();
        let fd = new FormData(this);
        fd.append('_method', 'PUT');
        $.ajax({
            url: fsUpdateUrl, type: 'POST', data: fd,
            processData: false, contentType: false,
        }).done(function (r) {
            toastr.success(r.message || 'Saved!');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('fsSettingsModal')).hide();
            setTimeout(() => location.reload(), 1000);
        }).fail(function (xhr) {
            if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                let errs = xhr.responseJSON.errors;
                $('.fs-banner-error').text(window.firstErrorMessage(errs, 'Validation failed.')).show();
            } else {
                toastr.error('Failed to save settings.');
            }
        });
    });

    // ── Stats Modal ─────────────────────────────────────────────────────
    $('.btn-fs-stats').on('click', function () {
        $.get(fsDataUrl).done(function (d) {
            $('#fs-stats-title').val(d.why_choose_title || '');
            $('#fs-stats-sub-title').val(d.why_choose_sub_title || '');
            let highlights = d.highlights || [];
            $('#fsStatsTable tbody').html(
                highlights.length
                    ? highlights.map((h, i) => fsStatsRow(i, h.stat, h.label)).join('')
                    : fsStatsRow(0, '', '')
            );
            bootstrap.Modal.getOrCreateInstance(document.getElementById('fsStatsModal')).show();
        }).fail(function () { toastr.error('Failed to load Stats.'); });
    });

    $('#fsAddStatsRow').on('click', function () {
        let idx = $('#fsStatsTable tbody tr').length;
        $('#fsStatsTable tbody').append(fsStatsRow(idx, '', ''));
    });

    $('#fsStatsForm').on('submit', function (e) {
        e.preventDefault();
        fsReindex('#fsStatsTable', 'highlights', ['stat', 'label']);
        $.ajax({
            url: fsUpdateUrl, type: 'PUT', data: $(this).serialize(),
        }).done(function (r) {
            toastr.success(r.message || 'Saved!');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('fsStatsModal')).hide();
        }).fail(function () { toastr.error('Failed to save Stats.'); });
    });

    $(document).on('click', '#fsStatsTable .rm-row', function () {
        $(this).closest('tr').remove();
        fsReindex('#fsStatsTable', 'highlights', ['stat', 'label']);
    });

    // ── Why Experience Festivals Modal ──────────────────────────────────
    $('.btn-fs-why-experience').on('click', function () {
        $.get(fsDataUrl).done(function (d) {
            $('#fs-why-experience-title').val(d.why_experience_title || '');
            $('#fs-why-experience-sub-title').val(d.why_experience_sub_title || '');
            let items = d.why_experiences || [];
            $('#fsWhyExperienceTable tbody').html(
                items.length
                    ? items.map((w, i) => fsWhyExperienceRow(i, w.title, w.tagline)).join('')
                    : fsWhyExperienceRow(0, '', '')
            );
            bootstrap.Modal.getOrCreateInstance(document.getElementById('fsWhyExperienceModal')).show();
        }).fail(function () { toastr.error('Failed to load Why Experience Festivals.'); });
    });

    $('#fsAddWhyExperienceRow').on('click', function () {
        let idx = $('#fsWhyExperienceTable tbody tr').length;
        $('#fsWhyExperienceTable tbody').append(fsWhyExperienceRow(idx, '', ''));
    });

    $('#fsWhyExperienceForm').on('submit', function (e) {
        e.preventDefault();
        fsReindex('#fsWhyExperienceTable', 'why_experiences', ['title', 'tagline']);
        $.ajax({
            url: fsUpdateUrl, type: 'PUT', data: $(this).serialize(),
        }).done(function (r) {
            toastr.success(r.message || 'Saved!');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('fsWhyExperienceModal')).hide();
        }).fail(function () { toastr.error('Failed to save Why Experience Festivals.'); });
    });

    $(document).on('click', '#fsWhyExperienceTable .rm-row', function () {
        $(this).closest('tr').remove();
        fsReindex('#fsWhyExperienceTable', 'why_experiences', ['title', 'tagline']);
    });

    // ── FAQ Modal ───────────────────────────────────────────────────────
    $('.btn-fs-faq').on('click', function () {
        $.get(fsDataUrl).done(function (d) {
            $('#fs-faq-title').val(d.faq_title || '');
            $('#fs-faq-sub-title').val(d.faq_sub_title || '');
            let faqs = d.faqs || [];
            $('#fsFaqTable tbody').html(
                faqs.length ? faqs.map((f, i) => fsFaqRow(i, f.question, f.answer)).join('') : fsFaqRow(0, '', '')
            );
            bootstrap.Modal.getOrCreateInstance(document.getElementById('fsFaqModal')).show();
        }).fail(function () { toastr.error('Failed to load FAQs.'); });
    });

    $('#fsAddFaqRow').on('click', function () {
        let idx = $('#fsFaqTable tbody tr').length;
        $('#fsFaqTable tbody').append(fsFaqRow(idx, '', ''));
    });

    $('#fsFaqForm').on('submit', function (e) {
        e.preventDefault();
        fsReindex('#fsFaqTable', 'faqs', ['question', 'answer']);
        $.ajax({
            url: fsUpdateUrl, type: 'PUT', data: $(this).serialize(),
        }).done(function (r) {
            toastr.success(r.message || 'Saved!');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('fsFaqModal')).hide();
        }).fail(function () { toastr.error('Failed to save FAQs.'); });
    });

    $(document).on('click', '#fsFaqTable .rm-row', function () {
        $(this).closest('tr').remove();
        fsReindex('#fsFaqTable', 'faqs', ['question', 'answer']);
    });

    // ── SEO Meta Modal ──────────────────────────────────────────────────
    $('.btn-fs-meta').on('click', function () {
        $.get(fsDataUrl).done(function (d) {
            $('#fs-meta-title').val(d.meta_title || '');
            $('#fs-meta-desc').val(d.meta_description || '');
            $('#fs-meta-keywords').val(d.meta_keywords || '');
            $('#fs-h1-heading').val(d.h1_heading || '');
            $('#fs-meta-details').val(d.meta_details || '');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('fsMetaModal')).show();
        }).fail(function () { toastr.error('Failed to load meta.'); });
    });

    $('#fsMetaForm').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: fsUpdateUrl, type: 'PUT', data: $(this).serialize(),
        }).done(function (r) {
            toastr.success(r.message || 'Saved!');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('fsMetaModal')).hide();
        }).fail(function () { toastr.error('Failed to save meta.'); });
    });

});
</script>
@endsection
