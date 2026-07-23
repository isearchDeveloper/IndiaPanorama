@section('title', 'Experience Setting')
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
            <h2 class="h4 mb-0 fw-bold"><i class="fas fa-compass me-2 text-primary"></i>Experience Setting</h2>
            <small class="text-muted">Overall Experiences hub page — Banner, FAQs, Meta and more.</small>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.experience-categories.index') }}" class="btn btn-outline-success">
                <i class="fas fa-layer-group me-1"></i>Manage Categories
            </a>
            <a href="{{ route('admin.experience-subcategories.index') }}" class="btn btn-outline-info">
                <i class="fas fa-tags me-1"></i>Manage Subcategories
            </a>
            <a href="{{ route('admin.experiences.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-map-marker-alt me-1"></i>Manage Experiences
            </a>
            <a href="{{ route('admin.experience-pages.index') }}" class="btn btn-outline-dark">
                <i class="fas fa-map me-1"></i>Manage States
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="50">#</th>
                            <th>Name</th>
                            <th width="110">Status</th>
                            <th width="180">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td class="fw-medium">{{ $setting->title ?: 'Experiences' }}</td>
                            <td>
                                <input type="checkbox"
                                       class="js-switch"
                                       id="es-status"
                                       {{ $setting->is_active ? 'checked' : '' }}>
                            </td>
                            <td class="text-nowrap">
                                <button class="btn btn-sm btn-outline-orange btn-es-settings" title="Banner & Settings">
                                    <i class="fas fa-cog"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-success btn-es-best-time" title="Best Time to Visit India">
                                    <i class="fas fa-calendar-alt"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-info btn-es-why-choose" title="Why Choose Indian Panorama?">
                                    <i class="fas fa-thumbs-up"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-warning btn-es-faq" title="FAQs">
                                    <i class="fas fa-question-circle"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-purple btn-es-meta" title="SEO Meta">
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
<div class="modal fade" id="esSettingsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header modal-header-orange">
                <h5 class="modal-title"><i class="fas fa-cog me-2"></i>Experiences — Banner & Settings</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="esSettingsForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="section" value="banner_settings">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" id="es-title" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Banner Text</label>
                            <textarea name="banner_text" id="es-banner-text" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="col-md-6">
                            <x-media-picker name="banner_image" picker-id="es_banner_image" label="Banner Image" folder="experiences" />
                            <div class="text-danger small mt-1 es-banner-error" style="display:none"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Banner Image Alt</label>
                            <input type="text" name="banner_image_alt" id="es-banner-alt" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Short Description</label>
                            <textarea name="short_description" id="es-short-desc" class="form-control tinymce" rows="4"></textarea>
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

{{-- ══════════════ BEST TIME TO VISIT MODAL ══════════════ --}}
<div class="modal fade" id="esBestTimeModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-calendar-alt me-2"></i>Best Time to Visit India</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="esBestTimeForm">
                @csrf
                <input type="hidden" name="section" value="best_time">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Section Title</label>
                        <input type="text" name="best_time_title" id="es-best-time-title" class="form-control" placeholder="Best Time to Visit India">
                    </div>
                    <table class="table" id="esBestTimeTable">
                        <thead><tr><th width="220">Label</th><th>Text</th><th width="40"></th></tr></thead>
                        <tbody></tbody>
                    </table>
                    <button type="button" class="btn btn-sm btn-outline-success" id="esAddBestTimeRow"><i class="fas fa-plus me-1"></i>Add Row</button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success"><i class="fas fa-save me-1"></i>Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════ WHY CHOOSE MODAL ══════════════ --}}
<div class="modal fade" id="esWhyChooseModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-thumbs-up me-2"></i>Why Choose Indian Panorama?</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="esWhyChooseForm">
                @csrf
                <input type="hidden" name="section" value="why_choose">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="why_choose_title" id="es-why-choose-title" class="form-control" placeholder="Why Choose Indian Panorama?">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="why_choose_description" id="es-why-choose-description" class="form-control tinymce" rows="3"></textarea>
                    </div>
                    <table class="table" id="esWhyChooseTable">
                        <thead><tr><th>Label</th><th width="40"></th></tr></thead>
                        <tbody></tbody>
                    </table>
                    <button type="button" class="btn btn-sm btn-outline-success" id="esAddWhyChooseRow"><i class="fas fa-plus me-1"></i>Add Item</button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info text-white"><i class="fas fa-save me-1"></i>Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════ FAQ MODAL ══════════════ --}}
<div class="modal fade" id="esFaqModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title"><i class="fas fa-question-circle me-2"></i>Experiences — FAQs</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="esFaqForm">
                @csrf
                <input type="hidden" name="section" value="faqs">
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Section Title</label>
                            <input type="text" name="faq_title" id="es-faq-title" class="form-control" placeholder="Frequently Asked Questions">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Section Sub Title</label>
                            <input type="text" name="faq_sub_title" id="es-faq-sub-title" class="form-control">
                        </div>
                    </div>
                    <table class="table" id="esFaqTable">
                        <thead><tr><th>Question</th><th>Answer</th><th width="40"></th></tr></thead>
                        <tbody></tbody>
                    </table>
                    <button type="button" class="btn btn-sm btn-outline-success" id="esAddFaqRow"><i class="fas fa-plus me-1"></i>Add FAQ</button>
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
<div class="modal fade" id="esMetaModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header modal-header-purple">
                <h5 class="modal-title"><i class="fas fa-globe me-2"></i>Experiences — SEO Meta</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="esMetaForm">
                @csrf
                <input type="hidden" name="section" value="meta">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Meta Title</label>
                            <input type="text" name="meta_title" id="es-meta-title" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Meta Description</label>
                            <input type="text" name="meta_description" id="es-meta-desc" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Meta Keywords</label>
                            <input type="text" name="meta_keywords" id="es-meta-keywords" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">H1 Heading</label>
                            <input type="text" name="h1_heading" id="es-h1-heading" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Extra Meta Tags</label>
                            <textarea name="meta_details" id="es-meta-details" class="form-control" rows="4"></textarea>
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
function esFaqRow(idx, question, answer) {
    question = (question || '').replace(/"/g, '&quot;');
    return '<tr>' +
        '<td><input type="text" name="faqs[' + idx + '][question]" value="' + question + '" class="form-control" required></td>' +
        '<td><textarea name="faqs[' + idx + '][answer]" class="form-control">' + (answer || '') + '</textarea></td>' +
        '<td><button type="button" class="btn btn-sm btn-outline-danger rm-row"><i class="fas fa-trash"></i></button></td>' +
        '</tr>';
}

function esReindex(tableSel, prefix, fields) {
    $(tableSel + ' tbody tr').each(function (i) {
        let $row = $(this);
        fields.forEach(function (f) {
            $row.find('[name$="[' + f + ']"]').attr('name', prefix + '[' + i + '][' + f + ']');
        });
    });
}

function esBestTimeRow(idx, label, text) {
    label = (label || '').replace(/"/g, '&quot;');
    return '<tr>' +
        '<td><input type="text" name="best_times[' + idx + '][label]" value="' + label + '" class="form-control" placeholder="e.g. September – November"></td>' +
        '<td><textarea name="best_times[' + idx + '][text]" class="form-control" rows="2">' + (text || '') + '</textarea></td>' +
        '<td><button type="button" class="btn btn-sm btn-outline-danger rm-row"><i class="fas fa-trash"></i></button></td>' +
        '</tr>';
}

function esWhyChooseRow(idx, label) {
    label = (label || '').replace(/"/g, '&quot;');
    return '<tr>' +
        '<td><input type="text" name="why_choose_items[' + idx + '][label]" value="' + label + '" class="form-control" placeholder="e.g. 150+ Experiences"></td>' +
        '<td><button type="button" class="btn btn-sm btn-outline-danger rm-row"><i class="fas fa-trash"></i></button></td>' +
        '</tr>';
}

$(document).ready(function () {

    const esDataUrl   = '{{ route("admin.experiences.setting.data") }}';
    const esUpdateUrl = '{{ route("admin.experiences.setting.update-section") }}';

    // ── Status toggle ──────────────────────────────────────────────────
    $('#es-status').on('change', function () {
        $.ajax({
            url: '{{ route("admin.experiences.setting.toggle-status") }}',
            type: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function () { toastr.success('Status updated'); },
            error: function () { toastr.error('Failed to update status.'); }
        });
    });

    // ── Settings Modal ─────────────────────────────────────────────────
    $('.btn-es-settings').on('click', function () {
        $.get(esDataUrl).done(function (d) {
            $('#es-title').val(d.title || '');
            $('#es-banner-text').val(d.banner_text || '');
            $('#es-banner-alt').val(d.banner_image_alt || '');
            let shortDesc = d.short_description || '';
            $('#es-short-desc').val(shortDesc);
            if (typeof window.setMediaPickerValue === 'function') {
                window.setMediaPickerValue('es_banner_image', d.banner_image, d.banner_image ? (s3BaseUrl + d.banner_image) : null);
            }
            $('#esSettingsModal').one('shown.bs.modal', function () {
                if (typeof tinymce !== 'undefined' && tinymce.get('es-short-desc')) {
                    tinymce.get('es-short-desc').setContent(shortDesc);
                }
            });
            bootstrap.Modal.getOrCreateInstance(document.getElementById('esSettingsModal')).show();
        }).fail(function () { toastr.error('Failed to load settings.'); });
    });

    $('#esSettingsForm').on('submit', function (e) {
        e.preventDefault();
        if (typeof tinymce !== 'undefined') tinymce.triggerSave();
        let fd = new FormData(this);
        fd.append('_method', 'PUT');
        $.ajax({
            url: esUpdateUrl, type: 'POST', data: fd,
            processData: false, contentType: false,
        }).done(function (r) {
            toastr.success(r.message || 'Saved!');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('esSettingsModal')).hide();
            setTimeout(() => location.reload(), 1000);
        }).fail(function (xhr) {
            if (xhr.status === 422 && xhr.responseJSON.errors.banner_image) {
                $('.es-banner-error').text(xhr.responseJSON.errors.banner_image[0]).show();
            } else {
                toastr.error('Failed to save settings.');
            }
        });
    });

    // ── Best Time to Visit Modal ─────────────────────────────────────────
    $('.btn-es-best-time').on('click', function () {
        $.get(esDataUrl).done(function (d) {
            $('#es-best-time-title').val(d.best_time_title || '');
            let rows = d.best_times || [];
            $('#esBestTimeTable tbody').html(
                rows.length ? rows.map((r, i) => esBestTimeRow(i, r.label, r.text)).join('') : esBestTimeRow(0, '', '')
            );
            bootstrap.Modal.getOrCreateInstance(document.getElementById('esBestTimeModal')).show();
        }).fail(function () { toastr.error('Failed to load Best Time to Visit.'); });
    });

    $('#esAddBestTimeRow').on('click', function () {
        let idx = $('#esBestTimeTable tbody tr').length;
        $('#esBestTimeTable tbody').append(esBestTimeRow(idx, '', ''));
    });

    $(document).on('click', '#esBestTimeTable .rm-row', function () {
        $(this).closest('tr').remove();
        esReindex('#esBestTimeTable', 'best_times', ['label', 'text']);
    });

    $('#esBestTimeForm').on('submit', function (e) {
        e.preventDefault();
        esReindex('#esBestTimeTable', 'best_times', ['label', 'text']);
        $.ajax({
            url: esUpdateUrl, type: 'PUT', data: $(this).serialize(),
        }).done(function (r) {
            toastr.success(r.message || 'Saved!');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('esBestTimeModal')).hide();
        }).fail(function () { toastr.error('Failed to save Best Time to Visit.'); });
    });

    // ── Why Choose Modal ──────────────────────────────────────────────────
    $('.btn-es-why-choose').on('click', function () {
        $.get(esDataUrl).done(function (d) {
            $('#es-why-choose-title').val(d.why_choose_title || '');
            let whyChooseDescription = d.why_choose_description || '';
            $('#es-why-choose-description').val(whyChooseDescription);
            let rows = d.why_choose_items || [];
            $('#esWhyChooseTable tbody').html(
                rows.length ? rows.map((r, i) => esWhyChooseRow(i, r.label)).join('') : esWhyChooseRow(0, '')
            );
            $('#esWhyChooseModal').one('shown.bs.modal', function () {
                if (typeof tinymce !== 'undefined' && tinymce.get('es-why-choose-description')) {
                    tinymce.get('es-why-choose-description').setContent(whyChooseDescription);
                }
            });
            bootstrap.Modal.getOrCreateInstance(document.getElementById('esWhyChooseModal')).show();
        }).fail(function () { toastr.error('Failed to load Why Choose section.'); });
    });

    $('#esAddWhyChooseRow').on('click', function () {
        let idx = $('#esWhyChooseTable tbody tr').length;
        $('#esWhyChooseTable tbody').append(esWhyChooseRow(idx, ''));
    });

    $(document).on('click', '#esWhyChooseTable .rm-row', function () {
        $(this).closest('tr').remove();
        esReindex('#esWhyChooseTable', 'why_choose_items', ['label']);
    });

    $('#esWhyChooseForm').on('submit', function (e) {
        e.preventDefault();
        if (typeof tinymce !== 'undefined') tinymce.triggerSave();
        esReindex('#esWhyChooseTable', 'why_choose_items', ['label']);
        $.ajax({
            url: esUpdateUrl, type: 'PUT', data: $(this).serialize(),
        }).done(function (r) {
            toastr.success(r.message || 'Saved!');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('esWhyChooseModal')).hide();
        }).fail(function () { toastr.error('Failed to save Why Choose section.'); });
    });

    // ── FAQ Modal ───────────────────────────────────────────────────────
    $('.btn-es-faq').on('click', function () {
        $.get(esDataUrl).done(function (d) {
            $('#es-faq-title').val(d.faq_title || '');
            $('#es-faq-sub-title').val(d.faq_sub_title || '');
            let faqs = d.faqs || [];
            $('#esFaqTable tbody').html(
                faqs.length ? faqs.map((f, i) => esFaqRow(i, f.question, f.answer)).join('') : esFaqRow(0, '', '')
            );
            bootstrap.Modal.getOrCreateInstance(document.getElementById('esFaqModal')).show();
        }).fail(function () { toastr.error('Failed to load FAQs.'); });
    });

    $('#esAddFaqRow').on('click', function () {
        let idx = $('#esFaqTable tbody tr').length;
        $('#esFaqTable tbody').append(esFaqRow(idx, '', ''));
    });

    $('#esFaqForm').on('submit', function (e) {
        e.preventDefault();
        esReindex('#esFaqTable', 'faqs', ['question', 'answer']);
        $.ajax({
            url: esUpdateUrl, type: 'PUT', data: $(this).serialize(),
        }).done(function (r) {
            toastr.success(r.message || 'Saved!');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('esFaqModal')).hide();
        }).fail(function () { toastr.error('Failed to save FAQs.'); });
    });

    $(document).on('click', '#esFaqTable .rm-row', function () {
        $(this).closest('tr').remove();
        esReindex('#esFaqTable', 'faqs', ['question', 'answer']);
    });

    // ── SEO Meta Modal ──────────────────────────────────────────────────
    $('.btn-es-meta').on('click', function () {
        $.get(esDataUrl).done(function (d) {
            $('#es-meta-title').val(d.meta_title || '');
            $('#es-meta-desc').val(d.meta_description || '');
            $('#es-meta-keywords').val(d.meta_keywords || '');
            $('#es-h1-heading').val(d.h1_heading || '');
            $('#es-meta-details').val(d.meta_details || '');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('esMetaModal')).show();
        }).fail(function () { toastr.error('Failed to load meta.'); });
    });

    $('#esMetaForm').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: esUpdateUrl, type: 'PUT', data: $(this).serialize(),
        }).done(function (r) {
            toastr.success(r.message || 'Saved!');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('esMetaModal')).hide();
        }).fail(function () { toastr.error('Failed to save meta.'); });
    });

});
</script>
@endsection
