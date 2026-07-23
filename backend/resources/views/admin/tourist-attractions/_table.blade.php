<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>City / State</th>
                        <th width="110">Status</th>
                        <th width="100">Popular</th>
                        <th width="260">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attractions as $i => $ta)
                    <tr id="ta-row-{{ $ta->id }}">
                        <td class="fw-medium"><img src="{{ storage_link($ta->banner_image) }}" width="100"></td>
                        <td class="fw-medium">{{ $ta->name }}</td>
                        <td>{{ $ta->location->name ?? '—' }}, {{ $ta->state->name ?? '—' }}</td>
                        <td>
                            <input type="checkbox" class="js-switch ta-status"
                                   data-url="{{ route('admin.tourist-attractions.toggle-status', $ta->id) }}"
                                   {{ $ta->is_active ? 'checked' : '' }}>
                        </td>
                        <td>
                            <input type="checkbox" class="js-switch ta-popular"
                                   data-url="{{ route('admin.tourist-attractions.toggle-popular', $ta->id) }}"
                                   {{ $ta->is_popular ? 'checked' : '' }}>
                        </td>
                        <td class="text-nowrap">
                            <button class="btn btn-sm btn-outline-success btn-ta-edit"
                                    data-fetch="{{ route('admin.tourist-attractions.show', $ta->id) }}"
                                    data-update="{{ route('admin.tourist-attractions.update', $ta->id) }}"
                                    title="Edit Core Info">
                                <i class="fas fa-edit"></i>
                            </button>
<button class="btn btn-sm btn-outline-info btn-ta-quick-info"
                                    data-fetch="{{ route('admin.tourist-attractions.show', $ta->id) }}"
                                    data-update="{{ route('admin.tourist-attractions.update-section', $ta->id) }}"
                                    title="Quick Information">
                                <i class="fas fa-list-ul"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-orange btn-ta-why-visit"
                                    data-fetch="{{ route('admin.tourist-attractions.show', $ta->id) }}"
                                    data-update="{{ route('admin.tourist-attractions.update-section', $ta->id) }}"
                                    title="Why Visit & Highlights">
                                <i class="fas fa-star"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-success btn-ta-activities"
                                    data-fetch="{{ route('admin.tourist-attractions.show', $ta->id) }}"
                                    data-update="{{ route('admin.tourist-attractions.update-section', $ta->id) }}"
                                    title="Things To Do">
                                <i class="fas fa-hiking"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-secondary btn-ta-gallery"
                                    data-fetch="{{ route('admin.tourist-attractions.show', $ta->id) }}"
                                    data-add="{{ route('admin.tourist-attractions.gallery.store', $ta->id) }}"
                                    title="Gallery">
                                <i class="fas fa-images"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-warning btn-ta-faq"
                                    data-fetch="{{ route('admin.tourist-attractions.show', $ta->id) }}"
                                    data-update="{{ route('admin.tourist-attractions.update-section', $ta->id) }}"
                                    title="FAQs">
                                <i class="fas fa-question-circle"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-purple btn-ta-meta"
                                    data-fetch="{{ route('admin.tourist-attractions.show', $ta->id) }}"
                                    data-update="{{ route('admin.tourist-attractions.update-section', $ta->id) }}"
                                    title="SEO Meta">
                                <i class="fas fa-globe"></i>
                            </button>
                            @if($ta->state && $ta->location)
                            <a href="{{ config('app.frontend_url') }}/{{ $ta->state->slug }}/{{ $ta->location->slug }}/{{ $ta->slug }}" target="_blank"
                               class="btn btn-sm btn-outline-success" title="View on Site">
                                <i class="fas fa-tv"></i>
                            </a>
                            @endif
                            <button class="btn btn-sm btn-outline-danger delete-ta"
                                    data-url="{{ route('admin.tourist-attractions.destroy', $ta->id) }}"
                                    title="Delete">
                                <i class="fas fa-trash icon"></i>
                                <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted py-5">No tourist attractions found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($attractions->lastPage() > 1)
    <div class="card-footer">
        @include('admin.common.pagination', ['paginator' => $attractions])
    </div>
    @endif
</div>
