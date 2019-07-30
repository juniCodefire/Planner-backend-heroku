<!DOCTYPE html>
<html lang="">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Welcome</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

        <!-- Styles -->
        <style>
          .activation {
            justify-content: center;
            align-content: center;
          }
        </style>
    </head>
		<body>
<section style="width: 100%;margin: auto;height:700px;box-shadow: 0 0 10px #e6e6e6;color: grey;">
	<div id="head_1" style="background: #343a40; height: 80px;">
		<h2 style="margin: 0;padding: 25px; color: #e6e6e6; background: #343a40;font-family:sans-serif;font-weight: bold;">Plannerr</h2>
	</div>

	<div id="box" style="width: 95%; margin: auto; color:#343a40;"><br>
		<h4>Hi {{$user->name}}</h4>
		<div>
				<div>
					<p>Thank you for creating a Plannerr account. Please review this email in its entirety as it contains important information.</p>
				</div>

			<div class="activation">
				   <h4>Activate Account</h4>
						<a style=" text-decoration: none;padding: 15px;background: #343a40;color: white;font-weight: bold;"href="https://plannerr-fbf4a.firebaseapp.com/onboard/signup.html?confirm_token={{$user->confirm_token}}#workspace-tab" target="_blank">Account Activation</a>
			</div>
      <div id="third_block" class="activation">
          <p>This email was sent to you because you create a new plannerr account...</p><br>
          Regards Plannerr Team. https://plannerr-fbf4a.firebaseapp.com
      </div>
		</div>
    </div>
</section>
</html>
