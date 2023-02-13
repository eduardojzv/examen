<?php
    $proceso = false;
    if(isset($_POST["oc_Control"])){
        $archivo = $_FILES["txtArchi"];
        $info=[
            "nombre"=>"name",
            "tipo"=>"type",
            "tamaño"=>"size"
        ];
        //procesa los datos generales del archivo recibido.
         $separador=$_POST["separador"];
         $encabezados=["id","genero","C","edad","grado","ingresos","provincia",
         "partido","orientacion","estado marital"];
         $verificar=$_POST["info"];

        //valida que
        if($archivo["size"][0] > 0 && $archivo["size"][1]>0){
            $vectorData=[];
            $cont=0;
            $observaciones=0;
            $observaciones2=0;
            $mayor=0;
            $menor=0;
            $hombres=0;
            $mujeres=0;
            $archivo01=false;
            foreach($archivo["tmp_name"] as $dato){
                // //procesa el contenido del archivo recibido.
                $archi = fopen($dato, "rb");
                $contenido = [];
                // $contenido[0]=["1","M","C","62","Universitaria Incompleta","1710000","Puntarenas",
                // "Partido Integración Nacional","Graysexual","S"];
                $posi = 0;
                $cont==0 ?$archivo01=true: null ;
                while($linea = fgets($archi)){
                    //oberservaciones contador
                    $cont==0 ? $observaciones++ : $observaciones2++;
                    //generos contador
                    //
                    $contenido[$posi] = explode($separador,$linea);
                    //menor y mayor
                    $var=$contenido[$posi][3];

                    if($menor==0){
                        $menor=$var;
                    }

                    if($var>$mayor){
                        $mayor=$var;
                    }elseif($var<$menor){
                        $menor=$var;
                    }
                    //hombres mujeres
                    if($archivo01==true){
                        $contenido[$posi][1]=="M"? $hombres++ : $mujeres++;
                    }
                    $posi++;
                }
                $cont++;
                array_push($vectorData,$contenido);
                $contenido="";
    
                //cierra el archivo.
                fclose($archi);
            }

            //cambia el estado del proceso.
            $proceso = true;
        }
    }
    function tipo($dato){
        $i=explode('/',$dato);
        if(is_numeric($dato)){
            return "int";
        }
        if(strlen($dato)==1){
             return "char";
        }
        if(count($i)>2){
                return "date";
        }else{
            return "string";
        }
    }
?>
<!--  -->
<!DOCTYPE html>
<html lang="en">
<head>	
    <?php
        include_once("segmentos/encabe.inc");        
	?>
    
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
    <script
  src="https://code.jquery.com/jquery-3.6.3.js"
  integrity="sha256-nQLuAZGRRcILA+6dMBOvcRh5Pe310sBpanc6+QBmyVM="
  crossorigin="anonymous"></script>
    <script type="text/javascript">
        var datos = $.ajax({
            url:'datos.php',
            type:'post',
            data:{
                separador:"&"
            },
            async:false
        }).responseText;
        datos=JSON.parse(datos)
        //parsear vectores
        var datos1=JSON.parse(datos[0])
        var datos2=JSON.parse(datos[1])
        
        google.load("visualization", "1", {packages:["corechart"]});

        google.setOnLoadCallback(creaGrafico);

        function creaGrafico() {
            var data01 = google.visualization.arrayToDataTable(datos1);
            var data02 = google.visualization.arrayToDataTable(datos2);
        
            var opciones = {
                title: 'Cantidad de hombres en cada provincia',
                hAxis: {title: 'Generos', titleTextStyle: {color: 'green'}},
                vAxis: {title: 'Encuestados', titleTextStyle: {color: '#FF0000'}},
                backgroundColor:'#ffffcc',
                legend:{position: 'bottom', textStyle: {color: 'blue', fontSize: 13}},
                width:700,
                height:500
            };

            var grafico = new google.visualization.PieChart(document.getElementById('grafica'));
            grafico.draw(data01, opciones);
            //
            var grafico = new google.visualization.PieChart(document.getElementById('grafica2'));
            grafico.draw(data02, opciones)
}
    </script>   
</head>
<body class="container">
	<header class="row">
		<?php
			include_once("segmentos/menu.inc");
		?>
	</header>
    <main class="row">
		<div class="linea_sep">
            <h3>Procesando archivo.</h3>
            <br>
            <?php
                if(!$proceso){
                    //En caso que el archivo .csv no pudiese ser procesado
                    echo '<div class="alert alert-danger" role="alert">';
                    echo '  El archivo no puede ser procesado, verifique sus datos.....!';
                    echo '</div>';
                }else{
                    //En caso que el archivo .csv pudiese ser procesado
                    $armar='<h4>Datos Generales.</h4>
                    <table class="table table-bordered table-hover">
                    <tr> 
                        <td>Archivo</td>
                        <td>Tipo</td>
                        <td>Peso</td>
                        <td>Observaciones</td>
                    </tr>
                    ';
                    for ($x = 0; $x <2; $x++) {
                        $armar.='<tr>';
                        foreach($info as $key=>$val){
                            if($key=="tamaño"){
                                $armar.='<td>'.number_format((($archivo[$val][$x])/1024)/1024,2).'MBs</td>';
                            }else{
                                $armar.='<td>'.$archivo[$val][$x].'</td>';
                            }
                        }
                        $x==0 ? $armar.='<td>'.$observaciones.'</td></tr>' : $armar.='<td>'.$observaciones2.'</td></tr>';
                      } 
                        $armar.=' </table>';

                    $armar.='<table class="table table-bordered table-hover">
                            <tr>
                                <td>Campo</td>
                                <td>tipo</td>
                                <td>Uso</td>
                                <td>Valor</td>
                                <td>Ejemplo</td>
                            </tr>';
                            $fila=$vectorData[0][0];
                            $cont=0;
                            foreach($fila as $i){
                                tipo($i)=="int" ? $uso="Cuantitativo" : $uso="Cualitativo";
                                $encabezado=$encabezados[$cont];
                                 if($encabezado=="edad"){
                                    $valor= 'De '.$menor.' a '.$mayor.' años';
                                 }elseif($encabezado=="genero"){
                                    $valor='Mujeres: '.$mujeres.' Hombres: '.$hombres;
                                 }else{
                                    $valor="variable";
                                 }

                                $armar.='<tr> 
                                <td>'.$encabezados[$cont].'</td>
                                <td>'.tipo($i).'</td> 
                                <td>'.$uso.'</td>
                                <td>'.$valor.'</td>
                                <td>'.$i.'</td>
                                </tr>';
                                $cont++;
                            }
                        $armar.='</table>';
                        echo($armar);
                }//fin del else (solo si el archivo fue procesado)
            ?>
            </table>
            <div style="display: flex;align-items: center;justify-content: center;">
                <div id="grafica"></div>
                <!--  -->
                <div id="grafica2" style="margin-left: 5px;"></div>
            </div>
		</div>
	</main>

	<footer class="row pie">
		<?php
			include_once("segmentos/pie.inc");
		?>
	</footer>

	<!-- jQuery necesario para los efectos de bootstrap -->
    <script src="formatos/bootstrap/js/jquery-1.11.3.min.js"></script>
    <script src="formatos/bootstrap/js/bootstrap.js"></script>
</body>
</html>
