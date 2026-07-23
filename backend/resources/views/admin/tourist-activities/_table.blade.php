<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>City / State</th>
                        <th width="110">Status</th>
                        <th width="100">Popular</th>
                        <th width="260">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activities as $i => $ac)
                    <tr id="tac-row-{{ $ac->id }}">
                        <td class="fw-medium">{{ $ac->name }}</td>
                        <td>{{ $ac->location->name ?? '—' }}, {{ $ac->state->name ?? '—' }}</td>
                        <td>
                            <input type="checkbox" class="js-switch tac-status"
                                   data-url="{{ route('admin.tourist-activities.toggle-status', $ac->id) }}"
                                   {{ $ac->is_active ? 'checked' : '' }}>
                        </td>
                        <td>
                            <input type="checkbox" class="js-switch tac-popular"
                                   data-url="{{ route('admin.tourist-activities.toggle-popular', $ac->id) }}"
                                   {{ $ac->is_popular ? 'checked' : '' }}>
                        </td>
                        <td class="text-nowrap">
                            <button class="btn btn-sm btn-outline-success btn-tac-edit"
                                    data-fetch="{{ route('admin.tourist-activities.show', $ac->id) }}"
                                    data-update="{{ route('admin.tourist-activities.update', $ac->id) }}"
                                    title="Edit Core Info">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-success btn-tac-itinerary"
                                    data-fetch="{{ route('admin.tourist-activities.show', $ac->id) }}"
                                    data-update="{{ route('admin.tourist-activities.update-section', $ac->id) }}"
                                    title="Itinerary / What To Expect">
                                <i class="fas fa-route"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-primary btn-tac-experiences"
                                    data-fetch="{{ route('admin.tourist-activities.show', $ac->id) }}"
                                    data-update="{{ route('admin.tourist-activities.update-section', $ac->id) }}"
                                    title="Explore Finest Activities">
                                <i class="fas fa-compass"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-success btn-tac-things-to-do"
                                    data-fetch="{{ route('admin.tourist-activities.show', $ac->id) }}"
                                    data-update="{{ route('admin.tourist-activities.update-section', $ac->id) }}"
                                    title="Things To Do">
                                <i class="fas fa-list-check"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-secondary btn-tac-gallery"
                                    data-fetch="{{ route('admin.tourist-activities.show', $ac->id) }}"
                                    data-add="{{ route('admin.tourist-activities.gallery.store', $ac->id) }}"
                                    title="Gallery">
                                <i class="fas fa-images"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-warning btn-tac-faq"
                                    data-fetch="{{ route('admin.tourist-activities.show', $ac->id) }}"
                                    data-update="{{ route('admin.tourist-activities.update-section', $ac->id) }}"
                                    title="FAQs">
                                <i class="fas fa-question-circle"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-purple btn-tac-meta"
                                    data-fetch="{{ route('admin.tourist-activities.show', $ac->id) }}"
                                    data-update="{{ route('admin.tourist-activities.update-section', $ac->id) }}"
                                    title="SEO Meta">
                                <i class="fas fa-globe"></i>
                            </button>
                            @if($ac->state && $ac->location)
                            <a href="{{ config('app.frontend_url') }}/{{ $ac->state->slug }}/{{ $ac->location->slug }}/{{ $ac->slug }}" target="_blank"
                               class="btn btn-sm btn-outline-success" title="View on Site">
                                <i class="fas fa-tv"></i>
                            </a>
                            @endif
                            <button class="btn btn-sm btn-outline-danger delete-tac"
                                    data-url="{{ route('admin.tourist-activities.destroy', $ac->id) }}"
                                    title="Delete">
                                <i class="fas fa-trash icon"></i>
                                <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted py-5">No tourist activities found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($activities->lastPage() > 1)
    <div class="card-footer">
        @include('admin.common.pagination', ['paginator' => $activities])
    </div>
    @endif
</div>
