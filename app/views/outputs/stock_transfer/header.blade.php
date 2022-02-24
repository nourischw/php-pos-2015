<?php 
	extract($params);
?>
<h3 style="font-size: 24px; font-family: Helvetica;"><u>MOBILE CITY (HK) LIMITED</u></h3>

<table border="0" cellpadding="0" style="font-family: Helvetica;">
	<tr><td width="440" style="font-size: 24px;"><strong>Transfer Note</strong></td>
		<td width="200"><tcpdf method="write1DBarcode" params="{{ $barcode }}" /></td>
	</tr>
</table>

<table border="0" cellpadding="0" style="font-family: Helvetica;">
	<tr><td width="90">Transfer No.:</td>
		<td width="150">{{ $stock_transfer_number }}</td>
		<td width="80">Total Qty.:</td>
		<td width="160">{{ $total_qty }}</td>
		<td width="100">Issue Staff:</td>
		<td width="100">{{ $issue_staff }}</td>
	</tr>
	
	<tr><td>From:</td>
		<td>{{ $from_shop_code }}</td>
		<td>Destination:</td>
		<td>{{ $to_shop_code }}</td>
		<td>Request Staff:</td>
		<td>{{ $request_by }}</td>
	</tr>
	
	<tr><td>Date Out:</td>
		<td>{{ $date_out }}</td>
		<td>Date In:</td>
		<td>{{ ($date_in !== '0000-00-00') ? $date_in : '---'; }}</td>
		<td>Deliver Staff:</td>
		<td>{{ $deliver_by }}</td>
	</tr>
	
	<tr><td>Remark:</td>
		<td colspan="3">{{ $remarks }}</td>
		<td>Received By:</td>
		<td>{{ $receive_by }}</td>
	</tr>
</table>

<table><tr><td style="line-height: 10px;"></td></tr></table>

<table border="0" cellpadding="0" cellspacing="0" style="font-family: Helvetica;">
    <tr style="line-height: 22px;">
        <td width="5" style="border-left: 1px solid black; border-top: 1px solid black; border-bottom: 1px solid black;">&nbsp;</td>
        <td width="590" style="border-top: 1px solid black; border-bottom: 1px solid black;">Product UPC Code & Description</td>
		<td width="10" style="border-top: 1px solid black; border-bottom: 1px solid black;">&nbsp;</td>
	    <td width="40" style="border-top: 1px solid black; border-right: 1px solid black; border-bottom: 1px solid black;">Qty.</td>
    </tr>
	
	<tr><td height="770" colspan="6" style="border-left: 1px solid black; border-right: 1px solid black; border-bottom: 1px solid black;"></td></tr>
</table>