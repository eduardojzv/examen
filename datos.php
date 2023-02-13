<?php

//0=provincia 1=sanjose 2=alajuela 3=cartagp 4=heredia 5=guana 6=puerto 7=limon
function filtrarData($csv){
  $generos=["Provincia","Hombres","Mujeres"];
  $provincias=[
      ["San José",0,0],
      ["Alajuela",0,0],
      ["Cartago",0,0],
      ["Heredia",0,0],
      ["Guanacaste",0,0],
      ["Puntarenas",0,0],
      ["Limón",0,0]
  ];
$archivo= file_get_contents("./datos/".$csv);
$data = explode("\n", $archivo);
$lineas= [];
$datos="";
//variables para la grafica
$grados=[];
$hombres=0;
$mujeres=0;
//archivo 1
foreach ($data as $i) {
  $datos=explode("&", $i);
  //eliminar posiciones que no ocupamos para la grafica
  unset($datos[0]);
  unset($datos[2]);
  unset($datos[5]);
  unset($datos[7]);
  unset($datos[8]);
  unset($datos[9]);
  //
  if(!empty($datos)){
    array_push($lineas,$datos);
    in_array($datos[4],$grados)==false ? array_push($grados,$datos[4]): null ;
    //
    $datos[1]=="M" ? $hombres++ : $mujeres++;
    //
  }
}
//
foreach ($lineas as $j) {
    if(isset($j[6])){
        for($i=0;$i<count($provincias);$i++){
            $aa=$provincias[$i][0];
            if($provincias[$i][0]==$j[6]){
                $j[1]=="M"?$provincias[$i][1]++:$provincias[$i][2]++;
                continue;
            }
        }
    }
}
$allData[]=$generos;
foreach($provincias as $data){
    $allData[]=$data;
}
//agregar a la ultima linea
return $allData;
}
//fin funcion filtrarData()
$dataArchivo1=[];
$dataArchivo2=[];
//introducir datos del archivo 1 y 2
//archivo 1
$csv="CensoNacional_Entrenamiento.csv";
$dataArchivo1=filtrarData($csv);
//archivo 2
$csv="CensoNacional_Prueba.csv";
$dataArchivo2=filtrarData($csv);
//
$allData[]=json_encode($dataArchivo1);
$allData[]=json_encode($dataArchivo2);
echo json_encode($allData);
?>