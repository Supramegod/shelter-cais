<div class="row">
    <div class="table-responsive text-nowrap">
    <table class="table" >
        <thead class="text-center">
        <tr class="table-success">
            <th>Keterangan</th>
            <th>HPP</th>
            <th>Harga Jual</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td style="text-align:left">Nominal</td>
            <td style="text-align:right">
            {{"Rp. ".number_format($quotation->grand_total_sebelum_pajak,2,",",".")}}
            </td>
            <td style="text-align:right">
            {{"Rp. ".number_format($quotation->grand_total_sebelum_pajak_coss,2,",",".")}}
            </td>
        </tr>
        <tr>
            <td style="text-align:left">Total Biaya</td>
            <td style="text-align:right">
            {{"Rp. ".number_format($quotation->total_sebelum_management_fee,2,",",".")}}
            </td>
            <td style="text-align:right">
            {{"Rp. ".number_format($quotation->total_sebelum_management_fee,2,",",".")}}
            </td>
        </tr>
        <tr>
            <td style="text-align:left">Margin</td>
            <td style="text-align:right">
            {{"Rp. ".number_format($quotation->margin,2,",",".")}}
            </td>
            <td style="text-align:right">
            {{"Rp. ".number_format($quotation->margin_coss,2,",",".")}}
            </td>
        </tr>
        <tr>
            <td class="fw-bold" style="text-align:left">GPM</td>
            <td class="fw-bold" style="text-align:right">
            {{number_format($quotation->gpm,2,",",".")}} %
            </td>
            <td class="fw-bold" style="text-align:right">
            {{number_format($quotation->gpm_coss,2,",",".")}} %
            </td>
        </tr>
        </tbody>
    </table>
    </div>
</div>
