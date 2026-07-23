@section('title', 'Manage Activity')
@extends('layouts.app')

@push('style')
<style>
    .btn-outline-orange { color: #1d4ed8; background-color: transparent; border: 1px solid #2563eb; }
    .btn-outline-orange:hover { color: #fff; background-color: #2563eb; border-color: #2563eb; }
    .btn-outline-purple { color: #6d28d9; background-color: transparent; border: 1px solid #7c3aed; }
    .btn-outline-purple:hover { color: #fff; background-color: #7c3aed; border-color: #7c3aed; }
    .modal-header-orange { background: linear-gradient(135deg, #2563eb, #1d4ed8); color: #fff; }
    .modal-header-purple { background: linear-gradient(135deg, #8b5cf6, #6d28d9); color: #fff; }
    #tac-gallery-grid img { width: 100%; height: 90px; object-fit: cover; border-radius: 6px; }
</style>
@endpush

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-0 fw-bold"><i class="fas fa-person-hiking me-2 text-primary"></i>Manage Activity</h2>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.tourist-activity-pages.index') }}" class="btn btn-outline-orange">
                <i class="fas fa-map me-2"></i>State / City Pages
            </a>
            <a href="{{ route('admin.tourist-activities.setting.index') }}" class="btn btn-outline-purple">
                <i class="fas fa-globe me-2"></i>Root Page Setting
            </a>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTacModal">
                <i class="fas fa-plus me-2"></i>Add Activity
            </button>
        </div>
    </div>

    <div class="mb-3">
        <div class="position-relative" style="max-width:320px;">
            <i class="fas fa-search position-absolute" style="left:12px; top:50%; transform:translateY(-50%); color:#94a3b8; font-size:.85rem;"></i>
            <input type="text" id="tacSearch" class="form-control" style="padding-left:32px;"
                   placeholder="Search activity..." value="{{ request('search') }}">
        </div>
    </div>

    <div class="tab-list mb-3">
        <ul>
            <li><a href="javascript:void(0);" class="tab-link @if($status == 'all') active @endif" data-status="all">All ({{ $allCount }})</a></li>
            <li><a href="javascript:void(0);" class="tab-link @if($status == 'active') active @endif" data-status="active">Active ({{ $activeCount }})</a></li>
            <li><a href="javascript:void(0);" class="tab-link @if($status == 'inactive') active @endif" data-status="inactive">Inactive ({{ $inactiveCount }})</a></li>
        </ul>
    </div>

    <div id="tacTableWrapper">
        @include('admin.tourist-activities._table')
    </div>

</div>
@endsection

@section('modal')

{{-- ══════════════ ADD ACTIVITY MODAL ══════════════ --}}
<div class="modal fade" id="addTacModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Add Tourist Activity</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="addTacForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Activity Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="add-tac-name" class="form-control" placeholder="e.g. Houseboat Cruise in Alleppey" required
                                   data-slug-check="tourist-activities" data-slug-submit="#add-tac-submit-btn" data-slug-suffix="activity">
                            <div class="text-danger small mt-1 name-error" style="display:none"></div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Slug <small class="text-muted">(auto-generated)</small></label>
                            <input type="text" name="slug" id="add-tac-slug" class="form-control bg-light"
                                   placeholder="Auto generated from activity name" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">State <span class="text-danger">*</span></label>
                            <select name="state_id" id="add-tac-state" class="form-select" required>
                                <option value="">— Select State —</option>
                                @foreach($states as $state)
                                <option value="{{ $state->id }}">{{ $state->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">City <span class="text-danger">*</span></label>
                            <select name="location_id" id="add-tac-location" class="form-select" required>
                                <option value="">— Select City —</option>
                                @foreach($locations as $loc)
                                <option value="{{ $loc->id }}" data-state="{{ $loc->state_id }}">{{ $loc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Tagline <small class="text-muted">(short subtitle under banner title)</small></label>
                            <input type="text" name="tagline" id="add-tac-tagline" class="form-control" placeholder="e.g. Glide Through Kerala's Backwaters">
                        </div>
                        <div class="col-md-6">
                            <x-media-picker name="banner_image" picker-id="tac_banner_add" label="Banner Image" folder="tourist-activities" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Banner Image Alt</label>
                            <input type="text" name="banner_image_alt" id="add-tac-banner-alt" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Short Description</label>
                            <textarea name="short_description" id="add-tac-short-desc" class="form-control tinymce" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="add-tac-submit-btn"><i class="fas fa-save me-1"></i>Create</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════ EDIT CORE INFO MODAL ══════════════ --}}
<div class="modal fade" id="tacEditModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Core Info</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="tacEditForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" value="PUT">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Activity Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="edit-tac-name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">State <span class="text-danger">*</span></label>
                            <select name="state_id" id="edit-tac-state" class="form-select" required>
                                @foreach($states as $state)
                                <option value="{{ $state->id }}">{{ $state->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">City <span class="text-danger">*</span></label>
                            <select name="location_id" id="edit-tac-location" class="form-select" required>
                                @foreach($locations as $loc)
                                <option value="{{ $loc->id }}" data-state="{{ $loc->state_id }}">{{ $loc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Tagline</label>
                            <input type="text" name="tagline" id="edit-tac-tagline" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <x-media-picker name="banner_image" picker-id="tac_banner_edit" label="Banner Image" folder="tourist-activities" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Banner Image Alt</label>
                            <input type="text" name="banner_image_alt" id="edit-tac-banner-alt" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Short Description</label>
                            <textarea name="short_description" id="edit-tac-short-desc" class="form-control tinymce" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" id="edit-tac-submit-btn"><i class="fas fa-save me-1"></i>Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════ ITINERARY / WHAT TO EXPECT MODAL ══════════════ --}}
<div class="modal fade" id="tacItineraryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="tacItineraryModalTitle"><i class="fas fa-route me-2"></i>Itinerary / What To Expect</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="tacItineraryForm">
                @csrf
                <input type="hidden" name="section" value="itinerary">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Section Title</label>
                        <input type="text" name="itinerary_title" id="tac-itin-title" class="form-control" placeholder="e.g. What Awaits You in Munnar's Tea Gardens">
                    </div>
                    <table class="table" id="tacItineraryTable">
                        <thead><tr><th>Step</th><th>Description</th><th width="40"></th></tr></thead>
                        <tbody></tbody>
                    </table>
                    <button type="button" class="btn btn-sm btn-outline-success" id="tacAddItineraryRow"><i class="fas fa-plus me-1"></i>Add Step</button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success"><i class="fas fa-save me-1"></i>Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════ EXPLORE FINEST ACTIVITIES (EXPERIENCES) MODAL ══════════════ --}}
<div class="modal fade" id="tacExperiencesModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="tacExperiencesModalTitle"><i class="fas fa-compass me-2"></i>Explore Finest Activities</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="tacExperiencesForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="section" value="experiences">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Section Title</label>
                        <input type="text" name="experiences_title" id="tac-exp-title" class="form-control" placeholder="e.g. Explore Munnar's Finest Tea Garden Activities">
                    </div>
                    <table class="table align-middle mb-2" id="tacExperiencesTable">
                        <thead><tr><th style="min-width:220px;">Image</th><th>Title</th><th>Description</th><th width="40"></th></tr></thead>
                        <tbody></tbody>
                    </table>
                    <button type="button" class="btn btn-sm btn-outline-success" id="tacAddExperienceRow"><i class="fas fa-plus me-1"></i>Add Card</button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════ THINGS TO DO MODAL ══════════════ --}}
<div class="modal fade" id="tacThingsToDoModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="tacThingsToDoModalTitle"><i class="fas fa-list-check me-2"></i>Things To Do</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="tacThingsToDoForm">
                @csrf
                <input type="hidden" name="section" value="things_to_do">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Section Title</label>
                        <input type="text" name="things_to_do_title" id="tac-ttd-title" class="form-control" placeholder="e.g. Things To Do At Tea Gardens">
                    </div>
                    <table class="table" id="tacThingsToDoTable">
                        <thead><tr><th>Title</th><th>Description</th><th width="40"></th></tr></thead>
                        <tbody></tbody>
                    </table>
                    <button type="button" class="btn btn-sm btn-outline-success" id="tacAddThingToDoRow"><i class="fas fa-plus me-1"></i>Add Item</button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success"><i class="fas fa-save me-1"></i>Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════ GALLERY MODAL ══════════════ --}}
<div class="modal fade" id="tacGalleryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title" id="tacGalleryModalTitle"><i class="fas fa-images me-2"></i>Gallery</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-2" id="tac-gallery-grid"></div>
                <hr>
                <form id="tacGalleryAddForm">
                    @csrf
                    <div class="row g-2">
                        <div class="col-12">
                            <x-media-gallery-picker name="gallery_images" picker-id="tac_gallery_add" label="" folder="tourist-activities/gallery" />
                        </div>
                    </div>
                    <div class="text-end mt-2">
                        <button type="submit" class="btn btn-secondary" id="tac-gallery-save-btn"><i class="fas fa-save me-1"></i>Save</button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
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
                            <input type="text" name="faq_title" id="tacf-faq-title" class="form-control" placeholder="FAQ's">
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
function tacItineraryRow(idx, title, description) {
    title = (title || '').replace(/"/g, '&quot;');
    return '<tr><td><input type="text" name="itinerary[' + idx + '][title]" value="' + title + '" class="form-control" placeholder="e.g. Pickup from hotel"></td>' +
        '<td><textarea name="itinerary[' + idx + '][description]" class="form-control">' + (description || '') + '</textarea></td>' +
        '<td><button type="button" class="btn btn-sm btn-outline-danger rm-row"><i class="fas fa-trash"></i></button></td></tr>';
}

function tacFaqRow(idx, question, answer) {
    question = (question || '').replace(/"/g, '&quot;');
    return '<tr><td><input type="text" name="faqs[' + idx + '][question]" value="' + question + '" class="form-control" required></td>' +
        '<td><textarea name="faqs[' + idx + '][answer]" class="form-control">' + (answer || '') + '</textarea></td>' +
        '<td><button type="button" class="btn btn-sm btn-outline-danger rm-row"><i class="fas fa-trash"></i></button></td></tr>';
}

function tacThingToDoRow(idx, title, description) {
    title = (title || '').replace(/"/g, '&quot;');
    return '<tr><td><input type="text" name="things_to_do[' + idx + '][title]" value="' + title + '" class="form-control" placeholder="e.g. Tea Heritage"></td>' +
        '<td><textarea name="things_to_do[' + idx + '][description]" class="form-control tac-ttd-editor" rows="2">' + (description || '') + '</textarea></td>' +
        '<td><button type="button" class="btn btn-sm btn-outline-danger rm-row"><i class="fas fa-trash"></i></button></td></tr>';
}

// Things To Do's Description is a rich-text editor, but scoped to its own small
// TinyMCE init (not the shared global `.tinymce` class) — the global one forces a
// fixed 600px height + full menubar/toolbar, which would blow out this compact
// per-row table cell. Kept to the row's current visual size instead.
function tacInitTtdEditor($el) {
    if (!$el || !$el.length || $el.data('tac-ttd-init') || typeof tinymce === 'undefined') return;
    $el.data('tac-ttd-init', true);
    if (!$el.attr('id')) $el.attr('id', 'tac_ttd_' + Math.random().toString(36).slice(2, 9));
    tinymce.init({
        selector: '#' + $el.attr('id'),
        height: 160,
        menubar: false,
        statusbar: false,
        branding: false,
        toolbar: 'bold italic | bullist numlist | link',
        plugins: 'lists link',
        setup: function (editor) {
            editor.on('change keyup', function () { editor.save(); });
            editor.on('init', function () {
                // Existing descriptions load with the cursor (and the iframe's scroll)
                // landed at the END of the text, so the box visually opened scrolled
                // mid-paragraph instead of showing the start — move both back to the top.
                editor.selection.select(editor.getBody(), true);
                editor.selection.collapse(true);
                try { editor.getWin().scrollTo(0, 0); } catch (e) {}

                // Bootstrap's modal focus-trap listens for `focusin` on the document
                // and yanks focus back into the modal whenever it lands somewhere it
                // doesn't recognize as "inside" it — including TinyMCE's own iframe,
                // whose editable area lives in a separate document. Left unguarded,
                // every keystroke's focus event gets fought over, breaking typing.
                // The site-wide TinyMCE init (layouts/app.blade.php) already works
                // around this; this compact editor needs the same guard.
                try {
                    const container = editor.getContainer();
                    const modal = container && container.closest && container.closest('.modal');
                    if (modal && !modal._tiny_focusin_handler) {
                        if (modal.getAttribute('tabindex') === '-1') modal.setAttribute('tabindex', '');
                        const handler = function (ev) {
                            if (ev.target && ev.target.closest && (ev.target.closest('.tox-tinymce') || ev.target.closest('.tox-tinymce-aux') || ev.target.closest('.tox-dialog'))) {
                                ev.stopImmediatePropagation();
                            }
                        };
                        modal.addEventListener('focusin', handler, true);
                        modal._tiny_focusin_handler = handler;
                    }
                    if (!window.__tiny_global_focusin_installed) {
                        window.__tiny_global_focusin_installed = true;
                        document.addEventListener('focusin', function (ev) {
                            if (ev.target && ev.target.closest && (ev.target.closest('.tox-tinymce') || ev.target.closest('.tox-tinymce-aux') || ev.target.closest('.tox-dialog'))) {
                                ev.stopImmediatePropagation();
                            }
                        }, true);
                    }
                } catch (e) {}
            });
        }
    });
}

function tacInitAllTtdEditors(scope) {
    let $scope = scope ? $(scope) : $(document);
    if ($scope.is('.tac-ttd-editor')) tacInitTtdEditor($scope);
    $scope.find('.tac-ttd-editor').each(function () { tacInitTtdEditor($(this)); });
}

function tacDestroyTtdEditors(scope) {
    let $scope = scope ? $(scope) : $(document);
    ($scope.is('.tac-ttd-editor') ? $scope : $scope.find('.tac-ttd-editor')).each(function () {
        let id = $(this).attr('id');
        if (id && typeof tinymce !== 'undefined' && tinymce.get(id)) {
            try { tinymce.remove('#' + id); } catch (e) {}
        }
    });
}

let tacExpPickerSeq = 0;

function tacExperienceRow(title, description, image) {
    title = (title || '').replace(/"/g, '&quot;');
    const pickerId = 'tac_exp_' + (tacExpPickerSeq++);
    const pickerHtml = typeof window.mediaPickerFieldHtml === 'function'
        ? window.mediaPickerFieldHtml('experience_images[]', pickerId, '', 'tourist-activities/experiences')
        : '';
    const row = `
        <tr>
            <td>${pickerHtml}</td>
            <td><input type="text" name="titles[]" class="form-control" value="${title || ''}" placeholder="e.g. Plantation Walks"></td>
            <td>
                <textarea name="descriptions[]" class="form-control">${description || ''}</textarea>
            </td>
            <td class="text-end" style="width:1%;"><button type="button" class="btn btn-sm btn-outline-danger rm-tac-exp-row"><i class="fas fa-trash"></i></button></td>
        </tr>
    `;
    if (image && typeof window.setMediaPickerValue === 'function') {
        setTimeout(function () { window.setMediaPickerValue(pickerId, image, s3BaseUrl + image); }, 0);
    }
    return row;
}

function tacReindex(tableSel, prefix, fields) {
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

function tacShowError(xhr, fallback) {
    if (xhr && xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
        toastr.error(Object.values(xhr.responseJSON.errors)[0][0]);
    } else {
        toastr.error(fallback);
    }
}

function tacFilterLocations(stateSelectId, locationSelectId) {
    let stateId = $('#' + stateSelectId).val();
    let $location = $('#' + locationSelectId);
    let selectedOpt = $location.find('option:selected');

    $location.find('option').each(function () {
        let opt = $(this);
        if (!opt.val()) return;
        opt.toggle(!stateId || opt.data('state') == stateId);
    });

    // If the currently selected city no longer belongs to the chosen state, clear it
    // so a stale, hidden selection can't be submitted with a mismatched state_id.
    if (stateId && selectedOpt.val() && selectedOpt.data('state') != stateId) {
        $location.val('');
    }
}

$(document).ready(function () {

    let tacSearchTimer;
    let tacCurrentStatus = '{{ $status }}';

    function tacFetchActivities() {
        let q = $('#tacSearch').val();
        showAjaxLoader($('#tacTableWrapper'));
        $.get('{{ route("admin.tourist-activities.index") }}', { search: q, status: tacCurrentStatus, ajax: 1 })
            .done(function (res) {
                $('#tacTableWrapper').html(res.html);
                if (typeof window.initSwitchery === 'function') window.initSwitchery();
            })
            .fail(function () { hideAjaxLoader($('#tacTableWrapper')); toastr.error('Search failed.'); });
    }

    $(document).on('click', '.tab-link', function () {
        $('.tab-link').removeClass('active');
        $(this).addClass('active');
        tacCurrentStatus = $(this).data('status');
        tacFetchActivities();
    });

    $('#tacSearch').on('input', function () {
        clearTimeout(tacSearchTimer);
        tacSearchTimer = setTimeout(tacFetchActivities, 300);
    });

    $('#add-tac-state').on('change', function () { tacFilterLocations('add-tac-state', 'add-tac-location'); });
    $('#edit-tac-state').on('change', function () { tacFilterLocations('edit-tac-state', 'edit-tac-location'); });

    // ── Live slug preview (matches the slug the server will actually save) ──
    $('#add-tac-name').on('input', function () {
        var name = $(this).val().trim();
        $('#add-tac-slug').val(name && window.SlugChecker ? SlugChecker.makeSlug(name + ' activity') : '');
    });

    // ── Add Activity ──────────────────────────────────────────────────
    $('#addTacModal').on('hidden.bs.modal', function () {
        $('#addTacForm')[0].reset();
        $('.name-error').hide().text('');
        $('#add-tac-slug').val('');
        if (typeof tinymce !== 'undefined' && tinymce.get('add-tac-short-desc')) {
            tinymce.get('add-tac-short-desc').setContent('');
        }
        if (typeof window.setMediaPickerValue === 'function') {
            window.setMediaPickerValue('tac_banner_add', '', null);
        }
    });

    $('#addTacForm').on('submit', function (e) {
        e.preventDefault();
        if (!$('#addTacForm input[name=banner_image]').val()) {
            toastr.warning('Please choose a Banner Image.');
            return;
        }
        let btn = $('#add-tac-submit-btn');
        btn.prop('disabled', true);
        $('.name-error').hide().text('');
        if (typeof tinymce !== 'undefined') tinymce.triggerSave();
        let fd = new FormData(this);
        $.ajax({ url: '{{ route("admin.tourist-activities.store") }}', type: 'POST', data: fd, processData: false, contentType: false })
            .done(function () {
                toastr.success('Activity created!');
                bootstrap.Modal.getOrCreateInstance(document.getElementById('addTacModal')).hide();
                $('#tacSearch').trigger('input');
            })
            .fail(function (xhr) {
                btn.prop('disabled', false);
                if (xhr.status === 422 && xhr.responseJSON.errors && xhr.responseJSON.errors.name) {
                    $('.name-error').text(xhr.responseJSON.errors.name[0]).show();
                } else {
                    tacShowError(xhr, 'Failed to create activity.');
                }
            });
    });

    // ── Edit Core Info ───────────────────────────────────────────────────
    $(document).on('click', '.btn-tac-edit', function () {
        let updateUrl = $(this).data('update');
        $.get($(this).data('fetch')).done(function (d) {
            $('#edit-tac-name').val(d.name || '').attr('data-slug-exclude', d.id || 0);
            $('#edit-tac-state').val(d.state_id);
            tacFilterLocations('edit-tac-state', 'edit-tac-location');
            $('#edit-tac-location').val(d.location_id);
            $('#edit-tac-tagline').val(d.tagline || '');
            $('#edit-tac-banner-alt').val(d.banner_image_alt || '');
            let shortDesc = d.short_description || '';
            $('#edit-tac-short-desc').val(shortDesc);
            if (typeof window.setMediaPickerValue === 'function') {
                window.setMediaPickerValue('tac_banner_edit', d.banner_image, d.banner_image ? (s3BaseUrl + d.banner_image) : null);
            }
            $('#tacEditForm').attr('data-url', updateUrl);
            $('#tacEditModal').one('shown.bs.modal', function () {
                if (typeof tinymce !== 'undefined' && tinymce.get('edit-tac-short-desc')) {
                    tinymce.get('edit-tac-short-desc').setContent(shortDesc);
                }
            });
            bootstrap.Modal.getOrCreateInstance(document.getElementById('tacEditModal')).show();
        }).fail(function () { toastr.error('Failed to load activity.'); });
    });

    $('#tacEditForm').on('submit', function (e) {
        e.preventDefault();
        if (typeof tinymce !== 'undefined') tinymce.triggerSave();
        let fd = new FormData(this);
        $.ajax({ url: $(this).attr('data-url'), type: 'POST', data: fd, processData: false, contentType: false })
            .done(function (r) {
                toastr.success(r.message || 'Saved!');
                bootstrap.Modal.getOrCreateInstance(document.getElementById('tacEditModal')).hide();
                $('#tacSearch').trigger('input');
            }).fail(function (xhr) { tacShowError(xhr, 'Failed to save.'); });
    });

    // ── Itinerary / What To Expect ──────────────────────────────────────
    $(document).on('click', '.btn-tac-itinerary', function () {
        let updateUrl = $(this).data('update');
        $.get($(this).data('fetch')).done(function (d) {
            $('#tac-itin-title').val(d.itinerary_title || '');
            let items = d.itinerary_steps || [];
            $('#tacItineraryTable tbody').html(
                items.length ? items.map((a, i) => tacItineraryRow(i, a.title, a.description)).join('') : tacItineraryRow(0, '', '')
            );
            $('#tacItineraryForm').attr('data-url', updateUrl);
            bootstrap.Modal.getOrCreateInstance(document.getElementById('tacItineraryModal')).show();
        }).fail(function () { toastr.error('Failed to load.'); });
    });
    $('#tacAddItineraryRow').on('click', function () {
        let idx = $('#tacItineraryTable tbody tr').length;
        $('#tacItineraryTable tbody').append(tacItineraryRow(idx, '', ''));
    });
    $(document).on('click', '#tacItineraryTable .rm-row', function () {
        $(this).closest('tr').remove();
        tacReindex('#tacItineraryTable', 'itinerary', ['title', 'description']);
    });
    $('#tacItineraryForm').on('submit', function (e) {
        e.preventDefault();
        tacReindex('#tacItineraryTable', 'itinerary', ['title', 'description']);
        $.ajax({ url: $(this).attr('data-url'), type: 'PUT', data: $(this).serialize() })
            .done(function (r) { toastr.success(r.message || 'Saved!'); bootstrap.Modal.getOrCreateInstance(document.getElementById('tacItineraryModal')).hide(); })
            .fail(function (xhr) { tacShowError(xhr, 'Failed to save.'); });
    });

    // ── Explore Finest Activities (Experiences) ──────────────────────────
    $(document).on('click', '.btn-tac-experiences', function () {
        let updateUrl = $(this).data('update');
        $.get($(this).data('fetch')).done(function (d) {
            $('#tac-exp-title').val(d.experiences_title || '');
            let items = d.experiences || [];
            $('#tacExperiencesTable tbody').html(
                items.length ? items.map((e) => tacExperienceRow(e.title, e.description, e.image)).join('') : tacExperienceRow('', '', '')
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
        }).done(function (r) { toastr.success(r.message || 'Saved!'); bootstrap.Modal.getOrCreateInstance(document.getElementById('tacExperiencesModal')).hide(); })
            .fail(function (xhr) { tacShowError(xhr, 'Failed to save.'); });
    });

    // ── Things To Do ──────────────────────────────────────────────────────
    $(document).on('click', '.btn-tac-things-to-do', function () {
        let updateUrl = $(this).data('update');
        $.get($(this).data('fetch')).done(function (d) {
            $('#tac-ttd-title').val(d.things_to_do_title || '');
            let items = d.things_to_do || [];
            tacDestroyTtdEditors('#tacThingsToDoTable');
            $('#tacThingsToDoTable tbody').html(
                items.length ? items.map((t, i) => tacThingToDoRow(i, t.title, t.description)).join('') : tacThingToDoRow(0, '', '')
            );
            $('#tacThingsToDoForm').attr('data-url', updateUrl);
            bootstrap.Modal.getOrCreateInstance(document.getElementById('tacThingsToDoModal')).show();
        }).fail(function () { toastr.error('Failed to load.'); });
    });
    $('#tacThingsToDoModal').on('shown.bs.modal', function () { tacInitAllTtdEditors('#tacThingsToDoTable'); });
    $('#tacAddThingToDoRow').on('click', function () {
        let idx = $('#tacThingsToDoTable tbody tr').length;
        $('#tacThingsToDoTable tbody').append(tacThingToDoRow(idx, '', ''));
        tacInitAllTtdEditors('#tacThingsToDoTable');
    });
    $(document).on('click', '#tacThingsToDoTable .rm-row', function () {
        tacDestroyTtdEditors($(this).closest('tr').find('.tac-ttd-editor'));
        $(this).closest('tr').remove();
        tacReindex('#tacThingsToDoTable', 'things_to_do', ['title', 'description']);
    });
    $('#tacThingsToDoForm').on('submit', function (e) {
        e.preventDefault();
        if (typeof tinymce !== 'undefined') tinymce.triggerSave();
        tacReindex('#tacThingsToDoTable', 'things_to_do', ['title', 'description']);
        $.ajax({ url: $(this).attr('data-url'), type: 'PUT', data: $(this).serialize() })
            .done(function (r) { toastr.success(r.message || 'Saved!'); bootstrap.Modal.getOrCreateInstance(document.getElementById('tacThingsToDoModal')).hide(); })
            .fail(function (xhr) { tacShowError(xhr, 'Failed to save.'); });
    });

    // ── Gallery ───────────────────────────────────────────────────────
    function tacGalleryItemHtml(img) {
        let alt = (img.image_alt || '').replace(/"/g, '&quot;');
        return '<div class="col-3" id="tac-gallery-item-' + img.id + '">' +
            '<div class="gallery-img-wrap"><img src="' + s3BaseUrl + img.image + '" class="img-fluid w-100" style="height:90px;object-fit:cover;border-radius:6px;">' +
            '<button type="button" class="gallery-remove-btn delete-tac-gallery-image" data-id="' + img.id + '" title="Remove"><i class="fas fa-times"></i></button></div>' +
            '<input type="text" class="form-control form-control-sm mt-1 tac-gallery-alt-input" data-id="' + img.id + '" value="' + alt + '" placeholder="Alt text...">' +
            '</div>';
    }

    $('#tacGalleryModal').on('hidden.bs.modal', function () { tacResetGalleryPending(); });

    function tacResetGalleryPending() {
        if (typeof window.resetMediaGalleryPicker === 'function') {
            window.resetMediaGalleryPicker('tac_gallery_add');
        }
    }

    $(document).on('click', '.btn-tac-gallery', function () {
        let addUrl = $(this).data('add');
        $.get($(this).data('fetch')).done(function (d) {
            let images = d.gallery_images || [];
            let html = images.map(tacGalleryItemHtml).join('');
            $('#tac-gallery-grid').html(html || '<p class="text-muted">No images yet.</p>');
            $('#tacGalleryAddForm').attr('data-url', addUrl);
            tacResetGalleryPending();
            bootstrap.Modal.getOrCreateInstance(document.getElementById('tacGalleryModal')).show();
        }).fail(function () { toastr.error('Failed to load gallery.'); });
    });

    $('#tacGalleryAddForm').on('submit', function (e) {
        e.preventDefault();
        let $form = $(this);

        if (!$form.find('.media-gallery-picker[data-field-name="tac_gallery_add"] .mgp-item').length) {
            toastr.error('Please add at least one image.');
            return;
        }

        let $btn = $('#tac-gallery-save-btn').prop('disabled', true);
        $.ajax({ url: $form.attr('data-url'), type: 'POST', data: $form.serialize() })
            .done(function (r) {
                toastr.success(r.message || 'Image(s) added.');
                $('#tac-gallery-grid p.text-muted').remove();
                (r.images || []).forEach(function (img) {
                    $('#tac-gallery-grid').append(tacGalleryItemHtml(img));
                });
                tacResetGalleryPending();
            }).fail(function (xhr) { tacShowError(xhr, 'Failed to add image(s).'); })
            .always(function () { $btn.prop('disabled', false); });
    });

    $(document).on('click', '.delete-tac-gallery-image', function () {
        let id = $(this).data('id');
        $.ajax({ url: '{{ url("admin/tourist-activities/gallery") }}/' + id, type: 'DELETE' })
            .done(function () { $('#tac-gallery-item-' + id).remove(); toastr.success('Image removed.'); })
            .fail(function () { toastr.error('Failed to remove image.'); });
    });

    $(document).on('blur', '.tac-gallery-alt-input', function () {
        let $input = $(this), id = $input.data('id'), val = $input.val();
        if (val === $input.data('saved')) return;
        $.ajax({
            url: '{{ url("admin/tourist-activities/gallery") }}/' + id + '/alt',
            type: 'POST', data: { _token: '{{ csrf_token() }}', image_alt: val },
        }).done(function () { $input.data('saved', val); toastr.success('Alt text saved.'); })
            .fail(function () { toastr.error('Failed to save alt text.'); });
    });

    // ── FAQs ──────────────────────────────────────────────────────────
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
    $(document).on('click', '#tacFaqTable .rm-row', function () {
        $(this).closest('tr').remove();
        tacReindex('#tacFaqTable', 'faqs', ['question', 'answer']);
    });
    $('#tacFaqForm').on('submit', function (e) {
        e.preventDefault();
        tacReindex('#tacFaqTable', 'faqs', ['question', 'answer']);
        $.ajax({ url: $(this).attr('data-url'), type: 'PUT', data: $(this).serialize() })
            .done(function (r) { toastr.success(r.message || 'Saved!'); bootstrap.Modal.getOrCreateInstance(document.getElementById('tacFaqModal')).hide(); })
            .fail(function (xhr) { tacShowError(xhr, 'Failed to save FAQs.'); });
    });

    // ── SEO Meta ──────────────────────────────────────────────────────
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
            .done(function (r) { toastr.success(r.message || 'Saved!'); bootstrap.Modal.getOrCreateInstance(document.getElementById('tacMetaModal')).hide(); })
            .fail(function (xhr) { tacShowError(xhr, 'Failed to save meta.'); });
    });

    // ── Status / Popular toggles ─────────────────────────────────────
    $(document).on('change', '.tac-status', function () {
        $.ajax({ url: $(this).data('url'), type: 'POST', data: { _token: '{{ csrf_token() }}' } })
            .done(function () { toastr.success('Status updated'); })
            .fail(function () { toastr.error('Failed to update status.'); });
    });
    $(document).on('change', '.tac-popular', function () {
        $.ajax({ url: $(this).data('url'), type: 'POST', data: { _token: '{{ csrf_token() }}' } })
            .done(function () { toastr.success('Popular flag updated'); })
            .fail(function () { toastr.error('Failed to update popular flag.'); });
    });

    // ── Delete ────────────────────────────────────────────────────────
    $(document).on('click', '.delete-tac', function () {
        let btn = $(this);
        let url = btn.data('url');
        let row = btn.closest('tr');
        Swal.fire({
            title: 'Are you sure?', text: 'This will delete the activity and all its content!', icon: 'warning',
            showCancelButton: true, confirmButtonColor: '#e3342f', cancelButtonColor: '#6c757d', confirmButtonText: 'Yes, delete it',
        }).then((result) => {
            if (result.isConfirmed) {
                btn.find('.spinner-border').removeClass('d-none');
                btn.find('.icon').addClass('d-none');
                $.ajax({ url: url, type: 'DELETE', data: { _token: '{{ csrf_token() }}' } })
                    .done(function (res) { if (res.status) { row.remove(); toastr.success('Activity deleted.'); } })
                    .fail(function () { toastr.error('Delete failed.'); })
                    .always(function () { btn.find('.spinner-border').addClass('d-none'); btn.find('.icon').removeClass('d-none'); });
            }
        });
    });

});
</script>
@endsection
