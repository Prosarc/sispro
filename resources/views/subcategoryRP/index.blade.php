@extends('layouts.app')
@section('htmlheader_title')
Lista de SubCategorias
@endsection
@section('contentheader_title')
<span style="background-image: linear-gradient(40deg, #d4fc79, #00C851); padding-right:30vw; position:relative; overflow:hidden;">
    Subcategorías
  <div style="background-color:#ecf0f5; position:absolute; height:145%; width:40vw; transform:rotate(30deg); right:-20vw; top:-45%;"></div>
</span>
@endsection
@section('main-content')
	<div class="container-fluid spark-screen">
		<div class="row">
			<div class="col-md-16 col-md-offset-0">
				<div class="box">
					<div class="box-header">
						<h3 class="box-title">Lista de SubCategorias</h3>
						<a href="/subcategorypublic/create" class="btn btn-primary pull-right">{{ trans('adminlte_lang::message.create') }}</a>
					</div>
					<div class="box box-info">
						<div class="box-body">
							<table id="AreaTable" class="table table-compact table-bordered table-striped">
								<thead>
									<tr>
										<th>SubCategoria</th>
										<th>Categoria</th>
										<th>Editar</th>
									</tr>
								</thead>
								<tbody id="readyTable">
									@foreach($SubCategoriesRP as $SubCategoryRP)
									<tr>
										<td>{{$SubCategoryRP->SubCategoryRpName}}</td>
										<td>{{$SubCategoryRP->CategoryRpName}}</td>
										<td><a href='/subcategorypublic/{{$SubCategoryRP->ID_SubCategoryRP}}/edit' class='btn btn-warning'><i class="fas fa-edit"></i> <b>{{trans('adminlte_lang::message.edit')}}</b></a></td>
									</tr>
									@endforeach
								</tbody>
							</table>
						</div>
						<!-- /.box-body -->
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection