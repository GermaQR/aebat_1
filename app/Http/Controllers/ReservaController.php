<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use Illuminate\Support\Facades\Auth;

use DB;

use PDF;

class ReservaController extends Controller
{
    //DEfinicion de $array_plazas_disponibles. En cada posición del array se indica el nº de plazas dsiponibles
    //en la parada asociada.
    private $definicion_array_plazas_disponibles = array('Puerto Rico'      => 1, 
                                                         'Arguineguín'      => 1, 
                                                         'Maspalomas'       => 1,  
                                                         'Playa del Inglés' => 1);

    private $salidas = array('10:00', '17:00');

    private $paradas = array('Puerto Rico', 'Arguineguín', 'Maspalomas', 'Playa del Inglés');


    //Control de acceso a ReservaController de usuarios no permitidos.
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
        
        // Busca todas las reservas
        $reservas = DB::table('reservas')
        ->join('clientes', 'reservas.cliente_id', '=', 'clientes.id')
        ->join('viajes', 'reservas.viaje_id', '=', 'viajes.id')
        ->select('reservas.*', 'clientes.*', 'viajes.*')
        ->get();

        //$viajes = DB::table('viajes')->join('reservas', 'viajes.viaje_id', '=', 'reservas.viaje_id')->select('')->get();

        // Render View
        return view('reserva/index', ['reservas' => $reservas]);
        //return view('home');
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
            'apellidos' => 'required',
            'alojamiento' => 'required',
            'numero_habitacion' => 'required',
        ]);


        //Obtención de los datos del formulario a partir del objeto Request
        $nombre = $request->input('nombre');
        $apellido = $request->input('apellidos');
        $alojamiento = $request->input('alojamiento');
        $numero_habitacion = $request->input('numero_habitacion');
        $dia_reservado = $request->input('dia_reservado');
        $horario_reservado = $request->input('horario_reservado');
        $parada_subida = $request->input('parada_subida');
        $parada_bajada = $request->input('parada_bajada');



        //Conversion de fecha a formato Y-m-d para guardar en BBDD.
        $dia_reservado_2 = date("Y-m-d", strtotime($dia_reservado));

        //Determinación del sentido del viaje (Norte-Sur o Sur-Norte)
        if ($parada_subida < $parada_bajada) {
            //Sentido Sur-Norte
            $sentido_viaje = 'SN';
        } else {
            //Sentido Norte-Sur
            $sentido_viaje = 'NS';
        }






        //Conversion de los campos de 'salida', 'parada de subida' y 'parada de bajada' 
        //para almacenarlos en la BBDD con los nombres indicados en los campos del formulario.
        $horario_reservado_2 = $this->salidas[$horario_reservado];
        $parada_subida_2 = $this->paradas[$parada_subida];
        $parada_bajada_2 = $this->paradas[$parada_bajada];

        //Mensaje enviado a la vista 'message.blade.php' para testeo de la app.
        //$message = serialize($array_plazas_disponibles);


        //Se comprueba si en la tabla 'viajes' existe algún registro para la fecha, hora y de
        //sentido de la salida introducidas en el formulario y si existe se comprueba la 
        //disponibilidad de plazas.
        $reg_viajes= DB::table('viajes')
        ->where([['dia', '=', $dia_reservado_2], 
                ['salida', '=', $horario_reservado_2],
                ['sentido', '=', $sentido_viaje]])
        ->get();

        $id_viaje_existente = -1;

        foreach ($reg_viajes as $reg_viaje) {
            $id_viaje_existente = $reg_viaje->id;
            $viaje_id = $id_viaje_existente;
        }


        //Procesado de $array_plazas_disponibles actualizando nº de plazas disponibles en cada parada.
        $actualizar_array_plazas_disponibles = false;
            
        if ($id_viaje_existente == -1) {
            //Se debe crear un nuevo registro para el viaje.
            $array_plazas_disponibles = $this->definicion_array_plazas_disponibles;
        } else {
            $array_plazas_disponibles = unserialize ($reg_viaje->array_plazas_disponibles);
            $actualizar_array_plazas_disponibles = true;
            
        }


        $claves_array = array_keys($array_plazas_disponibles);

        
        if ($parada_bajada >= $parada_subida){

            //Se procesa '$array_plazas_disponibles' para el sentido de viaje Sur-Norte. 
            for ($i = $parada_subida; $i < $parada_bajada; $i++ ) {

                $j = $claves_array[$i];

                $array_plazas_disponibles[$j] = $array_plazas_disponibles[$j] -1;

                if ($array_plazas_disponibles[$j] == -1){
                    //No hay disponibilidad de plazas en el trayecto $j.
                    $message = 'No hay plazas libres en '.$j.'.';
                    return view('reserva/message', ['mensaje' => $message]);

                }

            }



        } else {

            //Se procesa '$array_plazas_disponibles' para el sentido de viaje Norte-Sur.
            for ($i = $parada_subida; $i > $parada_bajada; $i-- ) {

                $j = $claves_array[$i];

                $array_plazas_disponibles[$j] = $array_plazas_disponibles[$j] -1;
            }

        }


        //Se convierte a 'string' el array '$array_plazas_disponibles' para poder 
        //guardarlo en la BBDD.
        $str_array_plazas_disponibles = serialize($array_plazas_disponibles);


        //Se comprueba si en la tabla 'viajes' existe algún registro para el cliente introducido
        //en el formulario
        $reg_clientes= DB::table('clientes')
        ->where([['nombre',     '=', $nombre], 
                ['apellido',   '=', $apellido],
                ['alojamiento', '=', $alojamiento],
                ['numero_habitacion', '=', $numero_habitacion]])
        ->get();

        $id_cliente_existente = -1;

        foreach ($reg_clientes as $reg_cliente) {
            $id_cliente_existente = $reg_cliente->id;
            $cliente_id = $id_cliente_existente;
        }


        
        // Guardar datos en la BBDD.
        if ($id_cliente_existente == -1) {
            $cliente_id = DB::table('clientes')->insertGetId(
                [
                    'nombre'            => $nombre,
                    'apellido'          => $apellido,
                    'alojamiento'       => $alojamiento,
                    'numero_habitacion' => $numero_habitacion
                ]
            );
        }

        if ($id_viaje_existente == -1) {

            $viaje_id = DB::table('viajes')->insertGetId(
                [
                    'dia'                       => $dia_reservado_2,
                    'salida'                    => $horario_reservado_2,
                    'sentido'                   => $sentido_viaje,
                    'array_plazas_disponibles'  => $str_array_plazas_disponibles
                ]
            );   
        }   

        //Se actualiza array_plazas_disponibles en tabla 'viajes' de la BBDD en el caso
        //de existir un registro existente para el viaje solicitado por el cliente.
        if ($actualizar_array_plazas_disponibles) {

                DB::table('viajes')
                        ->where('id', $viaje_id)
                        ->update(['array_plazas_disponibles'  => $str_array_plazas_disponibles]);           
        }


        DB::table('reservas')->insert(
            [

                'parada_subida' => $parada_subida_2,
                'parada_bajada' => $parada_bajada_2,    
                'viaje_id'      => $viaje_id,
                'cliente_id'    => $cliente_id

            ]
        );




        //\Session::flash('flash_message', 'Reserva creada');


        // Redirigir a vista donde se listan las reservas creadas.
        //return \Redirect::route('reserva.index');

        //Redirigir a vista 'message.blade.php' con mensaje para testeo de la app.
        


        $message = strval($id_viaje_existente);

        return view('reserva/message', ['mensaje' => $message]);

    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
        // Busca todas las reservas
        $reservas = DB::table('reservas')
        ->where('reservas.cliente_id', '=', $id)
        ->join('clientes', 'reservas.cliente_id', '=', 'clientes.id')
        ->join('viajes', 'reservas.viaje_id', '=', 'viajes.id')
        ->select('reservas.*', 'clientes.*', 'viajes.*')
        ->get();

        //Método 1
        view()->share('reservas',$reservas);

        //$pdf = PDF::loadView('reserva/index');
        //return $pdf->download('reserva/index');

        //Método 2
        $view =  \View::make('reserva.index', $reservas)->render();
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        return $pdf->stream('index');


        //$viajes = DB::table('viajes')->join('reservas', 'viajes.viaje_id', '=', 'reservas.viaje_id')->select('')->get();

        // Render View
        //return view('reserva/index', ['reservas' => $reservas]);
        //return view('home');

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
