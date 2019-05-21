<html>
<head>
	<script src="/middleware/transfer/js/valida.js"></script>
	<link rel='stylesheet' type='text/css' media='screen' href='/middleware/css/jquery-ui-1.7.3.custom.css' />
	<script language='javascript'>
		
		$("#msgresAltaLineabb_i").hide();
		$("#resAltaLineabb_i").hide();
		$("#msgModalbb_i").hide();
		$("#errorMsgbbInter").hide(300);

		function closeConfirm (dialog) {
			dialog.data.fadeOut('slow', function () {
				dialog.container.hide('slow', function () {
					dialog.overlay.slideUp('slow', function () {
						$.modal.close();
					});
				});
			});
		}

		function isNumberTkInter(code)
		{
			var valor = code;
			var patternx = "^\\d{10}$";
			
			if (valor.match(patternx) && valor != "" )
			{ 
			return true; }
			else
			{ 
			return false;  }
			return false;
		}

		function isEnterTkInter(e)
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

		function cancelaPagoInter()
		{
			$.modal.close();
		}

		function modificaOTR()
		{
			$("#alias_m").html($('#Nomben2').val());
			$("#rfc_m").html($('#RFC').val()); 
			$("#email_m").html($('#DireccB').val());
			$("#nombe_m").html($('#NombreB').val());

			$("#cuentasb_modf_i").html($('#Nomben2').val()+"|"+$('#RFC').val()+"|"+$('#DireccB').val()+"|"+$('#rcm').val()+"|"+$('#NombreB').val());
			$("#cuentasb_modf_i").val($('#Nomben2').val()+"|"+$('#RFC').val()+"|"+$('#DireccB').val()+"|"+$('#rcm').val()+"|"+$('#NombreB').val());
			try
				{ 		
					$('#modalModfLigaInter').modal({close:false,
						  containerCss: {
								height: 350,
								width: 760
							},
						onClose: closeConfirm
						}); 
				}
				catch(e)
				{
					$('#modalModfLigaInter').modal({close:false,
						  containerCss: {
								height: 350,
								width: 760
							},
							onClose: closeConfirm
						}); 
				}
		}

		function continuaModfInter(code,param)
		{	
			
			var user  = $("#cusOTR").val();
			var cte   = $("#Rowno").val();

			//alert("Por enviar para modificar...["+code+"]....["+param+"]....["+user+"].....["+cte+"]");
			
			$.ajax({
				 type: "POST",			
				url: "/procesarAjaxMiddleware.php",		
				data: {Access:"Process",modulo:"transfer",operacion:"modfligainter",param:param,user:user},
				dataTypedataType: "html",
					success: function(res){				
						
						cancelaPagoInter();					
						
						var res_split = res.split("-");

							if ( $.trim(res_split[1]) == "0000"){

								$("#msgresAltaLineabb_i").show();
								$("#resAltaLineabb_i").html("<p>Modificaci&oacute;n de informaci&oacute;n - "+$.trim(res_split[0])+"- Se proces&oacute; correctamente su cambio. N&uacute;mero de autorizaci&oacute;n:"+"<b>"+res_split[2]+"</b></p>");
								$("#resAltaLineabb_i").show();						
								
							}
							else
							{

								$("#errorMsgbbInter").empty();
								$("#errorMsgbbInter").append(res_split[1]+'<br>');
								$("#msgModalbb_i").show();
								$("#errorMsgbbInter").show(300);
								$("#errorMsgbbInter").delay(300).slideDown(300);
							}
					},
					error: function(resp){
						cancelaPagoInter();					
					}
				});
		}
		$("#closeModalBene").click( function () { $("#errorMsgbbInter").slideUp(300); $("#msgModalBene").delay(300).hide(300); });
	</script>
</head>
<body>
	<input type="HIDDEN" id="cusOTR" name="cusOTR" value="<?php echo  $session->variable( 'r_usr' );?>">
	<input type="HIDDEN" id="Rowno" name="Rowno" value="<?php echo  $session->variable( 'r_cno' );?>">
	<div id="modalModfLigaInter" style='display:none'>
	<div style="width:750; height:300px;">
		<div class='ui-jqgrid-titlebar ui-widget-header ui-corner-top ui-helper-clearfix' style='width:99.7%; height:20px;'><span class='ui-jqgrid-title' style='font-size:12px;'>&nbsp;Confirmaci&oacute;n</span></div>
		<table align="center" width="750px" height="280px" >
			<tr>
				<td height="33px" align="center">
					<span style="font-size:8px"><br/></span> 					 					
					Usted est&aacute; modificando la siguiente informaci&oacute;n:
					<br/><br/><br />
					<span id="cuentasb_modf_i" name="cuentasb_modf_i" value="" style="display:none;"></span>			
					<table align="center">
						<!--
						<tr >				
							<td><b>Alias:</b></td><td><span id='alias_m'></span></td>
						</tr>
						<tr >
							<td><b>Beneficiario:</b></td><td><span id='nombe_m'></span></td>
						</tr>
						<tr >
							<td><b>RFC:</b></td><td><span id='rfc_m'></span></td>
						</tr>-->
						<tr >
							<td><b>Correo Electr&oacute;nico:</b></td><td><span id='email_m'></span></td>									
						</tr>
					</table>	
				</td>				
			</tr>				
				
			<tr>
  				<td align="center">
  					<br/><b>&#191;Desea continuar?</b>
  				</td>
  			</tr>
  			<tr>
  				<td align="center">
  					
  					<b><table>
	  					<tr>
	  					<td  align='left' colspan=2>  					
	  					<hr noshade='' size='1'></td></tr>  					<tr>
	  					<td align='center' colspan=2>
	  					<div id='auxArcTkbb2_i' name='auxArcTkbb2_i' style='color:red;'></div></td></tr>
	  					<tr>
	  					<td align='center' colspan=2>Autorice la Transacci&oacute;n Capturando su Clave ASB ( Acceso Seguro Bajio )</td></tr>
	  					<tr>
	  					<td align='right'>Clave ASB (Acceso Seguro Bajio):</td>
	  					<td align='left'></b> 					
	  						<input onkeypress="if (isEnterTkInter(event) == true){if(isNumberTkInter($('#codeTkArchivoBBm_i').val())) {continuaModfInter($('#codeTkArchivoBBm_i').val(),$('#cuentasb_modf_i').html());} else {$('#codeTkArchivoBBm_i').val(''); $('#auxArcTkbb2_i').html('El n&uacute;mero de Clave ASB que ha ingresado es incorrecto, intentelo de nuevo.');} } else { return esInteger(event,this);}" id='codeTkArchivoBBm_i' size='12' maxlength='10' type='PASSWORD' align='LEFT'>
	  					</td>
	  					</tr>
  					</table> 		
  					
  					<input Class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" type="button"  id="btnModalContinuaModf2_i" value ="Aceptar" text="Aceptar" onclick="javascript: if(isNumberTkInter($('#codeTkArchivoBBm_i').val())) {continuaModfInter($('#codeTkArchivoBBm_i').val(),$('#cuentasb_modf_i').val());} else {$('#codeTkArchivoBBm_i').val(''); $('#auxArcTkbb2_i').html('El n&uacute;mero de Clave ASB que ha ingresado es incorrecto, intentelo de nuevo.');} " />  					
  					
  					<input Class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" type="button"  id="btnModalCierra_modf_i" value ="Cambiar" text="Cambiar" onclick="javascript:cancelaPagoInter();"/>
  				</td>
  			</tr>			
		</table>		
  	</div>
 </div>
 <input type='hidden' id='rcm' value='' />
 <br />
 <div id="msgModalbb_i" style="width: 100%;">
 	<table width="400" style="width: 100%; height:60px; border: 1px solid;" cellspacing="0" cellpadding="0" border="0">
 	<tbody>
 		<tr>
 			<td valign="top" >
 				<table style="width: 100%;" width="400" cellspacing="0" cellpadding="0"><tbody>
 					<tr>
 						<td valign="top" align="left" style="height:15px; font-size:10px; font-weight:bold; background-image:url('/sitedesign/bajionet/images/barra_mor.png'); color:#ffffff; padding-top:5px; font-family:Tahoma, Verdana, Arial, Helvetica, sans-serif;">
 							Mensaje Transferencias
 						</td>
 						<td valign="top" align="right" class="cabecera-tabla" style="height: 17px; background-image:url('/sitedesign/bajionet/images/barra_mor.png'); font-size:10px;font-weight: bold;color: white;font-family: Tahoma, Verdana, Arial, Helvetica, sans-serif;">
 							<img id="closeModalBene" border="0" src="/sitedesign/bajionet/images/btn_close.gif" style="hight: 10px; cursor: pointer" alt="Cerrar" onclick="javascript: $('#msgModalbb_i').hide('slow'); return false;">
 						</td>
 					</tr>
 				</tbody></table>
 			</td>
 		</tr>
 		<tr>
 			<td valign="top" >
 				<div id='errorMsgbbInter' tabindex='1' class="msgErrorT" style="color:red; font-size:11px;"></div>
 			</td>
 		</tr>
 	</tbody>
 	</table>
</div>
<!-- Exito -->
<div id="msgresAltaLineabb_i">
	<table width="860" style="width:860px; height:60px; border: 1px solid; align:center;" cellspacing="0" cellpadding="0" border="0">
		<tbody>
			<tr><td valign="top">
			<table width="860" style="width:100%;" cellspacing="0" cellpadding="0">
				<tbody><tr>
					<td valign="top" align="left" style="height:15px; font-size:10px; font-weight:bold; background-image:url('/sitedesign/bajionet/images/barra_mor.png'); color:#ffffff; padding-top:5px; font-family:Tahoma, Verdana, Arial, Helvetica, sans-serif;">
						Mensaje Transferencias
					</td>
					<td valign="top" align="right" class="cabecera-tabla" style="height: 17px; background-image:url('/sitedesign/bajionet/images/barra_mor.png'); font-size:10px;font-weight: bold;color: white;font-family: Tahoma, Verdana, Arial, Helvetica, sans-serif;">
						<img id="closeresAltaLinea2" border="0" src="/sitedesign/bajionet/images/btn_close.gif" style="hight: 10px; cursor: pointer" alt="Cerrar" onclick="javascript: $('#msgresAltaLineabb_i').hide('slow'); return false;">
					</td>
				</tr></tbody>
			</table>
			</td></tr>
			<tr><td valign="top">
				<div id='resAltaLineabb_i' name='resAltaLineabb_i' class="msgAvisoT" style="color:#000000; font-size:10px;" ></div>
			</td></tr>
		</tbody>
	</table>
</div>
<br />
<br />
</body>
</html>
<?php
include_once( "classes/ezlog.php" );

	//$email = $_POST['emailOTR']; 
	//$rfc   = $_POST['rfcOTR']; 

	ezlog::writeNotice("RECV---->[".$emailOTR."]----->[".$rfcOTR."]");
		$path = "statics";
		$file = $path ."/octa1.hdr";
		$f = @fopen($file, 'r');
		if($f)
		{
			$html .= fread($f, filesize($file));
			fclose($f);

			$html .= "<TD><B>RFC</B></TD></TR><TR><TD><INPUT  TYPE=\"TEXT\" NAME=\"RFC\" SIZE=\"13\" MAXLENGTH=\"13\"></TD></TR><TR>";

			$file_speilist  = $path ."/speilist.i";
			$f_speilist 	= @fopen($file_speilist,'r');

			if($f_speilist) // abrimos el listado de bancos
			{
				$html .= fread($f_speilist, filesize($file_speilist)); //cargado el speilist.i
				fclose($f_speilist);

					$file2  = $path ."/octa1.ftr";
					$f2 	= @fopen($file2,'r');

						if ($file2) // abrimos el footer de octa
						{
							$html .= fread($f2, filesize($file2));
							fclose($f2);
						}
			}
		}
		else
			$html .= "<strong>No se encontr&oacute; informaci&oacute;n referente a transferencias ($file) favor de volver a intentar.</strong>";

	$html = str_replace("\r\n", "", $html);
	$html = str_replace("\0", "", $html);
	//Reemplazamos la ayuda
	$html = str_replace("Para agregar una cuenta, por favor proporcione la siguiente informaci&oacute;n:","&Uacute;nicamente podr&aacute; modificar el correo electr&oacute;nico y no ser&aacute; necesario que espere para utilizar la cuenta destino.",$html);
	
	$posIni  = strpos($html, "<OL>");
	$posFin  = strpos($html, "<HR SIZE=1 NOSHADE>");
	$bufftemp= substr($html, $posIni, $posFin-$posIni);
	$html = str_replace($bufftemp, "", $html);

	//Reemplazamos la ayuda
	$html = str_replace("<TH>Beneficiario:</TH></TR>","<TH>Beneficiario:</TH><TH>RFC:</TH></TR>", $html);
	$html = str_replace("NAME=\"NombreB\" SIZE=\"30\" MAXLENGTH=\"30\" onKeypress=\"alfanumerico(this);\"></TD></TR>",
					"NAME=\"NombreB\" SIZE=\"30\" MAXLENGTH=\"30\" onKeypress=\"alfanumerico(this);\"></TD><TD><INPUT  TYPE=\"TEXT\" NAME=\"RFC\" SIZE=\"13\" MAXLENGTH=\"13\"></TD></TR>", $html);
	$html = str_replace("<TD><B>RFC</B></TD></TR><TR><TD><INPUT  TYPE=\"TEXT\" NAME=\"RFC\" SIZE=\"13\" MAXLENGTH=\"13\"></TD></TR><TR>"," ", $html);
	$html = str_replace("<TH ALIGN=LEFT>Banco receptor</TH></TR><TR>"," ", $html);
	$html = str_replace("</SELECT></TD></TR>","</SELECT></TD>", $html);
	$html = str_replace("NAME=\"Nomben2\"","NAME=\"Nomben2\" ID=\"Nomben2\" mensaje=\"Intruduzca  el dato con el que identificar a su beneficiario \" onMouseOver=\"mostrarAyuda('Nomben2');\" onmouseout=\"rmvAyuda();\"", $html);
	$html = str_replace("NAME=\"NombreB\"","NAME=\"NombreB\" ID=\"NombreB\" mensaje=\"Nombre de la persona o razón social que recibirá la transferencia \" onMouseOver=\"mostrarAyuda('NombreB');\" onmouseout=\"rmvAyuda();\"", $html);
	$html = str_replace("NAME=\"RFC\"","NAME=\"RFC\" ID=\"RFC\" mensaje=\"Dato Opcional \" onMouseOver=\"mostrarAyuda('RFC');\" onmouseout=\"rmvAyuda();\"", $html);
	$html = str_replace("<INPUT TYPE=\"HIDDEN\" NAME=\"CustID\" VALUE=\"~c\">","",$html);
	//Cambiamos el boton para modificar
	$html = str_replace("<INPUT TYPE=SUBMIT NAME=\"Button\" VALUE=\"Alta Beneficiario\" onclick = \"return Bancotefspei();\" />", "<input type='button' class='button_RIC' value='Modificar' id='btnModificaOTR' name='btnModificaOTR' onclick='modificaOTR();'>",$html);
	$html = str_replace("<INPUT TYPE=SUBMIT NAME=\"Listado\" VALUE=\"Cancelar\" >", "<INPUT TYPE=SUBMIT class='button_RIC' NAME=\"Listado\" VALUE=\"Regresar\" >", $html);
	
	$html = str_replace("ID= \"UbNo\"", "ID='UbNo' disabled", $html);
	
	$html = str_replace("NAME=\"Apocope\"", "NAME=\"Apocope\" id=\"Apocope\" disabled", $html);

	$html = str_replace("ID=\"ImpTipo\"", "ID=\"ImpTipo\" disabled", $html);

	$html = str_replace("NAME=\"NombreB\"", "NAME='NombreB' disabled", $html);

	$html = str_replace("NAME=\"Nomben2\"", "NAME='Nomben2' disabled", $html);

	$html = str_replace("NAME=\"RFC\"", "NAME='RFC' disabled", $html);

	
eZLog::writeNotice("--->>> El html--->[".$html."]");

	//Agregamos los valores de email, alias, nombre, etc que se van a editar
	$editForm = "<script language='javascript'>$('#DireccB').val('".$emailOTR."');$('#RFC').val('".$rfcOTR."');$('#Nomben2').val('".$aliasOTR."');$('#rcm').val('".$rcmOTR."');$('#NombreB').val('".$nombeOTR."');$('#ImpTipo').val('".$clabe[3]."');$('#Apocope option:selected').text('".strtoupper($clabe[1])."');</script>";

	$htmlDIVS = "<div id=\"msgresAltaLineabb_i\">
			<table width=\"860\" style=\"width:860px; height:60px; border: 1px solid;\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
				<tbody>
					<tr><td valign=\"top\">
					<table width=\"860\" style=\"width:100%;\" cellspacing=\"0\" cellpadding=\"0\">
						<tbody><tr>
							<td valign=\"top\" align=\"left\" style=\"height:15px; font-size:10px; font-weight:bold; background-image:url('/sitedesign/bajionet/images/barra_mor.png'); color:#ffffff; padding-top:5px; font-family:Tahoma, Verdana, Arial, Helvetica, sans-serif;\">
								Mensaje Transferencias
							</td>
							<td valign=\"top\" align=\"right\" class=\"cabecera-tabla\" style=\"height: 17px; background-image:url('/sitedesign/bajionet/images/barra_mor.png'); font-size:10px;font-weight: bold;color: white;font-family: Tahoma, Verdana, Arial, Helvetica, sans-serif;\">
								<img id=\"closeresAltaLinea2\" border=\"0\" src=\"/sitedesign/bajionet/images/btn_close.gif\" style=\"hight: 10px; cursor: pointer\" alt=\"Cerrar\" onclick=\"javascript: $('#msgresAltaLineabb_i').hide('slow'); return false;\">
							</td>
						</tr></tbody>
					</table>
					</td></tr>
					<tr><td valign=\"top\">
						<div id='resAltaLineabb_i' name='resAltaLineabb_i' class=\"msgAvisoT\" style=\"color:#000000; font-size:10px;\" ></div>
					</td></tr>
				</tbody>
			</table>
		</div>
		<div id=\"msgModalbb_i\" style=\"width: 100%;\">
		 	<table width=\"400\" style=\"width: 100%; height:60px; border: 1px solid;\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
		 	<tbody>
		 		<tr>
		 			<td valign=\"top\" >
		 				<table style=\"width: 100%;\" width=\"400\" cellspacing=\"0\" cellpadding=\"0\"><tbody>
		 					<tr>
		 						<td valign=\"top\" align=\"left\" style=\"height:15px; font-size:10px; font-weight:bold; background-image:url('/sitedesign/bajionet/images/barra_mor.png'); color:#ffffff; padding-top:5px; font-family:Tahoma, Verdana, Arial, Helvetica, sans-serif;\">
		 							Mensaje Transferencias
		 						</td>
		 						<td valign=\"top\" align=\"right\" class=\"cabecera-tabla\" style=\"height: 17px; background-image:url('/sitedesign/bajionet/images/barra_mor.png'); font-size:10px;font-weight: bold;color: white;font-family: Tahoma, Verdana, Arial, Helvetica, sans-serif;\">
		 							<img id=\"closeModalBene\" border=\"0\" src=\"/sitedesign/bajionet/images/btn_close.gif\" style=\"hight: 10px; cursor: pointer\" alt=\"Cerrar\" onclick=\"javascript: $('#msgModalbb_i').hide('slow'); return false;\">
		 						</td>
		 					</tr>
		 				</tbody></table>
		 			</td>
		 		</tr>
		 		<tr>
		 			<td valign=\"top\" >
		 				<div id='errorMsgbbInter' tabindex='1' class=\"msgErrorT\" style=\"color:red; font-size:11px;\"></div>
		 			</td>
		 		</tr>
		 	</tbody>
		 	</table>
		</div>";

		eZLog::writeNotice("******* HTML generado ******* -->|".$html."|<---");

	echo $editForm.$html//.$htmlDIVS;
		
?>
