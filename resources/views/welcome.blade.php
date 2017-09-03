@extends('layouts.master')

@section('title') aBillander @parent @stop


@section('content')

<!-- 
<div class="page-header">
    <h2>
        Start here! 
    </h2>        
</div>
-->

<div class="jumbotron">
<img src="{{URL::to('/assets/theme/images/Welcome.jpg')}}" title='"Don’t ever fight with Lisbeth Salander. Her attitude towards the rest of the world is that if someone threatens her with a gun, she’ll get a bigger gun.”

― Stieg Larsson, The Girl Who Played with Fire'
                    class="center-block"
                    style=" xborder: 2px solid black;
                            border-radius: 18px;
                            -moz-border-radius: 18px;
                            -khtml-border-radius: 18px;
                            -webkit-border-radius: 18px;">
</div>

@stop
