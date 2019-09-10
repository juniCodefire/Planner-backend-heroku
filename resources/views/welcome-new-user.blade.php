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
          align-items: center;
          text-align: center;
        }
        .link_out {
                text-decoration: none;
                padding: 15px 50px 15px 50px;
                background: #343a40;
                width: 100%;
                height: 10vh;
                border-radius: 5px;
                color: white !important;
                font-weight: bold;
             }
        .head_1 {
          background: #343a40;
          height: 80px;
          border-radius: 5px;
        }
        .head_1 h2 {
          margin: 0;padding: 25px; color: #e6e6e6;font-family:sans-serif;font-weight: bold;
        }
        </style>
    </head>
    <body>
      <section style="width: 100%;margin: auto;height:auto; box-shadow: 0 0 10px #e6e6e6; color: #e6e6e6;">
        <div class="head_1">
          <h2>Plannerr</h2>
        </div>


      	<div id="box" style="width: 95%; margin: auto; color:#343a40;"><br>
      		<h4>Hi {{$user->name}}</h4>
      		<div>
      				<div>
      					<p>Thank you for creating a Plannerr account. Please review this email in its entirety as it contains important information.</p>
      				</div>

      			<div class="activation">
               <br>
      						<a class="link_out" href="https://plannerr-fbf4a.firebaseapp.com/onboard/get_started.html?confirm_token={{$user->confirm_token}}#workspace-tab" target="_blank">Account Activation</a>
      			</div>
            <div id="third_block" class="activation">
                <p>This email was sent to you because you create a new plannerr account...</p><br>
                Regards Plannerr Team. https://plannerr-fbf4a.firebaseapp.com
            </div>
      		</div>
          </div>
      </section>
    </body>
</html>
