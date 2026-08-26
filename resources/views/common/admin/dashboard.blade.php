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