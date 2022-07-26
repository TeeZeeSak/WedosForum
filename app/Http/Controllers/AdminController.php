<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Models\User;
use App\Models\Topic;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = User::where('id', Auth::user()->id)->first();
        if(!$user->administrator)
            return redirect('/home')->with('error', 'Insufficient permissions!');
        
        return view('admin');
    }

    public function usersindex()
    {
        $user = User::where('id', Auth::user()->id)->first();
        if(!$user->administrator)
            return redirect('/home')->with('error', 'Insufficient permissions!');
        
        $users = User::skip(0)->take(20)->where('name', '!=', '[deleted]')->get();

         return view('admin.users', [
                'users' => $users,
            ]);
    }

    public function topics()
    {
        $user = User::where('id', Auth::user()->id)->first();
        if(!$user->administrator)
            return redirect('/home')->with('error', 'Insufficient permissions!');
        
        $topics = Topic::skip(0)->take(20)->get();

        return view('admin.topics', [
                'topics' => $topics,
        ]);
    }

    public function deleteTopic(Request $request)
    {
        $user = User::where('id', Auth::user()->id)->first();
        if(!$user->administrator)
            return redirect('/home')->with('error', 'Insufficient permissions!');
        
        $request->validate([
            'id' => 'required',
        ]);
        
        if(!preg_match('/^\d+$/', $request->get("id"))){
            return response()->json(['msg'=>'Fail']);
        }
        
        Topic::where('id', $request->get("id"))->delete();
    
        return response()->json(['msg'=>'OK']);
    }

    public function hideTopic(Request $request){
        $user = User::where('id', Auth::user()->id)->first();

        if(!$user->administrator)
            return redirect('/home')->with('error', 'Insufficient permissions!');

        $request->validate([
            'id' => 'required',
        ]);


      
        
        if(!preg_match('/^\d+$/', $request->get("id"))){
            return response()->json(['msg'=>'Fail']);
        }
        
        $topicid = (int)$request->get("id");

        $topic = Topic::where('id', $topicid)->first();

        if($topic->visible){
        
            $topic->update(array(
                'visible' => 0,
            ));
           
        }else{
            $topic->update(array(
                'visible' => 1,
            ));
           
        }
        return response()->json(['msg'=>'OK']);
    }

    public function stickyTopic(Request $request){
        $user = User::where('id', Auth::user()->id)->first();

        if(!$user->administrator)
            return redirect('/home')->with('error', 'Insufficient permissions!');

        $request->validate([
            'id' => 'required',
        ]);


      
        
        if(!preg_match('/^\d+$/', $request->get("id"))){
            return response()->json(['msg'=>'Fail']);
        }
        
        $topicid = (int)$request->get("id");

        $topic = Topic::where('id', $topicid)->first();

        if($topic->sticky){
        
            $topic->update(array(
                'sticky' => 0,
            ));
           
        }else{
            $topic->update(array(
                'sticky' => 1,
            ));
           
        }
        return response()->json(['msg'=>'OK']);
    }

    public function searchTopics(Request $request){
        $user = User::where('id', Auth::user()->id)->first();
        if(!$user->administrator)
            return redirect('/home')->with('error', 'Insufficient permissions!');

        $validated = $request->validate([
            'query' => 'required|min:1|max:255',
        ]);
        
        $topics;

        $author = User::where('name', 'like', '%' . $request->get("query") . '%')->first();

            

        if($request->query !== null && preg_match('/^\d+$/', $request->get("query"))){
            $topics = Topic::where('id', $request->get("query"))->get();

            return view('admin.topics.search', [
                'topics' => $topics,
            ]);
        }else{
            if($author != null)
                $topics = Topic::where('title', 'like', '%' . $request->get("query") . '%')->orWhere('content', 'like', '%' . $request->get("query") . '%')->orWhere('id',  $request->get("query"))->orWhere('author_id', $author->id)->get();
            else
                $topics = Topic::where('title', 'like', '%' . $request->get("query") . '%')->orWhere('content', 'like', '%' . $request->get("query") . '%')->orWhere('id',  $request->get("query"))->get();
            
                return view('admin.topics.search', [
                'topics' => $topics,
            ]);
        }

        return view('admin.topics.search', [
            'topics' => '',
            'status' => 'No users found',
        ]);

    }

    public function search(Request $request){
        $user = User::where('id', Auth::user()->id)->first();
        if(!$user->administrator)
            return redirect('/home')->with('error', 'Insufficient permissions!');

        $validated = $request->validate([
            'query' => 'required|min:1|max:255',
        ]);
        
        $users;
        if($request->query !== null && preg_match('/^\d+$/', $request->get("query"))){
            $users = User::where('id', $request->get("query"))->where('name', '!=', '[deleted]')->get();

            return view('admin.users.search', [
                'users' => $users,
            ]);
        }else{
            $users = User::where('name', 'like', '%' . $request->get("query") . '%')->orWhere('email', 'like', '%' . $request->get("query") . '%')->orWhere('id',  $request->get("query"))->where('name', '!=', '[deleted]')->get();
            return view('admin.users.search', [
                'users' => $users,
            ]);
        }

        return view('admin.users.search', [
            'users' => '',
            'status' => 'No users found',
        ]);

    }

    public function view()
    {
        $user = User::where('id', Auth::user()->id)->first();
        if(!$user->administrator)
            return redirect('/home')->with('error', 'Insufficient permissions!');
    
        return view('admin.users.new');
    }

    public function store(Request $request){
        $user = User::where('id', Auth::user()->id)->first();
        if(!$user->administrator)
            return redirect('/home')->with('error', 'Insufficient permissions!');
        
        if($request != null){
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ]);

            $new_user = new User();
            $new_user->password = Hash::make($request->get("password"));
            $new_user->email = $request->get("email");
            $new_user->name = $request->get("name");
            $new_user->save();
        }
        $users = User::skip(0)->take(20)->get();

         return view('admin.users', [
                'users' => $users,
            ]);
        return view('admin.users');
    }

    public function delete(Request $request)
    {
        $user = User::where('id', Auth::user()->id)->first();
        if(!$user->administrator)
            return redirect('/home')->with('error', 'Insufficient permissions!');
        
        $request->validate([
            'id' => 'required',
            'type' => 'required',
        ]);
        
        if($request->get("type") == "0"){
            User::where('id', $request->get("id"))->update(array(
                'avatar' => '/uploads/default.jpg',
            ));
            return response()->json(['msg'=>'OK']);
        }else if($request->get("type") == "1"){
            User::where('id', $request->get("id"))->update(array(
                'avatar' => '/uploads/default.jpg',
                'name' => '[deleted]',
                'password' => '',
            ));
            return response()->json(['msg'=>'OK']);
        }
        return response()->json(['msg'=>'Fail']);
    }
}
