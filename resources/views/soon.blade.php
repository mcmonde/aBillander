@extends('layouts.master')

@section('title') {{ l('Coming soon', [], 'layouts') }} @parent @stop


@section('content')

<div class="page-header">
    <h2>
        {{ l('Coming soon...', [], 'layouts') }}
    </h2>        
</div>

<img src="{{URL::to('/img/push_Billander.jpg')}}" title='"Don’t ever fight with Lisbeth Salander. Her attitude towards the rest of the world is that if someone threatens her with a gun, she’ll get a bigger gun.”

― Stieg Larsson, The Girl Who Played with Fire'
                    class="center-block"
                    style=" xborder: 2px solid black;
                            border-radius: 18px;
                            -moz-border-radius: 18px;
                            -khtml-border-radius: 18px;
                            -webkit-border-radius: 18px;">
@stop