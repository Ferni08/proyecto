<?php
include('conexion.php');
$postdata = file_get_contents("php://input");//permite leer los datos de un post del json data
$res = json_decode($postdata);//decodificamos los datos recibidos del Json

// $usuario = $res->user;
// $nip = $res->psw;
// $nip = md5($nip);
// $nip = strtoupper($nip);
$result = 0;

//Consula BD
$sql="SELECT  TRA.no_cdc, CASE TRA.status
   WHEN 'ACC'      THEN 'ACCIDENTADO'
   WHEN 'A'        THEN 'ACTIVO'
   WHEN 'T'        THEN 'EN TALLER'
   WHEN 'N'        THEN 'NUEVO'
   WHEN 'NUEVO'    THEN 'NUEVO'
   WHEN 'P'        THEN 'PARA VENTA'
   WHEN 'PA'       THEN 'PATIO'
   WHEN 'S'        THEN 'SIN OPERADOR'
   WHEN 'SD'       THEN 'SERVICIO DEDICADO'
   WHEN 'APV'      THEN 'APARTADO PARAVENTA'
   WHEN 'IN'       THEN 'INACTIVO'
   WHEN 'INACTIVO' THEN 'INACTIVO' END st_unidad,
   TRA.status, PED.no_oper, CIU.cve_ciud, CIU.cve_edo,
   PED.no_caja, PED.id_lintrans, PED.no_pedido,
   PED.Circu_serv, PED.fecha_descarga, PED.hora_d, PED.temp,
   TRA.pinteres,  CLI.nickname, ZON.nombre zona, GRU.nombre grupo,
   COT.origen, COT.destino, PED.parinter,
   PED.fecha_estimada, PED.hora_e, TRA.fultpos, TRA.hultpos, TRA.dist,
   TRA.horas, PED.fecha_carga, PED.hora_c, TRA.latitud, TRA.longitud,
   DEM.tipo_demora, DEM.lugar_demora, TRA.asignado, CAJ.pinteres pinteres_caja,
   CAJ.latitud latitud_caja, CAJ.longitud longitud_caja, TRA.grppre,
   CAJ.fultpos fultpos_caja, CAJ.hultpos hultpos_caja,
  (current - TO_DATE(TRA.fultpos_a || ' '  || TRA.hultpos_a,'%m/%d/%Y %H:%M'))Sin_mov,
  (current - TO_DATE(TRA.fultpos || ' ' || TRA.hultpos,'%m/%d/%Y %H:%M'))sin_Posicion,
   PAR.partida, TRA.s_fultpos, TRA.s_hultpos, TRA.s_pinteres, TRA.s_status, TRA.s_latitud, TRA.s_longitud,
   TRA.s_ignition, TRA.s_reason,
  (current - TO_DATE(TRA.fultpos_a || '' || TRA.hultpos_a,'%m/%d/%Y %H:%M'))Sin_mov,
  (current - TO_DATE(TRA.s_fultpos || '' || TRA.s_hultpos,'%m/%d/%Y %H:%M'))sin_Posicion_shield,
   TRA.ignicion, TRA.s_status, OPE.ref_e1, TRA.division,
   TO_DATE(PED.fecha_estimada || '' || PED.hora_e,'%m/%d/%Y %H:%M')- TO_DATE(PED.fecha_descarga || '' || PED.hora_d,'%m/%d/%Y %H:%M') tarde,
  (current - TO_DATE(PED.fecha_descarga || '' || PED.hora_d,'%m/%d/%Y %H:%M'))dias_destino,
   COT.destino[9,10] edo, GEO.id,
   CASE GEO.color WHEN 'R' THEN 'SALIO' WHEN 'A' THEN 'ENTRO' WHEN 'B' THEN 'ATENDIDA'  END evento,
   GEO.color, TRA.enpatio, TRA.alerta,
   CASE TRA.enpatio
     WHEN '10' THEN 'NVO. LAREDO'
     WHEN  '1' THEN 'AGS PATIO 1'
     WHEN  '2' THEN 'AGS PATIO 2'
     WHEN  '4' THEN 'AGS PATIO 3'
     WHEN  '3' THEN 'REYNOSA'  END patio, PED.id_ruta,
   CAJ.set_point, CAJ.supply, CAJ.retorno, (PED.temp - CAJ.set_point)dif_temp, RUT.horas hrs_ruta, TRA.cia

FROM mdtractor TRA, OUTER(mdpedido PED,  mvcotdet2 COT, lqrutas2 RUT,
     mdcliente CLI, mdgrupos GRU, mdzonas ZON, OUTER(mdcajas CAJ), OUTER( mddemoras DEM),
     mdoper OPE, mdciudades CIU, mdestados EDO), OUTER(mdalertasgeocercas GEO),
     OUTER (mdviajepar PAR)
WHERE TRA.cia = 'FrmPrincipal.LBCIA'
  and TRA.status not in('V',  'SEC')
  and TRA.modelo <> 'COMODIN'
  and PED.cve_cia = TRA.cia
  and PED.no_pedido = TRA.no_viaje
  and COT.id_serial = PED.id_cotiza
  and DEM.no_viaje = PED.no_pedido
  and CLI.cve_cia = PED.cve_cia
  and CLI.id = PED.id_cliente
  and GRU.id = PED.grupo_tra
  and ZON.id = PED.zona
  and CAJ.cve_cia = PED.cve_cia
  and CAJ.no_caja = PED.no_caja
  and CAJ.lin_tra = PED.id_lintrans
  and PAR.no_viaje = TRA.no_viaje
  and PAR.tracto = TRA.no_cdc
  and PAR.status  = 'AC'
  and PAR.origen <> PAR.destino
  and OPE.cve_cia = PED.cve_cia
  and OPE.driverno = PED.no_oper
  and GEO.economico = TRA.no_cdc
  and GEO.idTransaccion = TRA.id_alertageo
  and GEO.evento = 'Saliendo'
  and GEO.status = 1
  and CIU.cve_cia = TRA.cia
  and CIU.ciudad = OPE.ciudad
  and CIU.status = 'A'
  and EDO.cve_cia = TRA.cia
  and EDO.estado = OPE.edo
  and EDO.cve_edo = CIU.cve_edo
  and RUT.cve_cia = TRA.cia
  and RUT.iden = PED.id_ruta
 ORDER BY enpatio ASC, grppre DESC, sin_posicion_shield DESC, sin_posicion DESC";

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
