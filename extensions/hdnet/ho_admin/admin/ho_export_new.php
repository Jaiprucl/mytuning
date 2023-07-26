<?php

namespace hdnet\ho_admin\admin;
use oxAdminView;
use oxDb;
use oxRegistry;

/**
 * ho.Systeme View Class
 *
 * @author Christopher Olhoeft
 */
class ho_export extends oxAdminView
{
    /**
     * Current class template name.
     * @var string
     */
    protected $_sThisTemplate = 'ho_export.tpl';

    public function render()
    {
        parent::render();
        // export to Ecoro
        if ($_POST['exportEcoro']) {
            $csv = $this->createCSV();
            echo ($csv) ? "<div style='padding: 5px; border: 1px solid #7A7A7A; display: block; background: #ddffb2;'>Bestellungen wurden exportiert!</div>" : "<div style='padding: 5px; border: 1px solid #7A7A7A; display: block; background: #fcc4c4;'>Es sind keine neuen Bestellungen vorhanden oder es ist ein Fehler aufgetreten!</div>";
        }
        return $this->_sThisTemplate;
    }

    /**
     *
     * @return string
     */

    public function getOrderName($id)
    {
        $sQ = "SELECT `OXBILLEMAIL` FROM `oxorder` where OXORDERNR = '" . $id . "' LIMIT 1";
        return oxDb::getDb()->getOne($sQ);
    }

    /**
     *
     * @return string
     */

    public function getOrderCountry($id)
    {
        $sQ = "SELECT `OXISOALPHA2` FROM `oxcountry` where OXID = '" . $id . "' LIMIT 1";
        return oxDb::getDb()->getOne($sQ);
    }

    /**
     *
     * @return string
     */

    public function getOrderShipping($id)
    {

        $shippingArt = array('6.95' => '401', '9.95' => '402', '14.95' => '403', '19.95' => '404', '39.95' => '405', '49.95' => '406', '59.95' => '407', '69.95' => '408',
            '99.95' => '409', '299.95' => '410', '29.95' => '411', '0' => '412', '79.95' => '413', '89.95' => '414', '119.95' => '415', '129.95' => '416',
            '149.95' => '417', '159.95' => '418', '179.95' => '419', '199.95' => '420', '249.95' => '421',);
        return $shippingArt[$id];
    }

    /**
     *
     * @return string
     */

    public function getOrderPayment($id)
    {
        $paymentArt = array('oxidpaypal' => '1', 'oxidcomfinanz' => '102', '60be3bf6b6a443f7e197df1bf94c2ace' => '104', 'oxidpayadvance' => '201', 'billpay_rec' => '301');
        return $paymentArt[$id];
    }

    /**
     *
     * @return string
     */

    public function setNameTitle($title)
    {
        return str_replace(array("MRS", "MR"), array("Frau", "Herr"), $title);
    }

    /**
     *
     * @return string
     */

    public function getLogData()
    {
        $log = file_get_contents('../log/jumbo/order.log', true);
        return $log;
    }

    /**
     *
     * @return string
     */

    public function formatDate($date)
    {
        $year = substr($date, 0, 4);
        $month = substr($date, 5, 2);
        $day = substr($date, 8, 2);
        return $year . $month . $day;
    }

    /**
     *
     * @return string
     */

    public function convert($string)
    {
        return mb_convert_encoding($string, 'ISO-8859-1', 'UTF-8');
    }

    /**
     *
     * @return bool
     */

    public function csvData($file, $fields)
    {
        $fp = fopen($file, 'a+');

        foreach ($fields as $array) {
            $cfields[] = $this->convert($array);
        }

        $csv = @fputcsv($fp, $cfields, chr(9), chr(0));
        fclose($fp);
        return true;
    }

    /**
     *
     * @return void
     */

    public function createCSV()
    {
        /**
         *
         */
        $_sThisExportConfig = oxRegistry::get("oxConfig");
        /**
         *
         */
        $_sThisExportPath = $_sThisExportConfig->getConfigParam("HO_EXPORT_PATH");
        /**
         *
         */
        $_sThisExportFileHead = $_sThisExportPath . $_sThisExportConfig->getConfigParam("HO_CSV_OCTO_ORDER");
        /**
         *
         */
        $_sThisExportFilePosition = $_sThisExportPath . $_sThisExportConfig->getConfigParam("HO_CSV_ORDER_POSITION");
        /**
         *
         */
        $sQ = "SELECT o.oxid, o.oxordernr, o.oxorderdate, u.oxcustnr, o.oxremark, o.oxbillsal, o.oxbilllname, o.oxbillfname, o.oxbillstreet, o.oxbillstreetnr,
				o.oxbillzip, o.oxbillcity, o.oxbillfon, o.oxbillfax, o.oxbillcountryid, o.oxbillemail, u.oxbirthdate, o.oxdelsal, o.oxdelfname, o.oxdellname,
				o.oxdelstreet, o.oxdelstreetnr, o.oxdelzip, o.oxdelcity, o.oxdelfon, o.oxdelfax, o.oxdelcountryid, o.oxtotalordersum, o.oxcurrency, o.oxdelcost,
				o.oxpaymenttype, o.oxtransstatus, o.oxshopid, o.oxlang, o.oxpaycost, o.oxfolder FROM `oxorder` as o join `oxuser` as u ON o.oxuserid = u.oxid where o.oxfolder = 'ORDERFOLDER_NEW' OR o.oxfolder = 'ORDERFOLDER_EBAY'";

        $oOrderlist = oxNew("oxlist");
        $oOrderlist->init("oxorder");
        $oOrderlist->selectString($sQ);

        foreach ($oOrderlist->arrayKeys() as $key) {
            $oOrder = $oOrderlist[$key];

            $fields = array(($oOrder->oxorder__oxordernr->value + 10000), # 1	Auftragsnummer	Text	P	ein Kv-Nr. hat immer das Format <Ordnungsbegriff>,<Nummer>. Der Ordnungsbegriff ensprich der FilialNr, die Nummer ist fortlaufend und bewegt sich in einem fest zugeordneten Nummernkreis	1,5637
                $this->formatDate(substr($oOrder->oxorder__oxorderdate->value, 0, 10)), #2	Auftragsdatum	Text	P	Format yyyyMMdd	20080501
                substr($oOrder->oxorder__oxorderdate->value, 11), #3	Auftragszeit	Text		Format hh:mm:ss	14:01:05
                $oOrder->oxorder__oxcustnr->value, #4	Kundennummer	Integer		Kundennummer aus Ecoro	85423
                $this->setNameTitle($oOrder->oxorder__oxbillsal->value), #5	Anrede	Text		Rechnungs- und Lieferadresse	Herr, Frau, Firma, Familie
                $oOrder->oxorder__oxbilllname->value, #6	Name1	Text	P	Rechnungs- und Lieferadresse	Mozart
                $oOrder->oxorder__oxbillfname->value, #7	Name2	Text		Rechnungs- und Lieferadresse	Wolfgang
                $oOrder->oxorder__oxbillstreet->value . " " . $oOrder->oxorder__oxbillstrnr->value, #8	Strasse	Text		Rechnungs- und Lieferadresse	Waldorfweg 3
                $oOrder->oxorder__oxbillzip->value, #9	Postleitzahl	Text	P	Rechnungs- und Lieferadresse	54682
                $oOrder->oxorder__oxbillcity->value, #10	Ort	Text	P	Rechnungs- und Lieferadresse	Erft
                $oOrder->oxorder__oxbillfon->value, #11	Telefon	Text		Rechnungs- und Lieferadresse	02354/51258
                $oOrder->oxorder__oxbillfax->value, #12	Fax	Text		Rechnungs- und Lieferadresse	02354/51260
                $this->getOrderCountry($oOrder->oxorder__oxbillcountryid->value), #13	Land	Text	P	Rechnungs- und Lieferadresse / LandKz	DE
                $oOrder->oxorder__oxbillemail->value, #14	Email	Text			test@moebel.de
                $Etage, #15	Etage	Integer			3
                str_replace("-", "", $oOrder->oxuser__oxbirthdate->value), #16	Geburtsdatum	Text		Format yyyyMMdd	19690101
                $Filiale, #17	GeoFilialeKunde	Integer	P	FilialNr der dem Kunden zugeordneten Filial. Hiermit wird zur Zeit nur die Sprache des Kunden ermittelt	1
                $oOrder->oxorder__oxdelsal->value, #18	Anrede	Text		ggf. abweichende Lieferadresse
                $oOrder->oxorder__oxdellname->value, #19	Name1	Text		ggf. abweichende Lieferadresse
                $oOrder->oxorder__oxdelfname->value, #20	Name2	Text		ggf. abweichende Lieferadresse
                $oOrder->oxorder__oxdelstreet->value, #21	Strasse	Text		ggf. abweichende Lieferadresse
                $oOrder->oxorder__oxdelzip->value, #22	Postleitzahl	Text		ggf. abweichende Lieferadresse
                $oOrder->oxorder__oxdelcity->value, #23	Ort	Text		ggf. abweichende Lieferadresse
                $oOrder->oxorder__oxdelfon->value, #24	Telefon	Text		ggf. abweichende Lieferadresse
                $oOrder->oxorder__oxdelfax->value, #25	Fax	Text		ggf. abweichende Lieferadresse
                $this->getOrderCountry($oOrder->oxorder__oxdelcountryid->value), #26	Land	Text		ggf. abweichende Lieferadresse
                $oOrder->oxorder__oxtotalordersum->value, #27	Gesamtpreis	Text		zur Zeit nicht verwendet. Gesamtpreis ergibt sich aus den Einzelpreisen
                $oOrder->oxorder__oxcurrency->value, #28	W�hrung	Text	P	ISO-Kennzeichen	EUR
                str_replace(array("\r\n", "\n", "\r"), ' ', $oOrder->oxorder__oxremark->value), #29	Bemerkungen	Text Falls gesetzt, wird es im Auftrag in den Zusatztext "Lieferhinweis" geschrieben
                $this->getOrderShipping($oOrder->oxorder__oxdelcost->value), #30	Lieferart	ID		Verkn�pfung mit der Satzart "Lieferarten"
                $oOrder->oxorder__oxdelcost->value, #31	Preis Lieferart	Numeric		Betrag	10.00 oder 10
                $oOrder->oxorder__oxpaymenttype->value, #32	Zahlungsart	ID		Verkn�pfung mit der Satzart "Zahlungsarten"
                $oOrder->oxorder__oxpaycost->value, #33	Preis Zahlungsart	Numeric		Betrag	10.00 oder 10
            );

            $orderhead = $this->csvData($_sThisExportFileHead, $fields);

            $sQ = "SELECT o.oxordernr, a.oxartnum, a.oxamount, a.oxbprice, a.oxbrutprice, a.oxvat, o.oxcurrency, a.oxtitle, a.oxshortdesc, a.oxdelivery, a.oxbrutprice,
					a.oxlength, a.oxwidth, a.oxheight FROM `oxorderarticles` AS a JOIN `oxorder` AS o ON a.oxorderid = o.oxid WHERE a.oxorderid = '" . $oOrder->oxorder__oxid->value . "'";
            $pOrderlist = oxNew("oxlist");
            $pOrderlist->init("oxorder");
            $pOrderlist->selectString($sQ);

            foreach ($pOrderlist->arrayKeys() as $key) {
                $pOrder = $pOrderlist[$key];

                $pfields = array(($oOrder->oxorder__oxordernr->value + 10000), #1
                    $PosNr, #2
                    $PosID, #3
                    $Artikelnummer, #4
                    $Menge, #5
                    $Einzelpreis, #6
                    $Gesamtpreis, #7
                    $pOrder->oxorder__oxcurrency->value, #8
                    $Waehrung, #9
                    $pOrder->oxorderarticles__oxtitle->value, #10
                    $Artikelbeschreibung, #11
                    $Filiale, #12
                    $Lieferdatum, #13
                    $Lieferstatus, #14
                    $LieferstatusText, #15
                    $TrackingNr, #16
                    $Lieferhinweis, #17
                    $ZusatztextTeil, #18
                    $Bruttopreis,    #19
                    $Nachlaesse, #20
                    $Ausfuehrung1, #21
                    $Ausfuehrung2, #22
                    $Ausfuehrung3, #23
                    $FremdspracheZusatztext, #24
                    $FremdspracheLieferstatus, #25
                    $WEStatus, #26
                    $lfdnrAuslieferung, #27
                    $UhrzeitPTV, #28
                    $vonUhrzeit, #29
                    $bisUhrzeit, #30
                    $ersterKunde, #31
                    $Lieferart #32
                );

                $orderposition = $this->csvData($_sThisExportFilePosition, $pfields);
            }
        }
    }

    /**
     *
     * @return string
     */

    public function setOrderEdit($id)
    {
        $sQ = "UPDATE oxorder set OXFOLDER = 'ORDERFOLDER_FINISHED' where OXORDERNR = '" . $id . "' LIMIT 1";
        $execute = oxDb::getDb()->Execute($sQ);
        if ($execute) {
            $this->logOrder(2, $id);
        } else {
            $this->logOrder(0, $id);
        }
    }

    /**
     *
     * @return void
     */

    public function logOrder($error, $id)
    {
        $handle = fopen("../log/jumbo/order.log", "a");
        switch ($error) {
            case(0):
                $success = "Fehler: " . mysql_error() . "\n";
                break;

            case(1):
                $success = date("d.m.y H:i:s") . " - Bestellung " . $id . " (" . $this->getOrderName($id) . ") wurde exportiert\r\n";
                break;

            case(2):
                $success = date("d.m.y H:i:s") . " - Bestellung " . $id . " (" . $this->getOrderName($id) . ") wurde als erledigt gekennzeichnet\r\n";
                break;
        }
        fputs($handle, $success);
        fclose($handle);
    }
}

?>