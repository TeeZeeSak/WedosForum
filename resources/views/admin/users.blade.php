@extends('layouts.app')

@section('title', 'WedosForum • admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div>
            <div class="card">
                <div class="card-header">Administration - Users | <a href="/home" style="color:green">Usermenu</a><a style="float: right;" href="/logout">Logout</a></div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form action="/admin/users/search" method="get" class="search-users">
                        @csrf
                        <i class="fa-solid fa-magnifying-glass search-users"></i>
                        <input type="text" autocomplete="off" placeholder="Username, email, id..." name="query"></input>
                    </form>
                    <a style="float:right; padding-top:5px;" href="/admin/users/new"><i class="fa-solid fa-plus"></i> New user</p></a>
                   <table class="admin-users">
                    <tr>
                        <th>User</th>
                        <th>Email</th>
                        <th>Created</th>
                        <th>Admin level</th>
                        <th></th>
                    </tr>
                    @foreach($users as $user)
                        <tr>
                            <td><img src="{{$user->avatar}}" width="64px" height="64px"/> {{$user->name}}</td>
                            <td>{{$user->email}}</td>
                            <td>{{$user->created_at}}</td>
                            <td>{{$user->administrator}}</td>
                            <td><i class="fa-solid fa-image" onClick="remove({{$user->id}}, '{{$user->name}}', '0');" style="cursor:pointer;" title="Remove profile picture"></i>&nbsp;&nbsp;&nbsp;<i class="fa-solid fa-user-slash" onClick="remove({{$user->id}}, '{{$user->name}}', '1');" style="color: red; cursor:pointer;" title="Ban user"></i></td>
                        </tr>
                    @endforeach
                   </table> 
                   
                </div>
            </div>
        </div>
    </div>
    <script>
        function remove(id, name, type){
            if(type == '0'){
                if(confirm("Remove " + name + "'s avatar?")){
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "/admin/users/delete",
                        type: 'POST',
                        data: {
                            id: id,
                            type: type,
                        },
                        success: function(result){
                            if(result["msg"] == "OK"){
                                location.reload();
                            }
                        }
                    });
                }
            }else{
                if(confirm("Remove " + name + "'s profile?")){
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "/admin/users/delete",
                        type: 'POST',
                        data: {
                            id: id,
                            type: type,
                        },
                        success: function(result){
                            if(result["msg"] == "OK"){
                                location.reload();
                            }
                        }
                    });
                }
            }
        }
    </script>
</div>
@endsection
