<style>
.pkg-actions { display:flex; flex-wrap:wrap; gap:5px; align-items:center; }
.pkg-actions .btn-sm { width:32px; height:32px; padding:0; display:inline-flex; align-items:center; justify-content:center; flex-shrink:0; }
</style>

<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead class="bg-light">
            <tr>
                <th>Package Name</th>
                <th>Category</th>
                <th>Duration</th>
                <th style="width:90px">Status</th>
                <th style="width:200px">Actions</th>
            </tr>
        </thead>
        <tbody>
        @foreach($packages as $package)
            <tr>
                {{-- Package Name --}}
                <td>
                    <strong class="text-primary d-block">{{ $package->title }}</strong>
                    <small class="text-muted">
                        @if(($package->details->duration_days ?? 0) > 0)
                            {{ $package->details->duration_days }} {{ $package->details->duration_days == 1 ? 'Day' : 'Days' }}
                        @endif
                        @if(($package->details->duration_days ?? 0) > 0 && ($package->details->duration_nights ?? 0) > 0) / @endif
                        @if(($package->details->duration_nights ?? 0) > 0)
                            {{ $package->details->duration_nights }} {{ $package->details->duration_nights == 1 ? 'Night' : 'Nights' }}
                        @endif
                    </small>
                    <div style="font-size:11px; color:#94a3b8; margin-top:2px;">
                        Uploaded on: {{ optional($package->created_at)->format('d M Y, h:i A') ?? 'N/A' }}
                        @if(!empty($package->author_name)) by {{ $package->author_name }} @endif
                    </div>
                </td>

                {{-- Category --}}
                <td>{{ $package->category->name ?? '—' }}</td>

                {{-- Duration --}}
                <td>
                    @if(($package->details->duration_days ?? 0) > 0)
                        {{ $package->details->duration_days }} {{ $package->details->duration_days == 1 ? 'Day' : 'Days' }}
                    @endif
                    @if(($package->details->duration_days ?? 0) > 0 && ($package->details->duration_nights ?? 0) > 0) / @endif
                    @if(($package->details->duration_nights ?? 0) > 0)
                        {{ $package->details->duration_nights }} {{ $package->details->duration_nights == 1 ? 'Night' : 'Nights' }}
                    @endif
                </td>

                {{-- Status --}}
                <td>
                    <input type="checkbox"
                           id="status_{{ $package->id }}"
                           class="js-switch package-status"
                           data-id="{{ $package->id }}"
                           data-url="{{ route('admin.packages.update', $package->id) }}"
                           {{ $package->is_active ? 'checked' : '' }}>
                </td>

                {{-- Actions --}}
                <td>
                    <div class="pkg-actions">
                        {{-- View --}}
                        <button class="btn btn-sm btn-outline-primary package-details"
                                data-id="{{ $package->id }}"
                                title="View Details">
                            <i class="fas fa-eye icon"></i>
                            <span class="spinner-border spinner-border-sm d-none"></span>
                        </button>

                        {{-- Edit --}}
                        <a href="{{ route('admin.packages.edit', $package->id) }}"
                           class="btn btn-sm btn-outline-success"
                           title="Edit Package">
                            <i class="fas fa-edit"></i>
                        </a>

                        {{-- FAQs --}}
                        <button class="btn btn-sm btn-outline-primary package-faqs"
                                data-id="{{ $package->id }}"
                                data-title="{{ $package->title }}"
                                data-url="{{ route('admin.packages.faqUpdate', $package->id) }}"
                                title="FAQs">
                            <i class="fa fa-question-circle icon"></i>
                            <span class="spinner-border spinner-border-sm d-none"></span>
                        </button>

                        {{-- Meta --}}
                        <button class="btn btn-sm btn-outline-primary package-meta"
                                data-id="{{ $package->id }}"
                                data-url="{{ route('admin.packages-meta.show.meta', $package->id) }}"
                                data-upurl="{{ route('admin.packages.update', $package->id) }}"
                                title="SEO / Meta">
                            <i class="fa fa-globe icon"></i>
                            <span class="spinner-border spinner-border-sm d-none"></span>
                        </button>

                        {{-- Delete --}}
                        <button class="btn btn-sm btn-outline-danger delete-package"
                                data-id="{{ $package->id }}"
                                data-url="{{ route('admin.packages.destroy', $package->id) }}"
                                title="Delete">
                            <i class="fas fa-trash icon"></i>
                            <span class="spinner-border spinner-border-sm d-none"></span>
                        </button>

                        {{-- Live Preview --}}
                        @if($package->is_active)
                        <a href="{{ config('app.frontend_url') }}/tour-packages/{{ $package->slug }}"
                           class="btn btn-sm btn-outline-success"
                           target="_blank"
                           title="Live Preview">
                            <i class="fa fa-tv"></i>
                        </a>
                        @endif
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@include('admin.common.pagination', ['paginator' => $packages->appends(request()->query())])
