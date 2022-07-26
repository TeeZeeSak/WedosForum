@extends('layouts.app')

@section('title', 'WedosForum • admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Administration | <a href="/home" style="color:green">Usermenu</a><a style="float: right;" href="/logout">Logout</a></div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    
                    
                    <div class="menu">
                        <div><a href="/admin/users">Users</a></div>
                        <div><a href="/admin/topics">Topics</a></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</div>
@endsection
