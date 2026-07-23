<div class="row g-3">
    <div class="col-12">
        <label class="form-label">Heading</label>
        <input type="text" class="form-control form-control-sm" data-key="heading"
               value="{{ $content['heading'] ?? '' }}" placeholder="Frequently Asked Questions">
    </div>
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <label class="form-label mb-0 fw-medium">FAQ Items</label>
            <button type="button" class="btn btn-sm btn-outline-success btn-add-faq">
                <i class="fas fa-plus me-1"></i> Add Question
            </button>
        </div>
        <div class="faq-tbody">
            @if(!empty($content['items']))
                @foreach($content['items'] as $i => $item)
                <div class="faq-row">
                    <div class="row g-2 align-items-start">
                        <div class="col-md-5">
                            <input type="text" class="form-control form-control-sm" data-key="question"
                                   placeholder="Question" value="{{ $item['question'] ?? '' }}">
                        </div>
                        <div class="col-md-6">
                            <textarea class="form-control form-control-sm" data-key="answer" rows="2"
                                      placeholder="Answer">{{ $item['answer'] ?? '' }}</textarea>
                        </div>
                        <div class="col-md-1 text-end">
                            <button type="button" class="btn btn-sm btn-outline-danger btn-rm-faq">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            @else
            <div class="faq-row">
                <div class="row g-2 align-items-start">
                    <div class="col-md-5">
                        <input type="text" class="form-control form-control-sm" data-key="question" placeholder="Question">
                    </div>
                    <div class="col-md-6">
                        <textarea class="form-control form-control-sm" data-key="answer" rows="2" placeholder="Answer"></textarea>
                    </div>
                    <div class="col-md-1 text-end">
                        <button type="button" class="btn btn-sm btn-outline-danger btn-rm-faq">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
