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

$strScript = array("admin_fornit.php" =>
                   array(  'title'=>'Suppliers management',
                           'ins_this'=>'Insert new supplier',
                           'upd_this'=>'Update supplier',
                           'mesg'=>array('The search yielded no results!',
                                         'Insert at least 2 characters!',
                                         'Changing supplier'
                                          ),
                           'errors'=>array('Must indicate the company name',
                                           'Must indicate the address',
                                           'Invalid postal code',
                                           'Must indicate the city',
                                           'Must indicate the province',
                                           'Must indicate the sex',
                                           'L\'IBAN is  incorrect',
                                           'The IBAN and the nation are different',
                                           'Tax Code incorrect for an individual',
                                           'VAT registration number is incorrect',
                                           'There is already a supplier with the same VAT registration number',
                                           'Tax code is incorrect',
                                           'There is already a supplier with the same Tax Code',
                                           'Tax code missing! Is automatically set with <br />the same value of the VAT registration number',
                                           'Is an individual, enter the Tax Code',
                                           'Is there a registry with the same VAT registration number',
                                           'Is there a registry with the same Tax code',
                                           'You must choose your payment method',
                                           'The supplier code is already there, try the entry with the one proposed (plus 1)',
                                           'The date of birth is wrong',
                                           'Email address formally wrong'
                                          ),
                           'link_anagra'=>' Click below to enter the existing registry on the your chart of accounts',
                           'codice'=>"Code ",
                           'ragso1'=>"Company Name 1",
                           'ragso2'=>"Company Name 2",
                           'sedleg'=>"Registered Office",
                           'luonas'=>'Birthplace',
                           'datnas'=>'Date of birth',
                           'pronas'=>'Province of birth',
                           'counas'=>'Country of birth',
                           'legrap'=>"Legal representative ",
                           'sexper'=>"Sex/legal person",
                           'sexper_value'=>array(''=>'-','M'=>'Male','F'=>'Female','G'=>'Legal'),
                           'indspe'=>'Address',
                           'capspe'=>'Postal Code',
                           'citspe'=>'City - Province',
                           'country'=>'Nation',
                           'id_language'=>'Language',
                           'id_currency'=>'Currency',
                           'telefo'=>'Telephone',
                           'fax'=>'Fax',
                           'cell'=>'Cellphone',
                           'codfis'=>'Tax code',
                           'pariva'=>'VAT registration number',
                           'e_mail'=>'e mail',
                           'cosric'=>'Cost Account',
                           'codpag'=>'Payment method*',
                           'sconto'=>'% Discount applied',
                           'banapp'=>'Bank support',
                           'portos'=>'Rendered port',
                           'spediz'=>'Delivery',
                           'imball'=>'Package',
                           'listin'=>'Pricelist applied',
                           'id_des'=>'Destination &rArr; from registry',
                           'destin'=>'Destination &rArr; free description',
                           'iban'=>'IBAN',
                           'maxrat'=>'Maximum amount of bills',
                           'ragdoc'=>'Grouping documents',
                           'addbol'=>'Charge the stamp expenses',
                           'speban'=>'Charge the bank expenses',
                           'spefat'=>'Charge the cost of billing',
                           'stapre'=>'Print prices on shipping documents',
                           'op_type'=>'Operation type',
                           'op_type_value'=>array(3=>'Purchase of goods',4=>'Purchase of services'),
                           'allegato'=>'Attached VAT - Customers report',
                           'yn_value'=>array('S'=>'Yes','N'=>'No'),
                           'aliiva'=>'VAT reduction',
                           'ritenuta'=>'% Withholding',
                           'status'=>'Visibility at the research',
                           'status_value'=>array(''=>'Yes','HIDDEN'=>'Hidden'),
                           'annota'=>'Note'
                         ),
                   "report_broacq.php" =>
                     array('New Preveter','New Order',
                           'title'=>'Preventivi e ordini',
                         'mail_alert0'=>'Invio documento con email',
                         'mail_alert1'=>'Hai scelto di inviare una e-mail all\'indirizzo: ',
                         'mail_alert2'=>'con allegato il seguente documento:',
                            ),
                   "report_debiti.php" =>
                     array('title'=>'List of debts to suppliers',
                           'start_date'=>'Year-start',
                           'end_date'=>'Year-end',
                           'codice'=>'Code',
                           'partner'=>'Supplier',
                           'telefo'=>'Telephone',
                           'mov'=>'N.Entries',
                           'dare'=>'Debit',
                           'avere'=>'Credit',
                           'saldo'=>'Balance',
                           'pay'=>'Pay',
                           'statement'=>'Statement',
                           'pay_title'=>'Pays the debt with ',
                           'statement_title'=>'Print the statement of '
                           ),
                    "admin_docacq.php" =>
                    array(  array("DDR" => "D.d.T. di Reso a Fornitore","DDL" => "D.d.T. c/lavorazione","AFA" => "Fattura d'Acquisto","ADT" => "D.d.T. d'Acquisto","AFC" => "Nota Credito da Fornitore","AOR" => "Ordine a Fornitore","APR" => "Richiesta di Preventivo a Fornitore"),
                           'mesg'=>array('The search yielded no results!',
                                         'Insert at least 2 characters!',
                                         'Changing customer / supplier'
                                          ),
                           " corpo ",
                           " piede ",
                           " Tira su ",
                           " Sezione ",
                           " Indirizzo ",
                           " Data ",
                           " Listino ",
                           " Pagamento ",
                           " Banca ",
                           " Destinazione ",
                           " Causale ",
                           " magazzino ",
                           " acquisto " ,
                           "Vettore",
                           "Articolo",
                           "Quantit&agrave;",
                           "Tipo",
                           "Costo",
                           "I.V.A.",
                           "Codice",
                           "Descrizione",
                           "U.M.",
                           "Prezzo",
                           "Sconto",
                           "Importo",
                           "Imballo",
                           "Spedizione",
                           "Trasporto",
                           "Porto",
                           "Inizio trasporto",
                           " ore ",
                           "Imponibile",
                           "Imposta",
                           "Merce",
                           "Peso",
                           "Totale",
                           "La data di inizio trasporto non &egrave; corretta!",
                           "La data di inizio trasporto non pu&ograve; essere precedente alla data di emissione!",
                           "Non ci sono righi per poter emettere il documento!",
                           "Stai tentando di modificare il DdT con una data antecedente a quella del DdT con numero precedente!",
                           "Stai tentando di modificare il DdT con una data successiva a quella del DdT con numero successivo!",
                           "Stai tentando di modificare il documento con una data antecedente a quello dello stesso tipo di documento con numero precedente!" ,
                           "Stai tentando di modificare il documento con una data successiva a quello dello stesso tipo di documento con numero successivo!" ,
                           "La data di emissione non pu&ograve; essere antecedente a quella dell'ultimo DdT emesso!",
                           "La data di emissione non pu&ograve; essere antecedente a quella dell'ultimo documento dello stesso tipo emesso!",
                           "La data di emissione non &egrave; corretta!",
                           "Non hai selezionato il fornitore!",
                           "Non hai selezionato la modalit&agrave; di pagamento!",
                           "Un rigo &egrave; senza la descrizione!",
                           "Un rigo &egrave; senza l'unit&agrave; di misura!",
                           "Causale mag.",
                           "Numero ",
                           "La data di registrazione non pu&ograve; essere antecedente a quella del documento da registrare!",
                           "La data del documento da registrare non &egrave; corretta!",
                           "Non &egrave; stato inserito il numero del documento!"
                           ),
                    "accounting_documents.php" =>
                     array('title'=>'Create movements accounting from taxable documents',
                           'errors'=>array('Incorrect date',
                                           'There are no documents to be written in the selected'
                                          ),
                           'vat_section'=>' of VAT section n.',
                           'date'=>'Until :',
                           'type'=>'VAT register ',
                           'type_value'=>array('A'=>'of Purchase Invoices'),
                           'proini'=>'Initial protocol',
                           'profin'=>'Final protocol',
                           'preview'=>'Accounting preview',
                           'date_reg'=>'Date',
                           'protoc'=>'Protocol',
                           'doc_type'=>'Type',
                           'doc_type_value'=>array('FAD'=>'DEFERRED INVOICE TO THE customER',
                                                   'FAI'=>'IMMEDIATE INVOICE TO THE customER',
                                                   'FNC'=>'CREDIT NOTE TO THE customER',
                                                   'FND'=>'DEBT NOTE TO THE customER',
                                                   'VCO'=>'FEES',
                                                   'VRI'=>'RECEIVED',
                                                   'AFA'=>'PURCHASE INVOICE',
                                                   'AFC'=>'CREDIT NOTE FROM PURCHASE',
                                                   'AFD'=>'DEBT NOTE FROM PURCHASE'
                                                   ),
                           'customer'=>'Supplier',
                           'taxable'=>'Taxable',
                           'vat'=>'VAT',
                           'stamp'=>'Stamps on bills',
                           'tot'=>'Total'
                           ),
                    "report_schedule_acq.php" =>
                    array( 'title'=>'Lista dei movimenti delle partite',
                           'header'=>array( 'ID'=>'id',
                                            'Identificativo partita' => "id_tesdoc_ref",
                                            'Movimento contabile apertura (documento)' => "id_rigmoc_doc",
                                            'Movimento contabile chiusura (pagamento)' => "id_rigmoc_pay",
                                            'Importo' => "amount",
                                            'Scadenza' => "expiry"
                                            ),
                           ),
                    "select_schedule.php" =>
                    array( 'title'=>'Selezione per la visualizzazzione e/o la stampa delle partite aperte',
                           'mesg'=>array('La ricerca non ha dato risultati!',
                                         'Inserire almeno 2 caratteri!',
                                         'Cambia fornitore'
                                          ),
                           'errors'=>array('La data  non &egrave; corretta!',
                                           'Non sono stati trovati movimenti!'
                                          ),
                           'account'=>'Fornitoree ',
                           'orderby'=>'Ordina per: ',
                           'orderby_value'=>array(0=>'Scadenza crescente',1=>'Scadenza decrescente',
                                                  2=>'Fornitore crescente',3=>'Fornitore decrescente'
                                           ),
                           'header'=>array('Fornitore'=>'','ID Partita'=>'','Status'=>'','Mov.Cont.'=>'','Descrizione'=>'',
                                            'N.Doc.'=>'','Data Doc.' =>'','Data Reg.' =>'','Dare'=>'','Avere'=>'',
                                            'Scadenza'=>''
                                           ),
                           'status_value'=>array(0=>'Chiusa',1=>'Aperta'),
                           ),
                    "delete_schedule.php" =>
                    array( 'title'=>'Cancellazione movimenti chiusi dello scadenzario',
                           'ragsoc'=>'Fornitore',
                           'id_tesdoc_ref'=>'Identificativo partita',
                           'descri'=> 'Descrizione'
                           ),
                    "select_partner_status.php" =>
                    array( 'title'=>'Selezione per la visualizzazzione e/o la stampa dello scadenzario dei fornitori',
                           'print_title'=>'SCADENZARIO FORNITORI ',
                           'errors'=>array('La data  non &egrave; corretta!',
                                           'err'=>'Ci sono degli errori che impediscono la stampa'
                                          ),
                           'date_ini'=>'Data di riferimento ',
                           'header'=>array('ID'=>'','Importo apertura'=>'','Data Scadenza'=>'','Importo chiusura'=>''
                                           ,'Data chiusura'=>'','Giorni esposizione' =>'','Stato'=>''
                                           ),
                            'status_value'=>array(0=>'APERTA',1=>'CHIUSA',2=>'ESPOSTA',3=>'SCADUTA',9=>'ANTICIPO')
                           ),
                    "supplier_payment.php" =>
                    array( 'title'=>'Pagamento debito verso fornitoree (chiusura partita/e)',
                           'errors'=>array('La data  non &egrave; corretta!'),
                           'partner'=>'Fornitore ',
                           'date_ini'=>'Data di registrazione',
                           'target_account'=>"Conto per il pagamento: ",
                           'accbal'=>'Saldo risultante dai movimenti contabili: ',
                           'paymovbal'=>'Saldo risultante dalle partite aperte: ',
                           'header'=>array('ID'=>'','Importo apertura'=>'','Data Scadenza'=>'','Importo chiusura'=>''
                                           ,'Data chiusura'=>'','Giorni esposizione' =>'','Stato'=>''
                                           ),
                           'status_value'=>array(0=>'APERTA',1=>'CHIUSA',2=>'ESPOSTA',3=>'SCADUTA',9=>'ANTICIPO'),
                           'header'=>array('ID'=>'','Importo apertura'=>'','Data Scadenza'=>'','Importo chiusura'=>''
                                           ,'Data chiusura'=>'','Giorni esposizione' =>'','Stato'=>'', 'Paga'=>''
                                           ),
                           'mesg'=>array('La ricerca non ha dato risultati!',
                                         'Inserire almeno 2 caratteri!',
                                         'Cambia fornitore',
                                         'Il saldo contabile è diverso da quello dello scadenzario,<br> se vuoi registrare la riscossione di un documento presente solo in contabilità fallo da qui:',
                                         'Nessun importo è stato pagato!',
					 "Non è stato selezionato il conto per l'incasso",
                                         'Stai tentando di inserire il pagamento ad un fornitore senza movimenti'
                                          ),
                           )
);
?>