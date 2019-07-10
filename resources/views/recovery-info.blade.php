
<section style="width: 100%;margin: auto;height:600px; box-shadow: 0 0 10px #e6e6e6; color: #e6e6e6;">
	<div id="head_1" style="background: #343a40; height: 80px;">
		<h2 style="margin: 0;padding: 25px; color: #e6e6e6; background: #343a40; font-family:sans-serif;font-weight: bold;">Plannerr</h2>
	</div>

	<div id="box" style="width: 95%; margin: auto; color:#343a40;"><br>
		<h4>Dear {{$user->name}}</h4>
		<div>
				<div id="first_block">
					<p>You are getting this email because you have requested for the resetting of your forgotting password.</p>
				</div>

				<div id="third_block">

				   <h4>Password Verification Code</h4>
						<p>Use verification code to get a new password...</p>
						<a style=" text-decoration: none;color: white;font-weight: bold;" href="https://plannerr-fbf4a.firebaseapp.com/onboard/confirmation-code.html?verify_code={{$user->verify_code}}" target="_blank">
						<li style="list-style: none;display: inline-block;">
						<p style=" text-decoration: none;padding: 10px;background: #343a40;color: white;font-weight: bold;">Reset Password</p>
						</li>Account Activation</a> 
						

				</div>

				<div id="third_block">

						<h4>Getting Support</h4>

						<p>If this email was not authourize by you, please kindy delete ...</p><br>

						Regards Plannerr Team.
				 </div>
		</div>
    </div>
</section>
</html>
