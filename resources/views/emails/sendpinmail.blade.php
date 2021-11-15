<!DOCTYPE html>
<html>
<head>
    <title>Verification Pin</title>
</head>

<body>
<h1>{{$user['sixdigitpin']}}</h1>
<br/>
Please open the below link to enter this six didgit pin to complete the registration.
<br/>
<a href="{{url('api/verify-pin')}}">{{url('verify-pin')}}</a>
</body>

</html>