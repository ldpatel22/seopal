const NewKeywordControl = function(){
    let $form = $('#form_AddKeywords');
    let $toggleButton = $('#btn_toggleAddKeywords');

    let formToggle = new ToggleControl($form, $toggleButton);

    // init form behavior
    $form.on('submit', function (){
        let $form = $(this);
        let data = UTIL.Form.toJson($form);

        UTIL.Form.block($form);
        $.ajax({
            type: 'POST',
            url: App.View.Routes.addKeywords,
            data: data,
            success: (response) => {
                window.location = response.redirect;
            },
            error: (data) => {
                let error = (data.responseJSON && data.responseJSON.error) ? data.responseJSON.error : 'Unknown error.';
                UTIL.Alert.error(error);
                UTIL.Form.unblock($form);
            },
            dataType: 'json'
        });

        return false;
    });

    // cancel button
    $form.find('.cancel.button').on('click',function(e){
        e.preventDefault();
        formToggle.toggle();
        return false;
    });

    // auto-open keywords list
    if(UTIL.Request.get('addKeywords')) {
        $toggleButton.click();
        $form.find('[name="keywords"]').focus();
    }
};
