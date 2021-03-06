@extends('errors::illustrated-layout')

@section('code', '500')
@section('title', __('Error'))

@section('image')
<div style="background-image: url({{ asset('/svg/500.svg') }});" class="absolute pin bg-cover bg-no-repeat md:bg-left lg:bg-center">
</div>
@endsection
@if($exception->getMessage()&&(in_array(Auth::user()->UsRol, Permisos::TODOPROSARC)))
	@section('message', $exception->getMessage())
@else
	@section('message', trans('adminlte_lang::message.wewillwork'))
@endif