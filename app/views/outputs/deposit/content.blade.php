<?php extract($params); ?>
@foreach ($deposit_items as $item)
    <?php extract ($item); ?>
<table border="0" width="990" cellpadding="0" style="font-family: Helvetica, msungstdlight;">
    <tr><td colspan="12" style="line-height: 5px;"></td></tr>
    <tr>
        <td width="640"><table>
            <tr>
                <td width="5"></td>
                <td width="280" colspan="2" style="font-family: msungstdlight;">{{ $product_name }}</td>
                <td width="100">---</td>
                <td width="40" style="text-align: right;">{{ $qty }}</td>
                <td width="70" style="text-align: right;">${{ $unit_price }}</td>
                <td width="60" style="text-align: right;">---</td>
                <td width="65" style="text-align: right;">${{ $total_price }}</td>
            </tr>
            <!-- For item details
            <tr>
                <td width="60" colspan="2"></td>
                <td colspan="2">aa<br/>cc</td>
                <td colspan="4"></td>
            </tr>
            -->
        </table></td>
        <td width="340"><table><tr>
            <td width="90" style="font-family: msungstdlight">{{ $product_name }}</td>
            <td width="10"></td>
            <td width="85">---</td>
            <td width="35" style="text-align: right;">{{ $qty }}</td>
            <td width="60" style="text-align: right;">---</td>
            <td width="65" style="text-align: right;">{{ $unit_price }}</td>
        </tr></table></td>
    </tr>
</table>
@endforeach