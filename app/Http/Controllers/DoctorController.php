<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Appointments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DoctorController extends Controller
{
    public function dashboard()
    {
        return view("dashboard");
    }

    public function login1()
    {
        return view("doctors.loginDoctor");
    }

    public function registration1()
    {
        return view("doctors.registerDoctor");
    }

    public function prescription()
    {
        $data = array();
        if ('Session'::has('LoginId')) {
            $data = Doctor::where('id', '=', 'Session'::get('LoginId'))->first();
        }
        return view("doctors.prescriptions", compact('data'));
    }

    //Include Database
    public function registerDoctor(Request $request)
    {
        $request->validate([
            'doctorname' => 'required',
            'age' => 'required',
            'gender' => 'required',
            'phonenumber' => 'required|min:10|max:12',
            'address' => 'required',
            'speciality' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:5|max:12'
        ]);

        $doctor = new Doctor();
        $doctor->doctorname = $request->doctorname;
        $doctor->age = $request->age;
        $doctor->gender = $request->gender;
        $doctor->phonenumber = $request->phonenumber;
        $doctor->address = $request->address;
        $doctor->speciality = $request->speciality;
        $doctor->email = $request->email;
        $doctor->password = 'Hash'::make($request->password);
        $res = $doctor->save();
        if ($res) {
            return back()->with('success', 'You have registered successfully');
        } else {
            return back()->with('fail', 'Something wrong');
        }
    }

    public function loginDoctor(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:5|max:12'
        ]);
        $doctor = Doctor::where('email', '=', $request->email)->first();
        if ($doctor) {
            if ('Hash'::check($request->password, $doctor->password)) {
                $request->session()->put('LoginId', $doctor->id);
                return redirect('dashboardDoctor');
            } else {
                return back()->with('fail', 'Passowrd not matches.');
            }
        } else {
            return back()->with('fail', 'This email is not registered.');
        }
    }

    public function dashboardDoctor()
    {
        $data = array();
        if ('Session'::has('LoginId')) {
            $data = Doctor::where('id', '=', 'Session'::get('LoginId'))->first();
        }
        return view("doctors.dashboardDoctor", compact('data'));
    }

    public function profileDoctor()
    {
        $data = array();
        if ('Session'::has('LoginId')) {
            $data = Doctor::where('id', '=', 'Session'::get('LoginId'))->first();
        }

        return view("doctors.profileDoctor", compact('data'));
    }

    public function patientList()
    {
        $data = array();
        if ('Session'::has('LoginId')) {
            $data = Doctor::where('id', '=', 'Session'::get('LoginId'))->first();
        }

        $appointments = Appointments::where('doctoremail', $data->email)->get();
        return view("doctors.patientlist", compact('data', 'appointments'));
    }

    public function update($id)
    {
        $data = Appointments::find($id);
        return view('doctors.update', compact('data'));
    }

    public function getupdate(Request $request)
    {
        $data = Appointments::find($request->id);

        $data->patientemail = $request->patientemail;
        $data->doctoremail = $request->doctoremail;
        $data->date = $request->date;
        $data->time = $request->time;
        $data->symptoms = $request->symptoms;
        $data->link = $request->link;
        $data->doctorname = $request->doctorname;
        $data->patientname = $request->patientname;
        
        $res = $data->save();
                if($res){
            return redirect('patientList');
        }else {
            return back()->with('fail', 'Something wrong');
        }
    }
    public function edit_doctor()
    {
        $doctor = DB::select('select * from doctors');
        return view('editDoctor',['doctors'=>$doctor]);
    }
    public function update_doctor(Request $request)
    {
        $doctor_name = $request->input('doctortname');
        $doctor_email = $request->input('email');
        $doctor_phonenumber = $request->input('phonenumber');
        $doctor_age = $request->input('age');
        $doctor_speciality = $request->input('speciality');
        $doctor_address = $request->input('address');
        
        DB::update('update doctors set doctortname =?, email =?, phonenumber =?, age =?, speciality=?, address =? '
    ,[$doctor_name,$doctor_email,$doctor_phonenumber,$doctor_age,$doctor_speciality,$doctor_address]);

     return redirect('updateprofile.doctor')->with('success','Data Updated');
    }
    
}
