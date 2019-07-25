
<section style="width: 100%;margin: auto;height:700px;box-shadow: 0 0 10px #e6e6e6;color: grey;">
	<div id="head_1" style="background: #343a40; height: 80px;">
		<h2 style="margin: 0;padding: 25px; color: #e6e6e6; background: #343a40;font-family:sans-serif;font-weight: bold;">Plannerr</h2>
	</div>

	<div id="box" style="width: 95%; margin: auto; color:#343a40;"><br>
		<h4>Dear {{$user->name}}</h4>
		<div>
				<div>
					<p>Thank you for creating a Plannerr account. Please review this email in its entirety as it contains important information.</p>
				</div>

				<div>

				   <h4>Logging In</h4>

						<p>You can access our webapp Here: <a style="color: black;font-weight: bold;" href="https://plannerr-fbf4a.firebaseapp.com" target="_blank">Open Now</a>
                        </p>

						<p>You will need your email address and the password you choose during signup to login.</p>

				</div>

				<div>

				   <h4>Confirm Account</h4>
						<p>Follow this link to activate you account before you can be able to login...</p>

						<a style=" text-decoration: none;padding: 10px;background: #343a40;color: white;font-weight: bold;"href="https://plannerr-fbf4a.firebasssseapp.com/onboard/signup.html?confirm_token={{$user->confirm_token}}" target="_blank">Account Activation</a>
				</div>

				<div>

						<h4>Getting Support</h4>

						<p>If you need any help or assistance, you can access our support resources below.</p><br>

						<li style="list-style: none;display: inline-block;"><a style=" text-decoration: none;padding: 10px;background:#343a40;color: white;font-weight: bold;" href="https://plannerr-fbf4a.firebaseapp.com/knowledgebase" target="_blank">Knowledgebase</a></li>

						<li style="list-style: none;display: inline-block;"><a style=" text-decoration: none;padding: 10px;background:#343a40;color: white;font-weight: bold;" href="https://plannerr-fbf4a.firebaseapp.com/support" target="_blank">Submit a Ticket</a></li>


						<li style="list-style: none;display: inline-block;"><a style=" text-decoration: none;padding: 10px;background:#343a40;color: white;font-weight: bold;" href="https://plannerr-fbf4a.firebaseapp.com/download" target="_blank">Download App</a></li>

						<br><br>
						https://plannerr-fbf4a.firebaseapp.com
				 </div>
		</div>
    </div>
</section>
</html>
