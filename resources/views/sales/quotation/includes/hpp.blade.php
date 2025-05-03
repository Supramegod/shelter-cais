<div class="row">
    <div class="table-responsive text-nowrap">
    <table class="table" >
        <thead class="text-center">
        <tr class="table-success">
            <th colspan="{{3+count($quotation->quotation_detail)}}" style="vertical-align: middle;">HARGA POKOK BIAYA</th>
        </tr>
        <tr class="table-success">
            <th colspan="{{3+count($quotation->quotation_detail)}}" style="vertical-align: middle;">{{$leads->nama_perusahaan}} ( Provisi = {{$quotation->provisi}} )</th>
        </tr>
        <tr class="table-success">
            <th colspan="3">&nbsp;</th>
            @foreach($quotation->quotation_site as $site)
            <th colspan="{{$site->jumlah_detail}}" style="vertical-align: middle;">{{$site->nama_site}}</th>
            @endforeach
        </tr>
        <tr class="table-success">
            <th rowspan="2" style="vertical-align: middle;">No.</th>
            <th>Structure</th>
            <th rowspan="2" style="vertical-align: middle;">%</th>
            @foreach($quotation->quotation_detail as $detailJabatan)
            <th >{{$detailJabatan->jabatan_kebutuhan}}</th>
            @endforeach
        </tr>
        <tr class="table-success">
            <th>Jumlah Head Count ( Personil ) </th>
            @foreach($quotation->quotation_detail as $detailJabatan)
            <th >{{$detailJabatan->jumlah_hc}}</th>
            @endforeach
        </tr>
        </thead>
        <tbody>
        @php $nomorUrut = 1; @endphp
        <tr class="">
            <td style="text-align:center">{{$nomorUrut}}</td>
            <td style="text-align:left" class="">Gaji Pokok</td>
            <td style="text-align:center"></td>
            @foreach($quotation->quotation_detail as $detailJabatan)
            <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->nominal_upah,2,",",".")}}</td>
            @endforeach
        </tr>
        @php $nomorUrut++; @endphp
        @foreach($daftarTunjangan as $it => $tunjangan)
        <tr class="">
            <td style="text-align:center">{{$nomorUrut}}</td>
            <td style="text-align:left" class="">{{$tunjangan->nama}}</td>
            <td style="text-align:center"></td>
            @foreach($quotation->quotation_detail as $detailJabatan)
            <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->{$tunjangan->nama},2,",",".")}} </td>
            @endforeach
        </tr>
        @php $nomorUrut++; @endphp
        @endforeach
        @if($quotation->thr=="Ditagihkan" || $quotation->thr=="Diprovisikan" || $quotation->thr=="Diberikan Langsung")
        <tr class="">
            <td style="text-align:center">{{$nomorUrut}}</td>
            <td style="text-align:left" class="">Tunjangan Hari Raya <b>( {{$quotation->thr}} )</b></td>
            <td style="text-align:center"></td>
            @foreach($quotation->quotation_detail as $detailJabatan)
            <td style="text-align:right" class="">@if($quotation->thr=="Diprovisikan"){{"Rp. ".number_format($detailJabatan->tunjangan_hari_raya,2,",",".")}}  @elseif($quotation->thr=="Ditagihkan") Ditagihkan terpisah @elseif($quotation->thr=="Diberikan Langsung") Diberikan Langsung Oleh Client @endif</td>
            @endforeach
        </tr>
        @php $nomorUrut++; @endphp
        @endif
        @if($quotation->kompensasi=="Ditagihkan" || $quotation->kompensasi=="Diprovisikan")
        <tr class="">
            <td style="text-align:center">{{$nomorUrut}}</td>
            <td style="text-align:left" class="">Kompensasi <b>( {{$quotation->kompensasi}} )</b></td>
            <td style="text-align:center"></td>
            @foreach($quotation->quotation_detail as $detailJabatan)
            <td style="text-align:right" class="">@if($quotation->kompensasi=="Diprovisikan"){{"Rp. ".number_format($detailJabatan->kompensasi,2,",",".")}} @elseif($quotation->kompensasi=="Ditagihkan") Ditagihkan terpisah @endif</td>
            @endforeach
        </tr>
        @php $nomorUrut++; @endphp
        @endif
        @if($quotation->tunjangan_holiday=="Normatif" || $quotation->tunjangan_holiday=="Flat")
        <tr class="">
            <td style="text-align:center">{{$nomorUrut}}</td>
            <td style="text-align:left" class="">Tunjangan Hari Libur Nasional <b>( {{$quotation->tunjangan_holiday}} @if($quotation->tunjangan_holiday=="Normatif") : {{"Rp. ".number_format($quotation->tunjangan_holiday_display,2,",",".")}} @endif )</b></td>
            <td style="text-align:center"></td>
            @foreach($quotation->quotation_detail as $detailJabatan)
            <td style="text-align:right" class="">@if($quotation->tunjangan_holiday=="Normatif") Ditagihkan terpisah @elseif($quotation->tunjangan_holiday=="Flat") {{"Rp. ".number_format($detailJabatan->tunjangan_holiday,2,",",".")}} @endif </td>
            @endforeach
        </tr>
        @php $nomorUrut++; @endphp
        @endif
        @if($quotation->lembur=="Normatif" || $quotation->lembur=="Flat")
        <tr class="">
            <td style="text-align:center">{{$nomorUrut}}</td>
            <td style="text-align:left" class="">Lembur <b>( {{$quotation->lembur}} @if($quotation->lembur=="Normatif") : {{"Rp. ".number_format($quotation->lembur_per_jam,2,",",".")}} Per Jam @endif )</b></td>
            <td style="text-align:center"></td>
            @foreach($quotation->quotation_detail as $detailJabatan)
            <td style="text-align:right" class="">@if($quotation->lembur=="Normatif") Ditagihkan terpisah @elseif ($quotation->lembur=="Flat") {{"Rp. ".number_format($detailJabatan->lembur,2,",",".")}} @endif </td>
            @endforeach
        </tr>
        @php $nomorUrut++; @endphp
        @endif
        <tr class="">
            <td style="text-align:center">{{$nomorUrut}}</td>
            <td style="text-align:left">@if($quotation->penjamin =="BPJS") BPJS Kesehatan @else Asuransi Kesehatan Swasta @endif</td>
            <td style="text-align:center">{{number_format($quotation->persen_bpjs_kesehatan,2,",",".")}}%</td>
            @foreach($quotation->quotation_detail as $detailJabatan)
            <td style="text-align:right" class="">
            @if($detailJabatan->penjamin_kesehatan == "Takaful")
            {{"Rp. ".number_format($detailJabatan->nominal_takaful,2,",",".")}}</td>
            @else
            {{"Rp. ".number_format($detailJabatan->bpjs_kesehatan,2,",",".")}}</td>
            @endif
        </td>
            @endforeach
        </tr>
        <tr class="">
            <td style="text-align:center">{{$nomorUrut}}</td>
            <td style="text-align:left" class="">BPJS Ketenagakerjaan</td>
            <td style="text-align:center">{{number_format($quotation->persen_bpjs_ketenagakerjaan,2,",",".")}}%</td>
            @foreach($quotation->quotation_detail as $detailJabatan)
            <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->bpjs_ketenagakerjaan,2,",",".")}} <a href="javascript:void(0)"><i class="mdi mdi-magnify text-primary view-bpjs" data-persen-bpjs-jkk="{{$detailJabatan->persen_bpjs_jkk}}" data-persen-bpjs-jkm="{{$detailJabatan->persen_bpjs_jkm}}" data-persen-bpjs-jht="{{$detailJabatan->persen_bpjs_jht}}" data-persen-bpjs-jp="{{$detailJabatan->persen_bpjs_jp}}" data-bpjs-jkk="{{$detailJabatan->bpjs_jkk}}" data-bpjs-jkm="{{$detailJabatan->bpjs_jkm}}" data-bpjs-jht="{{$detailJabatan->bpjs_jht}}" data-bpjs-jp="{{$detailJabatan->bpjs_jp}}"></i></a></td>
            @endforeach
        </tr>
        @php $nomorUrut++; @endphp
        <tr class="">
            <td style="text-align:center">{{$nomorUrut}}</td>
            <td style="text-align:left" class="">Provisi Seragam </td>
            <td style="text-align:center"></td>
            @foreach($quotation->quotation_detail as $detailJabatan)
            <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->personil_kaporlap,2,",",".")}} </td>
            @endforeach
        </tr>
        @php $nomorUrut++; @endphp
        <tr class="">
            <td style="text-align:center">{{$nomorUrut}}</td>
            <td style="text-align:left" class="">Provisi Peralatan </td>
            <td style="text-align:center"></td>
            @foreach($quotation->quotation_detail as $detailJabatan)
            <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->personil_devices,2,",",".")}}</td>
            @endforeach
        </tr>
        @if($quotation->kebutuhan_id==3)
        @php $nomorUrut++; @endphp
        <tr class="">
            <td style="text-align:center">{{$nomorUrut}}</td>
            <td style="text-align:left" class="">Chemical </td>
            <td style="text-align:center"></td>
            @foreach($quotation->quotation_detail as $detailJabatan)
            <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->personil_chemical,2,",",".")}}</td>
            @endforeach
        </tr>
        @endif
        @php $nomorUrut++; @endphp
        <tr class="">
            <td style="text-align:center">{{$nomorUrut}}</td>
            <td style="text-align:left" class="">Overhead Cost </td>
            <td style="text-align:center"></td>
            @foreach($quotation->quotation_detail as $detailJabatan)
            <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->personil_ohc,2,",",".")}}</td>
            @endforeach
        </tr>
        @php $nomorUrut++; @endphp
        <tr class="">
            <td style="text-align:center">{{$nomorUrut}}</td>
            <td style="text-align:left" class="">Bunga Bank ( {{$quotation->top}} ) &nbsp;

            </td>
            <td style="text-align:center">{{$quotation->persen_bunga_bank}} %</td>
            @foreach($quotation->quotation_detail as $detailJabatan)
            <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->bunga_bank,2,",",".")}}</td>
            @endforeach
        </tr>
        @php $nomorUrut++; @endphp
        <tr class="">
            <td style="text-align:center">{{$nomorUrut}}</td>
            <td style="text-align:left" class="">Insentif
            </td>
            <td style="text-align:center">{{$quotation->persen_insentif}} % </td>
            @foreach($quotation->quotation_detail as $detailJabatan)
            <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->insentif,2,",",".")}}</td>
            @endforeach
        </tr>
        <tr class="table-success">
            <td colspan="2" style="text-align:right" class="fw-bold">Total Biaya per Personil</td>
            <td style="text-align:center"></td>
            @foreach($quotation->quotation_detail as $detailJabatan)
            <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->total_personil,2,",",".")}}</td>
            @endforeach
        </tr>
        <tr class="">
            <td colspan="2" style="text-align:right" class="fw-bold">Sub Total Biaya All Personil</td>
            <td style="text-align:center"></td>
            @foreach($quotation->quotation_detail as $detailJabatan)
            <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->sub_total_personil,2,",",".")}}</td>
            @endforeach
        </tr>
        <tr class="table-success">
            <td colspan="2" style="text-align:right" class="fw-bold">Total Sebelum Management Fee</td>
            <td style="text-align:center"></td>
            <td style="text-align:right" colspan="{{count($quotation->quotation_detail)}}">{{"Rp. ".number_format($quotation->total_sebelum_management_fee,2,",",".")}}</td>
        </tr>
        <tr class="">
            <td colspan="2" style="text-align:right" class="">Management Fee (MF) <span class="text-danger">*dari {{$quotation->management_fee}}</span></td>
            <td style="text-align:center">{{$quotation->persentase}} %</td>
            <td style="text-align:right" colspan="{{count($quotation->quotation_detail)}}">{{"Rp. ".number_format($quotation->nominal_management_fee,2,",",".")}}</td>
        </tr>
        @if($quotation->is_ppn == 1)
        <tr class="table-success">
            <td colspan="2" style="text-align:right" class="fw-bold">Grand Total Sebelum Pajak</td>
            <td style="text-align:center"></td>
            <td style="text-align:right" colspan="{{count($quotation->quotation_detail)}}">{{"Rp. ".number_format($quotation->grand_total_sebelum_pajak,2,",",".")}}</td>
        </tr>
        <tr class="table-success">
            <td colspan="2" style="text-align:right" class="fw-bold">Dasar Pengenaan Pajak</td>
            <td style="text-align:center"></td>
            <td style="text-align:right" colspan="{{count($quotation->quotation_detail)}}">{{"Rp. ".number_format($quotation->dpp,2,",",".")}}</td>
        </tr>
        <tr class="">
            <td colspan="2" style="text-align:right" class="fw-bold">PPN <span class='text-danger'>@if($quotation->ppn_pph_dipotong=="Management Fee")*dari management fee @else *dari Total Upah @endif</span></td>
            <td style="text-align:center">12 %</td>
            <td style="text-align:right" colspan="{{count($quotation->quotation_detail)}}">{{"Rp. ".number_format($quotation->ppn,2,",",".")}}</td>
        </tr>
        <tr class="">
            <td colspan="2" style="text-align:right" class="fw-bold">PPh <span class='text-danger'>@if($quotation->ppn_pph_dipotong=="Management Fee")*dari management fee @else *dari Total Upah @endif</span></td>
            <td style="text-align:center">-2 %</td>
            <td style="text-align:right" colspan="{{count($quotation->quotation_detail)}}">{{"Rp. ".number_format($quotation->pph,2,",",".")}}</td>
        </tr>
        @endif
        <tr class="table-success">
            <td colspan="2" style="text-align:right" class="fw-bold">TOTAL INVOICE</td>
            <td style="text-align:center"></td>
            <td style="text-align:right" colspan="{{count($quotation->quotation_detail)}}">{{"Rp. ".number_format($quotation->total_invoice,2,",",".")}}</td>
        </tr>
        <tr class="table-success">
            <td colspan="2" style="text-align:right" class="fw-bold">PEMBULATAN</td>
            <td style="text-align:center"></td>
            <td style="text-align:right" colspan="{{count($quotation->quotation_detail)}}">{{"Rp. ".number_format($quotation->pembulatan,2,",",".")}}</td>
        </tr>
        </tbody>
    </table>
    </div>
    <div class="mt-3" style="padding-left:40px">
    <p><b><i>Note :</i></b><br>
    {!! $quotation->note_harga_jual !!}</p>
    </div>
</div>
