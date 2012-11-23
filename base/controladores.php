<?php
//require_once '../Coneccion.php' || 'Coneccion.ph';

/**
 * Jose Antonio Henriquez
 *
 * Funciones de acceso a base de Daos
 * CRUD Basico
 *
 * Se generan las clase controladoras de las acciones de 
 * guardado, recuperacion, borrado y actulizacion de datos 
 * de cada tabla de la base de datos
 *
 * @author José Antonio Henríqurez Chavarría
 * @author twitter:@alphyon
 * @author facebook:http://facebook.com/alphyon21
 *
 * @package base
 */  
class Controladores 
{
    function CrearControladores($bdd)
    {
        $bandera=0;
        $con = BasedeDatos::coneccion();
        $sql_tablas = "SHOW TABLES from $bdd";
        $tablabd= BasedeDatos::consultaD($con, $sql_tablas, 2);
        if( ! file_exists("controladores")){
            mkdir("controladores");
        } 
        chmod("controladores", 0777);
        foreach ($tablabd as $value) {
            foreach ($value as $dato) {
                $fp = fopen("controladores/".str_replace("_", "", $dato).".php", 'w');
                chmod("controladores/".str_replace("_", "", $dato).".php",0777);
                $varsalida = "<?php";     
                $varsalida .= "\nrequire_once \"../../modelos/";
                $varsalida .= str_replace("_", "", $dato).".php\";";
                $varsalida .= "\nrequire_once \"../../Coneccion.php\";";
                $varsalida .= "\nclass  C";
                $varsalida .= (str_replace("_", "", $dato)); 
                $varsalida .= " extends ";
                $varsalida .= str_replace("_", "", $dato);
                $varsalida .= "\n{";
                $varsalida .= "\n\t//Generacion de la funcion para guardar los datos\n";
                $varsalida .= "\n\tpublic function guardar(\$objeto)";
                $varsalida .= "\n\t{";
                $varsalida .= "\n\t\t\$con = BasedeDatos::coneccion();";  
                fputs($fp, $varsalida);
                $des="\n\t\t\$sql = \"INSERT INTO ";
                $des.= $dato;
                $des.= "(";    
                $sql_campos = "SHOW COLUMNS from $bdd.".$dato;
                $camposbd = BasedeDatos::consultaD($con,$sql_campos,2);                
                foreach ($camposbd as $datog) {                    
                        $des.= "\n\t\t\t";
                        $des.=$datog["Field"]." ,";                  
                   
                }
                $des.=")";
                $des.="\n\t\t\tVALUES (";
                foreach ($camposbd as $datog) {
                    
                    
                    $des .= "\n\t\t\t'\".parent::get";
                    $des .= ucfirst(str_replace("_", "", $datog['Field']));
                    $des .= "().\"',";

                };
                $des.=");\";";   
                fputs($fp,  str_replace(",)", ")", $des));                
                $varsalida = "\n\t\t return BasedeDatos::consultaA(\$con,\$sql);";
                $varsalida .= "\n\t}";
                $varsalida .= "\n\t//Generacion de la funcion para modificar los datos\n";
                $varsalida .= "\n\tpublic function modificar(\$objeto,\$id)";
                $varsalida .= "\n\t{";
                $varsalida .= "\n\t\t\$con = BasedeDatos::coneccion();";             
                fputs($fp, $varsalida);
                $des1 = "\n\t\t\$sql = \"UPDATE ";
                $des1 .= $dato;
                $des1 .= " SET";
                foreach ($camposbd as $datou) {
                    $des1 .= "\n\t\t\t";
                    $des1 .= $datou['Field']." = '";
                    $des1 .= "\". parent::get";
                    $des1 .= ucfirst(str_replace("_", "", $datou['Field']));
                    $des1 .= "(). \"' ,";    
                }
                $des1 .= "WHERE id=\". \$id. \";\";";   
                fputs($fp,  str_replace(",WHERE", "\n\t\t\tWHERE", $des1));
                
                $varsalida = "\n\t\t return BasedeDatos::consultaA(\$con,\$sql);";
                $varsalida .= "\n\t}";
                fputs($fp, $varsalida);
                $varsalida = "\n\t//Generacion de la funcion para borrar los datos\n";  
                $varsalida .= "\n\tpublic function borrar(\$id)";
                $varsalida .= "\n\t{";
                $varsalida .= "\n\t\t\$con = BasedeDatos::coneccion();";            
                $varsalida .= "\n\t\t\$sql = \"DELETE FROM ". $dato ." WHERE id=\". \$id .\";\";";    
                $varsalida .= "\n\t\t return BasedeDatos::consultaA(\$con,\$sql);";
                $varsalida .= "\n\t}";
                $varsalida .= "\n\t//Generacion de la funcion para Consultar los datos los datos\n";       
                $varsalida .= "\n\tpublic function consultar(\$tabla,\$campos = \"*\")";
                $varsalida .= "\n\t{";
                $varsalida .= "\n\t\t\$con = BasedeDatos::coneccion();";
                $varsalida .= "\n\t\tif(\$campos == \"*\"){";
                $varsalida .= "\n\t\t\t\$sql=\"SELECT * FROM \$tabla\";";			
		$varsalida .= "\n\t\t}else{";			
		$varsalida .= "\n\t\t\t\$sql = \"SELECT \".\$campos.\" FROM \$tabla\";";
		$varsalida .= "\n\t\t}";
		$varsalida .= "\n\t\t\$res = BasedeDatos::consultaD(\$con,\$sql,2);";		
		$varsalida .= "\n\treturn \$res;";                
                $varsalida .= "\n\t}";
                $varsalida .= "\n\tfunction consultarFull(\$tabla,\$campos = \"*\")";
                $varsalida .= "\n\t{";
                $varsalida .= "\n\t\t\$con = BasedeDatos::coneccion();";
                $varsalida .= "\n\t\t\$sql1=\"SELECT * FROM \$tabla\";";
                $varsalida .= "\n\t\t\$sql2 = \"SHOW COLUMNS FROM \".\$tabla;";
                $varsalida .= "\n\t\t\$data1 = BasedeDatos::consultaD(\$con, \$sql2);";
                $varsalida .= "\n\t\tforeach (\$data1 as \$registros1){";
                $varsalida .= "\n\t\t\tif(\$registros1['Key'] == \"MUL\"){";
                $varsalida .= "\n\t\t\t\t\$contador +=1;";
                $varsalida .= "\n\t\t\t}else{";
                $varsalida .= "\n\t\t\t\t\$contador =0;";
                $varsalida .= "\n\t\t\t}";
                $varsalida .= "\n\t\t}";
                $varsalida .= "\n\t\tif(\$contador != 0){";
                $varsalida .= "\n\t\t\tif(\$campos == \"*\"){";
                $varsalida .= "\n\t\t\t\t\$sql1=\"SELECT * FROM \$tabla\";";
                $varsalida .= "\n\t\t\t\t\$sql2 = \"SHOW COLUMNS FROM \".\$tabla;";
                $varsalida .= "\n\t\t\t\t\$data1 = BasedeDatos::consultaD(\$con, \$sql2);";
                $varsalida .= "\n\t\t\t\t\tforeach (\$data1 as \$registros1) {";
                $varsalida .= "\n\t\t\t\t\tif(\$registros1['Key'] == \"MUL\"){";
                $varsalida .= "\n\t\t\t\t\t\t\$sql3=\"SELECT table_name as tabla, referenced_table_name as rtabla, referenced_column_name as ref,column_name as col";
    		$varsalida .= "\n\t\t\t\t\t\t\tFROM information_schema.key_column_usage";
    		$varsalida .= "\n\t\t\t\t\t\t\tWHERE referenced_table_schema ='$bdd'";
       		$varsalida .= "\n\t\t\t\t\t\t\tAND referenced_table_name is not NULL AND table_name ='\$tabla' AND column_name ='\".\$registros1['Field'].\"';\";";         
                $varsalida .= "\n\t\t\t\t\t\t\$data2 = BasedeDatos::consultaD(\$con, \$sql3);";
                $varsalida .= "\n\t\t\t\t\t\tforeach (\$data2 as \$registros2) {";
                $varsalida .= "\n\t\t\t\t\t\t\t\$datatemp[]= \$registros2;";
                $varsalida .= "\n\t\t\t\t\t\t}";
                $varsalida .= "\n\t\t\t\t\t}";
                $varsalida .= "\n\t\t\t\t}";
                $varsalida .= "\n\t\t\t\t\$data2 = BasedeDatos::consultaD(\$con, \$sql1);";
                $varsalida .= "\n\t\t\t\tforeach (\$data2 as \$registros3) {";
                $varsalida .= "\n\t\t\t\t\t@\$datatemp2[]=\$registros3;";
                $varsalida .= "\n\t\t\t\t}";
                $varsalida .= "\n\t\t\t\tfor(\$i=0;\$i < count(@\$datatemp);\$i++){";
                $varsalida .= "\n\t\t\t\t\tfor(\$j=0;\$j < count(@\$datatemp2);\$j++){";
                $varsalida .= "\n\t\t\t\t\t\t\$sqlext = \"SELECT column_name";
                $varsalida .= "\n\t\t\t\t\t\t\tFROM information_schema.columns";
                $varsalida .= "\n\t\t\t\t\t\t\tWHERE table_name =  '\".\$datatemp[\$i]['rtabla'].\"'";
                $varsalida .= "\n\t\t\t\t\t\t\tAND ordinal_position =2;\";";
                $varsalida .= "\n\t\t\t\t\t\t\$campoext = BasedeDatos::consultaD(\$con, \$sqlext,3);";
                $varsalida .= "\n\t\t\t\t\t\t\$cam = \$campoext[0][0];";
                $varsalida .= "\n\t\t\t\t\t\t\$a= \"SELECT \".\$cam.\" FROM \".\$datatemp[\$i]['rtabla'].\"";
                $varsalida .= "\n\t\t\t\t\t\t\tWHERE id = \".@\$datatemp2[\$j][\$datatemp[\$i]['col']];";
                $varsalida .= "\n\t\t\t\t\t\t\$s = BasedeDatos::consultaD(\$con, \$a);";
                $varsalida .= "\n\t\t\t\t\t\tforeach (\$s as \$valuek) {";
                $varsalida .= "\n\t\t\t\t\t\tforeach (\$valuek as \$valuess) {";
                $varsalida .= "\n\t\t\t\t\t\t\t@\$datatemp3[\$j][\$datatemp[\$i]['col']]=\$valuess;";
                $varsalida .= "\n\t\t\t\t\t\t}";
                $varsalida .= "\n\t\t\t\t\t}";
                $varsalida .= "\n\t\t\t\t}";
                $varsalida .= "\n\t\t\t}";
                $varsalida .= "\n\t\t\tif(count(@\$datatemp3)== count(@\$datatemp2)){";
                $varsalida .= "\n\t\t\tfor(\$l=0;\$l < count(@\$datatemp3);\$l++){";
                $varsalida .= "\n\t\t\t\t\$resultado[\$l]=array_merge(@\$datatemp2[\$l],@\$datatemp3[\$l]);";
                $varsalida .= "\n\t\t\t}";
                $varsalida .= "\n\t\t}";
                $varsalida .= "\n\t\t\$res = @\$resultado;";
                $varsalida .= "\n\t\t}else{";
                $varsalida .= "\n\t\t\t\$sqla = \"SELECT \".\$campos.\" FROM \$tabla\";";
                $varsalida .= "\n\t\t\t\$res = BasedeDatos::consultaD(\$con,\$sqla,2);";
                $varsalida .= "\n\t\t}"; 
                $varsalida .= "\n\t\t} else {";
                $varsalida .= "\n\t\t\$sqla = \"SELECT \".\$campos.\" FROM \$tabla\";";
                $varsalida .= "\n\t\t\$res = BasedeDatos::consultaD(\$con,\$sqla,2);";
                $varsalida .= "\n\t\t}";
                $varsalida .= "\n\treturn \$res;";
                $varsalida .= "\n\t}";
                $varsalida .= "\n}";   
                $varsalida .= "\n?>";                
                
                
                
               fputs($fp, $varsalida);
            chmod("controladores/".str_replace("_", "", $dato).".php",0777);
                 
            }
        }
  
  


 $bandera=1;
 $con=NULL;
 return $bandera; 


}

}



