<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Input;

use DB;

use PDF;

class ReservaController extends Controller
{
    //DEfinicion de $array_plazas_disponibles. En cada posición del array se indica el nº de plazas dsiponibles
    //en la parada asociada.
    private $definicion_array_plazas_disponibles = array('Puerto Rico'      => 5, 
                                                         'Arguineguín'      => 5, 
                                                         'Maspalomas'       => 5,  
                                                         'Playa del Inglés' => 5);

    private $salidas = array('10:00', '17:00');

    private $paradas = array('Puerto Rico', 'Arguineguín', 'Maspalomas', 'Playa del Inglés');


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

        // Busca todas las reservas
        $reservas = DB::table('reservas')
        ->join('clientes', 'reservas.cliente_id', '=', 'clientes.id')
        ->join('viajes', 'reservas.viaje_id', '=', 'viajes.id')
        ->select('reservas.*', 'clientes.nombre', 'clientes.alojamiento', 'clientes.numero_habitacion', 
                 'viajes.dia', 'viajes.salida', 'viajes.sentido', 'viajes.array_plazas_disponibles')
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
            'numero_plazas' => 'required',
            'alojamiento' => 'required',
            'numero_habitacion' => 'required',
        ]);




        //Obtención de los datos del formulario a partir del objeto Request
        $nombre = $request->input('nombre');
        $numero_plazas_reservadas = $request->input('numero_plazas');
        $alojamiento = $request->input('alojamiento');
        $numero_habitacion = $request->input('numero_habitacion');
        $dia_reservado = $request->input('dia_reservado');
        $horario_reservado = $request->input('horario_reservado');
        $parada_subida = $request->input('parada_subida');
        $parada_bajada = $request->input('parada_bajada');

        //Datos viaje de vuelta

   



        //Aquí se debe incluir la función 'guardarReserva()' pasando las variables anteriores
        $reserva_id =  guardarReserva($nombre, 
                                        $numero_plazas_reservadas, 
                                        $alojamiento, 
                                        $numero_habitacion, 
                                        $dia_reservado,
                                        $horario_reservado,
                                        $parada_subida,
                                        $parada_bajada );


        if ($request->input('tipo_viaje') == 'ida_y_vuelta') {
            //obtener datos del viaje de vuelta del objeto Request y guardar en BBDD.
            //return 'radiobutton encontrado';
        }




        return redirect()->action('ReservaController@show', ['id' => $reserva_id]);
        

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
        ->where('reservas.id', '=', $id)
        ->join('clientes', 'reservas.cliente_id', '=', 'clientes.id')
        ->join('viajes', 'reservas.viaje_id', '=', 'viajes.id')
        ->select('reservas.*', 'clientes.*', 'viajes.*')
        ->get();

        //$viajes = DB::table('viajes')->join('reservas', 'viajes.viaje_id', '=', 'reservas.viaje_id')->select('')->get();

        //Render View
        return view('reserva/show', ['reservas' => $reservas, 'reserva_id' => $id]);
        
        //return view('reserva/ticket', ['reservas' => $reservas]);

    }


    /**
     * Imprime reserva en formato PDF
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function imprimir($id)
    {
        
        // ->select('reservas.*', 'clientes.nombre', 'clientes.apellido', 'clientes.alojamiento', 'clientes.numero_habitacion', 'viajes.dia', 'viajes.salida', 'viajes.sentido', 'viajes.array_plazas_disponibles')

        $reservas = DB::table('reservas')
        ->where('reservas.id', '=', $id)
        ->join('clientes', 'reservas.cliente_id', '=', 'clientes.id')
        ->join('viajes', 'reservas.viaje_id', '=', 'viajes.id')
        ->select('reservas.*', 'clientes.*', 'viajes.*')
        ->get();

        //Método 1
        //view()->share('reservas',$reservas);

        //$pdf = PDF::loadView('reserva/index');
        //return $pdf->download('reserva/index');

        //Método 2
        $view =  \View::make('reserva.ticket', compact('reservas'))->render();
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
