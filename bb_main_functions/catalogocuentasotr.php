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

echo "<script language=\"javascript\">

	function editarReferencia(rfc,email,alias,rcm,nombe,clabe){

		//alert('Buscando la referencia....['+rfc+']......['+email+']....['+alias+']....['+rcm+']....['+nombe+']');		
		var postParam = 'emailOTR:'+email+',rfcOTR:'+rfc+',aliasOTR:'+alias+',rcmOTR:'+rcm+',nombeOTR:'+nombe+',clabe:'+clabe;
		$.ajax({
	        type: 'POST',
	        url: '/procesarAjaxMenu.php', 
	        data: {nomFunc:'otrCuentasE',parametro:postParam},         
          	//data: 'nomFunc=otrCuentasE',
            dataTypedataType: 'html',
	  success: function(msg){                     
                  
               //  alert('Recibiendo respuesta....['+msg+']');
                  $('#mainContents').html(msg);                           
	        }              
       });
	}

</script>";

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
                         "eztransaccion/user/intl/", $Language, "CatalogoDeCuentasotr.php" );

    $t->setAllStrings();

    $t->set_file( array(  "CatalogoDeCuentasotr_tpl" => "CatalogoDeCuentasotr.tpl"   ) );


    $session =& eZSession::globalSession();

    if ( !$session->fetch() )
    {
        $session->store();
    }

    $tr		= new eZTransaccion( );
    $usr	= $session->variable( "r_usr" );
    $qki	= $session->variable( "r_qki" );
    $priv	= $session->variable( "r_priv" );


    $transaccion_buffer = "";
    //DBA Aut2
    echo "<div id='contentOTR'></div>";

//HB
	if(!empty($_POST['Cust']))
		$Cust = $_POST['Cust'];
	
	if(!empty($_POST['Cuenta']))
		$Cuenta = $_POST['Cuenta'];
		
	if(!empty($_POST['Sub']))
		$Sub = $_POST['Sub'];
		
	if(!empty($_POST['code']))
		$code = $_POST['code'];
		
	if(!empty($_POST['RenCap']))
		$RenCap = $_POST['RenCap'];
		
		
	if(!empty($_POST['RenInt']))
		$RenInt = $_POST['RenInt'];
		
	if(!empty($_POST['LadaB']))
		$LadaB = $_POST['LadaB'];
		
	if(!empty($_POST['Talonar']))
		$Talonar = $_POST['Talonar'];
		
	if(!empty($_POST['Editar']))
		$Editar = $_POST['Editar'];
		
		
	if(!empty($_POST['TeleB']))
		$TeleB = $_POST['TeleB'];
		
	// DGM
	if(!empty($_POST['RFC']))
		$RFC = $_POST['RFC'];
//HB

    if(empty($_POST['Access'])) { $_POST['Access'] = "FrAc";  }

    switch($_POST['Access'])
	{
    case "FrAc":
        $particularFields = "";
        $_POST['Accion'] = "Listacta";
        $particularFields = "&Cust=".urlencode($Cust)."&Cuenta=".urlencode($Cuenta)."&Sub=".urlencode($Sub)."&Accion=".urlencode($_POST['Accion'])."&UbNo=".urlencode($_POST['UbNo'])."&Pos=".urlencode($_POST['Pos']);
        $tr->blog($qki,"Catalogo de CuentasOTR", $_POST['FrAccount'], $Cust, $Cuenta, $Sub, $_POST['Accion']);
        break;
    case "Confirm":
    case "Process":
        if($_POST['Listado']=="Consultas") {
            $_POST['Accion'] = "Listacta";
        }
        else if($_POST['Newcta']=="Altas") {
            $_POST['Accion'] = "Newcta";
        }
        else if($_POST['Editcta'] == "Cambios"){
        	$_POST['Accion'] = "Editcta";
        }
        else if($_POST['MenuCan']=="Bajas") {
            $_POST['Accion'] = "MenuCan";
            $_POST['DiasPzo'] = "";
            $_POST['DiasPzo'] = "a".urlencode($_POST['chkbox0'])."a".urlencode($_POST['chkbox1'])."a".urlencode($_POST['chkbox2'])."a".
            		   urlencode($_POST['chkbox3'])."a".urlencode($_POST['chkbox4'])."a".urlencode($_POST['chkbox5'])."a".
            		   urlencode($_POST['chkbox6'])."a".urlencode($_POST['chkbox7'])."a".urlencode($_POST['chkbox8'])."a".
            		   urlencode($_POST['chkbox9']);
        }
        else if($_POST['Listado']=="Cancelar" || $_POST['Listado']=="Regresar" ) {
            $_POST['Accion'] = "Listacta";
        }
        else if($_POST['Button'] == "Alta Beneficiario"){
        	$_POST['Accion'] = "MenuAlt";
        }
        else if($_POST['Button'] == "Actualizar"){
        	$_POST['Accion'] = "MenuEdit";
        }


		// en $Rencap se va el apocope del banco
		// en $Apocope se va la clave del banco
		if ( $_POST['Access'] == "Confirm")
		{
			$RenCap = $_POST['Apocope'];
			$_POST['Apocope'] = $_POST['Apocope2'];
		}

		// dgm agregar rfc
        $particularFields = "&Passwd=".encrypt( $code, strtolower( $usr ) )."&Cust=".urlencode($Cust)."&Apocope=".urlencode($_POST['Apocope']).
        "&UbNo=".urlencode($_POST['UbNo'])."&RFC=".urlencode($RFC)."&PlazaB=".urlencode($_POST['PlazaB']).
        "&Accion=".urlencode($_POST['Accion'])."&DiasPzo=".urlencode($_POST['DiasPzo'])."&NombreB=".urlencode($_POST['NombreB']).
        "&RenCap=".urlencode($RenCap)."&Nomben2=".urlencode($_POST['Nomben2']).
        "&RenInt=".urlencode($RenInt)."&Nomben3=".urlencode($_POST['Nomben3']).
        "&DireccB=".urlencode($_POST['DireccB'])."&LadaB=".urlencode($LadaB).
        "&ImpTipo=".urlencode($_POST['ImpTipo'])."&PAN=".urlencode($_POST['PAN']).
        "&Empresa=".urlencode($_POST['Empresa'])."&NumCheq=".urlencode($_POST['NumCheq']).
        "&EspecCh=".urlencode($_POST['EspecCh'])."&Talonar=".urlencode($Talonar).
        "&Editar=".urlencode($Editar).
		"&RFC=".urlencode($RFC).
        "&TeleB=".urlencode($TeleB)."&Pos=".urlencode($_POST['Pos'])."&Button=".urlencode($_POST['Button']);
        $tr->blog($qki,"Catalogo de Cuentas", $_POST['FrAccount'], $Cust, $Cuenta, $Sub, $_POST['Accion']);
        break;
    }
    $transaccion_buffer = "";
    // DebugBreak();
    $ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=ota&Access=".urlencode($_POST['Access'])."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // ligas de cuentas de terceros
 

	if ( $_POST['Accion'] == "Editcta" )
	{

		// sirve para encontrar los valores de reemplazo del banco
		$posIni1 = strpos($transaccion_buffer, "REMPLAZO1=");

		if ( $posIni1 > 0 )
		{
			$posFin1 = strpos($transaccion_buffer, "FIN1");
			$valor1 = substr($transaccion_buffer, ($posIni1 + 10), ($posFin1-($posIni1 + 10)));
		}

		$posIni2 = strpos($transaccion_buffer, "REMPLAZO2=");
		$posFin2 = strpos($transaccion_buffer, "FIN2");
		$valor2 = substr($transaccion_buffer, ($posIni2 + 10), ($posFin2-($posIni2 + 10)));


		if ( strpos($transaccion_buffer,"<OPTION VALUE=\"\">") != false )
		{
			$remplazo = "<OPTION SELECTED VALUE=".$valor2.">".$valor1;
			$transaccion_buffer = str_replace("<OPTION VALUE=\"\">",$remplazo,$transaccion_buffer);
		}
		//esta es la cadena que tenemos que eliminar del transaccion_buffer
		//sirvio unicamente como temporal para modificar un speilist.i
		$cadena = substr($transaccion_buffer, $posIni1, ($posFin2+4) - ($posIni1 ));
		// Por ultimo se quita la cadena que nos sirvio de reemplazos
		//var_dump($cadena);
		$transaccion_buffer = str_replace($cadena," ", $transaccion_buffer);


	}
	//MAOS Nov2013 Quitar Scrolls I
	if($_POST['MenuCan']=="Bajas" && $_POST['Access'] == "Confirm") 
	{
		$posIni3 = strpos($transaccion_buffer, "<!--rempAut2i-->");
		$posFin3 = strpos($transaccion_buffer, "<!--rempAut2f-->");
		$buff1 = substr($transaccion_buffer,0,$posIni3);
		$buff2 = substr($transaccion_buffer,$posFin3+16);
		
		$buffRemplazo = substr($transaccion_buffer, $posIni3+16, ($posFin3-($posIni3+16)));

		eZLog::writeNotice("Buffer antes de modificar.1..[".$buffRemplazo."]");

		$buffRemplazo = str_replace("<TR>", "", $buffRemplazo);
		$buffRemplazo = str_replace("<TD>", "", $buffRemplazo);
		$buffRemplazo = str_replace("</TD>", "", $buffRemplazo);
		$buffRemplazo = str_replace("<TD COLSPAN=3>&nbsp;", "", $buffRemplazo);
		$buffRemplazo = str_replace("</TR>", "|", $buffRemplazo);
		eZLog::writeNotice("Buffer antes de modificar..2.[".$buffRemplazo."]");

		$buff3 = explode("|", $buffRemplazo);
		eZLog::writeNotice("Buffer con explode....[".print_r($buff3,true)."]");
		foreach($buff3 as $aux)
		{
			if (trim($aux) != "") // Banamex ClaBE 002123456789012343
			{
				eZLog::writeNotice("Entre a verificar el aux..[".trim($aux)."]");
				$aux2 = explode(" ",trim($aux));
				eZLog::writeNotice("Buffer con explode..2..[".print_r($aux2,true)."]");
				
				if ( $aux2[1] != "")
					$tipo = $aux2[1];
				else
					$tipo = $aux2[2];

				if ( $aux2[3] != "" )
					$clabe = $aux2[3];
				else
					$clabe = $aux2[10];

				$html .= "<tr>".
							"<td bgcolor=\"#DDDDDD\"><center>".$aux2[0]."</center></td>".
				            "<td bgcolor=\"#DDDDDD\"><center>".$tipo."</center></td>".
				            "<td bgcolor=\"#DDDDDD\"><center>".$clabe."</center></td>".
				            "</tr>";
			}
		}


		$buffRemplazo = "<table width=\"95%\" cellspacing=\"2\" border=\"0\"><tbody>".
						"<tr bgcolor=\"#5A419C\"><th><font color=\"WHITE\"><center>Banco</center></font></th>". 
						"<th><font color=\"WHITE\"><center>Tipo</center></font></th>". 
						"<th><font color=\"WHITE\"><center>Cuenta /  N&uacute;m.Tel. M&oacute;vil</center></font></th></tr>". 
						$html."</tbody>";
		
		$transaccion_buffer = $buff1.$buffRemplazo.$buff2;

	}

	$transaccion_buffer = str_replace("<TH>Beneficiario:</TH></TR>","<TH>Beneficiario:</TH><TH>RFC:</TH></TR>", $transaccion_buffer);
	$transaccion_buffer = str_replace("NAME=\"NombreB\" SIZE=\"30\" MAXLENGTH=\"30\" onKeypress=\"alfanumerico(this);\"></TD></TR>",
					"NAME=\"NombreB\" SIZE=\"30\" MAXLENGTH=\"30\" onKeypress=\"alfanumerico(this);\"></TD><TD><INPUT  TYPE=\"TEXT\" NAME=\"RFC\" SIZE=\"13\" MAXLENGTH=\"13\"></TD></TR>", $transaccion_buffer);
	$transaccion_buffer = str_replace("<TD><B>RFC</B></TD></TR><TR><TD><INPUT  TYPE=\"TEXT\" NAME=\"RFC\" SIZE=\"13\" MAXLENGTH=\"13\"></TD></TR><TR>"," ", $transaccion_buffer);
	$transaccion_buffer = str_replace("<TH ALIGN=LEFT>Banco receptor</TH></TR><TR>"," ", $transaccion_buffer);
	$transaccion_buffer = str_replace("</OPTION></SELECT></TD></TR>","</OPTION></SELECT></TD>", $transaccion_buffer);
	$transaccion_buffer = str_replace("NAME=\"Nomben2\"","NAME=\"Nomben2\" ID=\"Nomben2\" mensaje=\"Intruduzca  el dato con el que identificar a su beneficiario \" onMouseOver=\"mostrarAyuda('Nomben2');\" onmouseout=\"rmvAyuda();\"", $transaccion_buffer);
	$transaccion_buffer = str_replace("NAME=\"NombreB\"","NAME=\"NombreB\" ID=\"NombreB\" mensaje=\"Nombre de la persona o razón social que recibirá la transferencia \" onMouseOver=\"mostrarAyuda('NombreB');\" onmouseout=\"rmvAyuda();\"", $transaccion_buffer);
	$transaccion_buffer = str_replace("NAME=\"RFC\"","NAME=\"RFC\" ID=\"RFC\" mensaje=\"Dato Opcional \" onMouseOver=\"mostrarAyuda('RFC');\" onmouseout=\"rmvAyuda();\"", $transaccion_buffer);
	//MAOS Nov2013 Quitar Scrolls F

	//DBA Aut2 , para agregar la columna de edicion
	// En omni(htmlocta.f) se regresa la cadena rempAut0,1,2,3...9 y reemplazamos el valor para agregar el lapz de editar
	for( $i=0;$i<10;$i++)
		$transaccion_buffer = str_replace("<!--rempAut$i-->", "<center><a href=\"javascript:editarReferencia(document.getElementsByName('rfc$i')[0].value,document.getElementsByName('direcc$i')[0].value,document.getElementsByName('alias$i')[0].value,document.getElementsByName('chkbox$i')[0].value,document.getElementsByName('nombe$i')[0].value,document.getElementsByName('benef$i')[0].value);\"><img width=\"15\" height=\"15\" title=\"Editar\" style=\"cursor: pointer; border: 0px solid red;\" src=\"/sitedesign/bajionet/images/ic_editar.gif\"></a></center>", $transaccion_buffer);
	//DBA Aut2 , para agregar la columna de edicion

    $t->set_var( "transaccion_buffer", $transaccion_buffer );
    $t->pparse( "output", "CatalogoDeCuentasotr_tpl" );
}
else
{
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "userlogin.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "user_login_tpl" => "userlogin.tpl"
        ) );

    $t->set_var( "redirect_url", "/transaccion/CatalogoDeCuentasotr/" );

    $t->pparse( "output", "user_login_tpl" );
}
?>
