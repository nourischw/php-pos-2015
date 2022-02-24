<?php extract($params); ?>
@foreach ($invoice as $invoice)
    <?php extract ($invoice); ?>
<table border="0" width="990" cellpadding="0" style="font-family: Helvetica, msungstdlight;">
    <tr><td colspan="12" style="line-height: 5px;"></td></tr>
    <tr>
        <td width="630"><table>
            <tr>
                <td width="5"></td>
                <td width="280" colspan="2" style="font-family: msungstdlight;">
					{{ $product_name }} <br /><table><tr>
                        <td width="30"></td>
                        <td width="280" style="line-height: 20px;">{{ $product_spec }}
                        </td>
                        </tr></table></td>
                <td width="100">{{ $serial_number }}</td>
                <td width="40" style="text-align: right;">{{ $qty }}</td>
                <td width="70" style="text-align: right;">${{ $unit_price }}</td>
                <td width="60" style="text-align: right;">{{ $item_discount }}</td>
                <td width="65" style="text-align: right;">${{ $total_price }}</td>
            </tr>
        </table></td>
        <td width="340"><table><tr>
            <td width="42"></td>
            <td width="120" style="font-family: msungstdlight;">{{ $product_name }}</td>
            <td width="62">{{ $serial_number }}</td>
            <td width="37" style="text-align: right;">{{ $qty }}</td>
            <td width="35" style="text-align: right;">{{ $item_discount }}</td>
            <td width="65" style="text-align: right;">${{ $unit_price }}</td>
        </tr></table></td>
    </tr>
</table>
@endforeach