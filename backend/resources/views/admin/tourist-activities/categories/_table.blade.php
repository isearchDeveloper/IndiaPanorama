<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="50">#</th>
                        <th width="80">Image</th>
                        <th>Name</th>
                        <th>Activities</th>
                        <th width="110">Status</th>
                        <th width="120">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $i => $cat)
                    <tr id="tacat-row-{{ $cat->id }}">
                        <td>{{ $categories->firstItem() + $i }}</td>
                        <td>
                            @if($cat->image)
                            <img src="{{ storage_link($cat->image) }}" class="rounded" style="width:48px;height:48px;object-fit:cover;">
                            @else
                            —
                            @endif
                        </td>
                        <td class="fw-medium">{{ $cat->name }}</td>
                        <td>{{ $cat->activities()->count() }}</td>
                        <td>
                            <input type="checkbox" class="js-switch tacat-status"
                                   data-url="{{ route('admin.tourist-activity-categories.toggle-status', $cat->id) }}"
                                   {{ $cat->is_active ? 'checked' : '' }}>
                        </td>
                        <td class="text-nowrap">
                            <button class="btn btn-sm btn-outline-primary btn-edit-tacat" data-id="{{ $cat->id }}" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger delete-tacat"
                                    data-url="{{ route('admin.tourist-activity-categories.destroy', $cat->id) }}"
                                    title="Delete">
                                <i class="fas fa-trash icon"></i>
                                <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-5">No categories found.</td></tr>
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
