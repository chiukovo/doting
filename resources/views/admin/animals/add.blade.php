@extends('layouts.admin')

@section('title', '新增')
@section('content')

<div class="container-fluid">

	<div class="row">
	    <div class="card col-md-6">
	      <div class="card-header"><strong>動物居民</strong> 新增</div>
	      <div class="card-body">
	         <form class="form-horizontal" action="/{{ env('ADMIN_PREFIX') }}/animals/add" method="post" enctype="multipart/form-data">
	            {{ csrf_field() }}

	            <div class="form-group row">
	               	<div class="col-md-12">
	               		<input type="hidden" name="avatar_url_base64">
                        <input type="file" id="avatar" name="avatar_url" required>

	                  	<img id="avatar_url" src="/image/icon/animals.svg" name="avatar_url" class="img-thumbnail avatar"/>
	               	</div>
               	</div>
              
	            <div class="form-group row">
	               <label class="col-md-3 col-form-label" for="text-input">中文名稱</label>
	               <div class="col-md-9">
	                  <input class="form-control" id="text-input" type="text" name="name" placeholder="請填寫" value="">
	               </div>
	            </div>

	            <div class="form-group row">

	               <label class="col-md-3 col-form-label" for="text-input">日文名稱</label>
	               <div class="col-md-9">
	                  <input class="form-control" id="text-input" type="text" name="jp_name" placeholder="請填寫" value="">
	               </div>
	            </div>

	            <div class="form-group row">
	               <label class="col-md-3 col-form-label" for="text-input">英文名稱</label>
	               <div class="col-md-9">
	                  <input class="form-control" id="text-input" type="text" name="en_name" placeholder="請填寫" value="">
	               </div>
	            </div>

	            <div class="form-group row">
	               <label class="col-md-3 col-form-label">性別</label>
	               <div class="col-md-9 col-form-label">
	                  <div class="form-check form-check-inline mr-1">
	                     <input class="form-check-input" id="inline-radio1" type="radio" value="♂" name="sex" checked>
	                     <label class="form-check-label" for="inline-radio1">♂</label>
	                  </div>
	                  <div class="form-check form-check-inline mr-1">
	                     <input class="form-check-input" id="inline-radio2" type="radio" value="♀" name="sex">
	                     <label class="form-check-label" for="inline-radio2">♀</label>
	                  </div>
	               </div>
	            </div>

	          	<div class="form-group row">
	               <label class="col-md-3 col-form-label" for="select2">個性</label>
	               <div class="col-md-9">
	                  <select class="form-control form-control-lg" name="personality">
	                     	<option value="">請選擇</option>
	                   		@foreach(config('animal_attribute.personality') as $val)
	                   			<option value="{{ $val }}">{{ $val }}</option>
	                   		@endforeach
	                  </select>
	               </div>
	            </div>

	          	<div class="form-group row">
	               <label class="col-md-3 col-form-label" for="select2">種族</label>
	               <div class="col-md-9">
	                  <select class="form-control form-control-lg" name="race">
	                     	<option value="0">請選擇</option>
	                     	@foreach(config('animal_attribute.race') as $val)
	                   			<option value="{{ $val }}">{{ $val }}</option>
	                   		@endforeach
	                  </select>
	               </div>
	            </div>

	            <div class="form-group row">
	                <label class="col-md-3 col-form-label" for="date-input">生日</label>
	                <div class="col-md-2">
	                  	<select class="form-control" name="bd_m">
		                    <option value="0">月</option>
		                    @for($i=1; $i<=12; $i++)
		                    	<option value="{{ $i }}">{{ $i }}</option>
		                    @endfor
		                 </select>
	             	</div>
	            	<div class="col-md-2">
		                <select class="form-control" name="bd_d">
		                    <option value="0">日</option>
		                    @for($i=1; $i<=31; $i++)
		                    	<option value="{{ $i }}">{{ $i }}</option>
		                    @endfor
		                </select>
	                </div>
	            </div>

	            <div class="form-group row">
	               <label class="col-md-3 col-form-label" for="textarea-input">口頭禪</label>
	               <div class="col-md-9">
	                  <textarea class="form-control" id="textarea-input" name="say" rows="9" placeholder="請填寫"></textarea>
	               </div>
	            </div>

	            <div class="form-group row">
	               <label class="col-md-3 col-form-label" for="textarea-input">目標</label>
	               <div class="col-md-9">
	                  <textarea class="form-control" id="textarea-input" name="target" rows="9" placeholder="請填寫"></textarea>
	               </div>
	            </div>
	      
	            <div class="form-group row">
	               <label class="col-md-3 col-form-label" for="text-input">KK</label>
	               <div class="col-md-9">
	                  <input class="form-control" id="text-input" type="text" name="kk" placeholder="請填寫" value="">
	               </div>
	            </div>

	            <div class="form-group row">
	               <label class="col-md-3 col-form-label">啟用</label>
	               <div class="col-md-9 col-form-label">
	                  <div class="form-check form-check-inline mr-1">
	                     <input class="form-check-input" id="inline-radio1" type="radio" value="1" name="status" checked>
	                     <label class="form-check-label" for="inline-radio1">啟用</label>
	                  </div>
	                  <div class="form-check form-check-inline mr-1">
	                     <input class="form-check-input" id="inline-radio2" type="radio" value="0" name="status">
	                     <label class="form-check-label" for="inline-radio2">停用</label>
	                  </div>
	               </div>
	            </div>

	            <div class="card-footer" style="text-align: center">
			        <button class="btn btn-sm btn-primary" type="submit">送出</button>
			        <button class="btn btn-sm btn-danger" type="reset">重置</button>
			    </div>
	         </form>
	      </div>
	    </div>
	</div>
</div>

<script type="text/javascript">
	(function(){
        $('form').submit(function(){
            if(!$('input[name=avatar_url]').val().trim()){
                return false;
            }
        })

        $('input[name=avatar_url]').change(function(event) {  
            readURL(this);
        });

        function readURL(input) {
            if (input.files && input.files[0]) {   
                let reader = new FileReader();
                let filename = input.files[0].name;

                if(input.files[0].size > 5242880){
                    swal("圖片大小請勿超過5M", "", "error");
                    $('input[name=avatar_url]').val('');
                    return false;
                }

                filename = filename.substring(filename.lastIndexOf('\\')+1);
                reader.onload = function(e) {
                    $('#avatar_url').attr('src', e.target.result);
                    $('#avatar_url').hide().fadeIn(500);
                    $('input[name=avatar_url_base64]').val(e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    })();
</script>

@endsection