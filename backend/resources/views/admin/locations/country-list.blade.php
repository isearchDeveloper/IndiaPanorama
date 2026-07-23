@foreach($countries as $key => $c)
    <div class="d-flex justify-content-between align-items-center py-2">
        <div class="co-md-6">
            <span>{{ $c->name }}</span>
            <small class="text-muted d-block">Packages: {{ $c->packages->count() }}</small>
            <small class="text-muted d-block">Author: {{$c->details->author_name ?? '---'}}</small>
        </div>

        <div class="co-md-6">
            <a href="https://www.indianpanorama.in/india" title="preview" class="btn btn-sm btn-outline-primary" target="_blank">
                <i class="fa fa-tv" aria-hidden="true"></i>
            </a>
        </div>
    </div>
@endforeach

@if($countries->lastPage() > 1)
    <hr>
    <div class="mt-2 custom-pagination">
        @include('admin.common.pagination', ['paginator' => $countries->appends(request()->only('country'))])
    </div>
@endif

