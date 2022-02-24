<?php extract($params); ?>
<table style="border: 1px solid black; line-height: 23px; font-size: small;">
	@foreach ($goods_in_items as $key => $value)
		<?php extract($value); ?>
    <tr>
        <td width="50" height="50" align="right" style="border-bottom: 1px solid black; border-right: 1px solid black;">
			{{ $key + 1 }}
		</td>	
        <td width="381" height="50" style="border-bottom: 1px solid black; border-right: 1px solid black;">
			<table>
				<tr>
					<td width="5"></td>
					<td width="200">{{ $barcode }}</td>
					<td width="200">{{ $serial_number }}</td>
				</tr>
				<tr>
					<td width="50"></td>
					<td width="350" style="font-size: small;">{{ $barcode }} {{ $product_name }}</td>
				</tr>
			</table>
		</td>
        <td width="50" height="50" align="right" style="border-bottom: 1px solid black; border-right: 1px solid black; font-family: Helvetica;">
			{{ $qty}}
		</td>
        <td width="100" height="50" align="right" style="border-bottom: 1px solid black; border-right: 1px solid black; font-family: Helvetica;">
			{{ $unit_price}}
		</td>
        <td width="90" height="50" align="right" style="border-bottom: 1px solid black; border-right: 1px solid black; font-family: Helvetica;">
			0.00
		</td>
    </tr>
    @endforeach
</table>

<table style="border-top: 1px solid black; border-left: 1px solid black; border-right: 1px solid black; line-height: 23px; font-size: small;">
    <tr>
        <td width="50" align="right" style="border-right: 1px solid black;"></td>	
        <td width="381" align="right" style="border-right: 1px solid black;">
			<strong>Total Qty.</strong>
		</td>
        <td width="50" align="right" style="border-right: 1px solid black; font-family: Helvetica;">
		{{ $total_qty }}
		</td>
        <td width="100" style="border-right: 1px solid black; font-family: Helvetica;">
			&nbsp;&nbsp;&nbsp;Total Cost
		</td>
        <td width="90" align="right" style="border-right: 1px solid black; font-family: Helvetica;">
			{{ $total_cost }}
		</td>
    </tr>
    <tr>
        <td width="50" align="right" style="border-right: 1px solid black;"></td>	
        <td width="381" align="right" style="border-right: 1px solid black;"></td>
        <td width="50" align="right" style="border-right: 1px solid black; font-family: Helvetica;"></td>
        <td width="100" style="border-right: 1px solid black; font-family: Helvetica;">
			&nbsp;&nbsp;&nbsp;NET TOTAL
		</td>
        <td width="90.5" align="right" style="border-right: 1px solid black; font-family: Helvetica;">
			{{ $total_cost }}
		</td>
    </tr>	
</table>