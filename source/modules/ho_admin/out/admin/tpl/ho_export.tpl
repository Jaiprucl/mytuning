[{include file="headitem.tpl" box="box" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]
<link rel="stylesheet" type="text/css" href="../modules/ho_admin/out/src/tools/datatables/datatables.min.css"/> 
<link rel="stylesheet" href="../modules/ho_admin/out/src/css/main.css">
<script type="text/javascript" src="../modules/ho_admin/out/src/js/jquery-1.9.1.js"></script>
<script type="text/javascript" src="../modules/ho_admin/out/src/tools/datatables/datatables.min.js"></script>
<script type="text/javascript">
<!--
    if (top)
    {
        top.sMenuItem    = "[{ oxmultilang ident="HO_ORDER_EXPORT" }]";
        top.setTitle();
    }
//-->
$(document).ready( function () {
    $('#order').DataTable({
			"language": {
				"url": "../modules/ho_admin/tools/datatables/german.json"
			}
		});
} );
</script>

<h2 style="margin: 1px;">[{ oxmultilang ident="HO_EXPORT_TITLE" }]</h2>
<br>
<h3>[{ oxmultilang ident="HO_EXPORT_ACT_ORDER" }]</h3>
<p>
		[{if $oView->getOrderList()}]
			<table id="order">
				<thead>
					<tr>
						<th>Best.-Nr.</th>
						<th>Datum</th>
						<th>Kdnr.</th>
						<th>Anrede</th>
						<th>Name</th>
						<th>Vorname</th>
						<th>Straße</th>
						<th>Hausnr.</th>
						<th>PLZ</th>
						<th>Ort</th>
						<th>Land</th>
						<th>Gesamt</th>
						<th>Versand</th>
					</tr>
				</thead>
				<tbody>
					[{* $oView->getOrderList()|@print_r:1 *}]
					[{ assign var=“orderitemlist” value=$oView->getOrderList() }]
					[{foreach key=orderindex from=$oView->getOrderList() item=orderitem}]
					<tr>
						[{foreach key=rowindex from=$orderitem item=rowitem}]
							<td>[{$rowitem}]</td>
						[{/foreach}]
					</tr>
					[{/foreach}]
				</tbody>
			</table>
		[{else}]
			Keine Bestellungen vorhanden.
		[{/if}]
</p>
<form action="" method="post">
<p>
	<input style="padding: 1px 15px;" type="submit" name="export-octoflex" value="[{ oxmultilang ident="HO_EXPORT_ORDER" }]">
</p>
<br><br>
</form>
<h3>[{ oxmultilang ident="HO_EXPORT_CRON" }]</h3>
<p>
Pfad f&uuml;r Cronjob:
<input style="width: 600px;" type="text" value="[{$oView->getCronjobPath()}]" disabled="disabled">
</p>