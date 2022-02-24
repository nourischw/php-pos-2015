<table style="line-height: 23px; font-size: small;">
    @foreach ($po_items as $item)
        <?php extract($item); ?>
    <tr style="line-height: 20px;">
        <td width="5"></td>
        <td width="22"><img src="img/square.jpg" /></td>
        <td width="379">{{ $name }}</td>
        <td width="5"></td>
        <td width="50" align="right" style="font-family: Helvetica;">{{ $qty }}</td>
        <td width="3"></td>
        <td width="5"></td>
        <td width="70" align="right" style="font-family: Helvetica;">${{ $unit_price }}</td>
        <td width="3"></td>
        <td width="5"></td>
        <td width="90" align="right" style="font-family: Helvetica;">${{ $total_price }}</td>
        <td width="3"></td>
    </tr>
    @endforeach
</table>