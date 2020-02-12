<?php

#Se inicia la sesión
if (strlen(session_id() < 1)) session_start();

#Se incluye el autoload
require_once '../modelos/Lapso.php';

#Se instancia el objeto de Sección
$Lapso = new Lapso();

$idlapso = isset($_POST['idlapso']) ? limpiarCadena($_POST['idlapso']) : '';
$lapso = isset($_POST['lapso']) ? limpiarCadena(mb_strtoupper($_POST['lapso'])) : '';
$estatus = isset($_POST['estatus']) ? limpiarCadena($_POST['estatus']) : '';

#Se ejecuta un caso dependiendo del valor del parámetro GET
switch ($_GET['op']) {

    case 'comprobarlapso':

        $rspta = $Lapso->comprobarlapso($lapso);
        echo json_encode($rspta->fetch_object());
        break;

    case 'guardaryeditar':

        #Se deshabilita el guardado automático de la base de datos
        autocommit(FALSE);

        #Variable para comprobar que todo salió bien al final
        $sw = TRUE;

        #Si la variable esta vacía quiere decir que es un nuevo registro
        if (empty($idlapso)) {

            #Se registra el lapso
            $Lapso->insertar($lapso, $estatus) or $sw = FALSE;

            #Se verifica que todo saliío bien y se guardan los datos o se eliminan todos
            if ($sw) {
                commit();
                echo 'true';
            } else {
                rollback();
                echo 'false';
            }
        } else {

            #Se edita el lapso
            $Lapso->editar($idlapso, $lapso, $estatus) or $sw = FALSE;

            #Se verifica que todo saliío bien y se guardan los datos o se eliminan todos
            if ($sw) {
                commit();
                echo 'update';
            } else {
                rollback();
                echo 'false';
            }
        }
        break;

    case 'listar':

        $rspta = $Lapso->listar();

        if ($rspta->num_rows != 0) {

            while ($reg = $rspta->fetch_object()) {

                $data[] = array(
                    '0' => ($reg->estatus) ? '<button class="btn btn-outline-primary " title="Editar" onclick="mostrar(' . $reg->id . ')" data-toggle="modal" data-target="#lapsoModal"><i class="fas fa-edit"></i></button>' .

                        ' <button class="btn btn-outline-danger" title="Desactivar" onclick="desactivar(' . $reg->id . ')"> <i class="fas fa-times"> </i></button> '

                        : '<button class="btn btn-outline-primary" title="Editar" onclick="mostrar(' . $reg->id . ')" data-toggle="modal" data-target="#lapsoModal"><i class="fa fa-edit"></i></button>' .

                        ' <button class="btn btn-outline-success" title="Activar" onclick="activar(' . $reg->id . ')"><i class="fa fa-check"></i></button> ',

                    '1' => $reg->lapso.'º lapso'
                );
            }

            $results = array(
                "draw" => 0, #Esto tiene que ver con el procesamiento del lado del servidor
                "recordsTotal" => count($data), #Se envía el total de registros al datatable
                "recordsFiltered" => count($data), #Se envía el total de registros a visualizar
                "data" => $data #datos en un array

            );
        } else {
            $results = array(
                "draw" => 0, #Esto tiene que ver con el procesamiento del lado del servidor
                "recordsTotal" => 0, #Se envía el total de registros al datatable
                "recordsFiltered" => 0, #Se envía el total de registros a visualizar
                "data" => '' #datos en un array
            );
        }

        echo json_encode($results);

        break;

    case 'mostrar':

        $rspta = $Lapso->mostrar($idlapso);

        #Se codifica el resultado utilizando Json
        echo json_encode($rspta);

        break;

    case 'desactivar':

        $rspta = $Lapso->desactivar($idlapso);
        echo $rspta ? 'true' : 'false';
        break;

    case 'activar':

        $rspta = $Lapso->activar($idlapso);
        echo $rspta ? 'true' : 'false';
        break;
}
