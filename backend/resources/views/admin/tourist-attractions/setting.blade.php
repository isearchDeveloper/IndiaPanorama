@section('title', 'Tourist Attraction Setting')
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
            <h2 class="h4 mb-0 fw-bold"><i class="fas fa-mountain-sun me-2 text-primary"></i>Tourist Attraction Setting</h2>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.tourist-attraction-pages.index') }}" class="btn btn-outline-success">
                <i class="fas fa-map me-1"></i>State / City Pages
            </a>
            <a href="{{ route('admin.tourist-attractions.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to Manage Attraction
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
                            <td class="fw-medium">{{ $setting->title ?: 'Tourist Attractions' }}</td>
                            <td>
                                <input type="checkbox"
                                       class="js-switch"
                                       id="tas-status"
                                       {{ $setting->is_active ? 'checked' : '' }}>
                            </td>
                            <td class="text-nowrap">
                                <button class="btn btn-sm btn-outline-orange btn-tas-settings" title="Banner & Settings">
                                    <i class="fas fa-cog"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-warning btn-tas-faq" title="FAQs">
                                    <i class="fas fa-question-circle"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-purple btn-tas-meta" title="SEO Meta">
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
<div class="modal fade" id="tasSettingsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header modal-header-orange">
                <h5 class="modal-title"><i class="fas fa-cog me-2"></i>Tourist Attractions — Banner & Settings</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="tasSettingsForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="section" value="banner_settings">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" id="tas-title" class="form-control" placeholder="Tourist Attraction">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Banner Text</label>
                            <textarea name="banner_text" id="tas-banner-text" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="col-md-6">
                            <x-media-picker name="banner_image" picker-id="tas_banner" label="Banner Image" folder="tourist-attractions" />
                            <div class="text-danger small mt-1 tas-banner-error" style="display:none"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Banner Image Alt</label>
                            <input type="text" name="banner_image_alt" id="tas-banner-alt" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Short Description</label>
                            <textarea name="short_description" id="tas-short-desc" class="form-control tinymce no-char-limit" rows="4"></textarea>
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

{{-- ══════════════ FAQ MODAL ══════════════ --}}
<div class="modal fade" id="tasFaqModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title"><i class="fas fa-question-circle me-2"></i>Tourist Attractions — FAQs</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="tasFaqForm">
                @csrf
                <input type="hidden" name="section" value="faqs">
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Section Title</label>
                            <input type="text" name="faq_title" id="tas-faq-title" class="form-control" placeholder="Frequently Asked Questions">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Section Sub Title</label>
                            <input type="text" name="faq_sub_title" id="tas-faq-sub-title" class="form-control">
                        </div>
                    </div>
                    <table class="table" id="tasFaqTable">
                        <thead><tr><th>Question</th><th>Answer</th><th width="40"></th></tr></thead>
                        <tbody></tbody>
                    </table>
                    <button type="button" class="btn btn-sm btn-outline-success" id="tasAddFaqRow"><i class="fas fa-plus me-1"></i>Add FAQ</button>
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
<div class="modal fade" id="tasMetaModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header modal-header-purple">
                <h5 class="modal-title"><i class="fas fa-globe me-2"></i>Tourist Attractions — SEO Meta</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="tasMetaForm">
                @csrf
                <input type="hidden" name="section" value="meta">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Meta Title</label>
                            <input type="text" name="meta_title" id="tas-meta-title" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Meta Description</label>
                            <input type="text" name="meta_description" id="tas-meta-desc" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Meta Keywords</label>
                            <input type="text" name="meta_keywords" id="tas-meta-keywords" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">H1 Heading</label>
                            <input type="text" name="h1_heading" id="tas-h1-heading" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Extra Meta Tags</label>
                            <textarea name="meta_details" id="tas-meta-details" class="form-control" rows="4"></textarea>
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
function tasFaqRow(idx, question, answer) {
    question = (question || '').replace(/"/g, '&quot;');
    return '<tr>' +
        '<td><input type="text" name="faqs[' + idx + '][question]" value="' + question + '" class="form-control" required></td>' +
        '<td><textarea name="faqs[' + idx + '][answer]" class="form-control">' + (answer || '') + '</textarea></td>' +
        '<td><button type="button" class="btn btn-sm btn-outline-danger rm-row"><i class="fas fa-trash"></i></button></td>' +
        '</tr>';
}

function tasReindex(tableSel, prefix, fields) {
    $(tableSel + ' tbody tr').each(function (i) {
        let $row = $(this);
        fields.forEach(function (f) {
            $row.find('[name$="[' + f + ']"]').attr('name', prefix + '[' + i + '][' + f + ']');
        });
    });
}

$(document).ready(function () {

    const tasDataUrl   = '{{ route("admin.tourist-attractions.setting.data") }}';
    const tasUpdateUrl = '{{ route("admin.tourist-attractions.setting.update-section") }}';

    $('#tas-status').on('change', function () {
        $.ajax({
            url: '{{ route("admin.tourist-attractions.setting.toggle-status") }}',
            type: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function () { toastr.success('Status updated'); },
            error: function () { toastr.error('Failed to update status.'); }
        });
    });

    // ── Settings Modal ─────────────────────────────────────────────────
    $('.btn-tas-settings').on('click', function () {
        $.get(tasDataUrl).done(function (d) {
            $('#tas-title').val(d.title || '');
            $('#tas-banner-text').val(d.banner_text || '');
            $('#tas-banner-alt').val(d.banner_image_alt || '');
            $('#tas-short-desc').val(d.short_description || '');
            if (typeof window.setMediaPickerValue === 'function') {
                window.setMediaPickerValue('tas_banner', d.banner_image, d.banner_image ? (s3BaseUrl + d.banner_image) : null);
            }
            let modalEl = document.getElementById('tasSettingsModal');
            bootstrap.Modal.getOrCreateInstance(modalEl).show();
            modalEl.addEventListener('shown.bs.modal', function onShown() {
                this.removeEventListener('shown.bs.modal', onShown);
                if (typeof window.initTinyMCEOn === 'function') window.initTinyMCEOn($('#tas-short-desc'));
                setTimeout(() => { tinymce.get('tas-short-desc')?.setContent(d.short_description || ''); }, 100);
            });
        }).fail(function () { toastr.error('Failed to load settings.'); });
    });

    $('#tasSettingsForm').on('submit', function (e) {
        e.preventDefault();
        if (typeof tinymce !== 'undefined') tinymce.triggerSave();
        let fd = new FormData(this);
        fd.append('_method', 'PUT');
        $.ajax({
            url: tasUpdateUrl, type: 'POST', data: fd,
            processData: false, contentType: false,
        }).done(function (r) {
            toastr.success(r.message || 'Saved!');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('tasSettingsModal')).hide();
        }).fail(function (xhr) {
            if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                let errs = xhr.responseJSON.errors;
                $('.tas-banner-error').text(window.firstErrorMessage(errs, 'Validation failed.')).show();
            } else {
                toastr.error('Failed to save settings.');
            }
        });
    });

    // ── FAQ Modal ───────────────────────────────────────────────────────
    $('.btn-tas-faq').on('click', function () {
        $.get(tasDataUrl).done(function (d) {
            $('#tas-faq-title').val(d.faq_title || '');
            $('#tas-faq-sub-title').val(d.faq_sub_title || '');
            let faqs = d.faqs || [];
            $('#tasFaqTable tbody').html(
                faqs.length ? faqs.map((f, i) => tasFaqRow(i, f.question, f.answer)).join('') : tasFaqRow(0, '', '')
            );
            bootstrap.Modal.getOrCreateInstance(document.getElementById('tasFaqModal')).show();
        }).fail(function () { toastr.error('Failed to load FAQs.'); });
    });

    $('#tasAddFaqRow').on('click', function () {
        let idx = $('#tasFaqTable tbody tr').length;
        $('#tasFaqTable tbody').append(tasFaqRow(idx, '', ''));
    });

    $('#tasFaqForm').on('submit', function (e) {
        e.preventDefault();
        tasReindex('#tasFaqTable', 'faqs', ['question', 'answer']);
        $.ajax({ url: tasUpdateUrl, type: 'PUT', data: $(this).serialize() })
            .done(function (r) {
                toastr.success(r.message || 'Saved!');
                bootstrap.Modal.getOrCreateInstance(document.getElementById('tasFaqModal')).hide();
            }).fail(function () { toastr.error('Failed to save FAQs.'); });
    });

    $(document).on('click', '#tasFaqTable .rm-row', function () {
        $(this).closest('tr').remove();
        tasReindex('#tasFaqTable', 'faqs', ['question', 'answer']);
    });

    // ── SEO Meta Modal ──────────────────────────────────────────────────
    $('.btn-tas-meta').on('click', function () {
        $.get(tasDataUrl).done(function (d) {
            $('#tas-meta-title').val(d.meta_title || '');
            $('#tas-meta-desc').val(d.meta_description || '');
            $('#tas-meta-keywords').val(d.meta_keywords || '');
            $('#tas-h1-heading').val(d.h1_heading || '');
            $('#tas-meta-details').val(d.meta_details || '');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('tasMetaModal')).show();
        }).fail(function () { toastr.error('Failed to load meta.'); });
    });

    $('#tasMetaForm').on('submit', function (e) {
        e.preventDefault();
        $.ajax({ url: tasUpdateUrl, type: 'PUT', data: $(this).serialize() })
            .done(function (r) {
                toastr.success(r.message || 'Saved!');
                bootstrap.Modal.getOrCreateInstance(document.getElementById('tasMetaModal')).hide();
            }).fail(function () { toastr.error('Failed to save meta.'); });
    });

});
</script>
@endsection
