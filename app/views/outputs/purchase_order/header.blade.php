<?php extract($params); ?>
<table border="0" cellpadding="0">
    <tr>
        <td rowspan="3" width="150" style="font-size: xx-large">PO</td>
        <td colspan="2" width="240" align="center"><strong>MOBILE CITY (HK) LIMITED</strong></td>
        <td rowspan="3" width="50">&nbsp;</td>
        <td rowspan="3" width="200">
            <table style="border: 1px solid black">
                <tr>
                    <td colspan="3" bgcolor="#CCC" align="center" style="border-left: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; font-family: Helvetica; font-size: small; line-height: 23px;">
                        <strong>Purchase Order</strong>
                    </td>
                </tr>
                <tr><td colspan="3" style="line-height: 7px;"></td></tr>
                <tr>
                    <td width="5"></td>
                    <td width="35" height="22">No.:</td>
                    <td width="120" style="font-family: Helvetica;"><strong>{{ $purchase_order_number }}</strong></td>
                </tr>
                <tr>
                    <td width="5"></td>
                    <td height="22">Date:</td>
                    <td>{{ $po_date }}</td>
                </tr>
                <tr><td colspan="3" style="line-height: 5px;"></td></tr>
            </table>
        </td>
    </tr>

    <tr>
        <td colspan="2" style="font-family: Helvetica; font-size: small; height: 42px;">
            <div style="text-align: center;">{{ $shop_address }}</div>
        </td>
    </tr>

    <tr style="font-family: Helvetica; font-size: small; line-height: 30px; text-align: center">
        <td width="120">Tel: {{ $shop_telephone }}</td>
        <td width="120">Fax: {{ $shop_fax }}</td>
    </tr>
</table>

<table><tr><td style="line-height: 10px;"></td></tr></table>

<table border="0" cellpadding="0" cellspacing="0">
    <tr style="line-height: 16px;">
        <td width="5" bgcolor="#CCC" style="border-left: 1px solid black; border-top: 1px solid black;"></td>
        <td width="350" colspan="4" bgcolor="#CCC" style="border-top: 1px solid black; border-right: 1px solid black; font-family: Helvetica; line-height: 23px;"><strong>Supplier:</strong> {{ $supplier_code }}</td>
        <td width="10" rowspan="5" style="border-left: 1px solid black; border-right: 1px solid black;"></td>
        <td width="5" bgcolor="#CCC" style="border-left: 1px solid black; border-top: 1px solid black;"></td>
        <td width="270" colspan="2" bgcolor="#CCC" style="font-family: Helvetica; border-top: 1px solid black; border-right: 1px solid black; line-height: 23px;">Remark:</td>
    </tr>

    <tr style="line-height: 16px;">
        <td rowspan="4" style="border-left: 1px solid black; border-bottom: 1px solid black;"></td>
        <td colspan="4"><strong>{{ $supplier_name }}</strong></td>
        <td rowspan="4" style="border-bottom: 1px solid black"></td>
        <td colspan="2" style="font-size: small; line-height: 20px; border-right: 1px solid black;"></td>
    </tr>

    <tr>
        <td colspan="4"></td>
        <td colspan="2" rowspan="2" style="border-right: 1px solid black;">{{ $remarks }}</td>
    </tr>

    <tr style="line-height: 20px; font-family: Helvetica; font-size: x-small;">
        <td width="40">Mobile:</td>
        <td width="120">{{ $supplier_mobile }}</td>
        <td width="40">Fax:</td>
        <td width="150">{{ $supplier_fax }}</td>
    </tr>

    <tr style="line-height: 20px; font-family: Helvetica; font-size: x-small;">
        <td style="border-bottom: 1px solid black">Email:</td>
        <td colspan="3" style="border-bottom: 1px solid black;">{{ $supplier_email }}</td>
        <td width="165" style="border-bottom: 1px solid black;">Request By: {{ $request_by }}</td>
        <td width="105" style="border-bottom: 1px solid black; border-right: 1px solid black;">Ship To: {{ $ship_to_shop }}</td>
    </tr>
</table>

<table><tr><td style="line-height: 10px;"></td></tr></table>

<table style="border: 1px solid black;" cellpadding="0">
    <tr bgcolor="#CCC" style="line-height: 20px; font-family: Helvetica; font-size: small; font-weight: bold;">
        <td width="5" style="border-left: 1px solid black; border-top: 1px solid black; border-bottom: 1px solid black;"></td>
        <td width="150" style="border-top: 1px solid black; border-right: 1px solid black; border-bottom: 1px solid black;">PURCHASER</td>
        <td width="5" style="border-top: 1px solid black; border-bottom: 1px solid black;"></td>
        <td width="100" style="border-top: 1px solid black; border-right: 1px solid black; border-bottom: 1px solid black;">CONTACT NO.</td>
        <td width="5" style="border-top: 1px solid black; border-bottom: 1px solid black;"></td>
        <td width="100" style="border-top: 1px solid black; border-right: 1px solid black; border-bottom: 1px solid black;">FAX NO.</td>
        <td width="5" style="border-top: 1px solid black; border-bottom: 1px solid black;"></td>
        <td width="270" style="border-right: 1px solid black; border-top: 1px solid black; border-bottom: 1px solid black;">EMAIL ADDRESS</td>
    </tr>

    <tr style="line-height: 23px;">
        <td></td>
        <td style="border-right: 1px solid black;"></td>
        <td></td>
        <td style="border-right: 1px solid black;"></td>
        <td></td>
        <td style="border-right: 1px solid black;"></td>
        <td></td>
        <td></td>
    </tr>
</table>

<table><tr><td style="line-height: 10px;"></td></tr></table>

<table style="border: 1px solid black; font-size: small" height="400" cellpadding="0">
    <tr bgcolor="#CCC" style="line-height: 20px; font-family: Helvetica; font-size: small; font-weight: bold;">
        <td width="5" style="border-left: 1px solid black; border-top: 1px solid black;"></td>
        <td width="401" style="border-top: 1px solid black; border-right: 1px solid black;">DESCRIPTION</td>
        <td width="5" style="border-top: 1px solid black;"></td>
        <td width="50" style="border-top: 1px solid black;">QTY.</td>
        <td width="3" style="border-top: 1px solid black; border-right: 1px solid black;"></td>
        <td width="5" style="border-top: 1px solid black;"></td>
        <td width="70" align="center" style="border-top: 1px solid black;">U. PRICE.</td>
        <td width="3" style="border-top: 1px solid black; border-right: 1px solid black;"></td>
        <td width="5" style="border-top: 1px solid black;"></td>
        <td width="90" align="center" style="border-top: 1px solid black;">AMOUNT</td>
        <td width="3" style="border-top: 1px solid black; border-right: 1px solid black;"></td>
    </tr>
    
    <tr>
        <td height="532" style="border-left: 1px solid black; border-top: 1px solid black;"></td>
        <td style="border-top: 1px solid black; border-right: 1px solid black;"></td>
        <td style="border-top: 1px solid black;"></td>
        <td style="border-top: 1px solid black;"></td>
        <td style="border-top: 1px solid black; border-right: 1px solid black;"></td>
        <td style="border-top: 1px solid black;"></td>
        <td align="center" style="border-top: 1px solid black;"></td>
        <td style="border-top: 1px solid black; border-right: 1px solid black;"></td>
        <td style="border-top: 1px solid black;"></td>
        <td align="center" style="border-top: 1px solid black;"></td>
        <td style="border-top: 1px solid black; border-right: 1px solid black;"></td> 
    </tr>
</table>

