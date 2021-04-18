<?php

namespace App\Http\Controllers;

use DateTime;

use Illuminate\Http\Request;
use App\Models\Programme;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use Carbon\Carbon;

class ProgrammeController extends Controller
{

    //Array multidimensional ce contine toate camerele si ce activitati se pot realiza in respectiva camera
    private $rooms = array(
        'room1' => array('pilates'),
        'room2' => array('kangoo jumps'),
        'room3' => array('pilates','kangoo jumps'),
        'room4' => array('aerobic','butt and abs','full body workout')
    );


    //tipurile de activittati
    private $programmeTypes= ['pilates','kangoo jumps','aerobic','butt and abs','full body workout'];


    //fucntie pentru afisarea tuturor "programme"-lor
    public function index()
    {
        return Programme::where('start_date','>',Carbon::now())->orderby('start_date')->get();
    }
    

    //functie care imi va returna toate tipurile de camere si de programe disponibile
    public function create()
    {
        if( auth()->user()->admin == "false" )
        {
            $message = [
                'message' => 'Error! You are not an admin! you can access this route!'
            ];
            return response($message, 401);
        }
        return [ $this->rooms, $this->programmeTypes ];
    }


    //functie pentru crearea de "programme" nou
    //functia va putea fi accesata doar de un admin
    public function store( Request $request )
    {
        if( auth()->user()->admin == "false" )
        {
            $message = [
                'message' => 'Error! You are not an admin! You cannot access this route!'
            ];
            return response($message, 401);
        }

        $data = $request->validate([
            'title' => 'required | max:100',
            'start_date' => 'required',
            'end_date' => 'required',
            'participants' => 'required | numeric',
            'room' => 'required',
        ]);

        //verific ca tipul de program ales sa fie unul din lista mea 
        if( !in_array($data['title'],$this->programmeTypes) )
        {
            return [
                'message' => 'Error! You have entered a programme that is not in the Programme Types list'
            ];
        }
        
        //verific ca numarul de participanti introdus sa aiba o valoare corecta
        //am introdus un numar maxim de 100 de participanti 
        if( $data['participants'] < 1 || $data['participants'] > 100 )
        {
            return [
                'message' => 'Error! The number of participants must be between 1 and 100'
            ];
        }

        //verific daca camera aleasa este una din lista mea
        if( !array_key_exists($data['room'],$this->rooms) )
        {
            return [
                'message' => 'Error! This room do not exist in rooms list'
            ];
        }

        //verific daca in camera aleasa se poate desfasura programul de antrenament dorit
        if( !in_array($data['title'], $this->rooms[$data['room']]) )
        {
            return [
                'message' => 'Error! '.$data['room']." not allow " .$data['title'].' programme!'
            ];
        }

        //verific daca start_date si end_date au fost introduse in formatul corect
        if(!preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $data['start_date'])){
            return [
                'message' => 'Error! Start_date is not sent correctly! Use this format YYYY-mm-dd HH:ii:ss !'
            ];    
        }

        if(!preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $data['end_date'])){
            return [
                'message' => 'Error! End_date is not sent correctly! Use this format YYYY-mm-dd HH:ii:ss !'
            ];    
        }

        $start_dateAndHour = new DateTime($data['start_date']);
        $end_dateAndHour = new DateTime($data['end_date']);

        $start_dateAndHour_30min = new DateTime($data['start_date']);
        $start_dateAndHour_30min->modify('+30 minutes');

        $start_dateAndHour_4hours = new DateTime($data['start_date']);
        $start_dateAndHour_4hours->modify('+4hours');

        //verific ca ora de sfarsit sa fie mai mare decat ora de inceput
        if( $start_dateAndHour >= $end_dateAndHour )
        {
            return [
                'message' => 'Error! End hour must be after start hour '
            ];   
        }

        //verific ca programul sa aiba o durata minima sa 30 de min 
        if($start_dateAndHour_30min >= $end_dateAndHour)
        {
            return [
                'message' => 'A program must have at least 30 minutes'
            ];   
        }

        
        //verific ca programul meu sa aiba o durata de maxim 4 ore
        if( $start_dateAndHour_4hours < $end_dateAndHour )
        {
            return [
                'message' => 'A program should last a maximum of 4 hours!'
            ];   
        }

        //verific daca camera este ocupata in intervalul de timp ales
        $result = Programme::where('room','=',$data['room'])->where(function($query)use($start_dateAndHour,$end_dateAndHour)
        {  
          return $query->whereBetween('start_date',[$start_dateAndHour,$end_dateAndHour])
               ->orWhereBetween('end_date',[$start_dateAndHour, $end_dateAndHour])
               ->orWhereRaw('? BETWEEN start_date and end_date', $start_dateAndHour) 
               ->orWhereRaw('? BETWEEN start_date and end_date', $end_dateAndHour);
        })->first();
        if( $result ){
            return [
                'message' => $data['room'].' is occupied between the chosen hours!'
            ];  
        }

        return Programme::create([
            'user_id' => auth()->user()->id,//pentru ca ruta asta nu poate fi acesata decat de un user autentifiact am luat id folosind auth() ca o extra protectie 
            'title' => $data['title'],
            'start_date'=> $data['start_date'],
            'end_date' => $data['end_date'],
            'participants' => $data['participants'],
            'room' => $data['room'],
        ]);
    }
   
   

    //sterg Programme-ul si toate programarile care au fost facute pentru acel program
    public function destroy($id)
    {
        if( auth()->user()->admin == "false")
        {
            $message = [
                'message' => 'Error! You are not an admin! you can access this route!'
            ];
            return response($message, 401);
        }
        Appointment::where('programme_id','=',$id)->delete();
        return Programme::destroy($id);
    }
}
