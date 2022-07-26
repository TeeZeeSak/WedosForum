@extends('layouts.app')

@section('title', 'WedosForum • admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div>
            <div class="card">
                <div class="card-header">Administration - Topics | <a href="/home" style="color:green">Usermenu</a><a style="float: right;" href="/logout">Logout</a></div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form action="/admin/topics/search" method="get" class="search-users">
                        @csrf
                        <i class="fa-solid fa-magnifying-glass search-users"></i>
                        <input type="text" autocomplete="off" placeholder="Author, title, content" name="query"></input>
                    </form>
                   <table class="admin-users">
                    <tr>
                        <th>Author</th>
                        <th>Title</th>
                        <th>Content</th>
                        <th>Created</th>
                        <th></th>
                    </tr>
                    @foreach($topics as $topic)
                        <tr>
                            <td>{!! App\Models\User::findOrFail($topic->author_id)->name !!}</td>
                            <td style="max-width: 120px; text-overflow: ellipsis; overflow: hidden;white-space: nowrap;">{{$topic->title}}</td>
                            <td style="max-width: 120px; text-overflow: ellipsis; overflow: hidden;white-space: nowrap;">{{$topic->content}}</td>
                            <td>{{$topic->created_at}}</td>
                            <td><i class="fa-solid fa-thumbtack @if($topic->sticky)stickied @else notstickied @endif" onClick="stickyTopic({{$topic->id}}, '{{$topic->title}}', {{$topic->sticky}});" title="@if($topic->sticky) Unstick @else Stick @endif"></i>&nbsp;&nbsp;&nbsp;<i class="fa-solid fa-eye-slash @if($topic->visible)visib @else notvisib @endif" onClick="hideTopic({{$topic->id}}, '{{$topic->title}}', {{$topic->visible}});" title="@if($topic->visible)Hide @else Show @endif"></i>&nbsp;&nbsp;&nbsp;<i class="fa-solid fa-trash-can redhover" onClick="deleteTopic({{$topic->id}}, '{{$topic->title}}');" title="Delete"></i></td>
                        </tr>
                    @endforeach
                   </table> 
                   
                </div>
            </div>
        </div>
    </div>
    <script>
        function deleteTopic(id, topic){
            topic = topic.substring(0, Math.min(32,topic.length));
            if(confirm("Delete " + topic + "?")){
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "/admin/topics/delete",
                    type: 'POST',
                    data: {
                        id: id,
                    },
                    success: function(result){
                        if(result["msg"] == "OK"){
                            location.reload();
                        }else{
                            console.log(result);
                        }
                    }
                });
            }
        }

        function hideTopic(id, topic, visible){
            topic = topic.substring(0, Math.min(32,topic.length));
            let text = visible ? "Hide " : "Show " ;
            if(confirm(text + topic + "?")){
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "/admin/topics/hide",
                    type: 'POST',
                    data: {
                        id: id,
                    },
                    success: function(result){
                        if(result["msg"] == "OK"){
                            location.reload();
                        }else{
                            console.log(result);
                        }
                    }
                });
            }
        }

        function stickyTopic(id, topic, sticky){
        topic = topic.substring(0, Math.min(32,topic.length));
            let text = sticky ? "Hide " : "Show " ;
            if(confirm(text + topic + "?")){
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "/admin/topics/sticky",
                    type: 'POST',
                    data: {
                        id: id,
                    },
                    success: function(result){
                        if(result["msg"] == "OK"){
                            location.reload();
                        }else{
                            console.log(result);
                        }
                    }
                });
            }
        }
    </script>
</div>
@endsection
