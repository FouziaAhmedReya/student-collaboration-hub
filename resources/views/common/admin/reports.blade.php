<!DOCTYPE html>

<html>

<head>

<title>
Admin Reports
</title>

</head>


<body>


<h1>
Reports Management
</h1>



<table border="1" cellpadding="10">


<tr>

<th>
Reporter
</th>

<th>
Reported User
</th>

<th>
Reason
</th>

<th>
Description
</th>

<th>
Status
</th>

<th>
Action
</th>

</tr>



@foreach($reports as $report)


<tr>


<td>
{{ $report->reporter->name }}
</td>


<td>
{{ $report->reportedUser->name }}
</td>


<td>
{{ $report->reason }}
</td>


<td>
{{ $report->description }}
</td>


<td>
{{ $report->status }}
</td>


<td>


<form method="POST"
action="{{ route('admin.report.resolve',$report->id) }}">

@csrf

<button>
Resolve
</button>

</form>



<form method="POST"
action="{{ route('admin.report.reject',$report->id) }}">

@csrf

<button>
Reject
</button>

</form>



</td>


</tr>


@endforeach


</table>


</body>

</html>