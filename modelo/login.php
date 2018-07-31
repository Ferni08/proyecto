<?php
include('conexion.php');
$postdata = file_get_contents("php://input");//permite leer los datos de un post del json data
$res = json_decode($postdata);//decodificamos los datos recibidos del Json

$usuario = $res->user;
$nip = $res->psw;
$nip = md5($nip);
$nip = strtoupper($nip);
$result = 0;

//consulta BD
$sql ="SELECT usuario, contra
FROM mdusr
WHERE usuario = '$usuario'
and contra = '$nip'
AND status='AC'";


try{
  $conexion = conexion();
  foreach ($conexion->query($sql) as $fila) {
      $result = json_encode($fila);
  }
  echo $result;
}catch (PDOException $error){ //imprimir el error para ver que es lo que esta pasando
  echo "Conexion Incorrecta" . $error->getMessage()."<br/>";
  die();
}
?>
