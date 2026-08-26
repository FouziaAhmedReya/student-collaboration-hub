<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Report;


class AdminController extends Controller
{

    public function dashboard()
    {

        $totalUsers = User::count();


        $totalStudents = User::where(
            'role',
            'student'
        )->count();


        $totalTutors = User::where(
            'role',
            'tutor'
        )->count();



        $pendingTutors = User::where(
            'role',
            'tutor'
        )
        ->where(
            'status',
            'pending'
        )
        ->get();



        return view(
            'common.admin.dashboard',
            compact(
                'totalUsers',
                'totalStudents',
                'totalTutors',
                'pendingTutors'
            )
        );

    }





    public function approveTutor($id)
    {

        $user = User::findOrFail($id);


        $user->status = 'approved';


        $user->save();



        return back()->with(
            'success',
            'Tutor approved successfully.'
        );

    }





    public function rejectTutor($id)
    {

        $user = User::findOrFail($id);


        $user->status = 'rejected';


        $user->save();



        return back()->with(
            'success',
            'Tutor rejected successfully.'
        );

    }
public function reports()
{

    $reports = Report::with([
        'reporter',
        'reportedUser'
    ])
    ->orderBy(
        'created_at',
        'desc'
    )
    ->get();


    return view(
        'common.admin.reports',
        compact('reports')
    );

}





public function resolveReport($id)
{

    $report = Report::findOrFail($id);


    $report->status = 'resolved';


    $report->save();


    return back()->with(
        'success',
        'Report marked as resolved.'
    );

}





public function rejectReport($id)
{

    $report = Report::findOrFail($id);


    $report->status = 'rejected';


    $report->save();


    return back()->with(
        'success',
        'Report rejected.'
    );

}

}