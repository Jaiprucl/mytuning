[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

[{ if $readonly }]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

<form name="transfer" id="transfer" action="[{ $oViewConf->getSelfLink() }]" method="post">
    [{ $oViewConf->getHiddenSid() }]
    <input type="hidden" name="oxid" value="[{ $oxid }]">
    <input type="hidden" name="cl" value="billpay_view">
</form>
[{if $isBillpayPayment}]
<table><tr><td>
	[{if $billpayType == $billpayUtils->getIName()}]
		[{oxmultilang ident="BILLPAY_INVOICE_TEXT_INFO_01"}]<br />
		[{if $billpayStatus == "open"}]
			[{oxmultilang ident="BILLPAY_INVOICE_TEXT_INFO_02_1"}]<br />
			[{oxmultilang ident="BILLPAY_INVOICE_TEXT_INFO_02_2"}]<br />
		[{else if $billpayStatus == "activated"}]
			[{oxmultilang ident="BILLPAY_INVOICE_TEXT_INFO_02"}] <b>[{$duedate}]</b><br />
			[{oxmultilang ident="BILLPAY_INVOICE_TEXT_INFO_03"}]<br />
		[{/if}]
		<br />
		<table>
			<tr>
				<td><b>[{oxmultilang ident="BILLPAY_INVOICE_TEXT_ACCOUNT_HOLDER"}]</b></td>
				<td>[{$accountHolder}]</td>
			</tr>
			<tr>
				<td><b>[{oxmultilang ident="BILLPAY_INVOICE_TEXT_ACCOUNT_NUMBER"}]</b></td>
				<td>[{$accountNumber}]</td>
			</tr>
			<tr>
				<td><b>[{oxmultilang ident="BILLPAY_INVOICE_TEXT_BANK_CODE"}]</b></td>
				<td>[{$bankCode}]</td>
			</tr>
			<tr>
				<td><b>[{oxmultilang ident="BILLPAY_INVOICE_TEXT_BANK_NAME"}]</b></td>
				<td>[{$bankName}]</td>
			</tr>
			<tr>
				<td><b>[{oxmultilang ident="BILLPAY_INVOICE_TEXT_BANK_REFERENCE"}]</b></td>
				<td>[{$invoiceReference}]</td>
			</tr>
			<tr>
				<td><b>[{oxmultilang ident="BILLPAY_INVOICE_TEXT_DUEDATE"}]</b></td>
				<td>[{$duedate}]</td>
			</tr>
		</table>
	[{elseif $billpayType == $billpayUtils->getDDName()}]
		[{oxmultilang ident="BILLPAY_INVOICE_TEXT_DD_INFO_01"}]<br />
		[{oxmultilang ident="BILLPAY_INVOICE_TEXT_DD_INFO_02"}]<br />
		[{oxmultilang ident="BILLPAY_INVOICE_TEXT_DD_INFO_03"}]<br />
		<br />
	[{elseif $billpayType == $billpayUtils->getTCName()}]
		[{oxmultilang ident="BILLPAY_INVOICE_TEXT_TC_INFO_01"}]<br />
		[{oxmultilang ident="BILLPAY_INVOICE_TEXT_TC_INFO_02"}]<br />
		[{oxmultilang ident="BILLPAY_INVOICE_TEXT_TC_INFO_03"}]<br />
		<br />
		<table>
			[{ assign var="inptcounter" value="0"}]
			[{foreach key=due from=$dues item=aDue}]
			[{ assign var="inptcounter" value="`$inptcounter+1`"}]
			<tr>
				<td style="text-align: right;">[{$inptcounter}]. [{oxmultilang ident="BILLPAY_INVOICE_TEXT_TC_RATE"}]:</td>
				<td>&nbsp;</td>
				<td style="text-align: right;">[{$lang->formatCurrency($billpayUtils->priceToHigherUnit($aDue.value), $currency)}] [{ $currency->sign}]</td>
				[{if $aDue.date != ''}] <td>&nbsp;</td><td>([{oxmultilang ident="BILLPAY_INVOICE_TEXT_TC_INFO_08"}] [{$billpayUtils->formatDate($aDue.date)}])</td>[{/if}]
			</tr>
			[{/foreach}]
		</table>
		<br />
		<b>[{oxmultilang ident="BILLPAY_TPL_TC_TOTAL_CALCULATION"}]</b><br />
		<table>
			<tr>
				<td style="text-align: left;">[{oxmultilang ident="BILLPAY_INVOICE_TEXT_TC_INFO_09"}]</td>
				<td style="text-align: center;">&nbsp;=&nbsp;</td>
				<td style="text-align: right;">[{$lang->formatCurrency($billpayUtils->priceToHigherUnit($calculation.base), $currency)}] [{ $currency->sign}]</td>
			</tr>
			<tr>
				<td style="text-align: left;">[{oxmultilang ident="BILLPAY_INVOICE_TEXT_TC_INFO_10"}]</td>
				<td style="text-align: center;">&nbsp;+&nbsp;</td>
				<td style="text-align: right;"></td>
			</tr>
			<tr>
				<td style="text-align: left;">([{$lang->formatCurrency($billpayUtils->priceToHigherUnit($calculation.base), $currency)}] [{ $currency->sign}] x [{$calculation.interest}] x [{$numberRates}]) /100</td>
				<td style="text-align: center;">&nbsp;=&nbsp;</td>
				<td style="text-align: right;">[{$lang->formatCurrency($billpayUtils->priceToHigherUnit($calculation.surcharge), $currency)}] [{ $currency->sign}]</td>
			</tr>
			<tr>
				<td style="text-align: left;">[{oxmultilang ident="BILLPAY_INVOICE_TEXT_TC_INFO_12"}]</td>
				<td style="text-align: center;">&nbsp;+&nbsp;</td>
				<td style="text-align: right;">[{$lang->formatCurrency($billpayUtils->priceToHigherUnit($calculation.fee), $currency)}] [{ $currency->sign}]</td>
			</tr>
			<tr>
				<td style="text-align: left;">[{oxmultilang ident="BILLPAY_INVOICE_TEXT_TC_INFO_11"}]</td>
				<td style="text-align: center;">&nbsp;+&nbsp;</td>
				[{assign var="additional value = `$calculation.cart-$calculation.base`}]
				<td style="text-align: right;">[{$lang->formatCurrency($billpayUtils->priceToHigherUnit($additional), $currency)}] [{ $currency->sign}]</td>
			</tr>
			<tr>
				<td style="text-align: left;"><b>[{oxmultilang ident="BILLPAY_INVOICE_TEXT_TC_INFO_13"}]</b></td>
				<td style="text-align: center;"><b>&nbsp;=&nbsp;</b></td>
				<td style="text-align: right;"><b>[{$lang->formatCurrency($billpayUtils->priceToHigherUnit($calculation.total), $currency)}] [{ $currency->sign}]</b></td>
			</tr>
			<tr>
				<td style="text-align: left;"><b>[{oxmultilang ident="BILLPAY_INVOICE_TEXT_TC_INFO_14"}]</b></td>
				<td style="text-align: center;"><b>&nbsp;=&nbsp;</b></td>
				<td style="text-align: right;"><b>[{$billpayUtils->priceToHigherUnit($calculation.anual)}]%</b></td>
			</tr>
		</table>
		<br />
		
		[{oxmultilang ident="BILLPAY_INVOICE_TEXT_TC_INFO_04"}]<br />
		[{oxmultilang ident="BILLPAY_INVOICE_TEXT_TC_INFO_05_01"}]<br />
		[{oxmultilang ident="BILLPAY_INVOICE_TEXT_TC_INFO_06_01"}]<br />
		[{oxmultilang ident="BILLPAY_INVOICE_TEXT_TC_INFO_07_01"}]<br />
		[{oxmultilang ident="BILLPAY_INVOICE_TEXT_TC_INFO_07_02"}]<br />
	
	[{/if}]
</td>
<td>
	&nbsp;&nbsp;
</td>
<td style="vertical-align: top; text-align: right">
	[{include file="billpay_status.tpl"}]
</td></tr></table>

[{else}]
	[{oxmultilang ident="BILLPAY_VIEW_NO_BILLPAY_ORDER"}]<br />
[{/if}]

[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]
