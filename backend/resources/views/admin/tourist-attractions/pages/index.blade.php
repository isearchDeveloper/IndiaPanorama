@section('title', 'Tourist Attraction Pages')
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
    #tap-type-state.active { background-color: #2563eb; border-color: #2563eb; color: #fff; }
    #tap-type-location.active { background-color: #7c3aed; border-color: #7c3aed; color: #fff; }
</style>
@endpush

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-0 fw-bold"><i class="fas fa-mountain-sun me-2 text-primary"></i>Tourist Attraction Pages</h2>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.tourist-attractions.setting.index') }}" class="btn btn-outline-purple">
                <i class="fas fa-globe me-2"></i>Root Page Setting
            </a>
            <a href="{{ route('admin.tourist-attractions.index') }}" class="btn btn-outline-orange">
                <i class="fas fa-list me-2"></i>Manage Attractions
            </a>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTapModal">
                <i class="fas fa-plus me-2"></i>Add Page
            </button>
        </div>
    </div>

    <div class="mb-3">
        <div class="position-relative" style="max-width:320px;">
            <i class="fas fa-search position-absolute" style="left:12px; top:50%; transform:translateY(-50%); color:#94a3b8; font-size:.85rem;"></i>
            <input type="text" id="tapSearch" class="form-control" style="padding-left:32px;"
                   placeholder="Search state or city..." value="{{ request('search') }}">
        </div>
    </div>

    <div id="tapTableWrapper">
        @include('admin.tourist-attractions.pages._table')
    </div>

</div>
@endsection

@section('modal')
<div class="modal fade" id="addTapModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Add Tourist Attraction Page</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="addTapForm">
                @csrf
                <input type="hidden" name="subject_type" id="tap-subject-type" value="state">
                <div class="modal-body">
                    <label class="form-label d-block">Page For <span class="text-danger">*</span></label>
                    <div class="btn-group w-100 mb-3" role="group">
                        <button type="button" class="btn btn-outline-orange active" id="tap-type-state">
                            <i class="fas fa-map me-1"></i>State
                        </button>
                        <button type="button" class="btn btn-outline-purple" id="tap-type-location">
                            <i class="fas fa-city me-1"></i>City
                        </button>
                    </div>

                    <div id="tap-state-wrap">
                        <label class="form-label">Select State <span class="text-danger">*</span></label>
                        <select name="state_id" id="tap-state-select" class="form-select">
                            <option value="">— Select State —</option>
                            @foreach($availableStates as $state)
                            <option value="{{ $state->id }}">{{ $state->name }}</option>
                            @endforeach
                        </select>
                        @if($availableStates->isEmpty())
                        <small class="text-muted d-block mt-2">All states already have a page.</small>
                        @endif
                        <div class="text-danger small mt-1 state-error" style="display:none"></div>
                    </div>

                    <div id="tap-location-wrap" style="display:none">
                        <label class="form-label">Select City <span class="text-danger">*</span></label>
                        <select name="location_id" id="tap-location-select" class="form-select">
                            <option value="">— Select City —</option>
                            @foreach($availableLocations as $loc)
                            <option value="{{ $loc->id }}">{{ $loc->name }}{{ $loc->state ? ' (' . $loc->state->name . ')' : '' }}</option>
                            @endforeach
                        </select>
                        @if($availableLocations->isEmpty())
                        <small class="text-muted d-block mt-2">All cities already have a page.</small>
                        @endif
                        <div class="text-danger small mt-1 location-error" style="display:none"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="add-tap-submit-btn">
                        <i class="fas fa-save me-1"></i>Create
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════ BANNER & SETTINGS MODAL ══════════════ --}}
<div class="modal fade" id="tapSettingsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header modal-header-orange">
                <h5 class="modal-title" id="tapSettingsModalTitle"><i class="fas fa-cog me-2"></i>Banner & Settings</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="tapSettingsForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="section" value="banner_settings">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" id="tps-title" class="form-control" placeholder="e.g. Kerala Tourist Attractions">
                        </div>
                        <div class="col-md-6">
                            <x-media-picker name="banner_image" picker-id="tap_banner" label="Banner Image" folder="tourist-attractions/pages" />
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
<div class="modal fade" id="tapBestTimesModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="tapBestTimesModalTitle"><i class="fas fa-calendar-alt me-2"></i>Best Time To Visit</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="tapBestTimesForm">
                @csrf
                <input type="hidden" name="section" value="best_times">
                <div class="modal-body">
                    <table class="table" id="tapBestTimesTable">
                        <thead><tr><th>Period</th><th>Description</th><th width="40"></th></tr></thead>
                        <tbody></tbody>
                    </table>
                    <button type="button" class="btn btn-sm btn-outline-success" id="tapAddBestTimeRow"><i class="fas fa-plus me-1"></i>Add Period</button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info"><i class="fas fa-save me-1"></i>Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════ FAQ MODAL ══════════════ --}}
<div class="modal fade" id="tapFaqModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="tapFaqModalTitle"><i class="fas fa-question-circle me-2"></i>FAQs</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="tapFaqForm">
                @csrf
                <input type="hidden" name="section" value="faqs">
                <div class="modal-body">
                    <div class="row g-3 mb-2">
                        <div class="col-md-6">
                            <label class="form-label">Section Title</label>
                            <input type="text" name="faq_title" id="tapf-faq-title" class="form-control" placeholder="Frequently Asked Questions">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Section Sub Title</label>
                            <input type="text" name="faq_sub_title" id="tapf-faq-sub-title" class="form-control">
                        </div>
                    </div>
                    <table class="table" id="tapFaqTable">
                        <thead><tr><th>Question</th><th>Answer</th><th width="40"></th></tr></thead>
                        <tbody></tbody>
                    </table>
                    <button type="button" class="btn btn-sm btn-outline-success" id="tapAddFaqRow"><i class="fas fa-plus me-1"></i>Add FAQ</button>
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
<div class="modal fade" id="tapMetaModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header modal-header-purple">
                <h5 class="modal-title" id="tapMetaModalTitle"><i class="fas fa-globe me-2"></i>SEO Meta</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="tapMetaForm">
                @csrf
                <input type="hidden" name="section" value="meta">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Meta Title</label>
                            <input type="text" name="meta_title" id="tapm-meta-title" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Meta Description</label>
                            <input type="text" name="meta_description" id="tapm-meta-desc" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Meta Keywords</label>
                            <input type="text" name="meta_keywords" id="tapm-meta-keywords" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">H1 Heading</label>
                            <input type="text" name="h1_heading" id="tapm-h1-heading" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Extra Meta Tags</label>
                            <textarea name="meta_details" id="tapm-meta-details" class="form-control" rows="4"></textarea>
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
function tapBestTimeRow(idx, period, description) {
    period = (period || '').replace(/"/g, '&quot;');
    return '<tr>' +
        '<td><input type="text" name="best_times[' + idx + '][period]" value="' + period + '" class="form-control" placeholder="e.g. September - November" required></td>' +
        '<td><textarea name="best_times[' + idx + '][description]" class="form-control">' + (description || '') + '</textarea></td>' +
        '<td><button type="button" class="btn btn-sm btn-outline-danger rm-row"><i class="fas fa-trash"></i></button></td>' +
        '</tr>';
}

function tapFaqRow(idx, question, answer) {
    question = (question || '').replace(/"/g, '&quot;');
    return '<tr>' +
        '<td><input type="text" name="faqs[' + idx + '][question]" value="' + question + '" class="form-control" required></td>' +
        '<td><textarea name="faqs[' + idx + '][answer]" class="form-control">' + (answer || '') + '</textarea></td>' +
        '<td><button type="button" class="btn btn-sm btn-outline-danger rm-row"><i class="fas fa-trash"></i></button></td>' +
        '</tr>';
}

function tapReindex(tableSel, prefix, fields) {
    $(tableSel + ' tbody tr').each(function (i) {
        let $row = $(this);
        fields.forEach(function (f) {
            $row.find('[name$="[' + f + ']"]').attr('name', prefix + '[' + i + '][' + f + ']');
        });
    });
}

$(document).ready(function () {

    let tapSearchTimer;
    $('#tapSearch').on('input', function () {
        clearTimeout(tapSearchTimer);
        let q = $(this).val();
        tapSearchTimer = setTimeout(function () {
            showAjaxLoader($('#tapTableWrapper'));
            $.get('{{ route("admin.tourist-attraction-pages.index") }}', { search: q, ajax: 1 })
                .done(function (res) {
                    $('#tapTableWrapper').html(res.html);
                    if (typeof window.initSwitchery === 'function') window.initSwitchery();
                })
                .fail(function () { hideAjaxLoader($('#tapTableWrapper')); toastr.error('Search failed.'); });
        }, 300);
    });

    // ── Settings Modal ──────────────────────────────────────────────────
    $(document).on('click', '.btn-tap-settings', function () {
        let updateUrl = $(this).data('update');
        $.get($(this).data('fetch')).done(function (d) {
            $('#tps-title').val(d.title || '');
            $('#tps-banner-alt').val(d.banner_image_alt || '');
            let shortDesc = d.short_description || '';
            let tpsEditor = tinymce.get('tps-short-desc');
            if (tpsEditor) { tpsEditor.setContent(shortDesc); } else { $('#tps-short-desc').val(shortDesc); }
            if (typeof window.setMediaPickerValue === 'function') {
                window.setMediaPickerValue('tap_banner', d.banner_image, d.banner_image ? (s3BaseUrl + d.banner_image) : null);
            }
            $('#tapSettingsForm').attr('data-url', updateUrl);
            bootstrap.Modal.getOrCreateInstance(document.getElementById('tapSettingsModal')).show();
        }).fail(function () { toastr.error('Failed to load settings.'); });
    });

    $('#tapSettingsForm').on('submit', function (e) {
        e.preventDefault();
        if (typeof tinymce !== 'undefined') tinymce.triggerSave();
        let fd = new FormData(this);
        fd.append('_method', 'PUT');
        $.ajax({
            url: $(this).attr('data-url'), type: 'POST', data: fd,
            processData: false, contentType: false,
        }).done(function (r) {
            toastr.success(r.message || 'Saved!');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('tapSettingsModal')).hide();
        }).fail(function (xhr) {
            if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                let msgs = Object.values(xhr.responseJSON.errors).map(function(e) { return e[0]; });
                toastr.error(msgs.join('<br>'), 'Validation Error', { escapeHtml: false });
            } else {
                let msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to save settings.';
                toastr.error(msg);
            }
        });
    });

    // ── Best Times Modal ────────────────────────────────────────────────
    $(document).on('click', '.btn-tap-best-times', function () {
        let updateUrl = $(this).data('update');
        $.get($(this).data('fetch')).done(function (d) {
            let items = d.best_times || [];
            $('#tapBestTimesTable tbody').html(
                items.length ? items.map((f, i) => tapBestTimeRow(i, f.period, f.description)).join('') : tapBestTimeRow(0, '', '')
            );
            $('#tapBestTimesForm').attr('data-url', updateUrl);
            bootstrap.Modal.getOrCreateInstance(document.getElementById('tapBestTimesModal')).show();
        }).fail(function () { toastr.error('Failed to load best times.'); });
    });

    $('#tapAddBestTimeRow').on('click', function () {
        let idx = $('#tapBestTimesTable tbody tr').length;
        $('#tapBestTimesTable tbody').append(tapBestTimeRow(idx, '', ''));
    });

    $('#tapBestTimesForm').on('submit', function (e) {
        e.preventDefault();
        tapReindex('#tapBestTimesTable', 'best_times', ['period', 'description']);
        $.ajax({ url: $(this).attr('data-url'), type: 'PUT', data: $(this).serialize() })
            .done(function (r) {
                toastr.success(r.message || 'Saved!');
                bootstrap.Modal.getOrCreateInstance(document.getElementById('tapBestTimesModal')).hide();
            }).fail(function () { toastr.error('Failed to save.'); });
    });

    // ── FAQ Modal ────────────────────────────────────────────────────────
    $(document).on('click', '.btn-tap-faq', function () {
        let updateUrl = $(this).data('update');
        $.get($(this).data('fetch')).done(function (d) {
            $('#tapf-faq-title').val(d.faq_title || '');
            $('#tapf-faq-sub-title').val(d.faq_sub_title || '');
            let faqs = d.faqs || [];
            $('#tapFaqTable tbody').html(
                faqs.length ? faqs.map((f, i) => tapFaqRow(i, f.question, f.answer)).join('') : tapFaqRow(0, '', '')
            );
            $('#tapFaqForm').attr('data-url', updateUrl);
            bootstrap.Modal.getOrCreateInstance(document.getElementById('tapFaqModal')).show();
        }).fail(function () { toastr.error('Failed to load FAQs.'); });
    });

    $('#tapAddFaqRow').on('click', function () {
        let idx = $('#tapFaqTable tbody tr').length;
        $('#tapFaqTable tbody').append(tapFaqRow(idx, '', ''));
    });

    $('#tapFaqForm').on('submit', function (e) {
        e.preventDefault();
        tapReindex('#tapFaqTable', 'faqs', ['question', 'answer']);
        $.ajax({ url: $(this).attr('data-url'), type: 'PUT', data: $(this).serialize() })
            .done(function (r) {
                toastr.success(r.message || 'Saved!');
                bootstrap.Modal.getOrCreateInstance(document.getElementById('tapFaqModal')).hide();
            }).fail(function () { toastr.error('Failed to save FAQs.'); });
    });

    // ── SEO Meta Modal ───────────────────────────────────────────────────
    $(document).on('click', '.btn-tap-meta', function () {
        let updateUrl = $(this).data('update');
        $.get($(this).data('fetch')).done(function (d) {
            $('#tapm-meta-title').val(d.meta_title || '');
            $('#tapm-meta-desc').val(d.meta_description || '');
            $('#tapm-meta-keywords').val(d.meta_keywords || '');
            $('#tapm-h1-heading').val(d.h1_heading || '');
            $('#tapm-meta-details').val(d.meta_details || '');
            $('#tapMetaForm').attr('data-url', updateUrl);
            bootstrap.Modal.getOrCreateInstance(document.getElementById('tapMetaModal')).show();
        }).fail(function () { toastr.error('Failed to load meta.'); });
    });

    $('#tapMetaForm').on('submit', function (e) {
        e.preventDefault();
        $.ajax({ url: $(this).attr('data-url'), type: 'PUT', data: $(this).serialize() })
            .done(function (r) {
                toastr.success(r.message || 'Saved!');
                bootstrap.Modal.getOrCreateInstance(document.getElementById('tapMetaModal')).hide();
            }).fail(function () { toastr.error('Failed to save meta.'); });
    });

    $(document).on('click', '#tapBestTimesTable .rm-row', function () {
        $(this).closest('tr').remove();
        tapReindex('#tapBestTimesTable', 'best_times', ['period', 'description']);
    });

    $(document).on('click', '#tapFaqTable .rm-row', function () {
        $(this).closest('tr').remove();
        tapReindex('#tapFaqTable', 'faqs', ['question', 'answer']);
    });

    // ── Add Page: State / City type toggle ──────────────────────────
    $('#tap-type-state').on('click', function () {
        $('#tap-subject-type').val('state');
        $(this).addClass('active');
        $('#tap-type-location').removeClass('active');
        $('#tap-state-wrap').show();
        $('#tap-location-wrap').hide();
    });

    $('#tap-type-location').on('click', function () {
        $('#tap-subject-type').val('location');
        $(this).addClass('active');
        $('#tap-type-state').removeClass('active');
        $('#tap-location-wrap').show();
        $('#tap-state-wrap').hide();
    });

    $('#addTapModal').on('hidden.bs.modal', function () {
        $('#tap-type-state').trigger('click');
        $('#addTapForm')[0].reset();
        $('.state-error, .location-error').hide().text('');
    });

    $('#addTapForm').on('submit', function (e) {
        e.preventDefault();
        let btn = $('#add-tap-submit-btn');
        btn.prop('disabled', true);
        $('.state-error, .location-error').hide().text('');

        $.ajax({
            url: '{{ route("admin.tourist-attraction-pages.store") }}',
            type: 'POST',
            data: $(this).serialize(),
            success: function () {
                toastr.success('Page created!');
                bootstrap.Modal.getOrCreateInstance(document.getElementById('addTapModal')).hide();
                $('#tapSearch').trigger('input');
            },
            error: function (xhr) {
                btn.prop('disabled', false);
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors || {};
                    if (errors.state_id) $('.state-error').text(errors.state_id[0]).show();
                    if (errors.location_id) $('.location-error').text(errors.location_id[0]).show();
                    let other = Object.entries(errors)
                        .filter(function(e) { return e[0] !== 'state_id' && e[0] !== 'location_id'; })
                        .map(function(e) { return e[1][0]; });
                    if (other.length) toastr.error(other.join('<br>'));
                } else {
                    let msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Something went wrong. Please try again.';
                    toastr.error(msg);
                }
            }
        });
    });

    $(document).on('change', '.ta-page-status', function () {
        $.ajax({ url: $(this).data('url'), type: 'POST', data: { _token: '{{ csrf_token() }}' } })
            .done(function () { toastr.success('Status updated'); })
            .fail(function () { toastr.error('Failed to update status.'); });
    });

    $(document).on('change', '.ta-page-featured', function () {
        $.ajax({ url: $(this).data('url'), type: 'POST', data: { _token: '{{ csrf_token() }}' } })
            .done(function () { toastr.success('Featured flag updated'); })
            .fail(function () { toastr.error('Failed to update featured flag.'); });
    });

    $(document).on('change', '.ta-page-popular', function () {
        $.ajax({ url: $(this).data('url'), type: 'POST', data: { _token: '{{ csrf_token() }}' } })
            .done(function () { toastr.success('Popular updated'); })
            .fail(function () { toastr.error('Failed to update popular flag.'); });
    });

    $(document).on('click', '.delete-ta-page', function () {
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
