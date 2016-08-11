<?php
/*
 --------------------------------------------------------------------------
                            GAzie - Gestione Azienda
    Copyright (C) 2004-2015 - Antonio De Vincentiis Montesilvano (PE)
         (http://www.devincentiis.it)
           <http://gazie.sourceforge.net>
 --------------------------------------------------------------------------
    Questo programma e` free software;   e` lecito redistribuirlo  e/o
    modificarlo secondo i  termini della Licenza Pubblica Generica GNU
    come e` pubblicata dalla Free Software Foundation; o la versione 2
    della licenza o (a propria scelta) una versione successiva.

    Questo programma  e` distribuito nella speranza  che sia utile, ma
    SENZA   ALCUNA GARANZIA; senza  neppure  la  garanzia implicita di
    NEGOZIABILITA` o di  APPLICABILITA` PER UN  PARTICOLARE SCOPO.  Si
    veda la Licenza Pubblica Generica GNU per avere maggiori dettagli.

    Ognuno dovrebbe avere   ricevuto una copia  della Licenza Pubblica
    Generica GNU insieme a   questo programma; in caso  contrario,  si
    scriva   alla   Free  Software Foundation,  Inc.,   59
    Temple Place, Suite 330, Boston, MA 02111-1307 USA Stati Uniti.
 --------------------------------------------------------------------------
*/
require("../../library/include/datlib.inc.php");

$admin_aziend=checkAdmin();

$mascli = $admin_aziend['mascli']."000000";
$clienti = $admin_aziend['mascli'];
$message = "";

if (isset($_POST['Delete'])) {
    $rs_check_mov = gaz_dbi_dyn_query("clfoco", $gTables['tesmov'], "clfoco = '".intval($_POST['codice'])."'","id_tes asc",0,1);
    $check_mov = gaz_dbi_num_rows($rs_check_mov);
    $rs_check_doc = gaz_dbi_dyn_query("clfoco", $gTables['tesdoc'], "clfoco = '".intval($_POST['codice'])."'","id_tes asc",0,1);
    $check_doc = gaz_dbi_num_rows($rs_check_doc);
    $rs_check_bro = gaz_dbi_dyn_query("clfoco", $gTables['tesbro'], "clfoco = '".intval($_POST['codice'])."'","id_tes asc",0,1);
    $check_bro = gaz_dbi_num_rows($rs_check_bro);
    if ($check_mov > 0) {
        $message .= "Cliente non cancellabile perche' ha ".$check_mov." movimenti contabili!<br>";
        }
    if ($check_doc > 0) {
        $message .= "Cliente non cancellabile perche' ha ".$check_doc." documenti fiscali!<br>";
        }
    if ($check_bro > 0) {
        $message .= "Cliente non cancellabile perche' ha ".$check_bro." documenti non fiscali!<br>";
        }
    if ($message == "") {
        gaz_dbi_del_row($gTables['clfoco'], "codice", $_POST['codice']);
        header("Location: report_client.php");
        exit;
    }
}

if (isset($_POST['Return'])) {
    header("Location: report_client.php");
    exit;
    }


if (!isset($_POST['codice'])){
    $codice = intval($mascli + $_GET['codice']);
} else {
    $codice = intval($_POST['codice']);
}
$anagrafica = new Anagrafica();
$form= $anagrafica->getPartner($codice);
$titolo="Cancella Cliente";
require("../../library/include/header.php");
$script_transl=HeadMain();
?>
<form method="POST" >
<input type="hidden" name="codice" value="<?php echo $codice?>">
<div align="center"><font class="FacetFormHeaderFont">Attenzione!!! Eliminazione Cliente Codice: <?php echo $codice; ?> </font></div>
<table border="0" cellpadding="3" cellspacing="1" class="FacetFormTABLE" align="center">
  <tr>
    <td colspan="2" class="FacetDataTD"  style="color: red;">
    <?php
            if (! $message == "") {
                echo $message;
            } else {
                echo "Sei sicuro di voler rimuovere?";
            }
    ?>
    </td>
  </tr>
<tr>
    <td class="FacetFieldCaptionTD">Codice Cliente &nbsp;</td>
    <td class="FacetDataTD"><?php echo $form["codice"]; ?>&nbsp;</td>
  </tr>
  <tr>
    <td class="FacetFieldCaptionTD">Ragione sociale 1 &nbsp;</td>
    <td class="FacetDataTD"><?php echo $form["ragso1"]; ?>&nbsp;</td>
  </tr>
  <tr>
    <td class="FacetFieldCaptionTD">Ragione sociale 2 &nbsp;</td>
    <td class="FacetDataTD"><?php echo $form["ragso2"]; ?>&nbsp;</td>
  </tr>
<tr>
    <td class="FacetFieldCaptionTD">Indirizzo &nbsp;</td>
    <td class="FacetDataTD"><?php echo $form["indspe"]; ?>&nbsp;</td>
  </tr>
  <tr>
    <td class="FacetFieldCaptionTD">CAP &nbsp;</td>
    <td class="FacetDataTD"><?php echo $form["capspe"]; ?>&nbsp;</td>
  </tr>
  <tr>
    <td class="FacetFieldCaptionTD">Citta' - Provincia &nbsp;</td>
    <td class="FacetDataTD"><?php echo $form["citspe"] ?>&nbsp;- <?php echo $form["prospe"] ?>&nbsp;</td>
  </tr>
  <tr>
    <td class="FacetFieldCaptionTD">Telefono e/o fax &nbsp;</td>
    <td class="FacetDataTD"><?php echo $form["telefo"]; ?>&nbsp;</td>
  </tr>
  <tr>
    <td class="FacetFieldCaptionTD">Codice Fiscale - Partita IVA &nbsp;</td>
    <td class="FacetDataTD"><?php echo $form["codfis"] ?>&nbsp;- <?php echo $form["pariva"]; ?>&nbsp;</td>
  </tr>
<tr>
    <td colspan="2" align="right">Se sei sicuro conferma l'eliminazione &nbsp;
<input title="Torna indietro"  type="submit" name="Return" value="Indietro">&nbsp;
<input title="Elimina definitivamente dall'archivio"  type="submit" name="Delete" value="ELIMINA !">&nbsp;
   </td>
</tr></table>
</form>
</body>
</html>