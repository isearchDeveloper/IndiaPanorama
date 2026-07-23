<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="50">#</th>
                        <th>State</th>
                        <th width="110">Status</th>
                        <th width="100">Popular</th>
                        <th width="320">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pages as $i => $page)
                    <tr id="page-row-{{ $page->id }}">
                        <td><img src="{{storage_link($page->banner_image)}}" width="100"></td>
                        <td class="fw-medium">{{ $page->state->name ?? '—' }}</td>
                        <td>
                            <input type="checkbox" class="js-switch page-status" data-id="{{ $page->id }}"
                                   data-url="{{ route('admin.experience-pages.toggle-status', $page->id) }}"
                                   {{ $page->is_active ? 'checked' : '' }}>
                        </td>
                        <td>
                            <input type="checkbox" class="js-switch page-popular" data-id="{{ $page->id }}"
                                   data-url="{{ route('admin.experience-pages.toggle-featured', $page->id) }}"
                                   {{ $page->is_featured ? 'checked' : '' }}>
                        </td>
                        <td class="text-nowrap">
                            <button class="btn btn-sm btn-outline-info btn-page-banner" data-id="{{ $page->id }}" title="Banner & Settings"><i class="fas fa-cog"></i></button>
                            <button class="btn btn-sm btn-outline-primary btn-page-activities" data-id="{{ $page->id }}" title="Adventure Experiences"><i class="fas fa-hiking"></i></button>
                            <button class="btn btn-sm btn-outline-success btn-page-highlights" data-id="{{ $page->id }}" title="What Makes It Special"><i class="fas fa-star"></i></button>
                            <button class="btn btn-sm btn-outline-warning btn-page-faq" data-id="{{ $page->id }}" title="FAQs"><i class="fas fa-question-circle"></i></button>
                            <button class="btn btn-sm btn-outline-purple btn-page-meta" data-id="{{ $page->id }}" title="SEO Meta"><i class="fas fa-globe"></i></button>
                            @if($page->state)
                            <a href="{{ config('app.frontend_url') }}/{{ $page->state->city_guide_slug }}/experiences" target="_blank"
                               class="btn btn-sm btn-outline-success" title="Preview">
                                <i class="fas fa-tv"></i>
                            </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted py-5">No state pages yet — a state's page is created automatically once an Experience is added for it.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($pages->lastPage() > 1)
    <div class="card-footer">
        @include('admin.common.pagination', ['paginator' => $pages])
    </div>
    @endif
</div>
