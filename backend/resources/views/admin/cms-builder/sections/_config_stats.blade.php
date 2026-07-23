<div class="row g-3">
    <div class="col-12">
        <label class="form-label">Heading</label>
        <input type="text" class="form-control form-control-sm" data-key="heading"
               value="{{ $content['heading'] ?? '' }}" placeholder="Indian Panorama in Numbers">
    </div>
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <label class="form-label mb-0 fw-medium">Stats <small class="text-muted">— Value, Label, Icon (FontAwesome class)</small></label>
            <button type="button" class="btn btn-sm btn-outline-success btn-add-stat">
                <i class="fas fa-plus me-1"></i> Add Stat
            </button>
        </div>
        <div class="stats-tbody">
            @if(!empty($content['items']))
                @foreach($content['items'] as $item)
                <div class="stat-row">
                    <div class="row g-2">
                        <div class="col-4">
                            <input type="text" class="form-control form-control-sm" data-key="value"
                                   placeholder="e.g. 25000+" value="{{ $item['value'] ?? '' }}">
                        </div>
                        <div class="col-4">
                            <input type="text" class="form-control form-control-sm" data-key="label"
                                   placeholder="Label" value="{{ $item['label'] ?? '' }}">
                        </div>
                        <div class="col-3">
                            <input type="text" class="form-control form-control-sm" data-key="icon"
                                   placeholder="fas fa-users" value="{{ $item['icon'] ?? '' }}">
                        </div>
                        <div class="col-1 text-end">
                            <button type="button" class="btn btn-sm btn-outline-danger btn-rm-stat">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            @else
            <div class="stat-row">
                <div class="row g-2">
                    <div class="col-4"><input type="text" class="form-control form-control-sm" data-key="value" placeholder="e.g. 25000+"></div>
                    <div class="col-4"><input type="text" class="form-control form-control-sm" data-key="label" placeholder="Label"></div>
                    <div class="col-3"><input type="text" class="form-control form-control-sm" data-key="icon" placeholder="fas fa-users"></div>
                    <div class="col-1 text-end"><button type="button" class="btn btn-sm btn-outline-danger btn-rm-stat"><i class="fas fa-times"></i></button></div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
