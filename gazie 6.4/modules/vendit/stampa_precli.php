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
require("../../library/include/document.php");
$testat = $_GET['id_tes'];
$tesbro = gaz_dbi_get_row($gTables['tesbro'],"id_tes", $testat);

if ($tesbro['tipdoc'] <> 'VPR') {
    header("Location: report_broven.php");
    exit;
}
if (isset($_GET['dest'])&& $_GET['dest']=='E' ){ // se l'utente vuole inviare una mail
     createDocument($tesbro, 'PreventivoCliente',$gTables,'rigbro','E');
} else {
     createDocument($tesbro, 'PreventivoCliente',$gTables,'rigbro');
}
?>