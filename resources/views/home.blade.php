@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif


            <div class="panel panel-default">
                <div class="panel-heading">Email Verification</div>

                <div class="panel-body">
                    @if($user->is_email_verified)
                        <p>Your email address {{ $user->email }} is verified.</p>
                    @else
                        <p>Your email address {{ $user->email }} is not verified.</p>

                        <form class="form-horizontal" method="POST" action="{{ route('user.verify') }}">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <div class="col-md-8 col-md-offset-4">
                                    <button class="btn btn-default">Resend Verification Email</button>
                                </div>
                            </div>
                        </form>
                    @endif

                    <form class="form-horizontal" method="POST" action="{{ route('user.email') }}">
                        {{ csrf_field() }}
                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">E-Mail Address</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email', $user->email) }}" required autofocus>

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-8 col-md-offset-4">
                                <button class="btn btn-primary">Change</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">Email Subscription</div>

                <div class="panel-body">

                    <form class="form-horizontal" method="POST" action="{{ route('user.subscribe') }}">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="subscribe" value="1" {{ $user->is_email_subscribed ? 'checked' : '' }}> Receive Notifications
                                    </label>
                                </div>
                            </div>

                            @if ($errors->has('subscribe'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('subscribe') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-group">
                            <div class="col-md-8 col-md-offset-4">
                                <button class="btn btn-primary">Change</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

                <div class="panel panel-default">
                    <div class="panel-heading">Test Notification Sending</div>

                    <div class="panel-body">

                        <form class="form-horizontal" method="POST" action="{{ route('user.test.email') }}">
                            {{ csrf_field() }}

                            <div class="form-group">
                                <div class="col-md-8 col-md-offset-4">
                                    <button class="btn btn-primary">Send</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
        </div>
    </div>
</div>
@endsection
