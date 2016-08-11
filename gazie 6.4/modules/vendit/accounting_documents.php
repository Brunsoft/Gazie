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
require("../../library/include/calsca.inc.php");
$msg = '';


function getExtremeDocs($type='_',$vat_section=1,$date=false)
{
    global $gTables;
    $type = substr($type,0,1);
    $docs=array();
    if ($date){
       $date=' AND datfat <= '.$date;
    } else {
       $date='';
    }
    $from =  $gTables['tesdoc'];
    $where = "id_con = 0 AND seziva = $vat_section AND tipdoc LIKE '$type"."__' $date";
    $orderby = "datfat ASC, protoc ASC";
    $result = gaz_dbi_dyn_query('*',$from,$where,$orderby,0,1);
    $row = gaz_dbi_fetch_array($result);
    $docs['ini'] = array('proini'=>$row['protoc'],'date'=>$row['datfat']);
    $orderby = "datfat DESC, protoc DESC";
    $result = gaz_dbi_dyn_query('*',$from,$where,$orderby,0,1);
    $row = gaz_dbi_fetch_array($result);
    $docs['fin'] = array('profin'=>$row['protoc'],'date'=>$row['datfat']);
    return $docs;
}

function getDocumentsAccounts($type='___',$vat_section=1,$date=false,$protoc=999999999)
{
    global $gTables,$admin_aziend;
    $calc = new Compute;
    $type = substr($type,0,1);
    if ($date){
       $p=' AND (YEAR(datfat)*1000000+protoc) <= '.(substr($date,0,4)*1000000+$protoc);
       $d=' AND datfat <= '.$date;
    } else {
       $d='';
       $p='';
    }
    $from =  $gTables['tesdoc'].' AS tesdoc
             LEFT JOIN '.$gTables['pagame'].' AS pay
             ON tesdoc.pagame=pay.codice
             LEFT JOIN '.$gTables['clfoco'].' AS customer
             ON tesdoc.clfoco=customer.codice
             LEFT JOIN '.$gTables['anagra'].' AS anagraf
             ON customer.id_anagra=anagraf.id';
    $where = "id_con = 0 AND seziva = $vat_section AND tipdoc LIKE '$type"."__' $d $p";
    $orderby = "datfat ASC, protoc ASC";
    $result = gaz_dbi_dyn_query('tesdoc.*,
                        pay.tippag,pay.numrat,pay.incaut,pay.tipdec,pay.giodec,pay.tiprat,pay.mesesc,pay.giosuc,pay.id_bank,
                        customer.codice,
                        customer.speban AS addebitospese,
                        CONCAT(anagraf.ragso1,\' \',anagraf.ragso2) AS ragsoc,CONCAT(anagraf.citspe,\' (\',anagraf.prospe,\')\') AS citta',
                        $from,$where,$orderby);
    $doc=array();
    $ctrlp=0;
    while ($tes = gaz_dbi_fetch_array($result)) {
           if ($tes['protoc'] <> $ctrlp) { // la prima testata della fattura
                if ($ctrlp>0 && ($doc[$ctrlp]['tes']['stamp'] >= 0.01 || $doc[$ctrlp]['tes']['taxstamp'] >= 0.01 )) { // non è il primo ciclo faccio il calcolo dei bolli del pagamento e lo aggiungo ai castelletti
					$calc->payment_taxstamp($calc->total_imp+$calc->total_vat+$carry-$rit-$ivasplitpay+$taxstamp, $doc[$ctrlp]['tes']['stamp'],$doc[$ctrlp]['tes']['round_stamp']*$doc[$ctrlp]['tes']['numrat']);
					$calc->add_value_to_VAT_castle($doc[$ctrlp]['vat'],$taxstamp+$calc->pay_taxstamp,$admin_aziend['taxstamp_vat']);
					$doc[$ctrlp]['vat']=$calc->castle;
					// aggiungo il castelleto conti
					if (!isset($doc[$ctrlp]['acc'][$admin_aziend['boleff']])) {
						$doc[$ctrlp]['acc'][$admin_aziend['boleff']]['import'] = 0;
					}
					$doc[$ctrlp]['acc'][$admin_aziend['boleff']]['import'] += $taxstamp+$calc->pay_taxstamp;
                }    
                $carry=0;
                $ivasplitpay=0;
                $cast_vat=array();
                $cast_acc=array();
                $somma_spese=0;
                $totimpdoc=0;
                $totimp_decalc=0.00;
                $n_vat_decalc=0;
                $spese_incasso=$tes['numrat']*$tes['speban'];
                $taxstamp=0;
                $rit=0;
           } else {
                $spese_incasso=0;
           }
           // aggiungo il bollo sugli esenti/esclusi se nel DdT c'è ma non è ancora stato mai aggiunto
           if ($tes['taxstamp']>=0.01 && $taxstamp<0.01) {
                $taxstamp=$tes['taxstamp'];
           }           
	   if ($tes['virtual_taxstamp'] == 0 || $tes['virtual_taxstamp'] == 3) { //  se è a carico dell'emittente non lo aggiungo al castelletto IVA
		$taxstamp = 0.00;
	   }
           if ($tes['traspo']>=0.01) {
                   if (!isset($cast_acc[$admin_aziend['imptra']]['import'])) {
                      $cast_acc[$admin_aziend['imptra']]['import'] = $tes['traspo'];
                   } else {
                      $cast_acc[$admin_aziend['imptra']]['import'] += $tes['traspo'];
                   }
           }
           if ($spese_incasso>=0.01) {
                   if (!isset($cast_acc[$admin_aziend['impspe']]['import'])) {
                      $cast_acc[$admin_aziend['impspe']]['import'] = $spese_incasso;
                   } else {
                      $cast_acc[$admin_aziend['impspe']]['import'] += $spese_incasso;
                   }
           }
           if ($tes['spevar']>=0.01) {
                   if (!isset($cast_acc[$admin_aziend['impvar']]['import'])) {
                      $cast_acc[$admin_aziend['impvar']]['import'] = $tes['spevar'];
                   } else {
                      $cast_acc[$admin_aziend['impvar']]['import'] += $tes['spevar'];
                   }
           }
           //recupero i dati righi per creare il castelletto
           $from =  $gTables['rigdoc'].' AS rows
                    LEFT JOIN '.$gTables['aliiva'].' AS vat
                    ON rows.codvat=vat.codice';
           $rs_rig = gaz_dbi_dyn_query('rows.*,vat.tipiva AS tipiva',$from, "rows.id_tes = ".$tes['id_tes'],"id_tes DESC");
           while ($r = gaz_dbi_fetch_array($rs_rig)) {
              if ($r['tiprig'] <= 1) {//ma solo se del tipo normale o forfait
                 //calcolo importo rigo
                 $importo = CalcolaImportoRigo($r['quanti'],$r['prelis'],array($r['sconto'],$tes['sconto']));
                 if ($r['tiprig'] == 1) {
                    $importo = CalcolaImportoRigo(1,$r['prelis'], $tes['sconto']);
                 }
                 //creo il castelletto IVA
                 if (!isset($cast_vat[$r['codvat']]['impcast'])) {
                    $cast_vat[$r['codvat']]['impcast']=0;
                    $cast_vat[$r['codvat']]['ivacast']=0;
                    $cast_vat[$r['codvat']]['periva']=$r['pervat'];
                    $cast_vat[$r['codvat']]['tipiva']=$r['tipiva'];
                 }
                 $cast_vat[$r['codvat']]['impcast']+=$importo;
                 $cast_vat[$r['codvat']]['ivacast']+=round(($importo*$r['pervat'])/ 100,2);
                 $totimpdoc += $importo;
                 //creo il castelletto conti
                 if (!isset($cast_acc[$r['codric']]['import'])) {
                    $cast_acc[$r['codric']]['import'] = 0;
                 }
                 $cast_acc[$r['codric']]['import']+=$importo;
                 $rit+=round($importo*$r['ritenuta']/100,2);
                 // aggiungo all'accumulatore l'eventuale iva non esigibile (split payment PA)   
                 if ($r['tipiva']=='T') {
                    $ivasplitpay += round(($importo*$r['pervat'])/ 100,2);
                 }
              } elseif($r['tiprig'] == 3) {
                 $carry += $r['prelis'] ;
              }
           }
           $doc[$tes['protoc']]['tes']=$tes;
           $doc[$tes['protoc']]['acc']=$cast_acc;
           $doc[$tes['protoc']]['car']=$carry;
           $doc[$tes['protoc']]['isp']=$ivasplitpay;
           $doc[$tes['protoc']]['rit']=$rit;
           $somma_spese += $tes['traspo'] + $spese_incasso + $tes['spevar'] ;
           $calc->add_value_to_VAT_castle($cast_vat,$somma_spese,$tes['expense_vat']);
           $doc[$tes['protoc']]['vat']=$calc->castle;
           $ctrlp=$tes['protoc'];
    }
    if ($doc[$ctrlp]['tes']['stamp'] >= 0.01 || $taxstamp >= 0.01 ) { // a chiusura dei cicli faccio il calcolo dei bolli del pagamento e lo aggiungo ai castelletti
        $calc->payment_taxstamp($calc->total_imp+$calc->total_vat+$carry-$rit-$ivasplitpay+$taxstamp, $doc[$ctrlp]['tes']['stamp'],$doc[$ctrlp]['tes']['round_stamp']*$doc[$ctrlp]['tes']['numrat']);
        // aggiungo al castelletto IVA
	$calc->add_value_to_VAT_castle($doc[$ctrlp]['vat'],$taxstamp+$calc->pay_taxstamp,$admin_aziend['taxstamp_vat']);
        $doc[$ctrlp]['vat']=$calc->castle;
        // aggiungo il castelleto conti
        if (!isset($doc[$ctrlp]['acc'][$admin_aziend['boleff']])) {
           $doc[$ctrlp]['acc'][$admin_aziend['boleff']]['import'] = 0;
        }
        $doc[$ctrlp]['acc'][$admin_aziend['boleff']]['import'] += $taxstamp+$calc->pay_taxstamp;
    }    
    return $doc;
}


function computeTot($data)
{
	$tax=0;$vat=0;
	foreach($data as $k=>$v) {
          $tax += $v['impcast'];
          $vat += round($v['impcast']*$v['periva'])/ 100;
	}
	$tot=$vat+$tax;
	return array('taxable'=>$tax,'vat'=>$vat,'tot'=>$tot);
}


if (!isset($_POST['hidden_req'])) { //al primo accesso allo script
    if (isset($_GET['type'])){
       $form['type']=substr($_GET['type'],0,1);
    } else {
       $form['type']='F';
    }
    if (isset($_GET['vat_section'])){
       $form['vat_section']=intval($_GET['vat_section']);
    } else {
       $form['vat_section']=1;
    }
    $extreme=getExtremeDocs($form['type'],$form['vat_section']);
    if ($extreme['ini']['proini'] > 0) {
        $form['this_date_Y']=substr($extreme['fin']['date'],0,4);
        $form['this_date_M']=substr($extreme['fin']['date'],5,2);
        $form['this_date_D']=substr($extreme['fin']['date'],8,2);
    } else {
        $form['this_date_Y']=date("Y");
        $form['this_date_M']=date("m");
        $form['this_date_D']=date("d");
    }
    $form['proini']=$extreme['ini']['proini'];
    $form['profin']=$extreme['fin']['profin'];
    if (isset($_GET['last'])){
       $form['profin']=intval($_GET['last']);
    }
    $form['year_ini']=substr($extreme['ini']['date'],0,4);
    $form['year_fin']=substr($extreme['fin']['date'],0,4);
    $form['hidden_req'] = '';
} else {    // accessi successivi
    $form['type'] = substr($_POST['type'],0,1);
    $form['vat_section']=intval($_POST['vat_section']);
    $form['this_date_Y']=intval($_POST['this_date_Y']);
    $form['this_date_M']=intval($_POST['this_date_M']);
    $form['this_date_D']=intval($_POST['this_date_D']);
    $form['proini']=intval($_POST['proini']);
    $form['profin']=intval($_POST['profin']);
    $form['year_ini']=intval($_POST['year_ini']);
    $form['year_fin']=intval($_POST['year_fin']);
    $form['hidden_req'] = htmlentities($_POST['hidden_req']);
    if (!checkdate( $form['this_date_M'], $form['this_date_D'], $form['this_date_Y']))
          $msg .= "0+";
    if ($form['hidden_req']=='type' || $form['hidden_req']=='vat_section'){   //se cambio il registro
        $extreme=getExtremeDocs($form['type'],$form['vat_section']);
        if ($extreme['ini']['proini'] > 0) {
            $form['this_date_Y']=substr($extreme['fin']['date'],0,4);
            $form['this_date_M']=substr($extreme['fin']['date'],5,2);
            $form['this_date_D']=substr($extreme['fin']['date'],8,2);
        } else {
            $form['this_date_Y']=date("Y");
            $form['this_date_M']=date("m");
            $form['this_date_D']=date("d");
        }
        $form['proini']=$extreme['ini']['proini'];
        $form['profin']=$extreme['fin']['profin'];
        $form['year_ini']=substr($extreme['ini']['date'],0,4);
        $form['year_fin']=substr($extreme['fin']['date'],0,4);
    }
    $form['hidden_req'] = '';
    $uts_this_date = mktime(0,0,0,$form['this_date_M'],$form['this_date_D'],$form['this_date_Y']);
    if (isset($_POST['submit'])&& empty($msg)) {   //confermo la contabilizzazione
       $rs=getDocumentsAccounts($form['type'],$form['vat_section'],strftime("%Y%m%d",$uts_this_date),$form['profin']);
       if (count($rs>0)) {
          require("lang.".$admin_aziend['lang'].".php");
          $script_transl=$strScript['accounting_documents.php'];
          foreach ($rs as $k=>$v) {
                  switch($v['tes']['tipdoc']) {
                    case "FAD":case "FAI":case "FAP":case "FND":
                               $reg=2;$op=1;$da_c='A';$da_p='D';$kac=$admin_aziend['ivaven'];break;
                    case "FNC":$reg=2;$op=2;$da_c='D';$da_p='A';$kac=$admin_aziend['ivaven'];break;
                    case "VCO":$reg=4;$op=1;$da_c='A';$da_p='D';$kac=$admin_aziend['ivacor'];break;
                    case "VRI":$reg=4;$op=1;$da_c='A';$da_p='D';$kac=$admin_aziend['ivacor'];break;
                    case "AFA":$reg=6;$op=1;$da_c='D';$da_p='A';$kac=$admin_aziend['ivaacq'];break;
                    case 'AFC':$reg=6;$op=2;$da_c='A';$da_p='D';$kac=$admin_aziend['ivaacq'];break;
                    case 'AFD':$reg=6;$op=1;$da_c='D';$da_p='A';$kac=$admin_aziend['ivaacq'];break;
                    default:$reg=0;$op=0;break;
                  }
                  $tot=computeTot($v['vat']);
                  // fine calcolo totali
                  // calcolo le rate al fine di inserire le partite aperte  
                  $rate = CalcolaScadenze($tot['tot'],substr($v['tes']['datfat'],8,2),substr($v['tes']['datfat'],5,2),substr($v['tes']['datfat'],0,4),$v['tes']['tipdec'],$v['tes']['giodec'],$v['tes']['numrat'],$v['tes']['tiprat'],$v['tes']['mesesc'],$v['tes']['giosuc']);
                  // inserisco la testata
                  $newValue=array('caucon'=>$v['tes']['tipdoc'],
                           'descri'=>$script_transl['doc_type_value'][$v['tes']['tipdoc']],
                           'id_doc'=>$v['tes']['id_tes'],
                           'datreg'=>$v['tes']['datfat'],
                           'seziva'=>$v['tes']['seziva'],
                           'protoc'=>$v['tes']['protoc'],
                           'numdoc'=>$v['tes']['numfat'],
                           'datdoc'=>$v['tes']['datfat'],
                           'clfoco'=>$v['tes']['clfoco'],
                           'regiva'=>$reg,
                           'operat'=>$op
                           );
                  tesmovInsert($newValue);
                  $tes_id = gaz_dbi_last_id();
                  //inserisco i righi iva nel db
                  foreach($v['vat'] as $k=>$vv) {
                      $vat = gaz_dbi_get_row($gTables['aliiva'],'codice',$k);
                      //aggiungo i valori mancanti all'array
                      $vv['tipiva']=$vat['tipiva'];
                      $vv['codiva']=$k;
                      $vv['id_tes']=$tes_id;
                      $vv['impost']=round($vv['impcast']*$vv['periva'])/ 100;
                      rigmoiInsert($vv);
                  }
                  //inserisco i righi contabili nel db
                  if ($v['tes']['tipdoc']=='VCO') {  // se è uno scontrino cassa anzichè scontrino
                      $v['tes']['clfoco']=$admin_aziend['cassa_'];
                  }
                  rigmocInsert(array('id_tes'=>$tes_id,'darave'=>$da_p,'codcon'=>$v['tes']['clfoco'],'import'=>($tot['tot']-$v['rit'])));
                  // memorizzo l'id del cliente  
                  $paymov_id = gaz_dbi_last_id();
                  foreach($v['acc'] as $acc_k=>$acc_v) {
                      if ($acc_v['import']>0){
                         rigmocInsert(array('id_tes'=>$tes_id,'darave'=>$da_c,'codcon'=>$acc_k,'import'=>$acc_v['import']));
                      }
                  }
                  if ($tot['vat']>0){
                      rigmocInsert(array('id_tes'=>$tes_id,'darave'=>$da_c,'codcon'=>$kac,'import'=>$tot['vat']));
                  }
                  if ($v['rit']>0) {  // se ho una ritenuta d'acconto
                      rigmocInsert(array('id_tes'=>$tes_id,'darave'=>$da_p,'codcon'=>$admin_aziend['c_ritenute'],'import'=>$v['rit']));
                  }
                  if (($v['tes']['incaut']=='S') && ($v['tes']['tippag']<>'K')){  // se il pagamento prevede l'incasso automatico 
		      rigmocInsert(array('id_tes'=>$tes_id,'darave'=>$da_c,'codcon'=>$v['tes']['clfoco'],'import'=>($tot['tot']-$v['rit'])));
		      rigmocInsert(array('id_tes'=>$tes_id,'darave'=>$da_p,'codcon'=>$admin_aziend['cassa_'],'import'=>($tot['tot']-$v['rit'])));
		  }elseif($v['tes']['tippag']=='K'){// se effettuato con carte viene incassato direttamente su C.C.
                      rigmocInsert(array('id_tes'=>$tes_id,'darave'=>$da_c,'codcon'=>$v['tes']['clfoco'],'import'=>($tot['tot']-$v['rit'])));
		      rigmocInsert(array('id_tes'=>$tes_id,'darave'=>$da_p,'codcon'=>$v['tes']['id_bank'],'import'=>($tot['tot']-$v['rit'])));
                    
                  } else { // altrimenti inserisco le partite aperte
                      foreach($rate['import'] as $k_rate=>$v_rate) {
                          paymovInsert(array('id_tesdoc_ref'=>substr($v['tes']['datfat'],0,4).$reg.$v['tes']['seziva'].str_pad($v['tes']['protoc'],9,0,STR_PAD_LEFT),'id_rigmoc_doc'=>$paymov_id,'amount'=>$v_rate,'expiry'=>$rate['anno'][$k_rate].'-'.$rate['mese'][$k_rate].'-'.$rate['giorno'][$k_rate]));
                      }
                  }
                  // alla fine modifico le testate documenti introducendo il numero del movimento contabile
                  gaz_dbi_put_query($gTables['tesdoc'],"tipdoc = '".$v['tes']['tipdoc']."' AND datfat = '".$v['tes']['datfat']."' AND seziva = ".$v['tes']['seziva']." AND protoc = ".$v['tes']['protoc'],"id_con",$tes_id);
                  // movimenti di storno in caso di split payment 
                  if ($v['isp']>0){
                      // inserisco la testata del movimento di storno Split payment
                      $newValue=array('caucon'=>'ISP',
                           'descri'=>'STORNO IVA SPLIT PAYMENT PA',
                           'id_doc'=>$v['tes']['id_tes'],
                           'datreg'=>$v['tes']['datfat'],
                           'seziva'=>$v['tes']['seziva'],
                           'protoc'=>$v['tes']['protoc'],
                           'numdoc'=>$v['tes']['numfat'],
                           'datdoc'=>$v['tes']['datfat'],
                           'clfoco'=>$v['tes']['clfoco'],
                           'regiva'=>'',
                           'operat'=>''
                           );
                      tesmovInsert($newValue);
                      $tes_id = gaz_dbi_last_id();
                      rigmocInsert(array('id_tes'=>$tes_id,'darave'=>$da_p,'codcon'=>$kac,'import'=>$v['isp']));
                      rigmocInsert(array('id_tes'=>$tes_id,'darave'=>$da_c,'codcon'=>$admin_aziend['split_payment'],'import'=>$v['isp']));
                      rigmocInsert(array('id_tes'=>$tes_id,'darave'=>$da_p,'codcon'=>$admin_aziend['split_payment'],'import'=>$v['isp']));
                      rigmocInsert(array('id_tes'=>$tes_id,'darave'=>$da_c,'codcon'=>$v['tes']['clfoco'],'import'=>$v['isp']));
                  }
          }
          header("Location: report_docven.php");
          exit;
       } else {
          $msg .= "1+";
       }
    }
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
</script>
";

echo "<form method=\"POST\" name=\"accounting\">\n";
echo "<input type=\"hidden\" value=\"".$form['hidden_req']."\" name=\"hidden_req\" />\n";
echo "<input type=\"hidden\" value=\"".$form['proini']."\" name=\"proini\" />\n";
echo "<input type=\"hidden\" value=\"".$form['year_ini']."\" name=\"year_ini\" />\n";
echo "<input type=\"hidden\" value=\"".$form['year_fin']."\" name=\"year_fin\" />\n";
$gForm = new GAzieForm();
echo "<div align=\"center\" class=\"FacetFormHeaderFont\">".$script_transl['title'].$script_transl['vat_section'];
$gForm->selectNumber('vat_section',$form['vat_section'],0,1,3,'FacetSelect','vat_section');
echo "</div>\n";
echo "<table class=\"Tsmall\">\n";
if (!empty($msg)) {
    echo '<tr><td colspan="2" class="FacetDataTDred">'.$gForm->outputErrors($msg,$script_transl['errors'])."</td></tr>\n";
}
echo "<tr>\n";
echo "<td class=\"FacetFieldCaptionTD\">".$script_transl['date']."</td><td  class=\"FacetDataTD\">\n";
$gForm->CalendarPopup('this_date',$form['this_date_D'],$form['this_date_M'],$form['this_date_Y'],'FacetSelect',1);
echo "</tr>\n";
echo "<tr>\n";
echo "\t<td class=\"FacetFieldCaptionTD\" align=\"right\">".$script_transl['type']." </td><td  class=\"FacetDataTD\">\n";
$gForm->variousSelect('type',$script_transl['type_value'],$form['type'],'FacetSelect',0,'type');
echo "\t </td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "\t<td class=\"FacetFieldCaptionTD\">".$script_transl['proini']."</td>\n";
echo "\t<td class=\"FacetDataTD\">".$form['proini']." / ".$form['year_ini']."</td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "\t<td class=\"FacetFieldCaptionTD\">".$script_transl['profin']."</td>\n";
echo "\t<td class=\"FacetDataTD\"><input type=\"text\" name=\"profin\" value=\"".$form['profin']."\" align=\"right\" maxlength=\"9\" size=\"3\" /> / ".$form['year_fin']."</td>\n";
echo "</tr>\n";
echo "\t<tr class=\"FacetDataTD\">\n";
echo "\t<td class=\"FacetFieldCaptionTD\"><input type=\"submit\" name=\"return\" value=\"".
     $script_transl['return']."\"></td>\n";
echo '<td align="right"><input type="submit" name="preview" value="';
echo $script_transl['view'];
echo '">';
echo "\t </td>\n";
echo "\t </tr>\n";
echo "</table>\n";

//mostro l'anteprima
if (isset($_POST['preview'])) {
   $rs=getDocumentsAccounts($form['type'],$form['vat_section'],strftime("%Y%m%d",$uts_this_date),$form['profin']);
   echo "<div align=\"center\"><b>".$script_transl['preview']."</b></div>";
   echo "<table class=\"Tlarge\">";
   echo "<th class=\"FacetFieldCaptionTD\">".$script_transl['date_reg']."</th>
         <th class=\"FacetFieldCaptionTD\">".$script_transl['protoc']."</th>
         <th class=\"FacetFieldCaptionTD\">".$script_transl['doc_type']."</th>
         <th class=\"FacetFieldCaptionTD\">N.</th>
         <th class=\"FacetFieldCaptionTD\">".$script_transl['customer']."</th>
         <th class=\"FacetFieldCaptionTD\">".$script_transl['taxable']."</th>
         <th class=\"FacetFieldCaptionTD\">".$script_transl['vat']."</th>
         <th class=\"FacetFieldCaptionTD\">".$script_transl['tot']."</th>\n";
   foreach($rs as $k=>$v) {
         $tot=computeTot($v['vat']);
         //fine calcolo totali
         echo "<tr class=\"FacetDataTD\">
               <td align=\"center\">".gaz_format_date($v['tes']['datfat'])."</td>
               <td>".$v['tes']['protoc']."</td>
               <td>".$v['tes']['tipdoc']."</td>
               <td>".$v['tes']['numfat']."</td>
               <td>".$v['tes']['ragsoc']."</td>
               <td align=\"right\">".gaz_format_number($tot['taxable'])."</td>
               <td align=\"right\">".gaz_format_number($tot['vat'])."</td>
               <td align=\"right\">".gaz_format_number($tot['tot'])."</td>
               </tr>\n";
   }
   if (count($rs) > 0) {
      echo "\t<tr class=\"FacetFieldCaptionTD\">\n";
      echo '<td colspan="9" align="right"><input type="submit" name="submit" value="';
      echo $script_transl['submit'];
      echo '">';
      echo "\t </td>\n";
      echo "\t </tr>\n";
   } else {
      echo "\t<tr>\n";
      echo '<td colspan="9" align="center" class="FacetDataTDred">';
      echo $script_transl['errors'][1];
      echo "\t </td>\n";
      echo "\t </tr>\n";
                
   }
}
?>
</form>
</body>
</html>