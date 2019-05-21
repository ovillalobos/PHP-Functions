<script language="javascript" type="text/javascript" src="/eztransaccion/user/menu/jquery.simplemodal.js"></script>
<style>
	.button{
		background-color: #AEAACB; border: 1px solid #000000; padding: 2px; color: #ffffff;
		font-weight: bold; font-size: 13px; text-shadow: 1px 1px 1px rgba(0,0,0,0.3); margin-right: 5px; cursor: pointer;		
	}
	.button:hover{
		background-color: #7C6CC5; border: 1px solid #000000;
	}
	.button:active{
		background-color: #615DA3; border: 1px solid #000000;
	}
	.titleAyuda{
		text-align: center;
		color: #ffffff;
		text-shadow: 1px 1px 1px rgba(0,0,0,0.5);
		font-weight: bold;
		height: 18px;
		background: #665497;
		background: -moz-linear-gradient(top, #665497 0%, #452e81 100%); /* FF3.6+ */
		background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#665497), color-stop(100%,#452e81)); /* Chrome,Safari4+ */
		background: -webkit-linear-gradient(top, #665497 0%,#452e81 100%); /* Chrome10+,Safari5.1+ */
		background: -o-linear-gradient(top, #665497 0%,#452e81 100%); /* Opera 11.10+ */
		background: -ms-linear-gradient(top, #665497 0%,#452e81 100%); /* IE10+ */
		background: linear-gradient(to bottom, #665497 0%,#452e81 100%); /* W3C */
		filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#665497', endColorstr='#452e81',GradientType=0 ); /* IE6-9 */
	}
	.infoDAO{
		width: 80%;
		margin: 0px auto;
	}
	.infoDAO B{
		color: red;
	}
	.infoDAO SPAN{
		color: #000000;
	}
	.thBB{
		background: #665497;
		background: -moz-linear-gradient(top, #665497 0%, #452e81 100%); /* FF3.6+ */
		background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#665497), color-stop(100%,#452e81)); /* Chrome,Safari4+ */
		background: -webkit-linear-gradient(top, #665497 0%,#452e81 100%); /* Chrome10+,Safari5.1+ */
		background: -o-linear-gradient(top, #665497 0%,#452e81 100%); /* Opera 11.10+ */
		background: -ms-linear-gradient(top, #665497 0%,#452e81 100%); /* IE10+ */
		background: linear-gradient(to bottom, #665497 0%,#452e81 100%); /* W3C */
		filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#665497', endColorstr='#452e81',GradientType=0 ); /* IE6-9 */		
	}
	.thBB TH{
		border-left: 1px solid #B498CB;
		font-size: 11px;
		text-align: center;
		border-bottom: 2px solid #979798; 
	}
	.newTable{
		background: #f2f2f2; /* Old browsers */
		background: -moz-linear-gradient(top, #f2f2f2 0%, #dbdbdb 100%); /* FF3.6+ */
		background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#f2f2f2), color-stop(100%,#dbdbdb)); /* Chrome,Safari4+ */
		background: -webkit-linear-gradient(top, #f2f2f2 0%,#dbdbdb 100%); /* Chrome10+,Safari5.1+ */
		background: -o-linear-gradient(top, #f2f2f2 0%,#dbdbdb 100%); /* Opera 11.10+ */
		background: -ms-linear-gradient(top, #f2f2f2 0%,#dbdbdb 100%); /* IE10+ */
		background: linear-gradient(to bottom, #f2f2f2 0%,#dbdbdb 100%); /* W3C */
		filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f2f2f2', endColorstr='#dbdbdb',GradientType=0 ); /* IE6-9 */
		border-left: 1px solid #fff;
		border-bottom: 1px solid #fff;
	}
	#msgDao{
		border:1px solid #979798;
	}
	#msgDao #title{
		background: #665497;
		background: -moz-linear-gradient(top, #665497 0%, #452e81 100%); /* FF3.6+ */
		background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#665497), color-stop(100%,#452e81)); /* Chrome,Safari4+ */
		background: -webkit-linear-gradient(top, #665497 0%,#452e81 100%); /* Chrome10+,Safari5.1+ */
		background: -o-linear-gradient(top, #665497 0%,#452e81 100%); /* Opera 11.10+ */
		background: -ms-linear-gradient(top, #665497 0%,#452e81 100%); /* IE10+ */
		background: linear-gradient(to bottom, #665497 0%,#452e81 100%); /* W3C */
		filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#665497', endColorstr='#452e81',GradientType=0 ); /* IE6-9 */
		text-align: center;

		padding-top: 3px;
		padding-bottom: 3px;
		color: #ffffff;
		font-weight: bold;
		text-shadow: 1px 1px 2px rgba(0,0,0,0.8);
	}
	#msgDao #text{
		padding-top: 5px;
		padding-bottom: 5px;
		padding-left: 5px;
		padding-right: 5px;	
		color: red;
	}
	.red{
		color: red;
	}
	.boCerrar{
		margin-top: -3px;
		float: right;
		cursor: pointer;
	}
</style>
<script type="text/javascript">
	$(document).ready(function(){
		$("#ImpEnvio, #ocultarAyudaDAO, .ocultarDAO, .pagoPeriodo").hide();
		
		$("#boHelpDao").click( function () {
			try{
				$('#modalAyudaAPP').modal({
					  close: false,
					  persist: true, //Dont clone data
					  containerCss: {
							height: 190,
							width: 650
							},
						onShow: gridDestinoTEF(),
				     			onClose: closeConfirm
							});
			}
			catch(e){
				$('#modalAyudaAPP').modal({close:false,
					  containerCss: {
							height: 190,
							width: 650
							},
						onClose: closeConfirm
							});
			}
		});
		
		$("#btnCierraAyudaDao").click( function () {
			cerrarModal();
		});
			
		$("input[name='Editar']").click( function(){		
			if ($("input[value='Unico']").is(':checked'))
			{
				$(".pagoUnico").show();
				$(".pagoPeriodo").hide();
			}
			if ($("input[value='Periodico']").is(':checked'))
			{
				$(".pagoPeriodo").show();
				$(".pagoUnico").hide();
			}
		});

		/*
		if ($("input[value='Unico']").is(':checked'))
		{
			$("#pagoPeriodo").hide();						  
			$("#pagoUnico").show();
		}
		else if ($("input[value='Periodico']").is(':checked'))
		{
			$("#pagoPeriodo").show();						  
			$("#pagoUnico").hide();
		}
		*/
		/****** FUNCIONES DOMINGO A LA ORDEN ******/
		function cerrarModal(){
			$.modal.close();
		}
		
		$(".boCerrar, .boTxtCerrar").click( function () {
			$("#msgDao").slideUp();
		});
		
		function closeConfirm (dialog) {
			dialog.data.fadeOut('slow', function () {
				dialog.container.hide('slow', function () {
					dialog.overlay.slideUp('slow', function () {
						$.modal.close();
					});
				});
			});
		}	
	});
</script>
<?php
//
// 
// Created on: <1-Nov-2001 17:37:53 smb>
//
// This source file is part of IVA.
//
// Copyright (C) 1997-2004 Internet de Alta Calidad, S.A. de C.V.  Todos los derechos reservados. All rights reserved.
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, US
//
include_once( "classes/INIFile.php" );
include_once( "classes/eztemplate.php" );
include_once( "classes/ezhttptool.php" );

include_once( "eztransaccion/classes/eztransaccion.php" );
include_once( "ezsession/classes/ezsession.php" );


//ATAR Agregar Calendario en Agenda de Pagos 24-Nov-2010
include("eztransaccion/user/include/jscalendar/calendar-blue.css");
// JAC MAR2012 - CORRECCION MODULO DE CREDITOS INI
include("eztransaccion/user/include/jscalendar/calendar.js"); // MAOS 05dic2012 track 241546  Para que muestre el calendario
/*
if($session->variable("calendario")==0)
{
	include("eztransaccion/user/include/jscalendar/calendar.js");
}
*/
$session->setVariable("calendario",$session->variable("calendario")+1);
// JAC MAR2012 - CORRECCION MODULO DE CREDITOS FIN
include("eztransaccion/user/include/js/FuncionesReimpresion.js");
//ATAR Agregar Calendario en Agenda de Pagos 24-Nov-2010

$session =& eZSession::globalSession();

if( !$session->fetch() )
    $session->store();

$ini =& $GLOBALS["GlobalSiteIni"];

$Language = $ini->read_var( "eZTransaccionMain", "Language" );
// $backend = $ini->read_var( "eZTransaccionMain", "Backend" );
$ServerNumber = $ini->read_var( "site", "ServerNumber" );
$DomainPostfix = $ini->read_var( "site", "DomainPostfix" );

$backend = "gw" . $ServerNumber. $DomainPostfix;

include_once( "ezuserbb/classes/ezuser.php" );

$user =& eZUserBB::currentUser();

// DebugBreak();

if ( $user )
{
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "PagosProgramadosDAO.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "pagosprogramados_tpl" => "PagosProgramados.tpl"
        ) );


    $session =& eZSession::globalSession();

    if ( !$session->fetch() )
    {
        $session->store();
    }

    $tr = new eZTransaccion( );
    $usr = $session->variable( "r_usr" );
    $qki = $session->variable( "r_qki" );
    $priv = $session->variable( "r_priv" );
    $transaccion_buffer = "";
//HB
	if(!empty($_POST['Cust']))
		$Cust = $_POST['Cust'];
		
	if(!empty($_POST['Cuenta']))
		$Cuenta = $_POST['Cuenta'];
		
	if(!empty($_POST['Sub']))
		$Sub = $_POST['Sub'];
//HB
    if(empty($_POST['Access'])) {
        $_POST['Access'] = "FrAc";
    }
    if(empty($_POST['Accion'])) {
        $_POST['Accion'] = "Listadao";
    }
	if($_POST['Listado']=="Cancelar")
	{
		$_POST['Accion'] = "Listadao";
	}

    switch($_POST['Access']) {
    case "FrAc":
        $particularFields = "&Accion=".urlencode($_POST['Accion'])."&Pos=".urlencode($Pos);
        $tr->blog($qki,"Pagos Programados", $_POST['FrAccount'], $Cust, $Cuenta, $Sub, $_POST['Accion']);
        break;
    case "Confirm":
                if($_POST['Newdao']=="Altas")
                {
                        $_POST['Accion'] = "Newdao";
                        $_POST['Access'] = "FrAc";
                }
                if($_POST['Canccta']=="Bajas")
                {
                        $_POST['Accion'] = "Cancapp";
                }
        switch($_POST['Access'])
        {
                case "FrAc":
                                $particularFields = "&Accion=".urlencode($_POST['Accion']).
                                "&Pos=".urlencode($Pos).
                                "&Rownos=".$_POST['Borrar0'].$_POST['Borrar1'].$_POST['Borrar2'].$_POST['Borrar3'];
                                $tr->blog($qki,"Pagos Programados", $_POST['FrAccount'], $Cust, $Cuenta, $Sub, $_POST['Accion']);
                                break;
                        case "Confirm":
                                if ($_POST['Accion'] == "Newdao")
                                {
									// RAI 07Junio2011 - Inicio - Pagos periódicos
									if ($_POST['Day'] == "" or $_POST['Day'] == " ")
										$_POST['Day'] = "01";
									
									if ($_POST['Month'] == "" or $_POST['Month'] == " ")
										$_POST['Month'] = "01";
										
									if ($_POST['Year'] == "" or $_POST['Year'] == " ")
										$_POST['Year'] = "2011";
										
									/*$particularFields = "&Accion=".urlencode($_POST['Accion']).
									"&Button=".urlencode($_POST['Button']).
									"&FrAccount=".urlencode($_POST['FrAccount']).
									"&ToAccount=".urlencode($_POST['ToAccount']).
									"&ImpEnvio=".urlencode($_POST['ImpEnvio']).
									"&Amount=".urlencode($_POST['Amount']).
									"&RenInt=".urlencode($RenInt).
									"&Desc=".urlencode($_POST['Desc']).
									"&Editar=".urlencode($_POST['Editar']).
									"&Day=".urlencode($_POST['Day']).
									"&Month=".urlencode($_POST['Month']).
									"&Year=".urlencode($_POST['Year']).
									"&Cq=".urlencode($_POST['Cq']).
									"&Porcen1=".urlencode($_POST['Porcen1']).
									"&DiasPzo=".urlencode($_POST['DiasPzo']).
									"&DayF=".urlencode($_POST['DayF']).
									"&MonthF=".urlencode($_POST['MonthF']); */
									
									$particularFields = "&Accion=".$_POST['Accion'].
									"&Button=".$_POST['Button'].
									"&FrAccount=".$_POST['FrAccount'].
									"&ToAccount=".$_POST['ToAccount'].
									"&ImpEnvio=".$_POST['ImpEnvio'].
									"&Amount=".$_POST['Amount'].
									"&RenInt=".$RenInt.
									"&Desc=".$_POST['Desc'].
									"&Editar=".$_POST['Editar'].
									"&Day=".$_POST['Day'].
									"&Month=".$_POST['Month'].
									"&Year=".$_POST['Year'].
									"&Cq=".$_POST['Cq'].
									"&Porcen1=".$_POST['Porcen1'].
									"&DiasPzo=".$_POST['DiasPzo'].
									"&DayF=".$_POST['DayF'].
									"&MonthF=".$_POST['MonthF'];
									
									// RAI 07Junio2011 - Fin - Pagos periódicos
                                }
                                else
                                {
                                        $Rownos = "";
                                        if ($_POST['Borrar0'] <> "") { $Rownos = $Rownos.$_POST['Borrar0']."-"; }
                                        if ($_POST['Borrar1'] <> "") { $Rownos = $Rownos.$_POST['Borrar1']."-"; }
                                        if ($_POST['Borrar2'] <> "") { $Rownos = $Rownos.$_POST['Borrar2']."-"; }
                                        if ($_POST['Borrar3'] <> "") { $Rownos = $Rownos.$_POST['Borrar3']."-"; }
                                        if ($_POST['Borrar4'] <> "") { $Rownos = $Rownos.$_POST['Borrar4']."-"; }
                                        if ($_POST['Borrar5'] <> "") { $Rownos = $Rownos.$_POST['Borrar5']."-"; }
                                        if ($_POST['Borrar6'] <> "") { $Rownos = $Rownos.$_POST['Borrar6']."-"; }
                                        if ($_POST['Borrar7'] <> "") { $Rownos = $Rownos.$_POST['Borrar7']."-"; }
                                        if ($_POST['Borrar8'] <> "") { $Rownos = $Rownos.$_POST['Borrar8']."-"; }
                                        if ($_POST['Borrar9'] <> "") { $Rownos = $Rownos.$_POST['Borrar9']."-"; }
                                        $particularFields = "&Accion=".urlencode($_POST['Accion']).
                                        "&Button=".urlencode($_POST['Button'])."&Pos=".urlencode($Pos).
                                        "&Desc=".urlencode($Rownos);
                                }
                        $tr->blog($qki,"Pagos Programados", $_POST['FrAccount'], $_POST['ToAccount'], $_POST['Amount'], $_POST['ImpEnvio'], $_POST['Accion']);
                                break;
                }
                break;
    case "Process":
		//AGG 15JUL2006 SE CAMBIA EL NOMBRE DEL BOTON
		if($_POST['Listado']=="Cambiar") 
		{
			$_POST['Accion'] = "Listadao";
		}
		//AGG 15JUL2006 SE CAMBIA EL NOMBRE DEL BOTON
        if($_POST['Listado']=="Consultas")
        {
            $_POST['Accion'] = "Listadao";
        }
        switch($_POST['Accion'])
        {
                        case "Newdao":
							// RAI 07Junio2011 - Inicio - Pagos periódicos
							/* $particularFields = "&Passwd=".encrypt( $_POST['code'], strtolower( $usr ) ).	//ALUNA 30Jul2007 agenda de pagos tokens
							"&Accion=".urlencode($_POST['Accion']).
							"&Button=".urlencode($_POST['Button']).
							"&FrAccount=".urlencode($_POST['FrAccount']).
							"&ToAccount=".urlencode($_POST['ToAccount']).
							"&ImpEnvio=".urlencode($_POST['ImpEnvio']).
							"&Amount=".urlencode($_POST['Amount']).
							"&RenInt=".urlencode($RenInt).
							"&Desc=".urlencode($_POST['Desc']).
							"&Editar=".urlencode($_POST['Editar']).
							"&Day=".urlencode($_POST['Day']).
							"&Month=".urlencode($_POST['Month']).
							"&Year=".urlencode($_POST['Year']).
							"&Cq=".urlencode($_POST['Cq']).
							"&Porcen1=".urlencode($_POST['Porcen1']).
							"&DiasPzo=".urlencode($_POST['DiasPzo']).
							"&DayF=".urlencode($_POST['DayF']).
							"&MonthF=".urlencode($_POST['MonthF']); */
							
							$particularFields = "&Passwd=".encrypt( $_POST['code'], strtolower( $usr ) ).	//ALUNA 30Jul2007 agenda de pagos tokens
							"&Accion=".$_POST['Accion'].
							"&Button=".$_POST['Button'].
							"&FrAccount=".$_POST['FrAccount'].
							"&ToAccount=".$_POST['ToAccount'].
							"&ImpEnvio=".$_POST['ImpEnvio'].
							"&Amount=".$_POST['Amount'].
							"&RenInt=".$RenInt.
							"&Desc=".$_POST['Desc'].
							"&Editar=".$_POST['Editar'].
							"&Day=".$_POST['Day'].
							"&Month=".$_POST['Month'].
							"&Year=".$_POST['Year'].
							"&Cq=".$_POST['Cq'].
							"&Porcen1=".$_POST['Porcen1'].
							"&DiasPzo=".$_POST['DiasPzo'].
							"&DayF=".$_POST['DayF'].
							"&MonthF=".$_POST['MonthF'];
							
							// RAI 07Junio2011 - Fin - Pagos periódicos
							
							$tr->blog($qki,"Pagos Programados", $_POST['FrAccount'], $_POST['ToAccount'], $_POST['Amount'], $_POST['ImpEnvio'], $_POST['Accion']);
							break;
                        case "Cancapp":
                                $particularFields = "&Cust=".urlencode($Cust)."&Accion=".urlencode($_POST['Accion']).
                                "&Button=".urlencode($_POST['Button'])."&Desc=".urlencode($_POST['Desc']);
                                $tr->blog($qki,"Catalogo de Cuentas", $_POST['FrAccount'], $Cust, $Cuenta, $Sub, $_POST['Accion']);

                                break;
                        default:
                                $particularFields = "&Cust=".urlencode($Cust)."&Accion=".urlencode($_POST['Accion']).
                                "&Button=".urlencode($_POST['Button']);
                                $tr->blog($qki,"Catalogo de Cuentas", $_POST['FrAccount'], $Cust, $Cuenta, $Sub, $_POST['Accion']);
                }


        break;
    }
    $transaccion_buffer = "";
    // DebugBreak();
	/****** MODULO FIRST ACCESS ******/		
    $ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=app&Access=".urlencode($_POST['Access'])."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer);
	$transaccion_buffer = str_replace("SIZE=\"20\" MAXVALUE=\"60\" onFocus=\"this.value=WithOutFormatAmount(this.value);focus();select();\"", "SIZE=\"17\" MAXLENGTH=\"17\" id=\"Amount\" onKeyUp=\"currencya( this,event );\"", $transaccion_buffer); //MAOS OCT2013 Formato de moneda en linea
	$transaccion_buffer = str_replace("SIZE=\"10\" MAXVALUE=\"10\" onFocus=\"this.value=WithOutFormatAmount(this.value);focus();select();\"", "SIZE=\"17\" MAXLENGTH=\"17\" id=\"RenInt\" onKeyUp=\"currencyi( this,event );\"", $transaccion_buffer); //MAOS OCT2013 Formato de moneda en linea
					
	switch($_POST['Accion'])
	{
		case "Newdao":
				$transaccion_buffer = str_replace("<INPUT TYPE=\"HIDDEN\" NAME=\"Accion\" VALUE=\"Newapp\">", "<INPUT TYPE=\"HIDDEN\" NAME=\"Accion\" VALUE=\"Newdao\">", $transaccion_buffer);
				/****** MODULO DE ALTA DE CUENTA ******/	
				$transaccion_buffer = str_replace("<INPUT TYPE=\"SUBMIT\" NAME=\"Button\" VALUE=\"Aceptar\" onClick = Amount.value=WithOutFormatAmount(Amount.value);onClick=RenInt.value=WithOutFormatAmount(RenInt.value)>", "<INPUT CLASS=\"button\"  TYPE=\"SUBMIT\" NAME=\"Button\" VALUE=\"Aceptar\" onClick = Amount.value=WithOutFormatAmount(Amount.value);onClick=RenInt.value=WithOutFormatAmount(RenInt.value)>", $transaccion_buffer);
				$transaccion_buffer = str_replace("<INPUT TYPE=\"SUBMIT\" NAME=\"Listado\" VALUE=\"Cancelar\">", "<INPUT CLASS=\"button\"  TYPE=\"SUBMIT\" NAME=\"Listado\" VALUE=\"Cancelar\">", $transaccion_buffer);

				/****** MODULO DE CONFIRMACION ASB ******/				
				$transaccion_buffer = str_replace("<P><B>Importante: Al programar un pago T.E.F., el cargo a su cuenta se realizar&aacute el d&iacute;a h&aacute;bil anterior a la fecha en que usted program&oacute el pago a su beneficiario.</B><P>", "", $transaccion_buffer);
				$transaccion_buffer = str_replace("<INPUT TYPE=SUBMIT NAME=\"Button\" VALUE=\"Aceptar\" ONCLICK=\"DisabledButton()\">", "<INPUT CLASS=\"button\" TYPE=SUBMIT NAME=\"Button\" VALUE=\"Aceptar\" ONCLICK=\"DisabledButton()\">", $transaccion_buffer);				
				$transaccion_buffer = str_replace("<INPUT TYPE=SUBMIT NAME=\"Listado\" VALUE=\"Cambiar\">", "<INPUT CLASS=\"button\" TYPE=SUBMIT NAME=\"Listado\" VALUE=\"Cambiar\">", $transaccion_buffer);	
				
				$transaccion_buffer = str_replace("<INPUT TYPE=\"TEXT\" NAME=\"Desc\" SIZE=\"30\" MAXVALUE=\"60\">", "<INPUT TYPE=\"HIDDEN\" NAME=\"Desc\" SIZE=\"30\" MAXVALUE=\"60\" VALUE=\"Domingo a la Orden\"  >", $transaccion_buffer);
				$transaccion_buffer = str_replace("<TH>Comentario</TH>", "<TH></TH>", $transaccion_buffer);					
				$transaccion_buffer = str_replace("<TH>Monto</TH>", "<TH><BR/>Monto</TH>", $transaccion_buffer);					
				
				/****** MODULO DE CONFIRMACION DE OPERACIÓN ******/
				if ( $_POST['Access'] == 'Process' )
				{
					$transaccion_buffer = str_replace("<TABLE BORDER=0 WIDTH=\"80%\" ALIGN=CENTER>", "<DIV id=\"msgDao\" ><DIV id=\"title\">Domingo a la Orden<a  HREF=\"/transaccion/pagosprogramadosDAO/\" ><img border=\"0\" src=\"/sitedesign/bajionet/images/btn_close.gif\" class=\"boCerrar\" alt=\"Cerrar\"></a></DIV><TABLE BORDER=0 WIDTH=\"100%\" ALIGN=CENTER>", $transaccion_buffer);
					$transaccion_buffer = str_replace("</TABLE>", "</TABLE></DIV>", $transaccion_buffer);					
					$transaccion_buffer = str_replace("<P>Para regresar a la Agenda de Pagos <A HREF=\"/transaccion/pagosprogramados/\">haga clic aqu&iacute</A>.</P>", "<P>Para regresar a la Domingo a la Orden <A HREF=\"/transaccion/pagosprogramadosDAO/\">haga clic aqu&iacute</A>.</P>", $transaccion_buffer);				
					
					$transaccion_buffer = str_replace("<TABLE BORDER=0 WIDTH=\"85%\">", "<DIV id=\"msgDao\" ><DIV id=\"title\">Domingo a la Orden<a  HREF=\"/transaccion/pagosprogramadosDAO/\" ><img border=\"0\" src=\"/sitedesign/bajionet/images/btn_close.gif\" class=\"boCerrar\" alt=\"Cerrar\"></a></DIV><TABLE BORDER=0 WIDTH=\"85%\">", $transaccion_buffer);
					$transaccion_buffer = str_replace("Agenda de Pagos", "Domingo a la Orden", $transaccion_buffer);		
				}					
				if ( $_POST['Access'] == 'Confirm' )
				{
					/****** ERROR RC3203.HTM ******/
					$msgTexto = "Su transacci&oacute;n no pudo ser procesada, el monto especificado es inv&aacute;lido.  Código: (3203)";
					$transaccion_buffer = str_replace("<p>Su transacci&oacute;n no pudo ser procesada.</p>", "", $transaccion_buffer);
					$transaccion_buffer = str_replace("<p>Código: (3203)</p>", "", $transaccion_buffer);
					$transaccion_buffer = str_replace("<p>El monto especificado es inv&aacute;lido.</p>", "<DIV id=\"msgDao\" ><DIV id=\"title\">Domingo a la Orden<a  HREF=\"/transaccion/pagosprogramadosDAO/\" ><img border=\"0\" src=\"/sitedesign/bajionet/images/btn_close.gif\" class=\"boCerrar\" alt=\"Cerrar\"></a></DIV><P id=\"text\">".$msgTexto."</P></DIV>", $transaccion_buffer);
					
					/****** VERIFICA LA FALTA DE INFORMACIÓN ******/
					$transaccion_buffer = str_replace("<P>Su transacci&oacute;n no pudo ser procesada.</P>", "<DIV id=\"msgDao\" ><DIV id=\"title\">Domingo a la Orden<a  HREF=\"/transaccion/pagosprogramadosDAO/\" ><img border=\"0\" src=\"/sitedesign/bajionet/images/btn_close.gif\" class=\"boCerrar\" alt=\"Cerrar\"></a></DIV><DIV STYLE=\"padding-left: 10px;\"><P CLASS='red'>Su transacci&oacute;n no pudo ser procesada.</P>", $transaccion_buffer);
					$transaccion_buffer = str_replace("<LI>La programaci&oacute;n de Transferencias Electr&oacute;nicas de fondos deben realizarse con anticipación de <a onClick='window.open(\"http://ayuda.bb.com.mx/CCayuda1.html\", \"help\", \"toolbar=no,menubar=no,scrollbars=1,width=520,height=300\"); return false' href=\"http://ayuda.bb.com.mx/CatalogoCuentas\">dos d&iacute;as hábiles anteriores a su aplicación, por la operación de este tipo de transferencias interbancarias.</A>", "", $transaccion_buffer);
					$transaccion_buffer = str_replace("<P>Para regresar a la Agenda de Pagos <A HREF=\"/transaccion/pagosprogramados/\">haga clic aqu&iacute</A>.</P>", "<P>Para regresar a la Domingo a la Orden <A HREF=\"/transaccion/pagosprogramadosDAO/\">haga clic aqu&iacute.</A></P></DIV></DIV>", $transaccion_buffer);
					$transaccion_buffer = str_replace("<LI>Es necesario llenar el campo \"Comentario\".", "", $transaccion_buffer);
				}
				break;
		case "Cancapp":
				if ( $_POST['Access'] == 'Confirm' )
				{
					/****** MODULO DE BAJAS ******/
					$transaccion_buffer = str_replace("<INPUT TYPE=SUBMIT NAME=\"Button\" VALUE=\"Aceptar\">", "<INPUT CLASS=\"button\" TYPE=SUBMIT NAME=\"Button\" VALUE=\"Aceptar\">", $transaccion_buffer);
					$transaccion_buffer = str_replace("<INPUT TYPE=SUBMIT NAME=\"Listado\" VALUE=\"Cancelar\">", "<INPUT CLASS=\"button\" TYPE=SUBMIT NAME=\"Listado\" VALUE=\"Cancelar\">", $transaccion_buffer);					
					/****** OPERACIÓN ERROR ******/
					$transaccion_buffer = str_replace("<P>Su transacci&oacute;n no pudo ser procesada.</P>", "<DIV id=\"msgDao\" ><DIV id=\"title\">Domingo a la Orden<a  HREF=\"/transaccion/pagosprogramadosDAO/\" ><img border=\"0\" src=\"/sitedesign/bajionet/images/btn_close.gif\" class=\"boCerrar\" alt=\"Cerrar\"></a></DIV><DIV STYLE='padding-left: 10px;' ><P CLASS=\"red\">Su transacci&oacute;n no pudo ser procesada.</P>", $transaccion_buffer);
					$transaccion_buffer = str_replace("<P>Código: ( )</P>", "</DIV></DIV>", $transaccion_buffer);	
					
					$transaccion_buffer = str_replace("0000", "", $transaccion_buffer);
					$transaccion_buffer = str_replace("<P>Para regresar a la Agenda de Pagos <A HREF=\"/transaccion/pagosprogramados/\">haga clic aqu&iacute</A>.</P>", "<P>Para regresar a Domingo a la Orden <A HREF=\"/transaccion/pagosprogramadosDAO/\">haga clic aqu&iacute</A>.</P>", $transaccion_buffer);				
					$transaccion_buffer = str_replace("<UL>", "<DIV id=\"msgDao\" ><DIV id=\"title\">Domingo a la Orden<a  HREF=\"/transaccion/pagosprogramadosDAO/\" ><img border=\"0\" src=\"/sitedesign/bajionet/images/btn_close.gif\" class=\"boCerrar\" alt=\"Cerrar\"></a></DIV><UL>", $transaccion_buffer);	
					$transaccion_buffer = str_replace("</UL>", "</UL></DIV>", $transaccion_buffer);		
					
					$transaccion_buffer = str_replace("por", "por $ ", $transaccion_buffer);		
				}
				if ( $_POST['Access'] == 'Process' )
				{
					$transaccion_buffer = str_replace("<TABLE BORDER=0 WIDTH=\"80%\" ALIGN=CENTER>", "<DIV id=\"msgDao\" ><DIV id=\"title\">Domingo a la Orden<a  HREF=\"/transaccion/pagosprogramadosDAO/\" ><img border=\"0\" src=\"/sitedesign/bajionet/images/btn_close.gif\" class=\"boCerrar\" alt=\"Cerrar\"></a></DIV><TABLE BORDER=0 WIDTH=\"100%\" ALIGN=CENTER>", $transaccion_buffer);
					$transaccion_buffer = str_replace("</TABLE>", "</TABLE></DIV>", $transaccion_buffer);					
					$transaccion_buffer = str_replace("<P>Para regresar a la Agenda de Pagos <A HREF=\"/transaccion/pagosprogramados/\">haga clic aqu&iacute</A>.</P>", "<P>Para regresar a Domingo a la Orden <A HREF=\"/transaccion/pagosprogramadosDAO/\">haga clic aqu&iacute</A>.</P>", $transaccion_buffer);				
					$transaccion_buffer = str_replace("Agenda de Pagos", "Domingo a la Orden", $transaccion_buffer);		
				}				
				break;
		case "Listadao":
				//$transaccion_buffer = str_replace("<TD>Este es el cat&aacute;logo de las cuentas que se encuentran ligadas con sus cuentas de BanBaj&iacute;o, hacia las cu&aacute;les Usted podr&aacute hacer transferencias a trav&eacute;s de la Agenda de Pagos o de los m&eacute;todos usuales de transferencia de fondos que le ofrece Bajionet.</TD>", "<TD>Este es el cat&aacute;logo de las cuentas que se encuentran ligadas con sus cuentas de BanBaj&iacute;o, hacia las cu&aacute;les Usted podr&aacute hacer transferencias a trav&eacute;s de Domingo a la Orden o de los m&eacute;todos usuales de transferencia de fondos que le ofrece Bajionet.</TD>", $transaccion_buffer);	
				$transaccion_buffer = str_replace("<TD>Este es el cat&aacute;logo de las cuentas que se encuentran ligadas con sus cuentas de BanBaj&iacute;o, hacia las cu&aacute;les Usted podr&aacute hacer transferencias a trav&eacute;s de la Agenda de Pagos o de los m&eacute;todos usuales de transferencia de fondos que le ofrece Bajionet.</TD>", "", $transaccion_buffer);	
				$transaccion_buffer = str_replace("<TD><B>Importante:</B> Al programar un pago T.E.F., el cargo a su cuenta se realizar&aacute el d&iacute;a h&aacute;bil anterior a la fecha en que usted program&oacute el pago a su beneficiario.</TD>", "", $transaccion_buffer);
				
				$transaccion_buffer = str_replace("<TABLE WIDTH=100% CELLSPACING=2 BORDER=0>", "<TABLE WIDTH=100% CELLSPACING=0 BORDER=0>", $transaccion_buffer);				
				$transaccion_buffer = str_replace("<TR BGCOLOR=\"#5A419C\">", "<TR CLASS=\"thBB\">", $transaccion_buffer);
				$transaccion_buffer = str_replace("<TD align=\"right\"COLSPAN=4><B>Usted no tiene ning&uacute;n pago calendarizado en su Agenda de Pagos.</B></TD>", "<TD align=\"right\"COLSPAN=5><B>Usted no tiene ning&uacute;n pago calendarizado para Domingo a la Orden.</B></TD>", $transaccion_buffer);
				break;
	}
	$transaccion_buffer = str_replace("<P>Código: ()</P>","", $transaccion_buffer); // Temporal [BORRAR]
		
	//$transaccion_buffer = str_replace("</TABLE>","</TABLE>".$_POST['Access']."|".$_POST['Accion'], $transaccion_buffer); // Temporal [BORRAR]	
	$transaccion_buffer = str_replace("pekes", "Pekes", $transaccion_buffer);
	$transaccion_buffer = str_replace("chavos", "Chavos", $transaccion_buffer);
	$transaccion_buffer = str_replace("", "", $transaccion_buffer);

			
    $t->set_var( "transaccion_buffer", $transaccion_buffer );
    $t->pparse( "output", "pagosprogramados_tpl" );
	
	//echo $particularFields;
}
else
{
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "userlogin.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "user_login_tpl" => "userlogin.tpl"
        ) );

    $t->set_var( "redirect_url", "/transaccion/PagosProgramadosDAO/" );

    $t->pparse( "output", "user_login_tpl" );
}
?>
<div id="modalAyudaAPP" style='display:none'>
	<div class="titleAyuda">Domingo a la orden</div>
	<div id="textAyudaDAO"  align="left" >
		<OL>
			<LI>Seleccione la cuenta origen de la transferenciass.</LI>
			<LI>Seleccione la cuenta destino de la transferencia (<B>debe</B> ser distinta de la <B>anterior</B>).</LI>
			<LI>Escriba el monto a transferir (use el punto como separador decimal).</LI>
			<LI>Escriba un comentario o referencia asociado con la transferencia (opcional).</LI>
			<LI>Seleccione la <a onClick='window.open("http://ayuda.bb.com.mx/CCayuda5.html", "help", "toolbar=no,menubar=no,scrollbars=1,width=520,height=300"); return false' href="http://ayuda.bb.com.mx/CatalogoCuentas">fecha</a> en que ser&aacute aplicado el pago:</LI>
					<li>Transferencia: El cargo a su cuenta y el abono a la cuenta del Beneficiario se realiza el d&iacute;a que usted programa el pago.</LI>
				</UL>
			<LI>Presione el bot&oacute;n <EM>Aceptar</EM> para proceder.</LI>
		</OL>
	</div>
	<div id="bottonAyuda" align="center" >
		<input Class="button" type="button" style="width: 100px; text-align: center;"  id="btnCierraAyudaDao" value ="Cerrar" text="Cerrar" />
	</div>
</div>