<?php
function conexion(){
  $dbhost     = "frioexp";
  $dbservice  = "1526";
  $dbname     = "bdfrio";
  $dbserver   = "frio";
  $dbprotocol = "onsoctcp";
  $dbuser     = "sfrio2";
  $dbpass     = "frio93";

  $conexion = new PDO("informix:host=$dbhost; service=$dbservice;database= $dbname; server=$dbserver; protocol=onsoctcp;EnableScrollableCursors=1", $dbuser, $dbpass);
  $conexion->exec("set names utf8"); //soporte a caracteres especiales
  $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //mensaje de error
  return $conexion;
}
?>
