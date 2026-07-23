<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="50">#</th>
                        <th width="80">Image</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Slug</th>
                        <th width="110">Status</th>
                        <th width="180">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subcategories as $i => $subcategory)
                    <tr id="subcategory-row-{{ $subcategory->id }}">
                        <td>{{ $subcategories->firstItem() + $i }}</td>
                        <td>
                            @if($subcategory->image)
                            <img src="{{ storage_link($subcategory->image) }}" alt="{{ $subcategory->image_alt }}"
                                 class="rounded" style="width:48px;height:48px;object-fit:cover;">
                            @else
                            <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="fw-medium">{{ $subcategory->name }}</td>
                        <td class="small text-muted">{{ $subcategory->category->name ?? '—' }}</td>
                        <td class="small text-muted">{{ $subcategory->slug }}</td>
                        <td>
                            <input type="checkbox"
                                   class="js-switch subcategory-status"
                                   data-id="{{ $subcategory->id }}"
                                   data-url="{{ route('admin.experience-subcategories.toggle-status', $subcategory->id) }}"
                                   {{ $subcategory->is_active ? 'checked' : '' }}>
                        </td>
                        <td class="text-nowrap">
                            <button class="btn btn-sm btn-outline-primary btn-edit-subcategory" data-id="{{ $subcategory->id }}" title="Edit"><i class="fas fa-edit"></i></button>
                            <button class="btn btn-sm btn-outline-danger delete-subcategory"
                                    data-id="{{ $subcategory->id }}"
                                    data-url="{{ route('admin.experience-subcategories.destroy', $subcategory->id) }}"
                                    title="Delete">
                                <i class="fas fa-trash icon"></i>
                                <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-5">No subcategories found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($subcategories->lastPage() > 1)
    <div class="card-footer">
        @include('admin.common.pagination', ['paginator' => $subcategories])
    </div>
    @endif
</div>
