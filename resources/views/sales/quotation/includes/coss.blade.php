<div class="row">
                  <div class="table-responsive text-nowrap">
                    <table class="table" >
                      <thead class="text-center">
                        <tr class="table-success">
                          <th colspan="{{2+count($quotation->quotation_detail)}}" style="vertical-align: middle;">Harga Jual {{$quotation->kebutuhan}}</th>
                        </tr>
                        <tr class="table-success">
                          <th colspan="{{2+count($quotation->quotation_detail)}}" style="vertical-align: middle;">{{$leads->nama_perusahaan}}  ( Provisi = {{$quotation->provisi}} )</th>
                        </tr>
                        <tr class="table-success">
                          <th colspan="2">&nbsp;</th>
                          @foreach($quotation->quotation_site as $site)
                          <th colspan="{{$site->jumlah_detail}}" style="vertical-align: middle;">{{$site->nama_site}}</th>
                          @endforeach
                        </tr>+
                      </thead>
                      <tbody>
                        <tr>
                          <td class="fw-bold">Structure</td>
                          <td class="text-center fw-bold">%</td>
                          @foreach($quotation->quotation_detail as $detailJabatan)
                          <th class="text-center">{{$detailJabatan->jabatan_kebutuhan}}</td>
                          @endforeach
                        </tr>
                        <tr>
                          <td class="fw-bold">Jumlah Personil</td>
                          <td class="text-center fw-bold"></td>
                          @foreach($quotation->quotation_detail as $detailJabatan)
                          <td class="text-center">{{$detailJabatan->jumlah_hc}}</td>
                          @endforeach
                        </tr>
                        <tr>
                          <td class="fw-bold">1. BASE MANPOWER COST</td>
                          <td class="text-center fw-bold"></td>
                          @foreach($quotation->quotation_detail as $detailJabatan)
                          <td class="text-center" class="text-center fw-bold">Unit/Month</td>
                          @endforeach
                        </tr>
                        <tr>
                          <td>Upah/Gaji</td>
                          <td class="text-center"></td>
                          @foreach($quotation->quotation_detail as $detailJabatan)
                          <td class="text-end">{{"Rp. ".number_format($detailJabatan->nominal_upah,2,",",".")}}</td>
                          @endforeach
                        </tr>
                        @foreach($daftarTunjangan as $it => $tunjangan)
                        <tr>
                          <td>{{$tunjangan->nama}}</td>
                          <td class="text-center"></td>
                          @foreach($quotation->quotation_detail as $detailJabatan)
                          <td class="text-end">{{"Rp. ".number_format($detailJabatan->{$tunjangan->nama},2,",",".")}}</td>
                          @endforeach
                        </tr>
                        @endforeach
                        <tr class="table-success">
                          <td class="fw-bold text-center">Total Base Manpower Cost per Month (THP)</td>
                          <td class="text-center fw-bold"></td>
                          @foreach($quotation->quotation_detail as $detailJabatan)
                          <td class="fw-bold" style="text-align:right">Rp {{number_format($detailJabatan->total_base_manpower,2,",",".")}}</td>
                          @endforeach
                        </tr>
                        <tr>
                          <td class="fw-bold">2. EXCLUDE BASE MANPOWER COST</td>
                          <td class="text-center fw-bold"></td>
                          @foreach($quotation->quotation_detail as $detailJabatan)
                          <td class="text-center fw-bold">Unit/Month</td>
                          @endforeach
                        </tr>
                        @if($quotation->thr=="Ditagihkan" || $quotation->thr=="Diprovisikan" || $quotation->thr=="Diberikan Langsung")
                        <tr>
                            <td>Provisi Tunjangan Hari Raya (THR)</td>
                            <td class="text-center"></td>
                            @foreach($quotation->quotation_detail as $detailJabatan)
                            <td class="text-end">@if($quotation->thr=="Ditagihkan")Ditagihkan Terpisah @elseif($quotation->thr=="Diberikan Langsung") Diberikan Langsung Oleh Client @else {{"Rp. ".number_format($detailJabatan->tunjangan_hari_raya,2,",",".")}} @endif</td>
                            @endforeach
                        </tr>
                        @endif
                        @if($quotation->kompensasi=="Ditagihkan" || $quotation->kompensasi=="Diprovisikan")
                        <tr>
                          <td>Provisi Kompensasi</td>
                          <td class="text-center"></td>
                          @foreach($quotation->quotation_detail as $detailJabatan)
                          <td class="text-end">@if($quotation->kompensasi=="Ditagihkan")Ditagihkan Terpisah @else {{"Rp. ".number_format($detailJabatan->kompensasi,2,",",".")}} @endif</td>
                          @endforeach
                        </tr>
                        @endif
                        @if($quotation->tunjangan_holiday=="Flat" || $quotation->tunjangan_holiday=="Normatif")
                        <tr>
                          <td>Tunjangan Hari Libur Nasional</td>
                          <td class="text-center"></td>
                          @foreach($quotation->quotation_detail as $detailJabatan)
                          <td class="text-end">@if($quotation->tunjangan_holiday=="Normatif") <b>Ditagihkan Terpisah</b> @else {{"Rp. ".number_format($detailJabatan->tunjangan_holiday,2,",",".")}} @endif</td>
                          @endforeach
                        </tr>
                        @endif
                        @if($quotation->lembur=="Flat" || $quotation->lembur=="Normatif")
                        <tr>
                          <td>Lembur</td>
                          <td class="text-center"></td>
                          @foreach($quotation->quotation_detail as $detailJabatan)
                          <td class="text-end">@if($quotation->lembur=="Normatif") <b>Ditagihkan Terpisah</b> @else {{"Rp. ".number_format($detailJabatan->lembur,2,",",".")}} @endif</td>
                          @endforeach
                        </tr>
                        @endif
                        <tr>
                        <td style="text-align:left">@if($quotation->penjamin =="BPJS") BPJS Kesehatan @else Asuransi Kesehatan Swasta @endif</td>
                        <td class="text-center">{{number_format($quotation->persen_bpjs_kesehatan,2,",",".")}}%</td>
                          @foreach($quotation->quotation_detail as $detailJabatan)
                          <td class="text-end">
                          @if($detailJabatan->penjamin_kesehatan == "Takaful")
                          {{"Rp. ".number_format($detailJabatan->nominal_takaful,2,",",".")}}</td>
                          @else
                          {{"Rp. ".number_format($detailJabatan->bpjs_kesehatan,2,",",".")}}</td>
                          @endif
                          @endforeach
                        </tr>
                        <tr>
                          <td>BPJS Ketenagakerjaan</td>
                          <td class="text-center">{{number_format($quotation->persen_bpjs_ketenagakerjaan,2,",",".")}}%</td>
                          @foreach($quotation->quotation_detail as $detailJabatan)
                          <td class="text-end">{{"Rp. ".number_format($detailJabatan->bpjs_ketenagakerjaan,2,",",".")}}</td>
                          @endforeach
                        </tr>
                        <tr>
                          <td>Provisi Seragam</td>
                          <td class="text-center"></td>
                          @foreach($quotation->quotation_detail as $detailJabatan)
                          <td class="text-end">{{"Rp. ".number_format($detailJabatan->personil_kaporlap_coss,2,",",".")}} </td>
                          @endforeach
                        </tr>
                        <tr>
                          <td>Provisi Peralatan </td>
                          <td class="text-center"></td>
                          @foreach($quotation->quotation_detail as $detailJabatan)
                          <td class="text-end">{{"Rp. ".number_format($detailJabatan->personil_devices_coss,2,",",".")}}</td>
                          @endforeach
                        </tr>
                        @if($quotation->kebutuhan_id==3)
                        <tr>
                          <td>Provisi Chemical </td>
                          <td class="text-center"></td>
                          @foreach($quotation->quotation_detail as $detailJabatan)
                          <td class="text-end">{{"Rp. ".number_format($detailJabatan->personil_chemical_coss,2,",",".")}}</td>
                          @endforeach
                        </tr>
                        @endif
                        <tr class="table-success">
                          <td class="fw-bold text-center">Total Exclude Base Manpower Cost</td>
                          <td class="text-center fw-bold"></td>
                          @foreach($quotation->quotation_detail as $detailJabatan)
                          <td class="fw-bold" style="text-align:right">Rp {{number_format($detailJabatan->total_exclude_base_manpower,2,",",".")}}</td>
                          @endforeach
                        </tr>
                        <tr>
                          <td class="fw-bold">3. BIAYA PENGAWASAN DAN PELAKSANAAN LAPANGAN ( UNIT / MONTH ) </th>
                          <td class="text-center fw-bold"></th>
                          @foreach($quotation->quotation_detail as $detailJabatan)
                          <td class="text-end">{{"Rp. ".number_format($detailJabatan->personil_ohc_coss,2,",",".")}}</td>
                          @endforeach
                        </tr>
                        <tr class="table-success">
                          <td class="fw-bold text-center">Total Biaya per Personil (1+2+3)</td>
                          <td class="text-center fw-bold"></td>
                          @foreach($quotation->quotation_detail as $detailJabatan)
                          <td class="fw-bold" style="text-align:right">Rp {{number_format($detailJabatan->total_personil_coss,2,",",".")}}</td>
                          @endforeach
                        </tr>
                        <tr class="table-success">
                          <td class="fw-bold text-center">Harga Per Personel x Jumlah Personel</td>
                          <td class="text-center fw-bold"></td>
                          @foreach($quotation->quotation_detail as $detailJabatan)
                          <td class="fw-bold" style="text-align:right">Rp {{number_format($detailJabatan->sub_total_personil_coss,2,",",".")}}</td>
                          @endforeach
                        </tr>
                        <tr class="table-success">
                          <td colspan="1" style="text-align:right" class="fw-bold">Total Sebelum Management Fee</td>
                          <td style="text-align:center"></td>
                          <td style="text-align:right" colspan="{{count($quotation->quotation_detail)}}">{{"Rp. ".number_format($quotation->total_sebelum_management_fee_coss,2,",",".")}}</td>
                        </tr>
                        <tr class="">
                          <td colspan="1" style="text-align:right" class="">Management Fee (MF) <span class="text-danger">*dari {{$quotation->management_fee}}</span></td>
                          <td style="text-align:center">{{$quotation->persentase}} %</td>
                          <td style="text-align:right" colspan="{{count($quotation->quotation_detail)}}">{{"Rp. ".number_format($quotation->nominal_management_fee_coss,2,",",".")}}</td>
                        </tr>
                        @if($quotation->is_ppn == 1)
                        <tr class="table-success">
                          <td colspan="1" style="text-align:right" class="fw-bold">Grand Total Sebelum Pajak</td>
                          <td style="text-align:center"></td>
                          <td style="text-align:right" colspan="{{count($quotation->quotation_detail)}}">{{"Rp. ".number_format($quotation->grand_total_sebelum_pajak_coss,2,",",".")}}</td>
                        </tr>
                        <tr class="table-success">
                          <td colspan="1" style="text-align:right" class="fw-bold">Dasar Pengenaan Pajak</td>
                          <td style="text-align:center"></td>
                          <td style="text-align:right" colspan="{{count($quotation->quotation_detail)}}">{{"Rp. ".number_format($quotation->dpp_coss,2,",",".")}}</td>
                        </tr>
                        <tr class="">
                          <td colspan="1" style="text-align:right" class="fw-bold">PPN <span class='text-danger'>@if($quotation->ppn_pph_dipotong=="Management Fee")*dari management fee @else *dari Total Upah @endif</span></td>
                          <td style="text-align:center">12 %</td>
                          <td style="text-align:right" colspan="{{count($quotation->quotation_detail)}}">{{"Rp. ".number_format($quotation->ppn_coss,2,",",".")}}</td>
                        </tr>
                        <tr class="">
                          <td colspan="1" style="text-align:right" class="fw-bold">PPh <span class='text-danger'>@if($quotation->ppn_pph_dipotong=="Management Fee")*dari management fee @else *dari Total Upah @endif</span></td>
                          <td style="text-align:center">-2 %</td>
                          <td style="text-align:right" colspan="{{count($quotation->quotation_detail)}}">{{"Rp. ".number_format($quotation->pph_coss,2,",",".")}}</td>
                        </tr>
                        @endif
                        <tr class="table-success">
                          <td colspan="1" style="text-align:right" class="fw-bold">TOTAL INVOICE</td>
                          <td style="text-align:center"></td>
                          <td style="text-align:right" colspan="{{count($quotation->quotation_detail)}}">{{"Rp. ".number_format($quotation->total_invoice_coss,2,",",".")}}</td>
                        </tr>
                        <tr class="table-success">
                          <td colspan="1" style="text-align:right" class="fw-bold">PEMBULATAN</td>
                          <td style="text-align:center"></td>
                          <td style="text-align:right" colspan="{{count($quotation->quotation_detail)}}">{{"Rp. ".number_format($quotation->pembulatan_coss,2,",",".")}}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                  <div class="mt-3" style="padding-left:40px">
                    <p><b><i>Note :</i></b>	<br>
                    <b>Upah pokok base on Umk 2024 </b> <br>
  Tunjangan overtime flat total 75 jam. <span class="text-danger">*jika system jam kerja 12 jam </span> <br>
  Tunjangan hari raya ditagihkan provisi setiap bulan. (upah/12) <br>
  @if($quotation->program_bpjs=="2 BPJS")
  BPJS Ketenagakerjaan 2 Program (JKK, JKM).
  @elseif($quotation->program_bpjs=="3 BPJS")
  BPJS Ketenagakerjaan 3 Program (JKK, JKM, JHT).
  @elseif($quotation->program_bpjs=="4 BPJS")
  BPJS Ketenagakerjaan 4 Program (JKK, JKM, JHT, JP).
  @endif
  <span class="text-danger">Pengalian base on upah</span>		<br>
  BPJS Kesehatan. <span class="text-danger">*base on Umk 2024</span> <br>
  <br>
  <span class="text-danger">*prosentase Bpjs Tk J. Kecelakaan Kerja disesuaikan dengan tingkat resiko sesuai ketentuan.</span>
  </p>
                  </div>
                </div>
