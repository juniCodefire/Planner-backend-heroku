<!DOCTYPE html>
<html lang="">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>WorkSpace Request</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

        <!-- Styles -->
        <style>
						.distint {
							color: hotpink;
							font-weight: bold;
							font-size: 15px;
						}
            .activation1 {
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
					<h4>Hello {{$requestee->name}}</h4>
					<div>
							<div>
								<p> A new request from
									<span class="distint">{{$requester->name }}</span>  with email
									<span class="distint">{{ $requester->email }}</span> to join your workspace
									<span class="distint" style="color:dodgerblue;">Title: {{$workspace->title}} (Unique Name; {{$workspace->unique_name}})</span>
								</p>
							</div>
  							<div class="activation1">
                   <br>
  			           <a class="link_out"
                    href="https://plannerr-fbf4a.firebaseapp.com/onboard/signin.html#request" target="_blank">Reply To Request</a>
  							</div>
              <div id="third_block" class="activation2">
                  <h4>Getting Support</h4>
                  <p>If this email was not authourize by you, please kindy delete ...</p><br>
                  Regards Plannerr Team. https://plannerr-fbf4a.firebaseapp.com
              </div>
					</div>
			    </div>
			</section>
		</body>
	</html>
