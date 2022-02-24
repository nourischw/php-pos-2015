<?php extract($params); ?>
<table border="0" cellpadding="0" style="line-height: 25px; font-family: Helvetica;">
    <tr>
        <td width="5" rowspan="3"></td>
        <td width="100" rowspan="3"><strong>PAYMENT DETAIL:</strong></td>
        <td width="306" rowspan="3">{{ $payment_terms }}</td>
        <td width="5" style="border-left: 1px solid black; border-bottom: 1px solid black;"></td>
        <td width="76" style="border-bottom: 1px solid black;"><strong> TOTAL</strong></td>
        <td width="50" align="right"style="border-bottom: 1px solid black;"><strong>(HKD)</strong></td>
        <td width="5"style="border-bottom: 1px solid black; border-right: 1px solid black;"></td>
        <td width="93" align="right" style="border-bottom: 1px solid black;"><strong>${{ $total_amount }}</strong></td>
        <td width="5" style="border-bottom: 1px solid black; border-right: 1px solid black;"></td>
    </tr>

    <tr>
        <td style="border-left: 1px solid black; border-bottom: 1px solid black;"></td>
        <td style="border-bottom: 1px solid black;"><strong> DISCOUNT</strong></td>
        <td align="right" style="border-bottom: 1px solid black;"><strong>(HKD)</strong></td>
        <td style="border-bottom: 1px solid black; border-right: 1px solid black;"></td>
        <td align="right" style="border-bottom: 1px solid black;"><strong>- ${{ $discount_amount }}</strong></td>
        <td style="border-bottom: 1px solid black; border-right: 1px solid black;"></td>
    </tr>

    <tr>
        <td style="border-left: 1px solid black; border-bottom: 1px solid black;"></td>
        <td style="border-bottom: 1px solid black;"><strong> SUB TOTAL</strong></td>
        <td align="right" style="border-bottom: 1px solid black;"><strong>(HKD)</strong></td>
        <td style="border-bottom: 1px solid black; border-right: 1px solid black;"></td>
        <td align="right" style="border-bottom: 1px solid black;"><strong>${{ $sub_total_amount }}</strong></td>
        <td style="border-bottom: 1px solid black; border-right: 1px solid black;"></td>
    </tr>
</table>

<br /><br />

<table width="640" cellpadding="0" style="font-family: Helvetica; text-align: center;">
    <tr>
        <td width="220">MOBILE CITY (HK) LIMITED</td>
        <td width="200"></td>
        <td width="220">Accept and Confirm by</td>
    </tr>

    <tr><td colspan="3" height="40"></td></tr>

    <tr>
        <td>_______________________________________</td>
        <td></td>
        <td>_______________________________________</td>
    </tr>
</table>

<br /><br />

<table width="640" cellpadding="0" style="line-height: 20px; border-top: 1px solid black;">
    <tr>
        <td width="500" style="font-family: Helvetica;"><i>Thank you for considering us! If you have any other concerns or comments, please feel free to contact us.</i></td>
        <td width="140" align="right">{{ $shop_code }}</td>
    </tr>
</table>