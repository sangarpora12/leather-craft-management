@component('mail::message')

# {{ $title }}

{{ $message }}

@if(!empty($data))
@component('mail::table')
| Parameter | Nilai |
| :-------- | :---- |
@foreach($data as $key => $value)
| **{{ $key }}** | {{ $value }} |
@endforeach
@endcomponent
@endif

@if($buttonText && $buttonUrl)
@component('mail::button', ['url' => $buttonUrl, 'color' => $alertType == 'warning' ? 'orange' : ($alertType == 'danger' ? 'red' : 'green')])
{{ $buttonText }}
@endcomponent
@endif

Hormat kami,<br>
**Berliano Leather**
---
*Ini adalah email otomatis dari sistem produksi. Mohon tidak membalas email ini.*

@endcomponent