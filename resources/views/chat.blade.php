@extends('layouts.app')
@section('content')
<div class="card-body">
	@if (session()->has('error'))
        <ul class="alert alert-danger">
            <li>{{ session('error') }}</li>
        </ul>
    @endif
</div>
@php $channelId = (isset($id)) ? $id : ''; @endphp
<div id="chat" style="width: 40%; margin: auto">
<a href="{{ url('/home') }}" style="padding: 5px;
    background-color: #15ca0a;
    border-radius: 10%;
    margin: 10px;
    color: #fff;
    margin-bottom: 20px;">Back</a>
	<div id="chat-window" style="overflow-y: scroll; height:300px;max-height: 100vh; background-color: #f3f3f3; border: 1px solid #ccc; margin: 10px">
		<div id="output">
			@if(is_array($history))
				@foreach($history as $message)
					@php 
						if ( isset($message->user) ) {
							$user = $message->user;
						} elseif ( isset($message->username) ) {
							$user = $message->username;
						} else {
							continue;
						}
					@endphp
					<p>
						<strong>{{$user}}: </strong>
						{{ $message->text }}
					</p>
				@endforeach
			@endif
		</div>

	</div>
	@csrf
	<input id="message" class="form-control" type="text" placeholder="Message" />
	<input id="channel" type="hidden" value="{{$channelId}}" />
	<input id="user" type="hidden" value="{{$channelId}}" />
	<button class="btn btn-primary" id="send">Send</button>
</div>
@endsection

@section('js-scripts')
<script>
    var websocket = new WebSocket("{{$url}}");
	var elem = document.getElementById('chat-window');

	var btn = document.getElementById('send');
		output = document.getElementById('output');
		user = $("#user").val();
		channel = $("#channel").val();


	elem.scrollTop = elem.scrollHeight;
	btn.addEventListener('click', function() {
		/** 
		 * Send message request to slack
		 */
		 message = $("#message").val();
		 var data = {
		 	"message": message,
		 	"user": user,
		 	"channel": channel,
		 	"_token": "{{ csrf_token() }}",
		 };
		 $.ajax({
		 	type: 'POST',
		 	url: "{{ env('APP_URL') }}" + "/send-message/" + user,
		 	data: data,
		 	success: function(response){
		 		// console.log(response)
		 		if ( response.status == true) {
					$("#message").val('');
		 		}
		 	},
		 	dataType: "json"
		 });
		
	});

	

	// listen to slack response
	websocket.onmessage = function(str) {
	  // console.log(JSON.parse(str.data));
	  if ( JSON.parse(str.data).type == 'message' ) {
	  	data = JSON.parse(str.data);
	  	if ( data.channel == "{{$channelId}}" ) {
		  	var p = document.createElement("p");
		  	userName = (data.user) ? data.user : data.username;
		  	p.innerHTML = '<strong>' + userName + ': </strong>' + data.text;
	  		output.appendChild( p );
	  		// var elem = document.getElementById('chat-window');
	  		elem.scrollTop = elem.scrollHeight;
	  	}
	  }
	};
</script>
@endsection