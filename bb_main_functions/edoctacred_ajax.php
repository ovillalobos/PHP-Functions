<link rel='stylesheet' type='text/css' media='screen' href='/eztransaccion/user/include/js_calendar/calendar-blue.css' />
<script language="javascript" src="/eztransaccion/user/include/js_calendar/calendar.js" />
<script language="javascript" src="/eztransaccion/user/include/js_calendar/lang/calendar-es.js" />
<script language="javascript" src="/eztransaccion/user/include/js_calendar/calendar-setup.js" />
<script language="javascript" src="/eztransaccion/user/include/js/FuncionesReimpPhp.js" />

<link rel="stylesheet" type="text/css" media='screen' href="/middleware/css/smart_wizard_inversion.css" />

<link rel='stylesheet' type='text/css' media='screen' href='/middleware/css/estilo_middleware.css' />

<link rel='stylesheet' type='text/css' media='screen' href='/middleware/css/jquery-ui-1.7.3.custom.css' />
<link rel='stylesheet' type='text/css' media='screen' href='/middleware/css/ayuda.css' />
<link rel='stylesheet' type='text/css' media='screen' href='/middleware/js/common/jquery.jqGrid-4.2.0/css/ui.jqgrid.css' />
<script language="javascript" src="/middleware/js/common/jsValidador.js"></script>

<!-- REGA Se agregan librerías y código javascript para la generación de una tabla modal y jqgrid de las cuentas agrupadas del cliente. -->

<!-- 
<script language="javascript" src="/middleware/js/common/jsTooltips.js"></script>
<script language="javascript" src="/eztransaccion/user/menu/jquery.simplemodal.js"></script>
<script language="javascript" type="text/javascript" src="/middleware/js/common/jquery.smartWizard.inversion.js"></script>

<script language="javascript" src="/middleware/js/common/jsPopUp.js" />
-->
<!--

<link rel='stylesheet' type='text/css' media='screen' href='/middleware/js/common/jquery.jqGrid-4.2.0/css/ui.jqgrid.css' />
<script language="javascript" src="/middleware/js/common/jquery.jqGrid-4.2.0/js/i18n/grid.locale-es.js" />
<script language="javascript" src="/middleware/js/common/jquery.jqGrid-4.2.0/js/jquery.jqGrid.min.js" />

-->
<script language="javascript" src="/middleware/js/common/jquery.jqGrid-4.2.0/js/i18n/grid.locale-es.js" />
<script language="javascript" src="/middleware/js/common/jquery.jqGrid-4.2.0/js/jquery.jqGrid.min.js" />
<link rel='stylesheet' type='text/css' media='screen' href='/middleware/css/estilo_middleware.css' />
<link rel='stylesheet' type='text/css' media='screen' href='/middleware/css/jquery-ui-1.7.3.custom.css' />
<link rel='stylesheet' type='text/css' media='screen' href='/middleware/css/ayuda.css' />
<link rel='stylesheet' type='text/css' media='screen' href='/middleware/js/common/jquery.jqGrid-4.2.0/css/ui.jqgrid.css' />

<script language="javascript">
var format="";
var cliente;
var cuenta;
var sub;
var tipo;

//$("#msgresAltaLineabb").hide();

$('#ToAccountO').click(function(){
	$( '#consultaDestinoO' ).trigger( 'click' );
	
});

$('#consultaDestinoO').click(function(){
	try	{
		$('#modalDestinoO').modal({
		  close:false,
		  persist: true,
		  containerCss: {
				height: 450,
				width: 750
				},
			onShow: gridDestinoOrigen(),
					onClose: closeConfirm
				});
	}
	catch(e){
		$('#modalDestinoO').modal({close:false,
			  containerCss: {
					height: 350,
					width: 760
					},
				onClose: closeConfirm
					});
				}
});
function closeConfirm (dialog) {
	dialog.data.fadeOut(200, function () {
		dialog.container.hide(200, function () {
			dialog.overlay.slideUp(200, function () {
				$.modal.close();
			});
		});
	});
}
function isNumberTk(code){
	var valor = code;
	var patternx = "^\\d{10}$";
	if (valor.match(patternx) && valor != "" ){return true;}
	else{return false;}
	return false;
}
function cierraModal() {
	$.modal.close();
}
function gridDestinoOrigen(){
	var userid ="";
	jQuery().ready(
			function ()
			{
				cliente   = $('#Rowno').val();
				userid = $("#CustID").val();	
				dialog 	  = $('#modalDestino2');
				var hash  = $("#hash").val(); //Auditoria
				jQuery("#catalogocuentasOrigen").jqGrid({
					 url:'/procesarAjaxMiddlewareGrid.php?ws=cuentasorigen&modulo=edoCtas&servicio=destinoAmigable&Cliente='+cliente+'&CustID='+userid+'&hash='+hash,
					datatype: "xml",					
					colNames:['Cliente','','Producto','Sub', 'Nombre', 'Tipo','Rowno'],
					colModel:[
						{name:'cliente',index:'cliente', width:100},
						{name:'firma',index:'cliente', hidden:true},
						{name:'producto',index:'clabe', width:100, hidden: true},
						{name:'sub',index:'sub', width:30, hidden: true},
						{name:'nombre',index:'nombre', width:240},
						{name:'tipo',index:'tipo',hidden: true, width:50},
						{name:'rowno',index:'rowno',hidden: true, width:100}],
					rowNum:10,
					height: 'auto',
					autowidth: true,
					pager: jQuery('#pagerCatalogoCuentasOrigen'),
					sortname: 'id',
					viewrecords: true,
					sortorder: "desc",
					emptyrecords: "No existen cuentas activas",					
					pgbuttons: true,					
					onSelectRow: function(id){
						for(var i=0; i<12; i++){
							if(id == i){
								jQuery("#catalogocuentasOrigen").jqGrid('setCell',i,'cliente','',{color: 'white'});
								jQuery("#catalogocuentasOrigen").jqGrid('setCell',i,'producto','',{color: 'white'});
								jQuery("#catalogocuentasOrigen").jqGrid('setCell',i,'sub','',{color: 'white'});
								jQuery("#catalogocuentasOrigen").jqGrid('setCell',i,'tipo','',{color: 'white'});
								jQuery("#catalogocuentasOrigen").jqGrid('setCell',i,'nombre','',{color: 'white'});
							}
							else{
								jQuery("#catalogocuentasOrigen").jqGrid('setCell',i,'cliente','',{color: 'black'});
								jQuery("#catalogocuentasOrigen").jqGrid('setCell',i,'producto','',{color: 'black'});
								jQuery("#catalogocuentasOrigen").jqGrid('setCell',i,'sub','',{color: 'black'});
								jQuery("#catalogocuentasOrigen").jqGrid('setCell',i,'tipo','',{color: 'black'});
								jQuery("#catalogocuentasOrigen").jqGrid('setCell',i,'nombre','',{color: 'black'});
							}
						}
						var cuentas 	= '';	
						var cliente 	= jQuery("#catalogocuentasOrigen").jqGrid('getCell', id, 'cliente');
						cuenta 			= jQuery("#catalogocuentasOrigen").jqGrid('getCell', id, 'producto');
						sub 			= jQuery("#catalogocuentasOrigen").jqGrid('getCell', id, 'sub');
						tipo			= jQuery("#catalogocuentasOrigen").jqGrid('getCell', id, 'tipo');
						var nombre		= jQuery("#catalogocuentasOrigen").jqGrid('getCell', id, 'nombre');
						
						$("#firma").val( jQuery("#catalogocuentasOrigen").jqGrid('getCell', id, 'firma') );
						
						if ( tipo == "Agrupada" )
							tipo="gpo-";
						else
							tipo=" ";
						$('#msgresAltaLineabb').hide();
						cambiaCuentasDestino3(cliente,nombre,tipo);
						closeConfirmDestino(dialog);
						
					},
					loadError: function(response){  													
						$("#error").empty();
        				$("#error").append(response.responseText);
   					},
					caption:"Elija la cuenta a descargar sus estados"
				} );
			}
		);
	}
	
function addMonths(date, months) {
  date.setMonth(date.getMonth() + months);
  return date;
}
	
function cambiaCuentasDestino3(cte,nom,tipo){
	var fecha;
	var today = new Date();
	var aMonth = today.getMonth()-1;
	var anio   = today.getFullYear();
	
	var months= [], i;
	var month = new Array("Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic");
	var anios = [];
	
	if (aMonth == -1){
		aMonth = 11;
		anio--;
	}
	
	for (i=0; i<13; i++) 
	{
	 months.push(month[aMonth]);
	  anios.push(anio);
	  
	 aMonth--;
	 if (aMonth == -1) {
	  aMonth = 11;
	  anio--;
	 }
	}

	var destino = '';
	var destino2 = '';
	destino = destino.concat(cte);
	destino2 = destino;
	destino2 = destino2.concat("-");
	destino2 = destino2.concat(tipo);
	destino2 = destino2.concat(nom);
	$("#ToAccountO").val(destino2);
	$("#TrAccount").val(destino);
	
	var mydata = [];
	for (var i=0;i<13; i++) {
		fechavalor = anios[i] + "-" + months[i];
		mydata.push({id:i,cliente:destino2,fecha:fechavalor,formato:""});
	}
	
	cliente   = $('#Rowno').val();
	userid = $("#CustID").val();
	dialog 	  = $('#modalDestino2');
	jQuery("#opciones").jqGrid({
		datatype: "local",
		data: mydata, 	
		colNames:['Cliente','Fecha','Formato'],
		colModel:[
			{name:'cliente',index:'cliente', width:230},
			{name:'fecha',index:'fecha', width:60},
			{name:'formato',index:'formato', width:80,sortable:false}
		],
		rowNum:12,
		height: 'auto',
		autowidth: false,
		width: '370',
		sortname: 'id',
		viewrecords: true,
		sortorder: "desc",
		emptyrecords: "No existen estados de cuenta",
		pgbuttons: false,	
		async: false,
		onSelectRow: function(id){
			for(var i=0; i<12; i++){
				if(id == i){
					jQuery("#opciones").jqGrid('setCell',i,'cliente','',{color: 'white'});
					jQuery("#opciones").jqGrid('setCell',i,'fecha','',{color: 'white'});
					jQuery("#opciones").jqGrid('setCell',i,'formato','',{color: 'white'});
				}
				else{
					jQuery("#opciones").jqGrid('setCell',i,'cliente','',{color: 'black'});
					jQuery("#opciones").jqGrid('setCell',i,'fecha','',{color: 'black'});
					jQuery("#opciones").jqGrid('setCell',i,'formato','',{color: 'black'});
				}
			}
			var cuentas 	= '';	
			var cliente 	= jQuery("#opciones").jqGrid('getCell', id, 'cliente');
			var fecha 		= jQuery("#opciones").jqGrid('getCell', id, 'fecha');
			formato			= jQuery("#opciones").jqGrid('getCell', id, 'formato');
		},
		gridComplete: function(){                               
		   var ids = jQuery("#opciones").jqGrid('getDataIDs');
		   for(var i=0;i < ids.length;i++){
			  var cl = ids[i];
			  xml = "<img src=\"/middleware/images/xml.jpg\" height=\"23\" width=\"21\" style='cursor: pointer; padding-left: 5px;' onclick='format=\"xml\"; pdfEdoCta("+cl+");'/>";
			  pdf = "<img src=\"/middleware/images/pdf.jpg\" height=\"23\" width=\"21\" style='cursor: pointer; padding-left: 5px;' onclick='format=\"pdf\";pdfEdoCta("+cl+");'/>";
			  jQuery("#opciones").jqGrid('setRowData',ids[i],{formato:pdf+xml});
			}
	   },
		caption:"Elija el estado cuenta a descargar"
	} );
	$("#opciones").jqGrid('setGridParam',
        { 
            datatype: 'local',
            data: mydata
        }).trigger("reloadGrid");
	return false;
}

function closeConfirmDestino(dialog){
	$.modal.close();	
	return false;
}

$("#btnModalReset").click(function(){
	cliente   = $('#Rowno').val();
	userid = $("#CustID").val();			
	var hash  = $("#hash").val(); //Auditoria			
	$('#busquedaDestinatario').val("");
	$('#catalogocuentasOrigen').jqGrid('setGridParam', {url:'/procesarAjaxMiddlewareGrid.php?ws=cuentasorigen&modulo=edoCtas&servicio=destinoAmigable&Cliente='+ cliente +'&CustID='+ userid + '&page=1'+ '&hash='+ hash });
	$('#catalogocuentasOrigen').trigger('reloadGrid');
});

jQuery("#btnModalBuscar").click(function(){
	cliente = $('#Rowno').val();
	userid = $("#CustID").val();			
	var hash  = $("#hash").val(); //Auditoria			
	var busqueda = $("#busquedaDestinatario").val();
	$('#catalogocuentasOrigen').jqGrid('setGridParam', {url:'/procesarAjaxMiddlewareGrid.php?ws=cuentasorigen&modulo=edoCtas&servicio=destinoAmigable&Cliente='+cliente+'&CustID='+userid + '&busqueda=' + busqueda + '&hash='+ hash });
	$('#catalogocuentasOrigen').trigger('reloadGrid', [{page:1}]);
});

jQuery("#busquedaDestinatario").live("keypress", function(e) {
	if (e.keyCode == 13) {
		$( '#btnModalBuscar' ).trigger( 'click' );
	}
});	

jQuery("#closeresAltaLinea2").click(function(){
	$('#msgresAltaLineabb').slideUp();
});

function pdfEdoCta(id){
	
	var hash = $("#firma").val(); 
	
	var f=jQuery("#opciones").jqGrid('getCell', id, 'fecha').split("-");
	var c=jQuery("#opciones").jqGrid('getCell', id, 'cliente').split("-");
	var mes = getMonth(f[1]);
	var year = f[0];
	cliente = c[0];
	loading();
	$("#msgresAltaLineabb").hide();
	$.ajax({
		 type: "POST",			
		url: "/procesarAjaxMenu.php",		
		data: {template:"no",parametro:"FrAccount:"+cliente+",firma:"+hash+",Access:Process,Trxn:stm,Accion:Mensual,Year:"+year+",Month:"+mes+",Formato:"+format+",CustID:"+$("#CustID").val()+"",nomFunc:'estadoscuentacred'},
		dataType: "html",
		success: function(resp){
			$.modal.close();
			$("#msgresAltaLineabb").show();
			$("#resAltaLineabb").html(resp);
			$("#resAltaLineabb").show();
		},
		error: function(resp){
			$.modal.close();
			alert("Error..["+resp+"]");
		}
	});
}

function getMonth(month){
	if(month=="Ene")return '01';
	if(month=="Feb")return '02';
	if(month=="Mar")return '03';
	if(month=="Abr")return '04';
	if(month=="May")return '05';
	if(month=="Jun")return '06';
	if(month=="Jul")return '07';
	if(month=="Ago")return '08';
	if(month=="Sep")return '09';
	if(month=="Oct")return '10';
	if(month=="Nov")return '11';
	if(month=="Dic")return '12';
}

function loading()
{
	var anime;

	anime = "<table width=100% align=center id=\"anime\">";
	anime += "<tr><td align=center style=\"font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 9px; color: #B7B7B7;\">";
	anime += "Cargando";
	anime += "</td></tr>";
	anime += "<tr><td align=center>";
	anime += "<img src=\"https://images.bb.com.mx/sitedesign/bajionet/images/loading.gif\"";
	anime += "style=\"cursor: pointer; border: 0px solid red;\" title=\"cargando\" alt=\"cargando . . .\" align=center />";
	anime += "</td></tr>";
	anime += "</table>";
	
	try	{
		$('#modalLoading').modal({
		  close:false,
		  persist: true,
		  containerCss: {
				height: 75,
				width: 150
				},
			onShow: $('#modalLoading').html(anime),
			onClose: closeConfirm
				});
	}
	catch(e){
		$('#modalLoading').modal({close:false,
			  containerCss: {
					height: 75,
					width: 150
					},
				onClose: closeConfirm
					});
				}
}

</script>
<!-- REGA Se agregan librerías y código javascript para la generación de una tabla modal y jqgrid de las cuentas agrupadas del cliente. -->
<!-- REGA Se agrega div contenedor de la ventana modal de consulta de cuentas agrupadas del cliente. -->
<div id="modalDestinoO" style='display:none' align='center'>

	<div style="width:750; height:320px; align:center;">
	<div class='ui-jqgrid-titlebar ui-widget-header ui-corner-top ui-helper-clearfix' style='width:auto; height:25px;'>
	<span class='ui-jqgrid-title' style='font-size:12px;valign:left;' >&nbsp;Cat&aacute;logo de Cuentas</span></div>
			<div><!--Buscar :-->
			</div>
			<br>
			<div id='error' style='font-size: 11px; color:red;' ></div>
			<div id='resCuentas' align='center'>

				<div id="btnBuscar" style="width:750; height:20px; position: relative; left:22%;">
					<input Class="tooltip" type="input"  size="12" maxlength="12" id="busquedaDestinatario" />
					<input Class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only"  type="button"  id="btnModalBuscar" value ="Buscar" text="Buscar" onclick="javascript: if(isNumberTk($('#codeTkArchivo3').val())) {continuaPago('1','2','3',$('#codeTkArchivo3').val(),$('#cuentas').val());} else {$('#codeTkArchivo3').val(''); $('#auxArcTk').html('El n&uacute;mero de Clave ASB que ha ingresado es incorrecto, intentelo de nuevo.');} " />

			    </div>

				<br>
				<table align="center" id='catalogocuentasOrigen'  border=1 ></table>
				<div id="pagerCatalogoCuentasOrigen"></div>
				<br>
				<div id='botones' style="width:750; height:20px; position: relative; left:30%;">
					<input Class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" type="button"  id="btnModalCierra" value ="Cancelar" text="Cancelar" onclick="javascript:cierraModal();"/>
					<input Class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" type="button"  id="btnModalReset" value ="Limpiar" text="Limpiar"/>
			    </div>

			</div>
			</br>
	</div>
	<!-- REGA Se agrega div contenedor de la ventana modal de consulta de cuentas agrupadas del cliente. -->

</div>
<div id="modalLoading" style='display:none' align='center'/>
<!-- REGA Se agregan inputs tipo hidden para control del proceso. -->
<input type="HIDDEN" id="firma" name="firma" value=""> 
<input type="HIDDEN" id="CustID" name="CustID" value="<?php echo  $session->variable( 'r_usr' );?>">
<input type="HIDDEN" id="Rowno" name="Rowno" value="<?php echo  $session->variable( 'r_cno' );?>">
<input type="HIDDEN" id="hash" name="hash" value="<?php echo  md5($session->variable( 'r_cno' )."|".strtolower(trim($session->variable( 'r_usr' )))."|"."Trn_$$"); ?>">
<input id="Month" name="Month" value=" " type="HIDDEN">
<input id="Year" name="Year" value=" " type="HIDDEN">
<input id="Day" name="Day" value=" " type="HIDDEN">
<input id="Formato" name="Formato" value=" " type="HIDDEN">
<input id="Fecha" name="Fecha" value=" " type="HIDDEN">
<input type="HIDDEN" id="FrAccount" name="FrAccount" value="">
<input type="HIDDEN" id="ToAccount" name="ToAccount" value="">
<input type="HIDDEN" id="TrAccount" name="TrAccount" value="">
<input type="HIDDEN" id="error" name="error" value="">
<!-- REGA Se agregan inputs tipo hidden para control del proceso  . -->
	<?php
	$session->setVariable("calendario",$session->variable("calendario")+1);
	include_once ( "classes/ezlog.php" );
	include_once ( "classes/ezfile.php" );
	include_once ( "ezfilemanager/classes/ezvirtualfile.php" );
	include_once ( "ezfilemanager/classes/ezvirtualfolder.php" );
	include ( "eztransaccion/user/include/httplib.php" );
	include ( "eztransaccion/user/include/tcpipnexions.php" );
//06May2010   ACS  Llave ASB para Circular X I
	include("eztransaccion/user/include/controles_javascript.inc");
//06May2010   ACS  Llave ASB para Circular X F
	include ( "xmlparser.inc" );
	include("estilo.inc");
	require_once("nusoap-0.7.3/lib/nusoap.php");
	include("eztransaccion/user/include/edosCtas.inc");

	$ImageDir = $ini->read_var ( "eZFileManagerMain", "ImageDir" );
	$Limit = $ini->read_var ( "eZFileManagerMain", "Limit" );
	$ShowUpFolder = $ini->read_var ( "eZFileManagerMain", "ShowUpFolder" ) == "enabled";

	// ********************************************************************************************
	//ACS  - MenuContenidoAjax I
	( $parametros [ 'FrAccount' ] != "" ? $FrAccount = $parametros [ 'FrAccount' ] : 0 );
	( $parametros [ 'Formato' ]   != "" ? $Formato   = $parametros [ 'Formato'   ] : 0 );
	( $parametros [ 'Accion2'   ] != "" ? $Accion2   = $parametros [ 'Accion2'   ] : 0 );
	( $parametros [ 'Cadpriv'   ] != "" ? $Cadpriv   = $parametros [ 'Cadpriv'   ] : 0 );
	( $parametros [ 'Reporte'   ] != "" ? $Reporte   = $parametros [ 'Reporte'   ] : 0 );
	( $parametros [ 'Accion'    ] != "" ? $Accion    = $parametros [ 'Accion'    ] : 0 );
	( $parametros [ 'Access'    ] != "" ? $Access    = $parametros [ 'Access'    ] : 0 );
	( $parametros [ 'CustID'    ] != "" ? $usr       = $parametros [ 'CustID'    ] : 0 );
	( $parametros [ 'MonthF'    ] != "" ? $MonthF    = $parametros [ 'MonthF'    ] : 0 );
	( $parametros [ 'Month'     ] != "" ? $Month     = $parametros [ 'Month'     ] : 0 );
	( $parametros [ 'YearF'     ] != "" ? $YearF     = $parametros [ 'YearF'     ] : 0 );
	( $parametros [ 'DayF'      ] != "" ? $DayF      = $parametros [ 'DayF'      ] : 0 );
	( $parametros [ 'File'      ] != "" ? $File      = $parametros [ 'File'      ] : 0 );
	( $parametros [ 'Trxn'      ] != "" ? $Trxn      = $parametros [ 'Trxn'      ] : 0 );
	( $parametros [ 'Year'      ] != "" ? $Year      = $parametros [ 'Year'      ] : 0 );
	( $parametros [ 'Day'       ] != "" ? $Day       = $parametros [ 'Day'       ] : 0 );
	//ACS 0 - MenuContenidoAjax F

	$transaccion_buffer = "";

	if ( !isset /*(HB AGL - Ajustes PHP5)*/ ( $Access ) || empty( $Access ) ) { $Access = "FrAc"; }

	if ( !isset /*(HB AGL - Ajustes PHP5)*/ ( $FrAccount ) ) { $FrAccount = ""; }
	if ( !isset /*(HB AGL - Ajustes PHP5)*/ ( $Formato   ) ) { $Formato   = ""; }
	if ( !isset /*(HB AGL - Ajustes PHP5)*/ ( $Reporte   ) ) { $Reporte   = ""; }
	if ( !isset /*(HB AGL - Ajustes PHP5)*/ ( $Accion    ) ) { $Accion    = ""; }
	if ( !isset /*(HB AGL - Ajustes PHP5)*/ ( $MonthF    ) ) { $MonthF    = ""; }
	if ( !isset /*(HB AGL - Ajustes PHP5)*/ ( $Month     ) ) { $Month     = ""; }
	if ( !isset /*(HB AGL - Ajustes PHP5)*/ ( $YearF     ) ) { $YearF     = ""; }
	if ( !isset /*(HB AGL - Ajustes PHP5)*/ ( $DayF      ) ) { $DayF      = ""; }
	if ( !isset /*(HB AGL - Ajustes PHP5)*/ ( $Year      ) ) { $Year      = ""; }
	if ( !isset /*(HB AGL - Ajustes PHP5)*/ ( $Day       ) ) { $Day       = ""; }
	if ( !isset /*(HB AGL - Ajustes PHP5)*/ ( $i         ) ) { $i         = ""; }

//06May2010   ACS  Llave ASB para Circular X I
$secure_site = $ini->read_var( "site", "SecureServer" ) . $ini->read_var( "site", "ServerNumber" ) . $ini->read_var( "site", "DomainPostfix" );

//IRG - Inicio - Paperless LEM
function completaBuffer($servicioOrigen, $filename, $mes, $anio, $color, $secure_site)
{
	/*$buffer_temporal  = "";
	$buffer_temporal .= "<tr>";
	// IMAGE INI
	$buffer_temporal .= "<td class='form_grid' BGCOLOR=$color width=1% >";
	$buffer_temporal .= "<a ";
	$buffer_temporal .= "onClick=\"getArchivoEstadoCuenta('".$servicioOrigen."', '$filename')\" ";
	$buffer_temporal .= "onMouseOut=\"MM_swapImgRestore()\" ";
	$buffer_temporal .= "onMouseOver=\"MM_swapImage('ezf".$filename."-dl','','https://$secure_site/images/downloadminimrk.gif',1)\" >";
	$buffer_temporal .= "<img name=\"ezf".$filename;
	$buffer_temporal .= "-dl\" src=\"https://$secure_site/images/downloadmini.gif\" border=0 alt=Bajar width=16 height=16 />";
	$buffer_temporal .= "</a>";
	$buffer_temporal .= "</td>";
	// IMAGE FIN

	// ORIGINAL FILE NAME INI
	$buffer_temporal .= "<td class='form_grid' BGCOLOR=$color width=40%>";
	$buffer_temporal .= "<a style='text-decoration: underline' ";
	$buffer_temporal .= "onClick=\"getArchivoEstadoCuenta('".$servicioOrigen."', '$filename')\" ";
	$buffer_temporal .= "onMouseOut=\"MM_swapImgRestore()\" ";
	$buffer_temporal .= "onMouseOver=\"MM_swapImage('ezf".$filename."-dl','','https://$secure_site/images/downloadminimrk.gif',1)\" >";
	$buffer_temporal .= $filename."</a>";
	$buffer_temporal .= "</td>";
	// ORIGINAL FILE NAME FIN

	// DESCRIPTION INI
	$buffer_temporal .= "<td class='form_grid' BGCOLOR=$color width=32%>";
	$buffer_temporal .= "<span class='small'>[". $filename."]</span>";
	$buffer_temporal .= "</td>";
	// DESCRIPTION FIN

	// FILE SIZE INI
	$buffer_temporal .= "<td class='form_grid' BGCOLOR=$color width=10%>";
	$buffer_temporal .= "";
	$buffer_temporal .= "</td>";
	// FILE SIZE FIN

	// FILE DATE INI
	$buffer_temporal .= "<td class='form_grid' BGCOLOR=$color width=15%>";
	$buffer_temporal .= date ( "d/M/Y H:i:s");
	$buffer_temporal .= "</td>";
	// FILE DATE FIN

	$buffer_temporal .= "</tr>";*/
	
	$buffer_temporal  = "";
	$buffer_temporal .= "<tr>";
	$buffer_temporal .= "<td class='form_grid' width=\"1%\" >";
	$buffer_temporal .= "<img src=\"https://$secure_site/images/file.gif\" border=\"0\" alt=\"\" width=\"16\" height=\"16\" border=\"0\" />";
	$buffer_temporal .= "</td>";
	$buffer_temporal .= "<td class='form_grid' width=\"40%\">$filename</td>";
	//$buffer_temporal .= "<td class='form_grid' width=\"56%\"><span class=\"small\">$file_description</span></td>";
	$buffer_temporal .= "<td class='form_grid' width=\"1%\">&nbsp;</td>";
	$buffer_temporal .= "<td class='form_grid' width=\"1%\">";
	$buffer_temporal .= "<a onClick=\"getArchivoEstadoCuenta('$servicioOrigen','$filename')\"";
	$buffer_temporal .= "onMouseOver=\"MM_swapImage('ezf$filename-dl','','https://$secure_site/images/downloadmini.gif',1)\">";
	$buffer_temporal .= "<img name=\"ezf$filename-dl\" border=\"0\" src=\"https://$secure_site/images/downloadmini.gif\" width=\"16\" height=\"16\" align=\"top\" alt=\"Bajar\" /></a>";		
	$buffer_temporal .= "</td>";
	$buffer_temporal .= "</tr>";
	$buffer_temporal .= "<tr><td colspan=\"5\">&nbsp;</td></tr>";

	return $buffer_temporal;
}
//IRG - Fin - Paperless LEM

$cliente_token="
					</tr>
				</table>
			</td>
		</tr>
		<tr><td>
		<form id=token_form method=post action='/procesarAjaxMenu.php' onsubmit= \"validaTknUser('edoctacred_ajax'); return false;\">
		<input type='hidden' id='nomFunc' name='nomFunc' value='clienteasb'>
		<input type='hidden' id='btn_opcion' name='btn_opcion'>
		<table width=98% align=center border=0 class=form_tbl>
			<tr>
				<td colspan=3 align=center>
					<table border=1 width=60% height=60px>
						<tr>
							<td colspan=2 border=1><b> Es necesario capturar su Clave ASB (Acceso Seguro Bajio)</b></td>
						</tr>
						<tr>
							<td colspan=2 align=center >
								<b>Clave ASB (Acceso Seguro Bajio):</b>
								<input type=password name=token id=token_value class=inputbox  size=15 maxlength=10 onKeyPress=\"if (token_valido(event,this) == true){ if (is_Enter(event) == true){validaTknUser('edoctacred_ajax');}}else{return false}\" ></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>
				</td>
				<td align=center>
					<input type=button name=btn_opcion id=btn_opcionA value=Aceptar class=button onmouseover=\"this.className='buttonON'\" onmouseout=\"this.className='button'\" onclick=\"if (validar_token('token_value','btn_opcionA')) { validaTknUser('edoctacred_ajax');}\" >
					<input type=button name=btn_opcion id=btn_opcionC value=Cancelar class=button onmouseover=\"this.className='buttonON'\" onmouseout=\"this.className='button'\" onclick=\"document.location.href='https://$secure_site/article/articleview/196/1/7/'\" >
				</td>
				<td align=left></td>
				<td></td>
			</tr>
		</table>
	</form>";
//06May2010   ACS  Llave ASB para Circular X F

	$filename = "";
	
	// [AETG:20141015] Omisión de clave ASB si ya se capturó una vez.
	if($Access != "FrAc"){
		$session->setVariable("AccessEdoCtaCred",1);
		$session->setVariable("AccionEdoCtaCred",1);
	}
	if($Access == "FrAc" && $session->variable("AccessEdoCtaCred")>0 && $session->variable("AccionEdoCtaCred")>0){
		$Access="Confirm";
		$Accion="Mensual";
	}
	// [AETG:20141015] Omisión de clave ASB si ya se capturó una vez.

	switch ( $Access )
	{
		case "FrAc":
			{
			$t = new eZTemplate ( "eztransaccion/user/" . $ini->read_var ( "eZTransaccionMain", "TemplateDir" ),
								  "eztransaccion/user/intl/", $Language, "edoctacred.php" );
			$t->setAllStrings();
			$t->set_file ( array ( "edoctacred_tpl" => "edoctacred.tpl" ) );
			$particularFields = "";
			$transaccion_buffer = "";
			$ret_code = $tr->PostToHost ( $backend, "/IBnkIIS.dll", "Trxn=stm&Access=FrAc&TipoEdo=CorteC&CustID=" . // JAC MAY2011 - ADAPTACION ESTADOS DE CUENTA DE CREDITOS.
				urlencode ( $usr ) . "&Cadpriv=" .
				urlencode ( $priv ) . $particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer );
			$t->set_var ( "transaccion_buffer", $transaccion_buffer );
			$transaccion_buffer = $t->parse   ( "output", "edoctacred_tpl" );
			// $transaccion_buffer = str_replace ( "type=\"SUBMIT\"", "type=\"BUTTON\"", $transaccion_buffer );
			// $transaccion_buffer = str_replace ( "onclick=\"javascript:{ document.getElementById( 'OK' ).disabled = true; document.forms[ 0 ].submit(); }\"",
			//	"onclick=\"javascript:procesarAjax();\"", $transaccion_buffer );
				// JAC - FEB2011 - INI Cambios solicitados
				// TEMPORAL HASTA QUE SE IMPLEMENTE EL MODULO DE ESTADO DE CUENTA DE CREDITO AL CORTE.
				// $html .= "<td align=center><input id=Accion name=Accion type=radio value=Corte disabled />Al corte</td>";
				// $html .= "</tr><tr>";
				// echo "<BR/>Comienza reemplazo";
				// JAC MAR2012 MODIFICACION SOLICITADA MODULO DE CREDITOS INI
				// $html = "<li>Mensual, Estado de Cuenta Oficial Mensual a partir de Enero de 2011.</li>";
				$html = "";
				// JAC MAR2012 MODIFICACION SOLICITADA MODULO DE CREDITOS FIN
				// echo "<BR/>Calcula posINI";
				$posrplINI = strpos ( $transaccion_buffer, "<!--rpl-->" );
				// echo "<BR/>Obtiene substr $posrplINI";
				$substr = substr ( $transaccion_buffer, 0, $posrplINI );
				// echo "<BR/>Calcula posFIN";
				$posrplFIN = strpos ( $transaccion_buffer, "<!--rpl-->", $posrplINI + 10 );
				// echo "<BR/>Obtiene substrCONT $posrplFIN";
				$substrCONT = substr ( $transaccion_buffer, $posrplFIN + 10, strlen ( $transaccion_buffer ) );

				$transaccion_buffer = $substr . $html . $substrCONT;

				$posASB = strpos ( $transaccion_buffer, "<!--ASB-->" );
				if ( $posASB != 0 )
				{
					$subASB_INI = substr ( $transaccion_buffer, 0, $posASB );
					$posASB_FIN = strpos ( $transaccion_buffer, "<!--ASB-->", $posASB + 10 );
					$subASB_FIN = substr ( $transaccion_buffer, $posASB_FIN + 10, strlen ( $transaccion_buffer ) );

					$transaccion_buffer = $subASB_INI . $subASB_FIN;
				}

				$html = "";
				$html .= "<td align=center><input id=Accion name=Accion type=radio value=Mensual checked />Mensual</td>";
				// $postagi = strpos ( $transaccion_buffer, "<!--rpl-->" );
				$postagi = strpos ( $transaccion_buffer, "<!--rpl-->", $posrplFIN + 10 );
				$substri = substr ( $transaccion_buffer, 0, $postagi );
				$postagf = strpos ( $transaccion_buffer, "<!--rpl-->", $postagi + 10 );
				$substrf = substr ( $transaccion_buffer, $postagf + 10, strlen( $transaccion_buffer ) );

				$transaccion_buffer = $substri . $html . $substrf;
				// JAC - FEB2011 FIN
//06May2010   ACS  Llave ASB para Circular X I
			if ($session->variable( "r_tknOp" ) == "no" )
			{
				//$transaccion_buffer=str_replace("<form method=\"post\">","<form method=\"post\" onsubmit='return false'>",$transaccion_buffer);
				$transaccion_buffer=str_replace("type=\"SUBMIT\"","type=\"BUTTON\"",$transaccion_buffer);
				$transaccion_buffer=str_replace("onclick=\"javascript:{ document.getElementById( 'OK' ).disabled = true; document.forms[ 0 ].submit(); }\"","onclick=\"javascript:procesarAjax();\"",$transaccion_buffer);
				//Se Agrega instricción para el token
				/*
				$instruccion="<LI>Es necesario capturar su <b>Clave ASB (Acceso Seguro Bajio)</b>, la cual est&aacute; formada por 10 d&iacute;gitos (4 d&iacute;gitos de su <b>NIP</b> actual m&aacute;s 6 d&iacute;gitos de su <b>Clave Din&aacute;mica</b>), para completar la transacci&oacute;n.<LI>Presione bot&oacute;n <EM>Aceptar</EM>";
				$transaccion_buffer=str_replace("<LI>Presione bot&oacute;n <EM>Aceptar</EM>",$instruccion, $transaccion_buffer);
				$transaccion_buffer=str_replace("<li>Presione el bot&oacute;n <em>Aceptar</em>",$instruccion, $transaccion_buffer);*/
			}
			else
			{
				$transaccion_buffer=str_replace("<input id=\"OK\" name=\"OK\" onclick=\"javascript:{ document.getElementById( 'OK' ).disabled = true; document.forms[ 0 ].submit(); }\" type=\"SUBMIT\" value=\"Aceptar\">",$cliente_token,$transaccion_buffer);
			}
//06May2010   ACS  Llave ASB para Circular X F
			$tr->blog ( $qki, "EstadosDeCuenta", $particularFields, "", "", "", "" );

			break;
			}
		case "Confirm":
		{

			$t = new eZTemplate ( "eztransaccion/user/" . $ini->read_var ( "eZTransaccionMain", "TemplateDir" ),
								  "eztransaccion/user/intl/", $Language, "edoctacred.php" );
			$t->setAllStrings();
			$t->set_file ( array( "edoctacred_tpl" => "edoctacred.tpl" ) );
			$particularFields = "&Accion=" . $Accion;


			$transaccion_buffer = "";

			$ret_code = $tr->PostToHost ( $backend, "/IBnkIIS.dll", "Trxn=stm&Access=Confirm&CustID=" .
				urlencode ( $usr ) . "&Cadpriv=" .
				urlencode ( $priv ) . $particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer );


			$t->set_var ( "transaccion_buffer", $transaccion_buffer );
			$transaccion_buffer = $t->parse   ( "output", "edoctacred_tpl" );
			$transaccion_buffer = str_replace ( "TYPE=\"SUBMIT\"", "TYPE=\"BUTTON\"", $transaccion_buffer );
			$transaccion_buffer = str_replace ( "ONCLICK=\"DisabledButton()\"", "ONCLICK=\"javascript:procesarAjax();\"", $transaccion_buffer );
			$transaccion_buffer = str_replace ( "type=\"submit\"", "type=\"BUTTON\"", $transaccion_buffer );
			$transaccion_buffer = str_replace ( "onclick=\"javascript:{ document.getElementById( 'OK' ).disabled = true; document.forms[ 0 ].submit(); }\"",
				"onclick=\"javascript:procesarAjax();\"", $transaccion_buffer );
				// JAC - FEB2011 - INI Cambios solicitados.
				// TEMPORAL HASTA QUE SE IMPLEMENTE EL MODULO DE ESTADO DE CUENTA DE CREDITO AL CORTE.
				$html = "";
				// JAC 12Mar2012 MODIFICACION SOLICITADA MODULO DE CREDITOS INI
				//$html .= "<li>Seleccione el mes y a&ntilde;o que requiera, s&oacute;lo puede obtener Estados de Cuenta Mensuales </li>";	//IRG - 210812  //IRG - Paperless LEM
				//IRG260412 - Inicio - Paperless
				//$html .= "<li>Seleccione el mes y a&ntilde;o que requiera, s&oacute;lo puede obtener Estados de Cuenta Mensuales de los &uacute;ltimos 3 meses</li>";
				$html .= "<li>Seleccione el n&uacute;mero de cliente del cual requiere realizar la consulta, para ello presione la imagen de b&uacute;squeda (lupa). Se mostrar&aacute;n los estados de cuenta de los &uacute;ltimos 12 meses.</li>";	//IRG - 210812   //IRG - Paperless LEM
				//IRG260412 - Fin - Paperless
				// JAC 12Mar2012 MODIFICACION SOLICITADA MODULO DE CREDITOS FIN
				$posINI = strpos ( $transaccion_buffer, "<!--rpl-->" );
				$rplINI = substr ( $transaccion_buffer, 0, $posINI );
				$posFIN = strpos ( $transaccion_buffer, "<!--rpl-->", $posINI + 10 );
				$rplFIN = substr ( $transaccion_buffer, $posFIN + 10, strlen ( $transaccion_buffer ) );

				$transaccion_buffer = $rplINI . $html . $rplFIN;

			// SE REEMPLAZA EL SELECT
			/*
			$posINI = strpos ( $transaccion_buffer, "<SELECT NAME=\"Year\">" );
			$strINI = substr ( $transaccion_buffer, 0, $posINI + 20 );
			$posFIN = strpos ( $transaccion_buffer, "</SELECT>", $posINI + 20 );
			$strFIN = substr ( $transaccion_buffer, $posFIN, strlen ( $transaccion_buffer ) );

			$startYear = 2011;
			$opt = "<OPTION VALUE=$startYear>$startYear</OPTION>";
			$year = date ( "Y" );
			$selected = "";	//IRG - 210812    //IRG - Paperless LEM
			while ( $startYear < $year )
			{
				$startYear = $startYear + 1;
				//IRG - Inicio - Paperless LEM
				//IRG - Inicio - 210812
				if ( $startYear == $year ) 
				{
					$selected .= "selected";
				}
				//IRG - Fin - 210812
				//IRG - Fin - Paperless LEM
				$opt .= "<OPTION VALUE=$startYear $selected>$startYear</OPTION>";
			}

			$transaccion_buffer = $strINI . $opt . $strFIN;
			*/
			// JAC - FEB2011 - FIN
			// JAC MAY2011 INI - ADAPTACION ESTADOS DE CUENTA DE CREDITOS
			//IRG260412 - Inicio - Paperless
				//IRG - Inicio - Paperless LEM
				//IRG - Inicio - 210812
				/*$posINI = strpos ( $transaccion_buffer, "<!--rpl-->" );
				$rplINI = substr ( $transaccion_buffer, 0, $posINI );
				$posFIN = strpos ( $transaccion_buffer, "<!--rpl-->", $posINI + 10 );
				$rplFIN = substr ( $transaccion_buffer, $posFIN + 10, strlen ( $transaccion_buffer ) );
				
				$transaccion_buffer = $rplINI . $rplFIN; */
				//IRG - Fin - 210812
				//IRG - Fin - Paperless LEM
			//IRG260412 - Fin - Paperless
			// JAC MAY2011 FIN - ADAPTACION ESTADOS DE CUENTA DE CREDITOS
			$tr->blog ( $qki, "EstadosDeCuenta", $particularFields, "", "", "", "" );

			break;
		}
		case "Process":
		{
			switch ( $Accion )
			{
				case 'Corte': break;	// NO IMPLEMENTADO.
				case 'Mensual':
				{
					$transaccion_buffer = "";	//IRG - 210812    //IRG - Paperless LEM
					$encontradosPaperless = $encontradosMonturas = 0;	//IRG - Paperless LEM
					$t = new eZTemplate ( "eztransaccion/user/" . $ini->read_var ( "eZTransaccionMain", "TemplateDir" ),
						                 "eztransaccion/user/intl/", $Language, "edoctacred.php" );
					$t->setAllStrings();
					
					$Formato = trim( $Formato );	//IRG - 210812   //IRG - Paperless LEM
					
					$t->set_file ( array ( "edoctacred_tpl" => "edoctacred_process.tpl" ) );
					$session =& eZSession::globalSession();

					if ( !$session->fetch() )
					{
						$session->store();
					}

					if( !isset /*(HB AGL - Ajustes PHP5)*/( $FrAccount ) )
					{
						$FrAccount = "0";
					}

					$tr = new eZTransaccion();
					$usr  = $session->variable ( "r_usr" );
					$qki  = $session->variable ( "r_qki" );
					$priv = $session->variable ( "r_priv" );

					$result = 0;
					//LGAM Inicio Validación para Edo. Ctas. sin fecha. 09/09/2013
					if ($Month == " " and $Year == " ")
					{
						if (date("m") == 1) {
							$Month = 12;
							$Year = (date("Y")-1);
						}
						else {
							$Month = (date("m")-1);
							$Year = date("Y");
							if ( strlen($Month) == 1)
							{
								$Month = "0".$Month;
							}
						}
					}
					//echo "LGAM Mes...[".$Month."]...Anio...[".$Year."]";
					//LGAM Fin Validación para Edo. Ctas. sin fecha. 09/09/2013
					//IRG - Inicio - Paperless LEM
					//IRG - Inicio - 210812
					$month = array( "", "enero", "febrero", "marzo", "abril", "mayo", "junio", "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre" );
					
					//IRG090512 - Inicio - Paperless
					//Se valida que para el formato xml se seleccione una fecha posterior a Dic de 2011
					//y se valida que la fecha seleccionada no sea superior a la fecha actual.

					/*if(($Formato == "xml" && $Year == "2011") ||
					(( $Year . $Month ) >= ( date( "Y", ( time() - ( 60 * 60 * 24 * 5 ) ) ) . date( "m", ( time() - ( 60 * 60 * 24 * 5 ) ) ) ) ))*/
					if(($Formato == "xml" && $Year == "2011") || 
					(( $Year . $Month ) > date("Y").date("m")))
					{
						$transaccion_buffer = "<p>No existe el Estado de Cuenta correspondiente al mes de <b>" . $month[ intval( $Month ) ] ."</b> de <b>" . $Year . "</b>.</p>";
						$transaccion_buffer .= "Si desea descargar otro Estado de Cuenta haga clic <a href=\"/transaccion/edoctacre/\">aqu&iacute;</a>.";
						//return;
					}
					else
					{
						$FrAccount = trim( $FrAccount );
						//$servicioOrigen = $Formato == "pdf" ? "CREDITOCONS" : "CREDITOCONSXML";
						$servicioOrigen = ($Formato == "pdf") ? "CREDITOLEM" : "CREDITOLEMXML";

						//::::::::::::::::::::::::::::LLAMADO AL WS::::::::::::::::::::::::::::::::::::::
						$edosExistentes = countEstadosCta($servicioOrigen, $FrAccount, $Year, $Month, $Formato);
						//::::::::::::::::::::::::::::LLAMADO AL WS::::::::::::::::::::::::::::::::::::::
						$numEdosConsumo = 0;
						$numEdosHipot 	= 0;
						$numEdosLem 	= 0;

						//Se crea la tabla de resultados
						$transaccion_buffer .=  "<PRE>";
						$transaccion_buffer .= "<table width=85% border=0 cellspacing=0 cellpading=4 >";
						
						$i = 0;
						$secure_site = $ini->read_var ( "site", "SecureServer"  ) .
									$ini->read_var ( "site", "ServerNumber"  ) .
									$ini->read_var ( "site", "DomainPostfix" );
						
						//Se valida del response si existen estados de cuenta en paperless para la fecha seleccionada
						for($x=0; $x < count($edosExistentes["vals"]); $x++){
							if( $edosExistentes["vals"][$x]["tag"] == "CUENTA"
								&& $edosExistentes["vals"][$x]["attributes"]["TIPO"] == "consumo"
								&& $edosExistentes["vals"][$x]["attributes"]["NUMERO"] > 0){
								$numEdosConsumo = 1;
								$encontradosPaperless++;
								
								$color = ( $i % 2 ) ? "#FFFFFF" : "#EFEFF0";
								$i++;
								
								$transaccion_buffer .= completaBuffer((strtoupper(trim($Formato)) == "PDF") ? "CREDITOCONS" : "CREDITOCONSXML", $FrAccount."-CONS-".$Year.$Month.".".$Formato, $month[ intval( $Month ) ], $Year, $color, $secure_site);	
								
							} else if( $edosExistentes["vals"][$x]["tag"] == "CUENTA"
								&& $edosExistentes["vals"][$x]["attributes"]["TIPO"] == "hipot"
								&& $edosExistentes["vals"][$x]["attributes"]["NUMERO"] > 0){
								$numEdosHipot = 1;
								$encontradosPaperless++;
								
								$color = ( $i % 2 ) ? "#FFFFFF" : "#EFEFF0";
								$i++;
								
								$transaccion_buffer .= completaBuffer((strtoupper(trim($Formato)) == "PDF") ? "CREDITOHIPOT" : "CREDITOHIPOTXML", $FrAccount."-HIPO-".$Year.$Month.".".$Formato, $month[ intval( $Month ) ], $Year, $color, $secure_site);
								
							} else if( $edosExistentes["vals"][$x]["tag"] == "CUENTA"
								&& $edosExistentes["vals"][$x]["attributes"]["TIPO"] == "lem"
								&& $edosExistentes["vals"][$x]["attributes"]["NUMERO"] > 0){
								$encontradosPaperless++;
								
								$numEdosLem = $edosExistentes["vals"][$x]["attributes"]["NUMERO"];
								$lineasLem = array();
								
								for($y=0; $y < count($edosExistentes["vals"]); $y++){
									if( $edosExistentes["vals"][$y]["tag"] == "LEM"
										&& $edosExistentes["vals"][$y]["attributes"]["LINEA"] != "")
									{
										$lineaObtenida = $edosExistentes["vals"][$y]["attributes"]["LINEA"];
										$z = 0;
										while(substr($lineaObtenida, $z, 1) == "0" and 
											$z < strlen($lineaObtenida))
										{$z++; }
										$lineaObtenida = substr($lineaObtenida, $z, strlen($lineaObtenida));
										
										$color = ( $i % 2 ) ? "#FFFFFF" : "#EFEFF0";
										$i++;

										$transaccion_buffer .= completaBuffer((strtoupper(trim($Formato)) == "PDF") ? "CREDITOLEM" : "CREDITOLEMXML", $FrAccount."-LINEA-".$lineaObtenida.".".$Formato, $month[ intval( $Month ) ], $Year, $color, $secure_site);
									}
								}
							}
						}
						/*
						if($numEdosConsumo != 0 || $numEdosHipot != 0 || $numEdosLem != 0){
							$transaccion_buffer .=  "<PRE>";
							$transaccion_buffer .= "<table width=85% border=0 cellspacing=0 cellpading=4 >";
							
							if($numEdosConsumo != 0)
							{
								$transaccion_buffer .= completaBuffer(($Formato == "PDF") ? "CREDITOCONS" : "CREDITOCONSXML", $FrAccount."_CONS_".$Year.$Month.".".$Formato, $month[ intval( $Month ) ], $Year);	
							}
							
							if($numEdosHipot != 0)
							{
								$transaccion_buffer .= completaBuffer(($Formato == "PDF") ? "CREDITOHIPOT" : "CREDITOHIPOTXML", $FrAccount."_HIPO_".$Year.$Month.".".$Formato, $month[ intval( $Month ) ], $Year);
							}
							
							if($numEdosLem != 0)
							{
								$transaccion_buffer .= completaBuffer(($Formato == "PDF") ? "CREDITOLEM" : "CREDITOLEMXML", $FrAccount."_LINEA_".$Year.$Month.".".$Formato, $month[ intval( $Month ) ], $Year);
							}
						}
/////////////////////////////////   //IRG - Inicio - Paperless LEM
						if($encontradosPaperless < 1)
						{
							$transaccion_buffer .=  "<PRE>";
							$transaccion_buffer .= "<table width=85% border=0 cellspacing=0 cellpading=4 >";
						}*/
						if(strtoupper(trim($Formato)) == "PDF")
						{
							$elimina_serv = $ini->read_var ( "site", "EliminaServices" ); // Si o No.
							if ( strtoupper( trim ( $elimina_serv ) ) == "SI" )
							{ $base_dir = '/var/www/repo_eec/'; }
							else
							{ $base_dir = '/var/www/data/'; }
							$base_dir .= "edocta/paso/";
							$base_dir .= $Year . "/" . $Month . "/";
							$ruta = explode ( "/",  $PHP_SELF );
							$n = count ( $ruta );
							for ( $w = 0; $w < $n - 3; $w++ )
							{
								if ( $ruta [ 3 + $w ] != "" )
								{
									$p .= $ruta [ 3 + $w ] . "/";
								}
							}
							$path = $base_dir . $p;

							$error_desc = "";
							$secure_site = $ini->read_var ( "site", "SecureServer"  ) .
										$ini->read_var ( "site", "ServerNumber"  ) .
										$ini->read_var ( "site", "DomainPostfix" );


							if ( ! $dir_handle = @opendir( $path ) )
							{
							eZLog::writeNotice("EdoCtaCre.....Confirmar>>>12>>$path>>..");
								$transaccion_buffer = "";
								$error_desc = 'access_deny';
							}
							else
							{
								$cant_files = 0;
								if ( $path != "." )
								{
									$files = array();
									while ( $file = @readdir ( $dir_handle ) )
									{
										$pos = strpos ( $file, "-" );
										//if ( $file != "." && $file != ".." && $file != "ThisIsEec" && ( substr ( $file, 0, $pos ) == $FrAccount ) )
										if ( $file != "." && $file != ".." && $file != "ThisIsEec" && ( substr ( $file, 0, $pos ) == $FrAccount ) && strtoupper(substr( $file, ($pos + 1), 5 )) != "LINEA" )
										{
											$count = count( $files );
											$files [ $count ][ "key"         ] = date ( "YmdHis", filemtime ( $path . $file ) );
											$files [ $count ][ "name"        ] = $file;
											$files [ $count ][ "size"        ] = ( filesize ( $path . $file ) / 1024 );
											$files [ $count ][ "date"        ] = date ( "d/M/Y H:i:s", filemtime ( $path . $file ) );
											$files [ $count ][ "description" ] = $file;
											$files [ $count ][ "file_id"     ] = "";

											$cant_files++;
											$encontradosMonturas++;
										}
									}
									if ( isset( $files ) )
									{
										if ( $cant_files == 0 )
										{
											$error_desc = 'empty';
										}
										rsort ( $files );
										//$transaccion_buffer .= "<table width=85% border=0 cellspacing=0 cellpading=4 >";
//										$i = 0;
										foreach ( $files as $file )
										{
											$color = ( $i % 2 ) ? "#FFFFFF" : "#EFEFF0";

											$transaccion_buffer .= "<tr>";
											// IMAGE INI
											$transaccion_buffer .= "<td class='form_grid' BGCOLOR=$color width=1% >";
											//$transaccion_buffer .= "<a href=\"/access/getEdoCta/edocta/paso/$Year/$Month/" . $p .$file[ "name" ] . "\" ";
											$transaccion_buffer .= "<a onClick=\"getEstadoCuentaPaper('".$servicioOrigen."', '".$file[ "name" ]."','$Month', '$Year')\" ";
											$transaccion_buffer .= "onMouseOut=\"MM_swapImgRestore()\" ";
											$transaccion_buffer .= "onMouseOver=\"MM_swapImage('ezf" . $file [ "name" ] ."-dl','','https://$secure_site/images/downloadminimrk.gif',1)\" >";
											$transaccion_buffer .= "<img name=\"ezf" . $file [ "name" ];
											$transaccion_buffer .= "-dl\" src=\"https://$secure_site/images/downloadmini.gif\" border=0 alt=Bajar width=16 height=16 />";
											$transaccion_buffer .= "</a>";
											$transaccion_buffer .= "</td>";
											// IMAGE FIN

											// ORIGINAL FILE NAME INI
											$transaccion_buffer .= "<td class='form_grid' BGCOLOR=$color width=40%>";
											// $transaccion_buffer .= "<a href=\"/access/getEdoCta/edocta/paso/$Year/$Month/". $p . $file[ "name" ];
											//$transaccion_buffer .= "<a href=\"/access/getEdoCta/edocta/paso/$Year/$Month/". $p . $file[ "name" ] . "\" ";
											$transaccion_buffer .= "<a style='text-decoration: underline' onClick=\"getEstadoCuentaPaper('".$servicioOrigen."', '".$file[ "name" ]."','$Month', '$Year')\" ";
											$transaccion_buffer .= "onMouseOut=\"MM_swapImgRestore()\" ";
											$transaccion_buffer .= "onMouseOver=\"MM_swapImage('ezf" . $file [ "name" ] ."-dl','','https://$secure_site/images/downloadminimrk.gif',1)\" >";
											// $transaccion_buffer .= "\">";
											$transaccion_buffer .= $file[ "name" ] . "</a>";
											$transaccion_buffer .= "</td>";
											// ORIGINAL FILE NAME FIN

											// DESCRIPTION INI
											$transaccion_buffer .= "<td class='form_grid' BGCOLOR=$color width=32%>";
											$transaccion_buffer .= "<span class='small'>[" . $file[ "description" ] ."]</span>";
											$transaccion_buffer .= "</td>";
											// DESCRIPTION FIN

											// FILE SIZE INI
											$transaccion_buffer .= "<td class='form_grid' BGCOLOR=$color width=10%>";
											$transaccion_buffer .= "";//sprintf( "%01.2f", $file[ "size" ] ) . " KBytes";
											$transaccion_buffer .= "</td>";
											// FILE SIZE FIN

											// FILE DATE INI
											$transaccion_buffer .= "<td class='form_grid' BGCOLOR=$color width=15%>";
											$transaccion_buffer .= $file [ "date" ];
											$transaccion_buffer .= "</td>";
											// FILE DATE FIN
											$transaccion_buffer .= "</tr>";
											$i++;
										}
										//$transaccion_buffer .=  "</table>";
									}
								}
								elseif ( $path == ".")
								{
									while ( $file = readdir ( $dir_handle))
									{
											// Protect hidden files from being listed
											if ( $file [ 0 ] == '.' )
											{
												continue;
											}

											// filename - output left-justified
											$t = "<a href=\"/access/get/$file\">$file</a>";
											echo $t.substr ( $space, 0, 40 - strlen ( $file ) );

											// filesize - output right-justified
											$t = ( filesize ( $file ) / 1024 );
											$t = sprintf ( "%01.2f", $t ) . "kb ";
											echo substr ( $space, 0, 10 - strlen ( $t ) ) . $t;

											// filedate - output left-justified
											$t = date ( "d.M Y H:i:s", filemtime ( $file ) );
											echo $t.substr ( $space, 0, 20 - strlen ( $file ) );
											echo "\n";
									}
								}
								//closing the directory
								closedir ( $dir_handle );
							}
						}

						if($encontradosPaperless > 0 || $encontradosMonturas > 0)
						{
							$transaccion_buffer .=  "</table>";
							$transaccion_buffer .=  "</PRE>";
							$t->set_var ( "transaccion_buffer", "" );
							//$t->pparse( "output", "edoctacred_tpl" );
							$t->pparse( "output" ); //DMOS mostrar edos de cuenta
						}
/////////////////////////////////   //IRG - Fin - Paperless LEM
						else if ( ( $Year . $Month ) < ( date( "Y", ( time() - ( 60 * 60 * 24 * 30.4 * 15 ) ) ) . date( "m", ( time() - ( 60 * 60 * 24 * 30.4 * 15 ) ) ) ) )
						{
							$transaccion_buffer = "<p>No existe el Estado de Cuenta correspondiente al mes de <b>" . $month[ intval( $Month ) ] ."</b> de <b>" . $Year . "</b>.</p><p>Recuerde que s&oacute;lo puede obtener Estados de Cuenta Mensuales de 15 meses atr&aacute;s.</p>";
						}
						else
						{
							$transaccion_buffer = "<p>A&uacute;n no ha sido generado el Estado de Cuenta correspondiente al mes de <b>" . $month[ intval( $Month ) ] ."</b> de <b>" . $Year . "</b>.</p><p>Favor de volverlo a intentar 5 d&iacute;as despu&eacute;s del d&iacute;a de corte.</p>";
						}
					}
					//IRG - Fin - 210812
					//IRG - Fin - Paperless LEM
					/* RAI - Inicio cambio ruta dependiendo esquema */
					//IRG - Inicio - 210812
					///DMOS
					$elimina_serv = $ini->read_var ( "site", "EliminaServices" ); // Si o No.	//IRG - 210812
					$FrAccount = trim( $FrAccount );	//IRG - 210812
					//IRG - Inicio - 210812
					if ( strtoupper( trim ( $elimina_serv ) ) == "SI" )
					{
						$base_dir = '/var/www/repo_eec/';	
					}
					else
					{
						$base_dir = '/var/www/data/';
					}
					//IRG - Fin - 210812
					//IRG - Fin - Paperless LEM
					/* RAI - Fin cambio ruta dependiendo esquema */
					//IRG - Inicio - Paperless LEM
					//IRG - Inicio - 210812
					/*$base_dir .= "edocta/paso/";	// FORMA PARTE DE LA ESTRUCTURA DEL DIRECTORIO?
					
					$base_dir .= $Year . "/" . $Month . "/";
					$ruta = explode ( "/",  $_SERVER['PHP_SELF'] );
					$n = count ( $ruta );
					for ( $i = 0; $i < $n - 3; $i++ )
					{
						if ( $ruta [ 3 + $i ] != "" )
						{
							$p .= $ruta [ 3 + $i ] . "/";
						}
					}
					$path = $base_dir . $p;*/
					
					$transaccion_buffer = "";
					$error_desc = "";
					$secure_site = $ini->read_var ( "site", "SecureServer"  ) .
								   $ini->read_var ( "site", "ServerNumber"  ) .
								   $ini->read_var ( "site", "DomainPostfix" );
					/*if ( ! $dir_handle = @opendir( $path ) )
					{
						$transaccion_buffer = "";
						$error_desc = 'access_deny';
						eZLog::writeNotice("ERROR_DESC>>>>>>>>>>>>>".$error_desc."<<<<<<<<<<<<<<<<<<<");
					}
					else
					{*/
						//DMOS PARA VISUALIZACION TAMBIEN DE ESTADO DE CUENTA DE CREDITO DE REPOSITORIO Y PAPERLESS
						$transaccion_buffer .=  "<PRE>";
						$cant_files = 0;
						$transaccion_buffer .=  "<PRE>";
						$transaccion_buffer .= "<table width=85% border=0 cellspacing=0 cellpading=4 >";
						$color = ( $i % 2 ) ? "#FFFFFF" : "#FFFFFF";
						//$transaccion_buffer .= completaBuffer((strtoupper(trim($Formato)) == "PDF") ? "CREDITOLEM" : "CREDITOLEMXML", $FrAccount."-LINEA-".$lineaObtenida.".".$Formato, $month[ intval( $Month ) ], $Year, $color, $secure_site);//DMOS 
						for($y=0; $y < count($edosExistentes["vals"]); $y++){
						if( $edosExistentes["vals"][$y]["tag"] == "LEM"
							&& $edosExistentes["vals"][$y]["attributes"]["LINEA"] != "")
						{
							$lineaObtenida = $edosExistentes["vals"][$y]["attributes"]["LINEA"];
							$z = 0;
							while(substr($lineaObtenida, $z, 1) == "0" and 
								$z < strlen($lineaObtenida))
							{$z++; }
							$lineaObtenida = substr($lineaObtenida, $z, strlen($lineaObtenida));
							
							$color = ( $i % 2 ) ? "#FFFFFF" : "#EFEFF0";
							$i++;

							$transaccion_buffer .= completaBuffer((strtoupper(trim($Formato)) == "PDF") ? "CREDITOLEM" : "CREDITOLEMXML", $FrAccount."-LINEA-".$lineaObtenida.".".$Formato, $month[ intval( $Month ) ], $Year, $color, $secure_site);
									}
						}
						$transaccion_buffer .= "</table>";
						//DMOS PARA VISUALIZACION TAMBIEN DE ESTADO DE CUENTA DE CREDITO DE REPOSITORIO Y PAPERLESS
						if ( $path != "." )
						{
							$files = array();
						
							while ( $file = @readdir ( $dir_handle ) )
							{
								$pos = strpos ( $file, "-" ); // JAC 13APR2011
								if ( $file != "." && $file != ".." && $file != "ThisIsEec" && ( substr ( $file, 0, $pos ) == $FrAccount ) ) // JAC 13APR2011
								{
									$count = count( $files );
									$files [ $count ][ "key"         ] = date ( "YmdHis", filemtime ( $path . $file ) );
									$files [ $count ][ "name"        ] = $file;
									$files [ $count ][ "size"        ] = ( filesize ( $path . $file ) / 1024 );
									$files [ $count ][ "date"        ] = date ( "d/M/Y H:i:s", filemtime ( $path . $file ) );
									$files [ $count ][ "description" ] = $file;
									$files [ $count ][ "file_id"     ] = "";

									$cant_files++;
								}
							}
							if ( isset( $files ) )
							{
								if ( $cant_files == 0 )
								{
									$error_desc = 'empty';
								}
								rsort ( $files );
								$transaccion_buffer .= "<table width=85% border=0 cellspacing=0 cellpading=4 >";
								$i = 0;
								foreach ( $files as $file )
								{
									$color = ( $i % 2 ) ? "#FFFFFF" : "#EFEFF0";

									$transaccion_buffer .= "<tr>";
									// IMAGE INI
									$transaccion_buffer .= "<td class='form_grid' BGCOLOR=$color width=1% >";
									$transaccion_buffer .= "<a href=\"/access/getEdoCta/edocta/paso/$Year/$Month/" . $p .$file[ "name" ] . "\" ";
									$transaccion_buffer .= "onMouseOut=\"MM_swapImgRestore()\" ";
									$transaccion_buffer .= "onMouseOver=\"MM_swapImage('ezf" . $file [ "name" ] ."-dl','','https://$secure_site/images/downloadminimrk.gif',1)\" >";
									$transaccion_buffer .= "<img name=\"ezf" . $file [ "name" ];
									$transaccion_buffer .= "-dl\" src=\"https://$secure_site/images/downloadmini.gif\" border=0 alt=Bajar width=16 height=16 />";
									$transaccion_buffer .= "</a>";
									$transaccion_buffer .= "</td>";
									// IMAGE FIN

									// ORIGINAL FILE NAME INI
									$transaccion_buffer .= "<td class='form_grid' BGCOLOR=$color width=40%>";
									// $transaccion_buffer .= "<a href=\"/access/getEdoCta/edocta/paso/$Year/$Month/". $p . $file[ "name" ];
									$transaccion_buffer .= "<a href=\"/access/getEdoCta/edocta/paso/$Year/$Month/". $p . $file[ "name" ] . "\" ";
									$transaccion_buffer .= "onMouseOut=\"MM_swapImgRestore()\" ";
									$transaccion_buffer .= "onMouseOver=\"MM_swapImage('ezf" . $file [ "name" ] ."-dl','','https://$secure_site/images/downloadminimrk.gif',1)\" >";
									// $transaccion_buffer .= "\">";
									$transaccion_buffer .= $file[ "name" ] . "</a>";
									$transaccion_buffer .= "</td>";
									// ORIGINAL FILE NAME FIN

									// DESCRIPTION INI
									$transaccion_buffer .= "<td class='form_grid' BGCOLOR=$color width=32%>";
									$transaccion_buffer .= "<span class='small'>[" . $file[ "description" ] ."]</span>";
									$transaccion_buffer .= "</td>";
									// DESCRIPTION FIN

									// FILE SIZE INI
									$transaccion_buffer .= "<td class='form_grid' BGCOLOR=$color width=10%>";
									$transaccion_buffer .= sprintf( "%01.2f", $file[ "size" ] ) . " KBytes";
									$transaccion_buffer .= "</td>";
									// FILE SIZE FIN

									// FILE DATE INI
									$transaccion_buffer .= "<td class='form_grid' BGCOLOR=$color width=15%>";
									$transaccion_buffer .= $file [ "date" ];
									$transaccion_buffer .= "</td>";
									// FILE DATE FIN
									$transaccion_buffer .= "</tr>";
									$i++;
								}
								$transaccion_buffer .=  "</table>";
							}
							else
							{
								$error_desc = 'empty';
								$transaccion_buffer = "";
							}
						}
						elseif ( $path == ".")
						{
							while ( $file = readdir ( $dir_handle))
							{
									// Protect hidden files from being listed
									if ( $file [ 0 ] == '.' )
									{
										continue;
									}

									// filename - output left-justified
									$t = "<a href=\"/access/get/$file\">$file</a>";
									echo $t.substr ( $space, 0, 40 - strlen ( $file ) );

									// filesize - output right-justified
									$t = ( filesize ( $file ) / 1024 );
									$t = sprintf ( "%01.2f", $t ) . "kb ";
									echo substr ( $space, 0, 10 - strlen ( $t ) ) . $t;

									// filedate - output left-justified
									$t = date ( "d.M Y H:i:s", filemtime ( $file ) );
									echo $t.substr ( $space, 0, 20 - strlen ( $file ) );
									echo "\n";
							}
						}
						//closing the directory
						closedir ( $dir_handle );
						$transaccion_buffer .=  "</PRE>";
					//}

					$t->set_var ( "transaccion_buffer", $transaccion_buffer );	//IRG - 210812
					switch ( $error_desc )
					{
						case 'access_deny':
							$transaccion_buffer  = "<PRE><STRONG><FONT color=red>No fue posible obtener la lista de archivos disponibles. ";
							$transaccion_buffer .= "Por favor, intente nuevamente m&aacute;s tarde.</FONT></STRONG></PRE>";
							break;
						case 'empty':
							$transaccion_buffer = "<PRE><STRONG><FONT color=blue>No existen archivos disponibles. ";
							$transaccion_buffer .= "Por favor, intente nuevamente m&aacute;s tarde.</FONT></STRONG></PRE>"; // JAC 13ARP2011
							break;
						default:
							$transaccion_buffer = "";
							break;
					}
					$t->pparse( "output", "edoctacred_tpl" );	//IRG - 210812
	//DMOS				
					//IRG - Fin - 210812
					//IRG - Fin - Paperless LEM
					break;
				}
			}
			break;
		}
	}
	//IRG - Inicio - Paperless LEM
	//IRG - Inicio - 210812
	if($_POST['Otra'] == 'Download'){
		if(($_POST['Origen'] == "CREDITOCONS") || ($_POST['Origen'] == "CREDITOCONSXML"))
		{
			$nombreOrigen = "CONS";
			$nombreArchivo = $_POST['FrAccount'].'-'.$nombreOrigen.'-'.$_POST['Year'].$_POST['Month'].'.'.$_POST['Formato'];
		}
		else if(($_POST['Origen'] == "CREDITOHIPOT") || ($_POST['Origen'] == "CREDITOHIPOTXML"))
		{
			$nombreOrigen = "HIPO";
			$nombreArchivo = $_POST['FrAccount'].'-'.$nombreOrigen.'-'.$_POST['Year'].$_POST['Month'].'.'.$_POST['Formato'];
		}
		else if(($_POST['Origen'] == "CREDITOLEM") || ($_POST['Origen'] == "CREDITOLEMXML"))
		{
			$nombreOrigen = "LINEA";
			$nombreArchivo = $_POST['File'];
		}
		getDocumento($_POST['Origen'], $_POST['FrAccount'], $_POST['Year'], $_POST['Month'], $_POST['Formato'], $nombreArchivo);
	}
	//IRG - Fin - 210812
	if($_POST['Otra'] == 'DownloadEdoCta'){
		$origen = $_POST['Origen']."&periodo=".$_POST['periodo']."&repositorio=".$_POST['repositorio']."&indice=".$_POST['indice'];
		$nombreArchivo = $_POST['File'];
		eZLog::writeNotice("URL:: ".$origen);
		getDocumentoPaperless($origen, $nombreArchivo);
	}
	$transaccion_buffer = str_replace ( "href=\"/transaccion/edoctacre/\"", "HREF=\"#\" onclick=\"javascript:iniProcesarMenu('edoctacre','')\" ", $transaccion_buffer );	//IRG - 210812
	//$transaccion_buffer = str_replace ( "href=\"/transaccion/estadosdecuenta/\"", "HREF=\"#\" onclick=\"javascript:iniProcesarMenu('estadosdecuenta','')\" ", $transaccion_buffer );	//IRG - 210812
	//IRG090512 - Fin - Paperless
	$transaccion_buffer = "<script>
							function getArchivoEstadoCuenta(origen, filename)
							{
								$.ajax({
									type: 'POST',
									url: '/procesarAjaxMenu.php',
									data: 'nomFunc=edoctacre&Access=Download&Otra=Download&Origen=$servicioOrigen&FrAccount=$FrAccount&Year=$Year&Month=$Month&Formato=$Formato&File='+filename,
									dataTypedataType: 'html',
									success: function(datos)
											{
											window.location.href = '/filedownload/'+ filename;
											}
									});
							}

							function getEstadoCuentaPaper(origen, filename, month, year)
							{
								$.ajax({
									type: 'POST',
									url: '/procesarAjaxMenu.php',
									data: 'nomFunc=edoctacre&Access=Otro&Origen=$servicioOrigen&FrAccount=$FrAccount&Year=$Year&Month=$Month&Formato=$Formato&File='+filename,
									dataType: 'html',
									success: function(datos)
											{
											window.location.href = '/filedownload/'+ filename+ '?trxn=pplss&month=$Month&year=$Year&file='+filename;
											}
									});
							}

							function procesarAjax()
							{
								var parametros=	'CustID:'+document.getElementsByName('CustID')[0].value+
												',Cadpriv:'+document.getElementsByName('Cadpriv')[0].value+
												',Trxn:'+document.getElementsByName('Trxn')[0].value+
												',Access:'+document.getElementsByName('Access')[0].value;

								if (document.getElementsByName('Accion')[0]!=null)
								{
									if (document.getElementsByName('Accion')[0].type!='HIDDEN' && document.getElementsByName('Accion')[0].type!='hidden')
									{
										parametros+=',Accion:'+$('input[@name=Accion]:checked').val();
									}
									else
									{
										parametros+=',Accion:'+document.getElementsByName('Accion')[0].value;
									}
								}
								if (document.getElementsByName('FrAccount')[0]!=null)
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

								/*if (document.getElementsByName('Day')[0]!=null)
								{
									parametros+=',Day:'+document.getElementsByName('Day')[0].options[document.getElementsByName('Day')[0].selectedIndex].value;
								}

								if (document.getElementsByName('Month')[0]!=null)
								{
									parametros+=',Month:'+document.getElementsByName('Month')[0].options[document.getElementsByName('Month')[0].selectedIndex].value;
								}

								if (document.getElementsByName('Year')[0]!=null)
								{
									parametros+=',Year:'+document.getElementsByName('Year')[0].options[document.getElementsByName('Year')[0].selectedIndex].value;
								}

								if (document.getElementsByName('DayF')[0]!=null)
								{
									parametros+=',DayF:'+document.getElementsByName('DayF')[0].options[document.getElementsByName('DayF')[0].selectedIndex].value;
								}

								if (document.getElementsByName('MonthF')[0]!=null)
								{
									parametros+=',MonthF:'+document.getElementsByName('MonthF')[0].options[document.getElementsByName('MonthF')[0].selectedIndex].value;
								}

								if (document.getElementsByName('YearF')[0]!=null)
								{
									parametros+=',YearF:'+document.getElementsByName('YearF')[0].options[document.getElementsByName('YearF')[0].selectedIndex].value;
								} */
								
								if (document.getElementById('Day')!=null)
								{
									parametros+=',Day:'+document.getElementById('Day').value;
								}

								if (document.getElementById('Month')!=null)
								{
									parametros+=',Month:'+document.getElementById('Month').value;
								}

								if (document.getElementById('Year')!=null)
								{
									parametros+=',Year:'+document.getElementById('Year').value;
								}

								if (document.getElementById('DayF')!=null)
								{
									parametros+=',DayF:'+document.getElementById('DayF').value;
								}

								if (document.getElementById('MonthF')!=null)
								{
									parametros+=',MonthF:'+document.getElementById('MonthF').value;
								}

								if (document.getElementById('YearF')!=null)
								{
									parametros+=',YearF:'+document.getElementById('YearF').value;
								}

								if (document.getElementsByName('File')[0]!=null)
								{
									parametros+=',File:'+document.getElementsByName('File')[0].options[document.getElementsByName('File')[0].selectedIndex].value;
								}

								if (document.getElementsByName('Accion2')[0]!=null)
								{
									if (document.getElementsByName('Accion2')[0].type!='HIDDEN' && document.getElementsByName('Accion2')[0].type!='hidden')
									{
										parametros+=',Accion2:'+$('input:radio[name=Accion2]:checked').val();
									}
									else
									{
										parametros+=',Accion:'+document.getElementsByName('Accion2')[0].value;
									}

								}
								if (document.getElementsByName('Reporte')[0]!=null)
								{
									if (document.getElementsByName('Reporte')[0].type!='HIDDEN' && document.getElementsByName('Reporte')[0].type!='hidden')
									{
										parametros+=',Reporte:'+$('input:radio[name=Reporte]:checked').val();
									}
									else
									{
										parametros+=',Reporte:'+document.getElementsByName('Reporte')[0].value;
									}
								}
								//IRG - Inicio - Paperless LEM
								//IRG - Inicio - 210812
								if (document.getElementsByName('Formato')[0]!=null)
								{
									if (document.getElementsByName('Formato')[0].type!='HIDDEN' && document.getElementsByName('Formato')[0].type!='hidden')
									{
										parametros+=',Formato:'+$('input:radio[name=Formato]:checked').val();
									}
									else
									{
										parametros+=',Formato:'+document.getElementsByName('Formato')[0].value;
									}
								}
								//IRG - Fin - 210812
								//IRG - Fin - Paperless LEM
								iniProcesarMenu('edoctacre', parametros);
							}
//06May2010   ACS  Llave ASB para Circular X I



									$(document).ready(function (){
											$('#token_value').focus()
											$('form').bind('submit', function() {
												return false;
											}
										)
									});




//06May2010   ACS  Llave ASB para Circular X F
								</script>". $transaccion_buffer;
	?>