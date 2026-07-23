@section('title', 'Festivals By State')
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
            <h2 class="h4 mb-0 fw-bold"><i class="fas fa-map-marked-alt me-2 text-primary"></i>Festivals By State</h2>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.festival.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Manage Festival
            </a>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addFspModal">
                <i class="fas fa-plus me-2"></i>Add State Page
            </button>
        </div>
    </div>

    <div class="mb-3">
        <div class="position-relative" style="max-width:320px;">
            <i class="fas fa-search position-absolute" style="left:12px; top:50%; transform:translateY(-50%); color:#94a3b8; font-size:.85rem;"></i>
            <input type="text" id="fspSearch" class="form-control" style="padding-left:32px;"
                   placeholder="Search state..." value="{{ request('search') }}">
        </div>
    </div>

    <div id="fspTableWrapper">
        @include('admin.festivals.by-state._table')
    </div>

</div>
@endsection

@section('modal')
{{-- ══════════════ ADD STATE PAGE MODAL ══════════════ --}}
<div class="modal fade" id="addFspModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Add Festival State Page</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="addFspForm">
                @csrf
                <div class="modal-body">
                    <label class="form-label">Select State <span class="text-danger">*</span></label>
                    <select name="state_id" id="fsp-state-select" class="form-select">
                        <option value="">— Select State —</option>
                        @foreach($availableStates as $state)
                        <option value="{{ $state->id }}">{{ $state->name }}</option>
                        @endforeach
                    </select>
                    @if($availableStates->isEmpty())
                    <small class="text-muted d-block mt-2">All states already have a festival page.</small>
                    @endif
                    <div class="text-danger small mt-1 fsp-state-error" style="display:none"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="add-fsp-submit-btn">
                        <i class="fas fa-save me-1"></i>Create
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════ BANNER & SETTINGS MODAL ══════════════ --}}
<div class="modal fade" id="fspSettingsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header modal-header-orange">
                <h5 class="modal-title"><i class="fas fa-cog me-2"></i>Banner & Settings</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="fspSettingsForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="section" value="banner_settings">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Title <small class="text-muted">e.g. "Festivals of Rajasthan"</small></label>
                            <input type="text" name="title" id="fsps-title" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Banner Text <small class="text-muted">(hero subtitle)</small></label>
                            <textarea name="banner_text" id="fsps-banner-text" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-md-6">
                            <x-media-picker name="banner_image" picker-id="festival_state_page_banner" label="Banner Image" folder="festivals/state-pages" />
                            <div class="text-danger small mt-1 fsps-banner-error" style="display:none"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Banner Image Alt</label>
                            <input type="text" name="banner_image_alt" id="fsps-banner-alt" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Short Description <small class="text-muted">("Discover {State}'s Cultural Celebrations" paragraph)</small></label>
                            <textarea name="short_description" id="fsps-short-desc" class="form-control tinymce no-char-limit" rows="4"></textarea>
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

{{-- ══════════════ FEATURED FESTIVAL MODAL ══════════════ --}}
<div class="modal fade" id="fspFeaturedModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-star me-2"></i>Featured Festival</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="fspFeaturedForm">
                @csrf
                <input type="hidden" name="section" value="featured">
                <div class="modal-body">
                    <label class="form-label">Festival</label>
                    <select name="featured_festival_id" id="fsp-featured-select" class="form-select">
                        <option value="">— None —</option>
                    </select>
                    <small class="text-muted d-block mt-2">Only festivals belonging to this state can be featured. Add festivals for this state first under "Manage Festival" if the list is empty.</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════ WHY VISIT MODAL ══════════════ --}}
<div class="modal fade" id="fspWhyVisitModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-circle-check me-2"></i>Why Visit During Festivals</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="fspWhyVisitForm">
                @csrf
                <input type="hidden" name="section" value="why_visit">
                <div class="row g-3 mb-3 px-3 pt-3">
                    <div class="col-md-6">
                        <label class="form-label">Title</label>
                        <input type="text" name="why_visit_title" id="fsp-wv-title" class="form-control" placeholder="Why Visit Rajasthan During Festivals?">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Sub Title</label>
                        <textarea name="why_visit_sub_title" id="fsp-wv-sub-title" class="form-control" rows="1"></textarea>
                    </div>
                </div>
                <div class="modal-body">
                    <table class="table" id="fspWhyVisitTable">
                        <thead><tr><th>Title</th><th>Description</th><th width="40"></th></tr></thead>
                        <tbody></tbody>
                    </table>
                    <button type="button" class="btn btn-sm btn-outline-success" id="fspAddWhyVisitRow"><i class="fas fa-plus me-1"></i>Add Item</button>
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
<div class="modal fade" id="fspFaqModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title"><i class="fas fa-question-circle me-2"></i>FAQs</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="fspFaqForm">
                @csrf
                <input type="hidden" name="section" value="faqs">
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Section Title</label>
                            <input type="text" name="faq_title" id="fspf-faq-title" class="form-control" placeholder="FAQ's">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Section Sub Title</label>
                            <input type="text" name="faq_sub_title" id="fspf-faq-sub-title" class="form-control">
                        </div>
                    </div>
                    <table class="table" id="fspFaqTable">
                        <thead><tr><th>Question</th><th>Answer</th><th width="40"></th></tr></thead>
                        <tbody></tbody>
                    </table>
                    <button type="button" class="btn btn-sm btn-outline-success" id="fspAddFaqRow"><i class="fas fa-plus me-1"></i>Add FAQ</button>
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
<div class="modal fade" id="fspMetaModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header modal-header-purple">
                <h5 class="modal-title"><i class="fas fa-globe me-2"></i>SEO Meta</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="fspMetaForm">
                @csrf
                <input type="hidden" name="section" value="meta">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Meta Title</label>
                            <input type="text" name="meta_title" id="fspm-meta-title" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Meta Description</label>
                            <input type="text" name="meta_description" id="fspm-meta-desc" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Meta Keywords</label>
                            <input type="text" name="meta_keywords" id="fspm-meta-keywords" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">H1 Heading</label>
                            <input type="text" name="h1_heading" id="fspm-h1-heading" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Extra Meta Tags</label>
                            <textarea name="meta_details" id="fspm-meta-details" class="form-control" rows="4"></textarea>
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
function fspWhyVisitRow(idx, title, description) {
    title = (title || '').replace(/"/g, '&quot;');
    return '<tr>' +
        '<td><input type="text" name="why_visits[' + idx + '][title]" value="' + title + '" class="form-control" placeholder="Authentic Cultural Experiences" required></td>' +
        '<td><textarea name="why_visits[' + idx + '][description]" class="form-control">' + (description || '') + '</textarea></td>' +
        '<td><button type="button" class="btn btn-sm btn-outline-danger rm-row"><i class="fas fa-trash"></i></button></td>' +
        '</tr>';
}

function fspFaqRow(idx, question, answer) {
    question = (question || '').replace(/"/g, '&quot;');
    return '<tr>' +
        '<td><input type="text" name="faqs[' + idx + '][question]" value="' + question + '" class="form-control" required></td>' +
        '<td><textarea name="faqs[' + idx + '][answer]" class="form-control">' + (answer || '') + '</textarea></td>' +
        '<td><button type="button" class="btn btn-sm btn-outline-danger rm-row"><i class="fas fa-trash"></i></button></td>' +
        '</tr>';
}

function fspReindex(tableSel, prefix, fields) {
    $(tableSel + ' tbody tr').each(function (i) {
        let $row = $(this);
        fields.forEach(function (f) {
            $row.find('[name$="[' + f + ']"]').attr('name', prefix + '[' + i + '][' + f + ']');
        });
    });
}

$(document).ready(function () {

    let fspSearchTimer;
    $('#fspSearch').on('input', function () {
        clearTimeout(fspSearchTimer);
        let q = $(this).val();
        fspSearchTimer = setTimeout(function () {
            showAjaxLoader($('#fspTableWrapper'));
            $.get('{{ route("admin.festival-state-pages.index") }}', { search: q, ajax: 1 })
                .done(function (res) {
                    $('#fspTableWrapper').html(res.html);
                    if (typeof window.initSwitchery === 'function') window.initSwitchery();
                })
                .fail(function () { hideAjaxLoader($('#fspTableWrapper')); toastr.error('Search failed.'); });
        }, 300);
    });

    // ── Add State Page ──────────────────────────────────────────────────
    $('#addFspForm').on('submit', function (e) {
        e.preventDefault();
        let btn = $('#add-fsp-submit-btn');
        btn.prop('disabled', true);
        $('.fsp-state-error').hide().text('');

        $.ajax({
            url: '{{ route("admin.festival-state-pages.store") }}',
            type: 'POST',
            data: $(this).serialize(),
            success: function () {
                toastr.success('Festival state page created!');
                bootstrap.Modal.getOrCreateInstance(document.getElementById('addFspModal')).hide();
                $('#fspSearch').trigger('input');
            },
            error: function (xhr) {
                btn.prop('disabled', false);
                if (xhr.status === 422 && xhr.responseJSON.errors) {
                    $('.fsp-state-error').text(window.firstErrorMessage(xhr.responseJSON.errors, 'Validation failed.')).show();
                }
            }
        }).always(function () { btn.prop('disabled', false); });
    });

    $('#addFspModal').on('hidden.bs.modal', function () {
        $('#addFspForm')[0].reset();
        $('.fsp-state-error').hide().text('');
    });

    // ── Settings Modal ──────────────────────────────────────────────────
    $(document).on('click', '.btn-fsp-settings', function () {
        let updateUrl = $(this).data('update');
        $.get($(this).data('fetch')).done(function (d) {
            $('#fsps-title').val(d.title || '');
            $('#fsps-banner-text').val(d.banner_text || '');
            $('#fsps-banner-alt').val(d.banner_image_alt || '');
            $('#fsps-short-desc').val(d.short_description || '');
            if (typeof window.setMediaPickerValue === 'function') {
                window.setMediaPickerValue('festival_state_page_banner', d.banner_image, d.banner_image ? (s3BaseUrl + d.banner_image) : null);
            }
            $('#fspSettingsForm').attr('data-url', updateUrl);
            let modalEl = document.getElementById('fspSettingsModal');
            bootstrap.Modal.getOrCreateInstance(modalEl).show();
            modalEl.addEventListener('shown.bs.modal', function onShown() {
                this.removeEventListener('shown.bs.modal', onShown);
                if (typeof window.initTinyMCEOn === 'function') window.initTinyMCEOn($('#fsps-short-desc'));
                setTimeout(() => { tinymce.get('fsps-short-desc')?.setContent(d.short_description || ''); }, 100);
            });
        }).fail(function () { toastr.error('Failed to load settings.'); });
    });

    $('#fspSettingsForm').on('submit', function (e) {
        e.preventDefault();
        if (typeof tinymce !== 'undefined') tinymce.triggerSave();
        let fd = new FormData(this);
        fd.append('_method', 'PUT');
        $.ajax({
            url: $(this).attr('data-url'), type: 'POST', data: fd,
            processData: false, contentType: false,
        }).done(function (r) {
            toastr.success(r.message || 'Saved!');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('fspSettingsModal')).hide();
        }).fail(function (xhr) {
            if (xhr.status === 422 && xhr.responseJSON.errors) {
                let errs = xhr.responseJSON.errors;
                $('.fsps-banner-error').text(window.firstErrorMessage(errs, 'Validation failed.')).show();
            } else {
                toastr.error('Failed to save settings.');
            }
        });
    });

    // ── Featured Festival Modal ─────────────────────────────────────────
    $(document).on('click', '.btn-fsp-featured', function () {
        let updateUrl = $(this).data('update');
        $.get($(this).data('fetch')).done(function (d) {
            let options = '<option value="">— None —</option>';
            (d.state_festivals || []).forEach(function (f) {
                let selected = (d.featured_festival_id == f.id) ? ' selected' : '';
                options += '<option value="' + f.id + '"' + selected + '>' + f.name + '</option>';
            });
            $('#fsp-featured-select').html(options);
            $('#fspFeaturedForm').attr('data-url', updateUrl);
            bootstrap.Modal.getOrCreateInstance(document.getElementById('fspFeaturedModal')).show();
        }).fail(function () { toastr.error('Failed to load festivals.'); });
    });

    $('#fspFeaturedForm').on('submit', function (e) {
        e.preventDefault();
        $.ajax({ url: $(this).attr('data-url'), type: 'PUT', data: $(this).serialize() })
            .done(function (r) {
                toastr.success(r.message || 'Saved!');
                bootstrap.Modal.getOrCreateInstance(document.getElementById('fspFeaturedModal')).hide();
                $('#fspSearch').trigger('input');
            }).fail(function (xhr) {
                toastr.error((xhr.responseJSON && xhr.responseJSON.message) || 'Failed to save.');
            });
    });

    // ── Why Visit Modal ─────────────────────────────────────────────────
    $(document).on('click', '.btn-fsp-why-visit', function () {
        let updateUrl = $(this).data('update');
        $.get($(this).data('fetch')).done(function (d) {
            $('#fsp-wv-title').val(d.why_visit_title || '');
            $('#fsp-wv-sub-title').val(d.why_visit_sub_title || '');
            let items = d.why_visits || [];
            $('#fspWhyVisitTable tbody').html(
                items.length ? items.map((w, i) => fspWhyVisitRow(i, w.title, w.description)).join('') : fspWhyVisitRow(0, '', '')
            );
            $('#fspWhyVisitForm').attr('data-url', updateUrl);
            bootstrap.Modal.getOrCreateInstance(document.getElementById('fspWhyVisitModal')).show();
        }).fail(function () { toastr.error('Failed to load.'); });
    });

    $('#fspAddWhyVisitRow').on('click', function () {
        let idx = $('#fspWhyVisitTable tbody tr').length;
        $('#fspWhyVisitTable tbody').append(fspWhyVisitRow(idx, '', ''));
    });

    $('#fspWhyVisitForm').on('submit', function (e) {
        e.preventDefault();
        fspReindex('#fspWhyVisitTable', 'why_visits', ['title', 'description']);
        $.ajax({ url: $(this).attr('data-url'), type: 'PUT', data: $(this).serialize() })
            .done(function (r) {
                toastr.success(r.message || 'Saved!');
                bootstrap.Modal.getOrCreateInstance(document.getElementById('fspWhyVisitModal')).hide();
            }).fail(function () { toastr.error('Failed to save.'); });
    });

    $(document).on('click', '#fspWhyVisitTable .rm-row', function () {
        $(this).closest('tr').remove();
        fspReindex('#fspWhyVisitTable', 'why_visits', ['title', 'description']);
    });

    // ── FAQ Modal ────────────────────────────────────────────────────────
    $(document).on('click', '.btn-fsp-faq', function () {
        let updateUrl = $(this).data('update');
        $.get($(this).data('fetch')).done(function (d) {
            $('#fspf-faq-title').val(d.faq_title || '');
            $('#fspf-faq-sub-title').val(d.faq_sub_title || '');
            let faqs = d.faqs || [];
            $('#fspFaqTable tbody').html(
                faqs.length ? faqs.map((f, i) => fspFaqRow(i, f.question, f.answer)).join('') : fspFaqRow(0, '', '')
            );
            $('#fspFaqForm').attr('data-url', updateUrl);
            bootstrap.Modal.getOrCreateInstance(document.getElementById('fspFaqModal')).show();
        }).fail(function () { toastr.error('Failed to load FAQs.'); });
    });

    $('#fspAddFaqRow').on('click', function () {
        let idx = $('#fspFaqTable tbody tr').length;
        $('#fspFaqTable tbody').append(fspFaqRow(idx, '', ''));
    });

    $('#fspFaqForm').on('submit', function (e) {
        e.preventDefault();
        fspReindex('#fspFaqTable', 'faqs', ['question', 'answer']);
        $.ajax({ url: $(this).attr('data-url'), type: 'PUT', data: $(this).serialize() })
            .done(function (r) {
                toastr.success(r.message || 'Saved!');
                bootstrap.Modal.getOrCreateInstance(document.getElementById('fspFaqModal')).hide();
            }).fail(function () { toastr.error('Failed to save FAQs.'); });
    });

    $(document).on('click', '#fspFaqTable .rm-row', function () {
        $(this).closest('tr').remove();
        fspReindex('#fspFaqTable', 'faqs', ['question', 'answer']);
    });

    // ── SEO Meta Modal ───────────────────────────────────────────────────
    $(document).on('click', '.btn-fsp-meta', function () {
        let updateUrl = $(this).data('update');
        $.get($(this).data('fetch')).done(function (d) {
            $('#fspm-meta-title').val(d.meta_title || '');
            $('#fspm-meta-desc').val(d.meta_description || '');
            $('#fspm-meta-keywords').val(d.meta_keywords || '');
            $('#fspm-h1-heading').val(d.h1_heading || '');
            $('#fspm-meta-details').val(d.meta_details || '');
            $('#fspMetaForm').attr('data-url', updateUrl);
            bootstrap.Modal.getOrCreateInstance(document.getElementById('fspMetaModal')).show();
        }).fail(function () { toastr.error('Failed to load meta.'); });
    });

    $('#fspMetaForm').on('submit', function (e) {
        e.preventDefault();
        $.ajax({ url: $(this).attr('data-url'), type: 'PUT', data: $(this).serialize() })
            .done(function (r) {
                toastr.success(r.message || 'Saved!');
                bootstrap.Modal.getOrCreateInstance(document.getElementById('fspMetaModal')).hide();
            }).fail(function () { toastr.error('Failed to save meta.'); });
    });

    // ── Status toggle ─────────────────────────────────────────────────────
    $(document).on('change', '.fsp-status', function () {
        $.ajax({ url: $(this).data('url'), type: 'POST', data: { _token: '{{ csrf_token() }}' } })
            .done(function () { toastr.success('Status updated'); })
            .fail(function () { toastr.error('Failed to update status.'); });
    });

});
</script>
@endsection
