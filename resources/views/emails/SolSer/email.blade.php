{{-- @php
    $url = url("/solicitud-servicio/{$email->SolSerSlug}");
    $nameButton = 'Ver Solicitud de Servicio';
@endphp --}}
@component('mail::message')
# Solicitud de Servicio N° {{$email->ID_SolSer}}

@switch($email->SolSerStatus)
    @case('Aprobado')
        @php
            $text = 'ha sido aprobada, ahora queda en espera para asignarle una programación';
        @endphp
        @break
    @case('Programado')
        @php
            setlocale(LC_ALL, "es_CO.UTF-8");
			if(date('H', strtotime($email->ProgVehSalida)) >= 12){
				$horas = " en las horas de la tarde";
            }else{
				$horas = " en las horas de la mañana";
            }
            $TextProgramacion = "el día ".strftime("%d", strtotime($email->ProgVehSalida))." del mes de ".strftime("%B", strtotime($email->ProgVehSalida)).$horas;
            $text = "ha sido Programada para $TextProgramacion";
        @endphp
        @break
    @case('Notificado')
        @php
            setlocale(LC_ALL, "es_CO.UTF-8");
            if(date('H', strtotime($email->ProgVehSalida)) >= 12){
                $horas = " en las horas de la tarde";
            }else{
                $horas = " en las horas de la mañana";
            }
            $TextProgramacion = "el día ".strftime("%d", strtotime($email->ProgVehSalida))." del mes de ".strftime("%B", strtotime($email->ProgVehSalida)).$horas;
            $text = "ha sido Programada para $TextProgramacion";
        @endphp
        @break
    @case('Completado')
        @php
            $text = 'esta lista para realizar una conciliación... por favor revise los pesos y/o cantidades conciliadas en cada uno de los residuos, y luego use el botón (Conciliado) para dar inicio al tratamiento de los residuos';
        @endphp
        @break
    @case('No Conciliado')
        @php
            $text = " ha sido rechazada por el cliente $email->CliName, ya que no esta de acuerdo con algunas de las cantidades enviadas a conciliación... se deben verificar las cantidades y enviar de nuevo a conciliación";
        @endphp
        @break
    @case('Conciliado')
        @php
            $text = "ha sido aceptada satisfactoriamente por el cliente $email->CliName, según las cantidades enviadas a conciliación... esto permite dar inicio al registro de las cantidades tratadas para cada residuo de la solicitud de servicio";
        @endphp
        @break
    @case('Certificacion')
        @php
            $text = 'ha sido Certificada con éxito. esperamos que el proceso haya sido realizado a su entera satisfacción, ¡Gracias por su preferencia!';
        @endphp
        @break
@endswitch

En estos momentos la Solicitud de Servicio N° {{$email->ID_SolSer}} {{$text}}.<br>

@if ($email->SolSerStatus === 'No Conciliado')
## @lang('¿Por qué?')

*@lang($email->SolSerDescript)*<br><br>

@lang("Puede comunicarse con:")<br>

***@lang("Nombre: ")***{{$email->PersFirstName}} {{$email->PersLastName}}<br>

***@lang("E-mail: ")***{{$email->PersEmail}}<br>
@endif

@component('mail::button', ['url' => url('/solicitud-servicio', [$email->SolSerSlug])])
{{-- {{$nameButton}} --}}
Ver Solicitud
@endcomponent

@if ($email->SolSerStatus === 'Conciliado' || $email->SolSerStatus === 'No Conciliado')
    @php
        $end = 'Por favor dar click en el botón para ver más detalles.';
    @endphp
@else
    @php
        $end = 'Si tiene alguna duda no olvide comunicarse con su asesor comercial. Saludos, Prosarc S.A. ESP.';
    @endphp
@endif

{{$end}}

{{-- @component('mail::subcopy')
@lang(
    "Si tiene problemas para hacer clic en el botón \":actionText\", copie y pegue la siguiente URL \nen su navegador web: [:actionURL](:actionURL)",
    [
        'actionText' => $nameButton,
        'actionURL' => $url,
    ]
)
@endcomponent --}}
@endcomponent
