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
if (!ini_get('safe_mode')){ //se me lo posso permettere...
    ini_set('memory_limit','128M');
    gaz_set_time_limit (0);
}

require("../../config/templates/report_template.php");
if (!isset($_GET['ci']) or
    !isset($_GET['cf']) or
    !isset($_GET['ai']) or
    !isset($_GET['af'])) {
    header("Location: select_deplia.php");
    exit;
}
if (empty($_GET['af'])) {
    $_GET['af'] = 'zzzzzzzzzzzzzzz';
}

$listino = 'preve1';
if (isset($_GET['li'])) {
   if (substr($_GET['li'],0,3) == '2') {
      $listino = 'preve2';
   } elseif (substr($_GET['li'],0,3) == '3') {
      $listino = 'preve3';
   } elseif (substr($_GET['li'],0,3) == 'web') {
      $listino = 'web_price';
   }
}

$luogo_data=$admin_aziend['citspe'].", lì ";
if (isset($_GET['ds'])) {
   $giosta = substr($_GET['ds'],0,2);
   $messta = substr($_GET['ds'],2,2);
   $annsta = substr($_GET['ds'],4,4);
   $utssta= mktime(0,0,0,$messta,$giosta,$annsta);
   $luogo_data .= ucwords(strftime("%d %B %Y",$utssta));
} else {
   $luogo_data .=ucwords(strftime("%d %B %Y", mktime (0,0,0,date("m"),date("d"),date("Y"))));
}

class Depliant extends  Report_template
{

function printItem($code,$description,$price='',$um='',$un=0,$note='',$image='',$barcode='',$link=false,$vat='')
{
   global $admin_aziend;
   $this->SetFillColor(hexdec(substr($this->colore,0,2)),hexdec(substr($this->colore,2,2)),hexdec(substr($this->colore,4,2)));
   $this->SetFont('helvetica','',9);
   if (floatval($price)<0.00001){
     $price='';
     $vat='';
     $um='';
   } else {
     $price = number_format($price,$admin_aziend['decimal_price'],',','.');
   }
   $x=$this->GetX();
   $y=$this->GetY();
   $lf=0;
   if (!empty($image)){
        if ($x > 20){
           $lf=1;
           $y -= 15;
           $x=103;
           $this->SetY($y);
           $this->SetX($x);
        }
        if (!$link) {
           $link='admin_artico.php?codice='.$code.'&Update';
        }
        $this->Image('@'.$image,$x+72,$y,20,20,'',$link);
        $this->Cell(93,5,$code,'LTR',2);
        $this->Cell(93,5,$description,'LR',2);
        if ($un > 0){
         $un .= ' N./Pack';
        } else {
         $un = '';
        }
        $this->Cell(93,5,$price.' '.$admin_aziend['symbol'].'/'.$um.' '.$vat.' '.$un,'LR',2);
        $this->Cell(73,5,$note,'LB',0,'R');
        $this->Cell(20,5,'','BR',$lf,'R');
   } elseif (!empty($barcode)) {
        if ($x > 20){
           $lf=1;
           $y-=15;
           $x=103;
           $this->SetY($y);
           $this->SetX($x);
        }
        $this->EAN13($x+40,$y+5,$barcode,7);
        $this->SetY($y);
        $this->SetX($x);
        $this->Cell(93,5,$code.' - '.$description,'LTR',2);
        $this->Cell(93,5,'','LR',2);
        $this->Cell(93,5,$price.' '.$admin_aziend['symbol'].'/'.$um,'LR',2);
        $this->Cell(93,5,$vat,'LBR',$lf);
   } else {
        if ($x > 20){
           $this->SetY($y+5);
           $this->SetX(10);
        }
        $this->SetX(10);
        $this->Cell(27,5,$code,1,0,'L');
        if (strlen(trim($description))>36) {
            $this->SetFont('helvetica','',8);
            $this->Cell(63,5,$description,1,0,'L');
            $this->SetFont('helvetica','',9);
        } else {
            $this->Cell(63,5,$description,1,0,'L');
        }
        $this->Cell(49,5,$price.' '.$admin_aziend['symbol'].'/'.$um.' '.$vat,1,0,'R');
        $this->SetFont('helvetica','',7);
        $this->Cell(47,5,$note,1,1,'R');
   }
}

function printGroupItem($code,$description,$image='',$link=false)
{
   $this->SetFillColor(hexdec(substr($this->colore,0,2)),hexdec(substr($this->colore,2,2)),hexdec(substr($this->colore,4,2)));
   $x=$this->GetX();
   $y=$this->GetY();
   $this->SetFont('helvetica','',10);
   if ($x > 20){
       $this->SetY($y+5);
       $this->SetX(10);
   }
   if (empty($image)){
        $this->Cell(120,6,'Cat.Merceologica: '.$code.' - '.$description,'T',1,'L',1);
   } else {
        if (!$link) {
           $link='admin_catmer.php?codice='.$code.'&Update';
        }
        $this->Image('@'.$image,$x+120,$y+1,0,19,'',$link);
        $this->Cell(120,20,'Cat.Merceologica: '.$code.' - '.$description,'T',1,'L',1);
   }
}

}

$title = array('luogo_data'=>$luogo_data,'title'=>'C A T A L O G O','hile'=>array());

$where = $gTables['catmer'].".codice BETWEEN ".intval($_GET['ci'])." AND ".intval($_GET['cf'])." AND ".
         $gTables['artico'].".codice BETWEEN '".substr($_GET['ai'],0,15)."' AND '".substr($_GET['af'],0,15)."'";
$result = gaz_dbi_dyn_query($gTables['artico'].".codice AS codart,".
                   $gTables['artico'].".descri AS desart,".
                   $gTables['artico'].".image AS imaart,".
                   $gTables['artico'].".catmer,".
                   $gTables['artico'].".unimis,".
                   $gTables['artico'].".barcode AS barcod,".
                   $gTables['artico'].".web_url AS linkart,".
                   $gTables['artico'].".web_mu,".
                   $gTables['artico'].".web_multiplier,".
                   $gTables['artico'].".$listino AS prezzo,".
                   $gTables['artico'].".annota AS annart,".
                   $gTables['artico'].".pack_units AS units,".
                   $gTables['catmer'].".descri AS descat,".
                   $gTables['catmer'].".image AS imacat,".
                   $gTables['catmer'].".codice AS codcat,".
                   $gTables['catmer'].".web_url AS linkcat,".
                   $gTables['aliiva'].".aliquo,".
                   $gTables['catmer'].".annota AS anncat ",
                   $gTables['artico']." LEFT JOIN ".$gTables['aliiva']." ON ".$gTables['artico'].".aliiva = ".$gTables['aliiva'].".codice ".
                   " LEFT JOIN ".$gTables['catmer']." ON ".$gTables['artico'].".catmer = ".$gTables['catmer'].".codice",
                   $where,
                   "codcat, codart");
$pdf = new Depliant();
$pdf->setVars($admin_aziend,$title);
$pdf->Open();
$pdf->SetTopMargin(32);
$pdf->setFooterMargin(10);
$pdf->AddPage();
$ctrl_cm = 0;
while ($row = gaz_dbi_fetch_array($result)) {
        if (intval($_GET['bc'])==1) { // per stampare i barcode in luogo delle immagini
           $row['imaart'] = '';
           $row['imacat'] = '';
        } else {
           $row['barcod'] = '';
        }
        $vat='+IVA '.floatval($row['aliquo']).'%';
        if ($listino=='web_price') {
            $price = $row['prezzo']*$row['web_multiplier'];
            $row['unimis']=$row['web_mu'];
        }  else {
            $price = $row['prezzo'];
        }
        if ($row['codcat'] <> $ctrl_cm) {
           if ($pdf->GetY()>250) {
              $pdf->AddPage();
           }
           $pdf->printGroupItem($row['codcat'],$row['descat'],$row['imacat'],$row['linkcat']);
        }
        if (!empty($row['imaart']) || !empty($row['barcod'])){
            if ($pdf->GetY()>235 && $pdf->GetX()>90) {
                $pdf->printItem($row['codart'],$row['desart'], $price,$row['unimis'],$row['units'],$row['annart'],$row['imaart'],substr($row['barcod'],0,13),$row['linkart'],$vat);
                $pdf->AddPage();
            } elseif ($pdf->GetY()>235) {
                $pdf->AddPage();
                $pdf->printItem($row['codart'],$row['desart'], $price,$row['unimis'],$row['units'],$row['annart'],$row['imaart'],substr($row['barcod'],0,13),$row['linkart'],$vat);
            } else {
                $pdf->printItem($row['codart'],$row['desart'], $price,$row['unimis'],$row['units'],$row['annart'],$row['imaart'],substr($row['barcod'],0,13),$row['linkart'],$vat);
            }
        } else {
            $pdf->printItem($row['codart'],$row['desart'], $price,$row['unimis'],$row['units'],$row['annart'],$row['imaart'],substr($row['barcod'],0,13),$row['linkart'],$vat);
        }
        $ctrl_cm = $row['codcat'];
}
$pdf->Output();
?>