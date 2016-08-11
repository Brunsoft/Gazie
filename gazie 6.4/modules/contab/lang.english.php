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
$strScript = array ("select_liqiva.php" =>
                    array( 'title'=>'Select for view and/or print Periodic VAT Clearance',
                           'errors'=>array('The date is incorrect!',
                                           'The start date can not be later than the end date !'
                                          ),
                           'page_ini'=>'N. page start',
                           'sta_def'=>'Final print',
                           'sta_def_title'=>'If selected changes the value of the last page printed from this company record configuration',
                           'descri'=>'Descrizione',
                           'descri_value'=>array('M'=>'of  ','T'=>'the quarter '
                                           ),
                           'date_ini'=>'Start date  ',
                           'sem_ord'=>' Regime ',
                           'sem_ord_value'=>array(0=>' Ordinary accounting ',1=>' Semplified accounting '
                                           ),
                           'cover'=>'Print the cover',
                           'date_fin'=>'End date ',
                           'header'=>array('Section'=>'','Register'=>'','Rate description'=>'','Taxable'=>'',
                                           'Rate'=>'','Tax'=>'','Indetraibile'=>'','Amount'=>''
                                           ),
                           'regiva_value'=>array(0=>'None',2 =>'Sale Invoices',4=>'Sale Tickets',6=>'Purchase Invoices'),
                           'of'=>' of ',
                           'tot'=>' AMOUNT ',
                           't_pos'=>' V.A.T. DEBIT',
                           't_neg'=>' V.A.T. CREDIT',
                           'carry'=>'Credit from previous period'
                           ),
                    "stampa_liqiva.php" =>
                    array( 'title'=>'Periodic VAT Clearance',
                           'cover_descri'=>'VAT summary book of the year ',
                           'page'=>'Page',
                           'sez'=>'Section',
                           'regiva_value'=>array(0=>'None',2 =>'Register of Sale Invoices',4=>'Regitro  of Sale Tickets',6=>'Register of Purchase Invoices'),
                           'code'=>'Code',
                           'descri'=>'Descriptiov of V.A.T. rate',
                           'imp'=>'Taxable',
                           'iva'=>'Tax',
                           'rate'=>'%',
                           'ind'=>'Indetraibile',
                           'tot'=>'Amount',
                           't_reg'=>'V.A.T. total of the register ',
                           't_pos'=>' DEBIT V.A.T.',
                           't_neg'=>' CREDIT V.A.T.',
                           'inter'=>'Increase as interest ',
                           'pay'=>' a pagare',
                           'carry'=>'Credit from previous period',
                           'pay_date'=>'Paid on ',
                           'co'=>'at the ',
                           'abi'=>' A.B.I. ',
                           'cab'=>' C.A.B. '
                           ),
                    "select_partit.php" =>
                    array( 'title'=>'Selection for view and/or printing accounts ledger entries',
                           'mesg'=>array('The search yielded no results!',
                                         'Insert at least 2 characters!',
                                         'Changing customer / supplier'
                                          ),
                           'errors'=>array('The date is incorrect!',
                                           'The start date of the ledger entries can not be printed after the last !',
                                           'The date of the press can not be earlier than the last ledger entries!',
                                           'The initial account can not be later than the final!',
                                           'There aren\' movements within selected'
                                          ),
                           'date'=>'Print Date ',
                           'master_ini'=>'Start Master Account',
                           'account_ini'=>'Start Sub Account',
                           'date_ini'=>'Start Date  ',
                           'master_fin'=>'End Master Account ',
                           'account_fin'=>'End Sub Account ',
                           'date_fin'=>'End Date ',
                           'selfin' => 'Copy initial account',
                           'header1'=>array('Account'=>'','Num.Mov.'=>'','Descrizione'=>'',
                                            'Debt'=>'','Credit'=>'','Progressive<br />balance'=>''
                                           ),
                           'header2'=>array('Date'=>'','ID'=>'','Description'=>'','N.Doc.'=>'',
                                            'Date Doc.' =>'','Credit'=>'','Debt'=>'',
                                            'Progressive<br />balance'=>''
                                           )
                           ),
                    "admin_caucon.php" =>
                    array( 'title'=>'Management accounting causal',
                           'ins_this'=>'Enter a new accounting causal ',
                           'upd_this'=>'Upadate of accounting causal',
                           'mesg'=>array('The search yielded no results!',
                                         'Insert at least 2 characters!',
                                         'Changing customer / supplier'
                                          ),
                           'errors'=>array('Enter a valid code!',
                                           'You must enter a description!',
                                           'Existing code using the appropriate procedure if you want to change!',
                                           'You must define at least one account!',
                                           'Code reserved for AUTOMATIC CLOSING ACCOUNTS!',
                                           'Code reserved for AUTOMATIC OPENING ACCOUNTS!'
                                          ),
                           'head'=>'Accounts to be moved ',
                           'codice'=>'Code causal *',
                           'descri'=>'Description *',
                           'insdoc'=>'Data Entry Reference Document',
                           'insdoc_value'=>array(0=>'No',1=>'Yes'),
                           'regiva'=>'VAT Register',
                           'regiva_value'=>array(0=>'None',2 =>'Invoice of sale',4=>'Tickets',6=>'Invoice of purchase'),
                           'operat'=>'Operator',
                           'operat_value'=>array(0=>'No',1=>'Sum',2=>'Subtract'),
                           'pay_schedule'=>'Open items (scheduler)',
                           'pay_schedule_value'=>array(0=>'Does not work',1=>'Document sale / purchase (open)',2=>'Payment (close)'),
                           'contr'=>'Account (min. 1) *',
                           'tipim'=>'Type of amount',
                           'tipim_value'=>array(''=>'','A'=>'Total','B'=>'Taxable','C'=>'Tax'),
                           'daav'=>'DEBITS/CREDITS',
                           'daav_value'=>array('D'=>'DEBITS','A'=>'CREDITS'),
                           'report'=>'List of the accounting causals',
                           'del_this'=>'Accounting causal '
                           ),
                    "admin_piacon.php" =>
                   array(  'title'=>'Manage chart of accounts',
                           'ins_this'=>'Insert account',
                           'upd_this'=>'update account',
                           'errors'=>array('Enter a valid code!',
                                           'Existing code using the appropriate procedure if you want to change!',
                                           'You must enter a description!'
                                          ),
                           'codice'=>"Code ",
                           'mas'=>"Master",
                           'sub'=>"Subaccount",
                           'descri'=>"Description",
                           'ceedar'=>"Reclassification of EEC balance sheet / DEBITS",
                           'ceeave'=>"Reclassification of EEC balance sheet / CREDITS",
                           'annota'=>"Note"
                         ),
                    "admin_movcon.php" =>
                    array( 'title'=>'Management ledger entries',
                           'ins_this'=>'Insert new ledger entries',
                           'upd_this'=>'Update new ledger entries',
                           'mesg'=>array('The search yielded no results!',
                                         'Insert at least 2 characters!',
                                         'Changing customer / supplier'
                                          ),
                           'errors'=>array('At least one row has no accounts!',
                                           'At least one row has zero value!',
                                           'Accounting entry is unbalanced!',
                                           'Total of DEBT rows must not be zero!',
                                           'Total of CREDIT rows must not be zero!',
                                           'VAT entry is zero !',
                                           'VAT entry have an amount different from that of the Accounting entry!',
                                           'Must be insert an description!',
                                           'The record date is incorrect!',
                                           'The document date is incorrect!!',
                                           'You forgot to put the registration number!',
                                           'You forgot to put the document number!',
                                           'The date of the document must not be later than that of the registration!',
                                           'WARNING you\'re editing a movement that involving a VAT registry!',
                                           'You\'re trying to record a document that is already registered',
                                           'Il totale dei movimenti dello scadenziario non coincidono con l\'importo del rigo ad esso relativo'
                                          ),
                           'id_testata'=>'Entry number',
                           'date_reg'=>'Registration date',
                           'descri'=>'Description',
                           'caucon'=>'Accounting Causal',
                           'v_caucon'=>'Confirm Causal!',
                           'insdoc'=>'Details of the reference document',
                           'insdoc_value'=>array(0=>'Yes',1=>'No'),
                           'regiva'=>'VAT register',
                           'regiva_value'=>array(0=>'None',2 =>'Invoices sales',4=>'Receipts tax',6=>'Invoices purchases'),
                           'operat'=>'Operator',
                           'operat_value'=>array(0=>'No',1=>'Sum',2=>'Subtract'),
                           'date_doc'=>'Document date',
                           'seziva'=>'VAT section',
                           'protoc'=>'Register number',
                           'numdoc'=>'Number',
                           'partner'=>'Customer / Supplier',
                           'insiva'=>'VAT entry',
                           'vat'=>'VAT rate',
                           'taxable'=>'Taxable',
                           'tax'=>'Tax',
                           'mas'=>"Master",
                           'sub'=>"Account",
                           'amount'=>'Amount',
                           'daav'=>'DEBT/CREDIT',
                           'daav_value'=>array('D'=>'DEBT','A'=>'CREDIT'),
                           'bal_title'=>"Balance compared to this value!",
                           'bal'=>"Balanced",
                           'addval'=>"Increase the value of ",
                           'subval'=>"Decrease the value of ",
                           'zero'=>"Accounting entries is zero!",
                           'diff'=>"Odds",
                           'tot_d'=>'DEBT total',
                           'tot_a'=>'CREDIT total',
                           'visacc'=>'View ledgers',
                           'report'=>'List of ledger entries',
                           'del_this'=>'Ledger entries',
                           'sourcedoc'=>'Source document',
                           'source'=>'Source',
						   'customer_receipt'=>'Print receipt',
                           ),
                    "report_piacon.php" =>
                   array(  'title'=>'Chart of accounts',
                           'ins_this'=>'Insert new accont',
                           'view_this'=>'View and/or print account report',
                           'print_this'=>'Print the chart of accounts',
                           'header'=>array('Master'=>'','Account'=>'','Description'=>'','Debits'=>'',
                                            'Credits'=>'','Balance'=>'','View<br />and/or print'=>'',
                                            'Delete'=>''),
                           'msg1'=>'Remember that you must master to introduce the activities between 100 and 199, between 200 and 299 liabilities, costs between 300 and 399, income between 400 and 499 and the memorandum accounts or transient between 500 and 599',
                           'msg2'=>'Balances for the year '
                         ),
                    "select_regiva.php" =>
                    array( 'title'=>'Select for prewiev and/or print VAT register',
                           'errors'=>array('Incorrect date!',
                                           'The start date can not be later than the end date !',
                                           'P'=>'The sequence of protocol numbers is not correct',
                                           'N'=>'The sequence of document numbers is not correct',
                                           'T'=>'There is a movement without VAT rate',
                                           'err'=>'There are some errors that do not justify the printing of the register'
                                          ),
                           'vat_reg'=>'VAT register print:',
                           'vat_reg_value'=>array(2=>'Sale invoices',4=>'Charges',6=>'Purchase invoices'),
                           'vat_section'=>'VAT section ',
                           'page_ini'=>'N. of start page',
                           'jump'=>'Summary for each hop period',
                           'jump_title'=>'If selected print on the PDF all periodic summaries',
                           'sta_def'=>'Final print',
                           'sta_def_title'=>'If you selected this value the company configuration archive is updated with the firm value of the printed page',
                           'descri'=>'Description',
                           'descri_value'=>array('M'=>' of the month ','T'=>'of the quarter '
                                           ),
                           'date_ini'=>'Start date entry  ',
                           'sem_ord'=>' Accounting system ',
                           'sem_ord_value'=>array(0=>' Ordinary ',1=>' Simplified '
                                           ),
                           'cover'=>'Print the cover',
                           'date_fin'=>'End date entry',
                           'header'=>array('Protocol'=>'','Date - ID movement'=>'','Document description'=>'','Customer or Supplier'=>'',
                                            'Taxable' =>'','VAT rate'=>'','Tax'=>''
                                           ),
                           'of'=>' of the ',
                           'tot'=>' TOTAL',
                           't_gen'=>' GENERAL'
                           ),
                    "stampa_regiva.php" =>
                    array( 'title'=>array(2=>'V.A.T. register of sales invoices ',
                                          4=>'V.A.T. register of receipts ',
                                          6=>'V.A.T. register of purchase invoices  '),
                           'cover_descri'=>array(2=>'Sales invoices register of the year',
                                                 4=>'Receipts register of the year ',
                                                 6=>'Purchase invoices register of the year '),
                           'partner_descri'=>array(2=>'Company customer',
                                                   4=>'Description',
                                                   6=>'CompanySupplier'),
                           'vat_section'=>' V.A.T. section n.',
                           'page'=>'page',
                           'top_carry'=>'from carry : ',
                           'bot_carry'=>'to carry : ',
                           'top'=>array('prot'=>'N.Prot.',
                                        'dreg'=>'Entry date', 
                                        'desc'=>'N.Document/Descr.',
                                        'ddoc'=>'Date Doc.', 
                                        'txbl'=>'Taxable',
                                        'perc'=>'Perc.',
                                        'tax'=>'Tax',
                                        'tot'=>'Total'), 
                           'of'=>' of ',
                           'vat_castle_title'=>' TOTAL SUMMARY FOR RATES ',
                           'descri'=>'description',
                           'taxable'=>'taxable',
                           'tax'=>'tax',
                           'tot'=>'total',
                           'tot_descri'=>'GENERAL TOTAL',
                           'acc_castle_title'=>' ACCOUNT TOTAL SUMMARY ',
                           'amount'=>'amount'
                           ),
                    "select_libgio.php" =>
                    array( 'title'=>'Select for prewiev and/or print General Ledger',
                           'errors'=>array('Incorrect start date!',
                                           'Incorrect final date!',
                                           'The start date can not be later than the end date !'
                                          ),
                           'pagini'=>'N. of start page',
                           'stadef'=>'Final print',
                           'stadef_title'=>'If selected changes the value of the last page printed from this company record configuration',
                           'date_ini'=>'Entry start date  ',
                           'cover'=>' print cover -> ',
                           'date_fin'=>'Entry end date ',
                           'valdar'=>'DEBIT (initial)',
                           'valave'=>'CREDIT (initial)',
                           'nrow'=>'Number of rows:',
                           'tot_a'=>' Total DEBIT ',
                           'tot_d'=>' Total CREDIT '
                           )
                    );

?>