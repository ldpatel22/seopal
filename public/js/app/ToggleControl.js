const ToggleControl = function($target,$toggle,behavior) {
    let isShown = false;
    let isAnimating = false;
    behavior = (typeof behavior !== 'undefined') ? behavior : 'slide';

    let toggle = (show) => {
        isAnimating = true;
        switch (behavior) {
            case 'slide':
                if(!show) $target.slideUp(250,() => { isAnimating = false; });
                else $target.slideDown(250,() => { isAnimating = false; });
                break;
            default:
                if(!show) $target.hide();
                else $target.show();
                isAnimating = false;
        }
       
       isShown = show;
        
    }

    $toggle.on('click',() => {
        if(isAnimating) return;
        toggle(!isShown);
    });

    this.toggle = (show) => { toggle(show); }
    return this;
};
