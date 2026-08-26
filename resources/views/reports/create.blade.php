<!DOCTYPE html>

<html>

<head>

<title>
Report User
</title>

</head>


<body>


<h1>
Report {{ $user->name }}
</h1>



<form method="POST" action="{{ route('report.store') }}">

@csrf


<input 
type="hidden"
name="reported_user_id"
value="{{ $user->id }}">



<label>
Reason
</label>


<input
type="text"
name="reason"
required>



<br><br>


<label>
Description
</label>


<textarea
name="description"></textarea>



<br><br>


<button type="submit">

Submit Report

</button>



</form>


</body>

</html>