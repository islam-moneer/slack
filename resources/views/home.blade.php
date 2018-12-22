@inject('channels', 'App\Services\Slack')
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
                                            <input id="channels" type="channels" class="form-control{{ $errors->has('channels') ? ' is-invalid' : '' }}" name="channels" value="{{ old('channels') }}"  autofocus>
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
                                            <input id="user" type="user" class="form-control{{ $errors->has('user') ? ' is-invalid' : '' }}" name="user" value="{{ old('user') }}"  autofocus>
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
