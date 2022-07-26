@extends("layouts.app")

@section("content")   

    <div class="content">

        <div class="content-topics">
            <div class="topic-reply">
                    <div class="topic-content">
                        <form enctype="multipart/form-data" name="form-reply" id="add-reply" method="post" action="/topics/new">
                                @csrf
                                <div class="topic-content" style="margin-left: -5px; margin-top: 5px;">
                                    <label for="title">Topic title</label>
                                    <input required class="newtopic-title" type="text" name="title" placeholder="Topic title" maxlength=30></input>
                                    <label for="tag">Category</label><br/>
                                    <select class="newtopic-select" name="tag" id="tags">
                                        <option value="general">General</option>
                                        <option value="software">Software</option>
                                        <option value="programming">Programming</option>
                                        <option value="games">Games</option>
                                        <option value="music">Music</option>
                                        <option value="offtopic">Off-topic</option>
                                        @if(App\Models\User::findOrFail(Auth::id())->administrator)
                                            <option value="Announcements">Announcements</option>
                                        @endif
                                    </select><br/>
                                    @if(App\Models\User::findOrFail(Auth::id())->administrator)
                                        <input title="Mark topic as sticky" type="checkbox" class="sticky" name="sticky"> Sticky</input><br/>
                                    @endif
                                    <br/>
                                    <label for="content">Topic content</label>
                                    <div class="newtopic-body">
                                        <textarea id="replyBody" rows="3" cols="25" name="content" autocomplete="off" class="newtopic-content newtopic" required placeholder="Topic content"></textarea>
                                        <div class="newtopic-footer">
                                            <input type="file"class="upload" id="uploadFile" name="file"><i onClick="document.getElementById('uploadFile').click();" title="Add an attachement" class="fa-solid fa-paperclip attachement"></i></input>
                                            <button class="btn-send newtopic" type="submit">Submit</button>
                                        </div>
                                    </div>
                                </div>  
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
                            var area = document.getElementById('replyBody');
                            if (area.addEventListener) {
                                area.addEventListener('input', function() {
                                    
                                    let emptyLines = 0;
                                    let limit;
                                    let lines = area.value.split("\n", limit)
                                    
      
                                    area.rows = lines.length + 3;
                                }, false);
                            }
                           
      </script>
@endsection