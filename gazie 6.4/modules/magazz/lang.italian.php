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

$strScript = array ("browse_document.php" =>
                   array('title'=>"Lista dei Documenti/Certificati",
                         'errors'=>array('Il formato del file non è stato accettato!',
                                         'Il file è troppo grande!',
                                         'Il file è vuoto!',
                                         'Nessun file selezionato'),
                         'ins_this'=>"Inserisci un Documento e/o Certificato",
                         'upd_this'=>"Modifica Documento e/o Certificato",
                         'item'=>"Articolo di riferimento",
                         'table_name_ref'=>"Tabella di riferimento",
                         'note'=>"Didascalia/Appunti/Note",
                         'ext'=>"Estensione",
                         'select'=>"Sel.",
                         'code'=>"Codice"),
                    "report_statis.php" =>
                   array(  "statistica ",
                           "vendite",
                           "acquisti",
                           "anno",
                           "ordinato per ",
                           " da: ",
                           " a: ",
                           "Ultimo acquisto il ",
                           "Ultima vendita il ",
                           " dell'anno ",
                           " Categoria merc. ",
                           " Quantit&agrave; ",
                           " Valore in ".$admin_aziend['curr_name'],
                           " Fuori "),
                    "report_movmag.php" =>
                   array(  "movimenti di magazzino ",
                           "codice",
                           "Inserisci ",
                           "Lista dei ",
                           "Data reg.",
                           "Articolo",
                           "Quantit&agrave;",
                           "Importo",
                           "Documento",
                           " del ",
                           "Genera movimenti da documenti"),
                    "admin_movmag.php" =>
                   array(  "movimento di magazzino ",
                           "Data della registrazione ",
                           "Causale ",
                           "Cliente",
                           "Fornitore",
                           "C",
                           "F",
                           "Articolo",
                           "Data documento ",
                           "Descrizione doc.",
                           "Sconto chiusura ",
                           "Unit&agrave; di misura ",
                           "Quantit&agrave; ",
                           "Prezzo",
                           "Sconto su rigo",
                           "La data del documento non &egrave; corretta!",
                           "La data di registrazione non &egrave; corretta!",
                           "La data del documento &egrave; successiva a quella di registazione!",
                           "Non &egrave; stato selezionato l'articolo!",
                           "La quantit&agrave; non pu&ograve; essere uguale a zero!",
                           'operat'=>'Operazione',
                           'operat_value'=>array(-1=>"Scarico",0=>"Non opera",1=>"Carico"),
                           'partner'=>'Cliente/Fornitore',
                           'del_this'=>'Elimina il movimento di magazzino',
                           'amount'=>" Valore in ".$admin_aziend['curr_name'],
                            ),
                    "report_caumag.php" =>
                   array(  "causali di magazzino ",
                           "Lista delle "
                           ),
                    "admin_catmer.php" =>
                   array(  "categoria merceologica ",
                           "Numero ",
                           "Descrizione ",
                           "Immagine (jpg,png,gif) max 10kb: ",
                           "% di ricarico ",
                           "Annotazioni ",
                           "codice gi&agrave; esistente!",
                           "la descrizione &egrave; vuota!",
                           "Il file immagine dev'essere nel formato PNG",
                           "L'immagine non dev'essere pi&ugrave; grande di 10 kb",
                           'web_url'=>'Web url<br />(es: http://site.com/group.html)'
                           ),
                    "admin_caumag.php" =>
                   array(  "causale di magazzino ",
                           "Codice ",
                           "Descrizione ",
                           "Dati documento ",
                           "Operazione",
                           "Aggiorna Esistenza",
                           "No",
                           "Si",
                           "Scarico",
                           "Non opera",
                           "Carico",
                           "Cliente/Fornitore",
                           "Cliente",
                           "Entrambi",
                           "Fornitore",
                           "codice gi&agrave; esistente!",
                           "la descrizione &egrave; vuota!",
                           "il codice dev'essere un numero minore di 99"
                           ),
                    "genera_movmag.php" =>
                   array(  "Genera movimenti di magazzino da documenti",
                           "Data inizio ",
                           "Data fine",
                           "Azienda senza obbligo di magazzino fiscale!",
                           " successiva alla ",
                           "  righi sono da traferire in magazzino:",
                           " Non ci sono righi da trasferire in magazzino!"),
                    "select_giomag.php" =>
                    array( 0=>'Stampa giornale di magazzino',
                           'title'=>'Selezione per la visualizzazzione e/o la stampa del giornale di magazzino',
                           'errors'=>array('La data  non &egrave; corretta!',
                                           'La data di inizio dei movimenti contabili da stampare non pu&ograve; essere successiva alla data dell\'ultimo !',
                                           'La data di stampa non pu&ograve; essere precedente a quella dell\'ultimo movimento!'
                                          ),
                           'date'=>'Data di stampa ',
                           'date_ini'=>'Data registrazione inizio  ',
                           'date_fin'=>'Data registrazione fine ',
                           'header'=>array('Data'=>'','Causale'=>'','Descrizione documento'=>'',
                                            'Prezzo'=>'','Importo'=>'','UM' =>'','Quantit&agrave;'=>''
                                           )
                           ),
                    "recalc_exist_value.php" =>
                   array(  "Rivalutazione esistenza articoli da movimenti di magazzino",
                           "Anno di riferimento",
                           "Metodo di rivalutazione, scelto in configurazione azienda",
                           "Sono stati movimentati i seguenti",
                           "articoli durante il ",
                           "Movimenti",
                           "Codice",
                           "Descrizione",
                           "Esistenza",
                           "UM acq.",
                           "Valore precedente",
                           "Valore rivalutato",
                           "NON RIVALUTATO vedi nota ",
                           "(1) perch&egrave; ci sono degli acquisti negli anni successivi al ",
                           "(2) perch&egrave; non ci sono movimenti di acquisto nel ",
                           "Non ci sono articoli movimentati!"),
                    "inventory_stock.php" =>
                   array(  'title'=>"Inventario fisico di magazzino",
                           'del'=>"del",
                           'catmer'=>"Categoria Merceologica ",
                           'select'=>"Sel.",
                           'code'=>"Codice",
                           'descri'=>"Descrizione articolo",
                           'mu'=>"U.M.",
                           'load'=>"Carico",
                           'unload'=>"Scarico",
                           'value'=>"Nuovo valore giacenza",
                           'v_a'=>"Valore attuale",
                           'v_r'=>"Valore reale",
                           'g_a'=>"Giacenza attuale",
                           'g_r'=>"Giacenza reale",
                           'g_v'=>"Valore giacenza",
                           'noitem'=>"Non sono stati trovati articoli in questa categoria merceologica",
                           'errors'=>array(" La giacenza reale non pu&ograve; essere negativa",
                                           " Il valore reale non pu&ograve; essere negativo o uguale a zero",
                                           " Si st&agrave; tentando di fare l'inventario con giacenza attuale e reale entrambe a zero"),
                           'preview_title'=>'Confermando le scelte fatte si registreranno i seguenti movimenti di magazzino:'
                           ),
                    "select_schart.php" =>
                    array( 0=>'Stampa schedari di magazzino',
                           'title'=>'Selezione per la visualizzazzione e/o la stampa delle schede di magazzino',
                           'mesg'=>array('La ricerca non ha dato risultati!',
                                         'Inserire almeno 1 carattere!',
                                         'Cambia articolo'
                                          ),
                           'errors'=>array('La data  non &egrave; corretta!',
                                           'La data di inizio dei movimenti contabili da stampare non pu&ograve; essere successiva alla data dell\'ultimo !',
                                           'La data di stampa non pu&ograve; essere precedente a quella dell\'ultimo movimento!',
                                           'L\'articolo iniziale non pu&ograve; avere un codice successivo a quello finale!',
                                           'La categoria merceologica iniziale non pu&ograve; avere un codice successivo a quello finale!'
                                          ),
                           'date'=>'Data di stampa ',
                           'cm_ini'=>'Categoria merceologica inizio ',
                           'art_ini'=>'Articolo inizio ',
                           'date_ini'=>'Data registrazione inizio  ',
                           'cm_fin'=>'Categoria merceologica fine ',
                           'art_fin'=>'Articolo fine ',
                           'date_fin'=>'Data registrazione fine ',
                           'header'=>array('Data'=>'','Causale'=>'','Descrizione<br \>documento'=>'',
                                            'Prezzo'=>'','UM' =>'','Quantit&agrave;'=>'',
                                            $admin_aziend['symbol'].'<br \>carico'=>'',$admin_aziend['symbol'].'<br \> scarico'=>'',
                                            'Quantit&agrave;<br \>giacenza'=>'','Valore<br \>giacenza'=>''
                                           ),
                           'tot'=>'Consistenza'
                           ),
                    "stampa_schart.php" =>
                    array( 0=>'SCHEDA DI MAGAZZINO dal ', 1=>' al ',
                           'bot'=>'a riportare : ',
                           'top'=>'da riporto :  ',
                           'item_head'=>array('Codice','Cat.Merc','Descrizione','U.M.','ScortaMin.'),
                           'header'=>array('Data','Causale','Descrizione documento',
                                            'Prezzo','UM','Quantita',
                                            $admin_aziend['symbol'].' carico',$admin_aziend['symbol'].' scarico',
                                            'Q.ta giacenza','Val. giacenza'
                                           ),
                           'tot'=>'Consistenza al '
                           ),
                    "select_deplia.php" =>
                    array( 'title'=>'Selezione per la stampa del catalogo',
                           'mesg'=>array('La ricerca non ha dato risultati!',
                                         'Inserire almeno 1 carattere!',
                                         'Cambia articolo'
                                          ),
                           'errors'=>array('La data  non &egrave; corretta!',
                                           'L\'articolo iniziale non pu&ograve; avere un codice successivo a quello finale!',
                                           'La categoria merceologica iniziale non pu&ograve; avere un codice successivo a quello finale!'
                                          ),
                           'date'=>'Data di stampa ',
                           'cm_ini'=>'Categoria merceologica inizio ',
                           'art_ini'=>'Articolo inizio ',
                           'cm_fin'=>'Categoria merceologica fine ',
                           'art_fin'=>'Articolo fine ',
                           'barcode'=>'Stampa',
                           'barcode_value'=>array(0=>'Immagini',1=>'Codici a Barre'),
                           'listino'=>'Listino',
                           'listino_value'=>array(1=>' di Vendita 1',2=>' di Vendita 2',3=>' di Vendita 3','web'=>' di Vendita Online')
                           ),
                    "select_listin.php" =>
                    array( 'title'=>'Selezione per la stampa dei listini',
                           'mesg'=>array('La ricerca non ha dato risultati!',
                                         'Inserire almeno 1 carattere!',
                                         'Cambia articolo'
                                          ),
                           'errors'=>array('La data  non &egrave; corretta!',
                                           'L\'articolo iniziale non pu&ograve; avere un codice successivo a quello finale!',
                                           'La categoria merceologica iniziale non pu&ograve; avere un codice successivo a quello finale!'
                                          ),
                           'date'=>'Data di stampa ',
                           'cm_ini'=>'Categoria merceologica inizio ',
                           'art_ini'=>'Articolo inizio ',
                           'cm_fin'=>'Categoria merceologica fine ',
                           'art_fin'=>'Articolo fine ',
                           'listino'=>'Listino',
                           'listino_value'=>array(0=>'d\'Acquisto',1=>' di Vendita 1',2=>' di Vendita 2',3=>' di Vendita 3','web'=>' di Vendita Online')
                           ),
                   "update_vatrate.php" =>
                   array( 'title'=>'Modifica aliquota IVA degli articoli',
                           'mesg'=>array('La ricerca non ha dato risultati!',
                                         'Inserire almeno 1 carattere!',
                                         'Cambia articolo'
                                          ),
                           'errors'=>array('Errore nullo',
                                           'L\'articolo iniziale non pu&ograve; avere un codice successivo a quello finale!',
                                           'La categoria merceologica iniziale non pu&ograve; avere un codice successivo a quello finale!'
                                          ),
                           'cm_ini'=>'Categoria merceologica inizio ',
                           'art_ini'=>'Articolo inizio ',
                           'cm_fin'=>'Categoria merceologica fine ',
                           'art_fin'=>'Articolo fine ',
                           'rate_obj'=>'Aliquota oggetto della modifica',
                           'rate_new'=>'Nuova aliquota',
                           'header'=>array('Cat.Merceologica'=>'','Codice'=>'','Descrizione'=>'','U.M.'=>'',
                                            'Aliquota vecchia'=>'','Aliquota nuova'=>''
                                          )
                           ),
                   "update_prezzi.php" =>
                   array( 'title'=>'Modifica prezzi di listino',
                           'mesg'=>array('La ricerca non ha dato risultati!',
                                         'Inserire almeno 1 carattere!',
                                         'Cambia articolo'
                                          ),
                           'errors'=>array('Valore "0" inaccettabile in questa modalit&agrave; di modifica !',
                                           'L\'articolo iniziale non pu&ograve; avere un codice successivo a quello finale!',
                                           'La categoria merceologica iniziale non pu&ograve; avere un codice successivo a quello finale!'
                                          ),
                           'cm_ini'=>'Categoria merceologica inizio ',
                           'art_ini'=>'Articolo inizio ',
                           'cm_fin'=>'Categoria merceologica fine ',
                           'art_fin'=>'Articolo fine ',
                           'lis_obj'=>'Listino oggetto della modifica',
                           'lis_bas'=>'Listino base di calcolo',
                           'listino_value'=>array(0=>'d\'Acquisto',1=>' di Vendita 1',2=>' di Vendita 2',3=>' di Vendita 3','web'=>' di Vendita Online'),
                           'mode'=>'Modalit&agrave; di modifica',
                           'mode_value'=>array('A'=>'Sostituzione','B'=>'Somma in percentuale','C'=>'Somma valore',
                                               'D'=>'Moltiplicazione per valore','E'=>'Divisione per valore','F'=>'Azzeramento e somma percentuale'),
                           'valore'=>'Percentuale/valore',
                           'round_mode'=>'Arrotondamento matematico a',
                           'round_mode_value'=>array('1 '.$admin_aziend['curr_name'],'10 centesimi','1 centesimo','1 millesimo','0,1 millesimi','0,01 millesimi'),
                           'header'=>array('Cat.Merceologica'=>'','Codice'=>'','Descrizione'=>'','U.M.'=>'',
                                            'Prezzo vecchio'=>'','Prezzo nuovo'=>''
                                          )
                           ),
                    "admin_artico.php" =>
                   array(  'title'=>'Gestione degli articoli',
                           'ins_this'=>'Inserimento articolo',
                           'upd_this'=>'Modifica l\'articolo ',
                           'errors'=>array('Il codice articolo &egrave; gi&agrave; esistente',
                                           'Si st&agrave; tentando di modificare il codice ad un articolo con dei movimenti di magazzino associati',
                                           'Codice di un articolo gi&agrave; esistente',
                                           'Il file dev\'essere nel formato PNG',
                                           'L\'immagine non dev\'essere pi&ugrave; grande di 10 kb',
                                           'Inserire un codice valido',
                                           'Inserire una descrizione',
                                           'Inserire l\'unit&agrave; di misura delle vendite',
                                           'Inserire l\'aliquota I.V.A.',
                                           'Per avere la tracciabilità per lotti è necessario attivare la contabilità di magazzino in configurazione azienda'
                                          ),
                           'codice'=>"Codice",
                           'descri'=>"Descrizione",
                           'lot_or_serial'=>'Lotti o numeri seriali',
                           'lot_or_serial_value'=>array(0=>'No',1=>'Lotti',2=>'Seriale/Matricola'),
                           'barcode'=>"Codice a Barre EAN13",
                           'image'=>"Immagine (jpg,png,gif) max 10kb",
                           'unimis'=>"Unit&agrave; di misura vendite",
                           'catmer'=>"Categoria merceologica",
                           'preacq'=>'Prezzo d\'acquisto',
                           'preve1'=>'Prezzo di vendita listino 1',
                           'preve2'=>'Prezzo di vendita listino 2',
                           'preve3'=>'Prezzo di vendita listino 3',
                           'aliiva'=>'Aliquota IVA',
                           'esiste'=>'Esistenza attuale',
                           'valore'=>'Valore dell\'esistente',
                           'last_cost'=>'Costo dell\'ultimo acquisto',
                           'scorta'=>'Scorta minima',
                           'riordino'=>'Lotto acquisto',
                           'uniacq'=>'Unit&agrave; di misura acquisti',
                           'peso_specifico'=>'Peso specifico/Moltiplicatore',
                           'volume_specifico'=>'Volume specifico',
                           'pack_units'=>'Pezzi in imballo',
                           'codcon'=>'Conto di ricavo su vendite',
                           'id_cost'=>'Conto di costo su acquisti',
                           'annota'=>'Annotazioni (pubblicate anche sul web)',
                           'document'=>'Documenti e/o certificazioni',
                           'web_mu'=>'Unit&agrave; di misura online',
                           'web_price'=>'Prezzo di vendita online',
                           'web_multiplier'=>'Moltiplicatore prezzo web',
                           'web_public'=>'Pubblica sul sito web',
                           'web_public_value'=>array(0=>'No',1=>'Si'),
                           'web_url'=>'Web url<br />(es: http://site.com/item.html)'
                         )
             );
?>