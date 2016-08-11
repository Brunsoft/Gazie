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


function lastDocNumber($year,$type='FAI',$vat_section=1)
{
    global $gTables;
    $last_pro = gaz_dbi_dyn_query("protoc, UNIX_TIMESTAMP(datfat) AS uts", $gTables['tesdoc'],
                        "YEAR(datfat) = $year
                         AND tipdoc LIKE '".substr($type,0,1)."__'
                         AND seziva = $vat_section","protoc DESC",0,1);
    $last = gaz_dbi_fetch_array($last_pro);
    $rtn['uts'] = $last['uts'];
    if ($last) {
        $rtn['protoc'] = $last['protoc'] + 1;
    } else {
        $rtn['protoc'] = 1;
    }
    $last_doc = gaz_dbi_dyn_query("numfat*1 AS n_fatt,numdoc", $gTables['tesdoc'],
                        "YEAR(datfat) = $year
                         AND tipdoc LIKE '".substr($type,0,2)."_'
                         AND seziva = $vat_section","protoc DESC",0,1);
    $last = gaz_dbi_fetch_array($last_doc);
    if ($last) {
        $rtn['numfat'] = $last['n_fatt'] + 1;
        $rtn['numdoc'] = $last['numdoc'] + 1;
    } else {
        $rtn['numfat'] = 1;
        $rtn['numdoc'] = 1;
    }
    return $rtn;
}

function getBillableContracts($date_ref=false, $vat_section=1,$customer=0)
{
    global $gTables;
    if ($date_ref==false){
        $date_ref = date("Y-m-d");
    }
    $selected_customer='';
    if ($customer >0 ){
       $selected_customer = " AND ".$gTables['tesdoc'].".clfoco = $customer";
    }
    $field =  $gTables['contract'].'.*,
              DATE_FORMAT(\''.$date_ref.'\',\'%Y\')*12 + DATE_FORMAT(\''.$date_ref.'\',\'%m\') AS this_month,
              YEAR('.$gTables['tesdoc'].'.datfat)*12 + MONTH('.$gTables['tesdoc'].'.datfat) AS last_month,
              YEAR('.$gTables['contract'].'.start_date)*12 + MONTH('.$gTables['contract'].'.start_date) AS start_month,
              ('.$gTables['contract'].'.months_duration - PERIOD_DIFF(DATE_FORMAT(\''.$date_ref.'\',\'%Y%m\' ),
              EXTRACT(YEAR_MONTH FROM '.$gTables['contract'].'.start_date))) AS months_at_end,
              '.$gTables['tesdoc'].'.clfoco, '.$gTables['tesdoc'].'.datfat AS df, CONCAT('.$gTables['anagra'].'.ragso1,\' \','.$gTables['anagra'].'.ragso2) AS ragsoc,
              PERIOD_ADD(EXTRACT(YEAR_MONTH FROM '.$gTables['tesdoc'].'.datfat),'.$gTables['contract'].'.periodicity) AS next_month';
    $from =  $gTables['contract'].' LEFT JOIN '.$gTables['tesdoc'].
             ' ON '.$gTables['contract'].'.id_contract=(SELECT '.$gTables['tesdoc'].'.id_contract FROM '.$gTables['tesdoc'].' WHERE '.$gTables['tesdoc'].'.tipdoc=\'FAI\' OR '.$gTables['tesdoc'].'.tipdoc=\'VRI\' LIMIT 1 ) 
              LEFT JOIN '.$gTables['clfoco'].' ON '.$gTables['clfoco'].'.codice='.$gTables['contract'].'.id_customer
              LEFT JOIN '.$gTables['anagra'].' ON '.$gTables['clfoco'].'.id_anagra='.$gTables['anagra'].'.id';
    $where = $gTables['contract'].".vat_section = $vat_section $selected_customer";
    $orderby = $gTables['contract'].'.id_contract ASC, '.$gTables['tesdoc'].'.datfat ASC, '.$gTables['tesdoc'].'.protoc ASC';
    $result = gaz_dbi_dyn_query($field, $from, $where, $orderby);
    $billable=array();
    while ($row = gaz_dbi_fetch_array($result)) {
           $billable[$row['id_contract']] = $row;
    }
    return $billable;
}

function getContractRows($id_contract)
{
    global $gTables;
    $rs_rows = gaz_dbi_dyn_query("protoc, UNIX_TIMESTAMP(datfat) AS uts", $gTables['contract_row'],
                        "YEAR(datfat) = $year
                         AND tipdoc LIKE '".substr($type,0,1)."__'
                         AND seziva = $vat_section","protoc DESC",0,1);
    $last = gaz_dbi_fetch_array($last_pro);
}

if (!isset($_POST['vat_section'])){ // al primo accesso
    $form['hidden_req'] = '';
    if (!isset($_GET['vat_section'])) {
      $_GET['vat_section']= 1;
    }
    $form['vat_section']=intval($_GET['vat_section']);
    $form['this_date_Y']=date("Y");
    $form['this_date_M']=date("m");
    $form['this_date_D']=date("d");
    $billable = getBillableContracts(false,$form['vat_section']);
} else { // accessi successivi
    $form['hidden_req'] = $_POST['hidden_req'];
    $form['vat_section']=intval($_POST['vat_section']);
    $form['this_date_Y']=intval($_POST['this_date_Y']);
    $form['this_date_M']=intval($_POST['this_date_M']);
    $form['this_date_D']=intval($_POST['this_date_D']);
    $uts_this_month = mktime(0,0,0,$form['this_date_M'],$form['this_date_D'],$form['this_date_Y']);
    $form['this_date'] = strftime("%Y-%m-%d",$uts_this_month);
    $billable = getBillableContracts($form['this_date'],$form['vat_section']);
    $calc= new venditCalc;
    if (isset($_POST['create']) && empty($msg)) {
       $first_protoc=0;
       $first_numdoc=0;
       foreach ($billable as $k=>$val) {
          if (isset($_POST['check_'.$k])){ // se è stato selezionato il contratto da fatturare
              $last = lastDocNumber($form['this_date_Y'],$val['doc_type'],$form['vat_section']);
              if ($first_protoc==0) {
                  $first_protoc=$last['protoc'];
                  $last_protoc=$last['protoc'];
                  $first_numdoc=$last['numfat'];
                  $last_numdoc=$last['numfat'];
              } else {
                  $last_protoc++;
                  $last_numdoc++;
              }
              //inserisco i dati della testata
              $calc->contractCalc($k); // creo e calcolo il castelletto IVA e i totali del contratto
              $cntr = gaz_dbi_get_row($gTables['contract'],'id_contract',$k);
              $paym = gaz_dbi_get_row($gTables['pagame'],'codice',$cntr['payment_method']);
              if ($paym['tippag'] == 'B' ||
                  $paym['tippag'] == 'T')  {
                  $speban = $admin_aziend['sperib'];
              } else {
                  $speban = 0;
              }
              $stamp=0;
              $round_stamp = 0;
              $taxstamp=0;
              if ($calc->total_exc > $admin_aziend['taxstamp_limit'] && $admin_aziend['virtual_taxstamp'] > 0 ) {
                $taxstamp = $admin_aziend['taxstamp'];
              }
              if ($paym['tippag'] == 'T') {  //se il pagamento prevede il bollo
                $stamp = $admin_aziend['perbol'];
                $round_stamp = $admin_aziend['round_bol'];
              }
              $head_data = array('seziva'=>$cntr['vat_section'],'tipdoc'=>$cntr['doc_type'],
                                 'datemi'=>$form['this_date'],'protoc'=>$last['protoc'],
                                 'numdoc'=>$last['numdoc'],'numfat'=>$last['numfat'],
                                 'datfat'=>$form['this_date'],'clfoco'=>$cntr['id_customer'],
                                 'pagame'=>$cntr['payment_method'],'banapp'=>$cntr['bank'],
                                 'speban'=>$speban,'expense_vat'=>$admin_aziend['preeminent_vat'],'stamp'=>$stamp,'round_stamp'=>$round_stamp,
                                 'taxstamp'=>$taxstamp,'virtual_taxstamp'=>$admin_aziend['virtual_taxstamp'],
                                 'id_agente'=>$cntr['id_agente'],'id_contract'=>$k,'initra'=>$form['this_date'],
                                 'status'=>'GENERATO','template'=>'FatturaSemplice'
                                 );
              tesdocInsert($head_data);
              $tesdoc_id = gaz_dbi_last_id();
              //inserisco i primi 2 righi (sempre)
              require("lang.".$admin_aziend['lang'].".php");
              $uts_conclusion = mktime(0,0,0,substr($cntr['conclusion_date'],5,2),substr($cntr['conclusion_date'],8,2),substr($cntr['conclusion_date'],0,4));
              $conclusion_date = strftime("%d %B %Y",$uts_conclusion);
              $rows_data = array('id_tes'=>$tesdoc_id,'tiprig'=>2,
                                 'descri'=>$strScript['invoice_from_contract.php']['ref'].
                                           $conclusion_date
                                 );
              rigdocInsert($rows_data);
              //formatto il periodo
              if(empty($val['last_month'])) { //first time
                 $y=floor($val['start_month']/12);
                 $m=$val['start_month']-$y*12;
                 $fee = $cntr['current_fee']*floor(1+($val['this_month']-$val['start_month'])/$val['periodicity']);
              } else {
                 $y=floor($val['last_month']/12);
                 $m=$val['last_month']-$y*12+1;
                 $fee = $cntr['current_fee']*floor(($val['this_month']-$val['last_month'])/$val['periodicity']);
              }
              $uts_first = mktime(0,0,0,$m,$form['this_date_D'],$y);
              $uts_last = mktime(0,0,0,$form['this_date_M']+$cntr['periodicity']-1,$form['this_date_D'],$form['this_date_Y']);
              $period = strftime("%B %Y",$uts_first);
              if ($uts_last > $uts_first) {
                  $period .= ' - '.strftime("%B %Y",$uts_last);
              }
              $vat_per = gaz_dbi_get_row($gTables['aliiva'],'codice',$val['vat_code']);
              $rows_data = array('id_tes'=>$tesdoc_id,'tiprig'=>1,
                                 'descri'=>$strScript['invoice_from_contract.php']['period'].
                                           $strScript['invoice_from_contract.php']['period_value'][$cntr['periodicity']].
                                           $period,
                                 'prelis'=>$fee,
                                 'codvat'=>$cntr['vat_code'],
                                 'pervat'=>$vat_per['aliquo'],
                                 'codric'=>$cntr['cod_revenue'],
                                 'provvigione'=>$cntr['provvigione']
                                 );
              rigdocInsert($rows_data);
              // e se ci sono altri addebiti
              $rs_rows = gaz_dbi_dyn_query("*", $gTables['contract_row'],"id_contract = ".$val['id_contract'],'id_row ASC');
              while ($row = gaz_dbi_fetch_array($rs_rows)) {
                     $vat_per = gaz_dbi_get_row($gTables['aliiva'],'codice',$row['vat_code']);
                     $rows_data = array('id_tes'=>$tesdoc_id,'tiprig'=>0,
                                        'descri'=>$row['descri'],
                                        'unimis'=>$row['unimis'],
                                        'quanti'=>$row['quanti'],
                                        'prelis'=>$row['price'],
                                        'sconto'=>$row['discount'],
                                        'codvat'=>$row['vat_code'],
                                        'pervat'=>$vat_per['aliquo'],
                                        'codric'=>$row['cod_revenue']
                                        );
                     rigdocInsert($rows_data);
              }
          }
       }
       //Mando in stampa le ricevute o le fatture generate
       if ($cntr['doc_type']=='VRI'){
                $doc_type = 7;
       } else {
                $doc_type = 4;
       }
       $locazione = "Location: select_docforprint.php?tipdoc=".$doc_type."&seziva=".$form['vat_section'].
                    "&proini=".$first_protoc."&profin=".$last_protoc.
                    "&numini=".$first_numdoc."&numfin=".$last_numdoc.
                    "&datini=".strftime("%Y%m%d",$uts_this_month)."&datfin=".strftime("%Y%m%d",$uts_this_month);
       header($locazione);
       exit;
    }
}
$form['rows']=array();
$uts_this_month = mktime(0,0,0,$form['this_date_M'],$form['this_date_D'],$form['this_date_Y']);
$FAI = lastDocNumber($form['this_date_Y'],'FAI',$form['vat_section']);
$uts_last['FAI'] = $FAI['uts'];
$VRI = lastDocNumber($form['this_date_Y'],'VRI',$form['vat_section']);
$uts_last['VRI'] = $VRI['uts'];
require("../../library/include/header.php");
$script_transl=HeadMain(0,array('calendarpopup/CalendarPopup'));
require("lang.".$admin_aziend['lang'].".php");

foreach ($billable as $k=>$val) {
            $form['rows'][$val['id_contract']]['doc_number'] = $val['doc_number'];
            $form['rows'][$val['id_contract']]['start_date'] = $val['start_date'];
            $form['rows'][$val['id_contract']]['ragsoc'] = $val['ragsoc'];
            $form['rows'][$val['id_contract']]['current_fee'] = $val['current_fee'];
            $form['rows'][$val['id_contract']]['df'] = $val['df'];
            $form['rows'][$val['id_contract']]['months_at_end'] = $val['months_at_end'];
            $form['rows'][$val['id_contract']]['tacit_renewal'] = $val['tacit_renewal'];
            $form['rows'][$val['id_contract']]['doc_type'] = $val['doc_type'];
            if (!empty($val['last_month'])){
                $form['rows'][$val['id_contract']]['n_bill'] = floor(($val['this_month']-$val['last_month'])/$val['periodicity']);
            } else {
                $form['rows'][$val['id_contract']]['n_bill'] = floor(1+($val['this_month']-$val['start_month'])/$val['periodicity']);
            }
            if ($form['rows'][$val['id_contract']]['n_bill'] > 0){
                $form['rows'][$val['id_contract']]['check_'.$k] = 'checked';
            } else {
                $form['rows'][$val['id_contract']]['check_'.$k] = '';
            }
            //rilevazione errori
            $form['rows'][$val['id_contract']]['error'] = '';
            if ($uts_last[$val['doc_type']] > $uts_this_month) { // ci sono fatture o ricevute emesse con date sucessive
               $form['rows'][$val['id_contract']]['error'] = $script_transl['err_date'];
               $form['rows'][$val['id_contract']]['check_'.$k] = 'disabled';
            }
            if ($val['months_at_end'] <= 0 && $val['tacit_renewal'] == 0) {
               $form['rows'][$val['id_contract']]['error'] = $script_transl['expired'];
               $form['rows'][$val['id_contract']]['check_'.$k] = 'disabled';
            }

}
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
echo "<form method=\"POST\" name=\"contract\">\n";
echo "<input type=\"hidden\" value=\"".$form['hidden_req']."\" name=\"hidden_req\" />\n";
$gForm = new GAzieForm();
echo "<div align=\"center\" class=\"FacetFormHeaderFont\">".$script_transl['title'].$script_transl['vat_section'];
$gForm->selectNumber('vat_section',$form['vat_section'],0,1,3,'FacetSelect','vat_section');
echo ' '.$script_transl['on'].' ';
$gForm->CalendarPopup('this_date',$form['this_date_D'],$form['this_date_M'],$form['this_date_Y'],'FacetSelect',1);
echo "</div>\n";
echo "<table class=\"Tlarge\">\n";
echo "<tr class=\"FacetColumnTD\">\n";
echo "<td align=\"right\">".$strScript['admin_contract.php']['doc_number']."</td>\n";
echo "<td align=\"center\">".$strScript['admin_contract.php']['start_date']."</td>\n";
echo "<td>".$strScript['admin_contract.php']['customer']."</td>\n";
echo "<td align=\"right\">".$strScript['admin_contract.php']['current_fee']."</td>\n";
echo "<td align=\"center\">".$strScript['admin_contract.php']['last_document_date']."</td>\n";
echo "<td align=\"center\">".$script_transl['n_creation']."</td>\n";
echo "<td align=\"center\">".$strScript['admin_contract.php']['doc_type']."</td>\n";
echo "<td></td>\n";
echo "\t </tr>\n";
foreach ($form['rows'] as $k=>$val) {
            echo "<tr class=\"FacetDataTD\">\n";
            echo "<td align=\"right\">".$val['doc_number']."</td>\n";
            echo "<td align=\"center\">".gaz_format_date($val['start_date'])."</td>\n";
            echo "<td>".$val['ragsoc']."</td>\n";
            echo "<td align=\"right\">".gaz_format_number($val['current_fee'])."</td>\n";
            echo "<td align=\"center\">".$val['df']."</td>\n";
            echo "<td align=\"center\">".$val['n_bill']."</td>\n";
            echo "<td align=\"center\">".$strScript['admin_contract.php']['doc_type_value'][$val['doc_type']]."</td>\n";
            if (empty($val['error'])) {
                echo "<td align=\"center\"><input type=\"checkbox\" name=\"check_$k\" ".$val['check_'.$k]." ></td>\n";
            } else {
                echo "<td class=\"FacetDataTDred\" align=\"center\">".$val['error']."</td>\n";
            }
            echo "\t </tr>\n";
}
if (count($form['rows']) > 0) {
   echo "\t<tr class=\"FacetFieldCaptionTD\">\n";
   echo '<td colspan="7" align="right"><input type="submit" name="create" value="';
   echo $script_transl['submit'];
   echo '">';
   echo "\t </td>\n";
   echo "\t </tr>\n";
} else {
   echo "\t<tr>\n";
   echo '<td colspan="8" align="center" class="FacetDataTDred">';
   echo $script_transl['norows'];
   echo "\t </td>\n";
   echo "\t </tr>\n";
}
echo "</table>\n";
?>
</form>
</body>
</html>