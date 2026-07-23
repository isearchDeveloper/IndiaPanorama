@section('title', 'Edit City Page — ' . $manageCity->display_name)
@extends('layouts.app')

@push('style')
<style>
    .city-tabs { background:#fff; border-radius:12px; padding:6px; display:flex; gap:4px; box-shadow:0 1px 4px rgba(0,0,0,.07); }
    .city-tabs .nav-link { border-radius:8px; border:none; font-weight:500; color:#555; padding:8px 16px; white-space:nowrap; }
    .city-tabs .nav-link:hover:not(.active) { background:#f3f4f6; color:#333; }
    .city-tabs .nav-link.active { background:#2563eb; color:#fff; }
    .htr-mode-col { width:220px; flex-shrink:0; }
</style>
@endpush

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1 fw-bold">
                <i class="fas fa-city me-2 text-warning"></i>{{ $manageCity->display_name }}
            </h4>
            <div class="d-flex align-items-center gap-2">
                <span class="badge {{ $manageCity->type === 'Region' ? 'bg-success' : ($manageCity->type === 'State' ? 'bg-primary' : 'bg-info text-dark') }}">
                    {{ $manageCity->type }}
                </span>
                @if($manageCity->parent_name)
                    <span class="text-muted small">{{ $manageCity->parent_name }}</span>
                @endif
            </div>
        </div>
        <a href="{{ route('admin.city-pages.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>

    {{-- Pill Tabs --}}
    <ul class="nav city-tabs mb-3" id="editTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#panel-htr" role="tab">
                <i class="fas fa-plane-departure me-1"></i> How To Reach
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#panel-top-places" role="tab">
                <i class="fas fa-map-location-dot me-1"></i> Top Tourist Places
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#panel-things-todo" role="tab">
                <i class="fas fa-person-hiking me-1"></i> Things To Do
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#panel-tips" role="tab">
                <i class="fas fa-lightbulb me-1"></i> Travel Tips
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#panel-know" role="tab">
                <i class="fas fa-circle-info me-1"></i> Things To Know
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#panel-rel" role="tab">
                <i class="fas fa-place-of-worship me-1"></i> Religious Tourism
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#panel-souv" role="tab">
                <i class="fas fa-shopping-bag me-1"></i> Souvenirs &amp; Dishes
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#panel-festivals" role="tab">
                <i class="fas fa-drum me-1"></i> Festivals Intro
            </a>
        </li>
    </ul>

    <div class="tab-content">

        {{-- HOW TO REACH --}}
        <div class="tab-pane fade show active" id="panel-htr" role="tabpanel">
            <div class="card">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">How To Reach</h6>

                    {{-- Column headers --}}
                    <div class="d-flex gap-2 mb-2 px-1">
                        <div class="htr-mode-col"><span class="text-uppercase fw-semibold small text-secondary">Mode</span></div>
                        <div class="flex-grow-1"><span class="text-uppercase fw-semibold small text-secondary">Description</span></div>
                    </div>

                    <div id="htr-rows">
                        @forelse($manageCity->howToReach as $row)
                        <div class="htr-row d-flex gap-2 mb-3 align-items-start">
                            <div class="htr-mode-col">
                                <select class="form-select htr-mode">
                                    <option value="" disabled {{ $row->mode ? '' : 'selected' }}>— Select —</option>
                                    @foreach(['By Air','By Road','By Train','By Sea'] as $opt)
                                    <option value="{{ $opt }}" {{ $row->mode === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex-grow-1">
                                <textarea class="form-control htr-desc" rows="3">{{ $row->description }}</textarea>
                            </div>
                            <button type="button" class="btn btn-outline-danger htr-del" style="min-width:38px">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        @empty
                        <div class="htr-row d-flex gap-2 mb-3 align-items-start">
                            <div class="htr-mode-col">
                                <select class="form-select htr-mode">
                                    <option value="" disabled selected>— Select —</option>
                                    <option>By Air</option>
                                    <option>By Road</option>
                                    <option>By Train</option>
                                    <option>By Sea</option>
                                </select>
                            </div>
                            <div class="flex-grow-1">
                                <textarea class="form-control htr-desc" rows="3" placeholder="How to reach via this mode…"></textarea>
                            </div>
                            <button type="button" class="btn btn-outline-danger htr-del" style="min-width:38px">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        @endforelse
                    </div>

                    <div class="d-flex gap-2 mt-2">
                        <button type="button" class="btn btn-outline-success" id="htr-add">
                            <i class="fas fa-plus me-1"></i> Add Row
                        </button>
                        <button type="button" class="btn" style="background:#2563eb;color:#fff;" id="htr-save">
                            <i class="fas fa-floppy-disk me-1"></i> Save
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- TOP TOURIST PLACES --}}
        <div class="tab-pane fade" id="panel-top-places" role="tabpanel">
            <div class="card">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Top Tourist Places</h6>

                    <div class="d-flex gap-2 mb-2 px-1">
                        <div class="htr-mode-col"><span class="text-uppercase fw-semibold small text-secondary">Name</span></div>
                        <div class="flex-grow-1"><span class="text-uppercase fw-semibold small text-secondary">Description</span></div>
                    </div>

                    <div id="top-place-rows">
                        @forelse($manageCity->topPlaces as $row)
                        <div class="top-place-row d-flex gap-2 mb-3 align-items-start">
                            <div class="htr-mode-col">
                                <input type="text" class="form-control tp-name" value="{{ $row->name }}" placeholder="e.g. Munnar">
                            </div>
                            <div class="flex-grow-1">
                                <textarea class="form-control tp-desc" rows="3">{{ $row->description }}</textarea>
                            </div>
                            <button type="button" class="btn btn-outline-danger top-place-del" style="min-width:38px">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        @empty
                        <div class="top-place-row d-flex gap-2 mb-3 align-items-start">
                            <div class="htr-mode-col">
                                <input type="text" class="form-control tp-name" placeholder="e.g. Munnar">
                            </div>
                            <div class="flex-grow-1">
                                <textarea class="form-control tp-desc" rows="3" placeholder="Short description…"></textarea>
                            </div>
                            <button type="button" class="btn btn-outline-danger top-place-del" style="min-width:38px">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        @endforelse
                    </div>

                    <div class="d-flex gap-2 mt-2">
                        <button type="button" class="btn btn-outline-success" id="top-place-add">
                            <i class="fas fa-plus me-1"></i> Add Row
                        </button>
                        <button type="button" class="btn" style="background:#2563eb;color:#fff;" id="top-place-save">
                            <i class="fas fa-floppy-disk me-1"></i> Save
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- THINGS TO DO --}}
        <div class="tab-pane fade" id="panel-things-todo" role="tabpanel">
            <div class="card">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Things To Do</h6>

                    <div id="ttd-rows">
                        @forelse($manageCity->thingsToDo as $row)
                        <div class="ttd-row border rounded p-3 mb-3">
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                <input type="text" class="form-control ttd-title fw-semibold" value="{{ $row->title }}" placeholder="e.g. Temple Trail Exploration">
                                <button type="button" class="btn btn-outline-danger ttd-del" style="min-width:38px">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            <textarea class="form-control ttd-desc mb-2" rows="3" placeholder="Describe this experience…">{{ $row->description }}</textarea>
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label class="form-label small text-muted mb-1">Duration &amp; Timing</label>
                                    <input type="text" class="form-control ttd-duration" value="{{ $row->duration }}" placeholder="e.g. 2-3 hrs; mornings ideal">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small text-muted mb-1">Best For</label>
                                    <input type="text" class="form-control ttd-best-for" value="{{ $row->best_for }}" placeholder="e.g. Culture seekers">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small text-muted mb-1">Approximate Cost</label>
                                    <input type="text" class="form-control ttd-cost" value="{{ $row->approx_cost }}" placeholder="e.g. Free entry">
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="ttd-row border rounded p-3 mb-3">
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                <input type="text" class="form-control ttd-title fw-semibold" placeholder="e.g. Temple Trail Exploration">
                                <button type="button" class="btn btn-outline-danger ttd-del" style="min-width:38px">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            <textarea class="form-control ttd-desc mb-2" rows="3" placeholder="Describe this experience…"></textarea>
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label class="form-label small text-muted mb-1">Duration &amp; Timing</label>
                                    <input type="text" class="form-control ttd-duration" placeholder="e.g. 2-3 hrs; mornings ideal">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small text-muted mb-1">Best For</label>
                                    <input type="text" class="form-control ttd-best-for" placeholder="e.g. Culture seekers">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small text-muted mb-1">Approximate Cost</label>
                                    <input type="text" class="form-control ttd-cost" placeholder="e.g. Free entry">
                                </div>
                            </div>
                        </div>
                        @endforelse
                    </div>

                    <div class="d-flex gap-2 mt-2">
                        <button type="button" class="btn btn-outline-success" id="ttd-add">
                            <i class="fas fa-plus me-1"></i> Add Row
                        </button>
                        <button type="button" class="btn" style="background:#2563eb;color:#fff;" id="ttd-save">
                            <i class="fas fa-floppy-disk me-1"></i> Save
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- TRAVEL TIPS --}}
        <div class="tab-pane fade" id="panel-tips" role="tabpanel">
            <div class="card">
                <div class="card-body">
                    <textarea id="editor-travel-tips" class="tinymce no-char-limit"
                              style="width:100%;min-height:420px">{{ $manageCity->travel_tips }}</textarea>
                </div>
                <div class="card-footer text-end">
                    <button type="button" class="btn" style="background:#2563eb;color:#fff;" id="tips-save">
                        <i class="fas fa-floppy-disk me-1"></i> Save
                    </button>
                </div>
            </div>
        </div>

        {{-- THINGS TO KNOW --}}
        <div class="tab-pane fade" id="panel-know" role="tabpanel">
            <div class="card">
                <div class="card-body">
                    <textarea id="editor-things-to-know" class="tinymce no-char-limit"
                              style="width:100%;min-height:420px">{{ $manageCity->things_to_know }}</textarea>
                </div>
                <div class="card-footer text-end">
                    <button type="button" class="btn" style="background:#2563eb;color:#fff;" id="know-save">
                        <i class="fas fa-floppy-disk me-1"></i> Save
                    </button>
                </div>
            </div>
        </div>

        {{-- RELIGIOUS TOURISM --}}
        <div class="tab-pane fade" id="panel-rel" role="tabpanel">
            <div class="card">
                <div class="card-body">
                    <textarea id="editor-religious-tourism" class="tinymce no-char-limit"
                              style="width:100%;min-height:420px">{{ $manageCity->religious_tourism }}</textarea>
                </div>
                <div class="card-footer text-end">
                    <button type="button" class="btn" style="background:#2563eb;color:#fff;" id="rel-save">
                        <i class="fas fa-floppy-disk me-1"></i> Save
                    </button>
                </div>
            </div>
        </div>

        {{-- SOUVENIRS & DISHES --}}
        <div class="tab-pane fade" id="panel-souv" role="tabpanel">
            <div class="card">
                <div class="card-body">
                    <label class="form-label fw-semibold mb-2">Souvenirs To Shop</label>
                    <textarea id="editor-souvenirs" class="tinymce no-char-limit"
                              style="width:100%;min-height:320px">{{ $manageCity->souvenirs_to_shop }}</textarea>

                    <hr class="my-4">

                    <label class="form-label fw-semibold mb-2">Popular Dishes</label>
                    <textarea id="editor-dishes" class="tinymce no-char-limit"
                              style="width:100%;min-height:320px">{{ $manageCity->popular_dishes }}</textarea>
                </div>
                <div class="card-footer text-end">
                    <button type="button" class="btn" style="background:#2563eb;color:#fff;" id="souv-save">
                        <i class="fas fa-floppy-disk me-1"></i> Save
                    </button>
                </div>
            </div>
        </div>

        {{-- FESTIVALS INTRO --}}
        <div class="tab-pane fade" id="panel-festivals" role="tabpanel">
            <div class="card">
                <div class="card-body">
                    <p class="text-muted small mb-3">
                        Shown as the intro text under "Festivals" on both this state's page and every one of its
                        cities' pages via the API — it's shared per state, not per city.
                    </p>
                    <textarea id="editor-festivals-intro" class="tinymce no-char-limit"
                              style="width:100%;min-height:320px">{{ $festivalsIntro }}</textarea>
                </div>
                <div class="card-footer text-end">
                    <button type="button" class="btn" style="background:#2563eb;color:#fff;" id="festivals-save">
                        <i class="fas fa-floppy-disk me-1"></i> Save
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
const CSRF = '{{ csrf_token() }}';
const ID   = {{ $manageCity->id }};
const BASE = '{{ url("admin/city-pages") }}';

function apiUrl(suffix) { return `${BASE}/${ID}/${suffix}`; }

// Init TinyMCE when a hidden tab is shown
$('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
    const $pane = $($(e.target).attr('href'));
    $pane.find('textarea.tinymce').each(function () {
        if (typeof window.initTinyMCEOn === 'function') window.initTinyMCEOn($(this));
    });
});

// ── How To Reach ──────────────────────────────────────────────────────────
function htrRowHtml() {
    return `<div class="htr-row d-flex gap-2 mb-3 align-items-start">
        <div class="htr-mode-col">
            <select class="form-select htr-mode">
                <option value="" disabled selected>— Select —</option>
                <option>By Air</option>
                <option>By Road</option>
                <option>By Train</option>
                <option>By Sea</option>
            </select>
        </div>
        <div class="flex-grow-1">
            <textarea class="form-control htr-desc" rows="3" placeholder="How to reach via this mode…"></textarea>
        </div>
        <button type="button" class="btn btn-outline-danger htr-del" style="min-width:38px">
            <i class="fas fa-trash"></i>
        </button>
    </div>`;
}

$('#htr-add').on('click', () => $('#htr-rows').append(htrRowHtml()));
$(document).on('click', '.htr-del', function () { $(this).closest('.htr-row').remove(); });

$('#htr-save').on('click', function () {
    const $btn = $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Saving…');
    const rows = [];
    $('#htr-rows .htr-row').each(function () {
        rows.push({ mode: $(this).find('.htr-mode').val(), description: $(this).find('.htr-desc').val() });
    });
    $.ajax({ url: apiUrl('how-to-reach'), method: 'POST', data: { _token: CSRF, rows }, traditional: false })
        .done(r => toastr.success(r.message))
        .fail(x => toastr.error(x.responseJSON?.message || 'Error saving.'))
        .always(() => $btn.prop('disabled', false).html('<i class="fas fa-floppy-disk me-1"></i> Save'));
});

// ── Top Tourist Places ───────────────────────────────────────────────────
function topPlaceRowHtml() {
    return `<div class="top-place-row d-flex gap-2 mb-3 align-items-start">
        <div class="htr-mode-col">
            <input type="text" class="form-control tp-name" placeholder="e.g. Munnar">
        </div>
        <div class="flex-grow-1">
            <textarea class="form-control tp-desc" rows="3" placeholder="Short description…"></textarea>
        </div>
        <button type="button" class="btn btn-outline-danger top-place-del" style="min-width:38px">
            <i class="fas fa-trash"></i>
        </button>
    </div>`;
}

$('#top-place-add').on('click', () => $('#top-place-rows').append(topPlaceRowHtml()));
$(document).on('click', '.top-place-del', function () { $(this).closest('.top-place-row').remove(); });

$('#top-place-save').on('click', function () {
    const $btn = $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Saving…');
    const rows = [];
    $('#top-place-rows .top-place-row').each(function () {
        rows.push({ name: $(this).find('.tp-name').val(), description: $(this).find('.tp-desc').val() });
    });
    $.ajax({ url: apiUrl('top-places'), method: 'POST', data: { _token: CSRF, rows }, traditional: false })
        .done(r => toastr.success(r.message))
        .fail(x => toastr.error(x.responseJSON?.message || 'Error saving.'))
        .always(() => $btn.prop('disabled', false).html('<i class="fas fa-floppy-disk me-1"></i> Save'));
});

// ── Things To Do ─────────────────────────────────────────────────────────
function ttdRowHtml() {
    return `<div class="ttd-row border rounded p-3 mb-3">
        <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
            <input type="text" class="form-control ttd-title fw-semibold" placeholder="e.g. Temple Trail Exploration">
            <button type="button" class="btn btn-outline-danger ttd-del" style="min-width:38px">
                <i class="fas fa-trash"></i>
            </button>
        </div>
        <textarea class="form-control ttd-desc mb-2" rows="3" placeholder="Describe this experience…"></textarea>
        <div class="row g-2">
            <div class="col-md-4">
                <label class="form-label small text-muted mb-1">Duration &amp; Timing</label>
                <input type="text" class="form-control ttd-duration" placeholder="e.g. 2-3 hrs; mornings ideal">
            </div>
            <div class="col-md-4">
                <label class="form-label small text-muted mb-1">Best For</label>
                <input type="text" class="form-control ttd-best-for" placeholder="e.g. Culture seekers">
            </div>
            <div class="col-md-4">
                <label class="form-label small text-muted mb-1">Approximate Cost</label>
                <input type="text" class="form-control ttd-cost" placeholder="e.g. Free entry">
            </div>
        </div>
    </div>`;
}

$('#ttd-add').on('click', () => $('#ttd-rows').append(ttdRowHtml()));
$(document).on('click', '.ttd-del', function () { $(this).closest('.ttd-row').remove(); });

$('#ttd-save').on('click', function () {
    const $btn = $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Saving…');
    const rows = [];
    $('#ttd-rows .ttd-row').each(function () {
        rows.push({
            title:       $(this).find('.ttd-title').val(),
            description: $(this).find('.ttd-desc').val(),
            duration:    $(this).find('.ttd-duration').val(),
            best_for:    $(this).find('.ttd-best-for').val(),
            approx_cost: $(this).find('.ttd-cost').val(),
        });
    });
    $.ajax({ url: apiUrl('things-to-do'), method: 'POST', data: { _token: CSRF, rows }, traditional: false })
        .done(r => toastr.success(r.message))
        .fail(x => toastr.error(x.responseJSON?.message || 'Error saving.'))
        .always(() => $btn.prop('disabled', false).html('<i class="fas fa-floppy-disk me-1"></i> Save'));
});

// ── Rich-text save helpers ────────────────────────────────────────────────
function saveTiny(editorId, field, endpoint, $btn) {
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Saving…');
    const content = tinymce.get(editorId)?.getContent() ?? $(`#${editorId}`).val();
    $.post(apiUrl(endpoint), { _token: CSRF, [field]: content })
        .done(r => toastr.success(r.message))
        .fail(x => toastr.error(x.responseJSON?.message || 'Error saving.'))
        .always(() => $btn.prop('disabled', false).html('<i class="fas fa-floppy-disk me-1"></i> Save'));
}

$('#tips-save').on('click', function () { saveTiny('editor-travel-tips',    'travel_tips',      'travel-tips',      $(this)); });
$('#know-save').on('click', function () { saveTiny('editor-things-to-know', 'things_to_know',   'things-to-know',   $(this)); });
$('#rel-save' ).on('click', function () { saveTiny('editor-religious-tourism','religious_tourism','religious-tourism',$(this)); });
$('#festivals-save').on('click', function () { saveTiny('editor-festivals-intro', 'intro', 'festivals-intro', $(this)); });

$('#souv-save').on('click', function () {
    const $btn = $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Saving…');
    const souvenirs = tinymce.get('editor-souvenirs')?.getContent() ?? $('#editor-souvenirs').val();
    const dishes    = tinymce.get('editor-dishes')?.getContent()    ?? $('#editor-dishes').val();
    $.post(apiUrl('souvenirs'), { _token: CSRF, souvenirs_to_shop: souvenirs, popular_dishes: dishes })
        .done(r => toastr.success(r.message))
        .fail(x => toastr.error(x.responseJSON?.message || 'Error saving.'))
        .always(() => $btn.prop('disabled', false).html('<i class="fas fa-floppy-disk me-1"></i> Save'));
});
</script>
@endsection
