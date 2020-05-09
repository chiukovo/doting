@php
	$routeName = request()->route()->getName();
@endphp
<a href="/fish/list"><button type="button" class="btn btn-search {{ $routeName == 'fish' ? 'current' : '' }}">魚</button></a>
<a href="/insect/list"><button type="button" class="btn btn-search {{ $routeName == 'insect' ? 'current' : '' }}">昆蟲</button></a>
<a href="/fossil/list"><button type="button" class="btn btn-search {{ $routeName == 'fossil' ? 'current' : '' }}">化石</button></a>
<a href="/art/list"><button type="button" class="btn btn-search {{ $routeName == 'art' ? 'current' : '' }}">藝術品</button></a>