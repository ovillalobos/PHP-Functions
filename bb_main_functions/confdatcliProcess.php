
<?php echo($procesarAjax);?>


<h1>Claves de validaci&oacute;n </h1>
<hr noshade="noshade" size="4" />

<br><br>
<TABLE border="0" width="90%">
<TR><TD>
</TD></TR>
<TR><TD>
&nbsp;<br>
</TD></TR>
<TR><TD>

<?php

echo( Valida_ClaveActivacion($session->variable( "r_usr" ), $parametros["cemail"], $parametros["cphone"] ) );

?>


</TD></TR>
<TR><TD>
</TD></TR>
<TR><TD>
&nbsp;<br>
</TD></TR>
</TABLE>

