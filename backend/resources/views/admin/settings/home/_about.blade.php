{{-- ══ TAB: Trusted India Tour Operator ══ --}}
<div class="tab-pane fade" id="cms-pane-about">

    @php $aboutSec = $sections->get('about_intro'); @endphp

    {{-- ── LEFT SIDE: Section Content Settings ── --}}
    <div class="sec-heading-panel mb-4">
        <div class="panel-label"><i class="fas fa-cog"></i>Left Side Content</div>
        <form class="row g-3" id="about-heading-form">
            <div class="col-12">
                <label class="form-label fw-semibold small">Title</label>
                <input type="text" class="form-control form-control-sm" name="title"
                       value="{{ $aboutSec?->title }}" placeholder="Trusted India Tour Operator">
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold small">Description / Rich Content</label>
                <textarea class="form-control tinymce" name="description" id="about-description"
                          rows="6">{{ $aboutSec?->description }}</textarea>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold small">CTA Button Text</label>
                <input type="text" class="form-control form-control-sm" name="button_text"
                       value="{{ $aboutSec?->button_text }}" placeholder="Learn More">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold small">CTA Button URL</label>
                <input type="text" class="form-control form-control-sm" name="button_url"
                       value="{{ $aboutSec?->button_url }}" placeholder="/about-us">
            </div>
            <div class="col-md-4">
                <x-media-picker name="image" picker-id="about_image" label="Banner / Background Image"
                    folder="home-sections" :value="$aboutSec?->image" />
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold small">Banner Alt Tag</label>
                <input type="text" class="form-control form-control-sm" name="image_alt"
                       value="{{ $aboutSec?->image_alt }}" placeholder="About banner">
            </div>
            <div class="col-12 pt-1">
                <button type="button" class="btn btn-sm btn-primary" id="about-heading-save">
                    <i class="fas fa-save me-1"></i>Save Settings
                </button>
            </div>
        </form>
    </div>

    {{-- ── RIGHT SIDE: Master Text ── --}}
    <div class="sec-heading-panel mb-4">
        <div class="panel-label">
            <i class="fas fa-align-left"></i>Right Side Master Text
            <small class="text-muted fw-normal ms-2" style="text-transform:none;letter-spacing:0;">
                Appears above all feature items
            </small>
        </div>
        <div class="mb-2">
            <textarea class="form-control tinymce" id="about-right-text" rows="4"
                      placeholder="Planning a memorable journey to India is simple and stress-free...">{{ $aboutSec?->extra('right_side_text') }}</textarea>
        </div>
        <button type="button" class="btn btn-sm btn-primary mt-2" id="about-right-text-save">
            <i class="fas fa-save me-1"></i>Save Master Text
        </button>
    </div>

    {{-- ── RIGHT SIDE: Feature / Icon List ── --}}
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h6 class="mb-0 fw-700">
            <i class="fas fa-th-list me-2 text-success"></i>Feature Items
            <small class="text-muted fw-normal">(right side, below master text)</small>
        </h6>
        <button type="button" class="btn btn-sm btn-outline-success" id="open-add-feature-btn">
            <i class="fas fa-plus me-1"></i>Add Feature
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-sm table-hover align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th style="width:36px;"></th>
                    <th style="width:46px;">Icon</th>
                    <th>Feature Title</th>
                    <th>Description</th>
                    <th style="width:55px;" class="text-center">Sort</th>
                    <th style="width:90px;">Actions</th>
                </tr>
            </thead>
            <tbody id="features-table-body">
                @forelse($about_features as $feat)
                <tr data-id="{{ $feat->id }}">
                    <td class="feat-drag-handle text-center" style="cursor:grab;">
                        <i class="fas fa-grip-vertical text-muted"></i>
                    </td>
                    <td class="text-center">
                        <i class="{{ $feat->icon_class ?: 'fas fa-check-circle' }}"
                           style="font-size:18px;color:#2563eb;"></i>
                    </td>
                    <td class="small fw-semibold">{{ $feat->text ?: '—' }}</td>
                    <td class="small text-muted text-truncate" style="max-width:220px;">
                        {{ $feat->feature_description ?: '—' }}
                    </td>
                    <td class="small text-center">{{ $feat->sort_order }}</td>
                    <td>
                        <button class="btn btn-xs btn-outline-primary edit-feature me-1"
                                data-id="{{ $feat->id }}"
                                data-text="{{ $feat->text }}"
                                data-icon="{{ $feat->icon_class }}"
                                data-description="{{ $feat->feature_description }}"
                                data-sort="{{ $feat->sort_order }}"
                                title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-xs btn-outline-danger delete-feature"
                                data-id="{{ $feat->id }}" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr id="features-empty">
                    <td colspan="6" class="text-center text-muted py-5">
                        <i class="fas fa-th-list fa-2x opacity-25 mb-2 d-block"></i>
                        No features yet. Click <strong>Add Feature</strong>.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
