<?php extract($params); ?>

<table border="0" width="990" cellpadding="0" style="font-family: Helvetica; line-height: 18px; border-top: 1px solid black">
    <tr><td colspan="9" style="line-height: 30px;"></td></tr>

    <tr>
        <td width="5"></td>
        <td width="445">C. O. D.</td>
        <td width="45" style="font-family: msungstdlight">總金額:</td>
        <td width="60" style="text-align: center;">(HKD)</td>
        <td width="65" style="text-align: right;">${{ $sub_total_amount }}</td>
        <td width="160"></td>
        <td width="70" style="font-family: msungstdlight">總金額:</td>
        <td width="45">(HKD)</td>
        <td width="90" style="text-align: right;">${{ $total_amount }}</td>
    </tr>

    <tr>
        <td colspan="6"></td>
        <td width="70" style="font-family: msungstdlight">訂金:</td>
        <td width="45"></td>
        <td width="90" style="text-align: right;">{{ $payment_amount }}</td>	
    </tr>
	
    <tr>
        <td colspan="6"></td>
        <td width="70" style="font-family: msungstdlight">餘額:</td>
        <td width="45">(HKD)</td>
        <td width="90" style="text-align: right;">${{ $sub_total_amount }}</td>
    </tr>

    <tr>
        <td></td>
        <td colspan="5" style="font-family: msungstdlight">如手機不能開啓, 不接受14天新機換機服務。</td>
        <td colspan="3" style="text-align: right; font-family: msungstdlight">{{ $payment_type_text }}</td>
    </tr>
</table>

<table border="0" width="1000" cellpadding="0" style="font-family: Helvetica;">
    <tr><td colspan="9" style="line-height: 20px;"></td></tr>

    <tr>
        <td width="300" style="font-size: small; line-height: 9px;">
            <span style="font-family: msungstdlight">[一年保修，必須携同此單，遺失不獲補發]</span><br />
            Please retain this copy of invoice for 1 year warranty,<br />
            no invoice reprint is provided.<br />
            <span style="font-family: msungstdlight">請保留此單據作為維修及更換手機服務之用</span><br />
            <span style="font-family: msungstdlight">客戶服務熱線</span> Customer Service Hotline: (852) 8203 3166
        </td>
        <td width="340" style="line-height: 11px; text-align: right;">
            <span style="font-family: msungstdlight">如手機不能開啓，不接受14天新機換機服務。</span><br />
            No defective trade-in during the 14 days of purchase<br />
            guarantees if Handset could on power-on.<br />
            <span style="font-size: small;">** pay attention any article in front page **</span>
        </td>
        <td width="50"></td>
        <td width="70">Non-Member</td>
    </tr>
</table>