<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Programme;

class AppointmentController extends Controller
{
    //functie care imi returneaza toate programarile facute
    public function index()
    {
        return Appointment::all();
    }

    //functie ce imi returneaza toate programele la care se pot face programari
    public function create()
    {
        return Programme::all();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            "user_id" => '',
            "CNP" => 'required | numeric ',
            "programme_id" => 'required | numeric',
        ]);
        //verific CNP-ul

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
                    $result = Programme::where('id','=',$data['programme_id'])->where(function($query)use($startDate,$endDate)
                            {  
                                return $query->whereBetween('start_date',[$startDate,$endDate])
                                    ->orWhereBetween('end_date',[$startDate, $endDate])
                                    ->orWhereRaw('? BETWEEN start_date and end_date', $startDate) 
                                    ->orWhereRaw('? BETWEEN start_date and end_date', $endDate);
                            })->first();
                    if($result){
                        return [
                            'message' =>'You already have an appointment for another programme that overlaps with this one'
                        ];  
                    }
                }
            }
        }

        
        //testez daca userul este conectat
        if(auth()->user()){
            $data["user_id"] = auth()->user()->id;
        }

        return Appointment::create($data);
    }
}
