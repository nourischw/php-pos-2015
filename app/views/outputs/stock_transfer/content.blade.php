<?php extract($params); ?>
<table border="0" cellpadding="0" cellspacing="0" style="line-height: 26px;">
	@foreach ($transfer_items as $value)
		<?php extract($value); ?>
	<tr style="line-height: 16%;"><td>&nbsp;</td></tr>
    <tr style="line-height: 20%;">
        <td width="5" style="border-bottom: 1px solid black;">&nbsp;</td>
        <td width="590" style="border-bottom: 1px solid black; font-size: smaller">
			UPC: {{ $barcode }}, {{ $product_name }} {{ ($serial_number != "") ? "(S/N: $serial_number)" : null; }}
		</td>
		<td width="10" style="border-bottom: 1px solid black;">&nbsp;</td>
	    <td width="40" style="border-bottom: 1px solid black;">{{ $qty }}</td>
    </tr>
	@endforeach
</table>

<table border="0" cellpadding="0" cellspacing="0" style="font-family: Helvetica;">
	<tr><td width="520">
			<table>
				<tr><td width="20">&nbsp;</td>
					<td width="150" height="40" style="border-bottom: 1px solid black;">&nbsp;</td>
					<td width="20">&nbsp;</td>
					<td width="150" style="border-bottom: 1px solid black;">&nbsp;</td>
					<td width="200"></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td style="text-align: center">Prepared By</td>
					<td>&nbsp;</td>
					<td style="text-align: center">Received By</td>
				</tr>
			</table>
		</td>
		<td width="170">
			<table border="0" cellpadding="0" cellspacing="0">
				<tr><td colspan="2" style="line-height: 20px;">&nbsp;</td></tr>
				<tr><td width="80"><strong>Total Qty:</strong></td>
					<td width="40" style="float:left; text-align: right;"><u>{{ $total_qty }}</u></td>
				</tr>
			</table>
		</td>
	</tr>
</table>