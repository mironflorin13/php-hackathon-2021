<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Programme;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    //functie care imi returneaza toate programarile facute
    public function index()
    {
        //deoarece aceacta functie va afisa toate programarile din baza de date vom permite accesul doar adminilor
        if(auth()->user()->admin == "false")
        {
            $message = [
                'message' => 'Error! You are not an admin! you can access this route!'
            ];
            return response($message, 401);
        }
        return Appointment::all();
    }

    //functie ce imi returneaza toate posibilele programele la care se pot face programari
    public function create()
    {
        return Programme::where('start_date','>',Carbon::now())->orderby('start_date')->get();
    }

    //functia prin care creez o noua programare la unul din Programme
    public function store(Request $request)
    {
        $data = $request->validate([
            "user_id" => '',
            "CNP" => 'required | numeric ',
            "programme_id" => 'required | numeric',
        ]);
        //verific CNP-ul
        if(!preg_match('/^[1-9]\d{2}(0[1-9]|1[0-2])(0[1-9]|[12]\d|3[01])(0[1-9]|[1-4]\d|5[0-2]|99)(00[1-9]|0[1-9]\d|[1-9]\d\d)\d$/', $data['CNP'])){
            return [
                'message' => 'Use a valid CNP !'
            ];    
        }

        //verific daca nu am atins limita maxima de persoane care se pot inregistra la un program
        $programme = Programme::where('id','=',$data['programme_id'])->first();
        if(!$programme){
            return [
                'message' => 'Error! There is no programme with this id!'
            ];   
        }
        $appointments = count(Appointment::where('programme_id','=',$data['programme_id'])->get());
        if($appointments >= $programme->participants){
            return [
                'message' => 'The maximum number of participants was reached for this programme!'
            ];   
        }

        //verific la ce "programme" mai este programat userul 
        //si verific daca vreo programare se suprapune cea pe care urmeaza sa o fac

        $otherAppointments = Appointment::where('CNP','=',$data['CNP'])->get();

        if(count($otherAppointments) > 0){
            $myProgramme = Programme::where('id','=',$data['programme_id'])->first();
            $startDate = $myProgramme->start_date;
            $endDate = $myProgramme->end_date;
            
            foreach ($otherAppointments as $appointment) {
                if($appointment->programme_id == $data['programme_id']){
                    return [
                        'message' => 'You are already enrolled in this programme!'
                    ];  
                }
                else
                {
                    $result = Programme::where('id','=',$appointment->programme_id)->where(function($query)use($startDate,$endDate)
                            {  
                                return $query->whereBetween('start_date',[$startDate,$endDate])
                                    ->orWhereBetween('end_date',[$startDate, $endDate])
                                    ->orWhereRaw('? BETWEEN start_date and end_date', $startDate) 
                                    ->orWhereRaw('? BETWEEN start_date and end_date', $endDate);
                            })->first();
                    if($result){
                        return [
                            'message' =>'You already have an appointment for another programme that overlaps with this one!'
                        ];  
                    }
                }
            }
        }

        
        //testez daca userul este conectat
        if( auth()->user() )
        {
            $data["user_id"] = auth()->user()->id;
        }

        return Appointment::create($data);
    }

    //functie ce imi va returna toate "programme"-ele la care s-a inregistrat utilizatorul in functie de cnp sau;
    public function showAll( $cnp )
    {
        $app = Appointment::where('CNP','=',$cnp)->get();
        if( count($app) > 0 )
        {
            $programme = [];
            foreach ($app as $a) {
                $p = Programme::where('id','=',$a->programme_id)->first();
                array_push($programme,$p);
            }
            return $programme;
        }
        return [
            'message' => 'You not have appointments!'
        ];  
    }
    
    //functie ce imi va stege o rezervare in functie de id
    //functie trebuie sa primeasaca si un CNP
    public function destroy($id)
    {
        $data =request()->validate([
            'CNP' => 'required',
        ]);
        
        $a = null;
        $a = Appointment::where('id','=',$id)->first();
        
        if(!$a){
            return [
                'message' => 'There is no appointment with this id!'
            ];
        }
        if( $a->CNP != $data['CNP'])
        {
            return [
                'message' => 'The CNP does not metch!'
            ];
        }
        return Appointment::destroy($id);
    }
}
