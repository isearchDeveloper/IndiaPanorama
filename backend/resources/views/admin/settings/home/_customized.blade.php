{{-- ══ TAB: Customized India Tours ══ --}}
<div class="tab-pane fade" id="cms-pane-customized">

    @php
        $custSec = $sections->get('customized_tours');
        $selectedIds = $custSec?->extra('package_ids', []);
    @endphp

    {{-- Section settings --}}
    <div class="sec-heading-panel">
        <div class="panel-label"><i class="fas fa-cog"></i>Section Settings</div>
        <form class="row g-3 align-items-end" id="customized-heading-form">
            <div class="col-md-5">
                <label class="form-label fw-semibold small">Title</label>
                <input type="text" class="form-control form-control-sm" name="title"
                       value="{{ $custSec?->title }}" placeholder="e.g. Customized India Tours">
            </div>
            <div class="col-md-5">
                <label class="form-label fw-semibold small">Subtitle</label>
                <input type="text" class="form-control form-control-sm" name="subtitle"
                       value="{{ $custSec?->subtitle }}">
            </div>
            <div class="col-md-2 d-flex align-items-center gap-3">
                <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" name="is_visible"
                           id="cust-visible" {{ $custSec?->is_visible ? 'checked' : '' }}>
                    <label class="form-check-label small" for="cust-visible">Visible</label>
                </div>
                <button type="button" class="btn btn-sm btn-primary" id="customized-heading-save">
                    <i class="fas fa-save me-1"></i>Save
                </button>
            </div>
        </form>
    </div>

    {{-- Package search & select --}}
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h6 class="mb-0 fw-700">
            <i class="fas fa-magic me-2 text-primary"></i>Package Selection
        </h6>
        <button class="btn btn-sm btn-primary" id="customized-pkg-save">
            <i class="fas fa-save me-1"></i>Save Selection
        </button>
    </div>

    {{-- Search box --}}
    <div class="mb-3">
        <input type="text" id="cust-pkg-search"
               class="form-control form-control-sm"
               placeholder="Search packages by title…"
               style="max-width:340px;">
    </div>

    {{-- Hidden: selected IDs carried to save --}}
    <input type="hidden" id="customized-selected-ids"
           value="{{ json_encode($selectedIds) }}">

    {{-- Package grid (checkboxes) --}}
    <div class="row g-2" id="customized-pkg-grid">
        @forelse($all_packages as $pkg)
        @php $checked = in_array($pkg->id, (array)$selectedIds); @endphp
        <div class="col-md-3 col-sm-4 col-6 cust-pkg-item"
             data-title="{{ strtolower($pkg->title) }}">
            <label class="d-block border rounded-3 p-2 cursor-pointer cust-pkg-card
                          {{ $checked ? 'border-primary bg-primary bg-opacity-10' : '' }}"
                   style="cursor:pointer; transition:.15s;">
                <input type="checkbox" class="cust-pkg-check d-none"
                       value="{{ $pkg->id }}" {{ $checked ? 'checked' : '' }}>
                <div class="fw-semibold" style="font-size:12.5px; line-height:1.3;">
                    {{ $pkg->title }}
                </div>
                @if($checked)
                <span class="badge mt-1"
                      style="background:rgba(34,197,94,.15);color:#16a34a;font-size:10px;">
                    <i class="fas fa-check me-1"></i>Selected
                </span>
                @endif
            </label>
        </div>
        @empty
        <div class="col-12 text-center text-muted py-4">
            <i class="fas fa-box-open fa-2x opacity-25 mb-2 d-block"></i>
            No active packages found.
        </div>
        @endforelse
    </div>

</div>
