<?php extract($params);?>
<table border="0" width="1000" style="text-align: center; font-family: Helvetica;">
    <tr>
        <td width="700">
            <table border="0" width="500" style="text-align: center; font-family: Helvetica;">
                <tr>
                    <td></td>
                </tr>
                <tr>
                    <td width="150"></td>
                    <td width="190">{{ $shop_address }}</td>
                    <td width="50"></td>
                    <td width="200">Tel: {{ $shop_tel }}<br />Fax: {{ $shop_fax }}</td>                      
                </tr>
            </table>
        </td>
        <td width="300">
            <table border="0" width="500" style="text-align: center; font-family: Helvetica;">
                <tr>
                    <td width="5"></td>
                    <td width="190">{{ $shop_address }}</td>
                </tr>
                <tr>
                    <td width="5"></td>
                    <td><span style="font-family: msungstdlight">電話號碼</span>: {{ $shop_tel }}</td>
                </tr>
            </table>
        </td>
    </tr>
    <tr><td colspan="3" style="line-height: 5px;"></td></tr>
</table>

<hr width="990" height="1" />

<table style="width: 1000px;" cellpadding="5" style="font-family: Helvetica;">
    <tr>
        <td width="55" style="font-family: msungstdlight">發票編號:</td>
        <td width="150" colspan="2">{{ $sales_invoice_number }}</td>

        <td width="250" rowspan="3" style="font-size: xx-large; font-family: msungstdlight;">銷售發票</td>
        <td width="212" style="font-size: x-large;" rowspan="3">Non-Member</td>

        <td width="55" style="font-family: msungstdlight">發票編號:</td>
        <td width="285" colspan="3">{{ $sales_invoice_number }}</td>
    </tr>

    <tr>
        <td style="font-family: msungstdlight">日期:</td>
        <td colspan="2">{{ $create_time }}</td>

        <td style="font-family: msungstdlight">日期:</td>
        <td colspan="3">{{ $create_time }}</td>
    </tr>

    <tr>
        <td style="font-family: msungstdlight">銷售員:</td>
        <td width="50">{{ $sales }}</td>
        <td width="100"><span  style="font-family: msungstdlight">收銀員</span>: {{ $cashier }}</td>

        <td style="font-family: msungstdlight">銷售員:</td>
        <td width="60">{{ $sales }}</td>
        <td width="45" style="font-family: msungstdlight">收銀員:</td>
        <td width="130">{{ $cashier }}</td>
    </tr>
</table>

<hr width="990" height="1" style="background: black" />

<table border="0" width="990" cellpadding="0" style="line-height: 15px;">
    <tr>
        <td width="5"></td>
        <td width="280">貨品名稱</td>
        <td width="100">序號</td>
        <td width="40" style="text-align: right;">數量</td>
        <td width="70" style="text-align: right;">單價</td>
        <td width="60" style="text-align: right;">折扣</td>
        <td width="65" style="text-align: right;">合計</td>
        <td width="52"></td>
        <td width="120" colspan="2">貨品名稱</td>
        <td width="55">序號</td>
        <td width="40" style="text-align: right;">數量</td>
        <td width="40" style="text-align: right;">折扣</td>
        <td width="65" style="text-align: right;">單價</td>
    </tr>
</table>

<hr width="990" height="1" style="background: black" />