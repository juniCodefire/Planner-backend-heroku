
<section style="width: 80%;margin: auto;height:700px;box-shadow: 0 0 10px #e6e6e6;color: grey;">
	<div id="head_1" style="background: #e6e6e6; height: 80px;">
		<h2 style="margin: 0;padding: 25px;color: skyblue;background: #e6e6e6;font-family:sans-serif;font-weight: bold;">GoalSetter</h2>
	</div>

	<div id="box" style="width: 95%; margin: auto;"><br>
		<h4>Dear {{$user->name}}</h4>
		<div>
				<div>
					<p>Thank you for creating a GoalSetter account. Please review this email in its entirety as it contains important information.</p>
				</div>

				<div>

				   <h4>Logging In</h4>

						<p>You can access our webapp Here: <a style="color: black;font-weight: bold;" href="https://goalsetter.com/" target="_blank">Open Now</a>
                        </p>

						<p>You will need your email address and the password you choose during signup to login.</p>

				</div>

				<div>

				   <h4>Confirm Account</h4>
				   			<!-- https://goalsetterapi.herokuapp.com -->
						<p>Follow this link to confirm you account before you can be able to login...</p>

						<a style=" text-decoration: none;padding: 10px;background: skyblue;color: white;font-weight: bold;"href="https://goalsetterapi.herokuapp.com/api/confirmation/{{$user->confirm_token}}" target="_blank">Click To Confirm...</a> 
				</div>

				<div>

						<h4>Getting Support</h4>

						<p>If you need any help or assistance, you can access our support resources below.</p><br>

						<li style="list-style: none;display: inline-block;"><a style=" text-decoration: none;padding: 10px;background: skyblue;color: white;font-weight: bold;" href="https://www.goalsetter.com/knowledgebase" target="_blank">Knowledgebase</a></li>

						<li style="list-style: none;display: inline-block;"><a style=" text-decoration: none;padding: 10px;background: skyblue;color: white;font-weight: bold;" href="https://www.goalsetter.com/support" target="_blank">Submit a Ticket</a></li>


						<li style="list-style: none;display: inline-block;"><a style=" text-decoration: none;padding: 10px;background: skyblue;color: white;font-weight: bold;" href="https://www.goalsetter.com/download" target="_blank">Download App</a></li>

						<br><br>
						www.goalsetter.com
				 </div>
		</div>
    </div>
</section>
</html>

