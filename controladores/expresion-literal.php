<?php 

#Se inicia la sesión
if (strlen(session_id() < 1)) session_start(); 

#Se incluye el modelo de ExpresionLiteral
require_once '../modelos/ExpresionLiteral.php';

#Se instancia el objeto de ExpresionLiteral
$ExpresionLiteral = new ExpresionLiteral();

$idliteral = isset($_POST['idliteral']) ? limpiarCadena($_POST['idliteral']) : '';
$literal = isset($_POST['literal']) ? limpiarCadena(mb_strtoupper($_POST['literal'])) : '';
$interpretacion = isset($_POST['interpretacion']) ? limpiarCadena($_POST['interpretacion']) : '';
$estatus = isset($_POST['estatus']) ? limpiarCadena($_POST['estatus']) : '';

#Se ejecuta un caso dependiendo del valor del parámetro GET
switch ($_GET['op']) {
	
	case 'comprobarliteral': 

		$rspta = $ExpresionLiteral->comprobarliteral($literal);
		echo json_encode($rspta->fetch_object());
		break;

	case 'guardaryeditar':

		#Se deshabilita el guardado automático de la base de datos
		autocommit(FALSE);

		#Variable para comprobar que todo salió bien al final
		$sw = TRUE;

		#Si la variable esta vacía quiere decir que es un nuevo registro
		if (empty($idliteral)) {
			
			#Se registra el literal
			$ExpresionLiteral->insertar($literal, $interpretacion, $estatus) or $sw = FALSE;

			#Se verifica que todo saliío bien y se guardan los datos o se eliminan todos
			if ($sw) {
				commit();
				echo 'true';
			}
			else {
				rollback();
				echo 'false';
			}

		}
		else{

			#Se edita la materia
			$ExpresionLiteral->editar($idliteral, $literal, $interpretacion, $estatus) or $sw = FALSE;

			#Se verifica que todo saliío bien y se guardan los datos o se eliminan todos
			if ($sw) {
				commit();
				echo 'update';
			}
			else {
				rollback();
				echo 'false';
			}
		}
		break;

	case 'listar':

		$rspta = $ExpresionLiteral->listar();

		if ($rspta->num_rows != 0) {
			
			while ($reg = $rspta->fetch_object()) {

				$data[] = array('0' => 
					($reg->estatus) ? 

					// Se verifica que tenga el permiso de editar para mostrar o no el botón
					( ( isset($_SESSION['permisos']['expresion-literal']) && 
	              	    in_array('editar' , $_SESSION['permisos']['expresion-literal']) ) ?

					'<button class="btn btn-outline-primary " title="Editar" onclick="mostrar('.$reg->id.')" data-toggle="modal" data-target="#literalModal"><i class="fas fa-edit"></i></button>' : '' ).

					// Se verifica que tenga el permiso de eliminar para mostrar o no el botón
					( ( isset($_SESSION['permisos']['expresion-literal']) && 
                  	  in_array('activar-desactivar' , $_SESSION['permisos']['expresion-literal']) ) ?

					' <button class="btn btn-outline-danger" title="Desactivar" onclick="desactivar('.$reg->id.')"> <i class="fas fa-times"> </i></button> ' : '')

					:

					( ( isset($_SESSION['permisos']['expresion-literal']) && 
	              	    in_array('editar' , $_SESSION['permisos']['expresion-literal']) ) ?

					 '<button class="btn btn-outline-primary" title="Editar" onclick="mostrar('.$reg->id.')" data-toggle="modal" data-target="#literalModal"><i class="fa fa-edit"></i></button>' : '' ).


					( ( isset($_SESSION['permisos']['expresion-literal']) && 
                  	  in_array('activar-desactivar' , $_SESSION['permisos']['expresion-literal']) ) ?

					 ' <button class="btn btn-outline-success" title="Activar" onclick="activar('.$reg->id.')"><i class="fa fa-check"></i></button> ' : ''),

             '1' => $reg->literal,
             '2' => $reg->interpretacion);
			}

			$results = array(
				"draw" => 0, #Esto tiene que ver con el procesamiento del lado del servidor
				"recordsTotal" => count($data), #Se envía el total de registros al datatable
				"recordsFiltered" => count($data), #Se envía el total de registros a visualizar
				"data" => $data #datos en un array

			);
		}
		else {
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
	
		$rspta = $ExpresionLiteral->mostrar($idliteral);

		#Se codifica el resultado utilizando Json
		echo json_encode($rspta);

		break;

	case 'desactivar': 

		$rspta = $ExpresionLiteral->desactivar($idliteral);
		echo $rspta ? 'true' : 'false';
		break;

	case 'activar': 

		$rspta = $ExpresionLiteral->activar($idliteral);
		echo $rspta ? 'true' : 'false';
		break;
}