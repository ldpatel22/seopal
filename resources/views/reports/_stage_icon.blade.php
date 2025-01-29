@if($report->data['stages'][$stage] < -1)
    <i class="delete icon"></i>
@elseif($report->data['stages'][$stage] == -1)
    <i class="red delete icon"></i>
@elseif($report->data['stages'][$stage] == 0)
    <i class="grey spinner icon"></i>
@elseif($report->data['stages'][$stage] == 1)
    <i class="yellow loading spinner icon"></i>
@elseif($report->data['stages'][$stage] > 1)
    <i class="green check icon"></i>
@endif
