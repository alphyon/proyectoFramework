
<?php

/**
 * José Antonio Henríquez Chavarría
 *
 * clase helper de controles para formularios
 * generar controles para acceso y captura de datos
 *
 * Clase que se usa para generar los controles de acceso y captura de datos
 * se especifican la mayoria
 *
 * @author José Antonio Henríqurez Chavarría
 * @author twitter:@alphyon
 * @author facebook:http://facebook.com/alphyon21
 *
 * @package base_zamara
 */ 
class controles {
    private $textoC;
    private $claveC;
    private $radioC;
    private $areaC;
    private $listaC;
    private $listaD;
    private $fecha;
    private $tabla;
    
    //se crea un control dinamico del tipo caja de texto
    public function texto($nombre,$value="",$required="required",$read=0,$tamaño=25){
        if($read != 0){
            $lec="readonly";
        }else{
            $lec="";
        }
        
        $this->textoC = "<input ".$lec." type='text' name='".$nombre."' id='".$nombre."' value='".$value."' class='".$required."' size='".$tamaño."' />";
        
        return $this->textoC;
    }
    //se crea un control dinamico del tipo password
    public function clave($nombre,$value="", $tamaño=25, $required="required"){
        $this->claveC = "<input type='password' name='".$nombre."' id='".$nombre."' value='".$value."' class='".$required."' size='".$tamaño."' />";
        
        return $this->claveC;
    } 
    // se crea un control del tipo textarea
    public function texarea($nombre,$value="",$required="required"){
        $this->areaC = "<textarea name='".$nombre."' id='".$nombre."'  class='".$required."'>".$value." </textarea>";
        
        return $this->areaC;
    }    
    // se crea un control del tipo radio
    public function radio($nombre,$valor,$activo=0,$required="required"){
        
            if($activo==1){
                $ele="checked";
            }  else {
                $ele="";
            }
        
        $this->radioC = "<input type='radio' name='".$nombre."' id='".$nombre."' class='".$required."' value='".$valor."' ".$ele." />";
        
        return $this->radioC;
    }   
    // se crea un control del tipo lista se pasa como parametro un arreglo para el llenado de datos.
    public function lista($nombre, $array,$required="required"){
        $this->listaC = "<select name='".$nombre."' id='".$nombre."' class='".$required."'>";
        $this->listaC.="<option value=''>Elija un dato</option>";
        foreach ($array as $value) {
            $this->listaC .="<option value='".$value."'>".$value."</option>";
        }
        $this->listaC .="</select>";
        
        return $this->listaC;
    }    
    // se crea un control del tipo lista cargado con los datos que estan en una tabla de x base de datos
    public function listaDatos($nombre,$modelo,$valor="",$campos="",$required="required"){
        $con= BasedeDatos::coneccion();
        $this->listaD =  "<select name='$nombre'>";
        if($campos == ""){
            $sqlext = "SELECT column_name							FROM information_schema.columns
                    WHERE table_name =  '".$modelo."'
                    AND ordinal_position =2;";
            $campoext = BasedeDatos::consultaD($con, $sqlext,3);
            $cam = $campoext[0][0];
            $datosinternos="id,$cam";            
            $arrdato= explode(",",$datosinternos);
            
        }else{
            $datosinternos = "id,".$campos;
            $arrdato= explode(",",$datosinternos);
        }
        $itemdat=count($arrdato);
        
        if ($valor == "") {
           $v="";
           $valoru="elija un dato";
           $sql ="SELECT id,".$datosinternos." FROM ".$modelo.";";
        }  else {
            $vart="SELECT ".$datosinternos." FROM ".$modelo." WHERE id = '".$valor."';";
            $res=  BasedeDatos::consultaD($con,$vart);
            foreach ($res as $dat){
                $v=$dat['id'];
                $valoru="";
                for($i=1;$i<$itemdat;$i++){
                $valoru .= $dat[$arrdato[$i]].", ";
            }
            }
            
            $sql="SELECT ".$datosinternos." FROM ".$modelo." WHERE NOT id=".$valor;
        }
        $this->listaD .=  "<option value=".$v.">".$valoru."</option>";
        $salida = BasedeDatos::consultaD($con, $sql,2);
        foreach ($salida as $registro=>$dato) {
            $this->listaD .=  "<option value=\"".$dato[$arrdato[0]]."\">";
            for($i=1;$i<$itemdat;$i++){
            $this->listaD .=  $dato[$arrdato[$i]]." ";
            }
            $this->listaD .=  "</option>";
            //$this->listaD =  "<option>".$dato["id"].",".$dato["nombre"]."</option>";
        }
        
        $this->listaD .=  "</select>";
       
        return $this->listaD;
        
    } 
    //crea un control del tipo calendario 
    public function fecha($nombre,$id,$dfecha=""){
        if($dfecha==""){
            $val=date('Y-m-d');
        }else{
            $val=$dfecha;
        }
        
        $this->fecha="
       <script> 
       $(document).ready(
            function(){
                \$(\"#".$id."\").datepicker(    
                    {
                        changeMonth: true,
                        changeYear: true,
                        dateFormat: 'yy-mm-dd',
                        showAnim:'slide'
                    }
                    
                 );
            }
           
       )
       </script>
         
           <input type=text id=\"".$id."\" name='".$nombre."' value=\"".$val."\">";              
        return $this->fecha;        
    } 
    
    //crea un tabla de datos estatica 
   public function imprimetabla($objeto,$estilo="table",$editar=0) {
      if($objeto != NULL){
          if(is_array($objeto)){
          $this->tabla =  "<table class=\"$estilo\">";
          $this->tabla .=  "<tr class='info'>";
          foreach (array_keys($objeto[0]) as $value) {
              $this->tabla .=  "<td>" ;
              $this->tabla .=  $value;
              $this->tabla .=  "</td>" ;
          }
          if($editar!="0"){
            $this->tabla.= "<td>Modificar</td><td>Eliminar</td>"; 
          }
          $this->tabla .=  "</tr><tr>";
          foreach ($objeto as $datos) {
             
              foreach ($datos as $registro) {                 
                 $this->tabla .=  "<td>"; 
                 $this->tabla .=  $registro;
                 $this->tabla .=  "</td>";
              }
              if($editar!="0"){
                 $this->tabla .=  "<td><a href=formulario.php?id=".$datos['id']."&formu=Mod>Modificar</a></td>";
                 $this->tabla .=  "<td><a href=acciones.php?id=".$datos['id']."&bot=Borrar>Eliminar</a></td>";  
              }
               
               $this->tabla .=  "</tr>";
          }
           $this->tabla .=  "</table>";
      }else{
          $this->tabla = "Debe de pasarse un arreglo como parametro";
      } 
    }else{
        $this->tabla = "No hay registro para mostrar ";
    }
       
      
       
       
       
  
return $this->tabla;
   
       
   }
    
    
    
    
}

?>
