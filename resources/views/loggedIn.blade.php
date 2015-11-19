@extends('layouts.master')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Welcome</div>
                    <div class="panel-body">
                        Hi {{ Auth::user()->name }},

                        You have successfully logged in!

                        <a href="{{URL::to('/')}}/logout">Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection