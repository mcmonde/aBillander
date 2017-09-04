@extends('layouts.master')

@section('title') aBillander @parent @stop


@section('content')


<div class="page-header">
    <h2>
        <!-- Start here! -->
    </h2>        
</div>


<div class="jumbotron">
<img src="{{URL::to('/assets/theme/images/Welcome.png')}}" 
					title=""
                    class="center-block"
                    style=" xborder: 2px solid black;
                            border-radius: 18px;
                            -moz-border-radius: 18px;
                            -khtml-border-radius: 18px;
                            -webkit-border-radius: 18px;">
</div>

@stop
