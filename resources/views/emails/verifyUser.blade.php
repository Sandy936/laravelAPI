<!DOCTYPE html>
<html>
<head>
    <title>Signup Invitation</title>
</head>

<body>
<h2>Welcome {{$user['email']}}</h2>
<br/>
Your email-id is {{$user['email']}} , Please click on the below link to signup.
<br/>
<a href="{{url('api/sign-up', $user->verifyUser->token)}}">SignUp</a>
</body>

</html>