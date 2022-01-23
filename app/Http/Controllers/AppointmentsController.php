<?php

namespace App\Http\Controllers;

use App\Models\Appointments;
use Illuminate\Http\Request;

class AppointmentsController extends Controller
{
    //[4]BOOK AN APPOINTMENT
    public function appointmentSet(Request $request)
    {
        $request->validate([
            'patientemail' => 'required',
            'doctoremail' => 'required',
            'date' => 'required',
            'time' => 'required',
            'symptoms' => 'required',
            'link' => 'required',
            'doctorname' => 'required',
            'patientname' => 'required',
            'patientid' => 'required'
        ]);

        $appointment = new Appointments();
        $appointment->patientemail = $request->patientemail;
        $appointment->doctoremail = $request->doctoremail;
        $appointment->date = $request->date;
        $appointment->time = $request->time;
        $appointment->symptoms = $request->symptoms;
        $appointment->link = $request->link;
        $appointment->doctorname = $request->doctorname;
        $appointment->patientname = $request->patientname;
        $appointment->patientID = $request->patientid;
        $res = $appointment->save();
        if($res){
            return redirect('confirmation');
        }else {
            return back()->with('fail', 'Something wrong');
        }
    }

    public function confirmation()
    {
        return view("patients.confirmation");
    }

}
