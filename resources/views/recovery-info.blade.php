
<section style="width: 80%;margin: auto;height:600px;box-shadow: 0 0 10px #e6e6e6;color: grey;">
	<div id="head_1" style="background: #e6e6e6; height: 80px;">
		<h2 style="margin: 0;padding: 25px;color: skyblue;background: #e6e6e6;font-family:sans-serif;font-weight: bold;">GoalSetter</h2>
	</div>

	<div id="box" style="width: 95%; margin: auto;"><br>
		<h4>Dear {{$user->name}}</h4>
		<div>
				<div id="first_block">
					<p>You are getting this email because you have requested for the resetting of your forgotting password.</p>
				</div>

				<div id="third_block">

				   <h4>Password Verification Code</h4>
				   			<!-- https://goalsetterapi.herokuapp.com -->
						<p>Use verification code to get a new password...</p>
						<li style="list-style: none;display: inline-block;"><p style=" text-decoration: none;padding: 10px;background: skyblue;color: white;font-weight: bold;">{{$user->verify_code}}</p></li>
						

				</div>

				<div id="third_block">

						<h4>Getting Support</h4>

						<p>If this email was not authourize by you, please kindy delete ...</p><br>

						Regards GoalSetter Team.
				 </div>
		</div>
    </div>
</section>
</html>
