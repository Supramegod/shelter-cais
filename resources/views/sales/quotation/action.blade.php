<div class="row mt-5">
    <div class="col-12 d-flex justify-content-between">
    @php
    $stepSebelumnya = $request->step-1;
    if($quotation->kebutuhan_id==2){
        if($stepSebelumnya==10){
            $stepSebelumnya = 9;
        }
    }else if($quotation->kebutuhan_id==1){
        if($stepSebelumnya==10){
            $stepSebelumnya = 9;
        }
    }else if($quotation->kebutuhan_id==3){
        
    }else if($quotation->kebutuhan_id==4){
        if($stepSebelumnya==10){
            $stepSebelumnya = 9;
        }
    }
    @endphp
    @if(!$isEdit)
        <input type="hidden" name="edit" value="0">
        @if($request->step>1)
            <a href="{{route('quotation.step',['id'=>$quotation->id,'step'=>$stepSebelumnya])}}" class="btn btn-primary btn-back w-20">
                <span class="align-middle d-sm-inline-block d-none me-sm-1">back</span>
                <i class="mdi mdi-arrow-left"></i>
            </a>
        @else
        <div></div>
        @endif
        <button type="button" class="btn btn-primary btn-next w-20" id="btn-submit">
            <span class="align-middle d-sm-inline-block d-none me-sm-1">@if($request->step==12) Selesai @else Next @endif</span>
            <i class="mdi mdi-arrow-right"></i>
        </button>
    @else
        <input type="hidden" name="edit" value="1">
        <a href="{{route('quotation.view',['id'=>$quotation->id])}}" class="btn btn-secondary w-30">
            <i class="mdi mdi-arrow-left"></i>
            <span class="align-middle d-sm-inline-block d-none me-sm-1">&nbsp;Kembali Ke Quotation</span>
        </a>
        <button type="button" class="btn btn-primary btn-next w-20" id="btn-submit">
            <span class="align-middle d-sm-inline-block d-none me-sm-1">&nbsp;Simpan</span>
            <i class="mdi mdi-content-save"></i>
        </button>
    @endif
    </div>
</div>