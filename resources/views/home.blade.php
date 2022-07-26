@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }} @if(Auth::user()->administrator)| <a href="/admin" style="color:red">Admin</a>@endif<a style="float: right;" href="/logout">Logout</a></div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    
                    
                    <div style="width: 150px; height:auto; text-align: center;">
                        <p>Current picture:</p>
                        <img src="{{ Auth::user()->avatar }}" width="110px" height="110px"/>
                        <div style="position: absolute; left: 160px;top: 140px; text-align: left;">
                            <p>Max dimensions: 256x256<br>
                            Max size: 2MB</p>
                        </div>
                        <form enctype="multipart/form-data" name="form-avatar" method="post" action="/upload">
                                @csrf                         
                                <input type="file" class="upload" id="uploadFile" name="file"><i onClick="document.getElementById('uploadFile').click();" style="margin-top: 15px; color: #999999; cursor: pointer;" class="fa-solid fa-upload"></i> <a onClick="document.getElementById('uploadFile').click();" style="color:SteelBlue; cursor: pointer;">Change avatar</a></input>
                                <button class="btn-send" style="margin-left: -0px; float:none !important; display: inline;" id="btnSubmit" type="submit">Upload</button>
                        </form>
                    </div>
                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
</div>
@endsection
