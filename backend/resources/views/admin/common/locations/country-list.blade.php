@foreach($countries as $key => $c)
    <div class="d-flex justify-content-between align-items-center py-2">
        <div class="co-md-6">
            <span>{{ $c->name }}</span>
            <small class="text-muted d-block">Continent: {{ $c->continent->name }}</small>
            <small class="text-muted d-block">Packages: {{ $c->packages->count() }}</small>
        </div>

        @if($c->code != 'IN')
        <div class="co-md-6">
            <button class="btn btn-sm btn-outline-primary add-country-details"
                    data-id="{{ $c->id }}"
                    data-url="{{ route('admin.countries.update',$c->id) }}"
                    data-eurl="{{ route('admin.countries.show',$c->id) }}">
                <i class="fas fa-cog icon"></i>
                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
            </button>
            <button class="btn btn-sm btn-outline-primary country-faqs"
                    data-id="{{ $c->id }}"
                    data-title="{{ $c->title }}"
                    data-url="{{ route('admin.countries.faqUpdate',$c->id) }}">
                <i class="fa fa-question-circle icon"></i>
                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
            </button>
            <button class="btn btn-sm btn-outline-primary country-meta"
                    data-id="{{ $c->id }}"
                    data-upurl="{{ route('admin.countries.update',$c->id) }}"
                    data-url="{{ route('admin.countries-meta.show.meta',$c->id) }}">
                <i class="fa fa-globe icon"></i>
                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
            </button>
            @if($c->details)
                <button class="btn btn-sm btn-outline-primary country-details"
                        data-id="{{ $c->id }}"
                        data-url="{{ route('admin.countries.show',$c->id) }}">
                    <i class="fas fa-eye icon"></i>
                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                </button>
            @endif
        </div>
        @endif
    </div>
@endforeach

@if($countries->lastPage() > 1)
    <hr>
    <div class="mt-2 custom-pagination">
        @include('admin.common.pagination', ['paginator' => $countries->appends(request()->only('country'))])
    </div>
@endif

