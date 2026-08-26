<!DOCTYPE html>
<html>

<head>

    <title>
        Admin Dashboard
    </title>

    <style>

        body {
            font-family: Arial, sans-serif;
            padding: 40px;
        }

        .card {

            border: 1px solid #ddd;
            padding: 20px;
            margin: 15px 0;
            border-radius: 10px;

        }

    </style>

</head>


<body>


<h1>
    Admin Dashboard
</h1>



<div class="card">

    <h3>
        Total Users
    </h3>

    <p>
        {{ $totalUsers }}
    </p>

</div>



<div class="card">

    <h3>
        Total Students
    </h3>

    <p>
        {{ $totalStudents }}
    </p>

</div>




<div class="card">

    <h3>
        Total Tutors
    </h3>

    <p>
        {{ $totalTutors }}
    </p>

</div>



</body>

</html>
<hr>


<h2>
    Pending Tutor Requests
</h2>


@if($pendingTutors->count() > 0)


<table border="1" cellpadding="10">

<tr>

<th>Name</th>

<th>Email</th>

<th>Action</th>

</tr>



@foreach($pendingTutors as $tutor)


<tr>

<td>
{{ $tutor->name }}
</td>


<td>
{{ $tutor->email }}
</td>


<td>


<form method="POST"
action="{{ route('admin.tutor.approve',$tutor->id) }}"
style="display:inline">

@csrf

<button type="submit">
Approve
</button>

</form>



<form method="POST"
action="{{ route('admin.tutor.reject',$tutor->id) }}"
style="display:inline">

@csrf

<button type="submit">
Reject
</button>

</form>


</td>


</tr>


@endforeach


</table>


@else

<p>
No pending tutors.
</p>


@endif