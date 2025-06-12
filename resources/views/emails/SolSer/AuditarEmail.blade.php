'{{-- @php
    $url = url("/solicitud-servicio/{$AuditarEmail->SolSerSlug}");
    $nameButton = 'Ver Solicitud de Servicio'; $SolicitudServicio;
@endphp --}}

@component('mail::message')
# Solicitud de Servicio N° {{$SolicitudServicio->ID_SolSer}}

@switch ($SolicitudServicio->SolSerAuditable) 
        @case ('Virtual')     
            @php 
               $text = 'Se ha solicitado un servicio con auditoria Virtual';
            @endphp   
            @break
        @case ('Presencial')
            @php 
               $text = 'Se ha solicitado un servicio con auditoria Precencial';
            @endphp   
            @break
@endswitch
@component('mail::message')

Le informamos que el cliente {{ $SolicitudServicio['cliente']->CliName }} ha presentado una solicitud de servicio que requiere una auditoría. Para obtener más detalles, no dude en  contactar al solicitante <br>
<p style="background-color:#f0f3f8;"><i>{!!nl2br($SolicitudServicio->SolSerDescript)!!}</i></p>
@endcomponent

@component('mail::button', ['url' => url('/solicitud-servicio', [$SolicitudServicio->SolSerSlug])])
{{-- {{$nameButton}} --}}
Ver Solicitud
@endcomponent

{{-- @component('mail::subcopy')
@lang(
    "Si tiene problemas para hacer clic en el botón \":actionText\", copie y pegue la siguiente URL \nen su navegador web: [:actionURL](:actionURL)",
    [
        'actionText' => $nameButton,
        'actionURL' => $url,
    ]
)
@endcomponent --}}