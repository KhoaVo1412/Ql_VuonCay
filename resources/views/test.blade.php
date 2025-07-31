@foreach ($x as $key => $i)
@php
$oldValues = is_string($i->old_values) ? json_decode($i->old_values, true) : $i->old_values;
$new_values = is_string($i->new_values) ? json_decode($i->new_values, true) : $i->new_values;
@endphp

<p>{{ $oldValues['name'] ?? 'Không có tên' }}</p>
<p>{{ $new_values['name'] ?? 'Không có tên' }}</p>
@endforeach