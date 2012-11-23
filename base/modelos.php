<?php
//require_once '../Coneccion.php';

/**
* Jose Antonio Henriquez
*
* Automatizacion de Clases modelos
*
* Se generan las clase con los metodos set y get
* apartir de las tablas especificadas en la base de datos
*
* @author José Antonio Henríqurez Chavarría
* @author twitter:@alphyon
* @author facebook:http://facebook.com/alphyon21
*
* @package base
*/
class Modelos 
{
    function CrearModelos($bdd){
        $bandera=0;
        $con = BasedeDatos::coneccion();
        $sql_tablas = "SHOW TABLES from $bdd";
        $tablabd= BasedeDatos::consultaD($con, $sql_tablas, 2);
        if(!file_exists("modelos")){
            mkdir("modelos");
        } 
        chmod("modelos", 0777); 
        foreach ($tablabd as $value) {
            foreach ($value as $dato) {
                $nomT = str_replace("_", "", $dato);
                $fp = fopen("modelos/".$nomT.".php", 'w');     
                $varsalida = "<?php\n";
                $varsalida .="\tclass " . $nomT . "{\n";
                $sql_campos = "SHOW COLUMNS from $bdd.".$dato;
                $camposbd = BasedeDatos::consultaD($con,$sql_campos,2);
                foreach ($camposbd as $datosf) {
                    $varsalida .= "\tprivate $" . str_replace("_", "", $datosf['Field']) . ";\n";
                    
                }
                $sqlclaves="SELECT table_name, referenced_table_name, referenced_column_name,column_name
    		 FROM information_schema.key_column_usage
    		 WHERE referenced_table_schema ='$bdd'
       		 AND referenced_table_name is not NULL ";                 
                 $camposbdc = BasedeDatos::consultaD($con,$sqlclaves,2);
                 foreach ($camposbdc as $foraneos=>$datosfor) {
                    if($dato == $datosfor['table_name']){
                       $varsalida .= "\n\tpublic \$rel".$datosfor['referenced_table_name']."=";
                       $varsalida .="\"SELECT * FROM ". $datosfor['referenced_table_name']."\";";
                    }
                 }
                foreach ($camposbd as $datosf) {
                    $varsalida .= "\n\t//Generacion de metodos set y get para variable ";
                    $varsalida .= str_replace("_", "", $datosf['Field']);  
                    $varsalida .= "\n\tpublic function set";
                    $varsalida .= ucfirst(str_replace("_", "", $datosf['Field'])) . "(\$valor){\n";
                    $varsalida .= "\t\t\$this->" . str_replace("_", "", $datosf['Field']) . "= \$valor;";
                    $varsalida .= "\n\t}\n";     
                    $varsalida .= "\n\tpublic function get";
                    $varsalida .= ucfirst(str_replace("_", "", $datosf['Field'])) . "(){";
                    $varsalida .= "\n\treturn \$this->" . str_replace("_", "", $datosf['Field']).";";
                    $varsalida .= "\n\t}\n";
                    
                }
                
                 
                 $varsalida .= "\n}\n?>";
                 fputs($fp,$varsalida);
                 chmod("modelos/".$nomT.".php",0777);   
            }
         }   
        $bandera=1;
        $con=NULL;
        return $bandera; 
    }

}



