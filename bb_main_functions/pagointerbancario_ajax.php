<!-- DBA ReingenieriaBajioNET -->
<!-- DGM
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
	
	jQuery('#ToAccountR').click(function(){
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


 });



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

function cambiaCuentasDestino(nombre,banco,tipo,clabe,rowno)
{
	var destino = '';
	destino = destino.concat(nombre);
	destino = destino.concat(" ");
	destino = destino.concat(banco);
	destino = destino.concat(" ");
	destino = destino.concat(tipo);
	destino = destino.concat("-");
	destino = destino.concat(clabe);
	$("#ToAccount").val(rowno);
	$("#ToAccountR").val(destino);
	return false;
}


function gridDestino()
{
var userid ="";
	jQuery().ready(
				function ()
				{
					cliente   = $('#Cliente').val();
					userid = document.getElementsByName('CustID')[0].value;
					dialog 	  = $('#modalDestino');
					var hash  = $("#hash").val(); //Auditoria
					$("#error").empty();

				jQuery("#catalogocuentas").jqGrid (
					{
						 url:'/procesarAjaxMiddlewareGrid.php?ws=interbancarias&modulo=pin&servicio=destinoAmigable&Cliente='+cliente+"&hash="+hash,
						datatype: "xml",
						colNames:['Nombre','Alias','Banco','Tipo', 'CLABE/MOVIL', 'rowno'],/*DMOS Nuevas Reglas de SPEI/TEF*/
						colModel:[
								{name:'nombre',index:'nombre', width:270},
								{name:'alias',index:'alias', width:110},
								{name:'banco',index:'banco', width:95},
								{name:'tipo',index:'tipo', width:55},
								{name:'clabe',index:'clabe', width:150},
								{name:'rowno',index:'rowno',hidden: true, width:200},
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
							var cuentas 	= '';
							var nombre 	= jQuery("#catalogocuentas").jqGrid('getCell', id, 'nombre');
							var banco	= jQuery("#catalogocuentas").jqGrid('getCell', id, 'banco');
							var tipo 	= jQuery("#catalogocuentas").jqGrid('getCell', id, 'tipo');
							var clabe 	= jQuery("#catalogocuentas").jqGrid('getCell', id, 'clabe');
						    var rowno 	= jQuery("#catalogocuentas").jqGrid('getCell', id, 'rowno');
							closeConfirmDestino(dialog);
						    cambiaCuentasDestino(nombre,banco,tipo,clabe,rowno);
   						},
   						loadError: function(response){  							
   							
   							$("#error").empty();
	            			$("#error").append(response.responseText);
   						},
						caption:"Elija la cuenta destino de su transferencia"
					} )
				 }
		)
	jQuery("#btnModalReset").click( function() {
					var busqueda		 = $("#busquedaDestinatario").val();
					var cliente			 = $("#Cliente").val();
					var userid			 = $("#CustID").val();
					var hash  = $("#hash").val(); //Auditoria
					$('#catalogocuentas').jqGrid('setGridParam',
										{
											url:'/procesarAjaxMiddlewareGrid.php?ws=interbancarias&modulo=spi&servicio=destinoAmigable&Cliente='+cliente+"&hash="+hash
										}).trigger("reloadGrid");

					$("#busquedaDestinatario").val("");
		});

	jQuery("#btnModalBuscar").click( function() {
					var busqueda		 = $("#busquedaDestinatario").val();
					var cliente			 = $("#Cliente").val();
					var userid			 = $("#CustID").val();
					var hash  = $("#hash").val(); //Auditoria
					$('#catalogocuentas').jqGrid('setGridParam',
										{
												url: '/procesarAjaxMiddlewareGrid.php?ws=interbancarias&modulo=pin&servicio=destinoAmigable&Cliente=' + cliente + '&busqueda='+ busqueda + '&CustID=' + userid+"&hash="+hash,
												page:1
								}).trigger("reloadGrid");
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

function cancela(){
	$.modal.close();
}
function llamaPopUp()
	{
		$('#bitacora1').hide(0);
		popUpOper($(this),"tef");
	}
//OVVC [INFO] => LIBRER�AS POR SESI�N [DATE] => 20022015
$('#btnModalCierra').live('click', function(){
	cancela();
});
</script>

<div id="modalDestino" style='display:none' align='center'>
	<!--div style="width:750; height:320px; align:center;"-->
	<div style="width:750; height:550px; align:center;"> <!--MAOS NOV2013 20 Cuentas-->
	<div class='ui-jqgrid-titlebar ui-widget-header ui-corner-top ui-helper-clearfix' style='width:auto; height:20px;'>
	<span class='ui-jqgrid-title' style='font-size:12px;valign:left;' >&nbsp;Cat&aacute;logo de Cuentas</span></div>
		<!--/br>
			<div><Buscar :>
						</div-->
						<br>
			<div id='error' style='font-size: 11px; color:red;' ></div>
			<div id='resCuentas' align='center'>
				<div id="btnBuscar" style="width:750; height:15px; position: relative; left:34%;">
					<input Class="tooltip" type="input"  size=12 maxlength=12 autocomplete="off" id="busquedaDestinatario" mensaje="Puede filtrar sus cuentas por nombre, banco, tipo o CLABE." />
					<input Class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only"  type="button"  id="btnModalBuscar" value ="Buscar" text="Buscar" onclick="javascript: if(isNumberTk($('#codeTkArchivo3').val())) {continuaPago('1','2','3',$('#codeTkArchivo3').val(),$('#cuentas').val());} else {$('#codeTkArchivo3').val(''); $('#auxArcTk').html('El n&uacute;mero de Clave ASB que ha ingresado es incorrecto, intentelo de nuevo.');} " />
			    </div>
				<br>
				<table align="center" id='catalogocuentas'  border=1 ></table>
				<div id="pagerCatalogoCuentas"></div>
				<br>
				<div id='botones' style="width:750; height:20px; position: relative; left:38%;">
					<input Class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" type="button"  id="btnModalCierra" value ="Cancelar" text="Cancelar" /><!-- //OVVC [INFO] => LIBRER�AS POR SESI�N [DATE] => 20022015 onclick="javascript:cancela();" -->
					<input Class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" type="button"  id="btnModalReset" value ="Limpiar" text="Limpiar"/>
			    </div>

			</div>
	</div>

</div>
<br>
<input type="HIDDEN" id="Cliente" name="Cliente" value="<?php echo  $session->variable( 'r_cno' );?>">
<input type="HIDDEN" id="hash" name="hash" value="<?php echo  md5($session->variable( 'r_cno' )."|".strtolower(trim($session->variable( 'r_usr' )))."|"."Trn_$$"); ?>">

<!-- DBA ReingenieriaBajioNET -->
<?php
include_once( "ezreimp/classes/ezreimp.php" ); //DBA Reimpresion de Comprobantes

    $transaccion_buffer = "";
//CRA - 08jul2013 Reingenieria Bajionet - Ini
$JQueryTrans = $ini->read_var("site" , "JQueryTrans");
If($JQueryTrans == 1)
{?>
	<script type="text/javascript">
		//OVVC [INFO] => LIBRER�AS POR SESI�N [DATE] => 20022015 [I]
		/*
		$("#aceptaTEF").live('click', function(){
			llamaPopUp();
		});
		*/

	//DBA Arreglar multiples peticiones de CONFIRM
		$("#aceptaTEF").click(function(){
			llamaPopUp();
		});
	//DBA Arreglar multiples peticiones de CONFIRM

		$("#Amount").live('keypress', function(event){
			if ( event.keyCode == 13 ) {
				llamaPopUp();
			}
		});
		$("#Amount").live('keyup', function(event){
			formatoMontoJQ(event);
		});

		$("#Desc").live('keypress',function(event){
			if ( event.keyCode == 13 ) {
				llamaPopUp();
			} else {
				this.value = (this.value + '').replace(/[^a-zA-Z0-9\s]/g, '');
				//return abcnumerico(event, this);
			}
		});

		function llamaPopUp(){
			$('#bitacora1').hide();
			popUpOper($(this),"tef");
		}

		function formatoMontoJQ()
		{	
			var CaretPos = 0;
			var numero = $("#Amount").val();
			var formatoMoneda = "";
			
			CaretPos = 0;
			
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
		$(function() {
			$('#btnCambiar').hide();
			$('#btnAceptar').hide();
			$('#aceptaTEF').show();
			$("input").removeAttr("onkeypress");
		});
		//OVVC [INFO] => LIBRER�AS POR SESI�N [DATE] => 20022015 [F]
	</script>
<?php
}
else
{
?>
	<script type="text/javascript">
		$(function() {
			$('#btnCambiar').show();
			$('#btnAceptar').show();
			$('#aceptaTEF').hide();
		});
	</script>
<?php
}
//CRA - 08jul2013 Reingenieria Bajionet - Fin
    if(empty($Access)) {
        $Access = "FrAc";
    }

    if( !isset /*(HB AGL - Ajustes PHP5)*/( $Amount ) ) {
    	$Amount = "";
    }
    if( !isset /*(HB AGL - Ajustes PHP5)*/( $Apocope ) ) {
    	$Apocope = "";
    }
    if( !isset /*(HB AGL - Ajustes PHP5)*/( $LadaB ) ) {
    	$LadaB = "";
    }
    if( !isset /*(HB AGL - Ajustes PHP5)*/( $RFC ) ) {
    	$RFC = "";
    }
    if( !isset /*(HB AGL - Ajustes PHP5)*/( $RenInt ) ) {
    	$RenInt = "";
    }
    if( !isset /*(HB AGL - Ajustes PHP5)*/( $DiasPzo ) ) {
    	$DiasPzo = "";
    }
    switch($Access) {
    case "FrAc":
        $particularFields = "&Filtro=".urlencode($Filtro);
        $tr->blog($qki,"PagoInterbancario_ajax", $FrAccount, $Apocope, $LadaB, $Amount, $Accion);
        break;
    case "Confirm":
		$particularFields = "&FrAccount=".urlencode($FrAccount)."&ToAccount=".urlencode($ToAccount)."&Amount=".urlencode($Amount)."&LadaB=".urlencode($LadaB)."&Desc=".urlencode(preg_replace("/&/", "-", $Desc))."&RenInt=".urlencode($RenInt);//-- EVG-EDS Bajionet CNBV 01042005   //HB AGL
		$tr->blog($qki,"PagoInterbancario_ajax", $FrAccount, $ToAccount, $DiasPzo, $Amount, $Accion);
		break;
	case "Process":
		$particularFields = "&Passwd=".encrypt( $code, strtolower( $usr ) )."&FrAccount=".urlencode($FrAccount)."&Apocope=".urlencode($Apocope)."&ToAccount=".urlencode($ToAccount)."&Amount=".urlencode($Amount)."&NombreB=".urlencode($NombreB)."&LadaB=".urlencode($LadaB)."&Day=".urlencode($Day)."&Desc=".urlencode(preg_replace("/&/", "-", $Desc))."&RFC=".urlencode($RFC)."&RenInt=".urlencode($RenInt)."&Estado=".$Estado;   //HB AGL
        $tr->blog($qki,"PagoInterbancario_ajax", $FrAccount, $ToAccount, $DiasPzo, $Amount, $Accion);
        break;
    }
    $transaccion_buffer = "";
    // DebugBreak();
    $ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=pin&Access=".urlencode($Access)."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // pago interbancario

	//REF WMA-27ene2009, Inicio
	if ($Access == "Confirm" and (trim($ToAccount) == "" or trim($ToAccount) == "998877665544332211"))
	{
		$transaccion_buffer = "El beneficiario no puede quedar vac&iacute;o</b>";
	}
	//REF WMA-27ene2009, Fin

	//***** CRA 03jul2013 Reingenieria Bajionet - Ini *****
	//***** CRA Se inserta html para darle tratamiento con 	Jquery Evita que cicle la pagina al usar Ajax en Transferencia
	if ( $Access == "Confirm" or $Access=="Process") // dgm - cra
	{
		$transaccion_buffer = "<!--BUSQUEDAI-->".$transaccion_buffer."<!--BUSQUEDAF-->"; // dgm
	}
	//***** CRA 03jul2013 Reingenieria Bajionet - Fin *****

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
				$buffer4 = substr($transaccion_buffer,strpos($transaccion_buffer,"No. de Folio"));
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
		$Pagina = "<SCRIPT LANGUAGE='JavaScript'> function ventana() { var windowW = 800; var windowH = 450; var windowX = 10; var windowY = 10; var title = 'Recibo'; s = 'width='+windowW+',height='+windowH; cadena=\"<HTML><HEAD><link REL='STYLESHEET' HREF='" . $SecureServerType . "://" . $SecureServer . $ServerNumber . $DomainPostfix . "/impuestos/recibos.css' TYPE='text/css'><TITLE>Pago T.E.F.</TITLE>";
		$Pagina = $Pagina."<SCRIPT LANGUAGE='JavaScript'> function window_onbeforeprint() { Imprimir.style.visibility = 'hidden'; Salir.style.visibility = 'hidden';} function window_onafterprint() { Imprimir.style.visibility = 'visible'; Salir.style.";
		$Pagina = $Pagina."visibility = 'visible'; }\"+\"<\"+\"/\"+\"SCRIPT></HEAD><BODY LANGUAGE='javascript' onbeforeprint='return window_onbeforeprint()' onafterprint='return window_onafterprint()'>";
		$Pagina = $Pagina."<INPUT type='button' value='Imprimir' id=Imprimir name=Imprimir onclick='window.print();'>&nbsp;<INPUT type='button' value=' Salir  ' id=Salir name=Salir onclick='window.close();'>";
		$Pagina = $Pagina."<BR>";
		$Pagina = $Pagina."<P><CENTER>";
		$Pagina = $Pagina."<IMG SRC='" . $SecureServerType . "://" . $SecureServer . $ServerNumber . $DomainPostfix . "/impuestos/LOGO_BB.GIF' ALIGN= 'TOP' Banco del Bajio>";
		$Pagina = $Pagina."</P></CENTER>";
		$Pagina = $Pagina."<H2>Pago T.E.F.</H2>";
		$Pagina = $Pagina."<BR><hr noshade='noshade' size='4' color='#5A419c' />";
		$Pagina = $Pagina."<P ALIGN='RIGHT'><B><FONT>$date $time</FONT></B></P>";
		$buffer2 = str_replace("panelDiv","",$buffer2);
		$buffer2 = str_replace("Transacci&oacute;n exitosa.","",$buffer2);
		$buffer2 = str_replace("Transacci�n exitosa.","",$buffer2);
		$Pagina = $Pagina.$buffer2;
		$Pagina = $Pagina."<BR><hr noshade='noshade' size='4' color='#5A419c' />";
		$Pagina = $Pagina."<p><FONT SIZE=1>Av. Manuel J. Clouthier No. 508 Col. Jardines del Campestre CP 37128 Le�n, Gto.</FONT> </p>";
		$Pagina = $Pagina."<p><FONT SIZE=1>Consultas y aclaraciones 01 (477) 710- 4600 o al 01 (800) 471-0400</FONT></p>";
		$Pagina = $Pagina."<p><FONT SIZE=1>LA TRANSFERENCIA A LA QUE HACE REFERENCIA ESTE COMPROBANTE FUE REALIZADA CONFORME A LA INFORMACI�N E INSTRUCCIONES QUE NUESTRO CLIENTE ALIMENT� EN EL SISTEMA DE BANCO DEL BAJ�O, POR LO TANTO EL CLIENTE ES EL �NICO RESPONSABLE PRESENTE Y FUTURO POR CUALQUIER ERROR U OMISI�N EN ESTA OPERACI�N.</FONT></P>";
		$Pagina = $Pagina."<p><FONT SIZE=1>EL �NICO COMPROBANTE OFICIAL DE ESTA TRANSACCI�N ES EL ESTADO DE CUENTA QUE EMITEN BANCO DEL BAJIO Y EL BANCO RECEPTOR DE ESTA TRANSACCI�N.</FONT></p>";
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
			$QryRIC -> store($CustID,$FrAccount,$buffer2,"pin","Pago T.E.F.",$FechaHora);*/
		}
		//DBA Reimpresion de Comprobantes
		$transaccion_buffer = $transaccion_buffer.$Pagina ;
	}

    $transaccion_buffer="<div id='tituloOper'><h1>Transferencia Electr&oacute;nica de Fondos (T.E.F.)</h1><hr noshade='noshade' size='4' /></div>".$transaccion_buffer;
	//****** JCR 12Jul2013 Inicio
	$transaccion_buffer = str_replace("ID=\"FrAccount\"", "ID=\"FrAccount\" class=\"combos\" mensaje=\"Seleccione la cuenta a la cual ser� cargado el importe de la transferencia y la comisi�n respectiva.\" onMouseOver=\"mostrarAyuda('FrAccount');\" onmouseout=\"rmvAyuda();\" ", $transaccion_buffer);
	$transaccion_buffer = str_replace("ID=\"ToAccount\"", "ID=\"ToAccount\" class=\"combos\" mensaje=\"Seleccione la cuenta del beneficiario. \" onMouseOver=\"mostrarAyuda('ToAccount');\" onmouseout=\"rmvAyuda();\" ", $transaccion_buffer);
	$transaccion_buffer = str_replace("NAME=\"Amount\"", "NAME=\"Amount\" ID=\"Amount\" mensaje=\"Defina el monto a transferir (use el punto como separador decimal).\" onMouseOver=\"mostrarAyuda('Amount');\" onmouseout=\"rmvAyuda();\" ", $transaccion_buffer);
	$transaccion_buffer = str_replace("NAME=\"Desc\"", "NAME=\"Desc\" ID=\"Desc\" mensaje=\"Escriba un comentario o referencia asociado con la transferencia (opcional).\" onMouseOver=\"mostrarAyuda('Desc');\" onmouseout=\"rmvAyuda();\" ", $transaccion_buffer);
	$transaccion_buffer = str_replace("NAME=\"LadaB\"", "NAME=\"LadaB\" ID=\"LadaB\" mensaje=\"Seleccione el plazo deseado para aplicar el abono en la cuenta del beneficiario.\" onMouseOver=\"mostrarAyuda('LadaB');\" onmouseout=\"rmvAyuda();\" ", $transaccion_buffer);
	$transaccion_buffer = str_replace("NAME=\"RenInt\"", "NAME=\"RenInt\" ID=\"RenInt\" mensaje=\"Defina el monto del I.V.A.\" onMouseOver=\"mostrarAyuda('RenInt');\" onmouseout=\"rmvAyuda();\" ", $transaccion_buffer);
	$transaccion_buffer = str_replace("NAME=\"RFC\"", "NAME=\"RFC\" ID=\"RFC\" mensaje=\"Defina el RFC del beneficiario.\" onMouseOver=\"mostrarAyuda('RFC');\" onmouseout=\"rmvAyuda();\" ", $transaccion_buffer);
	$transaccion_buffer = str_replace("ID=\"filtroID\"", "ID=\"filtroID\" mensaje=\"Defina el criterio por el cual desea buscar al beneficiario (Puede ser una palabra, letra o s&iacute;laba).\" onMouseOver=\"mostrarAyuda('filtroID');\" onmouseout=\"rmvAyuda();\" ", $transaccion_buffer);
	$transaccion_buffer = str_replace("ID=\"TFiltro\"", "ID=\"TFiltro\" mensaje=\"Especifique el tipo del filtro.\" onMouseOver=\"mostrarAyuda('TFiltro');\" onmouseout=\"rmvAyuda();\" ", $transaccion_buffer);
	$transaccion_buffer = str_replace("name=\"Filtrar\"", "NAME=\"Filtrar\" ID=\"Filtrar\" mensaje=\"Presione este bot�n para ver la lista de beneficiarios que concuerden con el filtro solicitado.\" onMouseOver=\"mostrarAyuda('Filtrar');\" onmouseout=\"rmvAyuda();\" ", $transaccion_buffer);
	$transaccion_buffer = str_replace("Beneficiario<", "<BR/>Beneficiario<", $transaccion_buffer);
	//$transaccion_buffer = str_replace("> Comentario", "><BR/>Comentario", $transaccion_buffer);
	$transaccion_buffer = str_replace("Comentario", "Concepto de pago", $transaccion_buffer);
	$transaccion_buffer = str_replace("Con el comentario", "Concepto de pago", $transaccion_buffer);

	//****** JCR 12Jul2013	Fin
	//MAOS OCT2013 Formato de moneda en linea I
	//$transaccion_buffer = str_replace("id=\"ToAccountR\"", "id=\"ToAccountR\" class=\"combos1\" ", $transaccion_buffer); //MAOS NOV2013 20 Cuentas
	$transaccion_buffer = str_replace("id=\"ToAccountR\"", "id=\"ToAccountR\" class=\"combos1\" style=\"cursor:pointer;\" ", $transaccion_buffer); //MAOS Cambio de lupa
	$transaccion_buffer = str_replace("<a href='#' id='consultaDestino'", "<a href='#' id='consultaDestino' style=\"position:relative;left:-25px;top:3px;\" ", $transaccion_buffer); //MAOS Cambio de lupa
	$transaccion_buffer = str_replace(" SIZE=\"14\", MAXVALUE=\"14\" onFocus=\"this.value=WithOutFormatAmount(this.value);focus();select();\""," MAXLENGTH='17' SIZE='17'", $transaccion_buffer);
	$transaccion_buffer = str_replace(" ID=\"Amount\""," ID=\"Amount\" ", $transaccion_buffer); // onKeyUp=\"currencya( this,event );\"
	$transaccion_buffer = str_replace(" ID=\"Amount\""," ID=\"Amount\" ", $transaccion_buffer); // onKeyUp=\"currencyi( this,event );\"
	$transaccion_buffer = str_replace(" onKeyUp=\"currencya( this,event );\" ","", $transaccion_buffer); // onKeyUp=\"currencyi( this,event );\"
	//MAOS OCT2013 Formato de moneda en linea F

?>
