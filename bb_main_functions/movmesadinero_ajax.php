<link rel='stylesheet' type='text/css' media='screen' href='/middleware/css/estilo_middleware.css' />
<script language="javascript" src="/middleware/js/common/jsTooltips.js" />

<!--
<script language="javascript" src="/middleware/js/common/tab-view.js" />
<script language="javascript" src="/middleware/js/common/jsValidador.js" />

<script language="javascript" src="/middleware/js/common/jsTkPopUp.js"/>
<script language="javascript" src="/middleware/js/common/jsPopUp.js" />
<script language="javascript" src="/middleware/js/common/jsControl.js" />
-->

<link rel='stylesheet' type='text/css' media='screen' href='/middleware/css/jquery-ui-1.7.3.custom.css' />
<link rel='stylesheet' type='text/css' media='screen' href='/middleware/js/common/jquery.jqGrid-4.2.0/css/ui.jqgrid.css' />
<!-- Aqui cierra, al final verificar si se utilizan o no-->

<!-- NNS Aqui inicia, al final revisar si se utilizan o no

<script language="javascript" src="/middleware/js/common/jquery.jqGrid-4.2.0/js/i18n/grid.locale-es.js" />
<script language="javascript" src="/middleware/js/common/jquery.jqGrid-4.2.0/js/jquery.jqGrid.min.js" />
-->

<?php
include_once( "ezreimp/classes/ezreimp.php" ); //DBA Reimpresion de Comprobantes
// Optimizacion Catalogo (TO) desde WebServices - Inicio [2/4]
$debugON = $ini->read_var( "site", "DebugON" );
// Optimizacion Catalogo (TO) desde WebServices - Fin [2/4]

($parametros['Access']!=""?$Access=$parametros['Access']:0);
($parametros['CustID']!=""?$CustID=$parametros['CustID']:0);
($parametros['Trxn']!=""?$Trxn=$parametros['Trxn']:0);
($parametros['Cadpriv']!=""?$priv=$parametros['Cadpriv']:0);
($parametros['Amount']!=""?$Amount=$parametros['Amount']:0);
($parametros['Day']!=""?$Day=$parametros['Day']:0);
($parametros['Month']!=""?$Month=$parametros['Month']:0);
$wsdlMovMD = $ini->read_var( "site", "wsdlMovMD" ); //NSS Movimientos mesa de dinero

$t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ), "eztransaccion/user/intl/", $Language, "movmesa.php" );

$t->setAllStrings();

// AGG I 25Nov2005 Para poner fecha y hora en los comprobantes
$dateTime  = new eZDateTime( );
$timeStamp = $dateTime->timeStamp();
$time =& date('H:i:s', $timeStamp );
$date =& date('jMY', $timeStamp );
// AGG F 25Nov2005 Para poner fecha y hora en los comprobantes

$t->set_file( array(
	"transferencia_tpl" => "movmesa.tpl"
	) );

if(empty($Access)) {
	$Access = "FrAc";
}
// $Accion = "transferencia";
if( !isset /*(HB AGL - Ajustes PHP5)*/( $Amount ) ) {
	$Amount = "";
}
if( !isset /*(HB AGL - Ajustes PHP5)*/( $Accion ) ) {
	$Accion = "";
}
/*if( !isset ( $DiasPzo ) ) {
	$DiasPzo = "";
}
if( !isset ( $FrAccount ) ) {
	$FrAccount = "";
}
if( !isset ( $ToAccount ) ) {
	$ToAccount = "";
}
if( !isset ( $RFC ) ) {
	$RFC = "";
}
if( !isset ( $RenInt ) ) {
	$RenInt = "";
}
if( isset ( $Desc ) ) {
   $Desc = preg_replace("/</", " ", $Desc );
   $Desc = preg_replace("/>/", " ", $Desc );
   $Desc = preg_replace("/&/", "-", $Desc );
}
 */
 switch($Access) {
case "FrAc":
	$particularFields = "";
	//$tr->blog($qki,"Transferencia", $FrAccount, $ToAccount, $DiasPzo, $Amount, $Accion);
	$tr->blog($qki,"Transferencia", $Amount, $Accion);
	break;
}
$transaccion_buffer = "";

	$ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=mmd&Access=".urlencode($Access)."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer);
	$t->set_var( "transaccion_buffer", $transaccion_buffer.$Pagina );

	//Reemplaza variables segun corresponda
	$transaccion_buffer=$t->parse( "output", "transferencia_tpl" );
?>
