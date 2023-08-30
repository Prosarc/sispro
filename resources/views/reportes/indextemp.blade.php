@extends('layouts.app')
@section('htmlheader_title', 'Reportes')
@section('contentheader_title')
<span style="background-image: linear-gradient(40deg, #6dffd3, #0052cc); padding-right:30vw; position:relative; overflow:hidden;">
	Reportes sispro PROSARC SA ESP
  <div style="background-color:#ecf0f5; position:absolute; height:145%; width:40vw; transform:rotate(30deg); right:-20vw; top:-45%;"></div>
</span>
@endsection
@section('main-content')
<div class="container">
    <div class="row">
        <div class="col-md-16 col-md-offset-0">
			<div class="box">
				<div class="box-header">
                    <div>
                         <center><a href="{{ route('reportes.ReporteRegular')}}"><img src="img/LogoProsarc.png" class="img-circle" alt="Cinque Terre" width="240" height="236"></a>
                        <br>
                        <h1><center>Servicios Regulares</center></h1></center>     
                     </div>
                    <div>
                        <center><a href="{{ route('reportes.ReporteExpress')}}"><img src="img/EXPRESS.jpg" class="img-circle" alt="Cinque Terre" width="240" height="236"></a>
                         <br>
                        <h1><center>Servicios Express</center></h1></center>          
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>    
@endsection

