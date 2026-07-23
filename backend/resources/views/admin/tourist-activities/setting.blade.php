@section('title', 'Tourist Activity Setting')
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
            <h2 class="h4 mb-0 fw-bold"><i class="fas fa-person-hiking me-2 text-primary"></i>Tourist Activity Setting</h2>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.tourist-activity-pages.index') }}" class="btn btn-outline-success">
                <i class="fas fa-map me-1"></i>State / City Pages
            </a>
            <a href="{{ route('admin.tourist-activities.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to Manage Activity
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
                            <th width="320">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="fw-medium">{{ $setting->title ?: 'Tourist Activities' }}</td>
                            <td>
                                <input type="checkbox"
                                       class="js-switch"
                                       id="tacs-status"
                                       {{ $setting->is_active ? 'checked' : '' }}>
                            </td>
                            <td class="text-nowrap">
                                <button class="btn btn-sm btn-outline-orange btn-tacs-settings" title="Banner & Settings">
                                    <i class="fas fa-cog"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-secondary btn-tacs-stats" title="Stats">
                                    <i class="fas fa-chart-simple"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-dark btn-tacs-perfect-for" title="Perfect For">
                                    <i class="fas fa-tags"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger btn-tacs-seasons" title="Seasonal Activities">
                                    <i class="fas fa-cloud-sun"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-info btn-tacs-why-choose" title="Why Choose Us">
                                    <i class="fas fa-heart"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-warning btn-tacs-faq" title="FAQs">
                                    <i class="fas fa-question-circle"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-purple btn-tacs-meta" title="SEO Meta">
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
<div class="modal fade" id="tacsSettingsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header modal-header-orange">
                <h5 class="modal-title"><i class="fas fa-cog me-2"></i>Tourist Activities — Banner & Settings</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="tacsSettingsForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="section" value="banner_settings">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" id="tacs-title" class="form-control" placeholder="Tourist Activities">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Banner Text</label>
                            <textarea name="banner_text" id="tacs-banner-text" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="col-md-6">
                            <x-media-picker name="banner_image" picker-id="tacs_banner" label="Banner Image" folder="tourist-activities" />
                            <div class="text-danger small mt-1 tacs-banner-error" style="display:none"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Banner Image Alt</label>
                            <input type="text" name="banner_image_alt" id="tacs-banner-alt" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Short Description</label>
                            <textarea name="short_description" id="tacs-short-desc" class="form-control tinymce" rows="4"></textarea>
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

{{-- ══════════════ WHY CHOOSE US MODAL (global — reused on every state/city activities page) ══════════════ --}}
<div class="modal fade" id="tacsWhyChooseModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-heart me-2"></i>Why Choose Us <small class="ms-1 opacity-75">(shown on every state &amp; city activities page)</small></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="tacsWhyChooseForm">
                @csrf
                <input type="hidden" name="section" value="why_choose">
                <div class="row g-3 mb-3 px-3 pt-3">
                    <div class="col-md-6">
                        <label class="form-label">Title</label>
                        <input type="text" name="why_choose_title" id="tacs-wc-title" class="form-control" placeholder="Why Choose Activities With Indian Panorama">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Sub Title</label>
                        <textarea name="why_choose_sub_title" id="tacs-wc-sub-title" class="form-control" rows="1"></textarea>
                    </div>
                </div>
                <div class="modal-body">
                    <table class="table" id="tacsWhyChooseTable">
                        <thead><tr><th>Title</th><th>Tagline</th><th width="40"></th></tr></thead>
                        <tbody></tbody>
                    </table>
                    <button type="button" class="btn btn-sm btn-outline-success" id="tacsAddWhyChooseRow"><i class="fas fa-plus me-1"></i>Add Item</button>
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
<div class="modal fade" id="tacsFaqModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title"><i class="fas fa-question-circle me-2"></i>Tourist Activities — FAQs</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="tacsFaqForm">
                @csrf
                <input type="hidden" name="section" value="faqs">
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Section Title</label>
                            <input type="text" name="faq_title" id="tacs-faq-title" class="form-control" placeholder="Frequently Asked Questions">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Section Sub Title</label>
                            <input type="text" name="faq_sub_title" id="tacs-faq-sub-title" class="form-control">
                        </div>
                    </div>
                    <table class="table" id="tacsFaqTable">
                        <thead><tr><th>Question</th><th>Answer</th><th width="40"></th></tr></thead>
                        <tbody></tbody>
                    </table>
                    <button type="button" class="btn btn-sm btn-outline-success" id="tacsAddFaqRow"><i class="fas fa-plus me-1"></i>Add FAQ</button>
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
<div class="modal fade" id="tacsMetaModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header modal-header-purple">
                <h5 class="modal-title"><i class="fas fa-globe me-2"></i>Tourist Activities — SEO Meta</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="tacsMetaForm">
                @csrf
                <input type="hidden" name="section" value="meta">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Meta Title</label>
                            <input type="text" name="meta_title" id="tacs-meta-title" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Meta Description</label>
                            <input type="text" name="meta_description" id="tacs-meta-desc" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Meta Keywords</label>
                            <input type="text" name="meta_keywords" id="tacs-meta-keywords" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">H1 Heading</label>
                            <input type="text" name="h1_heading" id="tacs-h1-heading" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Extra Meta Tags</label>
                            <textarea name="meta_details" id="tacs-meta-details" class="form-control" rows="4"></textarea>
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

{{-- ══════════════ STATS MODAL ══════════════ --}}
<div class="modal fade" id="tacsStatsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title"><i class="fas fa-chart-simple me-2"></i>Stats</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="tacsStatsForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="section" value="stats">
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <x-media-picker name="stats_image" picker-id="tacs_stats" label="Image" folder="tourist-activities/stats" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Image Alt</label>
                            <input type="text" name="stats_image_alt" id="tacs-stats-image-alt" class="form-control">
                        </div>
                    </div>
                    <table class="table" id="tacsStatsTable">
                        <thead><tr><th>Stat</th><th>Label</th><th width="40"></th></tr></thead>
                        <tbody></tbody>
                    </table>
                    <button type="button" class="btn btn-sm btn-outline-success" id="tacsAddStatsRow"><i class="fas fa-plus me-1"></i>Add Stat</button>
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
<div class="modal fade" id="tacsPerfectForModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title"><i class="fas fa-tags me-2"></i>Perfect For</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="tacsPerfectForForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="section" value="perfect_for">
                <div class="modal-body">
                    <table class="table align-middle" id="tacsPerfectForTable">
                        <thead><tr><th style="min-width:160px;">Icon</th><th>Label</th><th width="40"></th></tr></thead>
                        <tbody></tbody>
                    </table>
                    <button type="button" class="btn btn-sm btn-outline-success" id="tacsAddPerfectForRow"><i class="fas fa-plus me-1"></i>Add Label</button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-dark"><i class="fas fa-save me-1"></i>Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════ SEASONAL ACTIVITIES MODAL ══════════════ --}}
<div class="modal fade" id="tacsSeasonsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-cloud-sun me-2"></i>Seasonal Activities You Shouldn't Miss</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="tacsSeasonsForm">
                @csrf
                <input type="hidden" name="section" value="seasons">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Section Title</label>
                        <input type="text" name="seasons_title" id="tacs-seasons-title" class="form-control" placeholder="Seasonal Activities You Shouldn't Miss">
                    </div>
                    <table class="table" id="tacsSeasonsTable">
                        <thead><tr><th>Season</th><th>Period</th><th>Activities <small class="text-muted">(e.g. "Trekking | River Rafting")</small></th><th width="40"></th></tr></thead>
                        <tbody></tbody>
                    </table>
                    <button type="button" class="btn btn-sm btn-outline-success" id="tacsAddSeasonRow"><i class="fas fa-plus me-1"></i>Add Season</button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger"><i class="fas fa-save me-1"></i>Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
function tacsFaqRow(idx, question, answer) {
    question = (question || '').replace(/"/g, '&quot;');
    return '<tr>' +
        '<td><input type="text" name="faqs[' + idx + '][question]" value="' + question + '" class="form-control" required></td>' +
        '<td><textarea name="faqs[' + idx + '][answer]" class="form-control">' + (answer || '') + '</textarea></td>' +
        '<td><button type="button" class="btn btn-sm btn-outline-danger rm-row"><i class="fas fa-trash"></i></button></td>' +
        '</tr>';
}

function tacsWhyChooseRow(idx, title, tagline) {
    title   = (title   || '').replace(/"/g, '&quot;');
    tagline = (tagline || '').replace(/"/g, '&quot;');
    return '<tr>' +
        '<td><input type="text" name="why_chooses[' + idx + '][title]" value="' + title + '" class="form-control" placeholder="Expert Local Guides" required></td>' +
        '<td><input type="text" name="why_chooses[' + idx + '][tagline]" value="' + tagline + '" class="form-control" placeholder="Travel with knowledgeable guides who know every activity inside out"></td>' +
        '<td><button type="button" class="btn btn-sm btn-outline-danger rm-row"><i class="fas fa-trash"></i></button></td>' +
        '</tr>';
}

function tacsStatsRow(stat, label) {
    stat  = (stat  || '').replace(/"/g, '&quot;');
    label = (label || '').replace(/"/g, '&quot;');
    return `
        <tr>
            <td><input type="text" name="stats[]" value="${stat}" class="form-control" placeholder="500+" required></td>
            <td><input type="text" name="labels[]" value="${label}" class="form-control" placeholder="Activities Covered"></td>
            <td class="text-end" style="width:1%;"><button type="button" class="btn btn-sm btn-outline-danger rm-tacs-stat-row"><i class="fas fa-trash"></i></button></td>
        </tr>
    `;
}

let tacsPfPickerSeq = 0;

function tacsPerfectForRow(title, icon) {
    title = (title || '').replace(/"/g, '&quot;');
    const pickerId = 'tacs_pf_' + (tacsPfPickerSeq++);
    const pickerHtml = typeof window.mediaPickerFieldHtml === 'function'
        ? window.mediaPickerFieldHtml('perfect_for_icons[]', pickerId, '', 'tourist-activities/perfect-for')
        : '';
    const row = `
        <tr>
            <td>${pickerHtml}</td>
            <td>
                <input type="text" name="titles[]" class="form-control" value="${title || ''}" placeholder="e.g. Wildlife Lovers">
            </td>
            <td class="text-end" style="width:1%;"><button type="button" class="btn btn-sm btn-outline-danger rm-tacs-pf-row"><i class="fas fa-trash"></i></button></td>
        </tr>
    `;
    if (icon && typeof window.setMediaPickerValue === 'function') {
        setTimeout(function () {
            window.setMediaPickerValue(pickerId, icon, s3BaseUrl + icon);
        }, 0);
    }
    return row;
}

function tacsSeasonRow(idx, seasonLabel, periodText, activitiesText) {
    seasonLabel = (seasonLabel || '').replace(/"/g, '&quot;');
    periodText  = (periodText  || '').replace(/"/g, '&quot;');
    activitiesText = (activitiesText || '').replace(/"/g, '&quot;');
    return '<tr>' +
        '<td><input type="text" name="seasons[' + idx + '][season_label]" value="' + seasonLabel + '" class="form-control" placeholder="Summer"></td>' +
        '<td><input type="text" name="seasons[' + idx + '][period_text]" value="' + periodText + '" class="form-control" placeholder="Mar - May"></td>' +
        '<td><input type="text" name="seasons[' + idx + '][activities_text]" value="' + activitiesText + '" class="form-control" placeholder="Trekking | River Rafting"></td>' +
        '<td><button type="button" class="btn btn-sm btn-outline-danger rm-row"><i class="fas fa-trash"></i></button></td>' +
        '</tr>';
}

function tacsReindex(tableSel, prefix, fields) {
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

function tacsShowError(xhr, fallback) {
    if (xhr && xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
        toastr.error(Object.values(xhr.responseJSON.errors)[0][0]);
    } else {
        toastr.error(fallback);
    }
}

$(document).ready(function () {

    const tacsDataUrl   = '{{ route("admin.tourist-activities.setting.data") }}';
    const tacsUpdateUrl = '{{ route("admin.tourist-activities.setting.update-section") }}';

    $('#tacs-status').on('change', function () {
        $.ajax({
            url: '{{ route("admin.tourist-activities.setting.toggle-status") }}',
            type: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function () { toastr.success('Status updated'); },
            error: function () { toastr.error('Failed to update status.'); }
        });
    });

    // ── Settings Modal ─────────────────────────────────────────────────
    $('.btn-tacs-settings').on('click', function () {
        $.get(tacsDataUrl).done(function (d) {
            $('#tacs-title').val(d.title || '');
            $('#tacs-banner-text').val(d.banner_text || '');
            $('#tacs-banner-alt').val(d.banner_image_alt || '');
            let shortDesc = d.short_description || '';
            $('#tacs-short-desc').val(shortDesc);
            if (typeof window.setMediaPickerValue === 'function') {
                window.setMediaPickerValue('tacs_banner', d.banner_image, d.banner_image ? (s3BaseUrl + d.banner_image) : null);
            }
            $('#tacsSettingsModal').one('shown.bs.modal', function () {
                if (typeof tinymce !== 'undefined' && tinymce.get('tacs-short-desc')) {
                    tinymce.get('tacs-short-desc').setContent(shortDesc);
                }
            });
            bootstrap.Modal.getOrCreateInstance(document.getElementById('tacsSettingsModal')).show();
        }).fail(function () { toastr.error('Failed to load settings.'); });
    });

    $('#tacsSettingsForm').on('submit', function (e) {
        e.preventDefault();
        if (typeof tinymce !== 'undefined') tinymce.triggerSave();
        let fd = new FormData(this);
        fd.append('_method', 'PUT');
        $.ajax({
            url: tacsUpdateUrl, type: 'POST', data: fd,
            processData: false, contentType: false,
        }).done(function (r) {
            toastr.success(r.message || 'Saved!');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('tacsSettingsModal')).hide();
        }).fail(function (xhr) {
            if (xhr.status === 422 && xhr.responseJSON.errors && xhr.responseJSON.errors.banner_image) {
                $('.tacs-banner-error').text(xhr.responseJSON.errors.banner_image[0]).show();
            } else {
                tacsShowError(xhr, 'Failed to save settings.');
            }
        });
    });

    // ── Why Choose Us Modal ───────────────────────────────────────────────
    $('.btn-tacs-why-choose').on('click', function () {
        $.get(tacsDataUrl).done(function (d) {
            $('#tacs-wc-title').val(d.why_choose_title || '');
            $('#tacs-wc-sub-title').val(d.why_choose_sub_title || '');
            let items = d.why_chooses || [];
            $('#tacsWhyChooseTable tbody').html(
                items.length ? items.map((w, i) => tacsWhyChooseRow(i, w.title, w.tagline)).join('') : tacsWhyChooseRow(0, '', '')
            );
            bootstrap.Modal.getOrCreateInstance(document.getElementById('tacsWhyChooseModal')).show();
        }).fail(function () { toastr.error('Failed to load Why Choose Us.'); });
    });

    $('#tacsAddWhyChooseRow').on('click', function () {
        let idx = $('#tacsWhyChooseTable tbody tr').length;
        $('#tacsWhyChooseTable tbody').append(tacsWhyChooseRow(idx, '', ''));
    });

    $('#tacsWhyChooseForm').on('submit', function (e) {
        e.preventDefault();
        tacsReindex('#tacsWhyChooseTable', 'why_chooses', ['title', 'tagline']);
        $.ajax({ url: tacsUpdateUrl, type: 'PUT', data: $(this).serialize() })
            .done(function (r) {
                toastr.success(r.message || 'Saved!');
                bootstrap.Modal.getOrCreateInstance(document.getElementById('tacsWhyChooseModal')).hide();
            }).fail(function (xhr) { tacsShowError(xhr, 'Failed to save Why Choose Us.'); });
    });

    $(document).on('click', '#tacsWhyChooseTable .rm-row', function () {
        $(this).closest('tr').remove();
        tacsReindex('#tacsWhyChooseTable', 'why_chooses', ['title', 'tagline']);
    });

    // ── Stats Modal ─────────────────────────────────────────────────────
    $('.btn-tacs-stats').on('click', function () {
        $.get(tacsDataUrl).done(function (d) {
            $('#tacs-stats-image-alt').val(d.stats_image_alt || '');
            if (typeof window.setMediaPickerValue === 'function') {
                window.setMediaPickerValue('tacs_stats', d.stats_image, d.stats_image ? (s3BaseUrl + d.stats_image) : null);
            }
            let items = d.highlights || [];
            $('#tacsStatsTable tbody').html(
                items.length ? items.map((h) => tacsStatsRow(h.stat, h.label)).join('') : tacsStatsRow('', '')
            );
            bootstrap.Modal.getOrCreateInstance(document.getElementById('tacsStatsModal')).show();
        }).fail(function () { toastr.error('Failed to load Stats.'); });
    });
    $('#tacsAddStatsRow').on('click', function () {
        $('#tacsStatsTable tbody').append(tacsStatsRow('', ''));
    });
    $(document).on('click', '.rm-tacs-stat-row', function () {
        $(this).closest('tr').remove();
    });
    $('#tacsStatsForm').on('submit', function (e) {
        e.preventDefault();
        let fd = new FormData(this);
        fd.append('_method', 'PUT');
        $.ajax({ url: tacsUpdateUrl, type: 'POST', data: fd, processData: false, contentType: false })
            .done(function (r) { toastr.success(r.message || 'Saved!'); bootstrap.Modal.getOrCreateInstance(document.getElementById('tacsStatsModal')).hide(); })
            .fail(function (xhr) { tacsShowError(xhr, 'Failed to save Stats.'); });
    });

    // ── Perfect For Modal ────────────────────────────────────────────────
    $('.btn-tacs-perfect-for').on('click', function () {
        $.get(tacsDataUrl).done(function (d) {
            let items = d.perfect_fors || [];
            $('#tacsPerfectForTable tbody').html(
                items.length ? items.map((p) => tacsPerfectForRow(p.title, p.icon)).join('') : tacsPerfectForRow('', '')
            );
            bootstrap.Modal.getOrCreateInstance(document.getElementById('tacsPerfectForModal')).show();
        }).fail(function () { toastr.error('Failed to load.'); });
    });
    $('#tacsAddPerfectForRow').on('click', function () {
        $('#tacsPerfectForTable tbody').append(tacsPerfectForRow('', ''));
    });
    $(document).on('click', '.rm-tacs-pf-row', function () {
        $(this).closest('tr').remove();
    });
    $('#tacsPerfectForForm').on('submit', function (e) {
        e.preventDefault();
        let fd = new FormData(this);
        fd.append('_method', 'PUT');
        $.ajax({ url: tacsUpdateUrl, type: 'POST', data: fd, processData: false, contentType: false })
            .done(function (r) { toastr.success(r.message || 'Saved!'); bootstrap.Modal.getOrCreateInstance(document.getElementById('tacsPerfectForModal')).hide(); })
            .fail(function (xhr) { tacsShowError(xhr, 'Failed to save.'); });
    });

    // ── Seasonal Activities Modal ───────────────────────────────────────
    $('.btn-tacs-seasons').on('click', function () {
        $.get(tacsDataUrl).done(function (d) {
            $('#tacs-seasons-title').val(d.seasons_title || '');
            let items = d.seasons || [];
            $('#tacsSeasonsTable tbody').html(
                items.length ? items.map((s, i) => tacsSeasonRow(i, s.season_label, s.period_text, s.activities_text)).join('') : tacsSeasonRow(0, '', '', '')
            );
            bootstrap.Modal.getOrCreateInstance(document.getElementById('tacsSeasonsModal')).show();
        }).fail(function () { toastr.error('Failed to load.'); });
    });
    $('#tacsAddSeasonRow').on('click', function () {
        let idx = $('#tacsSeasonsTable tbody tr').length;
        $('#tacsSeasonsTable tbody').append(tacsSeasonRow(idx, '', '', ''));
    });
    $(document).on('click', '#tacsSeasonsTable .rm-row', function () {
        $(this).closest('tr').remove();
        tacsReindex('#tacsSeasonsTable', 'seasons', ['season_label', 'period_text', 'activities_text']);
    });
    $('#tacsSeasonsForm').on('submit', function (e) {
        e.preventDefault();
        tacsReindex('#tacsSeasonsTable', 'seasons', ['season_label', 'period_text', 'activities_text']);
        $.ajax({ url: tacsUpdateUrl, type: 'PUT', data: $(this).serialize() })
            .done(function (r) { toastr.success(r.message || 'Saved!'); bootstrap.Modal.getOrCreateInstance(document.getElementById('tacsSeasonsModal')).hide(); })
            .fail(function (xhr) { tacsShowError(xhr, 'Failed to save.'); });
    });

    // ── FAQ Modal ───────────────────────────────────────────────────────
    $('.btn-tacs-faq').on('click', function () {
        $.get(tacsDataUrl).done(function (d) {
            $('#tacs-faq-title').val(d.faq_title || '');
            $('#tacs-faq-sub-title').val(d.faq_sub_title || '');
            let faqs = d.faqs || [];
            $('#tacsFaqTable tbody').html(
                faqs.length ? faqs.map((f, i) => tacsFaqRow(i, f.question, f.answer)).join('') : tacsFaqRow(0, '', '')
            );
            bootstrap.Modal.getOrCreateInstance(document.getElementById('tacsFaqModal')).show();
        }).fail(function () { toastr.error('Failed to load FAQs.'); });
    });

    $('#tacsAddFaqRow').on('click', function () {
        let idx = $('#tacsFaqTable tbody tr').length;
        $('#tacsFaqTable tbody').append(tacsFaqRow(idx, '', ''));
    });

    $('#tacsFaqForm').on('submit', function (e) {
        e.preventDefault();
        tacsReindex('#tacsFaqTable', 'faqs', ['question', 'answer']);
        $.ajax({ url: tacsUpdateUrl, type: 'PUT', data: $(this).serialize() })
            .done(function (r) {
                toastr.success(r.message || 'Saved!');
                bootstrap.Modal.getOrCreateInstance(document.getElementById('tacsFaqModal')).hide();
            }).fail(function (xhr) { tacsShowError(xhr, 'Failed to save FAQs.'); });
    });

    $(document).on('click', '#tacsFaqTable .rm-row', function () {
        $(this).closest('tr').remove();
        tacsReindex('#tacsFaqTable', 'faqs', ['question', 'answer']);
    });

    // ── SEO Meta Modal ──────────────────────────────────────────────────
    $('.btn-tacs-meta').on('click', function () {
        $.get(tacsDataUrl).done(function (d) {
            $('#tacs-meta-title').val(d.meta_title || '');
            $('#tacs-meta-desc').val(d.meta_description || '');
            $('#tacs-meta-keywords').val(d.meta_keywords || '');
            $('#tacs-h1-heading').val(d.h1_heading || '');
            $('#tacs-meta-details').val(d.meta_details || '');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('tacsMetaModal')).show();
        }).fail(function () { toastr.error('Failed to load meta.'); });
    });

    $('#tacsMetaForm').on('submit', function (e) {
        e.preventDefault();
        $.ajax({ url: tacsUpdateUrl, type: 'PUT', data: $(this).serialize() })
            .done(function (r) {
                toastr.success(r.message || 'Saved!');
                bootstrap.Modal.getOrCreateInstance(document.getElementById('tacsMetaModal')).hide();
            }).fail(function (xhr) { tacsShowError(xhr, 'Failed to save meta.'); });
    });

});
</script>
@endsection
