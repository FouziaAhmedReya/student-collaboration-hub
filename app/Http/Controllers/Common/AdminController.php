<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Models\User;


class AdminController extends Controller
{

    /**
     * Admin Dashboard
     */
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



        return view(
            'common.admin.dashboard',
            compact(
                'totalUsers',
                'totalStudents',
                'totalTutors'
            )
        );

    }

}
