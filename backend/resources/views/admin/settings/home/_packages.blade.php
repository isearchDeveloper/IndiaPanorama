{{-- ══ TAB: India Tour Packages ══ --}}
<div class="tab-pane fade" id="cms-pane-packages">

    @php $pkgSec = $sections->get('india_tours'); @endphp

    <div class="sec-heading-panel">
        <div class="panel-label"><i class="fas fa-cog"></i>Section Settings</div>
        <form class="row g-3 align-items-end" id="packages-heading-form">
            <div class="col-md-5">
                <label class="form-label fw-semibold small">Title</label>
                <input type="text" class="form-control form-control-sm" name="title"
                       value="{{ $pkgSec?->title }}" placeholder="e.g. India Tour Packages">
            </div>
            <div class="col-md-5">
                <label class="form-label fw-semibold small">Subtitle</label>
                <input type="text" class="form-control form-control-sm" name="subtitle"
                       value="{{ $pkgSec?->subtitle }}">
            </div>
            <div class="col-md-2 d-flex align-items-center gap-3">
                <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" name="is_visible"
                           id="pkg-visible" {{ $pkgSec?->is_visible ? 'checked' : '' }}>
                    <label class="form-check-label small" for="pkg-visible">Visible</label>
                </div>
                <button type="button" class="btn btn-sm btn-primary" id="packages-heading-save">
                    <i class="fas fa-save me-1"></i>Save
                </button>
            </div>
        </form>
    </div>

    <div class="alert alert-info border-0 rounded-3 small" style="background:#eff6ff; color:#1e40af;">
        <i class="fas fa-info-circle me-2"></i>
        Packages are displayed dynamically from the live database, ordered by latest.
        Manage packages via the <strong>Packages</strong> section.
    </div>

</div>
