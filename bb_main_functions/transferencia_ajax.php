<!-- DBA ReingenieriaBajioNET -->
<!--
<link rel='stylesheet' type='text/css' media='screen' href='/middleware/css/errorValidate.css' />
<link rel='stylesheet' type='text/css' media='screen' href='/middleware/css/estilo_middleware.css' />
<link rel='stylesheet' type='text/css' media='screen' href='/middleware/css/tab-view.css' />
<link rel='stylesheet' type='text/css' media='screen' href='/middleware/css/ayuda.css' />

<script language="javascript" src="/middleware/js/common/tab-view.js" />
<script language="javascript" src="/middleware/js/common/jsValidador.js" />
<script language="javascript" src="/middleware/js/common/jsTooltips.js" />
<script language="javascript" src="/middleware/js/common/jsTkPopUp.js"/>
<script language="javascript" src="/middleware/js/common/jsPopUp.js" />
<script language="javascript" src="/middleware/js/common/jsControl.js" />

<link rel='stylesheet' type='text/css' media='screen' href='/middleware/css/jquery-ui-1.7.3.custom.css' />
<link rel='stylesheet' type='text/css' media='screen' href='/middleware/js/common/jquery.jqGrid-4.2.0/css/ui.jqgrid.css' />
-->
<!--
<script language="javascript" src="/middleware/js/common/jsTkPopUp.js"/>
<script language="javascript" src="/middleware/js/common/jsPopUp.js" />
<script language="javascript" src="/middleware/js/common/jsControl.js" />
<script language="javascript" src="/middleware/js/common/jsTooltips.js" />
-->

<script language="javascript" src="/middleware/js/common/jquery.jqGrid-4.2.0/js/i18n/grid.locale-es.js" />
<script language="javascript" src="/middleware/js/common/jquery.jqGrid-4.2.0/js/jquery.jqGrid.min.js" />

<script type="text/javascript">
	$( document).ready(function(){
        configuraAyuda( "/images/ic_ayuda.gif" );
	});
</script>

<script type="text/javascript">
function esnumerico(e) 
{
	var valid="0123456789";
	var charCode;
	if (navigator.appName == "Netscape")
		charCode = e.which 
	else
		charCode = e.keyCode
	 if ((charCode==null) || (charCode==0) || (charCode==8) || (charCode==9) || (charCode==13) || (charCode==27) || (charCode==32) )
		return true;
	charCode = String.fromCharCode(charCode);
	if(valid.indexOf(charCode)==-1)
		return false;
	return true;
}
</script>

<script type="text/javascript">
$(document).ready(function() {
    var options = {
        target:        '#mainContents'
 	};
jQuery('#ToAccount2').click(function(){
	$( "#consultaDestino" ).trigger( 'click' );
	
});

   jQuery("#consultaDestino").click( function()
   		{
			try
			{

				$('#modalDestino').modal({
						  close:false,
						  persist: true, 
						  containerCss: {
											height: 605,
											width: 750
										},
							onShow: gridDestino(),
			     			onClose: closeConfirm
							});
					}
					catch(e)
					{
					$('#modalDestino').modal({close:false,
											  containerCss: {
																height: 350,
																width: 760
															},
												onClose: closeConfirm
											});
			}

	});
	//OVVC [INFO] => LIBRERÍAS POR SESIÓN [DATE] => 20022015
	/*
	$("#aceptaTransfer").live('click', function(){
		llamaPopUp();
	});
	*/

	//DBA Arreglar multiples peticiones de CONFIRM
	$("#aceptaTransfer").click(function(){

		llamaPopUp();
	});
	//DBA Arreglar multiples peticiones de CONFIRM

	 $("#Amount").keypress(function(event)
	 {
		if ( event.which == 13 ) {
			llamaPopUp();
		} /*else {
			currencya($this,event);
		}*/
	});
	//OVVC [INFO] => LIBRERÍAS POR SESIÓN [DATE] => 20022015 [I]
	$("#Amount").keyup(function(event){		
		//currencya(this,event);
		formatoMontoJQ(event);
	});
	$("#Desc").keyup(function(event){		
		//currencyi(this,event);		
	});
	//OVVC [INFO] => LIBRERÍAS POR SESIÓN [DATE] => 20022015 [F]
	$("#Desc").keypress(function(event)
	 {
		if ( event.which == 13 ) {
		llamaPopUp();
		}
	});

	$("#Estado").keypress(function(event)
	 {
		if ( event.which == 13 ) {
		llamaPopUp();
		}
	});
 });
	function llamaPopUp()
	{
		$('#bitacora1').hide(0);
		popUpOper($(this),"trans");
	}
function formatoMontoJQ()
{	
	var CaretPos = 0;
	var numero = $("#Amount").val();
	var formatoMoneda = "";
	
	CaretPos = getPosition();
	
	numero = numero.replace('.','');
	numero = numero.replace(',','');	
	numero = numero.replace(',','');
	numero = numero.replace(',','');
	
	if ( checkNum(numero) == 0 ) numero = numero.substr(0,numero.length-1);
	
	if ( ( ( document.all ) ? event.keyCode : event.which) == 8 ){
		switch(numero.length)
		{
			case 1:  formatoMoneda = numero; break;
			case 2:  formatoMoneda = numero; CaretPos-=1; break;
			case 3:  formatoMoneda = numero.substr(0,1)+"."+numero.substr(1,2); break;
			case 4:  formatoMoneda = numero.substr(0,2)+"."+numero.substr(2,2); break;
			case 5:  formatoMoneda = numero.substr(0,3)+"."+numero.substr(3,2); CaretPos-=1; break;
			case 6:  formatoMoneda = numero.substr(0,1)+","+numero.substr(1,3)+"."+numero.substr(4,2); break;		
			case 7:  formatoMoneda = numero.substr(0,2)+','+numero.substr(2,3)+'.'+numero.substr(5,2); break;
			case 8:  formatoMoneda = numero.substr(0,3)+','+numero.substr(3,3)+'.'+numero.substr(6,2); CaretPos-=1; break;
			case 9:  formatoMoneda = numero.substr(0,1)+','+numero.substr(1,3)+','+numero.substr(4,3)+'.'+numero.substr(7,2); break;
			case 10: formatoMoneda = numero.substr(0,2)+','+numero.substr(2,3)+','+numero.substr(5,3)+'.'+numero.substr(8,2); break; 
			case 11: formatoMoneda = numero.substr(0,3)+','+numero.substr(3,3)+','+numero.substr(6,3)+'.'+numero.substr(9,2); CaretPos-=1; break;
			case 12: formatoMoneda = numero.substr(0,1)+','+numero.substr(1,3)+','+numero.substr(4,3)+','+numero.substr(7,3)+'.'+numero.substr(10,2); break;
			case 13: formatoMoneda = numero.substr(0,2)+','+numero.substr(2,3)+','+numero.substr(5,3)+','+numero.substr(8,3)+'.'+numero.substr(11,2); CaretPos-=1; break;
		}
	} else {
		if (((document.all) ? event.keyCode : event.which) == 37 || ((document.all) ? event.keyCode : event.which) == 38 || ((document.all) ? event.keyCode : event.which) == 39 || ((document.all) ? event.keyCode : event.which) == 40 ){
			switch(numero.length)
			{	
				case 1:  formatoMoneda = numero; break;
				case 2:  formatoMoneda = numero; break;
				case 3:  formatoMoneda = numero.substr(0,1)+'.'+numero.substr(1,2); break;
				case 4:  formatoMoneda = numero.substr(0,2)+'.'+numero.substr(2,2); break;
				case 5:  formatoMoneda = numero.substr(0,3)+'.'+numero.substr(3,2); break;
				case 6:  formatoMoneda = numero.substr(0,1)+','+numero.substr(1,3)+'.'+numero.substr(4,2); break;
				case 7:  formatoMoneda = numero.substr(0,2)+','+numero.substr(2,3)+'.'+numero.substr(5,2); break;
				case 8:  formatoMoneda = numero.substr(0,3)+','+numero.substr(3,3)+'.'+numero.substr(6,2); break;
				case 9:  formatoMoneda = numero.substr(0,1)+','+numero.substr(1,3)+','+numero.substr(4,3)+'.'+numero.substr(7,2); break;
				case 10: formatoMoneda = numero.substr(0,2)+','+numero.substr(2,3)+','+numero.substr(5,3)+'.'+numero.substr(8,2); break;
				case 11: formatoMoneda = numero.substr(0,3)+','+numero.substr(3,3)+','+numero.substr(6,3)+'.'+numero.substr(9,2); break;
				case 12: formatoMoneda = numero.substr(0,1)+','+numero.substr(1,3)+','+numero.substr(4,3)+','+numero.substr(7,3)+'.'+numero.substr(10,2); break;
				case 13: 
				case 14: 
				case 15:
				case 16: 
				case 17: formatoMoneda = numero.substr(0,2)+','+numero.substr(2,3)+','+numero.substr(5,3)+','+numero.substr(8,3)+'.'+numero.substr(11,2); break;
			}
		} else {
			switch(numero.length)
			{	
				case 1:  formatoMoneda = numero; break;
				case 2:  formatoMoneda = numero; break;
				case 3:  formatoMoneda = numero.substr(0,1)+'.'+numero.substr(1,2); CaretPos+=1; break;
				case 4:  formatoMoneda = numero.substr(0,2)+'.'+numero.substr(2,2); break;
				case 5:  formatoMoneda = numero.substr(0,3)+'.'+numero.substr(3,2); break;
				case 6:  formatoMoneda = numero.substr(0,1)+','+numero.substr(1,3)+'.'+numero.substr(4,2); CaretPos+=1;break;
				case 7:  formatoMoneda = numero.substr(0,2)+','+numero.substr(2,3)+'.'+numero.substr(5,2); break;
				case 8:  formatoMoneda = numero.substr(0,3)+','+numero.substr(3,3)+'.'+numero.substr(6,2); break;
				case 9:  formatoMoneda = numero.substr(0,1)+','+numero.substr(1,3)+','+numero.substr(4,3)+'.'+numero.substr(7,2);CaretPos+=1; break;
				case 10: formatoMoneda = numero.substr(0,2)+','+numero.substr(2,3)+','+numero.substr(5,3)+'.'+numero.substr(8,2); break;
				case 11: formatoMoneda = numero.substr(0,3)+','+numero.substr(3,3)+','+numero.substr(6,3)+'.'+numero.substr(9,2); break;
				case 12: formatoMoneda = numero.substr(0,1)+','+numero.substr(1,3)+','+numero.substr(4,3)+','+numero.substr(7,3)+'.'+numero.substr(10,2); CaretPos+=1; break;
				case 13: 
				case 14: 
				case 15:
				case 16: 
				case 17: formatoMoneda = numero.substr(0,2)+','+numero.substr(2,3)+','+numero.substr(5,3)+','+numero.substr(8,3)+'.'+numero.substr(11,2); break;
			}
		}
	}
	
	$("#Amount").val(formatoMoneda);
}

function getPosition(){
	var CaretPos = 0;
	var numero = $("#Amount").val();
	
	if (document.selection) { 
		$("#Amount").focus();
		var Sel = document.selection.createRange (); 
		Sel.moveStart ('character', -numero.length); 
		CaretPos = Sel.text.length;
	}

	else if ($("#Amount").selectionStart || $("#Amount").selectionStart == '0')
		CaretPos = $("#Amount").selectionStart;
 
	return (CaretPos);
}
function setPosition(pos){
	if($("#Amount").setSelectionRange){
		$("#Amount").focus();
		$("#Amount").setSelectionRange(pos,pos);
	}
	else if ($("#Amount").createTextRange) {
		var range = $("#Amount").createTextRange();
		range.collapse(true);
		range.moveEnd('character', pos);
		range.moveStart('character', pos);
		range.select();
	}
}
function checkNum(data){
	var valid = "0123456789.";
	var ok = 1; var checktemp;
	for ( var i=0; i<data.length; i++ ){
		checktemp = "" + data.substring(i, i+1);
		if ( valid.indexOf(checktemp) == "-1" ) return 0;
	}
	return 1;
}



function closeConfirm (dialog) {
	dialog.data.fadeOut(200, function () {
		dialog.container.hide(200, function () {
			dialog.overlay.slideUp(200, function () {
				$.modal.close();
			});
		});
	});
	$("#busquedaDestinatario").val("");
}
function closeConfirmDestino (dialog) {
	$.modal.close();
	return false;
}

function cambiaCuentasDestino(cte,cta,sub,nom,tipo)
{
	var destino = '';
	var destino2 = '';
	destino = destino.concat(cte);
	destino = destino.concat(cta);
	destino = destino.concat(" ");
	destino = destino.concat("-");
	destino = destino.concat(" ");
	destino = destino.concat(sub);

	destino2 = destino;

	destino2 = destino2.concat("-");
	destino2 = destino2.concat(tipo);
	destino2 = destino2.concat(nom);

	$("#ToAccount").val(destino);
	$("#ToAccount2").val(destino2);

	return false;
}


function gridDestino()
{
var userid ="";
	jQuery().ready(
				function ()
				{
					cliente   = $('#Cliente').val();
					userid = $("#CustID").val();
					var hash  = $("#hash").val();					
					dialog 	  = $('#modalDestino');
					$("#error").empty();

				jQuery("#catalogocuentas").jqGrid (
					{
						 url:'/procesarAjaxMiddlewareGrid.php?ws=catalogo&modulo=trn&servicio=destinoAmigable&Cliente='+cliente+'&CustID='+userid+"&hash="+hash,
						datatype: "xml",
						colNames:['Cliente','Producto','Sub', 'Nombre', 'Alias','Tipo','Rowno'],
						colModel:[
								{name:'cliente',index:'cliente', width:90},
								{name:'producto',index:'clabe', width:90},
								{name:'sub',index:'sub', width:30},
								{name:'nombre',index:'nombre', width:270},
								{name:'alias',index:'alias', width:110},
								{name:'tipo',index:'tipo',width:80},
								{name:'rowno',index:'rowno',hidden: true, width:100},
							],
						rowNum:20, 
						height: 'auto',
						autowidth: true,
						pager: jQuery('#pagerCatalogoCuentas'),
						sortname: 'id',
						viewrecords: true,
						sortorder: "desc",
						emptyrecords: "No existen cuentas activas",
						pgbuttons: true,
						onSelectRow: function(id){
							var cuentas = '';
						      var cliente = jQuery("#catalogocuentas").jqGrid('getCell', id, 'cliente');
						      var cuenta = jQuery("#catalogocuentas").jqGrid('getCell', id, 'producto');
						      var sub = jQuery("#catalogocuentas").jqGrid('getCell', id, 'sub');
						      var tipo    = jQuery("#catalogocuentas").jqGrid('getCell', id, 'tipo');
						      var nombre  = jQuery("#catalogocuentas").jqGrid('getCell', id, 'nombre');

						     if ( tipo == "Agrupada" )
						     		tipo="gpo-";
							else
									tipo=" ";

						      closeConfirmDestino(dialog);
						      cambiaCuentasDestino(cliente,cuenta,sub,nombre,tipo);
   						},
   						loadError: function(response){  							
   							
   							$("#error").empty();
	            			$("#error").append(response.responseText);

   						},
						caption:"Elija la cuenta destino de su transferencia"
					} )
				 }
		)
	jQuery("#cancelar").click( function()
				{
					var s,cuentas='';

					s = jQuery("#catalogocuentas").jqGrid('getGridParam','selarrrow');

					 			var cte = jQuery("#catalogocuentas").jqGrid('getCell', 1, 'rowno');

					 			$.each(s, function (ind, elem) {

					 				cuentas = cuentas.concat(jQuery("#catalogocuentas").jqGrid('getCell', elem, 'cliente') );
					 				cuentas = cuentas.concat("-");
					 				cuentas = cuentas.concat(jQuery("#catalogocuentas").jqGrid('getCell', elem, 'rowno') );
					 				cuentas = cuentas.concat("-");
					 				cuentas = cuentas.concat(jQuery("#catalogocuentas").jqGrid('getCell', elem, 'producto') );
					 				cuentas = cuentas.concat("-");
					 				cuentas = cuentas.concat(jQuery("#catalogocuentas").jqGrid('getCell', elem, 'sub') );
					 				cuentas = cuentas.concat(",");
					     			});
					 			CancelarCuenta(cuentas);
			});

	jQuery("#btnModalBuscar").click( function() {
		var busqueda		 = $("#busquedaDestinatario").val();
		var cliente			 = $("#Cliente").val();
		var userid			 = $("#CustID").val();
		var hash  			 = $("#hash").val();
		$('#catalogocuentas').jqGrid('setGridParam',
							{
									url: '/procesarAjaxMiddlewareGrid.php?ws=catalogo&modulo=trn&servicio=destinoAmigable&Cliente=' + cliente + '&busqueda='+ busqueda + '&CustID=' + userid+"&hash="+hash,
									page:1
							}).trigger("reloadGrid");
	});

	//OVVC [INFO] => LIBRERÍAS POR SESIÓN [DATE] => 20022015
	jQuery("#btnModalResetconftrn").click( function() {
			var busqueda		 = $("#busquedaDestinatario").val();
			var cliente			 = $("#Cliente").val();
			var userid			 = $("#CustID").val();
			var hash  			 = $("#hash").val();

			$('#catalogocuentas').jqGrid('setGridParam',
								{
										  url:'/procesarAjaxMiddlewareGrid.php?ws=catalogo&modulo=trn&servicio=destinoAmigable&Cliente='+cliente+'&CustID='+userid+"&hash="+hash
								}).trigger("reloadGrid");
			$("#busquedaDestinatario").val("");
	});
}
function buscarCuenta(cta)
{
	 $.ajax({
	        type: "POST",
	        url: "/procesarAjaxMenu.php",
	          async:false,
	          cache:false,
	          data: "Access=buscarCta&nomFunc=busqcta&parametro=Cuenta:" + cta,
		        success: function(msg){
	            $("#error").empty();
	            $("#error").append(msg);
	            resp = $('#error p').text();
		        }
	       });
	  if ( resp != "" )
	  {
	    return false;
	  }
	  else
	  {
	    return true;
  	}
}

function cancela()
{
	$.modal.close();
}

function muestraBtn()
{
	$('#btnCambiar').hide(0);
}
//OVVC [INFO] => LIBRERÍAS POR SESIÓN [DATE] => 20022015
$('#btnModalCierraconftrn').click(function(){
	cancela();
});
</script>

<div id="modalDestino" style='display:none' align='center'>

	<!--div style="width:750; height:320px; align:center;"-->
	<div style="width:750; height:550px; align:center;">
	<div class='ui-jqgrid-titlebar ui-widget-header ui-corner-top ui-helper-clearfix' style='width:auto; height:20px;'>
	<span class='ui-jqgrid-title' style='font-size:12px;valign:left;' >&nbsp;Cat&aacute;logo de Cuentas</span></div>
		<!--/br>
			<div><!--Buscar :>
						</div-->
						<br>
			<div id='error' style='font-size: 11px; color:red;' ></div>
			<div id='resCuentas' align='center'>
				<div id="btnBuscar" style="width:750; height:15px; position: relative; left:34%;">
					<input Class="tooltip" type="input"  size=12 maxlength=12 autocomplete="off" id="busquedaDestinatario" mensaje="Puede filtrar sus cuentas por cliente, producto, nombre o tipo." />
					<input Class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only"  type="button"  id="btnModalBuscar" value ="Buscar" text="Buscar" onclick="javascript: if(isNumberTk($('#codeTkArchivo3').val())) {continuaPago('1','2','3',$('#codeTkArchivo3').val(),$('#cuentas').val());} else {$('#codeTkArchivo3').val(''); $('#auxArcTk').html('El n&uacute;mero de Clave ASB que ha ingresado es incorrecto, intentelo de nuevo.');} " />
			    </div>

				<br>
				<table align="center" id='catalogocuentas'  border=1 ></table>
				<div id="pagerCatalogoCuentas"></div>
				<br>
				<div id='botones' style="width:750; height:20px; position: relative; left:38%;">
					<input Class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" type="button"  id="btnModalCierraconftrn" value ="Cancelar" text="Cancelar" /><!-- //OVVC [INFO] => LIBRERÍAS POR SESIÓN [DATE] => 20022015 -->
					<input Class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" type="button"  id="btnModalResetconftrn" value ="Limpiar" text="Limpiar"/><!-- //OVVC [INFO] => LIBRERÍAS POR SESIÓN [DATE] => 20022015 -->
			    </div>

			</div>

	</div>

</div>
<br>
<input type="HIDDEN" id="Cliente" name="Cliente" value="<?php echo  $session->variable( 'r_cno' );?>">
<input type="HIDDEN" id="hash" name="hash" value="<?php echo  md5($session->variable( 'r_cno' )."|".strtolower(trim($session->variable( 'r_usr' )))."|"."Trn_$$"); ?>">

<!-- DBA ReingenieriaBajioNET -->
<?php
// Optimizacion Catalogo (TO) desde WebServices - Inicio [1/4]
$optionsOrigen= "";
$DatFis = "";	// JAC NXN 26NOV2012
//CRA - 08jul2013 Reingenieria Bajionet - Ini
$JQueryTrans = $ini->read_var("site" , "JQueryTrans");
If($JQueryTrans == 1)
{?>
	<script>
		$(function() {
		  $('#btnCambiar').hide(0);
		  $('#btnAceptar').hide(0);
		  $('#aceptaTransfer').show(0);
		  $("input").removeAttr("onkeypress");
		});
	</script>
<?php
}
else
{
?>
	<script>
		$(function() {
		  $('#btnCambiar').show(0);
		  $('#btnAceptar').show(0);
		  $('#aceptaTransfer').hide(0);
		});
	</script>
<?php
}
//CRA - 08jul2013 Reingenieria Bajionet - Fin


function write_journal($msg) {
	if($debugON == 1) {
		eZLog::writeNotice($msg);
	}
}

function get_include_contents($filename) {
    if (is_file($filename)) {
        ob_start();
        include $filename;
        return ob_get_clean();
    }
    return false;
}
//NXN 04Jun2012 - Correccion - Inicio
function specialCharsReplacementXML($val) {
	if ($this->charencoding) {
		$val = str_replace("'", ' ', $val);
		$val = str_replace('"', " ", $val);
		$val = str_replace('&', '*', $val);
		$val = str_replace(chr(241), "$", $val);
		$val = str_replace(chr(209), "%", $val);
	}
	return $val;
}

function specialCharsReplacementHTML($val) {
	if ($this->charencoding) {
		$val = str_replace("*", "&", $val);
		$val = str_replace("$",chr(241), $val);
		$val = str_replace("%",chr(209), $val);
	}
	return $val;
}
function buscaError($str)
{
	$existeError3254 = strpos($str,"<!--Código: (3254)-->");
	$existeError3053 = strpos($str,"<!--Código: (3053)-->");

//eZLog::writeNotice ("Valor del 3254...[$existeError3254]");	MAOS Oct2013 Quitar Notice	
//eZLog::writeNotice ("Valor del 3053...[$existeError3053]");	MAOS Oct2013 Quitar Notice

	if ( $existeError3254 === false && $existeError3053 === false) //no existe, no hay error
		return $existeError3254 = "no";

	/*if ( $existeError3053 === false)
		return $existeError3053 = "no";
		*/

return "si";
}
//NXN 04Jun2012 - Correccion - Fin
function consultaCuentasCliente($tipoCtaConsulta, &$DatFis) {
	require_once( "nusoap-0.7.3/lib/nusoap.php");
	include_once( "classes/INIFile.php" );
	include_once( "classes/ezlogdb.php" );
	include_once( "classes/ezlog.php" );
	include_once( "ezsession/classes/ezsession.php" );
	include_once( "eztransaccion/user/include/xmlparser.inc");

	$session =& eZSession::globalSession();
	if (!$session->fetch()) {
		$session->store();
	}

	$ini =& $GLOBALS["GlobalSiteIni"];

	// Preparando los datos para enviar al Web Service
	//eZLog::writeNotice( "=============================================>>> Arma array con los parametros a enviar al web service via nusoap" );
	$data1 = "<trxn><custID value='".$session->variable( "r_cno" )."'/><userID value='".$session->variable( "r_usr" )."'/></trxn>" ;
	//eZLog::writeNotice( "=============================================>>> Parametros enviados: " . $data1);
	$param = array('xmlRequest'=>$data1);
	//eZLog::writeNotice( "=============================================>>> TRXN [".$param."]");

	// Se obtienen variables de ambiente para armar WSDL
	$acctWSDLPath = $ini->read_var("site", "AcctWS");
	$acctWSDLPort = $ini->read_var("site", "AcctWSPort");
	$acctWSDLTimeout = $ini->read_var("site", "AcctWSTimeout");

	// Armando WSDL para invocar Web Service en Servidor 1
	$acctWSDL = "http://". $ini->read_var("site", "JBossHost") . ":" . $acctWSDLPort . $acctWSDLPath; // NXS 222809
	$client = new nusoap_client($acctWSDL, 'wsdl', false, false, false, false, 0, $acctWSDLTimeout);
	//eZLog::writeNotice( "=============================================>>> Llamo al webservice, URL : ".$acctWSDL);

	//NXN 04Jun2012 - Correccion - Inicio
	$LogBB = new eZLogDB ();		// JAC JUN2011
	$LogBB->StoreTime();			// JAC JUN2011
	$useLogBB = $ini->read_var( "site", "useLogBB" );
	$TrxnLogBB = 'acct_trn';
	$AccessLogBB = 'qry';
	//NXN 04Jun2012 - Correccion - Fin

	// Invocacion del Web Service en Servidor 1
	if($tipoCtaConsulta=='CTASORIGEN') {
		$acctResponse = $client->call('getAccountsOut', $param);
	} else {
		$acctResponse = $client->call('getAccountsIn', $param);
	}

	// Validacion de la respuesta del Web Service en Servidor 1
	if ($acctResponse == false) {
		// Ha fallado la invocacion al Web Service en Servidor 1, se prepara invocacion al Servidor 2 (respaldo)
		//eZLog::writeNotice("=============================================>>> Fallo invocacion en Servidor 1 [" . $acctWSDL . "]");	MAOS Oct2013 Quitar Notice
		$acctWSDL = "http://". $ini->read_var("site", "JBossBackup") . ":" . $acctWSDLPort . $acctWSDLPath; // NXS 222809
		$client = new nusoap_client($acctWSDL, 'wsdl', false, false, false, false, 0, $acctWSDLTimeout);

		// Invocacion del Web Service en Servidor 2
		//eZLog::writeNotice( "=============================================>>> Llamo al webservice desde servidor 2, URL : ".$acctWSDL);	MAOS Oct2013 Quitar Notice
		if($tipoCtaConsulta=='CTASORIGEN') {
			$acctResponse = $client->call('getAccountsOut', $param);
		} else {
			$acctResponse = $client->call('getAccountsIn', $param);
		}
	}

	if ($acctResponse == false) {
		// Ha fallado la invocacion al Web Service en Servidor 1 y 2, se retorna falso para mostrar mensaje correspondiente
		//eZLog::writeNotice("=============================================>>> Fallo invocacion en Servidor 2 [" . $acctWSDL . "]");	MAOS Oct2013 Quitar Notice
		return false;
	}

	//eZLog::writeNotice("=============================================>>> Consulta Exitosa de Web Service!!! [" . $acctWSDL . "]");

	//NXN 04Jun2012 - Correccion - Inicio
	//$LogBB = new eZLogDB ();		// JAC JUN2011
	//$LogBB->StoreTime();			// JAC JUN2011
	//$useLogBB = $ini->read_var( "site", "useLogBB" );
	//$TrxnLogBB = 'acct_trn';
	//$AccessLogBB = 'qry';

	//$xml_struct = parse_xml(trim($acctResponse['return']['response']));

	$WSrespuestaXML = "";
	$WSrespuestaXML = specialCharsReplacementXML(trim($acctResponse['return']['response']));

	$xml_struct = parse_xml(trim($WSrespuestaXML));
	//NXN 04Jun2012 - Correccion - Fin

	//eZLog::writeNotice(":::::XML_STRUCT[".print_r($xml_struct,true)."]:::::");
	if ( trim($xml_struct["vals"][$xml_struct["index"]["STATUS"][0]]["attributes"]["VALUE"]) != "OK" ) {
		$resultDescription =  trim($xml_struct["vals"][$xml_struct["index"]["MSG"][0]]["attributes"]["VALUE"]);
		if ( $useLogBB === "SI" ) {
			$LogBB->write ( $TrxnLogBB, $AccessLogBB, "[ ".$resultDescription." ] : [ -1 ]" );
		}
		return 0;
	} else {
		if ( $useLogBB === "SI" ) {
			$LogBB->write ( $TrxnLogBB, $AccessLogBB, "[ Transaccion OK ] : [ 0 ]" );
		}
	}
	// JAC NXN 26NOV2012 INI $tipoCtaConsulta=='CTASORIGEN'
	if ( $tipoCtaConsulta=='CTASORIGEN') {
		$DatFis = trim($xml_struct["vals"][$xml_struct["index"]["DATFIS"][0]]["attributes"]["VALUE"]);
		//eZLog::writeNotice( "::::::::::::::DatFis[$DatFis]:::::::::::::::::" ); MAOS Oct2013 Quitar Notice
	}
	// JAC NXN 26NOV2012 FIN
	$optionsResult= "";
	foreach ($xml_struct["index"]["CUENTA"] as $key=>$val) {
		$xmlNodeTipoCliente = "";
		$xmlNodeTipoCuenta = "";
		$xmlNodeTipoCuentaAgrupado = "";

		 if($xml_struct["vals"][$val]["attributes"]['TIPOCLIENTE']=='Agrupado' or $tipoCtaConsulta=='CTASDESTINO') {
			$xmlNodeTipoCliente = $xml_struct["vals"][$val]["attributes"]['NROCLIENTE'];
		 }
		 if($xml_struct["vals"][$val]["attributes"]['TIPOCUENTA']=='PZO') {
			$xmlNodeTipoCuenta = "Plazo";
		 } else{
			$xmlNodeTipoCuenta = $xml_struct["vals"][$val]["attributes"]['TYPE'];
		 }

		 if ($xml_struct["vals"][$val]["attributes"]['TIPOCLIENTE']=='Agrupado') {
			//if ($tipoCtaConsulta=='CTASORIGEN') { //NXN 30jul2012
			$xmlNodeTipoCuentaAgrupado = " - "."gpo - ";
			//} else {  //NXN 30jul2012
			//	$xmlNodeTipoCuentaAgrupado = " - "; //NXN 30jul2012
			//}		 //NXN 30jul2012
		 }

		 if($xml_struct["vals"][$val]["attributes"]['TIPOCLIENTE']=='Agrupado' OR $xml_struct["vals"][$val]["attributes"]['TIPOCLIENTE']=='Cuenta'){
			//NXN 04Jun2012 - Correccion - Inicio
			//$xmlNodeNombreCliente = $xml_struct["vals"][$val]["attributes"]['NOMBRECLIENTE'];
			$xmlNodeNombreCliente = specialCharsReplacementHTML($xml_struct["vals"][$val]["attributes"]['NOMBRECLIENTE']);
			//NXN 04Jun2012 - Correccion - Fin
		 } else{
		 	$xmlNodeNombreCliente = "";
		 }

		 //if($xmlNodeTipoCuenta != 'cheqsc') {  // Optimizacion Catalogo (TO) desde WebServices - Inicio [11May2012]
			$optionsResult= $optionsResult .
				 "<OPTION VALUE='".
					 $xmlNodeTipoCliente .
					 $xmlNodeTipoCuenta .
					 " - ".
					 $xml_struct["vals"][$val]["attributes"]['SUB'].
					 "'>".
					 $xml_struct["vals"][$val]["attributes"]['NROCLIENTE'].
					 $xmlNodeTipoCuenta .
					 " - ".
					 $xml_struct["vals"][$val]["attributes"]['SUB'].
					 $xmlNodeTipoCuentaAgrupado . " " .
					 $xmlNodeNombreCliente.
				 "</OPTION>";
			//eZLog::writeNotice( "=============================================>>> OPTION [".$optionsResult."]");
		//} // Optimizacion Catalogo (TO) desde WebServices - Inicio [11May2012]
	}


	if ($client->fault) {
		//eZLog::writeNotice( "FAULT CONNECTION - No se pudo completar la operacion - WS service: getAccounts - URL " . $acctWSDL);	MAOS Oct2013 Quitar Notice
		return -1;
	} else {
		$error = $client->getError();
		if( $error ) {
			//eZLog::writeNotice( "ERROR CONNECTION - No se pudo completar la operacion - WS service: getAccounts - URL " . $acctWSDL);	MAOS Oct2013 Quitar Notice
			return -2;
		}
	}
	//eZLog::writeNotice( "=============================================>>> SALIDA Llamada al webservice");
	return $optionsResult;
}
// Optimizacion Catalogo (TO) desde WebServices - Fin [1/4]
?>

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
($parametros['Desc']!=""?$Desc=$parametros['Desc']:0);
($parametros['Estado']!=""?$Estado=$parametros['Estado']:0);
($parametros['FrAccount']!=""?$FrAccount=$parametros['FrAccount']:0);
($parametros['ToAccount']!=""?$ToAccount=$parametros['ToAccount']:0);
($parametros['Day']!=""?$Day=$parametros['Day']:0);
($parametros['Month']!=""?$Month=$parametros['Month']:0);
($parametros['RenInt']!=""?$RenInt=$parametros['RenInt']:0);
($parametros['NombreB']!=""?$NombreB=$parametros['NombreB']:0);
($parametros['RFC']!=""?$RFC=$parametros['RFC']:0);
($parametros['DayF']!=""?$DayF=$parametros['DayF']:0);
($parametros['Comprobante']!=""?$Comprobante=$parametros['Comprobante']:0);
($parametros['code']!=""?$code=$parametros['code']:0);
$hbltDestinoAmigable = $ini->read_var( "site", "hbltDestinoAmigable" ); //DBA Reingenieria BajioNET - Destino Amigable
$hbltAltaCtas = $ini->read_var( "site", "hbltAltaCtas" ); //DBA Reingenieria BajioNET - Destino Amigable
$t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
					 "eztransaccion/user/intl/", $Language, "transferencia.php" );

$t->setAllStrings();

// AGG I 25Nov2005 Para poner fecha y hora en los comprobantes
$dateTime  = new eZDateTime( );
$timeStamp = $dateTime->timeStamp();
$time =& date('H:i:s', $timeStamp );
$date =& date('jMY', $timeStamp );
// AGG F 25Nov2005 Para poner fecha y hora en los comprobantes

$t->set_file( array(
	"transferencia_tpl" => "transferencia.tpl"
	) );


if(empty($Access)) {
	$Access = "FrAc";
}
// $Accion = "transferencia";
if( !isset /*(HB AGL - Ajustes PHP5)*/( $Amount ) ) {
	$Amount = "";
}
if( !isset /*(HB AGL - Ajustes PHP5)*/( $DiasPzo ) ) {
	$DiasPzo = "";
}
if( !isset /*(HB AGL - Ajustes PHP5)*/( $FrAccount ) ) {
	$FrAccount = "";
}
if( !isset /*(HB AGL - Ajustes PHP5)*/( $ToAccount ) ) {
	$ToAccount = "";
}
if( !isset /*(HB AGL - Ajustes PHP5)*/( $RFC ) ) {
	$RFC = "";
}
if( !isset /*(HB AGL - Ajustes PHP5)*/( $RenInt ) ) {
	$RenInt = "";
}
if( isset /*(HB AGL - Ajustes PHP5)*/( $Desc ) ) {
   $Desc = preg_replace("/</", " ", $Desc );	 /* HB AGL*/
   $Desc = preg_replace("/>/", " ", $Desc );	 /* HB AGL*/
   $Desc = preg_replace("/&/", "-", $Desc );	 /* HB AGL*/
}

 switch($Access) {
case "FrAc":
	$particularFields = "";
	$tr->blog($qki,"Transferencia", $FrAccount, $ToAccount, $DiasPzo, $Amount, $Accion);
	break;
case "Confirm":
	$particularFields = "&Day=".urlencode($Day)."&Month=".urlencode($Month)."&FrAccount=".urlencode($FrAccount)."&ToAccount=".urlencode($ToAccount)."&Amount=".urlencode($Amount)."&Desc=".urlencode(preg_replace("/&/", "-", $Desc))."&RFC=".urlencode($RFC)."&RenInt=".urlencode($RenInt)."&Estado=".$Estado; /* HB AGL*/
	$tr->blog($qki,"Transferencia", $FrAccount, $ToAccount, $DiasPzo, $Amount, $Accion);
	break;
case "Process":
	$particularFields = "&Passwd=".encrypt( $code, strtolower( $usr ) )."&Day=".urlencode($Day)."&Month=".urlencode($Month)."&FrAccount=".urlencode($FrAccount)."&ToAccount=".urlencode($ToAccount)."&Amount=".urlencode($Amount)."&Desc=".urlencode(preg_replace("/&/", "-", $Desc))."&RFC=".urlencode($RFC)."&RenInt=".urlencode($RenInt)."&Estado=".$Estado;  /* HB AGL*/
	$tr->blog($qki,"Transferencia", $FrAccount, $ToAccount, $DiasPzo, $Amount, $Accion);
	break;
}
$transaccion_buffer = "";

// Optimizacion Catalogo (TO) desde WebServices - Inicio [3/4]
$AcctFromOMNI = $ini->read_var( "site", "AcctFromOMNI" );

if($AcctFromOMNI == 0 and $Access=='FrAc') {

		// Se invoca Web Service para obtener cuentas origen del cliente
		//eZLog::writeNotice("=============================================>>> Invocacion WS para cuentas Origen");
		$cuentasOrigenAsociadas = consultaCuentasCliente('CTASORIGEN', $DatFis);

		// Si hubo error en WS para cuentas Origen, no se invoca WS para cuentas destino
		if ($cuentasOrigenAsociadas != false) {
			// Solo si no hubo errores en WS para cuentas origen se invoca WS para cuentas destino
			//eZLog::writeNotice("=============================================>>> Invocacion WS para cuentas Destino");
			$cuentasDestinoAsociadas = consultaCuentasCliente('CTASDESTINO', $DatFis);
		}

		if ($cuentasOrigenAsociadas == false || $cuentasDestinoAsociadas == false) {
			// Ha fallado la invocacion a Web Service en Servidor 1 o 2, Va por el esquema viejo de OMNI
			//eZLog::writeNotice("=============================================>>> Fallo invocacion de Web Service en Servidor 1 o 2");
			$transaccion_buffer = $transaccion_buffer. get_include_contents( "statics/trn1.hdr" );
			$transaccion_buffer = $transaccion_buffer. get_include_contents( "statics/trn1err.ftr" );
			return;
		} else {
			// Sin errores en la invocacion de Web Service en Servidor 1 o 2
			//eZLog::writeNotice( "=============================================>>> INVOCACION EXITOSA DE WEB SERVICE!");

			$transaccion_buffer = $transaccion_buffer. get_include_contents( "statics/trn1.hdr" );

			$transaccion_buffer = $transaccion_buffer. " <TR>"; //EndTD //NXN trackID 224449
			$transaccion_buffer = $transaccion_buffer. " <TD VALIGN='RIGHT' COLSPAN=4> "; //htmlTableColRight
			$transaccion_buffer = $transaccion_buffer. " <SELECT NAME='FrAccount'> "; //htmlBeginSelect
			$transaccion_buffer = $transaccion_buffer. $cuentasOrigenAsociadas; //BuildAccountFr

			$transaccion_buffer = $transaccion_buffer. " </SELECT>"; //EndSelect
			$transaccion_buffer = $transaccion_buffer. " </TD> </TR>"; //EndTD //NXN trackID 224449
			$transaccion_buffer = $transaccion_buffer. " <TR> <TD><BR/><BR/> </TD></TR> ";//NXN trackID 224449
			$transaccion_buffer = $transaccion_buffer. " <TR> <TH>Cuenta Destino</TH> </TR> "; //NXN trackID 224449
			$transaccion_buffer = $transaccion_buffer. " <TR> <TD VALIGN='RIGHT' COLSPAN=4> "; //htmlTableColRight //NXN trackID 224449
			$transaccion_buffer = $transaccion_buffer. " <SELECT NAME='ToAccount'> "; //htmlBeginSelect
			$transaccion_buffer = $transaccion_buffer. $cuentasDestinoAsociadas; //BuildAccountFr
			$transaccion_buffer = $transaccion_buffer. " </SELECT>"; //EndSelect
			// $transaccion_buffer = $transaccion_buffer. " </TD>"; //EndTD //NXN trackID 224449

			$transaccion_buffer = $transaccion_buffer. " </TD></TR> ";
			$transaccion_buffer = $transaccion_buffer. " <TR> <TD ALIGN=LEFT COLSPAN=4><BR><b>Monto</b></BR></TD></TR>";
			$transaccion_buffer = $transaccion_buffer. " <TR> <TD ALIGN=LEFT COLSPAN=4> <INPUT TYPE=TEXT NAME=Amount SIZE=11 MAXVALUE=11 onFocus='this.value=WithOutFormatAmount(this.value);focus();select();' onblur='this.value=FormatAmount(this.value);'>";

			$transaccion_buffer = $transaccion_buffer. " </TD></TR> ";
			// JAC NXN 26NOV2012 INI
			// $transaccion_buffer = $transaccion_buffer. " <TR> <TD><BR/><BR/> </TD></TR> ";
			if ($DatFis=="si") {
				//eZLog::writeNotice("::::: ARMA PAGINA HTML DATFIS[$DatFis]::::::");	MAOS Oct2013 Quitar Notice
				$transaccion_buffer .= "<TR><TD ALIGN=LEFT>";
				$transaccion_buffer .= "<BR><b> RFC del Beneficiario</b></BR></TD>";
				$transaccion_buffer .= "<TD ALIGN=CENTER><BR><b> IVA del Beneficiario</b><BR/></TD></TR>";
				$transaccion_buffer .= "<TR><TD>";
				$transaccion_buffer .= "<INPUT TYPE=TEXT NAME=RFC SIZE=13 MAXLENGTH=13>";
				$transaccion_buffer .= "</TD><TD ALIGN=CENTER>";
				$transaccion_buffer .= "<INPUT TYPE=TEXT NAME='RenInt' SIZE=10 MAXVALUE=10 onFocus='this.value=WithOutFormatAmount(this.value);focus();select();' onBlur='this.value=FormatAmount(this.value);'>";
				$transaccion_buffer .= "</TD></TR>";
			}
			else {
				$transaccion_buffer = $transaccion_buffer. " <TR> <TD><BR/><BR/> </TD></TR> ";
			}
			// JAC NXN 26NOV2012 FIN

			$transaccion_buffer = $transaccion_buffer. get_include_contents( "statics/trn1.ftr" );
		}
} else {
	// Va por el esquema viejo de OMNI (AcctFromOMNI == 1)
	//eZLog::writeNotice(">>>>>>>>>>>>>>>>>>Por invocar el POST<<<<<<<<<<<<<<<<<<<<<<<<<<<<");	MAOS Oct2013 Quitar Notice
	$ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=trn&Access=".urlencode($Access)."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // transferencia
	
	// [AETG:20141024] Modificacion para que no se permitan transferencias entre cuentas de diferente moneda despues del horario back office.
	$existeErrorHorario = strpos($transaccion_buffer,"El horario para las Transferencias entre cuentas de diferentes");
	$existeErrorDiaInhabil = strpos($transaccion_buffer,"No es posible realizar Transferencias entre cuentas de diferentes monedas en d&iacute;as inh&aacute;biles.");
	
	if ($existeErrorHorario == true){
		$transaccion_buffer = "<P><B>Su transacci&oacute;n no pudo ser procesada.</B></P><P>El horario para las Transferencias entre cuentas de diferentes<br>monedas es de las 9:00 a las 17:00 horas.</P><P>Intente por favor de nuevo en este horario.</P>";
	}
	if ($existeErrorDiaInhabil == true){
		$transaccion_buffer = "<P><B>Su transacci&oacute;n no pudo ser procesada.</B></P><P>No es posible realizar Transferencias entre cuentas de diferentes monedas en d&iacute;as inh&aacute;biles.</P><P>Intente de nuevo en un d&iacute;a h&aacute;bil.</P>";
	}
	// [AETG:20141024]
	
//***** CRA 03jul2013 Reingenieria Bajionet - Ini *****
//***** CRA Se inserta html para darle tratamiento con 	Jquery Evita que cicle la pagina al usar Ajax en Transferencia
	if ( $Access == "Confirm" or $Access=="Process") // dgm - cra
	{
		$transaccion_buffer = "<!--BUSQUEDAI-->".$transaccion_buffer."<!--BUSQUEDAF-->"; // dgm<
		//$transaccion_buffer = "ELCRA".$transaccion_buffer."ELCRA2"; // dgm
	}
//***** CRA 03jul2013 Reingenieria Bajionet - Fin *****
}
// Optimizacion Catalogo (TO) desde WebServices - Fin [3/4]

// Esto se pasa para lo del destinatario
//if ( strtoupper(trim($hbltAltaCtas)) == "SI" )
//{

$errorClave = buscaError($transaccion_buffer);

//$existeError3254 = strpos($transaccion_buffer,"<!--Código: (3254)-->");
//$existeError3053 = strpos($transaccion_buffer,"<!--Código: (3053)-->");

	if ( $Access=='FrAc' && $hbltDestinoAmigable == "SI" &&  $errorClave == "no" )
	{

	//Deshabilitamos el select de omni

		$posInicial = strpos($transaccion_buffer,"<!--iniDestino-->");
		$posFinal = strpos($transaccion_buffer,"<!--finDestino-->");
		$transaccion_buffer =  substr($transaccion_buffer, 0, $posInicial) . substr($transaccion_buffer,$posFinal);

	//Agregamos un nuevo select

		$pos = strpos($transaccion_buffer,"Destino");
		$lugar = $pos + 7; //Tamaño de ToAccount
		//buena $insertar = "</br><input type='text' name='ToAccount' id='ToAccount' value='' size='46' maxlength='46' readonly> <input type='button' name='submitButton' id='consultaDestino' value='+' class='button' onmouseover=\"this.className='buttonON'\" onmouseout=\"this.className='button'\"/>";
		//$insertar = "</br><input type='hidden' name='ToAccount' id='ToAccount' class=\"combos\" mensaje=\"Elija la cuenta destino de la transferencia.\" onMouseOver=\"mostrarAyuda('ToAccount');\" onmouseout=\"rmvAyuda();\" value='' size='46' maxlength='46' readonly> <a href='#' id='consultaDestino'><img src='/images/lupa2.jpg' border=0></a>";
		$insertar = "</br><input type='hidden' name='ToAccount' id='ToAccount' value='' size='46' maxlength='46' readonly>";
		//$insertar2 = "</br><input type='text' name='ToAccount2' id='ToAccount2' class=\"combos\"  mensaje=\"Elija la cuenta destino de la transferencia.\" onMouseOver=\"mostrarAyuda('ToAccount2');\" onmouseout=\"rmvAyuda();\" value='' size='46' maxlength='46' readonly> <a href='#' id='consultaDestino'><img src='/images/lupa2.jpg' border=0></a>";
		$insertar2 = "</br><input type='text' name='ToAccount2' id='ToAccount2' class=\"combos\" style=\"cursor:pointer;\" mensaje=\"Elija la cuenta destino de la transferencia.\" onMouseOver=\"mostrarAyuda('ToAccount2');\" onmouseout=\"rmvAyuda();\" value='' size='46' maxlength='46' readonly> <a href='#' id='consultaDestino' style=\"position:relative;left:-25px;top:3px;\"><img src='/images/lupa2.jpg' border=0></a>"; //MAOS Cambio de lupa
		$transaccion_buffer = substr($transaccion_buffer, 0, $lugar) . $insertar .  $insertar2 . substr($transaccion_buffer, $lugar);
	}
//}
	//27Feb2015 - INI - Considerar privilegio Liga a cuentas de terceros.
	if($Access == 'FrAc'){
		$priv = "00000000000000000000000000000000000000000000000000";
		$priv = $session->variable( "r_priv" );
		$priv12 = substr($priv, 12, 1);
		$priv16 = substr($priv, 16, 1);
		$priv17 = substr($priv, 17, 1);
		eZLog::writeNotice(">>>>>>>>>>>>>>>>>>Priv: [$priv12][$priv16][$priv17]");
		if($priv12 != "1"){
			$transaccion_buffer = str_replace("<input type=\"button\" class=\"button\"  name=\"alta\" id=\"alta\" value=\"Alta de Cuentas\" onmouseover=\"this.className='buttonON'; mostrarAyuda('alta');\" onmouseout=\"this.className='button'; rmvAyuda();\" onclick=\"javascript:iniProcesarMenu('ligas','')\" mensaje=\"Presione este bot&oacute;n si desea crear o cancelar una liga a cuentas de terceros.\"/>", " ", $transaccion_buffer);
		}
	}
	//27Feb2015 - FIN - Considerar privilegio Liga a cuentas de terceros.

//DBA Reingenieria BajioNET - Destino Amigable
	$transaccion_buffer = str_replace("#_EUR_#", "&euro;", $transaccion_buffer);
	//****** JCR 10Jul2013 Inicio
	$transaccion_buffer = str_replace("ID=\"FrAccount\"", "ID=\"FrAccount\" class=\"combos\" mensaje=\"Elija la cuenta de la cual se realiza el retiro.\" onMouseOver=\"mostrarAyuda('FrAccount');\" onmouseout=\"rmvAyuda();\" ", $transaccion_buffer);
	$transaccion_buffer = str_replace("ID=\"ToAccount\"", "ID=\"ToAccount\" class=\"combos\" mensaje=\"Elija la cuenta destino de la transferencia.\" onMouseOver=\"mostrarAyuda('ToAccount');\" onmouseout=\"rmvAyuda();\" ", $transaccion_buffer);
	$transaccion_buffer = str_replace("NAME=\"Amount\"", "NAME=\"Amount\" ID=\"Amount\" mensaje=\"Defina el monto a transferir (use el punto como separador decimal).\" onMouseOver=\"mostrarAyuda('Amount');\" onmouseout=\"rmvAyuda();\" ", $transaccion_buffer);
	$transaccion_buffer = str_replace("<TD ALIGN=LEFT COLSPAN=4><BR><b>Monto</b></BR>", "<TD ALIGN=LEFT COLSPAN=4><b>Monto</b>", $transaccion_buffer);
	$transaccion_buffer = str_replace("ID=\"Desc\"", "ID=\"Desc\" mensaje=\"Escriba un comentario o referencia asociado con la transferencia (opcional).\"", $transaccion_buffer);
	$transaccion_buffer = str_replace("ID=\"Estado\"", "ID=\"Estado\" mensaje=\"Defina la cuenta de correo del beneficiario si desea enviar un mensaje de notificación de la transacción (opcional).\"", $transaccion_buffer);
	$transaccion_buffer = str_replace("NAME=\"RenInt\"", "NAME=\"RenInt\" ID=\"RenInt\" mensaje=\"Defina el monto del I.V.A.\" onMouseOver=\"mostrarAyuda('RenInt');\" onmouseout=\"rmvAyuda();\" ", $transaccion_buffer);
	$transaccion_buffer = str_replace("NAME=\"RFC\"", "NAME=\"RFC\" ID=\"RFC\" mensaje=\"Defina el RFC del beneficiario.\" onMouseOver=\"mostrarAyuda('RFC');\" onmouseout=\"rmvAyuda();\" ", $transaccion_buffer);
	//$transaccion_buffer = str_replace("ID=\"btnAcepMod2\"", "ID=\"btnAcepMod2\" mensaje=\"Defina el RFC del beneficiario.\" onMouseOver=\"mostrarAyuda('btnAcepMod2');\" onmouseout=\"rmvAyuda();\" ", $transaccion_buffer);
	//****** JCR 10Jul2013	Fin
	//MAOS OCT2013 Formato de moneda en linea I
	$transaccion_buffer = str_replace("onFocus=\"this.value=WithOutFormatAmount(this.value);focus();select();\"", " ", $transaccion_buffer);
	//$transaccion_buffer = str_replace("onblur=\"this.value=FormatAmount(this.value);\"", " ", $transaccion_buffer); //onblur='this.value=FormatAmount(this.value);'
	$transaccion_buffer = str_replace(" SIZE=\"11\", MAXVALUE=\"11\""," MAXLENGTH='17' SIZE='17' ", $transaccion_buffer); //para campo de Monto onKeyUp=\"currencya(this,event);\"
	$transaccion_buffer = str_replace(" SIZE=\"14\", MAXVALUE=\"14\""," MAXLENGTH='17' SIZE='17' ",$transaccion_buffer);// Para campo de IVA  onKeyUp=\"currencyi(this,event);\"
	//MAOS OCT2013 Formato de moneda en linea F
//NXN track 245991 - Mantener el comentario original - Inicio
$patron="";
$sustitucion="";

$patron="/(\<INPUT TYPE\=\"HIDDEN\" NAME\=\"Desc\" VALUE\=\")([a-zA-Z0-9\s]{1,})(\"\>)/i"; //
$sustitucion="$1 $Desc $3";
$transaccion_buffer = preg_replace($patron, $sustitucion, $transaccion_buffer);

//MAOS Mar2014 Observaciones Banxico I
$transaccion_buffer = str_replace("Comentario","Concepto de pago",$transaccion_buffer);
$transaccion_buffer = str_replace("Con el comentario","Concepto de pago",$transaccion_buffer);
//MAOS Mar2014 F
$patron="/(Con el comentario )(\<B\>)([a-zA-Z0-9\s]{1,})(\<\/B\>)/i"; //
$sustitucion="$1 $2 $Desc $4";
$transaccion_buffer = preg_replace($patron, $sustitucion, $transaccion_buffer);


$patron="/([a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+.[a-zA-Z0-9-.]{2,6})/i";
$sustitucion="$Estado";
$transaccion_buffer = preg_replace($patron, $sustitucion, $transaccion_buffer);
//NXN track 245991 - Mantener el comentario original - Fin

//ACS  - MenuContenidoAjax I

$procesarAjax = $ini->read_var( "site", "ProcesarAjax" );
$usersAjax = array( );
$usersAjax = explode( ',', $procesarAjax );

	foreach ($usersAjax as $keyA => $valueajax)
	{
		$valueajax = trim( $valueajax );
		$valueajax = strtoupper( $valueajax );
		if ( $valueajax == $usr )
		{
			$transaccion_buffer=str_replace("/transaccion/ligas/\">ligas","#\" onclick=\"iniProcesarMenu('ligas','')\" >ligas",$transaccion_buffer);
			break;
		}
	}
//ACS  - MenuContenidoAjax F
// AGG I 25Nov2005 Generacion de comprobantes
if ($Access == "Process" and strpos($transaccion_buffer,"No. de Autoriza") != false)
{
	if (strpos($transaccion_buffer,"El saldo disponible") === false ) //si no tiene saldos
	{
		$buffer2 = str_replace(chr(34),chr(92).chr(34),$transaccion_buffer);
	}
	else //si tiene saldos hay que quitarlos
	{
		if (strpos($transaccion_buffer,"Para clientes que requieren comprobante") === false ) // si no tiene DFA
		{
			$buffer3 = substr($transaccion_buffer,0,strpos($transaccion_buffer,"El saldo disponible"));
			$buffer4 = substr($transaccion_buffer,strpos($transaccion_buffer,"No. de Autoriza"));
			$buffer2 = str_replace(chr(34),chr(92).chr(34),$buffer3.$buffer4);
		}
		else
		{
			$buffer3 = substr($transaccion_buffer,0,strpos($transaccion_buffer,"El saldo disponible"));
			$buffer4 = substr($transaccion_buffer,strpos($transaccion_buffer,"Para clientes que requieren comprobante"));
			$buffer2 = str_replace(chr(34),chr(92).chr(34),$buffer3.$buffer4);
		}
	}
	$buffer2 = substr($buffer2,0,strpos($buffer2,"(Por favor anote el "));

	//var_dump ( $date);

	$ini =& INIFile::globalINI();
	$SecureServer = $ini->read_var( "site", "SecureServer" );
	$SecureServerType = $ini->read_var( "site", "SecureServerType" );
	$DomainPostfix = $ini->read_var( "site", "DomainPostfix" );
	$ServerNumber = $ini->read_var( "site", "ServerNumber" );

	if ( $Comprobante == "Activo" )
	{
	$Pagina = "<SCRIPT LANGUAGE='JavaScript'> function ventana() { var windowW = 800; var windowH = 450; var windowX = 10; var windowY = 10; var title = 'Recibo'; s = 'width='+windowW+',height='+windowH; cadena=\"<HTML><HEAD><link REL='STYLESHEET' HREF='" . $SecureServerType . "://" . $SecureServer . $ServerNumber . $DomainPostfix . "/impuestos/recibos.css' TYPE='text/css'><TITLE>Transferencias</TITLE>";
	$Pagina = $Pagina."<SCRIPT LANGUAGE='JavaScript'> function window_onbeforeprint() { Imprimir.style.visibility = 'hidden'; Salir.style.visibility = 'hidden';} function window_onafterprint() { Imprimir.style.visibility = 'visible'; Salir.style.";
	$Pagina = $Pagina."visibility = 'visible'; }\"+\"<\"+\"/\"+\"SCRIPT></HEAD><BODY LANGUAGE='javascript' onbeforeprint='return window_onbeforeprint()' onafterprint='return window_onafterprint()'>";
	$Pagina = $Pagina."<INPUT type='button' value='Imprimir' id=Imprimir name=Imprimir onclick='window.print();'>&nbsp;<INPUT type='button' value=' Salir  ' id=Salir name=Salir onclick='window.close();'>";
	$Pagina = $Pagina."<BR>";
	$Pagina = $Pagina."<P><CENTER>";
	$Pagina = $Pagina."<IMG SRC='" . $SecureServerType . "://" . $SecureServer . $ServerNumber . $DomainPostfix . "/impuestos/LOGO_BB.GIF' ALIGN= 'TOP' Banco del Bajio>";
	$Pagina = $Pagina."</P></CENTER>";
	$Pagina = $Pagina."<H2>Transferencia</H2>";
	$Pagina = $Pagina."<BR><hr noshade='noshade' size='4' color='#5A419c' />";
	$Pagina = $Pagina."<P ALIGN='RIGHT'><B><FONT>$date $time</FONT></B></P>";
	$buffer2 = str_replace("panelDiv","",$buffer2);
	$buffer2 = str_replace("Transacci&oacute;n exitosa.","",$buffer2);
	$buffer2 = str_replace("Transacción exitosa.","",$buffer2);
	$Pagina = $Pagina.$buffer2;
//JAG 25abr2011 inicio
	//$Pagina = $Pagina."Comisión por transferencia: $0.0";
	//$Pagina = $Pagina."<BR>I.V.A de la comisión: 0.0";
//JAG 25abr2011 fin
	$Pagina = $Pagina."<BR><hr noshade='noshade' size='4' color='#5A419c' />";
	$Pagina = $Pagina."<p><FONT SIZE=1>Av. Manuel J. Clouthier No. 508 Col. Jardines del Campestre CP 37128 León, Gto.</FONT> </p>";
	$Pagina = $Pagina."<p><FONT SIZE=1>Consultas y aclaraciones 01 (477) 710- 4600 o al 01 (800) 471-0400</FONT></p>";
	$Pagina = $Pagina."<p><FONT SIZE=1>LA TRANSFERENCIA A LA QUE HACE REFERENCIA ESTE COMPROBANTE FUE REALIZADA CONFORME A LA INFORMACIÓN E INSTRUCCIONES QUE NUESTRO CLIENTE ALIMENTÓ EN EL SISTEMA DE BANCO DEL BAJÍO, POR LO TANTO EL CLIENTE ES EL ÚNICO RESPONSABLE PRESENTE Y FUTURO POR CUALQUIER ERROR U OMISIÓN EN ESTA OPERACIÓN.</FONT></P>";
	$Pagina = $Pagina."<p><FONT SIZE=1>EL ÚNICO COMPROBANTE OFICIAL DE ESTA TRANSACCIÓN ES EL ESTADO DE CUENTA QUE EMITE BANCO DEL BAJIO.</FONT></p>";
	$Pagina = $Pagina."</BODY></HTML>\"; NFW =";
	$Pagina = $Pagina." window.open(\"\",'Recibo',\"toolbar=no,menubar=no,scrollbars=2,\"+s); NFW.blur(); NFW.resizeTo(windowW,windowH); NFW.moveTo(windowX,windowY); NFW.";
	$Pagina = $Pagina."document.open(); NFW.document.write(cadena); NFW.document.close(); NFW.window.focus(); } ventana(); </SCRIPT>";
	}
	//DBA Reimpresion de Comprobantes
	$posMancomunado = strpos($transaccion_buffer,"usuario facultado.");
	if ( $posMancomunado > 0 )
	{
	}
	else
	{
		/*$FechaHora = $date . " " . $time;
		$QryRIC = new eZReImp( );
		$QryRIC -> store($CustID,$FrAccount,$buffer2,"pro","Transferencia",$FechaHora);*/
	}
	//DBA Reimpresion de Comprobantes
}
$t->set_var( "transaccion_buffer", $transaccion_buffer.$Pagina );
// AGG F 25Nov2005 Generacion de comprobantes

//ACS  - MenuContenidoAjax I

// Optimizacion Catalogo (TO) desde WebServices - Inicio [4/4]
// Optimizacion Catalogo (TO) desde WebServices - Inicio [8May2012]
//if($AcctFromOMNI == 0 and $Access=='FrAc') {
//	$transaccion_buffer=$t->parse( "output", "transferencia_tpl" );
//	$transaccion_buffer=str_replace("TYPE='SUBMIT'","TYPE='BUTTON'", $transaccion_buffer);
//	$transaccion_buffer=str_replace("Amount.value=WithOutFormatAmount(Amount.value);onClick=RenInt.value=WithOutFormatAmount(RenInt.value)","onClick=procesarAjax()", $transaccion_buffer);
//	$transaccion_buffer=str_replace("alfanumerico(this)","return abcnumerico(event, this)", $transaccion_buffer);
//	$transaccion_buffer=str_replace("/transaccion/ligas/'","#' onclick='javascript:iniProcesarMenu('ligas', '');'", $transaccion_buffer);
//	$transaccion_buffer=str_replace("parent.history.back()","iniProcesarMenu('transferencia', '');", $transaccion_buffer);
//	$transaccion_buffer=str_replace("'DisabledButton()'","' procesarAjax()'", $transaccion_buffer);
//	$transaccion_buffer=str_replace("NAME='Estado'"," NAME='Estado' onKeyPress='if (isEnter(event) == true) { procesarAjax(); }'", $transaccion_buffer);
//	$transaccion_buffer=str_replace("TYPE='PASSWORD'"," TYPE='PASSWORD' onKeyPress='if (isEnter(event) == true) { procesarAjax(); }'", $transaccion_buffer);
//	$transaccion_buffer=str_replace("<TR><BR><BR></TR>","", $transaccion_buffer);
//	$transaccion_buffer=str_replace("<TR><TR>","<TR>", $transaccion_buffer);
//} else {
// Optimizacion Catalogo (TO) desde WebServices - Fin [8May2012]
	//Reemplaza variables segun corresponda
	$transaccion_buffer=$t->parse( "output", "transferencia_tpl" );
	$transaccion_buffer=str_replace("TYPE=\"SUBMIT\"","TYPE=\"BUTTON\"", $transaccion_buffer);
	$transaccion_buffer=str_replace("Amount.value=WithOutFormatAmount(Amount.value);onClick=RenInt.value=WithOutFormatAmount(RenInt.value)","onClick=procesarAjax()", $transaccion_buffer);
	$transaccion_buffer=str_replace("alfanumerico(this)","return abcnumerico(event, this)", $transaccion_buffer);
	$transaccion_buffer=str_replace("/transaccion/ligas/\"","#\" onclick=\"javascript:iniProcesarMenu('ligas', '');\"", $transaccion_buffer);
	$transaccion_buffer=str_replace("parent.history.back()","iniProcesarMenu('transferencia', '');", $transaccion_buffer);
	$transaccion_buffer=str_replace("\"DisabledButton()\"","\" procesarAjax()\"", $transaccion_buffer);
	$transaccion_buffer=str_replace("NAME=\"Estado\""," NAME=\"Estado\" onKeyPress=\"if (isEnter(event) == true) { procesarAjax(); }\"", $transaccion_buffer);
	$transaccion_buffer=str_replace("TYPE=\"PASSWORD\""," TYPE=\"PASSWORD\" onKeyPress=\"if (isEnter(event) == true) { procesarAjax(); }\"", $transaccion_buffer);
	$transaccion_buffer=str_replace("<TR><BR><BR></TR>","", $transaccion_buffer);
	$transaccion_buffer=str_replace("<TR><TR>","<TR>", $transaccion_buffer);
//} // Optimizacion Catalogo (TO) desde WebServices [8May2012]
// Optimizacion Catalogo (TO) desde WebServices - Fin [4/4]
$transaccion_buffer=$script."<script>
						function isEnter(e)
						{
							var characterCode;

							if(e && e.which)
							{
								e = e;
								characterCode = e.which;
							}
							else
							{
								characterCode = e.keyCode;
							}

							if(characterCode == 13)
							{
								return true;
							}
							else
							{
								return false;
							}
						}

						function abcnumerico(e, obj)
						{
							var characterCode;
							var letras='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789 .';
							var texto;

							if(e && e.which)
							{
								e = e;
								characterCode = e.which;
							}
							else
							{
								e = event;
								characterCode = e.keyCode;
							}

							if (characterCode == 8)
							{
								return true;
							}


							texto = String.fromCharCode(characterCode);


							for(i=0; i<texto.length; i++)
							{
								if (letras.indexOf(texto.charAt(i),0)!=-1)
								{
									return true;
								}
							}

							return false;
						}

						function procesarAjax()
							{
								var parametros=	'CustID:'+document.getElementsByName('CustID')[0].value;



									if(document.getElementsByName('Cadpriv')[0]!=null)
									{
										parametros+=',Cadpriv:'+document.getElementsByName('Cadpriv')[0].value;
									}
									if(document.getElementsByName('Access')[0]!=null)
									{
										parametros+=',Access:'+document.getElementsByName('Access')[0].value;
									}

									if(document.getElementsByName('Amount')[0]!=null)
									{
										parametros+=',Amount:'+WithOutFormatAmount(document.getElementsByName('Amount')[0].value);
									}

									if(document.getElementsByName('RenInt')[0]!=null)
									{

										parametros+=',RenInt:'+WithOutFormatAmount(document.getElementsByName('RenInt')[0].value);
									}

									if(document.getElementsByName('Trxn')[0]!=null)
									{
										parametros+=',Trxn:'+document.getElementsByName('Trxn')[0].value;
									}

									if(document.getElementsByName('Desc')[0]!=null)
									{
										parametros+=',Desc:'+document.getElementsByName('Desc')[0].value;
									}

									if(document.getElementsByName('Estado')[0]!=null)
									{
										parametros+=',Estado:'+document.getElementsByName('Estado')[0].value;
									}

									if(document.getElementsByName('FrAccount')[0]!=null)
									{

										if (document.getElementsByName('FrAccount')[0].type!='HIDDEN' && document.getElementsByName('FrAccount')[0].type!='hidden')
										{
											parametros+=',FrAccount:'+document.getElementsByName('FrAccount')[0].options[document.getElementsByName('FrAccount')[0].selectedIndex].value;
										}
										else
										{
											parametros+=',FrAccount:'+document.getElementsByName('FrAccount')[0].value;
										}
									}


									if(document.getElementsByName('ToAccount')[0]!=null)
									{
										if (document.getElementsByName('ToAccount')[0].type!='HIDDEN' && document.getElementsByName('ToAccount')[0].type!='hidden')
										{
											parametros+=',ToAccount:'+document.getElementsByName('ToAccount')[0].value;
											//DBA Reingenieria de BajioNET Destino - Amigable
											//parametros+=',ToAccount:'+document.getElementsByName('ToAccount')[0].value;
											//DBA Reingenieria de BajioNET Destino - Amigable
										}
										else
										{
											parametros+=',ToAccount:'+document.getElementsByName('ToAccount')[0].value;
										}
									}

									if(document.getElementsByName('Day')[0]!=null)
									{
										parametros+=',Day:'+document.getElementsByName('Day')[0].value;
									}
									if(document.getElementsByName('Month')[0]!=null)
									{
										parametros+=',Month:'+document.getElementsByName('Month')[0].value;
									}


									if(document.getElementsByName('NombreB')[0]!=null)
									{
										parametros+=',NombreB:'+document.getElementsByName('NombreB')[0].value;
									}

									if(document.getElementsByName('RFC')[0]!=null)
									{
										parametros+=',RFC:'+document.getElementsByName('RFC')[0].value;
									}

									if(document.getElementsByName('DayF')[0]!=null)
									{
										parametros+=',DayF:'+document.getElementsByName('DayF')[0].value;
									}

									if(document.getElementsByName('Comprobante')[0]!=null)
									{
										if(document.getElementsByName('Comprobante')[0].checked==true)
										{
											parametros+=',Comprobante:'+document.getElementsByName('Comprobante')[0].value;
										}
										else
										{
											parametros+=',Comprobante:';
										}
									}

									if(document.getElementsByName('code')[0]!=null)
									{
										parametros+=',code:'+document.getElementsByName('code')[0].value;
									}

									iniProcesarMenu('transferencia', parametros);

							}
					</script>".$transaccion_buffer;
//ACS  - MenuContenidoAjax F

?>
