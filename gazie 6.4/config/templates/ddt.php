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
require('template_scheda.php');

class DDT extends Template_con_scheda
{
    function setTesDoc()
    {
        $this->tesdoc = $this->docVars->tesdoc;
        $this->giorno = substr($this->tesdoc['datemi'],8,2);
        $this->mese = substr($this->tesdoc['datemi'],5,2);
        $this->anno = substr($this->tesdoc['datemi'],0,4);
        $this->nomemese = ucwords(strftime("%B", mktime (0,0,0,substr($this->tesdoc['datemi'],5,2),1,0)));
        $this->sconto = $this->tesdoc['sconto'];
        $this->trasporto = $this->tesdoc['traspo'];
        if ($this->tesdoc['tipdoc'] == 'DDR') {
            $descri='D.d.T. per Reso n.';
        } elseif ($this->tesdoc['tipdoc'] == 'DDL') {
            $descri='D.d.T. c/lavorazione n.';
        } else {
            $descri='Documento di Trasporto n.';
        }
        $this->tipdoc = $descri.$this->tesdoc['numdoc'].'/'.$this->tesdoc['seziva'].' del '.$this->giorno.' '.$this->nomemese.' '.$this->anno;
    }

    function newPage() {
        $this->AddPage();
        $this->SetFillColor(hexdec(substr($this->colore,0,2)),hexdec(substr($this->colore,2,2)),hexdec(substr($this->colore,4,2)));
        $this->SetFont('helvetica','',9);
        $this->Cell(30,6,'Codice',1,0,'L',1);
        $this->Cell(82,6,'Descrizione',1,0,'L',1);
        $this->Cell(10,6,'U.m.',1,0,'L',1);
        $this->Cell(30,6,'Quantità',1,0,'R',1);
        $this->Cell(25,6,'Prezzo',1,0,'R',1);
        $this->Cell(10,6,'%Sc.',1,1,'R',1);
    }

    function pageHeader()
    {
        $this->StartPageGroup();
        $this->newPage();
    }

    function compose()
    {
        $lines = $this->docVars->getRigo();
        while (list($key, $rigo) = each($lines)) {
            if ($this->GetY() >= 215) {
                $this->Cell(155,6,'','T',1);
                $this->SetFont('helvetica', '', 20);
                $this->SetY(225);
                $this->Cell(185,12,'>>> --- SEGUE SU PAGINA SUCCESSIVA --- >>> ',1,1,'R');
                $this->SetFont('helvetica', '', 9);
                $this->newPage();
                $this->Cell(185,5,'<<< --- SEGUE DA PAGINA PRECEDENTE --- <<< ',0,1);
            }
                if ($rigo['tiprig'] < 2) {
                    $this->Cell(30,6,$rigo['codart'],1,0,'L');
                    $this->Cell(82,6,$rigo['descri'],1,0,'L');
                    $this->Cell(10,6,$rigo['unimis'],1,0,'L');
                    $this->Cell(30,6,gaz_format_quantity($rigo['quanti'],1,$this->decimal_quantity),1,0,'R');
                    if ($this->docVars->client['stapre'] == 'S') {
                        $this->Cell(25,6,number_format($rigo['prelis'],$this->decimal_price,',',''),'TB',0,'R');
                        $this->Cell(10,6,$rigo['sconto'],1,1,'R');
                    } else {
                        $this->Cell(25,6);
                        $this->Cell(10,6,'','R',1);
                    }
                } elseif ($rigo['tiprig'] == 2) {
                   $this->Cell(30,6,'','L');
                   $this->Cell(122,6,$rigo['descri'],'LR');
                   $this->Cell(35,6,'','R',1);
                } elseif ($rigo['tiprig']==6 || $rigo['tiprig']==7) {
                    $this->writeHtmlCell(187,6,10,$this->GetY(),$rigo['descri'],1,1);
                } elseif ($rigo['tiprig'] == 11) {
                    $this->Cell(30,6,'','L');
                    $this->Cell(122,6,"CIG: " . $rigo['descri'],'LR',0,'L');
                    $this->Cell(35,6,'','R',1);
                } elseif ($rigo['tiprig'] == 12) {
                    $this->Cell(30,6,'','L');
                    $this->Cell(122,6,"CUP: " . $rigo['descri'],'LR',0,'L');
                    $this->Cell(35,6,'','R',1);
                } elseif ($rigo['tiprig'] == 13) {
                    $this->Cell(30,6,'','L');
                    $this->Cell(122,6,"IdDocumento: " . $rigo['descri'],'LR',0,'L');
                    $this->Cell(35,6,'','R',1);
                }                
       }
    }

    function pageFooter() {
        $y = $this->GetY();
        $this->Rect(10,$y,187,220-$y); //questa marca le linee dx e sx del documento
        $this->SetY(220);
        $this->SetFont('helvetica','',9);
        $this->Cell(83, 5,'Agente','LTR',0,'C',1);
        $this->Cell(26, 5,'Peso netto','LTR',0,'C',1);
        $this->Cell(26, 5,'Peso lordo','LTR',0,'C',1);
        $this->Cell(26, 5,'N.colli','LTR',0,'C',1);
        $this->Cell(26, 5,'Volume','LTR',1,'C',1);
        $this->Cell(83, 5,$this->agente,'LR');
        if ($this->tesdoc['net_weight'] > 0) {
            $this->Cell(26, 5,gaz_format_number($this->tesdoc['net_weight']),'LR',0,'C');
        } else {
            $this->Cell(26, 5,'','LR');
        }
        if ($this->tesdoc['gross_weight'] > 0) {
            $this->Cell(26, 5,gaz_format_number($this->tesdoc['gross_weight']),'LR',0,'C');
        } else {
            $this->Cell(26, 5,'','LR');
        }
        if ($this->tesdoc['units'] > 0) {
            $this->Cell(26, 5,$this->tesdoc['units'],'LR',0,'C');
        } else {
            $this->Cell(26, 5,'','LR');
        }
        if ($this->tesdoc['volume'] > 0) {
            $this->Cell(26, 5,gaz_format_number($this->tesdoc['volume']),'LR',1,'C');
        } else {
            $this->Cell(26, 5,'','LR',1);
        }
        $this->Cell(187,5,'Pagamento - Banca','LTR',1,'C',1);
        $this->Cell(187,5,$this->pagame['descri'].' '.$this->banapp['descri'],'LBR',1,'C');
        $this->Cell(51,5,'Spedizione','LTR',0,'C',1);
        $this->Cell(114,5,'Vettore','LTR',0,'C',1);
        $this->Cell(22,5,'Trasporto','LTR',1,'C',1);
        $this->Cell(51,5,$this->tesdoc['spediz'],'LBR',0,'C');
        $this->Cell(114,5,$this->docVars->vettor['ragione_sociale'].' '.
                          $this->docVars->vettor['indirizzo'].' '.
                          $this->docVars->vettor['citta'].' '.
                          $this->docVars->vettor['provincia'],'LBR',0,'C');
        if ($this->docVars->tesdoc['traspo'] == 0) {
            $ImportoTrasporto = "";
        } else {
            $ImportoTrasporto = gaz_format_number($this->docVars->tesdoc['traspo']);
        }
        $this->Cell(22,5,$ImportoTrasporto,'LBR',1,'C');
        $this->Cell(51,5,'Inizio trasporto','LTR',0,'C',1);
        $this->Cell(68,5,'Firma vettore','LTR',0,'C',1);
        $this->Cell(68,5,'Firma destinatario','LTR',1,'C',1);
        if ($this->day > 0) {
           $this->Cell(51,5,'data '.$this->day.'-'.$this->month.'-'.$this->year,'LR',0,'C');
        } else {
           $this->Cell(51,5,'      data','LR',0,'L');
        }
        $this->Cell(68,5,'','R',0);
        $this->Cell(68,5,'','R',1);
        $this->Cell(51,5,'ora '.$this->ora.':'.$this->min,'LRB',0,'C');
        $this->Cell(68,5,'','RB',0);
        $this->Cell(68,5,'','RB',1);
        if (!empty($this->docVars->vettor['ragione_sociale'])){
          $this->StartPageGroup();
          $this->appendix=true;
          $this->addPage();
          $this->SchedaTrasporto();
          $this->appendix=false;
        }
    }

    function Footer()
    {
        if(isset($this->appendix)){
          if ($this->appendix==false){
              // sull'appendice non stampo il footer
              unset($this->appendix);
          } else {
           $this->SetY(-20);
           $this->SetFont('helvetica','',8);
           $this->MultiCell(184,4,$this->intesta1.' '.$this->intesta2.' '.$this->intesta3.' '.$this->intesta4.' ',0,'C',0);
          }
        } else {
           $this->SetY(-20);
           $this->SetFont('helvetica','',8);
           $this->MultiCell(184,4,$this->intesta1.' '.$this->intesta2.' '.$this->intesta3.' '.$this->intesta4.' ',0,'C',0);
        }
    }
}
?>