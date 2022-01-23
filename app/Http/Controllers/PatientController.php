<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Appointments;
use App\Models\Prescriptions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PatientController extends Controller
{
    public function dashboard()
    {
        return view("dashboard");
    }

    public function login()
    {
        return view("patients.loginPatient");
    }

    public function registration()
    {
        return view("patients.registerPatient");
    }

    public function prescriptionInfo()
    {
        return view("patients.prescriptionInfo");
    }

    //Include Database
    public function registerPatient(Request $request)
    {
        $request->validate([
            'patientname' => 'required',
            'age' => 'required',
            'gender' => 'required',
            'DOB' => 'required',
            'address' => 'required',
            'phonenumber' => 'required|min:10|max:12',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:5|max:12'
        ]);

        $patient = new Patient();
        $patient->patientname = $request->patientname;
        $patient->age = $request->age;
        $patient->gender = $request->gender;
        $patient->DOB = $request->DOB;
        $patient->phonenumber = $request->phonenumber;
        $patient->address = $request->address;
        $patient->email = $request->email;
        $patient->password = 'Hash'::make($request->password);
        $res = $patient->save();
        if ($res) {
            return back()->with('success', 'You have registered successfully');
        } else {
            return back()->with('fail', 'Something wrong');
        }
    }

    public function loginPatient(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:5|max:12'
        ]);
        $patient = Patient::where('email', '=', $request->email)->first();
        if ($patient) {
            if ('Hash'::check($request->password, $patient->password)) {
                $request->session()->put('LoginId', $patient->id);
                return redirect('dashboardPatient');
            } else {
                return back()->with('fail', 'Passowrd not matches.');
            }
        } else {
            return back()->with('fail', 'This email is not registered.');
        }
    }

    public function dashboardPatient()
    {
        $data = array();
        if ('Session'::has('LoginId')) {
            $data = Patient::where('id', '=', 'Session'::get('LoginId'))->first();
        }
        return view("patients.dashboardPatient", compact('data'));
    }

    public function profilePatient()
    {
        $data = array();
        if ('Session'::has('LoginId')) {
            $data = Patient::where('id', '=', 'Session'::get('LoginId'))->first();
        }

        return view("patients.profilePatient", compact('data'));
    }

    //[1]CHOOSE THE DOCTOR
    public function doctorDisplay()
    {
        $data = array();
        if ('Session'::has('LoginId')) {
            $data = Patient::where('id', '=', 'Session'::get('LoginId'))->first();
        }

        $doctor = Doctor::get();
        return view("doctors.doctor", compact('doctor', 'data'));
    }

    //[2]VIEW THE CHOOSEN DOCTOR 
    public function doctorSingle($email)
    {
        $data = array();
        if ('Session'::has('LoginId')) {
            $data = Patient::where('id', '=', 'Session'::get('LoginId'))->first();
        }

        $doctor = Doctor::where('email', '=', $email)->first();
        return view("doctors.doctorSingle", compact(['doctor', 'data']));
    }

    //[3]PROCEED TO SET APPOINTMENT PAGE
    public function appointment($email)
    {
        $data = array();
        if ('Session'::has('LoginId')) {
            $data = Patient::where('id', '=', 'Session'::get('LoginId'))->first();
        }

        $doctor = Doctor::where('email', '=', $email)->first();
        return view("patients.appointmentSet", compact(['doctor', 'data']));
    }

    public function logout()
    {
        if ('Session'::has('LoginId')) {
            'Session'::pull('loginId');
            return redirect('dashboard');
        }
    }

    public function appointmentList()
    {
        $data = array();
        if ('Session'::has('LoginId')) {
            $data = Patient::where('id', '=', 'Session'::get('LoginId'))->first();
        }

        $appointments = Appointments::where('patientemail', $data->email)->get();
        return view("patients.appointmentList", compact('data', 'appointments'));
    }

    public function edit_patient()
    {
        $patient = DB::select('select * from patients');
        return view('editPatient',['patients'=>$patient]);
    }
    public function update_Patient(Request $request)
    {
        $patient_name = $request->input('patientname');
        $patient_email = $request->input('email');
        $patient_phonenumber = $request->input('phonenumber');
        $patient_age = $request->input('age');
        $patient_DOB = $request->input('DOB');
        $patient_address = $request->input('address');
        
        DB::update('update patients set patientname =?, email =?, phonenumber =?, age =?, DOB =?, address =? '
    ,[$patient_name,$patient_email,$patient_phonenumber,$patient_age,$patient_DOB,$patient_address]);

     return redirect('updateprofile.patient')->with('success','Data Updated');
    }
    public function generate ()
    {
    }
}
