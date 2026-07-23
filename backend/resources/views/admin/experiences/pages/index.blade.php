@section('title', 'Manage States')
@extends('layouts.app')

@push('style')
<style>
    .btn-outline-purple { color: #6d28d9; background-color: transparent; border: 1px solid #7c3aed; }
    .btn-outline-purple:hover { color: #fff; background-color: #7c3aed; border-color: #7c3aed; }
</style>
@endpush

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-0 fw-bold"><i class="fas fa-map me-2 text-primary"></i>Manage States</h2>
            <small class="text-muted">Hub pages for /{state}/experiences and /{state}/{city}/experiences — banner, adventure experiences, highlights, FAQs &amp; meta. A page is created automatically the first time an Experience is added for that state/city.</small>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.experiences.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to Experiences
            </a>
        </div>
    </div>

    <ul class="nav nav-tabs mb-3" id="epTabs">
        <li class="nav-item">
            <button class="nav-link {{ ($activeTab ?? 'states') === 'states' ? 'active' : '' }}" id="epStatesTab" data-bs-toggle="tab" data-bs-target="#epStatesPane" type="button">
                <i class="fas fa-map me-1"></i>States
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link {{ ($activeTab ?? 'states') === 'cities' ? 'active' : '' }}" id="epCitiesTab" data-bs-toggle="tab" data-bs-target="#epCitiesPane" type="button">
                <i class="fas fa-city me-1"></i>Cities
            </button>
        </li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade {{ ($activeTab ?? 'states') === 'states' ? 'show active' : '' }}" id="epStatesPane">
            <div class="mb-3">
                <input type="text" id="epStateSearch" class="form-control" style="max-width:320px;" placeholder="Search state..." value="{{ request('search') }}">
            </div>
            <div id="epStatesTableWrapper">
                @if(isset($pages))
                    @include('admin.experiences.pages._table', ['pages' => $pages])
                @endif
            </div>
        </div>
        <div class="tab-pane fade {{ ($activeTab ?? 'states') === 'cities' ? 'show active' : '' }}" id="epCitiesPane">
            <div class="mb-3">
                <input type="text" id="epCitySearch" class="form-control" style="max-width:320px;" placeholder="Search city..." value="{{ request('search') }}">
            </div>
            <div id="epCitiesTableWrapper">
                @if(isset($cityPages))
                    @include('admin.experiences.pages._cities_table', ['cityPages' => $cityPages])
                @endif
            </div>
        </div>
    </div>

</div>
@endsection

@section('modal')
{{-- ══════════════ BANNER MODAL ══════════════ --}}
<div class="modal fade" id="pageBannerModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white" style="background:#0891b2;">
                <h5 class="modal-title"><i class="fas fa-image me-2"></i><span id="pb-modal-title">Page — Banner</span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="pageBannerForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="section" value="banner_settings">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" id="pb-title" class="form-control">
                    </div>
                    <div class="mb-3">
                        <x-media-picker name="banner_image" picker-id="pb_banner_image" label="Banner Image" folder="experiences/pages" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Banner Image Alt</label>
                        <input type="text" name="banner_image_alt" id="pb-image-alt" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Short Description</label>
                        <textarea name="short_description" id="pb-short-desc" class="form-control tinymce" rows="4"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn text-white" style="background:#0891b2;"><i class="fas fa-save me-1"></i>Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════ ADVENTURE EXPERIENCES MODAL ══════════════ --}}
<div class="modal fade" id="pageActivitiesModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header text-white" style="background:#2563eb;">
                <h5 class="modal-title"><i class="fas fa-hiking me-2"></i><span id="pa-modal-title">Page — Adventure Experiences</span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="pageActivitiesForm">
                @csrf
                <input type="hidden" name="section" value="activities">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Section Title <small class="text-muted">(defaults to "Adventure Experiences in {Place}")</small></label>
                        <input type="text" name="activities_title" id="pa-title" class="form-control">
                    </div>
                    <table class="table align-middle" id="paTable">
                        <thead><tr><th>Title</th><th>Description</th><th width="130">Best Time</th><th width="150">Best For</th><th width="150">Approx. Cost</th><th width="40"></th></tr></thead>
                        <tbody></tbody>
                    </table>
                    <button type="button" class="btn btn-sm btn-outline-success" id="paAddRow"><i class="fas fa-plus me-1"></i>Add Row</button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn text-white" style="background:#2563eb;"><i class="fas fa-save me-1"></i>Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════ WHAT MAKES IT SPECIAL MODAL ══════════════ --}}
<div class="modal fade" id="pageHighlightsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-star me-2"></i><span id="ph-modal-title">Page — What Makes It Special</span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="pageHighlightsForm">
                @csrf
                <input type="hidden" name="section" value="highlights">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Section Title <small class="text-muted">(defaults to "What Makes {Place} Special?")</small></label>
                        <input type="text" name="highlights_title" id="ph-title" class="form-control">
                    </div>
                    <table class="table" id="phTable">
                        <thead><tr><th>Title</th><th>Description</th><th width="40"></th></tr></thead>
                        <tbody></tbody>
                    </table>
                    <button type="button" class="btn btn-sm btn-outline-success" id="phAddRow"><i class="fas fa-plus me-1"></i>Add Row</button>
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
<div class="modal fade" id="pageFaqModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title"><i class="fas fa-question-circle me-2"></i><span id="pf-modal-title">Page — FAQs</span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="pageFaqForm">
                @csrf
                <input type="hidden" name="section" value="faqs">
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Section Title</label>
                            <input type="text" name="faq_title" id="pf-title" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Section Sub Title</label>
                            <input type="text" name="faq_sub_title" id="pf-sub-title" class="form-control">
                        </div>
                    </div>
                    <table class="table" id="pageFaqTable">
                        <thead><tr><th>Question</th><th>Answer</th><th width="40"></th></tr></thead>
                        <tbody></tbody>
                    </table>
                    <button type="button" class="btn btn-sm btn-outline-success" id="pageAddFaqRow"><i class="fas fa-plus me-1"></i>Add FAQ</button>
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
<div class="modal fade" id="pageMetaModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background:#7c3aed;color:#fff;">
                <h5 class="modal-title"><i class="fas fa-globe me-2"></i><span id="pm-modal-title">Page — SEO Meta</span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="pageMetaForm">
                @csrf
                <input type="hidden" name="section" value="meta">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Meta Title</label>
                            <input type="text" name="meta_title" id="pm-meta-title" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Meta Description</label>
                            <input type="text" name="meta_description" id="pm-meta-desc" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Meta Keywords</label>
                            <input type="text" name="meta_keywords" id="pm-meta-keywords" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">H1 Heading</label>
                            <input type="text" name="h1_heading" id="pm-h1-heading" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Extra Meta Tags</label>
                            <textarea name="meta_details" id="pm-meta-details" class="form-control" rows="4"></textarea>
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
function pageFaqRow(idx, question, answer) {
    question = (question || '').replace(/"/g, '&quot;');
    return '<tr>' +
        '<td><input type="text" name="faqs[' + idx + '][question]" value="' + question + '" class="form-control" required></td>' +
        '<td><textarea name="faqs[' + idx + '][answer]" class="form-control">' + (answer || '') + '</textarea></td>' +
        '<td><button type="button" class="btn btn-sm btn-outline-danger rm-row"><i class="fas fa-trash"></i></button></td>' +
        '</tr>';
}
function paRow(idx, row) {
    row = row || {};
    function esc(v) { return (v || '').toString().replace(/"/g, '&quot;'); }
    return '<tr>' +
        '<td><input type="text" name="activities[' + idx + '][title]" value="' + esc(row.title) + '" class="form-control" placeholder="e.g. Heritage Walks"></td>' +
        '<td><input type="text" name="activities[' + idx + '][description]" value="' + esc(row.description) + '" class="form-control"></td>' +
        '<td><input type="text" name="activities[' + idx + '][best_time]" value="' + esc(row.best_time) + '" class="form-control" placeholder="e.g. October - March"></td>' +
        '<td><input type="text" name="activities[' + idx + '][best_for]" value="' + esc(row.best_for) + '" class="form-control" placeholder="e.g. Culture seekers"></td>' +
        '<td><input type="text" name="activities[' + idx + '][approximate_cost]" value="' + esc(row.approximate_cost) + '" class="form-control" placeholder="e.g. Rs.1,499 / Person"></td>' +
        '<td><button type="button" class="btn btn-sm btn-outline-danger rm-row"><i class="fas fa-trash"></i></button></td>' +
        '</tr>';
}
function phRow(idx, title, description) {
    title = (title || '').replace(/"/g, '&quot;');
    return '<tr>' +
        '<td><input type="text" name="highlights[' + idx + '][title]" value="' + title + '" class="form-control" placeholder="e.g. Signature Landscapes"></td>' +
        '<td><input type="text" name="highlights[' + idx + '][description]" value="' + (description || '') + '" class="form-control"></td>' +
        '<td><button type="button" class="btn btn-sm btn-outline-danger rm-row"><i class="fas fa-trash"></i></button></td>' +
        '</tr>';
}
function pageReindex(tableSel, prefix, fields) {
    $(tableSel + ' tbody tr').each(function (i) {
        let $row = $(this);
        fields.forEach(function (f) {
            $row.find('[name$="[' + f + ']"]').attr('name', prefix + '[' + i + '][' + f + ']');
        });
    });
}

$(document).ready(function () {

    let citiesLoaded = {{ isset($cityPages) ? 'true' : 'false' }};
    let statesLoaded = {{ isset($pages) ? 'true' : 'false' }};

    function reloadStates() {
        showAjaxLoader($('#epStatesTableWrapper'));
        $.get('{{ route("admin.experience-pages.index") }}', { tab: 'states', search: $('#epStateSearch').val(), ajax: 1 })
            .done(function (res) {
                $('#epStatesTableWrapper').html(res.html);
                statesLoaded = true;
                if (typeof window.initSwitchery === 'function') window.initSwitchery();
            })
            .fail(function () { hideAjaxLoader($('#epStatesTableWrapper')); toastr.error('Search failed.'); });
    }

    function reloadCities() {
        showAjaxLoader($('#epCitiesTableWrapper'));
        $.get('{{ route("admin.experience-pages.index") }}', { tab: 'cities', search: $('#epCitySearch').val(), ajax: 1 })
            .done(function (res) {
                $('#epCitiesTableWrapper').html(res.html);
                citiesLoaded = true;
                if (typeof window.initSwitchery === 'function') window.initSwitchery();
            })
            .fail(function () { hideAjaxLoader($('#epCitiesTableWrapper')); toastr.error('Search failed.'); });
    }

    let epSearchTimer;
    $('#epStateSearch').on('input', function () { clearTimeout(epSearchTimer); epSearchTimer = setTimeout(reloadStates, 300); });
    $('#epCitySearch').on('input', function () { clearTimeout(epSearchTimer); epSearchTimer = setTimeout(reloadCities, 300); });

    // Lazy-load whichever tab's table hasn't been fetched yet, the first time it's shown.
    $('#epStatesTab').on('shown.bs.tab', function () { if (!statesLoaded) reloadStates(); });
    $('#epCitiesTab').on('shown.bs.tab', function () { if (!citiesLoaded) reloadCities(); });

    $(document).on('click', '.btn-page-banner', function () {
        let id = $(this).data('id');
        $.get('{{ url("admin/experience-pages") }}/' + id).done(function (d) {
            $('#pageBannerForm').attr('data-id', id);
            $('#pb-modal-title').text((d.subject?.name || 'Page') + ' — Banner');
            $('#pb-title').val(d.title || '');
            $('#pb-image-alt').val(d.banner_image_alt || '');
            let shortDesc = d.short_description || '';
            $('#pb-short-desc').val(shortDesc);
            if (typeof window.setMediaPickerValue === 'function') {
                window.setMediaPickerValue('pb_banner_image', d.banner_image, d.banner_image ? (s3BaseUrl + d.banner_image) : null);
            }
            $('#pageBannerModal').one('shown.bs.modal', function () {
                if (typeof tinymce !== 'undefined' && tinymce.get('pb-short-desc')) {
                    tinymce.get('pb-short-desc').setContent(shortDesc);
                }
            });
            bootstrap.Modal.getOrCreateInstance(document.getElementById('pageBannerModal')).show();
        }).fail(function () { toastr.error('Failed to load page.'); });
    });

    $('#pageBannerForm').on('submit', function (e) {
        e.preventDefault();
        if (typeof tinymce !== 'undefined') tinymce.triggerSave();
        let id = $(this).attr('data-id');
        let fd = new FormData(this);
        fd.append('_method', 'PUT');
        $.ajax({
            url: '{{ url("admin/experience-pages") }}/' + id + '/section', type: 'POST', data: fd,
            processData: false, contentType: false,
        }).done(function (r) {
            toastr.success(r.message || 'Saved!');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('pageBannerModal')).hide();
        }).fail(function () { toastr.error('Failed to save banner.'); });
    });

    // ── Adventure Experiences ────────────────────────────────────────────────
    $(document).on('click', '.btn-page-activities', function () {
        let id = $(this).data('id');
        $.get('{{ url("admin/experience-pages") }}/' + id).done(function (d) {
            $('#pageActivitiesForm').attr('data-id', id);
            $('#pa-modal-title').text((d.subject?.name || 'Page') + ' — Adventure Experiences');
            $('#pa-title').val(d.activities_title || '');
            let rows = d.activities || [];
            $('#paTable tbody').html(rows.length ? rows.map((r, i) => paRow(i, r)).join('') : paRow(0, {}));
            bootstrap.Modal.getOrCreateInstance(document.getElementById('pageActivitiesModal')).show();
        }).fail(function () { toastr.error('Failed to load activities.'); });
    });

    $('#paAddRow').on('click', function () {
        let idx = $('#paTable tbody tr').length;
        $('#paTable tbody').append(paRow(idx, {}));
    });

    $(document).on('click', '#paTable .rm-row', function () {
        $(this).closest('tr').remove();
        pageReindex('#paTable', 'activities', ['title', 'description', 'best_time', 'best_for', 'approximate_cost']);
    });

    $('#pageActivitiesForm').on('submit', function (e) {
        e.preventDefault();
        pageReindex('#paTable', 'activities', ['title', 'description', 'best_time', 'best_for', 'approximate_cost']);
        let id = $(this).attr('data-id');
        $.ajax({
            url: '{{ url("admin/experience-pages") }}/' + id + '/section', type: 'PUT', data: $(this).serialize(),
        }).done(function (r) {
            toastr.success(r.message || 'Saved!');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('pageActivitiesModal')).hide();
        }).fail(function () { toastr.error('Failed to save activities.'); });
    });

    // ── What Makes It Special ────────────────────────────────────────────────
    $(document).on('click', '.btn-page-highlights', function () {
        let id = $(this).data('id');
        $.get('{{ url("admin/experience-pages") }}/' + id).done(function (d) {
            $('#pageHighlightsForm').attr('data-id', id);
            $('#ph-modal-title').text((d.subject?.name || 'Page') + ' — What Makes It Special');
            $('#ph-title').val(d.highlights_title || '');
            let rows = d.highlights || [];
            $('#phTable tbody').html(rows.length ? rows.map((r, i) => phRow(i, r.title, r.description)).join('') : phRow(0, '', ''));
            bootstrap.Modal.getOrCreateInstance(document.getElementById('pageHighlightsModal')).show();
        }).fail(function () { toastr.error('Failed to load highlights.'); });
    });

    $('#phAddRow').on('click', function () {
        let idx = $('#phTable tbody tr').length;
        $('#phTable tbody').append(phRow(idx, '', ''));
    });

    $(document).on('click', '#phTable .rm-row', function () {
        $(this).closest('tr').remove();
        pageReindex('#phTable', 'highlights', ['title', 'description']);
    });

    $('#pageHighlightsForm').on('submit', function (e) {
        e.preventDefault();
        pageReindex('#phTable', 'highlights', ['title', 'description']);
        let id = $(this).attr('data-id');
        $.ajax({
            url: '{{ url("admin/experience-pages") }}/' + id + '/section', type: 'PUT', data: $(this).serialize(),
        }).done(function (r) {
            toastr.success(r.message || 'Saved!');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('pageHighlightsModal')).hide();
        }).fail(function () { toastr.error('Failed to save highlights.'); });
    });

    $(document).on('click', '.btn-page-faq', function () {
        let id = $(this).data('id');
        $.get('{{ url("admin/experience-pages") }}/' + id).done(function (d) {
            $('#pageFaqForm').attr('data-id', id);
            $('#pf-modal-title').text((d.subject?.name || 'Page') + ' — FAQs');
            $('#pf-title').val(d.faq_title || '');
            $('#pf-sub-title').val(d.faq_sub_title || '');
            let faqs = d.faqs || [];
            $('#pageFaqTable tbody').html(faqs.length ? faqs.map((f, i) => pageFaqRow(i, f.question, f.answer)).join('') : pageFaqRow(0, '', ''));
            bootstrap.Modal.getOrCreateInstance(document.getElementById('pageFaqModal')).show();
        }).fail(function () { toastr.error('Failed to load FAQs.'); });
    });

    $('#pageAddFaqRow').on('click', function () {
        let idx = $('#pageFaqTable tbody tr').length;
        $('#pageFaqTable tbody').append(pageFaqRow(idx, '', ''));
    });

    $(document).on('click', '#pageFaqTable .rm-row', function () {
        $(this).closest('tr').remove();
        pageReindex('#pageFaqTable', 'faqs', ['question', 'answer']);
    });

    $('#pageFaqForm').on('submit', function (e) {
        e.preventDefault();
        pageReindex('#pageFaqTable', 'faqs', ['question', 'answer']);
        let id = $(this).attr('data-id');
        $.ajax({
            url: '{{ url("admin/experience-pages") }}/' + id + '/section', type: 'PUT', data: $(this).serialize(),
        }).done(function (r) {
            toastr.success(r.message || 'Saved!');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('pageFaqModal')).hide();
        }).fail(function () { toastr.error('Failed to save FAQs.'); });
    });

    $(document).on('click', '.btn-page-meta', function () {
        let id = $(this).data('id');
        $.get('{{ url("admin/experience-pages") }}/' + id).done(function (d) {
            $('#pageMetaForm').attr('data-id', id);
            $('#pm-modal-title').text((d.subject?.name || 'Page') + ' — SEO Meta');
            $('#pm-meta-title').val(d.meta_title || '');
            $('#pm-meta-desc').val(d.meta_description || '');
            $('#pm-meta-keywords').val(d.meta_keywords || '');
            $('#pm-h1-heading').val(d.h1_heading || '');
            $('#pm-meta-details').val(d.meta_details || '');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('pageMetaModal')).show();
        }).fail(function () { toastr.error('Failed to load meta.'); });
    });

    $('#pageMetaForm').on('submit', function (e) {
        e.preventDefault();
        let id = $(this).attr('data-id');
        $.ajax({
            url: '{{ url("admin/experience-pages") }}/' + id + '/section', type: 'PUT', data: $(this).serialize(),
        }).done(function (r) {
            toastr.success(r.message || 'Saved!');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('pageMetaModal')).hide();
        }).fail(function () { toastr.error('Failed to save meta.'); });
    });

    $(document).on('change', '.page-status', function () {
        $.ajax({ url: $(this).data('url'), type: 'POST', data: { _token: '{{ csrf_token() }}' },
            success: function () { toastr.success('Status updated'); }, error: function () { toastr.error('Failed to update.'); } });
    });

    $(document).on('change', '.page-popular', function () {
        $.ajax({ url: $(this).data('url'), type: 'POST', data: { _token: '{{ csrf_token() }}' },
            success: function () { toastr.success('Popular status updated'); }, error: function () { toastr.error('Failed to update.'); } });
    });

});
</script>
@endsection
