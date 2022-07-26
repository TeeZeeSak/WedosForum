<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Models\User;

class HomeController extends Controller
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
        return view('home');
    }

    public function logout(Request $request)
    {
        Auth::logout();
    
        $request->session()->invalidate();
    
        $request->session()->regenerateToken();
    
        return redirect('/');
    }

    public function store(Request $request){
        $request->validate([
            'file' => 'required|mimes:gif,jpeg,jpg,png,svg,webp,webm|max:2048|dimensions:max_width=256,max_height=256'
        ]);
        if($request->hasFile("file")){
            $file = $request->file;
            $filename = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $file->getClientOriginalName());
            $filename = mb_ereg_replace("([\.]{2,})", '', $filename);
           
            $destinationPath = 'uploads/avatars/' . Auth::user()->id;
            $file->move($destinationPath, $filename);
            
            $filename = '/' . $destinationPath . '/' . $filename;
            User::where('id', Auth::user()->id)->update(array(
                'avatar' => $filename,
            ));
            return redirect('/home')->with('status', 'Profile picture changed!');
        }
        return redirect('/home')->with('error', 'Unknown error occured');

    }

    

}
