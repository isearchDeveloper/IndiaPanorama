@foreach($locations as $key => $l)
    <div class="d-flex justify-content-between align-items-center py-2 city-row">
        <div class="co-md-6">
            <span>{{$l->name}}</span>
            <small class="text-muted d-block">Country: {{$l->country->name}}</small>
            @if($l->packages_location_count > 0)
             <small class="text-muted d-block">Packages:{{ $l->packages_location_count}}</small>
            @else
             <small class="text-muted d-block">Packages:{{ $l->packages_source_count}}</small>
            @endif
             <small class="text-muted d-block">Author: {{ $l->author_name ?: '---' }}</small>
        </div>
        <div class="co-md-6">
            <button class="btn btn-sm btn-outline-primary edit-city" data-id="{{$l->id}}" data-url="{{ route('admin.locations.show',$l->id) }}" data-upurl="{{ route('admin.locations.update',$l->id) }}">
                <i class="fas fa-edit icon"></i>
                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
            </button>
            <button class="btn btn-sm btn-outline-primary add-city-details" data-id="{{$l->id}}" data-url="{{ route('admin.locations.update',$l->id) }}" data-eurl="{{ route('admin.locations.show',$l->id) }}">
                <i class="fas fa-cog icon"></i>
                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
            </button>
            <button class="btn btn-sm btn-outline-primary location-faqs" data-id="{{$l->id}}" data-title="{{$l->title}}" data-url="{{ route('admin.locations.faqUpdate',$l->id) }}">
                <i class="fa fa-question-circle icon"></i>
                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
            </button>
            <button class="btn btn-sm btn-outline-primary location-meta" data-id="{{$l->id}}" data-upurl="{{ route('admin.locations.update',$l->id) }}" data-url="{{ route('admin.locations-meta.show.meta',$l->id) }}">
                <i class="fa fa-globe icon"></i>
                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
            </button>
            @if($l->details)
                <button class="btn btn-sm btn-outline-primary city-details" data-id="{{$l->id}}" data-url="{{ route('admin.locations.show',$l->id) }}">
                    <i class="fas fa-eye icon"></i>
                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                </button>
            @endIf
            @if($l->packages_location_count > 0 || $l->packages_source_count > 0)
            <a href="https://www.indianpanorama.in/india/{{$l->slug}}" title="preview" class="btn btn-sm btn-outline-primary" target="_blank">
                <i class="fa fa-tv" aria-hidden="true"></i>
            </a>
            @endif
            

        </div>
    </div>
@endforeach

@if($locations->lastPage() > 1)
    <hr>
    <div class="mt-2 custom-pagination">
        @include('admin.common.pagination', ['paginator' => $locations->appends(request()->only('city'))])
    </div>

@endif