<?php

namespace App\Http\Controllers;


use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;



class ReportController extends Controller
{


    /**
     * Show report form
     */
    public function create($userId)
    {

        $user = User::findOrFail($userId);


        return view(
            'reports.create',
            compact('user')
        );

    }





    /**
     * Store report
     */
    public function store(Request $request)
    {


        $validated = $request->validate([


            'reported_user_id' => [
                'required',
                'exists:users,id'
            ],


            'reason' => [
                'required',
                'string',
                'max:255'
            ],


            'description' => [
                'nullable',
                'string'
            ],


        ]);




        Report::create([


            'reporter_id' => Auth::id(),


            'reported_user_id' => 
                $validated['reported_user_id'],


            'reason' =>
                $validated['reason'],


            'description' =>
                $validated['description'] ?? null,


            'status' =>
                'pending',


        ]);




        return back()->with(

            'success',

            'Report submitted successfully.'

        );


    }


}