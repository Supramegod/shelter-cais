@php
    $arrStep = [
        [ 'stepno' => 1 ,'step' => 'Site & Jenis Kontrak', 'info' => 'Informasi Site & Kontrak' ],
        [ 'stepno' => 2 ,'step' => 'Detail Kontrak', 'info' => 'Informasi Detail Kontrak' ],
        [ 'stepno' => 3 ,'step' => 'Headcount', 'info' => 'Informasi Headcount' ],
        [ 'stepno' => 4 ,'step' => 'Upah dan MF', 'info' => 'Informasi Upah dan MF' ],
        [ 'stepno' => 5 ,'step' => 'BPJS', 'info' => 'Informasi Program BPJS' ],
        [ 'stepno' => 6 ,'step' => 'Aplikasi Pendukung', 'info' => 'Informasi Aplikasi Pendukung' ],
        [ 'stepno' => 7 ,'step' => 'Kaporlap / Seragam', 'info' => 'Informasi Kaporlap / Seragam' ],
        [ 'stepno' => 8 ,'step' => 'Devices', 'info' => 'Informasi Devices' ],
        [ 'stepno' => 9 ,'step' => 'Chemical', 'info' => 'Informasi Chemical' ],
        [ 'stepno' => 10 ,'step' => 'OHC', 'info' => 'Informasi OHC' ],
        [ 'stepno' => 11 ,'step' => 'Cost Structure', 'info' => 'Informasi Cost Structure' ],
        [ 'stepno' => 12 ,'step' => 'Perjanjian', 'info' => 'Informasi Perjanjian' ]
    ];
@endphp
<div class="bs-stepper-header gap-lg-3 pt-5"  style="border-right:1px solid rgba(0, 0, 0, 0.1);">
    @php
    $no = 1;
    @endphp
    @foreach($arrStep as $key => $data)
        <!-- kalo belum ada kebutuhan maka skip kaporlap dll` -->
        @php
            if($quotation->kebutuhan_id==2){
                    if(in_array($data['stepno'],[9])){
                        continue;
                    }
                }else if($quotation->kebutuhan_id==1){
                    if(in_array($data['stepno'],[9])){
                        continue;
                    }
                }else if($quotation->kebutuhan_id==3){
                    if(in_array($data['stepno'],[])){
                        continue;
                    }
                }else if($quotation->kebutuhan_id==4){
                    if(in_array($data['stepno'],[9])){
                        continue;
                    }
                }
        @endphp
        @if(!$loop->first)
            <div class="line"></div>
        @endif
        <a href="@if($data['stepno']<$quotation->step) {{route('quotation.step',['id'=>$request->id,'step'=>$data['stepno']])}} @else javascript:void(0) @endif">
            <div class="step @if($request->step>$data['stepno']) crossed @elseif($request->step==$data['stepno']) active @endif)">
                <button type="button" class="step-trigger">
                    <span class="bs-stepper-circle"><i class="mdi mdi-check"></i></span>
                    <span class="bs-stepper-label">
                    <span class="bs-stepper-number">{{sprintf('%02d',$no)}}</span>
                    <span class="d-flex flex-column gap-1 ms-2">
                        <span class="bs-stepper-title">{{$data["step"]}}</span>
                        <span class="bs-stepper-subtitle">{{$data["info"]}}</span>
                    </span>
                    </span>
                </button>
            </div>
        </a>
        @php
        $no++;
        @endphp
    @endforeach
</div>