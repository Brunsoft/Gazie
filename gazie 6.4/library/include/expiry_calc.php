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

class Expiry {

  public function CalcExpiry($amount,$date,$effect='D',$start_day=0,$expiry_number=1,$periodicity='M',$foll_month=0,$fix_day=0)
  {
    /* Questa funzione serve per il calcolo delle scadenze.
       Restituisce una matrice (array) bidimensionale dove
       sull'indice 'amount' c'� l'importo e
       sull'indice 'date' c'� la data di scadenza.
       $effect pu� avere i valori D,G,F
       $start_day sono i giorni di decorrenza della prima scadenza
       $expiry_number � il numero di rate
       $periodicity  � la periodicit� in mesi
       $foll_month � il mese da saltare (es.agosto)
       $fix_day � il giorno fisso di scadenza quando $effect vale 'G'
    */
    // definisco le variabili comuni
    $this->year=intval(substr($date,0,4));
    $this->month=intval(substr($date,5,2));
    $this->day=intval(substr($date,8,2));
    $this->effect=strtoupper(substr($effect,0,1));
    $this->start_day=intval($start_day);
    $this->expiry_number=intval($expiry_number);
    $this->periodicity=strtoupper(substr($periodicity,0,1));
    $this->foll_month=intval($foll_month);
    $this->fix_day=intval($fix_day);

    // variabili di flusso
    $this->ctrl_day=$this->day;
    $this->ctrl_month=$this->month;
    $this->ctrl_year=$this->year;

    // main
    $this->expiry=array();
    $partial=0.00;
    for ($c=1; $c<=$this->expiry_number; $c++) {
       if ($c==$this->expiry_number && $this->expiry_number==1) { // rata unica
           $this->expiry[$c]['amount']= number_format($amount,2,'.','');
       } elseif ($c==$this->expiry_number && $this->expiry_number>1) { // ultima rata
           $this->expiry[$c]['amount']= number_format(($amount-$partial),2,'.','');
       } elseif ($c<$this->expiry_number && $this->expiry_number>1) { // rate intermedie e prima
           $this->expiry[$c]['amount']= number_format(($amount/$this->expiry_number),2,'.','');
       }
       $partial+=$this->expiry[$c]['amount'];
       // chiamo la funzione per il calcolo della data
       $this->expiry[$c]['date']= $this->_Date($c);
    }
    // end main
    return $this->expiry;
  }

  // calcolo date
  protected function _Date($c) {
           if ($c==1) { // alla prima scadenza si devono aggiungere i giorni di decorrenza
              switch($this->effect) {
                     case "D": //caso in cui la scadenza fa riferimento alla data della fattura
                        $uts = mktime(0,0,0,$this->month,$this->day+$this->start_day,$this->year);
                        $this->ctrl_day = strftime("%d",$uts);
                        $this->ctrl_month = strftime("%m",$uts);
                        $this->ctrl_year = strftime("%Y",$uts);
                     break;
                     case "G": //caso in cui la scadenza fa riferimento ad un giorno fisso impostato sul relativo campo
                        $uts = mktime(0,0,0,$this->month,$this->day+$this->start_day,$this->year);
                        $this->ctrl_day = strftime("%d",$uts);
                        $this->ctrl_month = strftime("%m",$uts);
                        $this->ctrl_year = strftime("%Y",$uts);
                        if ($this->fix_day<$this->ctrl_day) { // salto un mese se va a cadere in un giorno precedente
                            $this->ctrl_month++;
                        }
                        $uts = mktime(0,0,0,$this->ctrl_month,$this->fix_day,$this->ctrl_year);
                        $this->ctrl_day = strftime("%d",$uts);
                        $this->ctrl_month = strftime("%m",$uts);
                        $this->ctrl_year = strftime("%Y",$uts);
                     break;
                     case "F": //caso in cui la scadenza deve far riferimento al fine mese rispetto alla data della fattura
                        // prima porto il riferimento al primo finemese
                        $uts = mktime(0,0,0,$this->ctrl_month+1,0,$this->ctrl_year);
                        $this->ctrl_day = strftime("%d",$uts);
                        $this->ctrl_month = strftime("%m",$uts);
                        $this->ctrl_year = strftime("%Y",$uts);
                        // poi aumento dei giorni di decorrenza (-2 per compensare febbraio)
                        $uts = mktime(0,0,0,$this->ctrl_month,$this->ctrl_day+$this->start_day-2,$this->ctrl_year);
                        $this->ctrl_month = strftime("%m",$uts);
                        $this->ctrl_year = strftime("%Y",$uts);
                        // quindi riporto a fine mese
                        $uts = mktime(0,0,0,$this->ctrl_month+1,0,$this->ctrl_year);
                        $this->ctrl_day = strftime("%d",$uts);
                        $this->ctrl_month = strftime("%m",$uts);
                        $this->ctrl_year = strftime("%Y",$uts);
                        if ($this->fix_day>0){   // eventualmente vado al giorno successivo
                           $uts = mktime(0,0,0,$this->ctrl_month+1,$this->fix_day,$this->ctrl_year);
                           $this->ctrl_day = strftime("%d",$uts);
                           $this->ctrl_month = strftime("%m",$uts);
                           $this->ctrl_year = strftime("%Y",$uts);
                        }
                     break;
              }
              if ($this->foll_month>0 && $this->foll_month==$this->ctrl_month) {
                 $this->ctrl_month++;
              }
           } else {  // le scadenze successive
              switch($this->periodicity) {
                case "Q":  // quindicinale
                    if ($c%2==0) {
                        $n_m=0;
                        $this->ctrl_day += 15;
                    } else {
                        $n_m=1;
                        $this->ctrl_day -= 15;
                    }
                break;
                case "M":$n_m= 1;break;  // mensile
                case "B":$n_m= 2;break;  // bimestrale
                case "T":$n_m= 3;break;  // trimestrale
                case "U":$n_m= 4;break;  // quadrimestrale
                case "E":$n_m= 6;break;  // semestrale
                case "A":$n_m=12;break;  // annuale
              }
              $this->ctrl_month+=$n_m;
              if ($this->effect=='F' && $this->periodicity!='Q' && $this->fix_day==0) { // quando si deve andare a fine mese
                    $this->ctrl_day=0;
                    $this->ctrl_month++;
              }
              $uts = mktime(0,0,0,$this->ctrl_month,$this->ctrl_day,$this->ctrl_year);
              $this->ctrl_day = strftime("%d",$uts);
              $this->ctrl_month = strftime("%m",$uts);
              $this->ctrl_year = strftime("%Y",$uts);
              if ($this->foll_month>0 && $this->foll_month==$this->ctrl_month) {
                 $this->ctrl_month++;
              }
           }
           $uts = mktime(0,0,0,$this->ctrl_month,$this->ctrl_day,$this->ctrl_year);
           $this->ctrl_day = strftime("%d",$uts);
           $this->ctrl_month = strftime("%m",$uts);
           $this->ctrl_year = strftime("%Y",$uts);
           return strftime("%Y-%m-%d",mktime(0,0,0,$this->ctrl_month,$this->ctrl_day,$this->ctrl_year));
  }
  // fine calcolo date
}
?>