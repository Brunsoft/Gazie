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
$titolo="Eliminazione articolo";
$message = "Sei sicuro di voler rimuovere ?";
if (isset($_POST['Delete'])) {
        $result = gaz_dbi_del_row($gTables['artico'], "codice", substr($_POST['codice'],0,30));
        header("Location: report_artico.php");
        exit;
} else {
    $form = gaz_dbi_get_row($gTables['artico'], "codice", substr($_GET['codice'],0,30));
}

if (isset($_POST['Return'])) {
        header("Location: report_artico.php");
        exit;
}

$codice =$_GET['codice'];
require("../../library/include/header.php");
$script_transl=HeadMain();
?>
<form method="POST" action="<?php print $_SERVER['PHP_SELF']."?codice=".$codice; ?>" >
<input type="hidden" name="codice" value="<?php print $codice?>">
<div align="center" font class="FacetFormHeaderFont">Attenzione!!! Eliminazione Articolo Codice: <?php print $codice; ?> </div>
<table border="0" cellpadding="3" cellspacing="1" class="FacetFormTABLE" align="center">
<tr>
<td colspan="2" class="FacetDataTDred">
<?php
if (! $message == "")
    {
    print "$message";
    }
?>
</td>
</tr>
<tr>
<td class="FacetFieldCaptionTD">Codice Articolo &nbsp;</td>
<td class="FacetDataTD"><?php print $codice ?>&nbsp;</td>
</tr>
<tr>
<td class="FacetFieldCaptionTD">Descrizione &nbsp;</td>
<td class="FacetDataTD"><?php print $form["descri"] ?>&nbsp;</td>
</tr>
<tr>
<td class="FacetFieldCaptionTD">Unit&agrave; di misura &nbsp;</td>
<td class="FacetDataTD"><?php print $form["unimis"] ?>&nbsp;</td>
</tr>
<tr>
<td class="FacetFieldCaptionTD">Categoria merceologica  &nbsp;</td>
<td class="FacetDataTD"><?php print $form["catmer"] ?>&nbsp;</td>
</TR>
<tr>
<td class="FacetFieldCaptionTD">Prezzo d'acquisto &nbsp;</td>
<td class="FacetDataTD"><?php print $form["preacq"] ?>&nbsp;</td>
</tr>
<tr>
<td class="FacetFieldCaptionTD">Prezzo listino 1 &nbsp;</td>
<td class="FacetDataTD"><?php print $form["preve1"] ?>&nbsp;</td>
</tr>
<tr>
<td class="FacetFieldCaptionTD">Prezzo listino 2 &nbsp;</td>
<td class="FacetDataTD"><?php print $form["preve2"] ?>&nbsp;</td>
</tr>
<tr>
<td class="FacetFieldCaptionTD">Prezzo listino 3 &nbsp;</td>
<td class="FacetDataTD"><?php print $form["preve3"] ?>&nbsp;</td>
</tr>
<tr>
<td class="FacetFieldCaptionTD">Aliquota I.V.A &nbsp;</td>
<td class="FacetDataTD"><?php print $form["aliiva"] ?>&nbsp;</td>
</TR>
<tr>
<td class="FacetFieldCaptionTD">Esistenza attuale &nbsp;</td>
<td class="FacetDataTD"><?php print $form["esiste"] ?>&nbsp;</td>
</tr>
<tr>
<td class="FacetFieldCaptionTD">Valore dell'esistente &nbsp;</td>
<td class="FacetDataTD"><?php print $form["valore"] ?>&nbsp;</td>
</tr>
<tr>
<td class="FacetFieldCaptionTD">Conto contropartita &nbsp;</td>
<td class="FacetDataTD"><?php print $form["codcon"] ?>&nbsp;</td>
</tr>
<tr>
<td class="FacetFieldCaptionTD">Annotazioni &nbsp;</td>
<td class="FacetDataTD"><?php print $form["annota"] ?>&nbsp;</td>
</tr>
<td colspan="2" align="right">Se sei sicuro conferma l'eliminazione &nbsp;
<!-- BEGIN Button Return --><input type="submit" name="Return" value="Indietro"><!-- END Button Return -->&nbsp;
<!-- BEGIN Button Insert --><input type="submit" name="Delete" value="ELIMINA !"><!-- END Button Insert -->&nbsp;
</td>
</tr>
</table>
</form>
</body>
</html>