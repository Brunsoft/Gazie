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

if (!isset($_GET['ri']) or
    !isset($_GET['rf'])) {
    header("Location: select_giomag.php");
    exit;
}

function getMovements($date_ini,$date_fin)
    {
        global $gTables,$admin_aziend;
        $m=array();
        $where="datreg BETWEEN $date_ini AND $date_fin";
        $what=$gTables['movmag'].".*, ".
              $gTables['caumag'].".codice, ".$gTables['caumag'].".descri, ".
              $gTables['artico'].".codice, ".$gTables['artico'].".descri AS desart, ".$gTables['artico'].".unimis, ".$gTables['artico'].".scorta, ".$gTables['artico'].".catmer ";
        $table=$gTables['movmag']." LEFT JOIN ".$gTables['caumag']." ON (".$gTables['movmag'].".caumag = ".$gTables['caumag'].".codice)
               LEFT JOIN ".$gTables['artico']." ON (".$gTables['movmag'].".artico = ".$gTables['artico'].".codice)";
        $rs=gaz_dbi_dyn_query ($what,$table,$where, 'datreg ASC, clfoco ASC');
        while ($r = gaz_dbi_fetch_array($rs)) {
            $m[] = $r;
        }
        return $m;
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

$giori = substr($_GET['ri'],0,2);
$mesri = substr($_GET['ri'],2,2);
$annri = substr($_GET['ri'],4,4);
$utsri= mktime(0,0,0,$mesri,$giori,$annri);
$giorf = substr($_GET['rf'],0,2);
$mesrf = substr($_GET['rf'],2,2);
$annrf = substr($_GET['rf'],4,4);
$utsrf= mktime(0,0,0,$mesrf,$giorf,$annrf);

$result=getMovements(strftime("%Y%m%d",$utsri),strftime("%Y%m%d",$utsrf));

require("../../config/templates/report_template.php");
$title = array('luogo_data'=>$luogo_data,
               'title'=>"GIORNALE DI MAGAZZINO dal ".strftime("%d %B %Y",$utsri)." al ".strftime("%d %B %Y",$utsrf),
               'hile'=>array(array('lun' => 20,'nam'=>'Data Reg.'),
                             array('lun' => 40,'nam'=>'Causale'),
                             array('lun' => 70,'nam'=>'Articolo'),
                             array('lun' => 70,'nam'=>'Rif.Documento'),
                             array('lun' => 17,'nam'=>'Prezzo'),
                             array('lun' => 18,'nam'=>'Importo'),
                             array('lun' => 10,'nam'=>'U.M.'),
                             array('lun' => 20,'nam'=>'Mov.Quant.')
                            )
              );

$pdf = new Report_template();
$pdf->setVars($admin_aziend,$title);
$pdf->SetTopMargin(39);
$pdf->SetFooterMargin(20);
$config = new Config;
$pdf->AddPage('L',$config->getValue('page_format'));
$pdf->SetFont('helvetica','',7);
if (sizeof($result) > 0) {
  while (list($key, $row) = each($result)) {
      $datadoc = substr($row['datdoc'],8,2).'-'.substr($row['datdoc'],5,2).'-'.substr($row['datdoc'],0,4);
      $datareg = substr($row['datreg'],8,2).'-'.substr($row['datreg'],5,2).'-'.substr($row['datreg'],0,4);
      $movQuanti = $row['quanti']*$row['operat'];
      $pdf->Cell(20,3,$datareg,1,0,'C');
      $pdf->Cell(40,3,$row['caumag'].'-'.substr($row['descri'],0,22),1);
      $pdf->Cell(70,3,$row['artico'].' - '.$row['desart'],1);
      $pdf->Cell(70,3,$row['desdoc'].' del '.$datadoc,1);
      $pdf->Cell(17,3,number_format($row['prezzo'],$admin_aziend['decimal_price'],',','.'),1,0,'R');
      $pdf->Cell(18,3,gaz_format_number(CalcolaImportoRigo($row['quanti'],$row['prezzo'],array($row['scochi'],$row['scorig']))),1,0,'R');
      $pdf->Cell(10,3,$row['unimis'],1,0,'C');
      $pdf->Cell(20,3,gaz_format_quantity($movQuanti,1,$admin_aziend['decimal_quantity']),1,1,'R');
  }
}
$pdf->Output();
?>