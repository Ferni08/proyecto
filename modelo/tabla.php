<?php
include('conexion.php');
$postdata = file_get_contents("php://input");//permite leer los datos de un post del json data
$res = json_decode($postdata);//decodificamos los datos recibidos del Json
$result = 0;


// Consula BD
$sql= "SELECT  TRA.no_cdc, CASE TRA.status
      WHEN 'ACC' THEN 'ACCIDENTADO'
      WHEN 'A' THEN 'ACTIVO'
      WHEN 'T' THEN 'EN TALLER'
      WHEN 'N' THEN 'NUEVO'
      WHEN 'NUEVO' THEN 'NUEVO'
      WHEN 'P' THEN 'PARA VENTA'
      WHEN 'PA' THEN 'PATIO'
      WHEN 'S' THEN 'SIN OPERADOR'
      WHEN 'SD' THEN 'SERVICIO DEDICADO'
      WHEN 'APV' THEN 'APARTADO PARAVENTA'
      WHEN 'IN' THEN 'INACTIVO'
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
     (current - TO_DATE(TRA.fultpos_a || ' ' || TRA.hultpos_a,'%m/%d/%Y %H:%M'))Sin_mov,
     (current - TO_DATE(TRA.fultpos || ' ' || TRA.hultpos,'%m/%d/%Y %H:%M'))sin_Posicion,
      PAR.partida, TRA.s_fultpos, TRA.s_hultpos, TRA.s_pinteres, TRA.s_status, TRA.s_latitud, TRA.s_longitud,
      TRA.s_ignition, TRA.s_reason,
     (current - TO_DATE(TRA.fultpos_a || ' ' || TRA.hultpos_a,'%m/%d/%Y %H:%M'))Sin_mov,
     (current - TO_DATE(TRA.s_fultpos || ' ' || TRA.s_hultpos,'%m/%d/%Y %H:%M'))sin_Posicion_shield,
      TRA.ignicion, TRA.s_status, OPE.ref_e1, TRA.division,
      TO_DATE(PED.fecha_estimada || ' ' || PED.hora_e,'%m/%d/%Y %H:%M')- TO_DATE(PED.fecha_descarga || ' ' ||PED.hora_d,'%m/%d/%Y %H:%M') tarde,
     (current - TO_DATE(PED.fecha_descarga || ' ' || PED.hora_d,'%m/%d/%Y %H:%M'))dias_destino,
      COT.destino[9,10] edo, GEO.id,
      CASE GEO.color WHEN 'R' THEN 'SALIO' WHEN 'A' THEN 'ENTRO' WHEN 'B' THEN 'ATENDIDA'  END evento,
      GEO.color, TRA.enpatio, TRA.alerta,
      CASE TRA.enpatio
        WHEN '10' THEN 'NVO. LAREDO'
        WHEN  '1' THEN 'AGS PATIO 1'
        WHEN  '2' THEN 'AGS PATIO 2'
        WHEN  '4' THEN 'AGS PATIO 3'
        WHEN  '3' THEN 'REYNOSA'  END patio, PED.id_ruta,
      CAJ.set_point, CAJ.supply, CAJ.retorno, (PED.temp - CAJ.set_point)dif_temp, RUT.horas hrs_ruta, TRA.cia,
      (current + ((TRA.horas)UNITS HOUR)) eta_pos
   FROM mdtractor TRA, OUTER(mdpedido PED,  mvcotdet2 COT, lqrutas2 RUT,
        mdcliente CLI, mdgrupos GRU, mdzonas ZON, OUTER(mdcajas CAJ), OUTER( mddemoras DEM),
        mdoper OPE, mdciudades CIU, mdestados EDO), OUTER(mdalertasgeocercas GEO),
        OUTER (mdviajepar PAR)
   WHERE TRA.cia = '01'
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
     and GEO.economico = TRA.no_cdc+0
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


$result = array();
$aux= array();
    try{
      $conexion = conexion();
      foreach($conexion->query($sql) as $fila) {

      $aux['no_cdc'] = utf8_encode($fila['NO_CDC']);
      $aux['circu_serv'] = utf8_encode($fila['CIRCU_SERV']);
      $aux['dist'] = utf8_encode($fila['DIST']);
      $aux['horas'] = utf8_encode($fila['HORAS']);
      $aux['eta_pos'] = utf8_encode($fila['ETA_POS']);
      $aux['pinteres'] = utf8_encode($fila['PINTERES']);
      $aux['destino'] = utf8_encode($fila['DESTINO']);
      $aux['fecha_carga'] = utf8_encode($fila['FECHA_CARGA']);
      $aux['hora_c'] = utf8_encode($fila['HORA_C']);
      $aux['fecha_descarga'] = utf8_encode($fila['FECHA_DESCARGA']);
      $aux['hora_d'] = utf8_encode($fila['HORA_D']);
      $aux['no_pedido'] = utf8_encode($fila['NO_PEDIDO']);
      $aux['no_oper'] = utf8_encode($fila['NO_OPER']);
      $aux['no_caja'] = utf8_encode($fila['NO_CAJA']);
      $aux['temp'] = utf8_encode($fila['TEMP']);
      $aux['st_unidad'] = utf8_encode($fila['ST_UNIDAD']);
      $aux['cve_ciud'] = utf8_encode($fila['CVE_CIUD']);
      $aux['cve_edo'] = utf8_encode($fila['CVE_EDO']);
      $aux['set_point'] = utf8_encode($fila['SET_POINT']);
      $aux['origen'] = utf8_encode($fila['ORIGEN']);
      $aux['supply'] = utf8_encode($fila['SUPPLY']);
      $aux['retorno'] = utf8_encode($fila['RETORNO']);
      $aux['nickname'] = utf8_encode($fila['NICKNAME']);
      $aux['zona'] = utf8_encode($fila['ZONA']);
      $aux['grupo'] = utf8_encode($fila['GRUPO']);
      $aux['edo'] = utf8_encode($fila['EDO']);
      $aux['dias_destino'] = utf8_encode($fila['DIAS_DESTINO']);
      $aux['fecha_estimada'] = utf8_encode($fila['FECHA_ESTIMADA']);
      $aux['hora_e'] = utf8_encode($fila['HORA_E']);
      $aux['hrs_ruta'] = utf8_encode($fila['HRS_RUTA']);
      $aux['partida'] = utf8_encode($fila['PARTIDA']);
      $aux['patio'] = utf8_encode($fila['PATIO']);
      $aux['alerta'] = utf8_encode($fila['ALERTA']);
      $aux['evento'] = utf8_encode($fila['EVENTO']);
      $aux['sin_posicion_shield'] = utf8_encode($fila['SIN_POSICION_SHIELD']);
      $aux['s_ignition'] = utf8_encode($fila['S_IGNITION']);
      $aux['s_status'] = utf8_encode($fila['S_STATUS']);
      $aux['s_fultpos'] = utf8_encode($fila['S_FULTPOS']);
      $aux['s_hultpos'] = utf8_encode($fila['S_HULTPOS']);
      $aux['s_pinteres'] = utf8_encode($fila['S_PINTERES']);
      $aux['sin_mov'] = utf8_encode($fila['SIN_MOV']);
      $aux['sin_posicion'] = utf8_encode($fila['SIN_POSICION']);
      $aux['ignicion'] = utf8_encode($fila['IGNICION']);
      $aux['fultpos'] = utf8_encode($fila['FULTPOS']);
      $aux['hultpos'] = utf8_encode($fila['HULTPOS']);
      $aux['hultpos_caja'] = utf8_encode($fila['HULTPOS_CAJA']);
      $aux['fultpos_caja'] = utf8_encode($fila['FULTPOS_CAJA']);
      $aux['pinteres_caja'] = utf8_encode($fila['PINTERES_CAJA']);


      array_push($result, $aux);
      }
      if(empty($result)){
        echo "0";
      } else{
        print  json_encode($result);

      }
    }catch (PDOException $error){ //imprimir el error para ver que es lo que esta pasando
      echo "Conexion Incorrecta" . $error->getMessage()."<br/>";
      die();
      }
?>
