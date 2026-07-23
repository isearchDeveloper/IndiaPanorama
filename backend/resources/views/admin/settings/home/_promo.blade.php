{{-- ══ TAB: Promo Banner ══ --}}
<div class="tab-pane fade" id="cms-pane-promo">

    @php $promoSec = $sections->get('promotional_banner'); @endphp

    @if($promoSec?->image)
    {{-- ── Banner exists: manage it ── --}}
    <div class="card border-0 shadow-sm" style="border-radius:14px;overflow:hidden;" id="promo-banner-card">
        {{-- Image preview --}}
        <div class="position-relative" style="max-height:230px;overflow:hidden;">
            <img id="promo-current-img" src="{{ storage_link($promoSec->image) }}"
                 alt="{{ $promoSec->image_alt }}"
                 style="width:100%;max-height:230px;object-fit:cover;display:block;">
            <span id="promo-status-badge"
                  class="badge position-absolute top-0 end-0 m-3 {{ $promoSec->is_visible ? 'bg-success' : 'bg-secondary' }}"
                  style="font-size:12px;padding:6px 12px;">
                {{ $promoSec->is_visible ? 'Active' : 'Inactive' }}
            </span>
        </div>

        {{-- Controls --}}
        <div class="p-3 d-flex align-items-center gap-3 flex-wrap"
             style="border-top:1px solid #e2e8f0;background:#f8fafc;">
            <div class="small text-muted me-auto">
                <i class="fas fa-tag me-1 opacity-50"></i>
                <span id="promo-alt-display">{{ $promoSec->image_alt ?: '(no alt tag)' }}</span>
            </div>

            {{-- Status toggle --}}
            <div class="d-flex align-items-center gap-2">
                <span class="small fw-semibold text-muted">Status:</span>
                <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" id="promo-status-toggle"
                           {{ $promoSec->is_visible ? 'checked' : '' }}>
                    <label class="form-check-label small" id="promo-status-label"
                           for="promo-status-toggle">
                        {{ $promoSec->is_visible ? 'Active' : 'Inactive' }}
                    </label>
                </div>
            </div>

            <button type="button" class="btn btn-sm btn-outline-primary" id="promo-edit-btn">
                <i class="fas fa-edit me-1"></i>Edit
            </button>
            <button type="button" class="btn btn-sm btn-outline-danger" id="promo-delete-btn">
                <i class="fas fa-trash me-1"></i>Delete
            </button>
        </div>
    </div>

    {{-- Inline edit form (hidden by default) --}}
    <div id="promo-edit-wrap" class="mt-3" style="display:none;">
        <div class="card border-0 shadow-sm" style="border-radius:14px;">
            <div class="card-header" style="background:#f8fafc;border-bottom:1.5px solid #e2e8f0;padding:12px 18px;">
                <span class="fw-semibold small"><i class="fas fa-edit me-2 text-primary"></i>Edit Banner</span>
            </div>
            <div class="card-body">
                <form id="promo-edit-form">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-6">
                            <x-media-picker name="image" picker-id="promo_image_edit" label="Replace Image"
                                folder="home-sections" :value="$promoSec->image" />
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Image Alt Tag</label>
                            <input type="text" class="form-control form-control-sm"
                                   id="promo-edit-alt" name="image_alt"
                                   value="{{ $promoSec->image_alt }}"
                                   placeholder="Promotional banner India">
                        </div>
                        <div class="col-md-2 d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-primary flex-fill"
                                    id="promo-update-btn">
                                <i class="fas fa-save me-1"></i>Save
                            </button>
                            <button type="button" class="btn btn-sm btn-light"
                                    id="promo-cancel-edit-btn">Cancel</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @else
    {{-- ── No banner: upload form ── --}}
    <div class="card border-0 shadow-sm" style="border-radius:14px;">
        <div class="card-body">
            <div class="text-center py-5 mb-3"
                 style="background:#f8fafc;border-radius:10px;border:2px dashed #e2e8f0;">
                <i class="fas fa-image fa-3x opacity-25 text-muted mb-2 d-block"></i>
                <span class="text-muted small">No banner image uploaded yet</span>
            </div>
            <form id="promo-upload-form">
                <div class="row g-3 align-items-end">
                    <div class="col-md-6">
                        <x-media-picker name="image" picker-id="promo_image_upload" label="Banner Image"
                            folder="home-sections" />
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Image Alt Tag</label>
                        <input type="text" class="form-control form-control-sm" name="image_alt"
                               placeholder="Promotional banner India">
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-sm btn-primary w-100"
                                id="promo-image-save">
                            <i class="fas fa-upload me-1"></i>Upload
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endif

</div>
