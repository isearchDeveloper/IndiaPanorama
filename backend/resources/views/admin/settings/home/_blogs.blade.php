{{-- ══ TAB: Latest Blogs ══ --}}
<div class="tab-pane fade" id="cms-pane-blogs">

    @php $blogSec = $sections->get('latest_blogs'); @endphp

    {{-- Section Settings: Title + Subtitle only --}}
    <div class="sec-heading-panel">
        <div class="panel-label"><i class="fas fa-cog"></i>Section Settings</div>
        <form class="row g-3 align-items-end" id="blogs-heading-form">
            <div class="col-md-5">
                <label class="form-label fw-semibold small">Section Title</label>
                <input type="text" class="form-control form-control-sm" name="title"
                       value="{{ $blogSec?->title }}" placeholder="Latest Blogs">
            </div>
            <div class="col-md-5">
                <label class="form-label fw-semibold small">Sub-title</label>
                <input type="text" class="form-control form-control-sm" name="subtitle"
                       value="{{ $blogSec?->subtitle }}">
            </div>
            <div class="col-md-2 mt-3">
                <button type="button" class="btn btn-sm btn-primary w-100" id="blogs-heading-save">
                    <i class="fas fa-save me-1"></i>Save
                </button>
            </div>
        </form>
    </div>

    {{-- Blog List Header --}}
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h6 class="mb-0 fw-700">
            <i class="fas fa-images me-2 text-primary"></i>Blog Items
        </h6>
        <button type="button" class="btn btn-sm btn-primary" id="add-blog-item-btn">
            <i class="fas fa-plus me-1"></i>Add Blog
        </button>
    </div>

    {{-- Blog Items Table --}}
    <div class="table-responsive">
        <table class="table table-sm table-hover align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th style="width:36px;"></th>
                    <th style="width:80px;">Image</th>
                    <th>Title</th>
                    <th>Alt Tag</th>
                    <th>Link / URL</th>
                    <th style="width:90px;">Actions</th>
                </tr>
            </thead>
            <tbody id="blog-items-list">
                @forelse($blog_items as $item)
                <tr data-id="{{ $item->id }}">
                    <td class="blog-drag-handle text-center" style="cursor:grab;">
                        <i class="fas fa-grip-vertical text-muted"></i>
                    </td>
                    <td>
                        @if($item->image)
                        <img src="{{ storage_link($item->image) }}" alt="{{ $item->image_alt }}"
                             style="width:64px;height:46px;object-fit:cover;border-radius:4px;">
                        @else
                        <div style="width:64px;height:46px;background:#f1f5f9;border-radius:4px;
                                    display:flex;align-items:center;justify-content:center;">
                            <i class="fas fa-image text-muted opacity-50"></i>
                        </div>
                        @endif
                    </td>
                    <td class="small fw-semibold">{{ $item->title ?: '—' }}</td>
                    <td class="small">{{ $item->image_alt ?: '—' }}</td>
                    <td class="small text-truncate" style="max-width:200px;">
                        {{ $item->link ?: '—' }}
                    </td>
                    <td>
                        <button class="btn btn-xs btn-outline-primary edit-blog-item me-1"
                                data-id="{{ $item->id }}" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-xs btn-outline-danger delete-blog-item"
                                data-id="{{ $item->id }}" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr id="blog-items-empty">
                    <td colspan="6" class="text-center text-muted py-5">
                        <i class="fas fa-images fa-2x opacity-25 mb-2 d-block"></i>
                        No blog items yet. Click <strong>Add Blog</strong> to get started.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
