<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Models\Doctor;
use App\Notifications\SendEmailNotification;
use Illuminate\Support\Facades\Auth;
use Notification;


class AdminController extends Controller
{
    public function addview(){
        
        if(Auth::id()){
            if(Auth::user()->usertype==1){
                return view('admin.add_doctor');
            } else{
                return redirect()->back();
            }
        }

        return redirect('login');
    }

    public function upload(Request $request){
        $doctor = new doctor;
        $image = $request->file;
        $imagename = time().'.'.$image->getClientoriginalExtension();

        $request->file->move('doctorimage',$imagename);
        $doctor->image=$imagename;

        $doctor->name=$request->name;
        $doctor->phone=$request->number;
        $doctor->room=$request->room;
        $doctor->speciality=$request->speciality;

        $doctor->save();
        return redirect()->back()->with('message','Doctor Added Successfully');
    }

    public function showappointment(){
        if(Auth::id()){
            if(Auth::user()->usertype==1){
                $data = Appointment::all();
                return view('admin.showappointment',compact('data'));
            } else{
                return redirect()->back();
            }
        }

        return redirect('login');
        
    }

    public function approved($id){
        $data = Appointment::find($id); 
        $data->status='Approved';
        $data->save();

        return redirect()->back();
    }

    public function canceled($id){
        $data = Appointment::find($id); 
        $data->status='Canceled';
        $data->save();

        return redirect()->back();
    }

    public function showdoctor(){
        $data = Doctor::all();
        return view('admin.showdoctor',compact('data'));
    }

    public function deletedoctor($id){
        $data = Doctor::find($id);
        $data->delete();

        return redirect()->back();
    }

    public function updatedoctor($id){
        $data = Doctor::find($id);
        return view('admin.update_doctor',compact('data'));
    }

    public function editdoctor(request $request, $id){
        $data = Doctor::find($id);

        $data->name = $request->name;
        $data->phone = $request->phone;
        $data->speciality = $request->speciality;
        $data->room = $request->room;

        $image = $request->file;

        if($image){
            $imagename = time().'.'.$image->getClientOriginalExtension();

            $request->file->move('doctorimage',$imagename);
            $data->image = $imagename;
        }
        

        $data->save();
        return redirect()->back()->with('message','Doctor details updated successfully');

    }

    public function emailview($id){
        $data = Appointment::find($id);
        return view('admin.email_view',compact('data'));
    }

    public function sendemail(Request $request, $id){
        $data = Appointment::find($id);
        $details = [
            'greeting' => $request->greeting,
            'body' => $request->body,
            'actiontext' => $request->actiontext,
            'actionurl' => $request->actionurl,
            'endpart' => $request->endpart,
        ];

        Notification::send($data, new SendEmailNotification($details));

        return redirect()->back()->with('message','Email send is successfully');
    }
}
