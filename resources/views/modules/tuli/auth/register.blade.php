@extends('layouts.app')

@section('title', 'Student Registration')

@section('content')
<div class="mx-auto max-w-lg space-y-6 py-8">

    <div class="text-center">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
            Student Registration
        </h1>

        <p class="mt-2 text-sm text-slate-600">
            Create your account to access project ideas, team matching, and dashboard.
        </p>
    </div>


    @if($errors->any())

        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">

            <ul class="list-disc pl-5 space-y-1">

                @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif




    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">


        <form method="POST" action="{{ route('register.store') }}" class="space-y-4">

            @csrf



            <!-- Name -->

            <div>

                <label class="block text-sm font-semibold text-slate-700">
                    Full Name <span class="text-red-500">*</span>
                </label>


                <input
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    required
                    placeholder="e.g. Tuli Saha"
                    class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2.5"
                >

            </div>





            <!-- Email -->

            <div>

                <label class="block text-sm font-semibold text-slate-700">
                    University Email <span class="text-red-500">*</span>
                </label>


                <input
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    placeholder="student@g.bracu.ac.bd"
                    class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2.5"
                >

            </div>





            <!-- Role -->

            <div>

                <label class="block text-sm font-semibold text-slate-700">
                    Register As <span class="text-red-500">*</span>
                </label>


                <select
                    name="role"
                    required
                    class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2.5">


                    <option value="">
                        Select Role
                    </option>


                    <option value="student"
                    {{ old('role') == 'student' ? 'selected' : '' }}>
                        Student
                    </option>


                    <option value="tutor"
                    {{ old('role') == 'tutor' ? 'selected' : '' }}>
                        Tutor
                    </option>


                </select>

            </div>





            <!-- Student ID -->

            <div>

                <label class="block text-sm font-semibold text-slate-700">
                    Student ID
                </label>


                <input
                    type="text"
                    name="student_id"
                    value="{{ old('student_id') }}"
                    placeholder="e.g. 20231234"
                    class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2.5"
                >

            </div>





            <!-- Phone -->

            <div>

                <label class="block text-sm font-semibold text-slate-700">
                    Phone Number
                </label>


                <input
                    type="text"
                    name="phone"
                    value="{{ old('phone') }}"
                    placeholder="e.g. 017xxxxxxxx"
                    class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2.5"
                >

            </div>





            <!-- Password -->

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">


                <div>

                    <label class="block text-sm font-semibold text-slate-700">
                        Password <span class="text-red-500">*</span>
                    </label>


                    <input
                        type="password"
                        name="password"
                        required
                        class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2.5"
                    >

                </div>



                <div>

                    <label class="block text-sm font-semibold text-slate-700">
                        Confirm Password <span class="text-red-500">*</span>
                    </label>


                    <input
                        type="password"
                        name="password_confirmation"
                        required
                        class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2.5"
                    >

                </div>


            </div>





            <!-- Department -->

            <div>

                <label class="block text-sm font-semibold text-slate-700">
                    Department
                </label>


                <input
                    type="text"
                    name="department"
                    value="{{ old('department', 'Computer Science & Engineering') }}"
                    class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2.5"
                >

            </div>





            <!-- Skills -->

            <div>

                <label class="block text-sm font-semibold text-slate-700">
                    Technical Skills (Comma Separated)
                </label>


                <input
                    type="text"
                    name="skills"
                    value="{{ old('skills') }}"
                    placeholder="Python, React, Laravel"
                    class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2.5"
                >

            </div>





            <!-- Interests -->

            <div>

                <label class="block text-sm font-semibold text-slate-700">
                    Interests (Comma Separated)
                </label>


                <input
                    type="text"
                    name="interests"
                    value="{{ old('interests') }}"
                    placeholder="AI, Web Apps, Hackathons"
                    class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2.5"
                >

            </div>





            <!-- Bio -->

            <div>

                <label class="block text-sm font-semibold text-slate-700">
                    Bio / About Yourself
                </label>


                <textarea
                    name="bio"
                    rows="3"
                    class="mt-1 w-full rounded-xl border border-slate-300 px-3.5 py-2.5"
                >{{ old('bio') }}</textarea>


            </div>





            <div class="pt-2">

                <button
                    type="submit"
                    class="w-full rounded-xl px-5 py-3 text-sm font-bold shadow-md"
                    style="background-color:#2563eb;color:white;">

                    Register Account

                </button>

            </div>



        </form>




        <div class="mt-6 border-t border-slate-100 pt-4 text-center text-xs text-slate-500">

            Already registered?

            <a href="{{ route('login') }}"
               class="font-bold text-blue-600 hover:underline">

                Log In Here

            </a>

        </div>



    </div>

</div>

@endsection