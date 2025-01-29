@if($report->data['stages'][$stage] < -1) disabled @elseif($report->data['stages'][$stage] == 1) active @endif
