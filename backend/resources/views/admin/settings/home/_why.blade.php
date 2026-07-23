{{-- ══ TAB: Why Indian Panorama ══ --}}
<div class="tab-pane fade" id="cms-pane-why">

    @php $whySec = $sections->get('why_choose'); @endphp

    <div class="sec-heading-panel">
        <div class="panel-label"><i class="fas fa-cog"></i>Section Settings</div>
        <form class="row g-3" id="why-heading-form">

            <div class="col-md-6">
                <label class="form-label fw-semibold small">Title</label>
                <input type="text" class="form-control form-control-sm" name="title"
                       value="{{ $whySec?->title }}" placeholder="e.g. Why Indian Panorama?">
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold small">Sub Title</label>
                <input type="text" class="form-control form-control-sm" name="subtitle"
                       value="{{ $whySec?->subtitle }}">
            </div>

            {{-- Image upload --}}
            <div class="col-md-5">
                <x-media-picker name="image" picker-id="why_image" label="Right Side Image"
                    folder="home-sections" :value="$whySec?->image" />
            </div>

            <div class="col-md-5">
                <label class="form-label fw-semibold small">Image Alt Tag</label>
                <input type="text" class="form-control form-control-sm" name="image_alt"
                       value="{{ $whySec?->image_alt }}" placeholder="Descriptive alt text">
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <button type="button" class="btn btn-sm btn-primary w-100" id="why-heading-save">
                    <i class="fas fa-save me-1"></i>Save
                </button>
            </div>

        </form>

    </div>

</div>
