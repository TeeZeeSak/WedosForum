<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Topic;
use App\Models\Reply;
use App\Models\User;
use App\Models\Like;
use Auth;

class TopicController extends Controller
{
    public function index(){

        $topics = Topic::orderBy("sticky", "desc")->orderBy("updated_at", "desc")->where('visible', 1)->skip(0)->take(20)->get();


        return view('topics', [
            'topics' => $topics,
            'tag' => 'discussions',
        ]);
    }

    public function paginator($tag, $page = 1){
        if($tag == "all"){
            $skip = $page < 2 ? 0 : $page * 20;

            $topics = Topic::orderBy("sticky", "desc")->orderBy("updated_at", "desc")->where('visible', 1)->skip($skip)->take(20)->get();


            return view('topics', [
                'topics' => $topics,
                'tag' => $tag,
            ]);
        }
        if(filter_var($page, FILTER_VALIDATE_INT)){
            $skip = $page < 2 ? 0 : $page * 20;

            $topics = Topic::orderBy("sticky", "desc")->orderBy("updated_at", "desc")->where('visible', 1)->where("tag", "=", "$tag")->skip($skip)->take(20)->get();


            return view('topics', [
                'topics' => $topics,
                'tag' => $tag,
            ]);
        }else{
            abort(404);
        }
    }

    public function search(Request $request){
        $validated = $request->validate([
            'query' => 'required|min:4|max:25565',
            'tag' => 'required|max:255',
        ]);
        
        if($request->get("tag") == "all"){
            $topics = Topic::orderBy("sticky", "desc")->orderBy("updated_at", "desc")->where('content', 'like', '%' . $request->get("query") . '%')->orWhere('title', 'like', '%' . $request->get("query") . '%')->skip(0)->take(20)->get();
        }else{
            $topics = Topic::orderBy("sticky", "desc")->orderBy("updated_at", "desc")->where('tag', $request->get("tag"))->orWhere('content', 'like', '%' . $request->get("query") . '%')->orWhere('title', 'like', '%' . $request->get("query") . '%')->skip(0)->take(20)->get();

        }
        return view('topics', [
            'topics' => $topics,
            'tag' => $request->get("tag"),
        ]);
    }

    public function show($id){
        $topic = Topic::findOrFail($id);
        $replies = Reply::orderBy("created_at", "asc")->where("topicid", "=", "$id")->skip(0)->take(20)->get();
        $author = User::where("id", "=", $topic->author_id)->first(); 
        
        return view('topic', [
            'topic' => $topic,
            'replies' => $replies,
            'author' => $author,
        ]);
    }

    public function store(Request $request){
        if(!Auth::check())
            return redirect('/login')->with('status', 'Login first!');

        $validated = $request->validate([
                'content' => 'required|max:25565',
                'file' => 'max:10240',
        ]);



        $reply = new Reply;
        $reply->content = $request->content;
        $reply->topicid = $request->topicid;
        $reply->author_id = Auth::id();

    
        $reply->save();

        Topic::where('id', $request->topicid)
            ->update([
            'replies'=> Topic::raw('replies+1'),
        ]);

        Topic::where('id', $request->topicid)->update([
            'lastReplyBy' => $reply->author_id,
        ]);

        if($request->hasFile("file")){
            $file = $request->file;
            $filename = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $file->getClientOriginalName());
            $filename = mb_ereg_replace("([\.]{2,})", '', $filename);
           
            $destinationPath = 'uploads/replies/' . $reply->id;
            $file->move($destinationPath, $filename);

            Reply::where('id', $reply->id)->update(array(
                'attachments' => $filename,
            ));
        }


        $redirectTo = 'topic/';
        $redirectTo .= $request->topicid;
        return redirect($redirectTo)->with('status', 'Reply added!');
    }

    public function showCreate(){
        if(!Auth::check())
            return redirect('/login')->with('Error', 'Login first!');
        return view('/topics/new', []);
    }

    public function newStore(Request $request){
        if(!Auth::check())
            return redirect('/login')->with('Error', 'Login first!');
        $validated = $request->validate([
            'title' => 'required|max:30',
            'content' => 'required|max:25565',
            'tag' => 'required',
            'file' => 'max:10240',
        ]);

        $topic = new Topic;

        $topic->author_id = Auth::id();
        $topic->title = $request->title;
        $topic->content = $request->content;
        $topic->tag = $request->tag;

        if($request->has("sticky")){
            if($request->sticky)
                $topic->sticky = "1";
                else
                $topic->sticky = "0";
        }else{
            $topic->sticky = "0";
        }

        if($request->has("locked")){
            if($request->locked)
                $topic->locked = "1";
                else
                $topic->locked = "0";
        }else{
            $topic->locked = "0";
        }

        $topic->save();
        if($request->hasFile("file")){
            $file = $request->file;
            $filename = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $file->getClientOriginalName());
            $filename = mb_ereg_replace("([\.]{2,})", '', $filename);
           
            $destinationPath = 'uploads/' . $topic->id;
            $file->move($destinationPath, $filename);

            Topic::where('id', $topic->id)->update(array(
                'attachments' => $filename,
            ));
        }
        $redirectTo = '/topic/';
        $redirectTo .= $topic->id;
        return redirect($redirectTo)->with('status', 'Topic created!');

    }

    public function like(Request $request){
        if(!Auth::check())
            return response()->json(['msg'=>'Login']);
        
        $request->validate([
            'id' => 'required',
            'type' => 'required',
        ]);


        $liked_by_id = Auth::id();
        $like_type = $request->get("type");
        $liked_thing_id = $request->get("id");
        
        if(!preg_match('/^\d+$/', $like_type) || !preg_match('/^\d+$/', $liked_thing_id))
            return response()->json(['error'=> 'Not an int']);

        
        if(Like::where('liked_by_id', $liked_by_id)->where('like_type', $like_type)->where('liked_thing_id', $liked_thing_id)->first()){
        
            Like::where('liked_by_id', $liked_by_id)->where('like_type', $like_type)->where('liked_thing_id', $liked_thing_id)->delete();
            if($like_type == 0){
                Topic::where('id', $liked_thing_id)->decrement('likes', 1, [
                    'updated_at' => Topic::find($liked_thing_id)->updated_at
                ]);
            }else if($like_type == 1){
                Reply::where('id', $liked_thing_id)->decrement('likes', 1, [
                    'updated_at' => Reply::find($liked_thing_id)->updated_at
                ]);
            }

            //Could be better way to do this, but its 3 am and i cannot be fucked
            return response()->json(['msg'=>'Unlike']);
        }else{
            $like = new Like;

            $like->liked_by_id = $liked_by_id;
            $like->like_type = $like_type;
            $like->liked_thing_id = $liked_thing_id;
            
            $like->save();

            if($like_type == 0){
                Topic::where('id', $liked_thing_id)->increment('likes', 1, [
                    'updated_at' => Topic::find($liked_thing_id)->updated_at
                ]);
            }else if($like_type == 1){
                Reply::where('id', $liked_thing_id)->increment('likes', 1, [
                    'updated_at' => Reply::find($liked_thing_id)->updated_at
                ]);
            }
            
        }
        
        return response()->json(['msg'=>'Like']);
    }

}
