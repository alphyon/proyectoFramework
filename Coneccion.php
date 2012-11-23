<?php

/**
 *Clase para el manejo de las bases de datos
 * 
 *
 */
final class BasedeDatos 
{  
  private static $dns       = DNS;  
  private static $username  = USUARIO;  
  private static $password  = CLAVE;  
  private static $instance;  
      
  private function __construct() { }  
      
  /** 
   * Crea una instancia de la clase PDO 
   * @method coneccion a base de datos 
   * @access public static 
   * @return object de la clase PDO 
   */  
  public static function coneccion()  
  {  
    if (!isset(self::$instance))  
    {
        try {
                self::$instance = new PDO(self::$dns, self::$username, self::$password);  
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (Exception $exc) {
                echo $exc->getMessage();
        }

         
    }  
    return self::$instance;  
  }  
      
      
 /** 
  * Impide que la clase sea clonada 
  *  
  * @access public 
  * @return string trigger_error 
  */  
  private function __clone()  
  {  
    trigger_error('no se permite clonar la clase.', E_USER_ERROR);  
  }  
  
  /**
   * funcion para la ejecucion de las consultas a la base de
   * datos.
   * 
   * @author Jose Antonio Henriquez Chavarria  <jose.henriquez@itca.edu.sv>
   * @param objeto $coneccion objeto que conecta a la base de datos
   * @param string $sql sentencia SQL 
   * @return $msgok mensaje de la operacion realizada mas el numero de filas afectadas
   * @return $msgerror en caso de que ocurra un problema en la ejecucion de los datos
   *         
   */
  public  static function consultaA($coneccion, $sql)
    {
      $ejecutor = $coneccion;
      $msgok = NULL;
      $msgerror = NULL;
      try {
          $condicion = strstr(trim($sql)," ", TRUE);
          switch ($condicion) 
          {
              case "INSERT":
                  $msgerror = "No se ha Podido Insertar los Datos";
                  $msgok = "Datos Insertados";
                  
                  break;
              case "DELETE":
                  $msgerror = "No se ha Podido Eliminar los Datos";                  
                  $msgok = "Datos Eliminados";
                  break;
              case "UPDATE":
                  $msgerror = "No se ha Podido Actualizar los Datos";                  
                  $msgok = "Datos Actualizados";
                  break;
              default:
                  $msgerror = "Es posible que no use un estandar de consulta SQL";
                  break;
          }
          //$ejecutor = new PDO();
          $ejecutor->beginTransaction();
          $fila = $ejecutor->exec($sql);
          $ejecutor->commit();
          
          if($fila == 0){
              $msgok = $msgerror."<br> No existe coincidencia para realizar la accion sobre los registros ";
          }
          
          return $msgok. " Filas Afectadas ".$fila ;
          
          
      } catch (Exception $exc) {
          $ejecutor->rollBack();
          return $msgerror. "<br />".$exc->getMessage(); 
      }
    }
    /**
     * Devolucion de Regsitros
     *
     * @param  String $coneccion  Objeto de manejo de coneccion 
     * @param  String $sql Sentencia SQL 
     * @param  int $modo El tipo de salida de los registros segun PDO datos validos 1 y 2
     * por defecto usamos 2
     * @param boolean $presentacion define que devolvera el metodo
     * @return Array devuelve un array si parametro $presentacion es FALSE
     * @return tabla con los registros consultados si parametro $presentacion es TRUE
 */
   public static function consultaD($coneccion, $sql,$modo=2,$presentacion=FALSE)
    {
        $ejecutor = $coneccion;
        $manejador = trim($sql);
        $devolucion = "";
        //$ejecutor = new PDO($dsn, $username, $passwd, $options);
        try {       
            $datos = $ejecutor->query($manejador);
            $datos->setFetchMode($modo);
            if($presentacion == TRUE){
                $devolucion .="<table border=1>";
                foreach ($datos->fetchAll() as $registros) {
                    $devolucion.="<tr>";
                    foreach ($registros as $valor) {
                        $devolucion.="<td>".$valor."</td>";
                    }
                    $devolucion.="</tr>";
                }
                $devolucion .="</table>";
            }else{
                $contenedor = $datos->fetchAll(); 
                $devolucion=$contenedor;
                
                
            }
       
        } catch (Exception $exc) {
            return  "No se pudieron Consultar los Datos<br />".$exc->getMessage();  
        }        
        return $devolucion;
        
    }
  
   
}  
?>


