<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                         <th width="80">Image</th>
                        <th>State</th>
                        <th>Featured Festival</th>
                        <th width="110">Status</th>
                        <th width="190">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pages as $i => $page)
                    <tr id="fsp-row-{{ $page->id }}">
                        <td>
                            @if($page->image)
                            <img src="{{ storage_link($page->image) }}" alt="{{ $page->image_alt }}"
                                 class="rounded" style="width:48px;height:48px;object-fit:cover;">
                            @else
                            <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="fw-medium">{{ $page->state->name ?? '—' }}</td>
                        <td>{{ $page->featuredFestival->name ?? '—' }}</td>
                        <td>
                            <input type="checkbox"
                                   class="js-switch fsp-status"
                                   data-url="{{ route('admin.festival-state-pages.toggle-status', $page->id) }}"
                                   {{ $page->is_active ? 'checked' : '' }}>
                        </td>
                        <td class="text-nowrap">
                            <button class="btn btn-sm btn-outline-orange btn-fsp-settings"
                                    data-fetch="{{ route('admin.festival-state-pages.show', $page->id) }}"
                                    data-update="{{ route('admin.festival-state-pages.update-section', $page->id) }}"
                                    title="Banner & Settings">
                                <i class="fas fa-cog"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-primary btn-fsp-featured"
                                    data-fetch="{{ route('admin.festival-state-pages.show', $page->id) }}"
                                    data-update="{{ route('admin.festival-state-pages.update-section', $page->id) }}"
                                    title="Featured Festival">
                                <i class="fas fa-star"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-info btn-fsp-why-visit"
                                    data-fetch="{{ route('admin.festival-state-pages.show', $page->id) }}"
                                    data-update="{{ route('admin.festival-state-pages.update-section', $page->id) }}"
                                    title="Why Visit During Festivals">
                                <i class="fas fa-circle-check"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-warning btn-fsp-faq"
                                    data-fetch="{{ route('admin.festival-state-pages.show', $page->id) }}"
                                    data-update="{{ route('admin.festival-state-pages.update-section', $page->id) }}"
                                    title="FAQs">
                                <i class="fas fa-question-circle"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-purple btn-fsp-meta"
                                    data-fetch="{{ route('admin.festival-state-pages.show', $page->id) }}"
                                    data-update="{{ route('admin.festival-state-pages.update-section', $page->id) }}"
                                    title="SEO Meta">
                                <i class="fas fa-globe"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center text-muted py-5">No state festival pages found.</td></tr>
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
