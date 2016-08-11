<?php
$gTables['contract'] = $table_prefix.'_'.$id."contract";
$gTables['contract_row'] = $table_prefix.'_'.$id."contract_row";

function effettInsert ($newValue)
{
    $table = 'effett';
    $columns = array('tipeff','datemi','progre','numfat','seziva','protoc','datfat',
                     'totfat','salacc','impeff','scaden','clfoco','pagame',
                     'banapp','banacc','id_doc','id_con','status','adminid');
    $newValue['adminid'] = $_SESSION['Login'];
    tableInsert($table, $columns, $newValue);
}

function agentiInsert ($codice, $newValue)
{
    $table = 'agenti';
    $columns = array('id_agente','id_fornitore','base_percent','tipo_contratto','adminid');
    $newValue['adminid'] = $_SESSION['Login'];
    tableInsert($table, $columns, $newValue);
}

function agentiUpdate ($codice, $newValue)
{
    $table = 'agenti';
    $columns = array('id_agente','id_fornitore','base_percent','tipo_contratto','adminid');
    $newValue['adminid'] = $_SESSION['Login'];
    tableUpdate($table, $columns, $codice, $newValue);
}

function bodytextInsert ($newValue)
{
    $table = 'body_text';
    $columns = array('table_name_ref','id_ref','body_text','lang_id');
    tableInsert($table, $columns, $newValue);
}

function bodytextUpdate ($codice, $newValue)
{
    $table = 'body_text';
    $columns = array('table_name_ref','id_ref','body_text','lang_id');
    tableUpdate($table, $columns, $codice, $newValue);
}

function contractUpdate ($newValue,$codice=false)
{
    // per fare l'upload in $codice dev'essere passato un: array(0=>'id_contract',1=>valore di id_contract da aggiornare)
    // altrimenti si fa l'insert
    $table = 'contract';
    $columns = array( 'id_customer', 'vat_section', 'doc_number', 'doc_type', 'conclusion_date',
                      'start_date', 'months_duration', 'initial_fee','periodic_reassessment',
                      'bank', 'periodicity', 'payment_method', 'tacit_renewal', 'current_fee',
                      'id_con', 'cod_revenue', 'vat_code', 'id_body_text', 'last_reassessment',
                      'id_agente', 'provvigione', 'status', 'note', 'adminid');
    $newValue['adminid'] = $_SESSION['Login'];
    if (is_array($codice)) {
       tableUpdate($table, $columns, $codice, $newValue);
    } else {
       tableInsert($table, $columns, $newValue);
    }
}

function contractRowUpdate ($newValue,$codice=false)
{
    // per fare l'upload in $codice dev'essere passato un: array(0=>'id_row',1=>valore di id_row da aggiornare)
    // altrimenti si fa l'insert
    $table = 'contract_row';
    $columns = array( 'id_contract','descri','unimis','quanti',
                      'price','discount','vat_code','cod_revenue','status');
    $newValue['adminid'] = $_SESSION['Login'];
    if (is_array($codice)) {
       tableUpdate($table, $columns, $codice, $newValue);
    } else {
       tableInsert($table, $columns, $newValue);
    }
}

function provvigioniInsert ($newValue)
{
    $table = 'provvigioni';
    $columns = array('id_agente','id_provvigione','cod_articolo','cod_catmer','percentuale');
    $newValue['adminid'] = $_SESSION['Login'];
    tableInsert($table, $columns, $newValue);
}

function provvigioniUpdate ($codice, $newValue)
{
    $table = 'provvigioni';
    $columns = array('id_agente','id_provvigione','cod_articolo','cod_catmer','percentuale');
    $newValue['adminid'] = $_SESSION['Login'];
    tableUpdate($table, $columns, $codice, $newValue);
}


function fae_fluxInsert($newValue)
{
    $table = 'fae_flux';
    $columns = array('filename_ori','id_tes_ref','exec_date','received_date','delivery_date','filename_son','id_SDI','filename_ret','mail_id','data','flux_status','progr_ret','flux_descri');
    tableInsert($table, $columns, $newValue);
}


?>