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
require("./lang.".$admin_aziend['lang'].".php");
$script_transl = $strScript["stampa_liqiva.php"];

if (!isset($_GET['ds']) ||
    !isset($_GET['pi']) ||
    !isset($_GET['sd']) ||
    !isset($_GET['cv']) ||
    !isset($_GET['cr']) ||
    !isset($_GET['ri']) ||
    !isset($_GET['rf'])) {
    header("Location: ".$_SERVER['HTTP_REFERER']);
    exit;
}

$gioini = substr($_GET['ri'],0,2);
$mesini = substr($_GET['ri'],2,2);
$annini = substr($_GET['ri'],4,4);
$datainizio = date("Ymd",mktime(0,0,0,$mesini,$gioini,$annini));
$giofin = substr($_GET['rf'],0,2);
$mesfin = substr($_GET['rf'],2,2);
$annfin = substr($_GET['rf'],4,4);
$datafine = date("Ymd",mktime(0,0,0,$mesfin,$giofin,$annfin));
$title = $script_transl['title'].' '.$_GET['ds'];
$cover_descri = $script_transl['cover_descri']."\n$annini";

//recupero tutti i movimenti iva del periodo
$sqlquery= "SELECT seziva,regiva,codiva,aliquo,".$gTables['aliiva'].".tipiva,".$gTables['aliiva'].".descri,
       SUM((imponi*(operat = 1) - imponi*(operat = 2))*(-2*(regiva > 5)+1)) AS imponibile,
       SUM((impost*(operat = 1) - impost*(operat = 2))*(-2*(regiva > 5)+1)) AS imposta
       FROM ".$gTables['rigmoi']."
       LEFT JOIN ".$gTables['tesmov']." ON ".$gTables['rigmoi'].".id_tes = ".$gTables['tesmov'].".id_tes
       LEFT JOIN ".$gTables['aliiva']." ON ".$gTables['rigmoi'].".codiva = ".$gTables['aliiva'].".codice
       WHERE datreg BETWEEN $datainizio and $datafine
       GROUP BY seziva,regiva,codiva
       ORDER BY seziva,regiva,aliquo DESC";
$result = gaz_dbi_query($sqlquery);
$topCarry = array(array('lenght' => 118,'name'=>'da riporto : ','frame' => 'B','fill'=>0,'font'=>8),
                  array('lenght' => 20,'name'=>'','frame' => 1,'fill'=>1),
                  array('lenght' => 32,'name'=>'','frame' => 1,'fill'=>1),
                  array('lenght' => 20,'name'=>'','frame' => 1,'fill'=>1));
$botCarry = array(array('lenght' => 118,'name'=>'a riporto : ','frame' => 'T','fill'=>0,'font'=>8),
                  array('lenght' => 20,'name'=>'','frame' => 1,'fill'=>1),
                  array('lenght' => 32,'name'=>'','frame' => 1,'fill'=>1),
                  array('lenght' => 20,'name'=>'','frame' => 1,'fill'=>1));


require("../../config/templates/standard_template.php");

$pdf = new Standard_template();
$n_page = intval($_GET['pi']);
if ($_GET['cv']=='cover') {
   $n_page--;
}
$pdf->setVars($admin_aziend,$title,0,array('ini_page'=>$n_page,'year'=>$script_transl['page'].' '.$annfin));
if ($_GET['cv']=='cover') {
   $pdf->setCover($cover_descri);
   $pdf->AddPage();
}
$pdf->AddPage();
$pdf->setFooterMargin(21);
$pdf->setTopMargin(44);

$ctrl_sezione = 0;
$ctrl_registro = 0;
$pdf->SetFont('helvetica','',10);
$totale_iva_registro = 0.00;
$saldo_periodo = 0.00;
while ($row = gaz_dbi_fetch_array($result)) {
      if ($ctrl_sezione != $row['seziva']) {
         if ($ctrl_registro != 0) {
             $pdf->Cell(114,6,$script_transl['t_reg'],0,0,'R');
             $pdf->Cell(20,6,gaz_format_number($totale_iva_registro),1,1,'R',1);
         }
         $pdf->Ln(1);
         $ctrl_registro = 0;
         $pdf->Cell(42,6);
         $pdf->Cell(93,6,strtoupper($script_transl['sez']).' '.$row['seziva']." - ".$admin_aziend["desez".$row['seziva']],1,1,'C',1);
         $ctrl_sezione = $row['seziva'];
      }
      if ($ctrl_registro != $row['regiva']) {
         if ($ctrl_registro != 0) {
             $pdf->Cell(114,6,$script_transl['t_reg'],0,0,'R');
             $pdf->Cell(20,6,gaz_format_number($totale_iva_registro),1,1,'R',1);
         }
         $pdf->Cell(70,6,$script_transl['regiva_value'][$row['regiva']],1,1,'L',1);
         $pdf->SetFont('helvetica','',8);
         $pdf->Cell(10,4,$script_transl['code'],1,0,'C');
         $pdf->Cell(60,4,$script_transl['descri'],1,0,'C');
         $pdf->Cell(29,4,$script_transl['imp'],1,0,'C');
         $pdf->Cell(15,4,$script_transl['rate'],1,0,'C');
         $pdf->Cell(20,4,$script_transl['iva'],1,0,'C');
         $pdf->Cell(20,4,$script_transl['isp'],1,0,'C');
         $pdf->Cell(20,4,$script_transl['ind'],1,0,'C');
         $pdf->Cell(20,4,$script_transl['tot'],1,1,'R');
         $pdf->SetFont('helvetica','',10);
         $totale_iva_registro = 0.00;
         $ctrl_registro = $row['regiva'];
      }
      $pdf->Cell(10,6,$row['codiva'],1,0,'C');
      $pdf->Cell(60,6,$row['descri'],1,0,'C');
      $pdf->Cell(29,6,gaz_format_number($row['imponibile']),1,0,'R');
      $pdf->Cell(15,6,floatval($row['aliquo']),1,0,'C');
      if ($row['tipiva']=='D') { // indetraibile
         $row['isp']=0;
         $row['ind']=$row['imposta'];
         $row['imposta']=0;
      } elseif ($row['tipiva']=='T') {  // split payment PA
         $row['isp']=$row['imposta'];
         $row['ind']=0;
         $row['imposta']=0;
      } else { // normale
         $row['isp']=0;
         $row['ind']=0;
         $totale_iva_registro += $row['imposta'];
         $saldo_periodo += $row['imposta'];
      }
      $pdf->Cell(20,6,gaz_format_number($row['imposta']),1,0,'R');
      $pdf->Cell(20,6,gaz_format_number($row['isp']),1,0,'R');
      $pdf->Cell(20,6,gaz_format_number($row['ind']),1,0,'R');
      $pdf->Cell(20,6,gaz_format_number($row['imponibile']+$row['imposta']+$row['isp']),1,1,'R');
}
$pdf->Cell(114,6,$script_transl['t_reg'],0,0,'R');
$pdf->Cell(20,6,gaz_format_number($totale_iva_registro),1,1,'R',1);
$pdf->Ln(2);

// totale periodo
$pdf->Cell(54,6);
if ($saldo_periodo < 0) {
    $pdf->Cell(55,6,strtoupper($script_transl['tot'].' '.$script_transl['t_neg']),'LTB',0,'L',1);
} else {
    $pdf->Cell(55,6,strtoupper($script_transl['tot'].' '.$script_transl['t_pos']),'LTB',0,'L',1);
}    
$pdf->Cell(5,6,$admin_aziend['symbol'],'TB',0,'L',1);
$pdf->Cell(20,6,gaz_format_number($saldo_periodo),'RTB',1,'R',1);

// credito riportato dal periodo precedente
if ($_GET['cr']>0) {
    $pdf->Cell(54,6);
    $pdf->Cell(55,6,$script_transl['carry'],'LTB',0,'L');
    $pdf->Cell(5,6,$admin_aziend['symbol'],'TB',0,'L');
    $pdf->Cell(20,6,'-'.gaz_format_number($_GET['cr']),'RTB',1,'R');
}

$saldo_totale = $saldo_periodo - floatval($_GET['cr']);

// calcolo interessi su iva trimestrale da versare
if ($saldo_totale>0 && $admin_aziend['ivam_t'] == 'T') {
    $interessi=0;
    $interessi=round($saldo_totale*$admin_aziend['interessi']/100,2);
    $pdf->Cell(114,6,$script_transl['inter'].$admin_aziend['interessi'].'% ',0,0,'R');
    $pdf->Cell(20,6,gaz_format_number($interessi),1,1,'R');
    $saldo_totale += $interessi;
} 

// totale
$pdf->Ln(2);
$pdf->Cell(31,6);
$pdf->SetFont('helvetica','B',12);
$pdf->Cell(72,6,strtoupper($script_transl['tot'].$script_transl['pay']),'LTB',0,'L',1);
$pdf->Cell(5,6,$admin_aziend['symbol'],'TB',0,'L',1);
$pdf->Cell(26,6,gaz_format_number($saldo_totale),'RTB',1,'R',1);

// dati versamento
if ($saldo_totale > 0) {
    $pdf->SetFont('helvetica','',8);
    $pdf->Ln(6);
    $pdf->Cell(50,6,$script_transl['pay_date'],0,0,'L');
    $pdf->Cell(60,6,$script_transl['co'],0,0,'L');
    $pdf->Cell(40,6,$script_transl['abi'],0,0,'L');
    $pdf->Cell(40,6,$script_transl['cab'],0,1,'L');
}

if ($_GET['sd']=='sta_def') {
    gaz_dbi_put_row($gTables['aziend'],'codice',1,'upgrie', $pdf->getGroupPageNo()+$n_page-1);
}
$pdf->Output($title.'.pdf');
?>
