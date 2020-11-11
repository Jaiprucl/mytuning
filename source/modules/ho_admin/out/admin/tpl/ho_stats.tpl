[{include file="headitem.tpl" box="box" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]
<script type="text/javascript">
<!--
    if (top)
    {
        top.sMenuItem    = "[{ oxmultilang ident="HO_ORDER_EXPORT" }]";
        top.setTitle();
    }
//-->
</script>

<h2 style="margin: 1px;">Statistiken</h2>
<br>
<h3>Bestellungen</h3>
<table>
  <tr>
	<td>Anzahl Bestellungen heute</td>
	<td>[{ $oView->getOrderToday() }]</td>
  </tr>
  <tr>
	<td>Anzahl Bestellungen gestern</td>
	<td>[{ $oView->getOrderYesterday() }]</td>
  </tr>
  <tr>
	<td>Anzahl Bestellungen gesamte Woche</td>
	<td>[{ $oView->getOrderWeek() }]</td>
  </tr>
  <tr>
	<td>Anzahl Bestellungen gesamter Monat</td>
	<td>[{ $oView->getOrderMonth() }]</td>
  </tr>
</table>
<h3>Artikel</h3>
Hier werden demn&auml;chst Statistiken aufgelistet.
<h3>SEO</h3>
Hier werden demn&auml;chst Statistiken aufgelistet.