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
// se l'utente non ha alcun registratore di cassa associato nella tabella cash_register non pu� emettere scontrini
$ecr_user = gaz_dbi_get_row($gTables['cash_register'],'adminid',$admin_aziend['Login']);
if (!$ecr_user){
    header("Location: error_msg.php?ref=admin_scontr");
    exit;
};

if (!isset($_POST['ritorno'])) {
    $form['ritorno'] = $_SERVER['HTTP_REFERER'];
} else {
    $form['ritorno'] = $_POST['ritorno'];
}

if (isset($_POST['return'])) {
    header("Location: ".$form['ritorno']);
    exit;
}


$gForm = new venditForm();
$ecr=$gForm->getECR_userData($admin_aziend['Login']);


function getLastProtoc($year,$seziva,$reg=4)
{
    global $gTables;
    $rs_last = gaz_dbi_dyn_query("protoc", $gTables['tesmov'], "YEAR(datreg) = ".intval($year)." AND regiva = ".intval($reg)." AND seziva = ".intval($seziva),'protoc DESC',0,1);
    $last = gaz_dbi_fetch_array($rs_last);
    $p = 1;
    if ($last) {
       $p = $last['protoc']+1;
    }
    return $p;
}

function getLastNumdoc($year,$seziva,$reg=4)
{
    global $gTables;
    $rs_last = gaz_dbi_dyn_query("numdoc", $gTables['tesmov'], "YEAR(datreg) = ".intval($year)." AND regiva = ".intval($reg)." AND seziva = ".intval($seziva),'protoc DESC',0,1);
    $last = gaz_dbi_fetch_array($rs_last);
    $p = 1;
    if ($last) {
       $p = $last['numdoc']+1;
    }
    return $p;
}

function getAccountedTickets($id_cash)
{
    global $gTables,$admin_aziend;
    $from =  $gTables['tesdoc'].' AS tesdoc
         LEFT JOIN '.$gTables['pagame'].' AS pay ON tesdoc.pagame=pay.codice
         LEFT JOIN '.$gTables['clfoco'].' AS customer ON tesdoc.clfoco=customer.codice
         LEFT JOIN '.$gTables['anagra'].' AS anagraf ON anagraf.id=customer.id_anagra';
    $where = "id_con = 0 AND id_contract = ".intval($id_cash)." AND tipdoc = 'VCO'";
    $orderby = "datemi ASC, numdoc ASC";
    $result = gaz_dbi_dyn_query('tesdoc.*,
                    pay.tippag,pay.numrat,pay.incaut,pay.id_bank,
                    customer.codice,
                    customer.speban AS addebitospese,
                    CONCAT(anagraf.ragso1,\' \',anagraf.ragso2) AS ragsoc,CONCAT(anagraf.citspe,\' (\',anagraf.prospe,\')\') AS citta',
                    $from,$where,$orderby);
    $doc['all']=array();
    $tot=0;
    while ($tes = gaz_dbi_fetch_array($result)) {
           $cast_vat=array();
           $cast_acc=array();
           $tot_tes=0;
           //recupero i dati righi per creare i castelletti
           $rs_rig = gaz_dbi_dyn_query("*", $gTables['rigdoc'], "id_tes = ".$tes['id_tes'],"id_rig");
           while ($v = gaz_dbi_fetch_array($rs_rig)) {
                 if ($v['tiprig'] <= 1) {    //ma solo se del tipo normale o forfait
                    if ($v['tiprig'] == 0) { // tipo normale
                       $tot_row = CalcolaImportoRigo($v['quanti'], $v['prelis'],array($v['sconto'],$tes['sconto'],-$v['pervat']));
                    } else {                 // tipo forfait
                       $tot_row = CalcolaImportoRigo(1,$v['prelis'],-$v['pervat']);
                    }
                    if (!isset($cast_vat[$v['codvat']])) {
                       $cast_vat[$v['codvat']]['totale']=0.00;
                       $cast_vat[$v['codvat']]['imponi']=0.00;
                       $cast_vat[$v['codvat']]['impost']=0.00;
                       $cast_vat[$v['codvat']]['periva']=$v['pervat'];
                    }
                    $cast_vat[$v['codvat']]['totale']+=$tot_row;
                    // calcolo il totale del rigo stornato dell'iva
                    $imprig=round($tot_row/(1+($v['pervat']/100)),2);
                    $cast_vat[$v['codvat']]['imponi']+=$imprig;
                    $cast_vat[$v['codvat']]['impost']+=$tot_row-$imprig;
                    $tot+=$tot_row;
                    $tot_tes+=$tot_row;
                    // inizio AVERE
                    if (!isset($cast_acc[$admin_aziend['ivacor']]['A'])) {
                        $cast_acc[$admin_aziend['ivacor']]['A']=0;
                    }
                    $cast_acc[$admin_aziend['ivacor']]['A']+=$tot_row-$imprig;
                    if (!isset($cast_acc[$v['codric']]['A'])) {
                        $cast_acc[$v['codric']]['A']=0;
                    }
                    $cast_acc[$v['codric']]['A']+=$imprig;
                    // inizio DARE
                    if ($tes['clfoco']>100000000) { // c'� un cliente selezionato
                        if (!isset($cast_acc[$tes['clfoco']]['D'])) {
                            $cast_acc[$tes['clfoco']]['D']=0;
                        }
                        $cast_acc[$tes['clfoco']]['D']+=$tot_row;
                      if ($tes['tippag']=='K') { // ha pagato con carta incasso direttamente sul CC/bancario
                           if (!isset($cast_acc[$tes['clfoco']]['A'])) {
                               $cast_acc[$tes['clfoco']]['A']=0;
                           }
                           $cast_acc[$tes['clfoco']]['A']+=$tot_row;
                           if (!isset($cast_acc[$tes['id_bank']]['D'])) {
                               $cast_acc[$tes['id_bank']]['D']=0;
                           }
                           $cast_acc[$tes['id_bank']]['D']+=$tot_row;
                        }else{
                                                                
                        if ($tes['incaut']=='S') { //  ha pagato contanti vado per cassa 
                        if (!isset($cast_acc[$tes['clfoco']]['A'])) {
                               $cast_acc[$tes['clfoco']]['A']=0;
                           }
                           $cast_acc[$tes['clfoco']]['A']+=$tot_row;
                           if (!isset($cast_acc[$admin_aziend['cassa_']]['D'])) {
                               $cast_acc[$admin_aziend['cassa_']]['D']=0;
                           }
                           $cast_acc[$admin_aziend['cassa_']]['D']+=$tot_row;
                        }
                       }
                    } else {  // il cliente � anonimo 
                        if ($tes['tippag']=='K'){ // paga con carta incasso direttamente sul CC/bancario
                        if (!isset($cast_acc[$tes['id_bank']]['D'])) {
                               $cast_acc[$tes['id_bank']]['D']=0;
                           }
                           $cast_acc[$tes['id_bank']]['D']+=$tot_row;
                         } else { //vado per cassa
                        if (!isset($cast_acc[$admin_aziend['cassa_']]['D'])) {
                            $cast_acc[$admin_aziend['cassa_']]['D']=0;
                        }
                        $cast_acc[$admin_aziend['cassa_']]['D']+=$tot_row;
                    }
                 }
              }
           }
           $doc['all'][]= array('tes'=>$tes,
                                    'vat'=>$cast_vat,
                                    'acc'=>$cast_acc,
                                    'tot'=>$tot_tes);
           if ($tes['clfoco']>100000000) {
                 $doc['invoice'][]= array('tes'=>$tes,
                                          'vat'=>$cast_vat,
                                          'acc'=>$cast_acc,
                                          'tot'=>$tot_tes);
           } else {
                 $doc['ticket'][]= array('tes'=>$tes,
                                         'vat'=>$cast_vat,
                                         'acc'=>$cast_acc,
                                         'tot'=>$tot_tes);
           }
    }
    $doc['tot']=$tot;
    return $doc;
}

if (isset($_POST['submit'])) {
            // INIZIO l'invio della richiesta al'ecr dell'utente
            require("../../library/cash_register/".$ecr['driver'].".php");
            $ticket_printer = new $ecr['driver'];
            $ticket_printer->set_serial($ecr['serial_port']);
            $ticket_printer->fiscal_report();
            // INIZIO contabilizzazione scontrini con fatture
            $rs=getAccountedTickets($ecr['id_cash']);
            if (count($rs['invoice']) > 0) {
                foreach($rs['invoice'] as $v) { //prima quelli con fattura allegata
                  $n_prot=getLastProtoc(substr($v['tes']['datemi'],0,4),$v['tes']['seziva']);
                  //inserisco la testata
                  $newValue=array('caucon'=>'VCO',
                           'descri'=>'SCONTRINO con Fattura n.'.$v['tes']['numfat'].' allegata',
                           'datreg'=>$v['tes']['datemi'],
                           'seziva'=>$v['tes']['seziva'],
                           'id_doc'=>$v['tes']['id_tes'],
                           'protoc'=>$n_prot,
                           'numdoc'=>$v['tes']['numdoc'],
                           'datdoc'=>$v['tes']['datemi'],
                           'clfoco'=>$v['tes']['clfoco'],
                           'regiva'=>4,
                           'operat'=>1
                           );
                  tesmovInsert($newValue);
                  $tes_id = gaz_dbi_last_id();
                  gaz_dbi_put_row($gTables['tesdoc'], 'id_tes' ,$v['tes']['id_tes'], 'id_con', $tes_id);
                  //inserisco i righi iva nel db
                  foreach($v['vat'] as $k=>$vv) {
                      $vat = gaz_dbi_get_row($gTables['aliiva'],'codice',$k);
                      //aggiungo i valori mancanti all'array
                      $vv['tipiva']=$vat['tipiva'];
                      $vv['codiva']=$k;
                      $vv['id_tes']=$tes_id;
                      rigmoiInsert($vv);
                  }
                  //inserisco i righi contabili nel db
                  foreach($v['acc'] as $acc_k=>$acc_v) {
                         foreach($acc_v as $da_k=>$da_v) {
                                 rigmocInsert(array('id_tes'=>$tes_id,'darave'=>$da_k,'codcon'=>$acc_k,'import'=>$da_v));
                         }
                  }
                }
            }
            if (count($rs['ticket']) > 0) {
                // poi gli scontrini senza fattura (anonimi)
                // ma in questo caso devo accumulare i valori per data

                // INIZIO accumulatore per data
                $cast_vat=array();
                $cast_acc=array();
                foreach($rs['ticket'] as $v) {
                       foreach($v['vat'] as $k=>$iva) { // accumulo l'iva

                           if (!isset($cast_vat[$v['tes']['datemi']][$k])) {
                               $cast_vat[$v['tes']['datemi']][$k]['totale']=0;
                               $cast_vat[$v['tes']['datemi']][$k]['imponi']=0;
                               $cast_vat[$v['tes']['datemi']][$k]['impost']=0;
                               $cast_vat[$v['tes']['datemi']][$k]['periva']=$iva['periva'];
                           }
                           $cast_vat[$v['tes']['datemi']][$k]['totale']+=$iva['totale'];
                           $cast_vat[$v['tes']['datemi']][$k]['imponi']+=$iva['imponi'];
                           $cast_vat[$v['tes']['datemi']][$k]['impost']+=$iva['impost'];
                       }
                       foreach($v['acc'] as $k=>$acc) {  // accumulo i conti
                           foreach($acc as $da_k=>$da_v) {
                                 if (!isset($cast_acc[$v['tes']['datemi']][$k][$da_k])) {
                                     $cast_acc[$v['tes']['datemi']][$k][$da_k]=0;
                                 }
                                 $cast_acc[$v['tes']['datemi']][$k][$da_k]+=$da_v;
                           }
                       }
                }
                // FINE accumulatore per data

                // INIZIO contabilizzazione scontrini anonimi
                foreach($cast_vat as $k=>$v) {
                  $n_prot=getLastProtoc(substr($k,0,4),$ecr['seziva']);
                  $n_docu=getLastNumdoc(substr($k,0,4),$ecr['seziva']);
                  //inserisco la testata
                  $newValue=array('caucon'=>'VCO',
                           'descri'=>'SCONTRINI '.$ecr['descri'],
                           'datreg'=>$k,
                           'seziva'=>$ecr['seziva'],
                           'id_doc'=>0,
                           'protoc'=>$n_prot,
                           'numdoc'=>$n_docu,
                           'datdoc'=>$k,
                           'clfoco'=>0,
                           'regiva'=>4,
                           'operat'=>1
                           );
                  tesmovInsert($newValue);
                  $tes_id = gaz_dbi_last_id();
                  tableUpdate('tesdoc',
                              array('id_con'),
                              array('id_contract', $ecr['id_cash'].'\' AND datemi = \''.substr($k,0,4).substr($k,5,2).substr($k,8,2)),
                              array('id_con'=>$tes_id)
                              );
                  //inserisco i righi iva nel db
                  foreach($cast_vat[$k] as $key=>$vv) {
                      $vat = gaz_dbi_get_row($gTables['aliiva'],'codice',$key);
                      //aggiungo i valori mancanti all'array
                      $vv['tipiva']=$vat['tipiva'];
                      $vv['codiva']=$key;
                      $vv['id_tes']=$tes_id;
                      rigmoiInsert($vv);
                  }
                  //inserisco i righi contabili nel db
                  foreach($cast_acc[$k] as $acc_k=>$acc_v) {
                         foreach($acc_v as $da_k=>$da_v) {
                                 rigmocInsert(array('id_tes'=>$tes_id,'darave'=>$da_k,'codcon'=>$acc_k,'import'=>$da_v));
                         }
                  }
                }
            }
            header("Location: report_scontr.php");
            exit;
}

require("../../library/include/header.php");
$script_transl=HeadMain(0);
echo "<form method=\"POST\" name=\"accounting\">\n";
echo "<input type=\"hidden\" value=\"".$form['ritorno']."\" name=\"ritorno\" />\n";
echo "<div align=\"center\" class=\"FacetFormHeaderFont\">".$script_transl['title1'].$ecr['descri'].$script_transl['title2']."</div>\n";
$rs=getAccountedTickets($ecr['id_cash']);
echo "<table class=\"Tlarge\">";
echo "<th class=\"FacetFieldCaptionTD\">".$script_transl['date']."</th>
      <th class=\"FacetFieldCaptionTD\">".$script_transl['num']."</th>
      <th class=\"FacetFieldCaptionTD\">".$script_transl['sez']."</th>
      <th class=\"FacetFieldCaptionTD\">".$script_transl['customer']."</th>
      <th class=\"FacetFieldCaptionTD\">".$script_transl['importo']."</th>";
if (count($rs['all']) > 0) {
   foreach($rs['all'] as $k=>$v) {
      if ($v['tes']['clfoco']<100000000){
        $v['tes']['ragsoc']= $script_transl['anony'];
      }
      echo "<tr class=\"FacetDataTD\">
            <td align=\"center\">".gaz_format_date($v['tes']['datemi'])."</td>
            <td align=\"center\">".$v['tes']['numdoc']."</td>
            <td align=\"center\">".$v['tes']['seziva']."</td>
            <td>".$v['tes']['ragsoc'].$v['tes']['citta']."</td>
            <td align=\"right\">".gaz_format_number($v['tot'])."</td>
            </tr>\n";
   }
   echo "<tr class=\"FacetFieldCaptionTD\">\n";
   echo '<td colspan="4" align="right"><input type="submit" name="submit" value="';
   echo $script_transl['submit'];
   echo '">';
   echo "</td>\n";
   echo '<td align="right" style="font-weight=bolt;">';
   echo gaz_format_number($rs['tot']);
   echo "\t </td>\n";
   echo "</tr>\n";
} else {
   echo "\t<tr>\n";
   echo '<td colspan="3" align="center" class="FacetDataTDred">';
   echo $script_transl['message'];
   echo "\t </td>\n";
   echo '<td colspan="2" align="center" class="FacetDataTDred">';
   echo "<input type=\"submit\" name=\"return\" value=\"".$script_transl['return']."\" />\n";
   echo "\t </td>\n";
   echo "\t </tr>\n";
}
?>
</form>
</body>
</html>