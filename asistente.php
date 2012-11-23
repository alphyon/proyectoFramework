<?php
include_once 'encabezado.php';
include_once 'config.php';
include_once 'Coneccion.php';
include_once 'base/controladores.php';
include_once 'base/modelos.php';
include_once 'base/vistas.php';

$coneccion= BasedeDatos::coneccion();
$controlador = new Controladores();
$modelo = new Modelos();
$vista = new Vistas();
$carpeta = basename(getcwd());
?>
<p><?php print "Usted esta en la carpeta  ".$carpeta;?></p>
<p><?php   
     
   if(is_writable('config.php')){
    print 'Archivo de configuracion OK ';
?>
</p>
<p>Escriba los datos de necesarios para la configuracion de su proyecto</p>
<form action="asistente.php" class="form-horizontal">
    <p>Nombre del proyecto
    <input type="text" name="proyecto" /></p>
    <p>Titulo del Proyecto
    <input type="text" name="titulo" /></p>
    <p>Usuario de la Base de Datos
    <input type="text" name="usuario" /></p>
    <p>Clave del Usuario de la base de Datos
    <input type="password" name="clave" /></p>
    <input type="hidden" name="carpeta" value="<?php print $carpeta?>" />
    <p>Seleccione la Base de Datos</p>
      <select name="base">
        <option>----</option>
    <?php 
    
    $sql="SHOW DATABASES";
    $a = BasedeDatos::consultaD($coneccion, $sql,3);
    foreach ($a as $key => $bdd) {
    print '<option>'.$bdd[0].'</option>';

    }
        
   
    
    ?>
   
    </select>
     <p>si esta seguro que estos datos estan correctos oprima el boton para generar proyecto </p><input type="submit" name="bot">
<p>
    
    <?php 
if(isset($_REQUEST['bot']) &&$_REQUEST['base']!="----" && $_REQUEST['base']!=NULL && isset($_REQUEST['base'])){
   
    print "<div class='alert alert-success'>";
 print "Se ha creado con exito su proyecto</div>";

  
    
  
 $controlador->CrearControladores($_REQUEST['base'],$carpeta);
$modelo->CrearModelos($_REQUEST['base']);
$vista->Formularios($_REQUEST['base'],$carpeta);
$vista->MFormularios($_REQUEST['base'],$carpeta);
$vista->accciones($_REQUEST['base'],$carpeta);
$vista->index($_REQUEST['base'],$carpeta);
$vista->CrearMenu($_REQUEST['base']);
      $fp = fopen("config.php", 'w');
     $varsalida="<?php
define('DNS','mysql:dbname=mydb;host=127.0.0.1');          
define(\"BASE\",\"ok\");         
define(\"NOMBRE\",\"".$_REQUEST['proyecto']."\");
define(\"TITULO\",\"".$_REQUEST['titulo']."\");
define(\"DBNAME\",\"".$_REQUEST['base']."\");
define(\"CLAVE\",\"".$_REQUEST['clave']."\");
define(\"HOST\",\"localhost\");
define(\"USUARIO\",\"".$_REQUEST['usuario']."\");
define(\"URL\",\"http://".$_SERVER['HTTP_HOST']."/".basename(getcwd())."/\");
?>";
       fputs($fp, $varsalida);
     
}


?></p>

</form>

<?php
}else{
    print 'NO'.is_writable($carpeta);
} 
    
?>



<?php include_once 'pie.php'?>




