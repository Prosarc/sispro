<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html" charset="utf-8" />
    {{-- <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'> --}}
    <title>Manifiesto M-{{sprintf("%10s", $certificado->ID_Cert)}}</title>
  
    <style>
      @page {
          margin: 0px 25px 0px 25px;
    
      }
  
   body{
          background-image: url('{{asset("img/WATERMARKV5.png")}}');
          -webkit-background-size: contain;
          -moz-background-size: contain;
          -o-background-size: contain;
          background-size: 600px;
          background-repeat: no-repeat;
          background-position: center;
      
      }
      header {
          position: relative;
          top: 0px;
          left: 0px;
          right: 0px;
          height: 100px;
  
          /** Extra personal styles **/
          background-color: #ffffff00;
          color: rgb(0, 0, 0);
          text-align: right;
          /* text-align: left; */
          /* line-height: 35px; */
      }
      footer {
          position: relative;
          bottom: 20px;
          left: 0px;
          right: 0px;
          height: auto;
  
          /** Extra personal styles **/
          background-color: #ffffff00;
          color: rgb(0, 0, 0);
          /* text-align: center;
          line-height: 35px; */
      }
       /* Font Definitions */
      @font-face
          {font-family:"Cambria Math";
          panose-1:2 4 5 3 5 4 6 3 2 4;}
      @font-face
          {font-family:Calibri;
          panose-1:2 15 5 2 2 2 4 3 2 4;}
      @font-face
          {font-family:"Segoe UI";
          panose-1:2 11 5 2 4 2 4 2 2 3;}
       /* Style Definitions */
       p.MsoNormal, li.MsoNormal, div.MsoNormal
          {margin:0cm;
          font-size:10.0pt;
          font-family:"Times New Roman",serif;}
      p.MsoHeader, li.MsoHeader, div.MsoHeader
          {mso-style-link:"Encabezado Car";
          margin:0cm;
          font-size:10.0pt;
          font-family:"Times New Roman",serif;}
      span.EncabezadoCar
          {mso-style-name:"Encabezado Car";
          mso-style-link:Encabezado;
          font-family:"Times New Roman",serif;}
      .MsoChpDefault
          {font-size:10.0pt;
          font-family:"Calibri",sans-serif;}
       /* Page Definitions */
       @page WordSection1
          {size:612.0pt 792.0pt;
          margin:90.35pt 84.95pt 99.25pt 92.15pt;}
       ol
          {margin-bottom:0cm;}
       ul
          {margin-bottom:0cm;}
  
  
        .invoice-box {
        /* padding: 10px; */
        /* border: 1px solid #eee; */
        font-size: 12px;
        line-height: 14px;
        font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
        color: #000;
      }
      .main{
  
      }
  
      .invoice-box table {
        width: 100%;
        line-height: inherit;
        text-align: left;
      }
  
      .invoice-box table td {
        padding: 3px;
        vertical-align: top;
      }
  
      .invoice-box table tr td:nth-child(3) {
        text-align: left;
      }
  
      .invoice-box table tr.top table td {
        padding-bottom: 10px;
      }
  
      .invoice-box table tr.top table td.title {
        font-size: 45px;
        line-height: 45px;
        color: #999;
      }
  
      .invoice-box table tr.information table td {
        padding-bottom: 0px;
      }
  
      .invoice-box table tr.heading td {
        background: rgb(0, 56, 140);
        border-bottom: 1px rgb(0, 56, 140);
        font-weight: bold;
          color: #ddd;
      }
  
      .invoice-box table tr.details td {
        padding-bottom: 0px;
      }
  
      .invoice-box table tr.item td {
        border-bottom: 1px solid rgb(198, 211, 255);
      }
  
      .invoice-box table tr.item.last td {
        border-bottom: none;
      }
  
      .invoice-box table tr.total td:nth-child(4) {
        border-top: 2px solid rgb(11, 24, 68);
        font-weight: bold;
        text-align: right;
  
      }
  
      @media only screen and (max-width: 600px) {
        .invoice-box table tr.top table td {
          width: 100%;
          display: block;
          text-align: center;
        }
  
        .invoice-box table tr.information table td {
          width: 100%;
          display: block;
          text-align: center;
        }
      }
  
      /** RTL **/
      .rtl {
        direction: rtl;
        font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
      }
  
      .rtl table {
        text-align: right;
      }
  
      .rtl table tr td:nth-child(2) {
        text-align: left;
      }    
  
    </style>
  </head>
  <header>
    <div class="invoice-box header-footer">
      <table cellpadding="0" cellspacing="0">
        <tr class="top">
          <td colspan="1">
            <table>
              <tr>
                <td class="title">
                </td>
                <td style="font-size: 16px; text-align: right;">
                  <img src="{{asset('img/logoheaderTinyVersion.png')}}" style="width:100%; max-width:300px;"><br><br>
                  <b style="margin-right:65.4pt;">Nit: 900.079.188-0</b><br><br>
                  <b>No.</b> <b style="margin-right:65.4pt; color:red;">{{sprintf("%05s", $certificado->ID_Cert)}}</b><br>
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </div>
  </header><br><br>  


    <body lang=ES-MX>
        <br>    
    <div class=WordSection1>

        <p class=MsoNormal align=center style='text-align:center'><b><span lang=ES
        style='font-size:12.0pt;font-family:"Arial",sans-serif'>PROSARC S.A ESP</span></b></p>
        
        <p class=MsoNormal align=center style='margin-left:35.4pt;text-align:center;
        text-indent:-35.4pt'><b><span lang=ES style='font-size:12.0pt;font-family:"Arial",sans-serif'>NIT
        900.079.188-0</span></b></p>
        
        <p class=MsoNormal align=center style='text-align:center'><span lang=ES
        style='font-size:7.5pt;font-family:"Arial",sans-serif'>&nbsp;</span></p>
        
        <p class=MsoNormal align=center style='text-align:center'><b><span lang=ES
        style='font-size:16.0pt;font-family:"Arial",sans-serif'>MANIFIESTA:</span></b></p>
        
        <p class=MsoNormal><span lang=ES style='margin-left:60.4pt;text-align:center;font-size:7.5pt;font-family:"Arial",sans-serif;
        color:#0D0D0D'>Que el generador:</span></p>
        
        <p class=MsoNormal><b><span lang=ES style='font-size:7.5pt;font-family:"Arial",sans-serif'>&nbsp;</span></b></p>
        
        <div style='margin-left:65.4pt;text-align:center;'>
        
        <table width="85%" class=MsoNormalTable border=1 cellspacing=0 cellpadding=0
         style='border-collapse:collapse;border:none;'>
         <tr width=35>
            <td width=70 style='width:40pt;border:solid gray 0.5pt;padding:0cm 5.4pt 0cm 5.4pt'>
          <p class=MsoNormal style='text-align:justify'><span lang=ES style='font-size:
          7.5pt;font-family:"Arial",sans-serif'>Empresa:  <b>{{$certificado->sedegenerador->generadors->GenerName}}</b></span></p>
          </td>
          <td width=60 colspan=1 style='width:60pt;border:solid gray 0.5pt;
          border-left:none;padding:0cm 5.4pt 0cm 5.4pt'>
          <p class=MsoNormal style='text-align:justify'><span lang=ES style='font-size:
          7.5pt;font-family:"Arial",sans-serif'>NIT:  <b>{{$certificado->sedegenerador->generadors->GenerNit}}</b></span></p>
          </td>
         </tr>

         <tr>
          <td width=207 valign=top style='width:82.0pt;border:solid gray 0.5pt;
          border-top:none;padding:0cm 5.4pt 0cm 5.4pt'>
          <p class=MsoNormal style='text-align:justify'><span lang=ES style='font-size:
          7.5pt;font-family:"Arial",sans-serif'>Dirección: <b>{{$certificado->sedegenerador->GSedeAddress}} (Municipio:{{$certificado->sedegenerador->municipio->MunName}}) ({{$certificado->sedegenerador->GSedeName}})</b></span></p>
          </td>
          <td width=123 style='width:92.15pt;border-top:none;border-left:none;
          border-bottom:solid gray 0.5pt;border-right:solid gray 0.5pt;padding:0cm 5.4pt 0cm 5.4pt'>
          <p class=MsoNormal><span lang=ES style='font-size:7.5pt;font-family:"Arial",sans-serif'>Ciudad:
          <b>{{$certificado->sedegenerador->municipio->Departamento->DepartName}}</b></span></p>
          </td>
         </tr>
        </table>
        
        </div>
        
        <p class=MsoNormal style='text-align:justify'><span lang=ES style='font-size:
        7.5pt;font-family:"Arial",sans-serif'>&nbsp;</span></p>
        
        <p class=MsoNormal style='margin-left:60.4pt;'><span lang=ES style='font-size:
        7.5pt;font-family:"Arial",sans-serif;color:#0D0D0D'>Por intermedio de la empresa
        transportadora:</span></p>
        
        <p class=MsoNormal style='text-align:justify'><span lang=ES style='font-size:
        7.5pt;font-family:"Arial",sans-serif'>&nbsp;</span></p>
        
        <div style='margin-left:65.4pt;text-align:center;'>
        
        @switch($certificado->SolicitudServicio->SolSerTipo)
        
        @case('Cliente')
        @case('Interno')
        
        <table width="85%" class=MsoNormalTable border=1 cellspacing=0 cellpadding=0
         style='border-collapse:collapse;border:none'>
         <tr>
          <td width=198 valign=top style='width:148.45pt;border:solid gray 0.5pt;
          padding:0cm 5.4pt 0cm 5.4pt'>
          <p class=MsoNormal><span lang=ES style='font-size:7.5pt;font-family:"Arial",sans-serif;
          color:#0D0D0D'>Empresa: <b>{{$certificado->transportador->ID_Cli == 1 ? $certificado->transportador->CliShortname : $certificado->transportador->CliName}}</b></span></p>
          </td>
          <td width=70 valign=top style='width:3.0cm;border:solid gray 0.5pt;
          border-left:none;padding:0cm 5.4pt 0cm 5.4pt'>
          <p class=MsoNormal style='text-align:justify'><span lang=ES style='font-size:
          7.5pt;font-family:"Arial",sans-serif;color:#0D0D0D'>NIT: <b>{{$certificado->transportador->CliNit}}</b></span></p>
          </td>
         </tr>
         <tr>
          <td width=298 valign=top style='width:248.45pt;border:solid gray 0.5pt;
          border-top:none;padding:0cm 5.4pt 0cm 5.4pt'>
          <p class=MsoNormal style='text-align:justify'><span lang=ES style='font-size:
          7.5pt;font-family:"Arial",sans-serif;color:#0D0D0D'>Dirección:  <b>{{$certificado->transportador->sedes[0]->SedeAddress}} ({{$certificado->transportador->sedes[0]->Municipios->MunName}})</b></span></p>
          </td>
          <td width=113 valign=top style='width:3.0cm;border-top:none;border-left:none;
          border-bottom:solid gray 0.5pt;border-right:solid gray 0.5pt;padding:0cm 5.4pt 0cm 5.4pt'>
          <p class=MsoNormal><span lang=ES style='font-size:7.5pt;font-family:"Arial",sans-serif;
          color:#0D0D0D'>Ciudad: <b>{{$certificado->transportador->sedes[0]->Municipios->Departamento->DepartName}}</b></span></p>
          </td>
         </tr>
        </table>
        
        @break
        
        @case('Generador')
        @case('Externo')
        
        <table width="85%" class=MsoNormalTable border=1 cellspacing=0 cellpadding=0
         style='border-collapse:collapse;border:none'>
         <tr>
          <td width=397 valign=top style='border:solid gray 0.5pt;
          padding:0cm 5.4pt 0cm 5.4pt'>
          <p class=MsoNormal><span lang=ES style='font-size:7.5pt;font-family:"Arial",sans-serif;
          color:#0D0D0D'>Empresa:<b>{{$certificado->SolicitudServicio->SolSerNameTrans}}</b></span></p>
          </td>
          <td width=70 valign=top style='border:solid gray 0.5pt;
          border-left:none;padding:0cm 5.4pt 0cm 5.4pt'>
          <p class=MsoNormal style='text-align:justify'><span lang=ES style='font-size:
          7.5pt;font-family:"Arial",sans-serif;color:#0D0D0D'>NIT: <b>{{$certificado->SolicitudServicio->SolSerNitTrans}}</b></span></p>
          </td>
         </tr>
         <tr>
          <td width=397 valign=top style='border:solid gray 0.5pt;
          border-top:none;padding:0cm 5.4pt 0cm 5.4pt'>
          <p class=MsoNormal style='text-align:justify'><span lang=ES style='font-size:
          7.5pt;font-family:"Arial",sans-serif;color:#0D0D0D'>Dirección:  <b>{{$certificado->SolicitudServicio->SolSerAdressTrans}} ({{$certificado->SolicitudServicio->municipio->MunName}})</b></span></p>
          </td>
          <td width=113 valign=top style='border-top:none;border-left:none;
          border-bottom:solid gray 0.5pt;border-right:solid gray 0.5pt;padding:0cm 5.4pt 0cm 5.4pt'>
          <p class=MsoNormal><span lang=ES style='font-size:7.5pt;font-family:"Arial",sans-serif;
          color:#0D0D0D'>Ciudad: <b>{{$certificado->SolicitudServicio->municipio->Departamento->DepartName}}</b></span></p>
          </td>
         </tr>
        </table>
        
        @break
        
        @endswitch
        
        </div>
        
        <p class=MsoNormal style='text-align:justify'><span lang=ES style='font-size:
        7.5pt;font-family:"Arial",sans-serif'>&nbsp;</span></p>
        
        <p class=MsoNormal style='margin-left:60.4pt;text-align:justify'><span lang=ES style='font-size:
          7.5pt;font-family:"Arial",sans-serif;color:#0D0D0D'>Entrego su(s) residuo(s)
          y/o desecho(s), para tratamiento y disposición final de acuerdo con la siguiente
          información:</span></p>
        
        <p class=MsoNormal style='text-align:justify'><span lang=ES style='font-size:
        7.5pt;font-family:"Arial",sans-serif;color:#0D0D0D'>&nbsp;</span></p>
        
        <div style='margin-left:65.4pt;text-align:center;'>
        
        <table width="85%" class=MsoNormalTable border=1 cellspacing=0 cellpadding=0 width=250
         style='width:250 pt;border-collapse:collapse;border:none'>
         <tr width=35>
          <td width="40" style='border:solid gray 0.5pt;padding:0cm 5.4pt 0cm 5.4pt'>
          <p class=MsoNormal><span lang=ES style='font-size:7.5pt;font-family:"Arial",sans-serif;
          color:#0D0D0D'>Fecha de Recepción</span></p>
          </td>
          <td width="60" colspan=3 style='border:solid gray 0.5pt;
          border-left:none;padding:0cm 5.4pt 0cm 5.4pt'>
          <p class=MsoNormal><span lang=ES style='font-size:7.5pt;font-family:"Arial",sans-serif;
          color:#0D0D0D'>@php

          $añorecepcion=date('Y', strtotime($certificado->recepcion));
          $mesrecepcion=date('m', strtotime($certificado->recepcion));
          $diarecepcion=date('d', strtotime($certificado->recepcion));
          switch ($mesrecepcion) {
          case 1:
          $mesrecepciontexto = 'Enero';
          break;
        
          case 2:
          $mesrecepciontexto = 'Febrero';
          break;
        
          case 3:
          $mesrecepciontexto = 'Marzo';
          break;
        
          case 4:
          $mesrecepciontexto = 'Abril';
          break;
        
          case 5:
          $mesrecepciontexto = 'Mayo';
          break;
        
          case 6:
          $mesrecepciontexto = 'Junio';
          break;
        
          case 7:
          $mesrecepciontexto = 'Julio';
          break;
        
          case 8:
          $mesrecepciontexto = 'Agosto';
          break;
        
          case 9:
          $mesrecepciontexto = 'Septiembre';
          break;
        
          case 10:
          $mesrecepciontexto = 'Octubre';
          break;
        
          case 11:
          $mesrecepciontexto = 'Noviembre';
          break;
        
          case 12:
          $mesrecepciontexto = 'Diciembre';
          break;
          }

          if($certificado->recepcion != ""){
          $añorecepcion=date('Y', strtotime($certificado->recepcion));
          $mesrecepcion=date('m', strtotime($certificado->recepcion));
          $diarecepcion=date('d', strtotime($certificado->recepcion));
          $mesrecepciontexto = "";
          switch ($mesrecepcion) {
          case 1:
          $mesrecepciontexto = 'Enero';
          break;
        
          case 2:
          $mesrecepciontexto = 'Febrero';
          break;
        
          case 3:
          $mesrecepciontexto = 'Marzo';
          break;
        
          case 4:
          $mesrecepciontexto = 'Abril';
          break;
        
          case 5:
          $mesrecepciontexto = 'Mayo';
          break;
        
          case 6:
          $mesrecepciontexto = 'Junio';
          break;
        
          case 7:
          $mesrecepciontexto = 'Julio';
          break;
        
          case 8:
          $mesrecepciontexto = 'Agosto';
          break;
        
          case 9:
          $mesrecepciontexto = 'Septiembre';
          break;
        
          case 10:
          $mesrecepciontexto = 'Octubre';
          break;
        
          case 11:
          $mesrecepciontexto = 'Noviembre';
          break;
        
          case 12:
          $mesrecepciontexto = 'Diciembre';
          break;
          }
          }
          @endphp
          {{$diarecepcion}} de {{$mesrecepciontexto}} del {{$añorecepcion}}</span></p>
          </td>
         </tr>
         @php
        $collection2 = collect([]);
        @endphp
        @foreach($certificado->SolicitudServicio->SolicitudResiduo as $Residuo)
        @if($Residuo->requerimiento->FK_ReqTrata == $certificado->FK_CertTrat&&$Residuo->generespel->gener_sedes->ID_GSede == $certificado->FK_CertGenerSede)
        @if($Residuo->SolResRM2 !== null && is_Array($Residuo->SolResRM2))
        @foreach ($Residuo->SolResRM2 as $rm2 => $value2)
        @php
        $collection2 = $collection2->concat([$value2]);
        @endphp
        @endforeach
        @else
        @if (is_Array($Residuo->SolResRM))
        @foreach ($Residuo->SolResRM as $rm => $value)
        @php
        $collection2 = $collection2->concat([$value]);
        @endphp
        @endforeach
        @else
        @php
        $uniquestring = 'RM Invalido -> '.$Residuo->SolResRM;
        @endphp
        @endif
        @endif
        @endif
        @endforeach
        @php
        if ($collection2->isNotEmpty()) {
        $unicos = collect($collection2->unique());
        $uniquestring = $unicos->values()->join(', ');
        }
        @endphp
         <tr width=35>
          <td width="40" nowrap style='border:solid gray 0.5pt;border-top:none;
          padding:0cm 5.4pt 0cm 5.4pt'>
          <p class=MsoNormal><span lang=ES style='font-size:7.5pt;font-family:"Arial",sans-serif;
          color:#0D0D0D'>Numero de Recibo de Materiales</span></p>
          </td>
          <td width="60" colspan=3 style='border-top:none;border-left:
          none;border-bottom:solid gray 0.5pt;border-right:solid gray 0.5pt;
          padding:0cm 5.4pt 0cm 5.4pt'>
          <p class=MsoNormal><b><span lang=ES style='font-size:7.5pt;font-family:"Arial",sans-serif;
          color:#0D0D0D'>{{$uniquestring}}</span></b></p>
          </td>
         </tr>
        @php
        $totalfilas=1;
        @endphp
        @foreach($certificado->SolicitudServicio->SolicitudResiduo as $Residuo)
        @foreach ($certificado->certdato as $certdato)
        @if($Residuo->ID_SolRes == $certdato->FK_DatoCertSolRes)
        @php
        $totalfilas=++$totalfilas;
        @endphp
        @endif
        @endforeach
        @endforeach
         <tr style='height:11.1pt' width=35>
          <td width="40" rowspan={{$totalfilas}} style='border:solid gray 0.5pt;
          border-top:none;padding:0cm 5.4pt 0cm 5.4pt;height:11.1pt'>
          <p class=MsoNormal><span lang=ES style='font-size:7.5pt;font-family:"Arial",sans-serif;
          color:#0D0D0D'>Descripción del Material</span></p>
          </td>
          <td width="20" style='border-top:none;border-left:none;border-bottom:
          solid gray 0.5pt;border-right:solid gray 0.5pt;padding:0cm 5.4pt 0cm 5.4pt;
          height:11.1pt'>
          <p class=MsoNormal align=center style='text-align:center'><b><span lang=ES
          style='font-size:7.5pt;font-family:"Arial",sans-serif;color:#0D0D0D'>DESCRIPCIÓN</span></b></p>
          </td>
          <td width="60" style='border-top:none;border-left:none;border-bottom:
          solid gray 0.5pt;border-right:solid gray 0.5pt;padding:0cm 5.4pt 0cm 5.4pt;
          height:11.1pt'>
          <p class=MsoNormal align=center style='text-align:center'><b><span lang=ES
          style='font-size:7.5pt;font-family:"Arial",sans-serif;color:#0D0D0D'>CORRIENTE</span></b></p>
          </td>
          <td width="60" style='border-top:none;border-left:none;
          border-bottom:solid gray 0.5pt;border-right:solid gray 0.5pt;padding:0cm 5.4pt 0cm 5.4pt;
          height:11.1pt'>
          <p class=MsoNormal align=center style='text-align:center'><b><span lang=ES
          style='font-size:7.5pt;font-family:"Arial",sans-serif;color:#0D0D0D'>PESO
          (kg)</span></b></p>
          </td>
         </tr>
         @php
        $totalKg = 0;
        @endphp
        @foreach($certificado->SolicitudServicio->SolicitudResiduo as $Residuo)
        @foreach ($certificado->certdato as $certdato)
        @if($Residuo->ID_SolRes == $certdato->FK_DatoCertSolRes)
         <tr style='height:3.2pt' width=35>
          <td width="30" style='border-top:none;border-left:none;border-bottom:
          solid gray 0.5pt;border-right:solid gray 0.5pt;padding:0cm 5.4pt 0cm 5.4pt;
          height:3.2pt'>
          <p class=MsoNormal><span lang=ES style='font-size:7.5pt;font-family:"Arial",sans-serif'>{{$Residuo->generespel->respels->RespelName}}</span></p>
          </td>
          <td width="15" style='border-top:none;border-left:none;border-bottom:
          solid gray 0.5pt;border-right:solid gray 0.5pt;padding:0cm 5.4pt 0cm 5.4pt;
          height:3.2pt'>
          <p class=MsoNormal align=center style='text-align:center'><span lang=ES
          style='font-size:7.5pt;font-family:"Arial",sans-serif'>
          @if($Residuo->generespel->respels->RespelIgrosidad == 'No peligroso')
          N/A
          @else
          {{$Residuo->generespel->respels->YRespelClasf4741}}{{$Residuo->generespel->respels->ARespelClasf4741}}
          @endif
          </span></p>
          </td>
          <td width="15" style='border-top:none;border-left:none;
          border-bottom:solid gray 0.5pt;border-right:solid gray 0.5pt;padding:0cm 5.4pt 0cm 5.4pt;
          height:3.2pt'>
          <p class=MsoNormal align=center style='text-align:center'><span lang=ES
          style='font-size:7.5pt;font-family:"Arial",sans-serif;color:black'>{{$Residuo->SolResKgConciliado === null ? 'N/A' : $Residuo->SolResKgConciliado }}</span></p>
          </td>
         </tr>
        @if($Residuo->SolResKgConciliado !== null)
        @php
            $totalKg = $totalKg+$Residuo->SolResKgConciliado;
        @endphp
        @endif
        @endif
        @endforeach
        @endforeach
         <tr width=35>
          <td width="40" style='width:121.65pt;border:solid gray 0.5pt;border-top:none;
          padding:0cm 5.4pt 0cm 5.4pt'>
          <p class=MsoNormal><span lang=ES style='font-size:7.5pt;font-family:"Arial",sans-serif;
          color:#0D0D0D'>Cantidad Total (kg)</span></p>
          </td>
          <td width="60" colspan=3 style='border-top:none;border-left:
          none;border-bottom:solid gray 0.5pt;border-right:solid gray 0.5pt;
          padding:0cm 5.4pt 0cm 5.4pt'>
          <p class=MsoNormal><b><span lang=ES style='font-size:7.5pt;font-family:"Arial",sans-serif;
          color:black'>{{$totalKg}}</span></b><b><span lang=ES style='font-size:7.5pt;
          font-family:"Arial",sans-serif;color:#0D0D0D'> kg</span></b></p>
          </td>
         </tr>
         <tr>
          <td width="40" style='border:solid gray 0.5pt;border-top:none;
          padding:0cm 5.4pt 0cm 5.4pt'>
          <p class=MsoNormal><span lang=ES style='font-size:7.5pt;font-family:"Arial",sans-serif;
          color:#0D0D0D'>Mes del Tratamiento</span></p>
          </td>
          <td width="60" colspan=3 style='width:280.05pt;border-top:none;border-left:
          none;border-bottom:solid gray 0.5pt;border-right:solid gray 0.5pt;
          padding:0cm 5.4pt 0cm 5.4pt'>
          <p class=MsoNormal><span lang=ES style='font-size:7.5pt;font-family:"Arial",sans-serif;
          color:#0D0D0D'>@php
          if($certificado->recepcion != ""){
          $añorecepcion=date('Y', strtotime($certificado->recepcion));
          $mesrecepcion=date('m', strtotime($certificado->recepcion));
          $diarecepcion=date('d', strtotime($certificado->recepcion));
          $mesrecepciontexto = "";
          switch ($mesrecepcion) {
          case 1:
          $mesrecepciontexto = 'Enero';
          break;
        
          case 2:
          $mesrecepciontexto = 'Febrero';
          break;
        
          case 3:
          $mesrecepciontexto = 'Marzo';
          break;
        
          case 4:
          $mesrecepciontexto = 'Abril';
          break;
        
          case 5:
          $mesrecepciontexto = 'Mayo';
          break;
        
          case 6:
          $mesrecepciontexto = 'Junio';
          break;
        
          case 7:
          $mesrecepciontexto = 'Julio';
          break;
        
          case 8:
          $mesrecepciontexto = 'Agosto';
          break;
        
          case 9:
          $mesrecepciontexto = 'Septiembre';
          break;
        
          case 10:
          $mesrecepciontexto = 'Octubre';
          break;
        
          case 11:
          $mesrecepciontexto = 'Noviembre';
          break;
        
          case 12:
          $mesrecepciontexto = 'Diciembre';
          break;
          }
          }
          @endphp
          {{$mesrecepciontexto}} del {{$añorecepcion}}</span></p>
          </td>
         </tr>
         <tr>
          <td width="40" style='border:solid gray 0.5pt;border-top:none;
          padding:0cm 5.4pt 0cm 5.4pt'>
          <p class=MsoNormal><span lang=ES style='font-size:7.5pt;font-family:"Arial",sans-serif;
          color:#0D0D0D'>Observaciones</span></p>
          </td>
          <td width="60" colspan=3 style='border-top:none;border-left:
          none;border-bottom:solid gray 0.5pt;border-right:solid gray 0.5pt;
          padding:0cm 5.4pt 0cm 5.4pt'>
          <p class=MsoNormal><span lang=ES style='font-size:7.5pt;font-family:"Arial",sans-serif;
          color:#0D0D0D'>Ninguna.</span></p>
          </td>
         </tr>
        </table>
        
        </div>
        
        <p class=MsoNormal style='text-align:justify'><span lang=ES style='font-size:
        7.5pt;font-family:"Arial",sans-serif;color:#0D0D0D'>&nbsp;</span></p>
        
        <p class=MsoNormal style='margin-left:60.4pt;margin-right:60.4pt;text-align:justify'><span lang=ES style='font-size:
        7.5pt;font-family:"Arial",sans-serif;color:#0D0D0D'>
        El material será entregado al gestor (<b>{{$certificado->gestor->ID_Cli == 1 ? $certificado->gestor->CliShortname : $certificado->gestor->CliName}}</b>), empresa autorizada para el tratamiento <b>{{$certificado->tratamiento->TratName}}</b> de acuerdo con los requerimientos técnicos y ambientales establecidos.
        </span></p>
        
        <p class=MsoNormal style='text-align:justify'><b><span lang=ES
        style='font-size:7.5pt;font-family:"Arial",sans-serif;color:#0D0D0D'>&nbsp;</span></b></p>
        @php
        $añofirma=date('Y', strtotime(now()));
        $mesfirma=date('m', strtotime(now()));
        $diafirma=date('d', strtotime(now()));
        $mesTexto = "";
        switch ($mesfirma) {
            case 1:
            $mesTexto = 'Enero';
            break;
        
            case 2:
            $mesTexto = 'Febrero';
            break;
        
            case 3:
            $mesTexto = 'Marzo';
            break;
        
            case 4:
            $mesTexto = 'Abril';
            break;
        
            case 5:
            $mesTexto = 'Mayo';
            break;
        
            case 6:
            $mesTexto = 'Junio';
            break;
        
            case 7:
            $mesTexto = 'Julio';
            break;
        
            case 8:
            $mesTexto = 'Agosto';
            break;
        
            case 9:
            $mesTexto = 'Setiembre';
            break;
        
            case 10:
            $mesTexto = 'Octubre';
            break;
        
            case 11:
            $mesTexto = 'Noviembre';
            break;
        
            case 12:
            $mesTexto = 'Diciembre';
            break;
            }
        @endphp
        <p class=MsoNormal style='margin-left:95.4pt;text-align:justify;text-indent:
        -35.4pt'><b><span lang=ES style='font-size:7.5pt;font-family:"Arial",sans-serif;
        color:#0D0D0D'>Para constancia se firma en Mosquera, el día {{$diafirma}} de {{$mesTexto}} del {{$añofirma
        }}.</span></b></p>
        </div>
            </body><br>
        
            <footer>

              <div style='margin-left:140.4pt;text-align:center;'>
                <table class=MsoTableGrid border=0 cellspacing=0 cellpadding=0
                 style='border-collapse:collapse;border:none' width=30%>
                 <tr style='height:8.1pt'width=30%>
                    
                    <td style='padding:0cm 0.4pt 0cm 0.4pt;height:8.1pt' width=10%>
                        <p class=MsoNormal style='text-align:justify'><span lang=ES><img width=118
                        height=76 id="Imagen 6"
                        src="/img/FirmaDuvan.png"></span></p>
                    </td>
        
                    <td style='padding:0cm 0.4pt 0cm 0.4pt;height:8.1pt' width=10%>
                        <p class=MsoNormal style='text-align:justify'><span lang=ES><img width=110
                        height=76 id="Imagen 4"
                        src="/img/FirmaJohn.png"></span></p>
                    </td>
        
                    <td width=100 valign=top style='width:142.4pt;padding:0cm 0.4pt 0cm 0.4pt' width=10%>
                        <p class=MsoNormal style='text-align:justify'><span lang=ES><img width=118
                        height=76 id="Imagen 5"
                        src="/img/VictorVelasco2.png"></span></p>
                    </td>
                 </tr>
        
                 <tr style='height:8.1pt'width=30%>
                    <td style='padding:0cm 0.4pt 0cm 0.4pt;height:8.1pt' width=10%> 
                        <p class=MsoNormal style='text-align:center'><b><span lang=ES
                        style='font-size:7.5pt;font-family:"Arial",sans-serif'>DUVAN CAMPOS</span></b></p>
                        <p class=MsoNormal style='text-align:center'><b><span lang=ES
                        style='font-size:7.5pt;font-family:"Arial",sans-serif'>Jefe de Logística</span></b></p>
                    </td>        
                    <td style='padding:0cm 0.4pt 0cm 0.4pt;height:8.1pt' width=10%>
                        <p class=MsoNormal style='text-align:justify'><b><span lang=ES
                        style='font-size:7.5pt;font-family:"Arial",sans-serif'>JOHN JAWER LOPEZ</span></b></p>
                        <p class=MsoNormal style='text-align:justify'><b><span lang=ES
                        style='font-size:7.5pt;font-family:"Arial",sans-serif'>Vo. Bo.</span></b></p>
                        <p class=MsoNormal style='text-align:justify'><b><span lang=ES
                        style='font-size:7.5pt;font-family:"Arial",sans-serif'>Director de Planta</span></b></p>
                    </td>         
                    <td style='padding:0cm 0.4pt 0cm 0.4pt;height:8.1pt' width=10%>
                        <p class=MsoNormal><b><span lang=ES style='font-size:7.5pt;font-family:"Arial",sans-serif'>VICTOR VELASCO</span></b></p>
                        <p class=MsoNormal style='text-align:justify'><b><span lang=ES
                         style='font-size:7.5pt;font-family:"Arial",sans-serif'>Jefe Técnico</span></b></p>
                    </td>
                    </tr>
                </table> 
                </div>

              <div style="margin-left:80.4pt">
                <table cellpadding="0" cellspacing="0" >
                        <div class="invoice-box header-footer">
                          <table cellpadding="0" cellspacing="0">
                            <tr>
                              <td colspan="2">
                                <table>
                                  <tr>
                                    <td style="text-align: left; font-size: 8px;">
                                     {{--<img src="{{$qrCode->writeDataUri()}}"style="width: 120px;"  alt="" id="inputQrImg"><br>--}}
                                    </td>
                                  </tr>
                                </table>
                              </td>
                              <td colspan="2" style="vertical-align: bottom;">
                                <table style="margin-left:15.4pt;">
                                  <tr>
                                    <td style="font-size: 12px; line-height: 11px;"> <b></b>
                                      <b>La lectura de este QR lo llevara a: https://sispro.prosarc.com</b><br><br>
                                    </td>
                                  </tr>
                                </table>
                              </td>
                            </tr>
                          </table>
                        </div>
                </table>
              </div>  

              <div class="invoice-box header-footer">
                <table cellpadding="0" cellspacing="0">
                  <tr>
                    <td colspan="2">
                      <table>
                        <tr>
                          <td style="text-align: left; font-size: 8px;">
                          </td>
                        </tr>
                      </table>
                    </td>
                    <td colspan="2" style="vertical-align: bottom;">
                      <table>
                        <tr>
                          <td style="text-align: right; font-size: 10px; line-height: 11px;"> <b></b>
                            <b>Planta de procesos</b>: kilómetro 6, vía a la mesa<br>
                            sub estación Balsillas <b>Mosquera Cundinamarca</b><br>
                            <b>Telefax:</b> (571) 742 5395 – 7425417<br>
                            <b>Celular:</b> 317 667 3032 – 317 667 3035<br>
                            <br>
                            <b>Sede administrativa y comercial:</b><br>
                            Calle 120 A No 7 – 62/68 Of. 605 <b>Bogotá, D.C. - Colombia</b><br>
                            <b>PBX-FAX</b> 629 9853 - 637 5112 <b>Servicio al cliente</b> 316 439 3895<br>
                            <b>www.prosarc.com</b><br>
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                </table>
              </div>
            </footer>
</html>