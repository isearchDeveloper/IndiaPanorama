<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="50">#</th>
                        <th>Title</th>
                        <th>Category / Subcategory</th>
                        <th>State / City</th>
                        <th width="110">Status</th>
                        <th width="100">Popular</th>
                        <th width="180">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($experiences as $i => $experience)
                    <tr id="experience-row-{{ $experience->id }}">
                        <td><img src="{{storage_link($experience->banner_image)}}" width="100"></td>
                        <td class="fw-medium">{{ $experience->title }}</td>
                        <td class="small text-muted">
                            {{ $experience->category->name ?? '—' }}
                            @if($experience->subcategory)
                            <br><span class="text-muted">{{ $experience->subcategory->name }}</span>
                            @else
                            <br><span class="badge bg-light text-muted border">No subcategory</span>
                            @endif
                        </td>
                        <td class="small text-muted">{{ $experience->state->name ?? '—' }} / {{ $experience->location->name ?? '—' }}</td>
                        <td>
                            <input type="checkbox"
                                   class="js-switch experience-status"
                                   data-id="{{ $experience->id }}"
                                   data-url="{{ route('admin.experiences.toggle-status', $experience->id) }}"
                                   {{ $experience->is_active ? 'checked' : '' }}>
                        </td>
                        <td>
                            <input type="checkbox"
                                   class="js-switch experience-popular"
                                   data-id="{{ $experience->id }}"
                                   data-url="{{ route('admin.experiences.toggle-popular', $experience->id) }}"
                                   {{ $experience->is_popular ? 'checked' : '' }}>
                        </td>
                        <td class="text-nowrap">
                            <button class="btn btn-sm btn-outline-primary btn-edit-experience" data-id="{{ $experience->id }}" title="Edit"><i class="fas fa-edit"></i></button>
                            <button class="btn btn-sm btn-outline-info btn-experience-gallery" data-id="{{ $experience->id }}" title="Gallery &amp; Quick Info"><i class="fas fa-cogs"></i></button>
                            <button class="btn btn-sm btn-outline-success btn-experience-highlights" data-id="{{ $experience->id }}" title="Highlights"><i class="fas fa-star"></i></button>
                            <button class="btn btn-sm btn-outline-warning btn-experience-faq" data-id="{{ $experience->id }}" title="FAQs"><i class="fas fa-question-circle"></i></button>
                            <button class="btn btn-sm btn-outline-purple btn-experience-meta" data-id="{{ $experience->id }}" title="SEO Meta"><i class="fas fa-globe"></i></button>
                            @if($experience->state)
                            <a href="{{ config('app.frontend_url') }}/{{ $experience->state->city_guide_slug }}/{{ $experience->location ? $experience->location->city_guide_slug . '/' : '' }}{{ $experience->slug }}-experience" target="_blank"
                               class="btn btn-sm btn-outline-success" title="Preview">
                                <i class="fas fa-tv"></i>
                            </a>
                            @endif
                            <button class="btn btn-sm btn-outline-danger delete-experience"
                                    data-id="{{ $experience->id }}"
                                    data-url="{{ route('admin.experiences.destroy', $experience->id) }}"
                                    title="Delete">
                                <i class="fas fa-trash icon"></i>
                                <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-5">No experiences found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($experiences->lastPage() > 1)
    <div class="card-footer">
        @include('admin.common.pagination', ['paginator' => $experiences])
    </div>
    @endif
</div>
