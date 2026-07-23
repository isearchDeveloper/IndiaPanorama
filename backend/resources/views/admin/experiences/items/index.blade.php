@section('title', 'Manage Experiences')
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
            <h2 class="h4 mb-0 fw-bold"><i class="fas fa-map-marker-alt me-2 text-primary"></i>Manage Experiences</h2>
            <small class="text-muted">Individual named places/experiences (e.g. Attukad Waterfalls, Periyar Wildlife Sanctuary).</small>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.experience-subcategories.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to Subcategories
            </a>
            <a href="{{ route('admin.experience-pages.index') }}" class="btn btn-outline-info">
                <i class="fas fa-map me-1"></i>Manage States
            </a>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addExperienceModal">
                <i class="fas fa-plus me-2"></i>Add Experience
            </button>
        </div>
    </div>

    <div class="mb-3 d-flex gap-2">
        <input type="text" id="exSearch" class="form-control" style="max-width:280px;" placeholder="Search title/slug..." value="{{ request('search') }}">
        <select id="exFilterCategory" class="form-select" style="max-width:220px;">
            <option value="">All Categories</option>
            @foreach($categories as $cat)
            <option value="{{ $cat->id }}" {{ (string)request('category_id') === (string)$cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
        </select>
        <select id="exFilterSubcategory" class="form-select" style="max-width:280px;">
            <option value="">All Subcategories</option>
            @foreach($subcategories as $sub)
            <option value="{{ $sub->id }}" {{ (string)request('subcategory_id') === (string)$sub->id ? 'selected' : '' }}>{{ $sub->category->name }} — {{ $sub->name }}</option>
            @endforeach
        </select>
    </div>

    <div id="exTableWrapper">
        @include('admin.experiences.items._table')
    </div>

</div>
@endsection

@section('modal')
{{-- ══════════════ ADD EXPERIENCE MODAL ══════════════ --}}
<div class="modal fade" id="addExperienceModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Add Experience</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="addExperienceForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Category <span class="text-danger">*</span></label>
                        <select name="category_id" id="add-ex-category" class="form-select" required>
                            <option value="">Select</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subcategory <small class="text-muted">(optional)</small></label>
                        <select name="subcategory_id" id="add-ex-subcategory" class="form-select" disabled>
                            <option value="">Select a category first</option>
                        </select>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">State <span class="text-danger">*</span></label>
                            <select name="state_id" id="add-ex-state" class="form-select" required>
                                <option value="">Select</option>
                                @foreach($states as $st)
                                <option value="{{ $st->id }}">{{ $st->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">City <small class="text-muted">(optional)</small></label>
                            <select name="location_id" id="add-ex-city" class="form-select">
                                <option value="">Select a state first</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 mt-3">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tagline</label>
                        <input type="text" name="tagline" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" id="add-ex-description" class="form-control tinymce" rows="4"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Images <small class="text-muted">(first image = banner)</small></label>
                        <x-media-gallery-picker name="gallery_images" picker-id="add_experience_gallery" label="" folder="experiences/gallery" />
                        <div class="text-danger small mt-1 add-experience-error" style="display:none"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Add Experience</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════ EDIT EXPERIENCE MODAL ══════════════ --}}
<div class="modal fade" id="editExperienceModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Experience</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editExperienceForm">
                @csrf
                <input type="hidden" name="_method" value="PUT">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Category <span class="text-danger">*</span></label>
                        <select name="category_id" id="eex-category" class="form-select" required>
                            <option value="">Select</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subcategory <small class="text-muted">(optional)</small></label>
                        <select name="subcategory_id" id="eex-subcategory" class="form-select">
                            <option value="">— None (category only) —</option>
                        </select>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">State <span class="text-danger">*</span></label>
                            <select name="state_id" id="edit-ex-state" class="form-select" required>
                                @foreach($states as $st)
                                <option value="{{ $st->id }}">{{ $st->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">City <small class="text-muted">(optional)</small></label>
                            <select name="location_id" id="edit-ex-city" class="form-select"></select>
                        </div>
                    </div>
                    <div class="mb-3 mt-3">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="eex-title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tagline</label>
                        <input type="text" name="tagline" id="eex-tagline" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" id="eex-description" class="form-control tinymce" rows="4"></textarea>
                    </div>
                    <div class="mb-3">
                        <x-media-picker name="banner_image" picker-id="eex_banner_image" label="Banner Image" folder="experiences/gallery" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Banner Image Alt</label>
                        <input type="text" name="banner_image_alt" id="eex-banner-alt" class="form-control">
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

{{-- ══════════════ GALLERY & QUICK INFO MODAL ══════════════ --}}
<div class="modal fade" id="experienceGalleryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white" style="background:#0891b2;">
                <h5 class="modal-title"><i class="fas fa-images me-2"></i><span id="eg-modal-title">Experience — Gallery &amp; Quick Info</span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3" id="eg-gallery-wrapper"></div>
                <form id="experienceAddImageForm" class="mb-4">
                    @csrf
                    <x-media-gallery-picker name="gallery_images" picker-id="exp_gallery_add" label="" folder="experiences/gallery" />
                </form>
                <hr>
                <h6 class="text-muted">Quick Info <small>(shown on the detail page)</small></h6>
                <form id="experienceQuickInfoForm">
                    @csrf
                    <input type="hidden" name="section" value="quick_info">
                    <table class="table" id="egQiTable">
                        <thead><tr><th>Label</th><th>Value</th><th width="40"></th></tr></thead>
                        <tbody></tbody>
                    </table>
                    <button type="button" class="btn btn-sm btn-outline-success" id="egQiAddRow"><i class="fas fa-plus me-1"></i>Add Item</button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="eg-save-btn"><i class="fas fa-save me-1"></i>Save</button>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════ HIGHLIGHTS MODAL ══════════════ --}}
<div class="modal fade" id="experienceHighlightsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-star me-2"></i><span id="eh-modal-title">Experience — Highlights</span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="experienceHighlightsForm">
                @csrf
                <input type="hidden" name="section" value="highlights">
                <div class="modal-body">
                    <table class="table" id="ehTable">
                        <thead><tr><th>Highlight</th><th width="40"></th></tr></thead>
                        <tbody></tbody>
                    </table>
                    <button type="button" class="btn btn-sm btn-outline-success" id="ehAddRow"><i class="fas fa-plus me-1"></i>Add Highlight</button>
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
<div class="modal fade" id="experienceFaqModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title"><i class="fas fa-question-circle me-2"></i><span id="ef-modal-title">Experience — FAQs</span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="experienceFaqForm">
                @csrf
                <input type="hidden" name="section" value="faqs">
                <div class="modal-body">
                    <table class="table" id="experienceFaqTable">
                        <thead><tr><th>Question</th><th>Answer</th><th width="40"></th></tr></thead>
                        <tbody></tbody>
                    </table>
                    <button type="button" class="btn btn-sm btn-outline-success" id="experienceAddFaqRow"><i class="fas fa-plus me-1"></i>Add FAQ</button>
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
<div class="modal fade" id="experienceMetaModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background:#7c3aed;color:#fff;">
                <h5 class="modal-title"><i class="fas fa-globe me-2"></i><span id="em-modal-title">Experience — SEO Meta</span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="experienceMetaForm">
                @csrf
                <input type="hidden" name="section" value="meta">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Meta Title</label>
                            <input type="text" name="meta_title" id="em-meta-title" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Meta Description</label>
                            <input type="text" name="meta_description" id="em-meta-desc" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Meta Keywords</label>
                            <input type="text" name="meta_keywords" id="em-meta-keywords" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">H1 Heading</label>
                            <input type="text" name="h1_heading" id="em-h1-heading" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Extra Meta Tags</label>
                            <textarea name="meta_details" id="em-meta-details" class="form-control" rows="4"></textarea>
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
let allLocationsForExperience = @json($locations ?? []);
let allSubcategoriesForExperience = @json($subcategories->map(fn ($s) => ['id' => $s->id, 'name' => $s->name, 'category_id' => $s->category_id]));

function exCityOptions(stateId, selectedId) {
    return allLocationsForExperience.filter(l => l.state_id == stateId)
        .map(l => '<option value="' + l.id + '"' + (l.id == selectedId ? ' selected' : '') + '>' + l.name + '</option>').join('');
}

function exSubcategoryOptionsForCategory(categoryId) {
    return allSubcategoriesForExperience.filter(s => s.category_id == categoryId);
}

function renderAddExSubcategories(categoryId, selectedId) {
    let subs = exSubcategoryOptionsForCategory(categoryId);
    $('#add-ex-subcategory').prop('disabled', false).html('<option value="">— None (category only) —</option>' +
        subs.map(s => '<option value="' + s.id + '"' + (s.id == selectedId ? ' selected' : '') + '>' + s.name + '</option>').join(''));
}

function ehRow(idx, text) {
    return '<tr>' +
        '<td><input type="text" name="highlights[' + idx + ']" value="' + (text || '').replace(/"/g, '&quot;') + '" class="form-control" placeholder="e.g. Boat cruise on Periyar Lake"></td>' +
        '<td><button type="button" class="btn btn-sm btn-outline-danger rm-row"><i class="fas fa-trash"></i></button></td>' +
        '</tr>';
}

function egQiRow(idx, label, value) {
    return '<tr>' +
        '<td><input type="text" name="quick_info[' + idx + '][label]" value="' + (label || '').replace(/"/g, '&quot;') + '" class="form-control" placeholder="e.g. Best Time"></td>' +
        '<td><input type="text" name="quick_info[' + idx + '][value]" value="' + (value || '').replace(/"/g, '&quot;') + '" class="form-control" placeholder="e.g. October to June"></td>' +
        '<td><button type="button" class="btn btn-sm btn-outline-danger rm-row"><i class="fas fa-trash"></i></button></td>' +
        '</tr>';
}

function efFaqRow(idx, question, answer) {
    question = (question || '').replace(/"/g, '&quot;');
    return '<tr>' +
        '<td><input type="text" name="faqs[' + idx + '][question]" value="' + question + '" class="form-control" required></td>' +
        '<td><textarea name="faqs[' + idx + '][answer]" class="form-control">' + (answer || '') + '</textarea></td>' +
        '<td><button type="button" class="btn btn-sm btn-outline-danger rm-row"><i class="fas fa-trash"></i></button></td>' +
        '</tr>';
}

function exReindex(tableSel, prefix, fields) {
    $(tableSel + ' tbody tr').each(function (i) {
        let $row = $(this);
        if (fields) {
            fields.forEach(function (f) {
                $row.find('[name$="[' + f + ']"]').attr('name', prefix + '[' + i + '][' + f + ']');
            });
        } else {
            $row.find('input[name^="' + prefix + '["]').attr('name', prefix + '[' + i + ']');
        }
    });
}

function renderExperienceGallery(images) {
    let html = (images || []).map(function (img) {
        return '<div class="col-md-3 mb-3" id="ex-gallery-image-' + img.id + '">' +
            '<div class="card">' +
                '<img src="' + s3BaseUrl + img.image + '" class="card-img-top" style="height:120px;object-fit:cover;">' +
                '<div class="card-body p-2">' +
                    '<button type="button" class="btn btn-sm btn-outline-danger w-100 delete-experience-gallery-image" data-id="' + img.id + '" data-url="' + expGalleryBaseUrl + '/' + img.id + '"><i class="fas fa-trash"></i></button>' +
                '</div>' +
            '</div>' +
        '</div>';
    }).join('');
    $('#eg-gallery-wrapper').html(html || '<p class="text-muted">No images yet.</p>');
}

const expGalleryBaseUrl = '{{ url("admin/experiences/gallery") }}';

$(document).ready(function () {

    function reload() {
        showAjaxLoader($('#exTableWrapper'));
        $.get('{{ route("admin.experiences.index") }}', {
            search: $('#exSearch').val(),
            category_id: $('#exFilterCategory').val(),
            subcategory_id: $('#exFilterSubcategory').val(),
            ajax: 1
        })
            .done(function (res) {
                $('#exTableWrapper').html(res.html);
                if (typeof window.initSwitchery === 'function') window.initSwitchery();
            })
            .fail(function () { hideAjaxLoader($('#exTableWrapper')); toastr.error('Search failed.'); });
    }

    let exSearchTimer;
    $('#exSearch').on('input', function () { clearTimeout(exSearchTimer); exSearchTimer = setTimeout(reload, 300); });
    $('#exFilterCategory').on('change', function () { $('#exFilterSubcategory').val(''); reload(); });
    $('#exFilterSubcategory').on('change', reload);

    $('#add-ex-category').on('change', function () {
        renderAddExSubcategories($(this).val(), null);
    });

    $('#add-ex-state').on('change', function () {
        $('#add-ex-city').html('<option value="">— None —</option>' + exCityOptions($(this).val(), null));
    });
    $('#edit-ex-state').on('change', function () {
        $('#edit-ex-city').html('<option value="">— None —</option>' + exCityOptions($(this).val(), null));
    });

    // ── Add Experience ───────────────────────────────────────────────────────
    $('#addExperienceForm').on('submit', function (e) {
        e.preventDefault();
        $('.add-experience-error').hide().text('');
        if (typeof tinymce !== 'undefined') tinymce.triggerSave();
        let fd = new FormData(this);
        $.ajax({
            url: '{{ route("admin.experiences.store") }}', type: 'POST', data: fd,
            processData: false, contentType: false,
        }).done(function (r) {
            toastr.success(r.message || 'Experience created.');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('addExperienceModal')).hide();
            document.getElementById('addExperienceForm').reset();
            $('#add-ex-subcategory').prop('disabled', true).html('<option value="">Select a category first</option>');
            if (typeof window.resetMediaGalleryPicker === 'function') {
                window.resetMediaGalleryPicker('add_experience_gallery');
            }
            if (typeof tinymce !== 'undefined' && tinymce.get('add-ex-description')) {
                tinymce.get('add-ex-description').setContent('');
            }
            reload();
        }).fail(function (xhr) {
            if (xhr.status === 422 && xhr.responseJSON.errors) {
                $('.add-experience-error').text(Object.values(xhr.responseJSON.errors)[0][0]).show();
            } else {
                toastr.error('Failed to create experience.');
            }
        });
    });

    // ── Edit Experience ──────────────────────────────────────────────────────
    function renderEditExSubcategories(categoryId, selectedId) {
        let subs = exSubcategoryOptionsForCategory(categoryId);
        $('#eex-subcategory').html('<option value="">— None (category only) —</option>' +
            subs.map(s => '<option value="' + s.id + '"' + (s.id == selectedId ? ' selected' : '') + '>' + s.name + '</option>').join(''));
    }

    $('#eex-category').on('change', function () {
        renderEditExSubcategories($(this).val(), null);
    });

    $(document).on('click', '.btn-edit-experience', function () {
        let id = $(this).data('id');
        $.get('{{ url("admin/experiences") }}/' + id).done(function (d) {
            $('#editExperienceForm').attr('data-id', d.id);
            $('#eex-category').val(d.category_id);
            renderEditExSubcategories(d.category_id, d.subcategory_id);
            $('#edit-ex-state').val(d.state_id).trigger('change');
            setTimeout(() => $('#edit-ex-city').val(d.location_id), 50);
            $('#eex-title').val(d.title || '');
            $('#eex-tagline').val(d.tagline || '');
            let description = d.description || '';
            $('#eex-description').val(description);
            let currentBanner = (d.gallery_images && d.gallery_images.length) ? d.gallery_images[0] : null;
            $('#eex-banner-alt').val(currentBanner ? (currentBanner.image_alt || '') : '');
            if (typeof window.setMediaPickerValue === 'function') {
                window.setMediaPickerValue('eex_banner_image', d.banner_image, d.banner_image ? (s3BaseUrl + d.banner_image) : null);
            }
            $('#editExperienceModal').one('shown.bs.modal', function () {
                if (typeof tinymce !== 'undefined' && tinymce.get('eex-description')) {
                    tinymce.get('eex-description').setContent(description);
                }
            });
            bootstrap.Modal.getOrCreateInstance(document.getElementById('editExperienceModal')).show();
        }).fail(function () { toastr.error('Failed to load experience.'); });
    });

    $('#editExperienceForm').on('submit', function (e) {
        e.preventDefault();
        if (typeof tinymce !== 'undefined') tinymce.triggerSave();
        let id = $(this).attr('data-id');
        $.ajax({
            url: '{{ url("admin/experiences") }}/' + id, type: 'PUT', data: $(this).serialize(),
        }).done(function (r) {
            toastr.success(r.message || 'Experience updated.');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('editExperienceModal')).hide();
            reload();
        }).fail(function () { toastr.error('Failed to update experience.'); });
    });

    // ── Gallery & Quick Info Modal ───────────────────────────────────────────
    $(document).on('click', '.btn-experience-gallery', function () {
        let id = $(this).data('id');
        $('#experienceGalleryModal').attr('data-id', id);
        $('#experienceAddImageForm').attr('data-id', id);
        $('#experienceQuickInfoForm').attr('data-id', id);
        $.get('{{ url("admin/experiences") }}/' + id).done(function (d) {
            $('#eg-modal-title').text(d.title + ' — Gallery & Quick Info');
            renderExperienceGallery(d.gallery_images);
            let qiRows = d.quick_infos || [];
            $('#egQiTable tbody').html(qiRows.length ? qiRows.map((r, i) => egQiRow(i, r.label, r.value)).join('') : egQiRow(0, '', ''));
            bootstrap.Modal.getOrCreateInstance(document.getElementById('experienceGalleryModal')).show();
        }).fail(function () { toastr.error('Failed to load experience.'); });
    });

    $('#egQiAddRow').on('click', function () {
        let idx = $('#egQiTable tbody tr').length;
        $('#egQiTable tbody').append(egQiRow(idx, '', ''));
    });

    $(document).on('click', '#egQiTable .rm-row', function () {
        $(this).closest('tr').remove();
        exReindex('#egQiTable', 'quick_info', ['label', 'value']);
    });

    $('#experienceAddImageForm, #experienceQuickInfoForm').on('submit', function (e) { e.preventDefault(); });

    $('#eg-save-btn').on('click', function () {
        let id = $('#experienceGalleryModal').attr('data-id');
        let hasPendingImages = !!$('#experienceAddImageForm .media-gallery-picker[data-field-name="exp_gallery_add"] .mgp-item').length;

        let imageSave = hasPendingImages
            ? $.ajax({
                url: '{{ url("admin/experiences") }}/' + id + '/gallery', type: 'POST', data: $('#experienceAddImageForm').serialize(),
            }).done(function (r) {
                renderExperienceGallery((r.images || []).concat($('#eg-gallery-wrapper').data('current') || []));
                document.getElementById('experienceAddImageForm').reset();
                if (typeof window.resetMediaGalleryPicker === 'function') {
                    window.resetMediaGalleryPicker('exp_gallery_add');
                }
            })
            : $.Deferred().resolve().promise();

        exReindex('#egQiTable', 'quick_info', ['label', 'value']);
        let quickInfoSave = $.ajax({
            url: '{{ url("admin/experiences") }}/' + id + '/section', type: 'PUT', data: $('#experienceQuickInfoForm').serialize(),
        });

        $.when(imageSave, quickInfoSave).done(function () {
            toastr.success('Saved!');
        }).fail(function () {
            toastr.error('Failed to save changes.');
        });
    });

    $(document).on('click', '.delete-experience-gallery-image', function () {
        let btn = $(this);
        Swal.fire({ title: 'Delete Image?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc3545', confirmButtonText: 'Yes, delete' })
            .then(function (r) {
                if (r.isConfirmed) {
                    $.ajax({ url: btn.data('url'), type: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
                        .done(function () { $('#ex-gallery-image-' + btn.data('id')).remove(); toastr.success('Image removed.'); })
                        .fail(function () { toastr.error('Failed to remove image.'); });
                }
            });
    });

    // ── Highlights Modal ─────────────────────────────────────────────────────
    $(document).on('click', '.btn-experience-highlights', function () {
        let id = $(this).data('id');
        $.get('{{ url("admin/experiences") }}/' + id).done(function (d) {
            $('#experienceHighlightsForm').attr('data-id', id);
            $('#eh-modal-title').text(d.title + ' — Highlights');
            let rows = d.highlights || [];
            $('#ehTable tbody').html(rows.length ? rows.map((r, i) => ehRow(i, r.text)).join('') : ehRow(0, ''));
            bootstrap.Modal.getOrCreateInstance(document.getElementById('experienceHighlightsModal')).show();
        }).fail(function () { toastr.error('Failed to load highlights.'); });
    });

    $('#ehAddRow').on('click', function () {
        let idx = $('#ehTable tbody tr').length;
        $('#ehTable tbody').append(ehRow(idx, ''));
    });

    $(document).on('click', '#ehTable .rm-row', function () {
        $(this).closest('tr').remove();
        exReindex('#ehTable', 'highlights', null);
    });

    $('#experienceHighlightsForm').on('submit', function (e) {
        e.preventDefault();
        exReindex('#ehTable', 'highlights', null);
        let id = $(this).attr('data-id');
        $.ajax({
            url: '{{ url("admin/experiences") }}/' + id + '/section', type: 'PUT', data: $(this).serialize(),
        }).done(function (r) {
            toastr.success(r.message || 'Saved!');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('experienceHighlightsModal')).hide();
        }).fail(function () { toastr.error('Failed to save highlights.'); });
    });

    // ── FAQ Modal ────────────────────────────────────────────────────────────
    $(document).on('click', '.btn-experience-faq', function () {
        let id = $(this).data('id');
        $.get('{{ url("admin/experiences") }}/' + id).done(function (d) {
            $('#experienceFaqForm').attr('data-id', id);
            $('#ef-modal-title').text(d.title + ' — FAQs');
            let faqs = d.faqs || [];
            $('#experienceFaqTable tbody').html(faqs.length ? faqs.map((f, i) => efFaqRow(i, f.question, f.answer)).join('') : efFaqRow(0, '', ''));
            bootstrap.Modal.getOrCreateInstance(document.getElementById('experienceFaqModal')).show();
        }).fail(function () { toastr.error('Failed to load FAQs.'); });
    });

    $('#experienceAddFaqRow').on('click', function () {
        let idx = $('#experienceFaqTable tbody tr').length;
        $('#experienceFaqTable tbody').append(efFaqRow(idx, '', ''));
    });

    $(document).on('click', '#experienceFaqTable .rm-row', function () {
        $(this).closest('tr').remove();
        exReindex('#experienceFaqTable', 'faqs', ['question', 'answer']);
    });

    $('#experienceFaqForm').on('submit', function (e) {
        e.preventDefault();
        exReindex('#experienceFaqTable', 'faqs', ['question', 'answer']);
        let id = $(this).attr('data-id');
        $.ajax({
            url: '{{ url("admin/experiences") }}/' + id + '/section', type: 'PUT', data: $(this).serialize(),
        }).done(function (r) {
            toastr.success(r.message || 'Saved!');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('experienceFaqModal')).hide();
        }).fail(function () { toastr.error('Failed to save FAQs.'); });
    });

    // ── SEO Meta Modal ───────────────────────────────────────────────────────
    $(document).on('click', '.btn-experience-meta', function () {
        let id = $(this).data('id');
        $.get('{{ url("admin/experiences") }}/' + id).done(function (d) {
            $('#experienceMetaForm').attr('data-id', id);
            $('#em-modal-title').text(d.title + ' — SEO Meta');
            $('#em-meta-title').val(d.meta_title || '');
            $('#em-meta-desc').val(d.meta_description || '');
            $('#em-meta-keywords').val(d.meta_keywords || '');
            $('#em-h1-heading').val(d.h1_heading || '');
            $('#em-meta-details').val(d.meta_details || '');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('experienceMetaModal')).show();
        }).fail(function () { toastr.error('Failed to load meta.'); });
    });

    $('#experienceMetaForm').on('submit', function (e) {
        e.preventDefault();
        let id = $(this).attr('data-id');
        $.ajax({
            url: '{{ url("admin/experiences") }}/' + id + '/section', type: 'PUT', data: $(this).serialize(),
        }).done(function (r) {
            toastr.success(r.message || 'Saved!');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('experienceMetaModal')).hide();
        }).fail(function () { toastr.error('Failed to save meta.'); });
    });

    // ── Status/Popular toggle & Delete ────────────────────────────────────────
    $(document).on('change', '.experience-status', function () {
        $.ajax({
            url: $(this).data('url'), type: 'POST', data: { _token: '{{ csrf_token() }}' },
            success: function () { toastr.success('Status updated'); },
            error: function () { toastr.error('Failed to update status.'); }
        });
    });

    $(document).on('change', '.experience-popular', function () {
        $.ajax({
            url: $(this).data('url'), type: 'POST', data: { _token: '{{ csrf_token() }}' },
            success: function () { toastr.success('Popular updated'); },
            error: function () { toastr.error('Failed to update popular.'); }
        });
    });

    $(document).on('click', '.delete-experience', function () {
        let btn = $(this);
        let row = btn.closest('tr');
        Swal.fire({
            title: 'Are you sure?', text: 'This will permanently delete this experience!',
            icon: 'warning', showCancelButton: true,
            confirmButtonColor: '#e3342f', cancelButtonColor: '#6c757d', confirmButtonText: 'Yes, delete it',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: btn.data('url'), type: 'DELETE', data: { _token: '{{ csrf_token() }}' },
                    success: function (res) { if (res.status) { row.remove(); toastr.success('Experience deleted.'); } },
                    error: function () { toastr.error('Failed to delete experience.'); }
                });
            }
        });
    });

});
</script>
@endsection
