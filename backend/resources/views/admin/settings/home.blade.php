@extends('layouts.app')
@section('title', 'Home Page CMS')

@push('style')
<style>
/* ══════════════════════════════════════════════════════
   HOME PAGE CMS — shared styles
══════════════════════════════════════════════════════ */
.cms-tab-nav {
    background: #f8fafc;
    border-bottom: 2px solid #e2e8f0;
    padding: 0 4px;
    display: flex;
    flex-wrap: nowrap;
    overflow-x: auto;
    scrollbar-width: none;
    gap: 2px;
}
.cms-tab-nav::-webkit-scrollbar { display: none; }
.cms-tab-btn {
    flex-shrink: 0;
    border: none;
    background: transparent;
    padding: 13px 18px;
    font-size: 12.5px;
    font-weight: 500;
    color: #64748b;
    border-bottom: 3px solid transparent;
    cursor: pointer;
    transition: color .15s, border-color .15s, background .15s;
    white-space: nowrap;
    margin-bottom: -2px;
    border-radius: 0;
}
.cms-tab-btn:hover  { color: #0d6efd; background: #f1f5f9; }
.cms-tab-btn.active { color: #0d6efd; font-weight: 700; border-bottom-color: #2563eb; }
.cms-tab-btn i      { margin-right: 6px; }


/* Section heading editor panel */
.sec-heading-panel {
    background: linear-gradient(135deg, #f8fafc 0%, #f0f4ff 100%);
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    padding: 18px 22px 16px;
    margin-bottom: 24px;
}
.sec-heading-panel .panel-label {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: #94a3b8;
    margin-bottom: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.sec-heading-panel .panel-label i { color: #2563eb; font-size: 13px; }

/* Repeater rows */
.repeater-row {
    display: flex;
    align-items: center;
    gap: 10px;
    background: #fff;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    padding: 12px 16px;
    margin-bottom: 8px;
    transition: border-color .15s, box-shadow .15s;
}
.repeater-row:hover { border-color: #93c5fd; box-shadow: 0 3px 10px rgba(13,110,253,.06); }
.repeater-row.sortable-ghost  { opacity: .3; border: 2px dashed #93c5fd !important; }
.repeater-row.sortable-chosen { box-shadow: 0 8px 24px rgba(13,110,253,.18) !important; }
.rr-handle  { cursor: grab; color: #cbd5e1; font-size: 16px; flex-shrink: 0; }
.rr-handle:hover { color: #0d6efd; }
.rr-icon    { width: 34px; height: 34px; border-radius: 8px; background: #f1f5f9; display: inline-flex; align-items: center; justify-content: center; color: #64748b; font-size: 14px; flex-shrink: 0; }
.rr-body    { flex: 1; min-width: 0; }
.rr-title   { font-size: 13.5px; font-weight: 600; color: #1e293b; margin-bottom: 2px; }
.rr-sub     { font-size: 12px; color: #64748b; }
.rr-actions { display: flex; align-items: center; gap: 8px; flex-shrink: 0; }

/* Mini toggle */
.mini-toggle { position: relative; display: inline-block; width: 38px; height: 21px; }
.mini-toggle input { opacity: 0; width: 0; height: 0; }
.mini-slider {
    position: absolute; inset: 0; border-radius: 12px; background: #e2e8f0;
    transition: background .2s; cursor: pointer;
}
.mini-slider:before {
    content: ''; position: absolute; width: 15px; height: 15px;
    left: 3px; top: 3px; border-radius: 50%; background: #fff;
    box-shadow: 0 1px 3px rgba(0,0,0,.2); transition: transform .2s;
}
.mini-toggle input:checked + .mini-slider { background: #22c55e; }
.mini-toggle input:checked + .mini-slider:before { transform: translateX(17px); }

/* Action icon buttons */
.btn-icon {
    width: 30px; height: 30px; border-radius: 7px; border: 1.5px solid #e2e8f0;
    background: #fff; cursor: pointer; display: inline-flex; align-items: center;
    justify-content: center; font-size: 12px; color: #475569; transition: all .15s;
    padding: 0;
}
.btn-icon:hover { border-color: #0d6efd; color: #0d6efd; background: #eff6ff; }
.btn-icon.danger:hover { border-color: #ef4444; color: #ef4444; background: #fff5f5; }

/* Blog preview cards */
.blog-preview-card { border: 1.5px solid #e2e8f0; border-radius: 10px; overflow: hidden; background: #fff; }
.blog-preview-card img { width: 100%; height: 100px; object-fit: cover; display: block; }
.blog-preview-card .bpc-body { padding: 10px 12px; font-size: 12.5px; font-weight: 600; color: #1e293b; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">

    {{-- ── Page Header ── --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1 fw-700" style="color:#0d1526;">
                <i class="fas fa-home me-2" style="color:#2563eb;"></i>Home Page CMS
            </h4>
            <p class="text-muted mb-0" style="font-size:13px;">
                Manage every section of the public homepage from one place.
            </p>
        </div>
        <div class="d-flex gap-2">
            @if($page_details)
            <button class="btn btn-sm btn-outline-secondary" type="button"
                    data-bs-toggle="modal" data-bs-target="#pageMetaModal">
                <i class="fas fa-search me-1"></i>Meta Setting
            </button>
            @endif
        </div>
    </div>

    {{-- ── Tab Navigation ── --}}
    <div class="card border-0 shadow-sm mb-0" style="border-radius:14px 14px 0 0; overflow:hidden;">
        <div class="cms-tab-nav" id="cmsTabNav" role="tablist">
            <button type="button" class="cms-tab-btn active" role="tab"
                    data-bs-toggle="tab" data-bs-target="#cms-pane-hero"
                    aria-controls="cms-pane-hero" aria-selected="true">
                <i class="fas fa-images"></i>Hero Slider
            </button>
            <button type="button" class="cms-tab-btn" role="tab"
                    data-bs-toggle="tab" data-bs-target="#cms-pane-packages"
                    aria-controls="cms-pane-packages" aria-selected="false">
                <i class="fas fa-map-marked-alt"></i>Tour Packages
            </button>
            <button type="button" class="cms-tab-btn" role="tab"
                    data-bs-toggle="tab" data-bs-target="#cms-pane-customized"
                    aria-controls="cms-pane-customized" aria-selected="false">
                <i class="fas fa-magic"></i>Customized Tours
            </button>
            <button type="button" class="cms-tab-btn" role="tab"
                    data-bs-toggle="tab" data-bs-target="#cms-pane-about"
                    aria-controls="cms-pane-about" aria-selected="false">
                <i class="fas fa-info-circle"></i>Trusted Operator
            </button>
            <button type="button" class="cms-tab-btn" role="tab"
                    data-bs-toggle="tab" data-bs-target="#cms-pane-why"
                    aria-controls="cms-pane-why" aria-selected="false">
                <i class="fas fa-star"></i>Why Indian Panorama
            </button>
            <button type="button" class="cms-tab-btn" role="tab"
                    data-bs-toggle="tab" data-bs-target="#cms-pane-blogs"
                    aria-controls="cms-pane-blogs" aria-selected="false">
                <i class="fas fa-newspaper"></i>Latest Blogs
            </button>
            <button type="button" class="cms-tab-btn" role="tab"
                    data-bs-toggle="tab" data-bs-target="#cms-pane-promo"
                    aria-controls="cms-pane-promo" aria-selected="false">
                <i class="fas fa-bullhorn"></i>Promo Banner
            </button>
        </div>

        <div class="card-body p-4">
            <div class="tab-content">
                @include('admin.settings.home._hero')
                @include('admin.settings.home._packages')
                @include('admin.settings.home._customized')
                @include('admin.settings.home._about')
                @include('admin.settings.home._why')
                @include('admin.settings.home._blogs')
                @include('admin.settings.home._promo')
            </div>
        </div>
    </div>

</div>
@endsection

{{-- ══════════════════════════════════════════════════════════════
     SHARED MODALS
══════════════════════════════════════════════════════════════ --}}
@section('modal')
{{-- Meta Setting Modal (SEO) --}}
<div class="modal fade" id="pageMetaModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="page-title">
                    <i class="fas fa-search me-2" style="color:#2563eb;"></i>Meta Setting
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="pageMeta" method="POST"
                  action="{{ $page_details ? route('admin.page.update', $page_details->id) : '#' }}"
                  enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @php $pageMeta = $page_details?->meta; @endphp
                <div class="modal-body">
                    <input type="hidden" name="meta_setting">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Meta Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="page_meta_title" name="meta_title"
                                   value="{{ $pageMeta?->meta_title }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Meta Description <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="page_meta_description" name="meta_description"
                                   value="{{ $pageMeta?->meta_description }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Meta Keywords</label>
                            <input type="text" class="form-control" id="page_meta_keywords" name="meta_keywords"
                                   value="{{ $pageMeta?->meta_keywords }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">H1 Heading</label>
                            <input type="text" class="form-control" id="page_h1_heading" name="h1_heading"
                                   value="{{ $pageMeta?->h1_heading }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Extra Meta Tags (Head)</label>
                            <textarea class="form-control" name="meta_details" id="page_meta_details" rows="3">{{ $pageMeta?->meta_details }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Extra Meta Tags (Body)</label>
                            <textarea class="form-control" name="meta_body_details" id="page_meta_body_details" rows="3">{{ $pageMeta?->meta_body_details }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-dark">
                        <i class="fas fa-save me-2"></i>Update SEO
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@include('admin.settings.home._modals')
@endsection

@section('scripts')
{{-- SortableJS (not in layout, load here) --}}
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
{{-- Select2 CSS + JS are already loaded in layouts/app.blade.php — do NOT re-load them here --}}

<script>
/* ════════════════════════════════════════
   SHARED — Bootstrap 5 native tab system
   Activate from URL hash on load; sync
   hash when user switches tabs.
════════════════════════════════════════ */
$(function () {
    /* Restore tab from URL hash (e.g. page reload or back/fwd) */
    var hash = (location.hash || '').replace('#', '');
    if (hash) {
        var $btn = $('#cmsTabNav [data-bs-target="#cms-pane-' + hash + '"]');
        if ($btn.length) {
            bootstrap.Tab.getOrCreateInstance($btn[0]).show();
        }
    }
    /* Keep URL hash in sync as tabs switch */
    $('#cmsTabNav').on('shown.bs.tab', '[data-bs-toggle="tab"]', function () {
        var target = $(this).attr('data-bs-target') || '';
        history.replaceState(null, '', '#' + target.replace('#cms-pane-', ''));
    });
});

/* ════════════════════════════════════════
   SHARED — Meta Setting modal
════════════════════════════════════════ */
$(document).on('submit', '#pageMeta', function (e) {
    e.preventDefault();
    var $form = $(this);
    var $btn  = $form.find('[type=submit]');
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Saving…');
    $.ajax({
        url    : $form.attr('action'),
        type   : 'POST',
        data   : $form.serialize(),
        headers: { 'X-CSRF-TOKEN': CSRF },
    })
    .done(function (r) {
        if (r && r.success) {
            toastr.success(r.message || 'SEO / Meta saved.');
            cmsHideModal('pageMetaModal');
        } else {
            toastr.error((r && r.message) || 'Save failed.');
        }
    })
    .fail(function (xhr) {
        var msg = 'Save failed.';
        try { msg = xhr.responseJSON.message || msg; } catch(e) {}
        toastr.error(msg);
    })
    .always(function () {
        $btn.prop('disabled', false).html('<i class="fas fa-save me-2"></i>Update SEO');
    });
});

/* ════════════════════════════════════════
   SHARED — Section heading saver
   Called per-tab with key + form fields
════════════════════════════════════════ */
var CSRF = $('meta[name="csrf-token"]').attr('content');

/* ── Bootstrap 5 modal helpers ──
   Bootstrap 5 dropped the jQuery modal plugin.
   Use native bootstrap.Modal API instead of $(...).modal('show'). */
function cmsShowModal(id) {
    var el = document.getElementById(id);
    if (!el) return;
    bootstrap.Modal.getOrCreateInstance(el).show();
}
function cmsHideModal(id) {
    var el = document.getElementById(id);
    if (!el) return;
    var m = bootstrap.Modal.getInstance(el);
    if (m) m.hide();
}

function saveSectionHeading(key, $form, $btn) {
    $btn.prop('disabled', true);
    var data = {
        _method    : 'PUT',
        title      : $form.find('[name=title]').val(),
        subtitle   : $form.find('[name=subtitle]').val(),
        description: $form.find('[name=description]').val(),
        button_text: $form.find('[name=button_text]').val(),
        button_url : $form.find('[name=button_url]').val(),
        is_visible : $form.find('[name=is_visible]').prop('checked') ? 1 : 0,
    };
    $.ajax({
        url    : '{{ url("admin/home-sections") }}/' + key,
        type   : 'POST',
        data   : data,
        headers: { 'X-CSRF-TOKEN': CSRF },
    })
    .done(function (r) {
        if (r.success) toastr.success('Section settings saved.');
        else toastr.error('Save failed.');
    })
    .fail(function () { toastr.error('Request failed.'); })
    .always(function () { $btn.prop('disabled', false); });
}

/* ════════════════════════════════════════
   SHARED — Init session success toast
════════════════════════════════════════ */
@if(session('success'))
    $(function() { toastr.success("{{ session('success') }}", 'Success'); });
@endif
</script>

@include('admin.settings.home._scripts')
@endsection
