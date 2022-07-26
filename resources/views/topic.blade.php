@extends("layouts.app")

@section('title')
WedosForum • {{$topic->title }}
@endsection

@section("content")
@php
            

            $now = date("Y-m-d H:i:s");
            function dateDifference($date_1 , $date_2 , $diff = '%a' )
            {
                $datetime1 = date_create($date_1);
                $datetime2 = date_create($date_2);
                $differenceFormat = $diff;
                $interval = date_diff($datetime1, $datetime2);
                $format = $interval->format($differenceFormat);

                if($differenceFormat == '%a' && intval($format) > 364 ){
                    $format_years = $interval->format('%y');
                    if(intval($format_years != 1)){
                        return "$format_years years ago";
                    }else{
                        return "1 year ago";
                    }
                }else if($differenceFormat == '%a' && intval($format) < 1 ){
                    //get hours:
                    $format_hours = $interval->format('%h');
                    
                    if(intval($format_hours) - 1< 1){
                        $format_minutes = $interval->format('%i');
                        if(intval($format_minutes) != 1)
                            return "less than an hour ago";
                    }else{
                        if(intval($format_hours) != 1)
                            return "$format_hours hours ago";
                        else
                            return "1 hour ago"; 
                    }
                }
                
                if(intval($format) != 1){
                    return "$format days ago";
                }else{
                    return "1 day ago";
                }
                return $interval->format($differenceFormat);
            
            }

            function dateLogic($date){
                $unformatted = date("Y", strtotime($date));
                

                if(strval(date("Y")) != $unformatted){
                    return date("d M Y", strtotime($date));
                }else{
                    return date("d M", strtotime($date));
                }
            }
            @endphp

    <div class="content">
        @if(session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif

        <script>
            function scrollToBottom(){
                window.scrollTo(0, document.body.scrollHeight);
                document.getElementById("replyBody").focus() ;
            }

            function quote(id){
                window.scrollTo(0, document.body.scrollHeight);
                let replyBody =document.getElementById("replyBody") ;
                replyBody.value = "[quote]" + document.getElementById("top-body-" + id).innerHTML + "[/quote]\n";
                
                replyBody.focus();

            }
        </script>

        <button onClick="scrollToBottom()" class="btn-reply"><i class="fa-solid fa-plus"></i> Post a Reply</button>
            
        <div class="content-topics">
            <div class="topic-top">
                <p class="topic-title">{{ $topic->title }}</p><a href="/topics/{{ $topic->tag }}" class="topic-tag">{{ $topic->tag }}</a>
            </div>
            <div class="topic">
                <span class="topic-age">@php echo dateDifference($topic->created_at, $now); @endphp</span>

                <img src="{{ $author->avatar }}" class="topic-avatar"/>
                <div class="topic-body">
                    <div class="topic-header">
                        <div class="topic-info">
                            <h3 style="display: inline; font-size: 110%;"><a style="color:#212121;">{{ $author->name }}</a></h3>
                            @if($author->administrator)
                                <span style="color: #999999; font-size: 90%;">Administrator</span>
                            @endif
                            <span style="color: #999999">@php echo dateLogic($topic->created_at); @endphp</span>
                        </div>
                        <div class="topic-controls">
                            <i onclick="quote({{ $topic->id }})" class="fa-solid fa-quote-left reply-quote"></i>
                        </div>
                        
                    </div>
                    <div class="topic-content">
                        <p id="top-body-{{ $topic->id }}" class="topic-content">{{ $topic->content }}</p>
                        @if($topic->attachments != "")
                            <div onClick="window.location.href = '/uploads/{{ $topic->id }}/{{$topic->attachments}}';" class="attachment">
                                <i class="fa-solid fa-file-arrow-down topic-attachement"></i><br>
                                <p class="attachement">{{ $topic->attachments }}</p>
                            </div>
                        @endif
                    </div>
                    <div class="topic-footer">
                    @auth
                        @if(App\Models\Like::where('liked_by_id', auth()->user()->id)->where('like_type', 0)->where('liked_thing_id', $topic->id)->first())
                            @if($topic->likes == 1)
                                <p id="likeBtn" likes="{{ $topic->likes }}" onClick="like({{$topic->id}}, 0, this);">Liked</p><a class="likes-info"> · {{Auth::user()->name}} likes this.</a>
                            @elseif($topic->likes == 2)
                                <p id="likeBtn" likes="{{ $topic->likes }}" onClick="like({{$topic->id}}, 0, this);">Liked</p><a class="likes-info"> · {{Auth::user()->name}} and 1 other user likes this.</a>
                            @else if($topic->likes > 2)
                                <p id="likeBtn" likes="{{ $topic->likes }}" onClick="like({{$topic->id}}, 0, this);">Liked</p><a class="likes-info"> · You and {{$topic->likes}} other users like this.</a>
                            @endif
                        @else
                            @if($topic->likes == 1)
                                <p id="likeBtn" likes="{{ $topic->likes }}" onClick="like({{$topic->id}}, 0, this);">Like</p><a class="likes-info"> · 1 user likes this.</a>
                            @elseif($topic->likes > 1)
                                <p id="likeBtn" likes="{{ $topic->likes }}" onClick="like({{$topic->id}}, 0, this);">Like</p><a class="likes-info"> · {{ $topic->likes }} users like this.</a>
                            @else
                                <p id="likeBtn" likes="{{ $topic->likes }}" onClick="like({{$topic->id}}, 0, this);">Like</p><a class="likes-info"></a>
                            @endif
                        @endif
                    @endauth

                            @guest
                            @if($topic->likes == 1)
                            <p id="likeBtn" likes="{{ $topic->likes }}" onClick="like({{$topic->id}}, 0, this);">Like</p><a class="likes-info"> · 1 user likes this.</a>
                                @elseif($topic->likes > 1)
                                <p id="likeBtn" likes="{{ $topic->likes }}" onClick="like({{$topic->id}}, 0, this);">Like</p><a class="likes-info"> · {{ $topic->likes }} users like this.</a>
                                @else
                                <p id="likeBtn" likes="{{ $topic->likes }}" onClick="like({{$topic->id}}, 0, this);">Like</p><a class="likes-info"></a>
                                @endif
                             @endguest
                    </div>
                </div>
            </div>
            @foreach($replies as $reply)
                <div class="topic" style="margin-bottom: -20px !important;">
                   
                    <img src="{!! App\Models\User::findOrFail($reply->author_id)->avatar; !!}" class="topic-avatar"/>
                    <div class="topic-body">
                        <div class="topic-header">
                            <div class="topic-info">
                                <h3 style="display: inline; font-size: 110%;"><a style="color:#212121;">{{ App\Models\User::findOrFail($reply->author_id)->name }}</a></h3>
                                @if(App\Models\User::findOrFail($reply->author_id)->administrator)
                                    <span style="color: #999999; font-size: 90%;">Administrator</span>
                                @endif
                                <span style="color: #999999">@php echo dateLogic($reply->created_at); @endphp</span>
                            </div>
                            <div class="topic-controls">
                                <i onclick="quote({{ $reply->id }})" class="fa-solid fa-quote-left reply-quote"></i>
                            </div>
                            
                        </div>
                        <div class="topic-content">
                            <p id="top-body-{{ $reply->id }}" class="topic-content">{{ $reply->content }}</p>
                            @if($reply->attachments != "")
                            <div onClick="window.location.href = '/uploads/replies/{{ $reply->id }}/{{$reply->attachments}}';" class="attachment">
                                <i class="fa-solid fa-file-arrow-down topic-attachement"></i><br>
                                <p class="attachement">{{ $reply->attachments }}</p>
                            </div>
                            @endif
                        </div>
                        <div class="topic-footer">
                            @auth
                                @if(App\Models\Like::where('liked_by_id', auth()->user()->id)->where('like_type', 1)->where('liked_thing_id', $reply->id)->first())
                                    @if($reply->likes == 1)
                                        <p id="likeBtn" likes="{{ $reply->likes }}" onClick="like({{$reply->id}}, 1, this);">Liked</p><a class="likes-info"> · {{Auth::user()->name}} likes this.</a>
                                    @elseif($reply->likes == 2)
                                        <p id="likeBtn" likes="{{ $reply->likes }}" onClick="like({{$reply->id}}, 1, this);">Liked</p><a class="likes-info"> · {{Auth::user()->name}} and 1 other user likes this.</a>
                                    @else if($reply->likes > 2)
                                        <p id="likeBtn" likes="{{ $reply->likes }}" onClick="like({{$reply->id}}, 1, this);">Liked</p><a class="likes-info"> · You and {{$reply->likes}} other users like this.</a>
                                    @endif
                                @else
                                    @if($reply->likes == 1)
                                        <p id="likeBtn" likes="{{ $reply->likes }}" onClick="like({{$reply->id}}, 1, this);">Like</p><a class="likes-info"> · 1 user likes this.</a>
                                    @elseif($reply->likes > 1)
                                        <p id="likeBtn" likes="{{ $reply->likes }}" onClick="like({{$reply->id}}, 1, this);">Like</p><a class="likes-info"> · {{ $reply->likes }} users like this.</a>
                                    @else
                                        <p id="likeBtn" likes="{{ $reply->likes }}" onClick="like({{$reply->id}}, 1, this);">Like</p><a class="likes-info"></a>
                                    @endif
                                @endif
                            @endauth

                            @guest
                                @if($reply->likes == 1)
                                    <p id="likeBtn" likes="{{ $reply->likes }}" onClick="like({{$reply->id}}, 1, this);">Like</p><a class="likes-info"> · 1 user likes this.</a>
                                @elseif($reply->likes > 1)
                                    <p id="likeBtn" likes="{{ $reply->likes }}" onClick="like({{$reply->id}}, 1, this);">Like</p><a class="likes-info"> · {{ $reply->likes }} users like this.</a>
                                @else
                                    <p id="likeBtn" likes="{{ $reply->likes }}" onClick="like({{$reply->id}}, 1, this);">Like</p><a class="likes-info"></a>
                                @endif
                             @endguest
                        </div>
                    </div>
                </div>
            @endforeach
            <div class="topic-reply">
                @auth
                <img src="{{ Auth::user()->avatar }}" class="topic-avatar"/>
                @endauth

                @guest
                <img src="/uploads/default.jpg" class="topic-avatar"/>
                @endguest
                <div class="topic-body">
                    <div class="topic-content">
                        <form name="form-reply" enctype="multipart/form-data"  id="add-reply" method="post" action="/topic">
                                @csrf
                                <input type="hidden" name="topicid" value="{{ $topic->id }}">
                                <div class="topic-content" style="margin-left: -5px; margin-top: 5px;">
                                    @guest
                                    <p class="topic-content reply"><a href="/login">Log In</a> or <a href="/register">Sign Up</a> to reply!</p>
                                    @endguest
                                    @auth
                                    <textarea id="replyBody" rows="3" cols="25" name="content" autocomplete="off" class="topic-content reply" placeholder="Write a reply..."></textarea>
                                    @endauth
                                </div>
                                @auth
                                <div class="topic-footer reply">
                                    <input type="file"class="upload" id="uploadFile" name="file"><i style="margin-top: 10px;" onClick="document.getElementById('uploadFile').click();" title="Add an attachement" class="fa-solid fa-paperclip attachement"></i></input>
                                    <button class="btn-send" style="font-size: 90% !important; margin-top: 8px;" type="submit">Reply</button>
                                </div>
                                @endauth
                        </form>
                        <script>
                            
                                var area = document.getElementById('replyBody');
                                if (area.addEventListener) {
                                    @php
                                    if(!Auth::check()){
                                      
                                        echo "return;";
                                    }
                                    @endphp
                                    area.addEventListener('input', function() {
                                        
                                        let emptyLines = 0;
                                        let limit;
                                        let lines = area.value.split("\n", limit)
                                        
        
                                        area.rows = lines.length + 3;
                                    }, false);
                                }
                          
                            
                            
                            
                            function like(id, type, e){
                                @php
                                    if(!Auth::check()){
                                        echo "$(location).prop('href', '/login');";
                                        echo "return;";
                                    }
                                @endphp
                                $.ajaxSetup({
                                    headers: {
                                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                                    }
                                });
                                $.ajax({
                                    url: "/topic/like",
                                    type: 'POST',
                                    data: {
                                        id: id,
                                        type: type,
                                    },
                                    success: function(result){
                                        console.log(result);
                                        if(result["msg"] == "Unlike"){
                                            e.innerHTML = "Like";
                                            
                                            if(type == 1){
                                                @php
                                                if($topic->replies > 0){
                                                    $reply->likes -= 1;
                                                    if($reply->likes == 1){
                                                        echo 'e.nextSibling.innerHTML = " · 1 user likes this.";';
                                                    }else if($reply->likes > 1){
                                                        echo 'e.nextSibling.innerHTML = " · ' . $reply->likes . ' users like this.";';
                                                    }else{
                                                        echo 'e.nextSibling.innerHTML = "";';
                                                    }
                                                }
                                                @endphp
                                            }else{
                                                @php
                                                $topic->likes -= 1;
                                                if($topic->likes == 1){
                                                    echo 'e.nextSibling.innerHTML = " · 1 user likes this.";';
                                                }else if($topic->likes > 1){
                                                    echo 'e.nextSibling.innerHTML = " · ' . $topic->likes . ' users like this.";';
                                                }else{
                                                    echo 'e.nextSibling.innerHTML = "";';
                                                }
                                                @endphp
                                            }
                                        }else if(result["msg"] == "Like"){
                                            e.innerHTML = "Liked";
                                            if(type == 1){
                                                @php
                                                if(Auth::check()){
                                                    if($topic->replies > 0){
                                                        if($reply->likes == 1){
                                                            echo 'e.nextSibling.innerHTML = " · ' . Auth::user()->name . ' likes this.";';
                                                        }else if($reply->likes == 2){
                                                            echo 'e.nextSibling.innerHTML = " · ' . Auth::user()->name . ' and 1 other user likes this.";';
                                                        }else if($reply->likes > 2){
                                                            echo 'e.nextSibling.innerHTML = " · You and ' . $reply->likes . ' other users like this.";';
                                                        }else if($reply->likes == 0){
                                                            echo 'e.nextSibling.innerHTML = " · ' . Auth::user()->name . ' likes this.";';
                                                        }else{
                                                            echo 'e.nextSibling.innerHTML = " · ' . Auth::user()->name . ' likes this.";'; 
                                                        }
                                                    }
                                                }
                                                @endphp
                                            }else{
                                                @php
                                                if(Auth::check()){
                                                    if($topic->likes == 1){
                                                        echo 'e.nextSibling.innerHTML = " · ' . Auth::user()->name . ' likes this.";';
                                                    }else if($topic->likes == 2){
                                                        echo 'e.nextSibling.innerHTML = " · ' . Auth::user()->name . ' and 1 other user likes this.";';
                                                    }else if($topic->likes > 2){
                                                        echo 'e.nextSibling.innerHTML = " · You and ' . $topic->likes . ' other users like this.";';
                                                    }else if($topic->likes == 0){
                                                        echo 'e.nextSibling.innerHTML = " · ' . Auth::user()->name . ' likes this.";';
                                                    }else{
                                                        echo 'e.nextSibling.innerHTML = " · ' . Auth::user()->name . ' likes this.";';
                                                    }
                                                }
                                                @endphp
                                            }
                                        }else if(result["msg"] == "Login"){
                                            $(location).prop('href', '/login');
                                        }
                                }});
                            };
                           

                        </script>
                </div>
            </div>
        </div>
    </div>
@endsection