{{-- ══ TAB: Hero Slider ══ --}}
<div class="tab-pane fade show active" id="cms-pane-hero">

    {{-- Action bar --}}
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h6 class="mb-0 fw-700"><i class="fas fa-images me-2 text-primary"></i>Slider Banners</h6>
        <button class="btn btn-sm btn-primary" id="add-banner-btn">
            <i class="fas fa-plus me-1"></i>Add Slide
        </button>
    </div>

    {{-- Banner table --}}
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="banner-sortable-table">
            <thead style="background:#f8fafc;">
                <tr>
                    <th style="width:36px;"></th>
                    <th style="width:90px;">Image</th>
                    <th>Title / Subtitle</th>
                    <th>Button</th>
                    <th style="width:80px;">Status</th>
                    <th style="width:90px;">Actions</th>
                </tr>
            </thead>
            <tbody id="banner-sortable">
                @forelse($banners as $b)
                <tr data-id="{{ $b->id }}">
                    <td><i class="fas fa-grip-vertical text-muted banner-drag-handle" style="cursor:grab;"></i></td>
                    <td>
                        <img src="{{ storage_link($b->banner_image) }}"
                             alt="{{ $b->banner_image_alt }}"
                             style="height:48px;width:80px;object-fit:cover;border-radius:6px;">
                    </td>
                    <td>
                        <div class="fw-semibold" style="font-size:13px;">{{ $b->title }}</div>
                        @if($b->subtitle)
                        <div class="text-muted" style="font-size:11.5px;">{{ $b->subtitle }}</div>
                        @endif
                    </td>
                    <td>
                        @if($b->button_text)
                        <span class="badge bg-light text-dark border" style="font-size:11px;">
                            {{ $b->button_text }}
                        </span>
                        @endif
                        @if($b->url)
                        <div class="text-muted" style="font-size:11px;">{{ Str::limit($b->url, 35) }}</div>
                        @endif
                    </td>
                    <td>
                        <label class="mini-toggle">
                            <input type="checkbox" class="banner-status-toggle"
                                   data-id="{{ $b->id }}"
                                   data-url="{{ route('admin.banners.update', $b->id) }}"
                                   {{ $b->is_active ? 'checked' : '' }}>
                            <span class="mini-slider"></span>
                        </label>
                    </td>
                    <td>
                        <button class="btn-icon edit-banner" data-id="{{ $b->id }}"
                                data-url="{{ route('admin.banners.edit', $b->id) }}" title="Edit">
                            <i class="fas fa-edit icon"></i>
                            <span class="spinner-border spinner-border-sm d-none"></span>
                        </button>
                        <button class="btn-icon danger delete-banner" data-id="{{ $b->id }}"
                                data-url="{{ route('admin.banners.destroy', $b->id) }}" title="Delete">
                            <i class="fas fa-trash icon"></i>
                            <span class="spinner-border spinner-border-sm d-none"></span>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-5">
                        <i class="fas fa-images fa-2x mb-2 d-block opacity-25"></i>
                        No slider banners yet. Click <strong>Add Slide</strong> to create one.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
