@inject('usersObject', 'App\Services\Slack')
@inject('channelsObject', 'App\Services\Slack')
@extends('layouts.app')

@php 
$users = $usersObject->getUsersNames();
$channels = $channelsObject->getChannelsNames();
$sharedUsers = $channelsObject->getChannels();
if ($users['ok'] == true) {
    $users = $users['data'];
} else {
    print_r($users['data']);
}
if ($channels['ok'] == true) {
    $channels = $channels['data'];
} else {
    print_r($channels['data']);
}
@endphp


@section('css-styles')
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="/vendor/messenger/css/messenger.css">
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-3 threads">
            {{-- @include('messenger::partials.threads') --}}
        </div>

        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading"><h4>{{-- {{$withUser->name}} --}}</h4></div>

                <div class="panel-body">
                    <div class="messenger">
                         @if( is_array($response) )
                            @if (count($response) === 20)
                                <div id="messages-preloader"></div>
                            @endif

                        @else
                            <p class="start-conv">Conversation started</p>
                        @endif
                        <div class="messenger-body">
                            {{-- @php dd($response);  @endphp --}}
                            @foreach ($response as $message)
                                <div class="row message-row">
                                    <p style="color:#000">
                                        {{-- @php dd($users[$message->user]);  @endphp --}}
                                        <strong>{{ $users[$message->user] }}: </strong>
                                        {{$message->text}}
                                        
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="panel-footer">
                    <input type="hidden" name="channel" value="{{$withId}}">
                    @if($usersObject->getusers()->ok != true)
                        <li>Error in retrieve users</li>
                    @else
                    <select class="user" id="user-select" class="form-control{{ $errors->has('user') ? ' is-invalid ' : '' }}" name="user">
                            @foreach($usersObject->getUsers()->members as $user)
                                <option value="{{$user->id}}">{{$user->name}}</option>
                            @endforeach                                               
                    </select>
                    @endif
                    @if ($errors->has('user'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('user') }}</strong>
                    </span>
                    @endif
                    <textarea id="message-body" name="message" rows="2" placeholder="Type your message..."></textarea>
                    <button type="submit" id="send-btn" class="btn btn-primary">SEND</button>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="panel panel-default">
                <div class="panel-heading"><h4>Channel {{ $channels[$withId] }}</h4></div>

                <div class="panel-body">
                    <p>
                        <span>Users assigned to this channel</span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js-scripts')
    <script src="https://js.pusher.com/4.1/pusher.min.js"></script>
    <script type="text/javascript">
        var withId        = "{{$withId}}",
            messagesCount = {{is_array($response) ? count($response) : '0'}};
            pusher        = new Pusher('{{config('messenger.pusher.app_key')}}', {
              cluster: '{{config('messenger.pusher.options.cluster')}}'
            });
    </script>
    <script src="/vendor/messenger/js/slack-chat.js" charset="utf-8"></script>
@endsection
