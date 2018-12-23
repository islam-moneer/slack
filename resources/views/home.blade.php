@inject('channels', 'App\Services\Slack')
@inject('users', 'App\Services\Slack')
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
            @include('layouts.sidebar')
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">Slack operations</div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    @if (session()->has('error'))
                        <ul class="alert alert-danger">
                            <li>{{ session('error') }}</li>
                        </ul>
                    @endif
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card">
                            @if (session('log'))
                                <pre>
                                    {{ session('log') }}
                                </pre>
                            @endif
                            @if ($errors->any())
                                <ul class="alert alert-danger">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            @endif
                            <!-- Create Channel -->
                            <div class="card-header">Create Channel</div>
                            <div class="card-body">
                                <form method="POST" action="{{ route('slack.create') }}">
                                    @csrf
                                    <div class="form-group row">
                                        <label for="channel" class="col-sm-4 col-form-label text-md-right">Channel Name</label>
                                        <div class="col-md-6">
                                            <input id="channel" type="channel" class="form-control{{ $errors->has('channel') ? ' is-invalid' : '' }}" name="channel" value="{{ old('channel') }}"  autofocus>
                                            @if ($errors->has('channel'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('channel') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-group row mb-0">
                                        <div class="col-md-8 offset-md-4">
                                            <button type="submit" class="btn btn-primary">
                                                Submit
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- Invite user to channel -->
                            <div class="card-header">Invite User</div>
                            <div class="card-body">
                                <form method="POST" action="{{ route('slack.invite') }}">
                                    @csrf
                                    <div class="form-group row">
                                        <label for="channels" class="col-sm-4 col-form-label text-md-right">Channel</label>
                                        <div class="col-md-6">
                                            {{-- Channels --}}
                                            @if($channels->getChannels()->ok != true)
                                                <li>Error in retrieve channels</li>
                                                @foreach($channels->getChannels() as $channel)
                                                    <li>@php print_r($channel)@endphp</li>
                                                @endforeach
                                                @else
                                            <select class="channels" id="channels" class="form-control{{ $errors->has('channels') ? ' is-invalid ' : '' }}" name="channels">
                                                    @foreach($channels->getChannels()->channels as $channel)
                                                        <option value="{{$channel->id}}">{{$channel->name}}</option>
                                                    @endforeach                                               
                                            </select>
                                            @endif
                                            @if ($errors->has('channels'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('channels') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="user" class="col-sm-4 col-form-label text-md-right">User</label>
                                        <div class="col-md-6">
                                            @if($users->getusers()->ok != true)
                                                <li>Error in retrieve users</li>
                                                @foreach($users->getUsers() as $user)
                                                    <li>@php print_r($user)@endphp</li>
                                                @endforeach
                                                @else
                                            <select class="user" id="user" class="form-control{{ $errors->has('user') ? ' is-invalid ' : '' }}" name="user">
                                                    @foreach($users->getUsers()->members as $user)
                                                        <option value="{{$user->id}}">{{$user->name}}</option>
                                                    @endforeach                                               
                                            </select>
                                            @endif
                                            @if ($errors->has('user'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('user') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-group row mb-0">
                                        <div class="col-md-8 offset-md-4">
                                            <button type="submit" class="btn btn-primary">
                                            {{ __('Submit') }}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
