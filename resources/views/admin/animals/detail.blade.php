@extends('layouts.admin')

@section('title', '編輯')
@section('content')

<div class="container-fluid">

	<div class="row">
	    <div class="card col-md-6">
	      <div class="card-header"><strong>動物居民</strong> 編輯</div>
	      <div class="card-body">
	         <form class="form-horizontal" action="/{{ env('ADMIN_PREFIX') }}/animals/edit/{{ $detail->id }}" method="post" enctype="multipart/form-data">
	            {{ csrf_field() }}
	            {{ method_field('PATCH') }}
	            <input type="hidden" name="id" value="{{ $detail->id }}">
	            <div class="form-group row">
                  	<div class="col-md-9" style="text-align: center;">
	                  	<img class="img-fluid" src="/animal/{{ $detail->name }}.png" alt="{{ $detail->name }}">
	              	</div>

	              	<!--
	              	<div class="col-md-9">
	                  	<input id="file-input" type="file" name="file-input">
	               	</div>
	               	-->
	            </div>

	            <div class="form-group row">
	               <label class="col-md-3 col-form-label" for="text-input">中文名稱</label>
	               <div class="col-md-9">
	                  <input class="form-control" id="text-input" type="text" name="name" placeholder="請填寫" value="{{ $detail->name }}" readonly>
	               </div>
	            </div>

	            <div class="form-group row">
	               <label class="col-md-3 col-form-label" for="text-input">日文名稱</label>
	               <div class="col-md-9">
	                  <input class="form-control" id="text-input" type="text" name="jp_name" placeholder="請填寫" value="{{ $detail->jp_name }}">
	               </div>
	            </div>

	            <div class="form-group row">
	               <label class="col-md-3 col-form-label" for="text-input">英文名稱</label>
	               <div class="col-md-9">
	                  <input class="form-control" id="text-input" type="text" name="en_name" placeholder="請填寫" value="{{ $detail->en_name }}">
	               </div>
	            </div>

	            <div class="form-group row">
	               <label class="col-md-3 col-form-label">性別</label>
	               <div class="col-md-9 col-form-label">
	                  <div class="form-check form-check-inline mr-1">
	                     <input class="form-check-input" id="inline-radio1" type="radio" value="♂" name="sex" @if($detail->sex == '♂') checked @endif>
	                     <label class="form-check-label" for="inline-radio1">♂</label>
	                  </div>
	                  <div class="form-check form-check-inline mr-1">
	                     <input class="form-check-input" id="inline-radio2" type="radio" value="♀" name="sex" @if($detail->sex == '♀') checked @endif>
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
	                   			<option value="{{ $val }}" @if($val == $detail->personality) selected @endif>{{ $val }}</option>
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
	                   			<option value="{{ $val }}" @if($val == $detail->race) selected @endif>{{ $val }}</option>
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
		                    	<option value="{{ $i }}" @if($i == $detail->bd_m) selected @endif >{{ $i }}</option>
		                    @endfor
		                 </select>
	             	</div>
	            	<div class="col-md-2">
		                <select class="form-control" name="bd_d">
		                    <option value="0">日</option>
		                    @for($i=1; $i<=31; $i++)
		                    	<option value="{{ $i }}" @if($i == $detail->bd_d) selected @endif >{{ $i }}</option>
		                    @endfor
		                </select>
	                </div>
	            </div>

	            <div class="form-group row">
	               <label class="col-md-3 col-form-label" for="textarea-input">口頭禪</label>
	               <div class="col-md-9">
	                  <textarea class="form-control" id="textarea-input" name="say" rows="9" placeholder="請填寫" value="{{ $detail->say }}">{{ $detail->say }}</textarea>
	               </div>
	            </div>

	            <div class="form-group row">
	               <label class="col-md-3 col-form-label" for="textarea-input">目標</label>
	               <div class="col-md-9">
	                  <textarea class="form-control" id="textarea-input" name="target" rows="9" placeholder="請填寫" value="{{ $detail->target }}">{{ $detail->target }}</textarea>
	               </div>
	            </div>
	      
	            <div class="form-group row">
	               <label class="col-md-3 col-form-label" for="text-input">KK</label>
	               <div class="col-md-9">
	                  <input class="form-control" id="text-input" type="text" name="kk" placeholder="請填寫" value="{{ $detail->kk }}">
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


@endsection