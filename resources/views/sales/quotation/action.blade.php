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
        <button type="submit" class="btn btn-primary btn-next w-20">
            <span class="align-middle d-sm-inline-block d-none me-sm-1">Next</span>
            <i class="mdi mdi-arrow-right"></i>
        </button>
    </div>
</div>