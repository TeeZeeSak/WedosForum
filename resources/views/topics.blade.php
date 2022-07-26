@extends("layouts.app")
@section('title', 'WedosForum')
@section("content")
<div class="relative items-top min-h-screen dark:bg-gray-900 sm:items-center py-4 sm:pt-0">
    <div class="content">
        <div class="content-tags">
            <form action="/search" method="get" class="search">
                @csrf
                <i class="fa-solid fa-magnifying-glass search"></i>
                @if($tag != "discussions")
                    <input type="hidden" name="tag" value="{{$tag}}"/>
                @else
                    <input type="hidden" name="tag" value="all"/>
                @endif
                <input type="text" autocomplete="off" minlength="2" placeholder="Search {{$tag}}..." name="query"></input> <br>
                <a class="btn-create" href="/topics/new"><i class="fa-solid fa-plus"></i> Create a topic</a>
            </form>
            
            


            <ul class="tags">
                <li><a href="/topics/general">General</a></li>
                <li><a href="/topics/announcements">Announcements</a></li>
                <li><a href="/topics/software">Software</a></li>
                <li><a href="/topics/programming">Programming</a></li>
                <li><a href="/topics/games">Games</a></li>
                <li><a href="/topics/music">Music</a></li>
                <li><a href="/topics/offtopic">Off-topic</a></li>
            </ul>
            
        </div>
        <div class="content-topics">
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
            @endphp
            <table>
                @foreach($topics as $topic)
                    @if(!$topic->visible)
                        @continue
                    @endif
                    @if($topic->sticky)
                        <tr style="background-color: rgba(250,244,211,0.3);">
                    @else
                        <tr>
                    @endif
                        <td style="width: 3rem; font-size:80%;"><a href="/topics/{{ $topic->tag }}" class="col-tags">{{ $topic->tag }}</a></td>
                        <td style="max-width: 12rem;overflow: hidden;text-overflow: ellipsis;white-space: nowrap; font-size: 90%;"><a href="/topic/{{ $topic->id }}" class="col-title">@if($topic->sticky) <i class="fa-solid fa-thumbtack" style="color: orange;"></i> @endif @if($topic->locked) <i class="fa-solid fa-lock"></i> @endif {{ $topic->title }}</a></td>
                        @if($topic->lastReplyBy != "")
                            <td style="width: 16rem; font-size: 80%;">Last post by {{ App\Models\User::findOrFail($topic->lastReplyBy)->name }} @php echo dateDifference($topic->updated_at, $now); @endphp</td>
                        @else
                            <td style="width: 16rem; font-size: 80%;">Posted by {!! App\Models\User::findOrFail($topic->author_id)->name !!} @php echo dateDifference($topic->updated_at, $now); @endphp</td>
                        @endif
                        <td style="width: 3rem; font-size: 80%;">{{ $topic->replies }}</td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>
@endsection