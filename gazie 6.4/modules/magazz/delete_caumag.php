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


if (isset($_POST['Delete'])) {
    gaz_dbi_del_row($gTables['caumag'], "codice", $_GET['codice']);
    header("Location: report_caumag.php");
    exit;
} else {
    $form = gaz_dbi_get_row($gTables['caumag'], "codice", $_GET['codice']);
}

if (isset($_POST['Return'])){
        header("Location: report_caumag.php");
        exit;
}

require("../../library/include/header.php");
$script_transl=HeadMain();
require("./lang.".$admin_aziend['lang'].".php");
$title = ucwords($script_transl['delete'].$strScript["admin_caumag.php"][0]);
print "<form method=\"POST\">\n";
print "<div align=\"center\" class=\"FacetFormHeaderFont\">$title</div>\n";
print "<table border=\"0\" cellpadding=\"3\" cellspacing=\"1\" class=\"FacetFormTABLE\" align=\"center\">\n";
print "<tr><td class=\"FacetFieldCaptionTD\">".$strScript["admin_caumag.php"][1]."</td><td class=\"FacetDataTD\">".$form["codice"]."</td></tr>";
print "<tr><td class=\"FacetFieldCaptionTD\">".$strScript["admin_movmag.php"][2]."</td><td class=\"FacetDataTD\">".$form["descri"]."</td></tr>\n";
print "<tr><td class=\"FacetFieldCaptionTD\">".$strScript["admin_caumag.php"][4]."</td><td class=\"FacetDataTD\">".$strScript["admin_caumag.php"][$form["operat"]+9]."</td></tr>\n";
print "<tr><td class=\"FacetFieldCaptionTD\">".$strScript["admin_caumag.php"][11]."</td><td class=\"FacetDataTD\">".$strScript["admin_caumag.php"][$form["clifor"]+14]."</td></tr>\n";
print "<tr><td class=\"FacetFieldCaptionTD\">".$strScript["admin_caumag.php"][5]."</td><td class=\"FacetDataTD\">".$strScript["admin_caumag.php"][$form["upesis"]+6]."</td></tr>\n";
print "<td align=\"right\"><input type=\"submit\" name=\"Return\" value=\"".$script_transl['return']."\"></td><td align=\"right\"><input type=\"submit\" name=\"Delete\" value=\"".strtoupper($script_transl['delete'])."!\"></td></tr>";
?>
</table>
</form>
</body>
</html>