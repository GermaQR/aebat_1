<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Input;

use DB;

use PDF;

use App\Reserva;

class ReservaController extends Controller
{
    //DEfinicion de $array_plazas_disponibles. En cada posición del array se indica el nº de plazas dsiponibles
    //en la parada asociada.
    /*private $definicion_array_plazas_disponibles = array('Puerto Rico'      => 20, 
                                                         'Arguineguín'      => 20, 
                                                         'Maspalomas'       => 20,  
                                                         'Playa del Inglés' => 20);

    private $salidas = array('10:00', '17:00');

    private $paradas = array('Puerto Rico', 'Arguineguín', 'Maspalomas', 'Playa del Inglés');*/


    //Control de acceso a ReservaController de usuarios no autorizados.
    public function __construct(){
        $this->middleware('auth');
        //$this->middleware('auth', ['except' => 'index']);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {


        if (Auth::user()->name == 'admin') {

            // Busca todas las reservas existentes, realizadas por todos los usuarios.
            $reservas = DB::table('reservas')
            ->join('clientes', 'reservas.cliente_id', '=', 'clientes.id')
            ->join('viajes', 'reservas.viaje_id', '=', 'viajes.id')
            ->select('reservas.*', 'clientes.nombre', 'clientes.alojamiento', 'clientes.numero_habitacion', 
                     'viajes.dia', 'viajes.salida', 'viajes.sentido', 'viajes.array_plazas_disponibles')
            ->orderBy('reservas.id', 'desc')
            ->get();

        } else {

            // Busca todas las reservas realizadas por el usuario.
            $reservas = DB::table('reservas')
            ->where('reservas.usuario', '=', Auth::user()->name)
            ->join('clientes', 'reservas.cliente_id', '=', 'clientes.id')
            ->join('viajes', 'reservas.viaje_id', '=', 'viajes.id')
            ->select('reservas.*', 'clientes.nombre', 'clientes.alojamiento', 'clientes.numero_habitacion', 
                     'viajes.dia', 'viajes.salida', 'viajes.sentido', 'viajes.array_plazas_disponibles')
            ->orderBy('reservas.id', 'desc')
            ->get();

        }

        //$viajes = DB::table('viajes')->join('reservas', 'viajes.viaje_id', '=', 'reservas.viaje_id')->select('')->get();

        // Render View
        return view('reserva/index', ['reservas' => $reservas]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('reserva/create');


    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Validación  de los datos introducidos en el formulario de creación de reservas.
        $this->validate($request, [
            'nombre' => 'required',
            'numero_plazas' => 'required',
            'alojamiento' => 'required',
            'numero_habitacion' => 'required',
        ]);




        //Obtención de los datos del formulario a partir del objeto Request
        $nombre = $request->input('nombre');
        $numero_plazas_reservadas = $request->input('numero_plazas');
        $alojamiento = $request->input('alojamiento');
        $numero_habitacion = $request->input('numero_habitacion');
        $precio_aux = $request->input('precio');
        $dia_reservado = $request->input('dia_reservado');
        $horario_reservado = $request->input('horario_reservado');
        $parada_subida = $request->input('parada_subida');
        $parada_bajada = $request->input('parada_bajada');

        //Se procesa el campo 'precio', convirtiendo, en caso necesario, la coma decimal en punto decimal (p.e. 1,5 => 1.5)
        //para poder almacenarlo como tipo DECIMAL en la BBDD.
        $precio = str_replace(',', '.', $precio_aux);

        //Se obtiene el nombre del usuario que realiza la reserva.
        $usuario = Auth::user()->name; 


        //Aquí se debe incluir la función 'guardarReserva()' pasando las variables anteriores
        
        $reserva_id =  guardarReserva($nombre, 
                                        $numero_plazas_reservadas, 
                                        $alojamiento, 
                                        $numero_habitacion,
                                        $precio,
                                        $usuario, 
                                        $dia_reservado,
                                        $horario_reservado,
                                        $parada_subida,
                                        $parada_bajada );


        if ($request->input('tipo_viaje') == 'ida_y_vuelta') {
            //obtener datos del viaje de vuelta del objeto Request y guardar en BBDD.
            //return 'radiobutton encontrado';
            $dia_reservado_vuelta = $request->input('dia_reservado_vuelta');
            $horario_reservado_vuelta = $request->input('horario_reservado_vuelta');
            $parada_subida_vuelta = $request->input('parada_subida_vuelta');
            $parada_bajada_vuelta = $request->input('parada_bajada_vuelta');

           
            $reserva_vuelta_id = guardarReserva($nombre, 
                                            $numero_plazas_reservadas, 
                                            $alojamiento, 
                                            $numero_habitacion,
                                            $precio,
                                            $usuario,  
                                            $dia_reservado_vuelta,
                                            $horario_reservado_vuelta,
                                            $parada_subida_vuelta,
                                            $parada_bajada_vuelta );

        } else {
            $reserva_vuelta_id = 0;
        }
        
        //return redirect()->action('ReservaController@show', ['ids_reservas' => $ids_reservas] );
        
        if ($reserva_id == -1 && $reserva_vuelta_id == -1) {
            $message = 'No hay plazas disponibles para el número de plazas solicitado. 
                        Por favor consulte la disponibilidad de plazas';
            return view('reserva/message', ['mensaje' => $message]);

        } elseif ($reserva_id == -1){

            $message = 'No hay plazas disponibles en el recorrido de IDA. 
                        Por favor consulte la disponibilidad de plazas';
            return view('reserva/message', ['mensaje' => $message]);

        } elseif ($reserva_vuelta_id== -1){  

            $message = 'No hay plazas disponibles en el recorrido de VUELTA. 
                        Por favor consulte la disponibilidad de plazas';
            return view('reserva/message', ['mensaje' => $message]);

        } else {

            return redirect()->route('show', ['id1' => $reserva_id, 'id2' => $reserva_vuelta_id]);
        }
        

    }




    /**
     * Display the specified resource.
     *
     * @param  array  $reservas_ids
     * @return \Illuminate\Http\Response
     */
    public function show($id1, $id2)
    {
        
        // Busca todas las reservas
        $reserva = DB::table('reservas')
        ->where('reservas.id', '=', $id1)
        ->join('clientes', 'reservas.cliente_id', '=', 'clientes.id')
        ->join('viajes', 'reservas.viaje_id', '=', 'viajes.id')
        ->select('reservas.*', 'clientes.*', 'viajes.*')
        ->get();

        if ($id2 > 0) {

            $reserva_vuelta = DB::table('reservas')
            ->where('reservas.id', '=', $id2)
            ->join('clientes', 'reservas.cliente_id', '=', 'clientes.id')
            ->join('viajes', 'reservas.viaje_id', '=', 'viajes.id')
            ->select('reservas.*', 'clientes.*', 'viajes.*')
            ->get();

        } else {
            $reserva_vuelta = null;
        }

        //$viajes = DB::table('viajes')->join('reservas', 'viajes.viaje_id', '=', 'reservas.viaje_id')->select('')->get();

        //Render View
        return view('reserva/show', ['reservas' => $reserva, 'reservas_vuelta' => $reserva_vuelta, 'reserva_id' => $id1, 'reserva_id_vuelta' => $id2]);
        
        //return view('reserva/ticket', ['reservas' => $reservas]);

        
    }


    /**
     * Imprime reserva en formato PDF
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function imprimir($id1, $id2)
    {
        
        // ->select('reservas.*', 'clientes.nombre', 'clientes.apellido', 'clientes.alojamiento', 'clientes.numero_habitacion', 'viajes.dia', 'viajes.salida', 'viajes.sentido', 'viajes.array_plazas_disponibles')

        $reserva_ida = DB::table('reservas')
        ->where('reservas.id', '=', $id1)
        ->join('clientes', 'reservas.cliente_id', '=', 'clientes.id')
        ->join('viajes', 'reservas.viaje_id', '=', 'viajes.id')
        ->select('reservas.*', 'clientes.*', 'viajes.*')
        ->get();

        if ($id2 > 0) {

            $reserva_vuelta = DB::table('reservas')
            ->where('reservas.id', '=', $id2)
            ->join('clientes', 'reservas.cliente_id', '=', 'clientes.id')
            ->join('viajes', 'reservas.viaje_id', '=', 'viajes.id')
            ->select('reservas.*', 'clientes.*', 'viajes.*')
            ->get();

        } else {
            $reserva_vuelta = null;
        }

        //Método 1
        //view()->share('reservas',$reservas);

        //$pdf = PDF::loadView('reserva/index');
        //return $pdf->download('reserva/index');

        //Método 2
        $view =  \View::make('reserva.ticket', compact('reserva_ida', 'reserva_vuelta'))->render();
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);

        //Formato del papel de impresión del ticket
        $pdf->setPaper('A4');

        return $pdf->stream('ticket');

        //return view('reserva/ticket', ['reservas' => $reservas]);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * 
     * @return \Illuminate\Http\Response
     */
    public function buscarReserva()
    {

        return view ('reserva/busqueda');
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function resultado(Request $request)
    {

        $numero_habitacion = $request->input('numero_habitacion');

        $reservas = DB::table('reservas')
        ->where('reservas.usuario', '=', Auth::user()->name)
        ->join('clientes', 'reservas.cliente_id', '=', 'clientes.id')
        ->join('viajes', 'reservas.viaje_id', '=', 'viajes.id')
        ->select('reservas.*', 'clientes.nombre', 'clientes.alojamiento', 'clientes.numero_habitacion', 
                 'viajes.dia', 'viajes.salida', 'viajes.sentido', 'viajes.array_plazas_disponibles')
        ->orderBy('reservas.id', 'desc')
        ->get();



        return view('reserva/resultado', ['reservas' => $reservas, 'numero_habitacion' => $numero_habitacion]);

    }

    
    /**
     * Devuelve vista con formulario para borrar reservas.
     *
     * 
     * @return \Illuminate\Http\Response
     */
    public function borrar()
    {
        return view ('reserva/borrar');
    }


    /**
     * Borra reserva de la BBDD.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function borrado(Request $request)
    {
        $fecha_borrado = $request->input('fecha_borrado');

        //Conversión de fecha al formado usado por la BBDD.
        $fecha_borrado_2 = date("Y-m-d", strtotime($fecha_borrado));


        $viajes = DB::table('viajes')
                    ->where('viajes.dia', '<', $fecha_borrado_2)
                    ->select('viajes.id')
                    ->get();





        foreach ($viajes as $viaje){




            $reservas = DB::table('reservas')
                        ->where('reservas.viaje_id', '=', $viaje->id)
                        ->select('reservas.cliente_id', 'reservas.id')
                        ->get();
    
            //Se borran los registros de la tabla 'reservas' con el valor de 'viaje_id' igual a '$viaje->id'.  
            DB::table('reservas')->where('reservas.viaje_id', '=', $viaje->id)->delete();
            //echo 'Borrado de reserva con id '.$reserva->id.'<br>';  
 

            foreach($reservas as $reserva){

                $clientes = DB::table('clientes')
                            ->where('clientes.id', '=', $reserva->cliente_id)
                            ->select('clientes.id')
                            ->get();

                foreach ($clientes as $cliente){
                    $otras_reservas = DB::table('reservas')
                                        ->where('reservas.cliente_id','=', $cliente->id)
                                        ->count();


  

                    //echo 'numero de reservas del cliente con id '.$cliente->id.': '.$otras_reservas.'<br>';

                    if ($otras_reservas == 0){


                        //Se borra el registro para el cliente con 'id' igual '$cliente->id'.
                        DB::table('clientes')->where('clientes.id', '=', $cliente->id)->delete();
                        //echo 'Borrado de cliente con id '.$cliente->id.'<br>';
                    }


                }



            }



        }

        //Se borran los registros de la tabla 'viajes' con el valor de 'dia' menor que '$fecha_borrado_2'.            
        DB::table('viajes')->where('viajes.dia', '<', $fecha_borrado_2)->delete();
        //echo 'Borrado de viaje con id '.$viaje->id.'<br>';
         //DB::table('viajes')->where('viajes.id', '=', 1)->delete();

        $message = 'Se han eliminado las reservas seleccionadas';
        return view('reserva/message', ['mensaje' => $message]);

    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
