@php $s3BaseUrl = rtrim(config('filesystems.disks.'.config('filesystems.upload_disk').'.url', ''), '/'); @endphp

<div class="card-body p-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th width="70">Banner</th>
                    <th>Name</th>
                    <th width="90">Status</th>
                    <th width="90">Popular</th>
                    <th width="300">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $row)
                <tr>
                    <td>
                        @if($row->banner_image)
                            <img src="{{ $s3BaseUrl . '/' . ltrim($row->banner_image, '/') }}"
                                 style="height:34px;width:52px;object-fit:cover;border-radius:4px;">
                        @else
                            <span class="text-muted small">—</span>
                        @endif
                    </td>
                    <td class="fw-semibold">
                        {{ $row->display_name }}
                        @if($row->type === 'City' && $row->location?->state)
                            <span class="text-muted small">({{ $row->location->state->name }})</span>
                        @endif
                    </td>
                    <td>
                        <input type="checkbox" class="js-switch toggle-status"
                               data-id="{{ $row->id }}"
                               {{ $row->is_active ? 'checked' : '' }}>
                    </td>
                    <td>
                        <input type="checkbox" class="js-switch toggle-popular"
                               data-id="{{ $row->id }}"
                               {{ $row->is_popular ? 'checked' : '' }}>
                    </td>
                    <td class="text-nowrap">
                        <div class="d-flex gap-1 flex-nowrap">
                            @if($row->type === 'Region')
                            <button class="btn btn-sm btn-outline-primary btn-edit-basic"
                                    data-id="{{ $row->id }}" title="Edit">
                                <i class="fas fa-pen-to-square"></i>
                            </button>
                            @else
                            <a href="{{ route('admin.city-pages.edit', $row->id) }}"
                               class="btn btn-sm btn-outline-primary" title="Edit Content">
                                <i class="fas fa-pen-to-square"></i>
                            </a>
                            @endif
                            <button class="btn btn-sm btn-outline-orange btn-settings"
                                    data-id="{{ $row->id }}" title="{{ $row->type === 'Region' ? 'Description' : 'Banner & Settings' }}">
                                <i class="fas fa-cog"></i>
                            </button>
                            @if($row->type !== 'Region')
                            <button class="btn btn-sm btn-outline-secondary btn-quick-facts"
                                    data-id="{{ $row->id }}" title="Quick Facts">
                                <i class="fas fa-list-check"></i>
                            </button>
                            @endif
                            <button class="btn btn-sm btn-outline-warning btn-faqs"
                                    data-id="{{ $row->id }}" title="FAQs">
                                <i class="fas fa-question-circle"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-purple btn-meta"
                                    data-id="{{ $row->id }}" title="SEO / Meta">
                                <i class="fas fa-globe"></i>
                            </button>
                            @if($row->type === 'State' && $row->state)
                            <a href="{{ config('app.frontend_url') }}/{{ $row->state->slug }}" target="_blank"
                               class="btn btn-sm btn-outline-success" title="View on Site">
                                <i class="fas fa-tv"></i>
                            </a>
                            @elseif($row->type === 'City' && $row->location?->state)
                            <a href="{{ config('app.frontend_url') }}/{{ $row->location->state->slug }}/{{ $row->location->slug }}" target="_blank"
                               class="btn btn-sm btn-outline-success" title="View on Site">
                                <i class="fas fa-tv"></i>
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-5">
                        <i class="fas fa-city fa-2x mb-2 d-block opacity-25"></i>
                        No records found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@if($rows->hasPages())
<div class="card-footer">
    @include('admin.common.pagination', ['paginator' => $rows->appends(['tab' => $tab])])
</div>
@endif
