<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Prescriptions;
use Illuminate\Http\Request;

class PrescriptionsController extends Controller
{
    public function prescriptionSet(Request $request)
    {
        $request->validate([
            'date' => 'required',
            'time' => 'required',
            'patientID' => 'required',
            'appointmentID' => 'required',
            'symptoms' => 'required',
            'prescription' => 'required',
            'validation' => 'required',
            'doctorID' => 'required'
        ]);

        $prescription = new Prescriptions();
        $prescription->date = $request->date;
        $prescription->time = $request->time;
        $prescription->patientID = $request->patientID;
        $prescription->appointmentID = $request->appointmentID;
        $prescription->symptoms = $request->symptoms;
        $prescription->prescription = $request->prescription;
        $prescription->validation = $request->validation;
        $prescription->doctorID = $request->doctorID;
        
        $res = $prescription->save();
        if($res){
            return redirect('reviewPrescriptions');
        }else {
            return back()->with('fail', 'Something wrong');
        }
    }

    public function reviewPrescriptions()
    {      
        $data = array();
        if ('Session'::has('LoginId')) {
            $data = Doctor::where('id', '=', 'Session'::get('LoginId'))->first();
        }
        $prescriptions = Prescriptions::where('doctorID', $data->id)->get();
        return view("doctors.reviewPrescriptions", compact('data', 'prescriptions'));
    }

    public function viewPrescriptions()
    {      
        $data = array();
        if ('Session'::has('LoginId')) {
            $data = Patient::where('id', '=', 'Session'::get('LoginId'))->first();
        }
        $prescriptions = Prescriptions::where('patientID', $data->id)->get();
        return view("patients.viewPrescription", compact('data', 'prescriptions'));
    }
}
