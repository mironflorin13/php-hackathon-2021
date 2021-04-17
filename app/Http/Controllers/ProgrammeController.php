<?php

namespace App\Http\Controllers;

use DateTime;

use Illuminate\Http\Request;
use App\Models\Programme;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Support\Facades\DB;


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
    
    public function create()
    {
        return [$this->rooms,$this->programmeTypes];
    }

    //functie pentru crearea de "programme" nou
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' =>'required | max:100',
            'start_date' =>'required',
            'end_date' =>'required',
            'participants' =>'required | numeric',
            'room' =>'required',
        ]);

        //verific ca tipul de program ales sa fie unul din lista mea 
        if(!in_array($data['title'],$this->programmeTypes))
        {
            return [
                'message' => 'Error! You have entered a programme that is not in the Programme Types list'
            ];
        }
        
        //verific ca numarul de participanti introdus sa aiba o valoare corecta
        //am introdus un numar maxim de 100 de participanti 
        if($data['participants']<1 || $data['participants']>100 )
        {
            return [
                'message' => 'Error! The number of participants must be between 1 and 100'
            ];
        }

        //verific daca camera aleasa este una din lista mea
        if(!array_key_exists($data['room'],$this->rooms))
        {
            return [
                'message' => 'Error! This room do not exist in rooms list'
            ];
        }

        //verific daca in camera aleasa se poate desfasura programul de antrenament dorit
        if(!in_array($data['title'],$this->rooms[$data['room']]))
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
        
        //verific ca ora de sfarsit sa fie mai mare decat ora de inceput
        
        if($start_dateAndHour >= $end_dateAndHour)
        {
            return [
                'message' => 'Error! End hour must be after start hour and '
            ];   
        }

        //verific ca programul sa aiba o durata minima sa 30 de min 
        $start_dateAndHour_30min = $start_dateAndHour;
        $start_dateAndHour_30min->modify('+30 minutes');
        

        if($start_dateAndHour_30min >= $end_dateAndHour)
        {
            return [
                'message' => 'Error! A program must have at least 30 minutes'
            ];   
        }

        //verific daca camera este ocupata in intervalul de timp ales
        $result=Programme::where('room','=',$data['room'])->where(function($query)use($start_dateAndHour,$end_dateAndHour)
        {  
          return $query->whereBetween('start_date',[$start_dateAndHour,$end_dateAndHour])
               ->orWhereBetween('end_date',[$start_dateAndHour, $end_dateAndHour])
               ->orWhereRaw('? BETWEEN start_date and end_date', $start_dateAndHour) 
               ->orWhereRaw('? BETWEEN start_date and end_date', $end_dateAndHour);
        })->first();
        if($result){
            return [
                'message' => $data['room'].' is occupied between the chosen hours!'
            ];  
        }

        return Programme::create([
            'user_id' => auth()->user()->id,
            'title' => $data['title'],
            'start_date'=> $data['start_date'],
            'end_date' => $data['end_date'],
            'participants' => $data['participants'],
            'room' => $data['room'],
        ]);
    }
   
    //fucntie pentru afisarea tuturor "programme"-lor
    public function index()
    {
        return Programme::all();
    }

    //sterg un program s toate programarile care au fost facute pentru acel program
    public function destroy($id)
    {
        Appointment::where('programme_id','=',$id)->delete();
        return Programme::destroy($id);
    }
}
