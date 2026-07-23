@section('title', 'Holiday Settings')
@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-0 fw-bold"><i class="fas fa-umbrella-beach me-2 text-primary"></i>Holiday Settings</h2>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Holiday Name</th>
                            <th width="200">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($holidays as $i => $holiday)
                        <tr>
                            <td class="fw-medium">{{ $holiday->name }}</td>
                            <td>
                                <button class="btn btn-sm btn-outline-secondary btn-hs-edit"
                                    data-fetch="{{ route('admin.holiday-setting.show', $holiday->id) }}"
                                    data-update="{{ route('admin.holiday-setting.update', $holiday->id) }}"
                                    title="Edit Content">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-primary btn-hs-settings"
                                    data-fetch="{{ route('admin.holiday-setting.show', $holiday->id) }}"
                                    data-update="{{ route('admin.holiday-setting.update', $holiday->id) }}"
                                    title="Settings">
                                    <i class="fas fa-cog"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-warning btn-hs-faq"
                                    data-id="{{ $holiday->id }}"
                                    data-name="{{ $holiday->name }}"
                                    data-faqurl="{{ route('admin.holiday-setting.faq', $holiday->id) }}"
                                    title="FAQs">
                                    <i class="fas fa-question-circle"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-info btn-hs-meta"
                                    data-fetch="{{ route('admin.holiday-setting.show', $holiday->id) }}"
                                    data-update="{{ route('admin.holiday-setting.update', $holiday->id) }}"
                                    title="SEO Meta">
                                    <i class="fas fa-globe"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="2" class="text-center text-muted py-5">No holiday settings found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($holidays->lastPage() > 1)
        <div class="card-footer">
            @include('admin.common.pagination', ['paginator' => $holidays])
        </div>
        @endif
    </div>

</div>
@endsection

@section('modal')

{{-- ══════════════════════════════════════════════════════
     EDIT MODAL — Title, Description, Banner Image, Alt, Short Description
══════════════════════════════════════════════════════ --}}
<div class="modal fade" id="editHolidayModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title" id="editHolidayModalTitle"><i class="fas fa-edit me-2"></i>Edit Holiday Content</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editHolidayForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="edit_modal" value="1">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Title</label>
                            <input type="text" name="banner_title" id="he_banner_title" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="banner_description" id="he_banner_desc" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="col-12">
                            <x-media-picker name="banner_image" picker-id="he_banner_image" label="Banner Image" folder="holiday-setting" />
                        </div>
                        <div class="col-12">
                            <label class="form-label">Banner Image Alt</label>
                            <input type="text" name="banner_image_alt" id="he_banner_alt" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Short Description</label>
                            <textarea name="short_description" id="he_short_desc" class="form-control tinymce" rows="4"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-secondary"><i class="fas fa-save me-1"></i>Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════
     SETTINGS MODAL — Long Description + Popular Tour Packages
══════════════════════════════════════════════════════ --}}
<div class="modal fade" id="holidaySettingsModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="holidaySettingsModalTitle"><i class="fas fa-cog me-2"></i>Holiday Settings</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="holidaySettingsForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="settings_modal" value="1">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Long Description</label>
                            <textarea name="long_description" id="hs_long_desc" class="form-control tinymce" rows="6"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Popular Tour Packages — Title</label>
                            <input type="text" name="popular_packages_heading" id="hs_popular_heading" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Popular Tour Packages — Description</label>
                            <textarea name="popular_packages_description" id="hs_popular_desc" class="form-control" rows="3"></textarea>
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

{{-- ══════════════════════════════════════════════════════
     FAQ MODAL
══════════════════════════════════════════════════════ --}}
<div class="modal fade" id="holidayFaqModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="holidayFaqModalTitle">Holiday FAQs</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="holidayFaqForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body" id="holidayFaqBody"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning"><i class="fas fa-save me-1"></i>Save FAQs</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════
     META MODAL
══════════════════════════════════════════════════════ --}}
<div class="modal fade" id="holidayMetaModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="holidayMetaModalTitle">Meta Info</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="holidayMetaForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="meta_setting" value="1">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Meta Title</label>
                            <input type="text" name="meta_title" id="hm_meta_title" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Meta Description</label>
                            <input type="text" name="meta_description" id="hm_meta_desc" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Meta Keywords</label>
                            <input type="text" name="meta_keywords" id="hm_meta_keywords" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">H1 Heading</label>
                            <input type="text" name="h1_heading" id="hm_h1_heading" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Extra Meta Tag</label>
                            <textarea name="meta_details" id="hm_meta_details" class="form-control" rows="4"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning"><i class="fas fa-save me-1"></i>Update Meta Setting</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
const STORAGE_URL = "{{ storage_base_url() }}";
const hsIndexUrl  = "{{ route('admin.holiday-setting.index') }}";

function showModal(id) {
    bootstrap.Modal.getOrCreateInstance(document.getElementById(id)).show();
}
function hideModal(id) {
    bootstrap.Modal.getOrCreateInstance(document.getElementById(id)).hide();
}

// ── FAQ helpers ──────────────────────────────────────────────────────────────
function faqHtml(faqs, faqTitle, faqImage, faqImageAlt) {
    faqs        = faqs        || [];
    faqTitle    = faqTitle    || '';
    faqImage    = faqImage    || '';
    faqImageAlt = faqImageAlt || '';
    var idx  = 0;
    var rows = faqs.length ? faqs.map(function(f) {
        var q = idx; var a = idx++;
        return '<tr>' +
            '<td><input type="text" name="faqs[' + q + '][question]" value="' + (f.question || '').replace(/"/g, '&quot;') + '" class="form-control" required></td>' +
            '<td><textarea name="faqs[' + a + '][answer]" class="form-control">' + (f.answer || '') + '</textarea></td>' +
            '<td><button type="button" class="btn btn-sm btn-outline-danger rm-faq"><i class="fas fa-trash"></i></button></td>' +
            '</tr>';
    }).join('') :
        '<tr><td><input type="text" name="faqs[0][question]" class="form-control" required></td>' +
        '<td><textarea name="faqs[0][answer]" class="form-control"></textarea></td><td></td></tr>';

    return '<div class="mb-3">' +
        '<label class="form-label">FAQ Section Title <span class="text-danger">*</span></label>' +
        '<input value="' + faqTitle.replace(/"/g, '&quot;') + '" name="faq_title" class="form-control" required>' +
        '</div>' +
        '<div class="row g-3 mb-3">' +
        '<div class="col-md-6">' +
        (typeof window.mediaPickerFieldHtml === 'function' ? window.mediaPickerFieldHtml('faq_image', 'he_faq_image', 'Image', 'holiday-setting/faq') : '') +
        '</div>' +
        '<div class="col-md-6">' +
        '<label class="form-label">Image Alt Tag</label>' +
        '<input type="text" name="faq_image_alt" value="' + faqImageAlt.replace(/"/g, '&quot;') + '" class="form-control">' +
        '</div>' +
        '</div>' +
        '<div class="mb-3">' +
        (typeof window.imageLicenseFieldsHtml === 'function' ? window.imageLicenseFieldsHtml('faq_license', 'FAQ Image License Details') : '') +
        '</div>' +
        '<table class="table" id="faqTable">' +
        '<thead><tr><th>Question</th><th>Answer</th>' +
        '<th><button type="button" class="btn btn-sm btn-outline-success" id="addFaqRow"><i class="fas fa-plus"></i></button></th>' +
        '</tr></thead><tbody>' + rows + '</tbody></table>';
}

function reindexFaq() {
    var i = 0;
    $('#faqTable tbody tr').each(function() {
        $(this).find('input[name*="[question]"]').attr('name',  'faqs[' + i + '][question]');
        $(this).find('textarea[name*="[answer]"]').attr('name', 'faqs[' + i + '][answer]');
        i++;
    });
}

function submitFaqAjax(formId, modalId) {
    reindexFaq();
    var form = document.getElementById(formId);
    var fd   = new FormData(form);
    fd.append('_method', 'PUT');
    $.ajax({
        url: $(form).attr('action'), type: 'POST', data: fd,
        processData: false, contentType: false,
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    }).done(function() {
        toastr.success('FAQs saved!');
        hideModal(modalId);
    }).fail(function(xhr) {
        toastr.error((xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to save FAQs.');
    });
}

$(document).on('click', '#addFaqRow', function() {
    var idx = $('#faqTable tbody tr').length;
    $('#faqTable tbody').append(
        '<tr><td><input type="text" name="faqs[' + idx + '][question]" class="form-control" required></td>' +
        '<td><textarea name="faqs[' + idx + '][answer]" class="form-control"></textarea></td>' +
        '<td><button type="button" class="btn btn-sm btn-outline-danger rm-faq"><i class="fas fa-trash"></i></button></td></tr>'
    );
});
$(document).on('click', '.rm-faq', function() { $(this).closest('tr').remove(); });

// ── EDIT MODAL ───────────────────────────────────────────────────────────────
$(document).on('click', '.btn-hs-edit', function() {
    var updateUrl = $(this).data('update');
    $.get($(this).data('fetch'))
        .done(function(d) {
            $('#editHolidayModalTitle').text(d.name + ' — Edit Content');
            $('#he_banner_title').val((d.details && d.details.banner_title)       ? d.details.banner_title       : '');
            $('#he_banner_desc').val((d.details && d.details.banner_description)  ? d.details.banner_description : '');
            $('#he_banner_alt').val((d.details && d.details.banner_image_alt)     ? d.details.banner_image_alt   : '');
            if (typeof window.setMediaPickerValue === 'function') {
                var bannerImg = (d.details && d.details.banner_image) ? d.details.banner_image : null;
                window.setMediaPickerValue('he_banner_image', bannerImg, bannerImg ? (STORAGE_URL + bannerImg) : null);
            }
            var shortDesc = (d.details && d.details.short_description) ? d.details.short_description : '';
            $('#he_short_desc').val(shortDesc);
            $('#editHolidayModal').one('shown.bs.modal', function() {
                if (typeof tinymce !== 'undefined' && tinymce.get('he_short_desc')) {
                    tinymce.get('he_short_desc').setContent(shortDesc);
                }
            });
            $('#editHolidayForm').attr('action', updateUrl);
            showModal('editHolidayModal');
        })
        .fail(function() { toastr.error('Failed to load content.'); });
});

$('#editHolidayForm').on('submit', function(e) {
    e.preventDefault();
    if (typeof tinymce !== 'undefined') tinymce.triggerSave();
    var fd = new FormData(this);
    fd.append('_method', 'PUT');
    $.ajax({
        url: $(this).attr('action'), type: 'POST', data: fd,
        processData: false, contentType: false,
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    }).done(function(r) {
        toastr.success(r.message || 'Content saved!');
        hideModal('editHolidayModal');
    }).fail(function(xhr) {
        toastr.error((xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to save.');
    });
});

// ── SETTINGS MODAL ───────────────────────────────────────────────────────────
$(document).on('click', '.btn-hs-settings', function() {
    var updateUrl = $(this).data('update');
    $.get($(this).data('fetch'))
        .done(function(d) {
            var longDesc = (d.details && d.details.long_description) ? d.details.long_description : '';
            $('#holidaySettingsModalTitle').text(d.name + ' — Settings');
            $('#hs_long_desc').val(longDesc);
            $('#hs_popular_heading').val((d.details && d.details.popular_packages_heading)     ? d.details.popular_packages_heading     : '');
            $('#hs_popular_desc').val((d.details && d.details.popular_packages_description)    ? d.details.popular_packages_description  : '');
            $('#holidaySettingsModal').one('shown.bs.modal', function() {
                if (typeof tinymce !== 'undefined' && tinymce.get('hs_long_desc')) {
                    tinymce.get('hs_long_desc').setContent(longDesc);
                }
            });
            $('#holidaySettingsForm').attr('action', updateUrl);
            showModal('holidaySettingsModal');
        })
        .fail(function() { toastr.error('Failed to load settings.'); });
});

$('#holidaySettingsForm').on('submit', function(e) {
    e.preventDefault();
    if (typeof tinymce !== 'undefined') tinymce.triggerSave();
    $.ajax({
        url: $(this).attr('action'), type: 'PUT', data: $(this).serialize(),
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    }).done(function(r) {
        toastr.success(r.message || 'Settings saved!');
        hideModal('holidaySettingsModal');
    }).fail(function(xhr) {
        toastr.error((xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to save settings.');
    });
});

// ── FAQ MODAL ────────────────────────────────────────────────────────────────
$(document).on('click', '.btn-hs-faq', function() {
    var id        = $(this).data('id');
    var name      = $(this).data('name');
    var updateUrl = $(this).data('faqurl');
    $('#holidayFaqForm').attr('action', updateUrl);
    $.get(hsIndexUrl, { faqs: true, id: id })
        .done(function(res) {
            var h = res.holiday;
            $('#holidayFaqModalTitle').text(name + ' — FAQs');
            $('#holidayFaqBody').html(faqHtml(h.faqs, h.faq_title, h.faq_image, h.faq_image_alt));
            if (typeof window.setMediaPickerValue === 'function') {
                window.setMediaPickerValue('he_faq_image', h.faq_image || '', h.faq_image ? (STORAGE_URL + h.faq_image) : null);
            }
            if (typeof window.populateImageLicenseBlock === 'function') {
                window.populateImageLicenseBlock('faq_license', h.faq_license);
            }
            showModal('holidayFaqModal');
        })
        .fail(function() { toastr.error('Failed to load FAQs.'); });
});

$('#holidayFaqForm').on('submit', function(e) {
    e.preventDefault();
    submitFaqAjax('holidayFaqForm', 'holidayFaqModal');
});

// ── META MODAL ───────────────────────────────────────────────────────────────
$(document).on('click', '.btn-hs-meta', function() {
    var updateUrl = $(this).data('update');
    $.get($(this).data('fetch'))
        .done(function(d) {
            $('#holidayMetaModalTitle').text('# ' + d.name + ' — Meta Info');
            $('#hm_meta_title').val((d.meta && d.meta.meta_title)       ? d.meta.meta_title       : '');
            $('#hm_meta_desc').val((d.meta && d.meta.meta_description)  ? d.meta.meta_description : '');
            $('#hm_meta_keywords').val((d.meta && d.meta.meta_keywords) ? d.meta.meta_keywords    : '');
            $('#hm_h1_heading').val((d.meta && d.meta.h1_heading)       ? d.meta.h1_heading       : '');
            $('#hm_meta_details').val((d.meta && d.meta.meta_details)   ? d.meta.meta_details     : '');
            $('#holidayMetaForm').attr('action', updateUrl);
            showModal('holidayMetaModal');
        })
        .fail(function() { toastr.error('Failed to load meta settings.'); });
});

$('#holidayMetaForm').on('submit', function(e) {
    e.preventDefault();
    $.ajax({
        url: $(this).attr('action'), type: 'PUT', data: $(this).serialize(),
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    }).done(function(r) {
        toastr.success(r.message || 'Meta saved!');
        hideModal('holidayMetaModal');
    }).fail(function(xhr) {
        toastr.error((xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to save meta.');
    });
});



var _flash = "{{ addslashes(session('success') ?? '') }}";
if (_flash) toastr.success(_flash, 'Success');
</script>
@endsection
