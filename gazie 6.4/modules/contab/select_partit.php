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
$msg = "";


function getMovements($account_ini,$account_fin,$date_ini,$date_fin)
    {
        global $gTables;
        if ($account_ini==$account_fin || $account_fin==0 ) {
            if ($account_fin==0) {
              $account_fin=$account_ini;
            }
            $where = " codcon = $account_ini AND datreg BETWEEN $date_ini AND $date_fin";
            $orderby = " datreg, id_tes ASC ";
            $select = $gTables['tesmov'].".id_tes,".$gTables['tesmov'].".descri AS tesdes,datreg,codice,protoc,numdoc,datdoc,".$gTables['clfoco'].".descri,import*(darave='D') AS dare,import*(darave='A') AS avere";
        } else {
            $where = $gTables['clfoco'].".codice BETWEEN $account_ini AND $account_fin AND datreg BETWEEN $date_ini AND $date_fin GROUP BY ".$gTables['clfoco'].".codice";
            $orderby = " codice ASC ";
            $select = "codice,".$gTables['clfoco'].".descri AS tesdes, COUNT(id_rig) AS rows, SUM(import*(darave='D')) AS dare, SUM(import*(darave='A')) AS avere";
        }
        $table = $gTables['clfoco']." LEFT JOIN ".$gTables['rigmoc']." ON ".$gTables['clfoco'].".codice = ".$gTables['rigmoc'].".codcon "
                    ."LEFT JOIN ".$gTables['tesmov']." ON ".$gTables['rigmoc'].".id_tes = ".$gTables['tesmov'].".id_tes ";

        $m=array();
        $rs=gaz_dbi_dyn_query ($select, $table, $where, $orderby);
        while ($r = gaz_dbi_fetch_array($rs)) {
            $m[] = $r;
        }
        return $m;
}


if (!isset($_POST['hidden_req'])) { //al primo accesso allo script
    $form['hidden_req'] = '';
    $form['ritorno'] = $_SERVER['HTTP_REFERER'];
    if (!isset($_GET['di'])) {
       $form['date_ini_D'] = "1";
    } else {
       $form['date_ini_D'] = intval($_GET['di']);
    }
    if (!isset($_GET['mi'])) {
       $form['date_ini_M'] = "1";
    } else {
       $form['date_ini_M'] = intval($_GET['mi']);
    }
    if (!isset($_GET['yi'])) {
       $rs_last_opening = gaz_dbi_dyn_query("YEAR(datreg) AS anno", $gTables['tesmov'], "caucon = 'APE'","datreg DESC",0,1);
       $last_opening = gaz_dbi_fetch_array($rs_last_opening);
       if ($last_opening) {
          $form['date_ini_Y'] = $last_opening['anno'];
       } else {
          $form['date_ini_Y'] = date("Y");
       }
    } else {
       $form['date_ini_Y'] = intval($_GET['yi']);
    }
    if (!isset($_GET['df'])) {
       $form['date_fin_D'] = date("d");
    } else {
       $form['date_fin_D'] = intval($_GET['df']);
    }
    if (!isset($_GET['mf'])) {
       $form['date_fin_M'] = date("m");
    } else {
       $form['date_fin_M'] = intval($_GET['mf']);
    }
    if (!isset($_GET['yf'])) {
       $form['date_fin_Y'] = date("Y");
    } else {
       $form['date_fin_Y'] = intval($_GET['yf']);
    }
    $form['this_date_Y']=date("Y");
    $form['this_date_M']=date("m");
    $form['this_date_D']=date("d");
    if (isset($_GET['id'])) {
       $form['master_ini']=substr($_GET['id'],0,3).'000000';
       $form['account_ini']=intval($_GET['id']);
       $form['master_fin']=$form['master_ini'];
       $form['account_fin']=$form['account_ini'];
    } elseif(isset($_GET['msf']) && isset($_GET['msi']) && isset($_GET['aci']) && isset($_GET['acf'])) {
       $form['master_ini']=intval($_GET['msi']);
       $form['account_ini']=intval($_GET['aci']);
       $form['master_fin']=intval($_GET['msf']);
       $form['account_fin']=intval($_GET['acf']);
    } else {
       $form['master_ini']=0;
       $form['account_ini']=0;
       $form['master_fin']=999000000;
       $form['account_fin']=999999999;
    }
    $form['search']['account_ini']='';
    $form['search']['account_fin']='';
} else { // accessi successivi
    $form['hidden_req']=htmlentities($_POST['hidden_req']);
    $form['ritorno']=$_POST['ritorno'];
    $form['date_ini_D']=intval($_POST['date_ini_D']);
    $form['date_ini_M']=intval($_POST['date_ini_M']);
    $form['date_ini_Y']=intval($_POST['date_ini_Y']);
    $form['date_fin_D']=intval($_POST['date_fin_D']);
    $form['date_fin_M']=intval($_POST['date_fin_M']);
    $form['date_fin_Y']=intval($_POST['date_fin_Y']);
    $form['this_date_Y']=intval($_POST['this_date_Y']);
    $form['this_date_M']=intval($_POST['this_date_M']);
    $form['this_date_D']=intval($_POST['this_date_D']);
    $form['master_ini']=intval($_POST['master_ini']);
    $form['account_ini']=intval($_POST['account_ini']);
    $form['master_fin']=intval($_POST['master_fin']);
    $form['account_fin']=intval($_POST['account_fin']);
    foreach($_POST['search'] as $k=>$v){
       $form['search'][$k]=$v;
    }
    if (isset($_POST['selall'])) {
       $query = 'SELECT MAX(codice) AS max, MIN(codice) AS min '.
                'FROM '.$gTables['clfoco'].
                " WHERE codice NOT LIKE '%000000'";
       $rs_extreme_accont = gaz_dbi_query($query);
       $extreme_account = gaz_dbi_fetch_array($rs_extreme_accont);
       if ($extreme_account) {
          $form['master_ini'] = substr($extreme_account['min'],0,3).'000000';
          $form['account_ini'] = $extreme_account['min'];
          $form['master_fin'] = substr($extreme_account['max'],0,3).'000000';
          $form['account_fin'] = $extreme_account['max'];
       }
    }
    if (isset($_POST['selfin'])) {
       $form['master_fin']=$form['master_ini'];
       $form['account_fin']=$form['account_ini'];
    }
    if (isset($_POST['return'])) {
        header("Location: ".$form['ritorno']);
        exit;
    }
}

//controllo i campi
if (!checkdate( $form['this_date_M'],$form['this_date_D'],$form['this_date_Y']) ||
    !checkdate( $form['date_ini_M'], $form['date_ini_D'], $form['date_ini_Y']) ||
    !checkdate( $form['date_fin_M'], $form['date_fin_D'], $form['date_fin_Y'])) {
    $msg .='0+';
}
$utsexe= mktime(0,0,0,$form['this_date_M'],$form['this_date_D'],$form['this_date_Y']);
$utsini= mktime(0,0,0,$form['date_ini_M'],$form['date_ini_D'],$form['date_ini_Y']);
$utsfin= mktime(0,0,0,$form['date_fin_M'],$form['date_fin_D'],$form['date_fin_Y']);
if ($utsini > $utsfin) {
    $msg .='1+';
}
if ($utsexe < $utsfin) {
    $msg .='2+';
}
if ($form['account_fin']<$form['account_ini'] && $form['account_fin']>0) {
    $msg .='3+';
}
// fine controlli

if (isset($_POST['print']) && $msg=='') {
    //Mando in stampa i movimenti contabili generati
    if ($form['account_fin']==0){
        $form['account_fin']==$form['account_ini'];
    }
    $_SESSION['print_request']=array('script_name'=>'stampa_partit',
                                     'codice'=>$form['account_ini'],
                                     'codfin'=>$form['account_fin'],
                                     'regini'=>date("dmY",$utsini),
                                     'regfin'=>date("dmY",$utsfin),
                                     'ds'=>date("dmY",$utsexe)
                                     );
    header("Location: sent_print.php");
    exit;
}

require("../../library/include/header.php");
$script_transl=HeadMain(0,array('calendarpopup/CalendarPopup'));
echo "<script type=\"text/javascript\">
var cal = new CalendarPopup();
var calName = '';
function setMultipleValues(y,m,d) {
     document.getElementById(calName+'_Y').value=y;
     document.getElementById(calName+'_M').selectedIndex=m*1-1;
     document.getElementById(calName+'_D').selectedIndex=d*1-1;
}
function setDate(name) {
  calName = name.toString();
  var year = document.getElementById(calName+'_Y').value.toString();
  var month = document.getElementById(calName+'_M').value.toString();
  var day = document.getElementById(calName+'_D').value.toString();
  var mdy = month+'/'+day+'/'+year;
  cal.setReturnFunction('setMultipleValues');
  cal.showCalendar('anchor', mdy);
}

// nuova funzione inserita da Zanella69 per la copia delle select conti iniziali sui conti finali

function copy(conto){
	var fr=conto.form;
  fr.master_fin.value=fr.master_ini.value;
	fr.account_fin.options.length=0;
	var master=fr.account_ini.options;
	for (i=0; i<master.length; i++){
    if (fr.account_ini.selectedIndex==i) {
  		fr.account_fin.options[i]=new Option(master[i].text, master[i].value, false, true)
    } else {
  		fr.account_fin.options[i]=new Option(master[i].text, master[i].value, false, false)
		}
	}
}
</script>
";
echo "<form method=\"POST\" name=\"select\">\n";
echo "<input type=\"hidden\" value=\"".$form['hidden_req']."\" name=\"hidden_req\" />\n";
echo "<input type=\"hidden\" value=\"".$form['ritorno']."\" name=\"ritorno\" />\n";
//echo "<input type=\"hidden\" value=\"".$form['search']."\" name=\"search\" />\n";
$gForm = new contabForm();
echo "<div align=\"center\" class=\"FacetFormHeaderFont\">".$script_transl['title'];
echo "</div>\n";
echo "<table class=\"Tmiddle\">\n";
if (!empty($msg)) {
    echo '<tr><td colspan="2" class="FacetDataTDred">'.$gForm->outputErrors($msg,$script_transl['errors'])."</td></tr>\n";
}
echo "<tr>\n";
echo "<td class=\"FacetFieldCaptionTD\">".$script_transl['date']."</td><td  colspan=\"2\" class=\"FacetDataTD\">\n";
$gForm->CalendarPopup('this_date',$form['this_date_D'],$form['this_date_M'],$form['this_date_Y'],'FacetSelect',1);
echo "</td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td class=\"FacetFieldCaptionTD\">".$script_transl['master_ini']."</td><td class=\"FacetDataTD\">\n";
$gForm->selMasterAcc('master_ini',$form['master_ini'],'master_ini');
echo "</td>\n";
echo "<td rowspan=\"2\" class=\"FacetDataTD\">";
echo '<input type="submit" name="selall" value="';
echo $script_transl['selall'];
echo '">';
echo "</td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td class=\"FacetFieldCaptionTD\">".$script_transl['account_ini']."</td><td class=\"FacetDataTD\">\n";
$gForm->lockSubtoMaster($form['master_ini'],'account_ini');
$gForm->selSubAccount('account_ini',$form['account_ini'],$form['search']['account_ini'],$form['hidden_req'],$script_transl['mesg']);
echo "</td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td class=\"FacetFieldCaptionTD\">".$script_transl['master_fin']."</td><td class=\"FacetDataTD\">\n";
$gForm->selMasterAcc('master_fin',$form['master_fin'],'master_fin');
echo "</td>\n";
echo "<td rowspan=\"2\" class=\"FacetDataTD\">";
echo '<input type="button" onclick="copy(this)" value="';
echo $script_transl['selfin'];
echo '">';
echo "</td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td class=\"FacetFieldCaptionTD\">".$script_transl['account_fin']."</td><td class=\"FacetDataTD\">\n";
$gForm->lockSubtoMaster($form['master_fin'],'account_fin');
$gForm->selSubAccount('account_fin',$form['account_fin'],$form['search']['account_fin'],$form['hidden_req'],$script_transl['mesg']);
echo "</td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td class=\"FacetFieldCaptionTD\">".$script_transl['date_ini']."</td><td colspan=\"2\" class=\"FacetDataTD\">\n";
$gForm->CalendarPopup('date_ini',$form['date_ini_D'],$form['date_ini_M'],$form['date_ini_Y'],'FacetSelect',1);
echo "</td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td class=\"FacetFieldCaptionTD\">".$script_transl['date_fin']."</td><td colspan=\"2\" class=\"FacetDataTD\">\n";
$gForm->CalendarPopup('date_fin',$form['date_fin_D'],$form['date_fin_M'],$form['date_fin_Y'],'FacetSelect',1);
echo "</td>\n";
echo "</tr>\n";
echo "\t<tr class=\"FacetFieldCaptionTD\">\n";
echo "<td align=\"left\"><input type=\"submit\" name=\"return\" value=\"".$script_transl['return']."\">\n";
echo '<td align="right" colspan="2"> <input type="submit" accesskey="i" name="preview" value="';
echo $script_transl['view'];
echo '" tabindex="100" >';
echo "\t </td>\n";
echo "\t </tr>\n";
echo "</table>\n";
//recupero tutti i movimenti contabili dei conti insieme alle relative testate...
$date_ini =  sprintf("%04d%02d%02d",$form['date_ini_Y'],$form['date_ini_M'],$form['date_ini_D']);
$date_fin =  sprintf("%04d%02d%02d",$form['date_fin_Y'],$form['date_fin_M'],$form['date_fin_D']);

if (isset($_POST['preview']) and $msg=='') {
  $span=6;
  $saldo=0.00;
  $m = getMovements($form['account_ini'], $form['account_fin'], $date_ini, $date_fin);
  echo "<table class=\"Tlarge\">";
  if (sizeof($m) > 0) {
     if ($form['account_ini'] < $form['account_fin']) {
        echo "<tr>";
        $linkHeaders = new linkHeaders($script_transl['header1']);
        $linkHeaders -> output();
        echo "</tr>";
        while (list($key, $mv) = each($m)) {
            echo "<tr><td class=\"FacetDataTD\">".$mv["codice"]." &nbsp;</td>";
            echo "<td  align=\"center\" class=\"FacetDataTD\">".$mv["rows"]." &nbsp</td>";
            echo "<td class=\"FacetDataTD\">".$mv["tesdes"]." &nbsp;</td>";
            echo "<td align=\"right\" class=\"FacetDataTD\">".gaz_format_number($mv['dare'])." &nbsp;</td>";
            echo "<td align=\"right\" class=\"FacetDataTD\">".gaz_format_number($mv['avere'])." &nbsp;</td>";
            echo "<td align=\"right\" class=\"FacetDataTD\">".gaz_format_number($mv['dare'] - $mv['avere'])." &nbsp;</td></tr>";
        }
     } else {
        $span=9;
        echo "<tr>";
        $linkHeaders = new linkHeaders($script_transl['header2']);
        $linkHeaders -> output();
        echo "</tr>";
        while (list($key, $mv) = each($m)) {
            $saldo += $mv['dare'];
            $saldo -= $mv['avere'];
            echo "<tr><td class=\"FacetDataTD\">".gaz_format_date($mv["datreg"])." &nbsp;</td>";
            echo "<td align=\"center\" class=\"FacetDataTD\"><a href=\"admin_movcon.php?id_tes=".$mv["id_tes"]."&Update\">".$mv["id_tes"]."</a> &nbsp</td>";
            echo "<td class=\"FacetDataTD\">".$mv["tesdes"]." &nbsp;</td>";
            if (!empty($mv['numdoc'])){
                echo "<td align=\"center\" class=\"FacetDataTD\">".$mv["protoc"]." &nbsp;</td>";
                echo "<td align=\"center\" class=\"FacetDataTD\">".$mv["numdoc"]." &nbsp;</td>";
                echo "<td align=\"center\" class=\"FacetDataTD\">".gaz_format_date($mv["datdoc"])." &nbsp;</td>";
            } else {
                echo "<td class=\"FacetDataTD\" colspan=\"3\"></td>";
            }
            echo "<td align=\"right\" class=\"FacetDataTD\">".gaz_format_number($mv['dare'])." &nbsp;</td>";
            echo "<td align=\"right\" class=\"FacetDataTD\">".gaz_format_number($mv['avere'])." &nbsp;</td>";
            echo "<td align=\"right\" class=\"FacetDataTD\">".gaz_format_number($saldo)." &nbsp;</td></tr>";
        }
     }
     echo "\t<tr class=\"FacetFieldCaptionTD\">\n";
     echo '<td colspan="'.$span.'" align="right"><input type="submit" name="print" value="';
     echo $script_transl['print'];
     echo '">';
     echo "\t </td>\n";
     echo "\t </tr>\n";
  } else {
     echo "<tr><td class=\"FacetDataTDred\" align=\"center\">".$script_transl['errors'][4]."</TD></TR>\n";
  }
  echo "</table></form>";
}
?>
</body>
</html>