<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="80">Image</th>
                        <th>Name</th>
                        <th>State</th>
                        <th width="110">Status</th>
                        <th width="120">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($festivals as $i => $festival)
                    <tr id="festival-row-{{ $festival->id }}">
                        <td>
                            @if($festival->image)
                            <img src="{{ storage_link($festival->image) }}" alt="{{ $festival->image_alt }}"
                                 class="rounded" style="width:48px;height:48px;object-fit:cover;">
                            @else
                            <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="fw-medium">{{ $festival->name }}</td>
                        <td>{{ $festival->state->name ?? '—' }}</td>
                        <td>
                            <input type="checkbox"
                                   class="js-switch festival-status"
                                   data-id="{{ $festival->id }}"
                                   data-url="{{ route('admin.festival.toggle-status', $festival->id) }}"
                                   {{ $festival->is_active ? 'checked' : '' }}>
                        </td>
                        <td class="text-nowrap">
                            <button class="btn btn-sm btn-outline-primary btn-edit-festival"
                                    data-id="{{ $festival->id }}" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-purple btn-festival-setting"
                                    data-id="{{ $festival->id }}" data-name="{{ $festival->name }}" title="Setting — Long Description">
                                <i class="fas fa-cog"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-info btn-festival-key-experience"
                                    data-id="{{ $festival->id }}" data-name="{{ $festival->name }}" title="Key Experiences">
                                <i class="fas fa-star"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-primary btn-festival-stats"
                                    data-id="{{ $festival->id }}" data-name="{{ $festival->name }}" title="Quick Stats">
                                <i class="fas fa-chart-bar"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-info btn-festival-highlights"
                                    data-id="{{ $festival->id }}" data-name="{{ $festival->name }}" title="Festival Highlights">
                                <i class="fas fa-images"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-success btn-festival-places"
                                    data-id="{{ $festival->id }}" data-name="{{ $festival->name }}" title="Popular Places">
                                <i class="fas fa-map-marker-alt"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-success btn-festival-how-to-reach"
                                    data-id="{{ $festival->id }}" data-name="{{ $festival->name }}" title="How to Reach">
                                <i class="fas fa-route"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-warning btn-festival-why-visit"
                                    data-id="{{ $festival->id }}" data-name="{{ $festival->name }}" title="Why Visit">
                                <i class="fas fa-heart"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-warning btn-festival-faqs"
                                    data-id="{{ $festival->id }}" data-name="{{ $festival->name }}" title="FAQs">
                                <i class="fas fa-question-circle"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-purple btn-festival-meta"
                                    data-id="{{ $festival->id }}" data-name="{{ $festival->name }}" title="SEO Meta">
                                <i class="fas fa-globe"></i>
                            </button>
                            <a href="{{ config('app.frontend_url') }}/festivals/{{ $festival->slug }}" target="_blank"
                               class="btn btn-sm btn-outline-success" title="View on Site">
                                <i class="fas fa-tv"></i>
                            </a>
                            <button class="btn btn-sm btn-outline-danger delete-festival"
                                    data-id="{{ $festival->id }}"
                                    data-url="{{ route('admin.festival.destroy', $festival->id) }}"
                                    title="Delete">
                                <i class="fas fa-trash icon"></i>
                                <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted py-5">No festivals found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($festivals->lastPage() > 1)
    <div class="card-footer">
        @include('admin.common.pagination', ['paginator' => $festivals])
    </div>
    @endif
</div>
