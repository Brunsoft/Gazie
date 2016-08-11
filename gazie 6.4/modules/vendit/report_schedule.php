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
require("../../library/include/header.php");
$script_transl=HeadMain();
echo '<div align="center" class="FacetFormHeaderFont">'.$script_transl['title'].'</div>';
$recordnav = new recordnav($gTables['paymov'], $where, $limit, $passo);
$recordnav -> output();
echo '<table class="Tlarge">';
$linkHeaders = new linkHeaders($script_transl['header']);
$linkHeaders->setAlign(array('left','center','center','center','right','center'));
$linkHeaders->output();

$result = gaz_dbi_dyn_query ('*', $gTables['paymov'], $where, $orderby, $limit, $passo);
while ($a_row = gaz_dbi_fetch_array($result))
    {
    // faccio una subquery che è più veloce di JOIN per ricavare l'id_tes
    $tes = gaz_dbi_get_row($gTables['rigmoc'],'id_rig = '.$a_row["id_rigmoc_pay"].' OR id_rig',$a_row["id_rigmoc_doc"]);
    echo "<tr>";
    echo "<td class=\"FacetDataTD\">".$a_row["id"]." &nbsp;</td>";
    echo "<td class=\"FacetDataTD\" align=\"center\">".$a_row["id_tesdoc_ref"]."</td>";
    if ($a_row["id_rigmoc_doc"]>0){
        echo "<td class=\"FacetDataTD\" align=\"center\"><a class=\"btn btn-xs btn-default btn-default\"  style=\"font-size:10px;\" href=\"../contab/admin_movcon.php?id_tes=".$tes["id_tes"]."&Update\">".$tes["id_tes"]." &nbsp;</td>";
    } else {
        echo "<td class=\"FacetDataTD\"></td>";
    }
    if ($a_row["id_rigmoc_pay"]>0){
        echo "<td class=\"FacetDataTD\" align=\"center\"><a class=\"btn btn-xs btn-default btn-default\"  style=\"font-size:10px;\" href=\"../contab/admin_movcon.php?id_tes=".$tes["id_tes"]."&Update\">".$tes["id_tes"]." &nbsp;</td>";
    } else {
        echo "<td class=\"FacetDataTD\"></td>";
    }
    echo "<td class=\"FacetDataTD\" align=\"right\">".$a_row["amount"]." &nbsp;</td>";
    echo "<td class=\"FacetDataTD\" align=\"center\">".$a_row["expiry"]." &nbsp;</td>";
    echo "</tr>";
    }
?>
</table>
</body>
</html>