@if($device == 'desktop')
    <i class="laptop icon"></i>
    @unless($iconOnly) <span>desktop</span> @endunless
@elseif($device == 'tablet')
    <i class="tablet alternate icon"></i>
    @unless($iconOnly) <span>tablet</span> @endunless
@elseif($device == 'mobile')
    <i class="mobile alternate icon"></i>
    @unless($iconOnly) <span>mobile</span> @endunless
@endif
