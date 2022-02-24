<?php extract($params); ?>
<table border="0" cellpadding="0">
	<tr>
		<td style="font-size: xx-large">Goods In Report</td>
	</tr>
</table>

<table><tr><td style="line-height: 10px;"></td></tr></table>

<table border="0" cellpadding="0">
	<tr>
		<td width="90" height="15">Bill To</td>
		<td colspan="4" width="400"><strong>MOBILE CITY (HK) LIMITED</strong></td>
		<td rowspan="6" width="350">
			<table>
				<tr>
					<td width="100" height="15">Gl Sys No</td>
					<td width="200">{{ $sys_no }}</td>
				</tr>
				<tr>
					<td width="100" height="15">Date</td>
					<td width="200">{{ $update_time }}</td>
				</tr>		
				<tr>
					<td width="100" height="15">Create Date</td>
					<td width="200">{{ $create_time }}</td>
				</tr>	
				<tr>
					<td width="100" height="15">Goods-in at</td>
					<td width="200">{{ $update_time }}</td>
				</tr>	
				<tr>
					<td width="100" height="15">Approved By</td>
					<td width="200">{{ $update_by }}</td>
				</tr>	
				<tr>
					<td width="100" height="15">Refer No.</td>
					<td width="200">{{ $po_ref_no }}</td>
				</tr>		
			</table>
		</td>
	</tr>
	<tr>
		<td width="90" height="15">Supplier</td>
		<td colspan="4" width="350"><strong>{{ $supplier_name }}</strong></td>
	</tr>
	<tr>
		<td width="90" height="15">Invoice No.</td>
		<td colspan="4" width="350"><strong>{{ $invoice_no }}</strong></td>
	</tr>	
	<tr>
		<td width="90" height="15">Purchase Cost</td>
		<td width="75">HKD</td>
		<td width="80">{{ $total_cost }}</td>
		<td width="120">Return Cost</td>
		<td width="50">0.00</td>
	</tr>		
	<tr>
		<td width="90" height="15">Delivery Note</td>
		<td width="75"></td>
		<td width="80"></td>
		<td width="120">Deliver By</td>
		<td width="50"></td>
	</tr>
	<tr>
		<td width="90" height="15">Settlement</td>
		<td width="75"></td>
		<td width="80">{{ $total_cost }}</td>
		<td colspan="2"></td>
	</tr>	
</table>

<table><tr><td style="line-height: 15px;"></td></tr></table>

<table border="0" cellpadding="0">
	<tr>
		<td width="90" height="15">Remarks</td>
		<td width="350"><strong></strong></td>
	</tr>
</table>

<table border="0" cellpadding="0">
	<tr style="font-family: Helvetica; font-size: small; line-height: 10px; text-align: right">
		<td>Report-ID: GoodsIn-Summary.FRX</td>
	</tr>
</table>

<table><tr><td style="line-height: 10px;"></td></tr></table>

<table>
	<tr bgcolor="#CCC" style="line-height: 23px;">
        <td width="50" align="center" style="border: 1px solid black;">
			#
		</td>	
        <td width="381" style="border: 1px solid black;">
			&nbsp;&nbsp;&nbsp;PRODUCT DESCRIPTION
		</td>
        <td width="50" align="center" style="border: 1px solid black; font-family: Helvetica;">
			QTY
		</td>
        <td width="100" align="center" style="border: 1px solid black; font-family: Helvetica;">
			U.COST
		</td>
        <td width="90.2" align="center" style="border: 1px solid black; font-family: Helvetica;">
			Compensation
		</td>
	</tr>
    <tr>
        <td height="751" style="border: 1px solid black"></td>
        <td style="border-top: 1px solid black; border-right: 1px solid black; border-bottom: 1px solid black;"></td>
        <td style="border-top: 1px solid black; border-right: 1px solid black; border-bottom: 1px solid black;"></td>
        <td style="border-top: 1px solid black; border-right: 1px solid black; border-bottom: 1px solid black;"></td>
        <td style="border-top: 1px solid black; border-right: 1px solid black; border-bottom: 1px solid black;"></td> 
    </tr>	
</table>