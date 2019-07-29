<!DOCTYPE html>
<html lang="">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

        <!-- Styles -->
        <style>
						.distint {
							color: hotpink;
							font-weight: bold;
							font-size: 15px;
						}
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
					<h4>Hello {{$requestee->name}}</h4>
					<div>
							<div>
								<p> A new request from
									<span class="distint">{{$requester->name }}</span>  with email
									<span class="distint">{{ $requester->email }}</span> to join your workspace
									<span class="distint" style="color:dodgerblue;">{{$workspace->title}} ({{$workspace->unique_name}})</span>
								</p>
							</div>
							<div class="activation">
							   <h4>Accept or Reject request</h4>
			           <a style=" text-decoration: none;padding: 15px;background: #343a40;color: white;font-weight: bold;" href="https://plannerr-fbf4a.firebaseapp.com/onboard/signin.html#request" target="_blank">Accept Or Reject</a>
							</div>
              <div id="third_block" class="activation">
                  <h4>Getting Support</h4>
                  <p>If this email was not authourize by you, please kindy delete ...</p><br>
                  Regards Plannerr Team. https://plannerr-fbf4a.firebaseapp.com
              </div>
					</div>
			    </div>
			</section>
		</body>
	</html>
