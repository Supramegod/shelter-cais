<div class="row mt-5">
    <div class="col-12 d-flex justify-content-between">
    @if($request->step>1)
        <a href="{{route('quotation.step',['id'=>$quotation->id,'step'=>$request->step-1])}}" class="btn btn-primary btn-back w-20">
            <span class="align-middle d-sm-inline-block d-none me-sm-1">back</span>
            <i class="mdi mdi-arrow-left"></i>
        </a>
    @else
    <div></div>
    @endif
    @if($isEdit)
        <a href="{{route('quotation.view',['id'=>$quotationKebutuhan[0]->id])}}" class="btn btn-success w-30">
            <i class="mdi mdi-home-outline"></i>
            <span class="align-middle d-sm-inline-block d-none me-sm-1">View Quotation</span>
        </a>
    @endif
        <button type="button" class="btn btn-primary btn-next w-20" id="btn-submit">
            <span class="align-middle d-sm-inline-block d-none me-sm-1">@if($request->step==13) Selesai @else Next @endif</span>
            <i class="mdi mdi-arrow-right"></i>
        </button>
    </div>
</div>