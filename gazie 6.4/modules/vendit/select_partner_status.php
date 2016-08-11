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
$msg='';
$paymov = new Schedule;
$paymov->setScheduledPartner($admin_aziend['mascli']);

if (!isset($_POST['hidden_req'])) { //al primo accesso allo script
    $form['hidden_req'] = '';
    $form['ritorno'] = $_SERVER['HTTP_REFERER'];
    $form['date_ini_D']=date("d");
    $form['date_ini_M']=date("m");
    $form['date_ini_Y']=date("Y");
    $form['search']['account']='';
    if (isset($_GET['id'])) {
       $form['account']=intval($_GET['id']);
    } else {
       $form['account']=0;
    }
    $form['orderby']=0;
} else { // accessi successivi
    $form['hidden_req']=htmlentities($_POST['hidden_req']);
    $form['ritorno']=$_POST['ritorno'];
    $form['date_ini_D']=intval($_POST['date_ini_D']);
    $form['date_ini_M']=intval($_POST['date_ini_M']);
    $form['date_ini_Y']=intval($_POST['date_ini_Y']);
    if (isset($_POST['return'])) {
        header("Location: ".$form['ritorno']);
        exit;
    }
}

//controllo i campi
if (!checkdate( $form['date_ini_M'], $form['date_ini_D'], $form['date_ini_Y'])) {
    $msg .='0+';
}
// fine controlli

if (isset($_POST['print']) && $msg=='') {
    $_SESSION['print_request']=array('script_name'=>'print_partner_status',
                                     'date'=>$form['date_ini_Y'].'-'.$form['date_ini_M'].'-'.$form['date_ini_D']
                                     );
    header("Location: sent_print.php");
    exit;
}

require("../../library/include/header.php");
$script_transl=HeadMain(0,array('jquery/jquery-1.7.1.min','calendarpopup/CalendarPopup',
                                  'jquery/ui/jquery.ui.core',
                                  'jquery/ui/jquery.ui.widget',
                                  'jquery/ui/jquery.ui.position',
                                  'jquery/ui/jquery.ui.autocomplete',
                                  'jquery/autocomplete_location'));
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
</script>
";
echo "<form method=\"POST\" name=\"select\">\n";
echo "<input type=\"hidden\" value=\"".$form['hidden_req']."\" name=\"hidden_req\" />\n";
echo "<input type=\"hidden\" value=\"".$form['ritorno']."\" name=\"ritorno\" />\n";
$gForm = new venditForm();
echo "<div align=\"center\" class=\"FacetFormHeaderFont\">".$script_transl['title'];
echo "</div>\n";
echo "<table class=\"Tmiddle\">\n";
if (!empty($msg)) {
    echo '<tr><td colspan="2" class="FacetDataTDred">'.$gForm->outputErrors($msg,$script_transl['errors'])."</td></tr>\n";
}
echo "<tr>\n";
echo "<td class=\"FacetFieldCaptionTD\">".$script_transl['date_ini']."</td><td colspan=\"3\" class=\"FacetDataTD\">\n";
$gForm->CalendarPopup('date_ini',$form['date_ini_D'],$form['date_ini_M'],$form['date_ini_Y'],'FacetSelect',1);
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

if (isset($_POST['preview'])) {
  echo "<table class=\"Tlarge\">";
  if (sizeof($paymov->Partners) > 0) {
        $anagrafica = new Anagrafica();
        echo "<tr>";
        $linkHeaders = new linkHeaders($script_transl['header']);
        $linkHeaders->setAlign(array('right','right','right','center','center','center','center'));
        $linkHeaders -> output();
        echo "</tr>";
        foreach ($paymov->Partners as $p){
            $anagrafica = new Anagrafica();
            $prt = $anagrafica->getPartner($p);
            echo "<tr></tr>";
            echo "<tr class=\"FacetFieldCaptionTD\">";
            echo "<td colspan='5'>".$prt['ragso1']." ".$prt['ragso2'].
                  " tel:".gaz_html_call_tel($prt['telefo']).
                  " fax:".$prt['fax'].
                  " mob:".gaz_html_call_tel($prt['cell'])."</td>";
            echo "</tr>\n";
            $paymov->getPartnerStatus($p,$form['date_ini_Y'].'-'.$form['date_ini_M'].'-'.$form['date_ini_D']);
            foreach ($paymov->PartnerStatus as $k=>$v){
               echo "<tr>";
               echo "<td class=\"FacetDataTDred\" colspan='2'>REF: $k</td>";
               echo "<td class=\"FacetDataTD\" colspan='5'><a class=\"btn btn-xs btn-default btn-edit\" href=\"../contab/admin_movcon.php?Update&id_tes=".$paymov->docData[$k]['id_tes']."\"><i class=\"glyphicon glyphicon-edit\"></i>".
                    $paymov->docData[$k]['descri'].' n.'.
                    $paymov->docData[$k]['numdoc'].'/'.
                    $paymov->docData[$k]['seziva'].' '.
                    $paymov->docData[$k]['datdoc']."</a></td>\n";
               echo "</tr>\n";
               foreach ($v as $ki=>$vi){
                  $lnk='';
                  $class_paymov='FacetDataTDevidenziaCL';
                  $v_op='';
                  $cl_exp='';
                  if ($vi['op_val']>=0.01){
                     $v_op=gaz_format_number($vi['op_val']);
                  }
                  $v_cl='';
                  if ($vi['cl_val']>=0.01){
                     $v_cl=gaz_format_number($vi['cl_val']);
                     $cl_exp=gaz_format_date($vi['cl_exp']);
                  }
                  $expo='';
                  if ($vi['expo_day']>=1){ 
                     $expo=$vi['expo_day'];
                     if ($vi['cl_val']==$vi['op_val']){
                        $vi['status']=2; // la partita è chiusa ma è esposta a rischio insolvenza 
                        $class_paymov='FacetDataTDevidenziaOK';
                     }
                  } else {
                     if ($vi['cl_val']==$vi['op_val']){ // chiusa e non esposta
                        $cl_exp='';
                        $class_paymov='FacetDataTD';
                     } elseif($vi['status']==3){ // SCADUTA
                        $cl_exp='';
                        $class_paymov='FacetDataTDevidenziaKO';
                     } elseif($vi['status']==9){ // PAGAMENTO ANTICIPATO
                        $class_paymov='FacetDataTDevidenziaBL';
                        $vi['expiry']=$vi['cl_exp'];
                     } elseif($vi['status']==0){ // APERTA
                        $lnk=" &nbsp;<a title=\"Riscuoti\" class=\"btn btn-xs btn-default btn-pagamento\" href=\"customer_payment.php?partner=".$p."\"><i class=\"glyphicon glyphicon-euro\"></i></a>";
                     }
                  }
                  echo "<tr class='".$class_paymov."'>";
                  echo "<td align=\"right\">".$vi['id']."</td>";
                  echo "<td align=\"right\">".$v_op."</td>";
                  echo "<td align=\"center\">".gaz_format_date($vi['expiry'])."</td>";
                  echo "<td align=\"right\">".$v_cl."</td>";
                  echo "<td align=\"center\">".$cl_exp."</td>";
                  echo "<td align=\"center\">".$expo."</td>";
                  echo "<td align=\"center\">".$script_transl['status_value'][$vi['status']]." &nbsp; $lnk</td>";
                  echo "</tr>\n";
               }
            }
        }
     echo "\t<tr class=\"FacetFieldCaptionTD\">\n";
     echo '<td colspan="9" align="right"><input type="submit" name="print" value="';
     echo $script_transl['print'];
     echo '">';
     echo "\t </td>\n";
     echo "\t </tr>\n";
  } else {
     echo "<tr><td class=\"FacetDataTDred\" align=\"center\">".$script_transl['errors'][1]."</TD></TR>\n";
  }
  echo "</table></form>";
}
?>
</body>
</html>