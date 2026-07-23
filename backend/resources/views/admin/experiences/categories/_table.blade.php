<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="80">Image</th>
                        <th>Name</th>
                        <th>Slug</th>
                        <th width="110">Status</th>
                        <th width="320">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $i => $category)
                    <tr id="category-row-{{ $category->id }}">
                        <td>
                            @if($category->image)
                            <img src="{{ storage_link($category->image) }}" alt="{{ $category->image_alt }}"
                                 class="rounded" style="width:48px;height:48px;object-fit:cover;">
                            @else
                            <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="fw-medium">{{ $category->name }}</td>
                        <td class="small text-muted">{{ $category->slug }}</td>
                        <td>
                            <input type="checkbox"
                                   class="js-switch category-status"
                                   data-id="{{ $category->id }}"
                                   data-url="{{ route('admin.experience-categories.toggle-status', $category->id) }}"
                                   {{ $category->is_active ? 'checked' : '' }}>
                        </td>
                        <td class="text-nowrap">
                            <button class="btn btn-sm btn-outline-primary btn-edit-category" data-id="{{ $category->id }}" title="Edit"><i class="fas fa-edit"></i></button>
                            <a href="{{ route('admin.experience-subcategories.index', ['category_id' => $category->id]) }}"
                               class="btn btn-sm btn-outline-success" title="Manage Subcategories">
                                <i class="fas fa-tags"></i>
                            </a>
                            <button class="btn btn-sm btn-outline-secondary btn-category-quick-info" data-id="{{ $category->id }}" title="Quick Info"><i class="fas fa-list"></i></button>
                            <button class="btn btn-sm btn-outline-success btn-category-perfect-for" data-id="{{ $category->id }}" title="Perfect For"><i class="fas fa-star"></i></button>
                            <button class="btn btn-sm btn-outline-warning btn-category-faq" data-id="{{ $category->id }}" title="FAQs"><i class="fas fa-question-circle"></i></button>
                            <button class="btn btn-sm btn-outline-purple btn-category-meta" data-id="{{ $category->id }}" title="SEO Meta"><i class="fas fa-globe"></i></button>
                            <a href="{{ config('app.frontend_url') }}/experiences/{{ $category->slug }}" target="_blank"
                               class="btn btn-sm btn-outline-success" title="Preview">
                                <i class="fas fa-tv"></i>
                            </a>
                            <button class="btn btn-sm btn-outline-danger delete-category"
                                    data-id="{{ $category->id }}"
                                    data-url="{{ route('admin.experience-categories.destroy', $category->id) }}"
                                    title="Delete">
                                <i class="fas fa-trash icon"></i>
                                <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted py-5">No categories found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($categories->lastPage() > 1)
    <div class="card-footer">
        @include('admin.common.pagination', ['paginator' => $categories])
    </div>
    @endif
</div>
