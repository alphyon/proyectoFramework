<?php

//require_once '../Coneccion.php';
//require_once '../controles.php';
/**
 * Jose Antonio Henriquez
 *
 * Generacion de tamplates basicas
 *
 * Se generan 4 archivos para generar un front-end
 * --Formularios crea el formulario asociado a los campos
 * de la tabla de la base de datos, este se utilizara para las
 * funciones de guardado y actulizado.
 * --MFormularios se crea para tener el objeto que controla
 * el acceso a la clase a la cual esta asociado el formulario
 * -- index genera un archivo de muestra de datos guardados en la tabla
 * permite acceder a las funciones basicas de manejo de los registros de las
 * tabla.
 * --acciones se crea como un ayudante que permitira contolar que accion se ejecuta
 * de acuerdo a las selecciones de los usuarios.
 *
 * @author José Antonio Henríqurez Chavarría
 * @author twitter:@alphyon
 * @author facebook:http://facebook.com/alphyon21
 *
 * @package base_zamara
 */

class Vistas{


    function Formularios($bdd)
{
/**
* Genera un formulario con los campos de las 
* tablas de la base de datos los controles que se
 * crean usan la base de clase controles por defecto solo 
 * se crean controles del tipo texto y todos poseeen el atributo 
 * de validacion activado.
*
* @return string bandera estado 1 si se ejecuta correctamente ,
*  0 si ha ocurrido un error
* @param string $bdd nombre de la base de datos a utilizar
* @param string $folder  nombre del folder raiz donde se crearan los archivos.
*/
    $bandera = 0;
    //$objCon = new controles();
    $con = BasedeDatos::coneccion();    
    $sql = "SHOW TABLES from $bdd";
    $ser = BasedeDatos::consultaD($con, $sql,2);

    if(!file_exists("vistas")){
        mkdir("vistas");
    } 
    foreach ($ser as $tabla) {
        foreach ($tabla as $dato) {
            $nomT=str_replace("_" , "" , $dato);
            if(!file_exists("vistas/" . $nomT)){
                mkdir("vistas/".$nomT);
            } 
            chmod("vistas/" . $nomT, 0777);
            $fp = fopen("vistas/" . $nomT . "/formulario.php" , 'w');
            $varsalida = "<?php require_once  \"Mformulario.php\" ?>";
            $varsalida .= "\n<h2>" . ucfirst($nomT) . "</h2>";
            $varsalida .= "\n<script type=\"text/javascript\">";
            $varsalida .= "\n$().ready(function(){\$(\"#formulario\").validate({";
            $varsalida .= "errorClass: \"validate\"})});";
            $varsalida .= "\n</script>";
            $varsalida .= "\n<?php print \$rotulo;?>";
            $varsalida .= "\n<div id = 'formu'>";
            $varsalida .= "\n<form name = \"" . $nomT;
            $varsalida .= "\" id = \"formulario\" action = \"acciones.php\" method = \"POST\" class=\" form-horizontal\">";
            
            $sql_tablas ="SHOW TABLES FROM $dato";

//se realiza un doble foreach para extraer el valor devuelto por la matriz

    
        //generar la consulta que devuelva 
        //el recorrido de cada tabla y muetre su estructura
       
        $sql_columnas="SHOW COLUMNS FROM ".$dato;
        $estructura = BasedeDatos::consultaD($con, $sql_columnas);
        foreach ($estructura as $datregistro) {
           if($datregistro["Key"] == "MUL"){
               $comparar = "rela";
               $tipo = "rela";
               $sqlclaves="SELECT table_name, referenced_table_name, referenced_column_name,column_name
    		 FROM information_schema.key_column_usage
    		 WHERE referenced_table_schema ='$bdd'
       		 AND referenced_table_name is not NULL AND table_name ='$dato';";                 
                 $camposbdc = BasedeDatos::consultaD($con,$sqlclaves,2);
                 foreach ($camposbdc as $tablarelacion) {                  
                   if($datregistro["Field"]==$tablarelacion["column_name"]){                      
                     $tablar = $tablarelacion["referenced_table_name"];
                    }  
                 }
                 
           }else{
               $comparar ="otro";
               $tablar ="nada";
               if(strpos($datregistro['Type'], "(")!= 0){
                     
                        $tipo = strstr($datregistro['Type'],"(",TRUE);
                }else{
                        $tipo =  $datregistro['Type'];
                }  
           }
          
           $nomF = str_replace("_", "", $datregistro['Field']);
           if($nomF == "id"){
                    $varsalida.= "\n<input type = 'hidden' name = '" . $nomF . "'";
                    $varsalida.= " value = '<?php print \$obj->get" . $nomF . "();?>' />";
                    
                }else{
                    $varsalida .= "<div class=\"control-group\">";
                    $varsalida .= "<div class=\"control-label\">".$nomF."</div>";
                    $varsalida .= "<div class=\"controls\">";
                    switch ($tipo) {
                    case 'int':
                    case 'varchar':
                    default:
                        $varsalida.= "\n\t\t<?php print \$objCon->texto(\"" . $nomF . "\",\$obj->get";
                        $varsalida.= ucfirst($nomF) . "()); ?>";
                        break;
                     case 'date':
                        $varsalida.= "\n\t\t<?php print \$objCon->fecha(\"" . $nomF . "\",\"" . $nomF . "\",\$obj->get";
                        $varsalida.= ucfirst($nomF) . "()); ?>";
                        break;
                     case 'enum':                      
                       $varsalida.= "\n\t<?php";
                       $ar=explode(',',str_replace('\'','',str_replace(')','',str_replace('(','',str_replace('enum','',$datregistro['Type'])))));
                       foreach ($ar as $ard) {
                       $varsalida.= "\n\t\$ars".$nomF."[]=\"".$ard."\";";
                       }
                       $varsalida.= "\n\t\t print \$objCon->lista(\"" . $nomF . "\",\$ars".$nomF.",\"\"); ?>";
                        break;
                       case 'rela':                      
                       $varsalida.="\n\t\t<?php print \$objCon->listaDatos(\"".$nomF."\",\"".$tablar."\",\$obj->get";
                       $varsalida.= ucfirst($nomF) . "());?>";
                       break;
                }
           
                $varsalida .= "</div>";
                $varsalida .= "</div>";
                    
                }
                     
        }
        
    

            
            $varsalida .= "\n\t<div class=\"controls\">\n\t\t<input type = 'submit' name = 'bot'";
            $varsalida .= " value ='<?php print \$nbo?>' class = 'btn btn-success'/>\n\t</div>";
            
            $varsalida .= "\n</form>";
            $varsalida .= "\n</div>";
            $varsalida .= "\n<?php require_once  \"../../pie.php\"; ?>";
            fputs($fp, $varsalida);
            chmod("vistas/" . $nomT . "/formulario.php", 0777);    
        }
        
    }
    $bandera=1;
    return $bandera;
}


function MFormularios($bdd)
{
    /**
* Genera el controlador de los formularios implementa
* elemento modelo para el acceso y control de los datos 
*
* @return $badera 1 si resultado es satisfactorio
* @param string $folder  nombre del folder raiz donde se crearan los archivos.
*/
    $bandera = 0;
    $con = BasedeDatos::coneccion();    
    $sql = "SHOW TABLES from $bdd";
    $ser = BasedeDatos::consultaD($con, $sql,2);
    if(!file_exists("vistas")){
        mkdir("vistas");        
    }
    foreach ($ser as $tabla) {
        foreach ($tabla as $dato) {
           $nomT = str_replace("_", "", $dato);
            if(!file_exists("vistas/" . $nomT)){
                mkdir("vistas/" . $nomT);
            } 
            chmod("vistas/" . $nomT, 0777);
            $fp = fopen("vistas/" . $nomT. "/Mformulario.php", 'w');

            $varsalida = "<?php require_once  \"/../../controles.php\";?>";
            $varsalida .= "\n<?php require_once   \"../../encabezado.php\";?>";  
            $varsalida .= "\n<?php require_once   \"../../modelos/".$nomT.".php\";?>"; 
            $varsalida .= "\n<?php require_once  \"../../Coneccion.php\";?>"; 
            $varsalida .= "\n<?php"; 
            $varsalida .= "\n\t\$objCon = new controles();";
            $varsalida .= "\n\t\$obj = new " . ucfirst($nomT) . "();";
            $varsalida .= "\n\t\$con = BasedeDatos::coneccion();";
            $varsalida .= "\n\tif(@\$_REQUEST[\"formu\"] == \"Mod\"){";
            $varsalida .= "\n\t\t@\$sql = \"SELECT * FROM ".$nomT." WHERE id=\" . \$_REQUEST['id'] . \"\";";
            $varsalida .= "\n\t\t\$resl = BasedeDatos::consultaD(\$con,\$sql);";
            $sql_campos = "SHOW COLUMNS from $bdd." . $dato;
            $res = BasedeDatos::consultaD($con, $sql_campos, 2);
            foreach ($res as $datosf) {
                $varsalida .= "\n\t\t\$obj->set";
                $varsalida .= ucfirst(str_replace("_", "", $datosf['Field']));
                $varsalida .= "(\$resl[0][\"" . $datosf['Field'] . "\"]);";
            }
            $varsalida .= "\n\t\t\$rotulo = \"<p>Numero de Registro a Modificar \""; 
            $varsalida .= " . \$obj->getId() . \"</p>\";";
            $varsalida .= "\n\t\t\$nbo = \"Modificar\";";        
            $varsalida .= "\n\t}else{";            
            foreach ($res as $datosf) {
                $varsalida .= "\n\t\t\$obj->set";
                $varsalida .= ucfirst(str_replace("_", "", $datosf['Field']));
                $varsalida .= "(\"\");";
            }
            $varsalida .= "\n\t\t\$rotulo = \"\";";
            $varsalida .= "\n\t\t\$nbo = \"Guardar\";";
            $varsalida .= "\n\t}";
            $varsalida .= "\n?>";
            fputs($fp,$varsalida);
            chmod("vistas/".$nomT."/Mformulario.php",0777); 

        }
    }
    
   
 
    $bandera=1;
    return $bandera;
}


function index($bdd)
{
    /**
* Archivo index de la vista 
 * muestra una tabla con los registros almacenados en la base de datos
 * y define vinculo para agregar un nuevo registro
*
* @return $badera 1 si resultado es satisfactorio
* @param string $folder  nombre del folder raiz donde se crearan los archivos.
*/
    
    //$objCon = new controles();
    $bandera = 0;
    $con = BasedeDatos::coneccion();    
    $sql = "SHOW TABLES from $bdd";
    $ser = BasedeDatos::consultaD($con, $sql,2);

    if(!file_exists("vistas")){
        mkdir("vistas");
    }
    chmod("vistas", 0777);
    foreach ($ser as $tabla){     
        foreach ($tabla as $dato) {
            $nomT=str_replace("_", "", $dato);     
        if(!file_exists("vistas/".$nomT)){
             mkdir("vistas/".$nomT);
        }     
        chmod("vistas/".$nomT, 0777);
        $fp = fopen("vistas/".$nomT."/index.php", 'w');
        $varsalida = "<?php require_once   \"../../controladores/" ;
        $varsalida .= $nomT . ".php\";?>";
        $varsalida .= "\n<?php require_once  \"../../controles.php\";?>";
        $varsalida .= "\n<?php";
        $varsalida .= "\n\t\$control = new controles();";
        $varsalida .= "\n\t\$persis = new C" . ucfirst($nomT) . "();";
        $varsalida .= "\n?>";
        $varsalida .= "\n<?php require_once   \"../../encabezado.php\";?>";
        $varsalida .= "\n<h2> prueba de index de app " . $nomT . " </h2>";
        $varsalida .= "\n<?php";
        $varsalida .= "\n\t\$objt = \$persis->consultarFull(\"" . $dato . "\");";    
        $varsalida .= "\n\tprint \$control->imprimetabla(\$objt,\"table table-striped table-bordered table-condensed\",1) \n?>";
        $varsalida .= "\n<a href = 'formulario.php' title = 'crear " . $nomT . "'> Nuevo " . $nomT . "</a>";
        $varsalida .= "\n<?php require_once  \"../../pie.php\";?>";
        fputs($fp,$varsalida);
        chmod("vistas/".str_replace("_", "", $dato)."/index.php", 0777);
        }
    };
    $bandera=1;
    return $bandera; 
}


function accciones($bdd)
{
/**
* Archivo de acciones
 * genera el selector de acciones de CRUD de la tabla
*
* @return $badera 1 si resultado es satisfactorio
* @param string $folder  nombre del folder raiz donde se crearan los archivos.
*/
    $bandera = 0;
   // $objCon = new controles();
    $con = BasedeDatos::coneccion();    
    $sql = "SHOW TABLES from $bdd";
    $ser = BasedeDatos::consultaD($con, $sql,2);

    if(!file_exists("vistas")){
        mkdir("vistas");
        
      } 
      
    chmod("vistas", 0777);
    
    foreach ($ser as $tabla){
        foreach ($tabla as $dato) {
            $nomT=str_replace("_", "", $dato);        
            if(!file_exists("vistas/" . $nomT)){
                mkdir("vistas/" . $nomT);
              }
            chmod("vistas/" . $nomT, 0777);     
            $fp = fopen("vistas/" . $nomT . "/acciones.php", 'w');
           
            $varsalida = "<?php require_once  \"../../encabezado.php\";?>";
            $varsalida .= "\n<script>";
            $varsalida .= "\n\t$(function(){";
            $varsalida .= "\n\t\t$( \"#mensaje\" ).dialog( \"destroy\" );";
            $varsalida .= "\n\t\t$( \"#mensaje\" ).dialog({";
            $varsalida .= "\n\t\t\tmodal: true,";
            $varsalida .= "\n\t\t\tshow: \"slide\",";
            $varsalida .= "\n\t\t\tbuttons:{";
            $varsalida .= "\n\t\t\t\tOk: function() {";
            $varsalida .= "\n\t\t\t\t$( this ).dialog( \"close\" );";
            $varsalida .= "\n\t\t\t\turl = \"index.php\";";
            $varsalida .= "\n\t\t\t\t$(location).attr('href',url);";  
            $varsalida .= "\n\t\t\t\t}";
            $varsalida .= "\n\t\t\t}";
            $varsalida .= "\n\t\t});";
            $varsalida .= "\n\t});";
            $varsalida .= "\n</script>";
            $varsalida .= "\n<div id ='mensaje'>";
            $varsalida .= "\n<?php require_once  \"../../controladores/" ;
            $varsalida .= $nomT . ".php\";?>";
            $varsalida .= "\n<?php";
            $varsalida .= "\n\t\$persis=new C" . ucfirst(str_replace("_", "", $dato))."();";
            $sql_camposcl = "SHOW COLUMNS from $bdd." . $dato;
            $campocl = BasedeDatos::consultaD($con,$sql_camposcl,2);
            foreach ($campocl as $datosf){    
                $varsalida .= "\n\t@\$persis->set" . ucfirst(str_replace("_", "", $datosf['Field']));
                $varsalida .= "(\$_REQUEST[\"" . str_replace("_", "", $datosf['Field']) . "\"]);";
                };

            $varsalida .= "\n\tswitch(\$_REQUEST[\"bot\"]){";
            $varsalida .= "\n\t\tcase \"Guardar\":";
            $varsalida .= "\n\t\tprint \$persis->guardar(\$persis);";
            $varsalida .= "\n\tbreak;";
            $varsalida .= "\n\tcase \"Modificar\":";
            $varsalida .= "\n\t\tprint \$persis->modificar(\$persis,\$_REQUEST[\"id\"]);";
            $varsalida .= "\n\tbreak;";
            $varsalida .= "\n\tcase \"Borrar\";";
            $varsalida .= "\n\t\tprint \$persis->borrar(\$_REQUEST[\"id\"]);";
            $varsalida .= "\n\tbreak;";
            $varsalida .= "\n\tdefault:";
            $varsalida .= "\n\t\tprint \"No seleccionado accion\";";
            $varsalida .= "\n\tbreak;";
            $varsalida .= "\n\t}";
            $varsalida .= "\n?>";
            $varsalida .= "\n</div>";
            $varsalida .= "\n<?php require_once  \"../../pie.php\"; ?>";
            fputs($fp,$varsalida);
            chmod("vistas/" . str_replace("_", "", $dato) . "/acciones.php", 0777);   
        }
        
    };
 
    $bandera=1;
    return $bandera;
}
  function CrearMenu($bdd){
   $bandera=0;
   $con = BasedeDatos::coneccion();    
    $sql = "SHOW TABLES from $bdd";
    $ser = BasedeDatos::consultaD($con, $sql,2);

    $fp = fopen("menu.php", 'w');  
    $varsalida = "<?php\n";
        $varsalida .="include_once 'encabezado.php';";
        foreach ($ser as $datos){
            foreach ($datos as $tabla) {
                
            }
       
            $nomT = str_replace("_", "", $tabla);       
       
       $varsalida .="\n\tprint '<p><a href=vistas/" . $nomT . "/ >ir a " . $nomT . " </a></p>';";
       
        };
        $varsalida .="\ninclude_once 'pie.php' ?>";
    
      
    
       fputs($fp,$varsalida);
       chmod("modelos/".$nomT.".php",0777);    
 
 
 $bandera=1;
 
 return $bandera; 
}


}


