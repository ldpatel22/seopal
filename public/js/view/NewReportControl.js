const NewReportControl = function(){
    let $form = $('#form_NewReport');
    let $toggleButton = $('#btn_toggleStartReport');

    let formToggle = new ToggleControl($form, $toggleButton);

    // init form behavior
    $form.on('submit', function (){
        let $form = $(this);
        let data = UTIL.Form.toJson($form);
        $('#form_NewReport .inline-spinner').removeClass("hide"); /* 387: For starter, show the spinner so that we know that the report is working in background */

        let csrfToken = $('meta[name="csrf-token"]').attr('content'); // Adjust this if your CSRF token is elsewhere
        if (csrfToken) {
            data._token = csrfToken; // Append CSRF token to form data
        }

        UTIL.Form.block($form);
        $.ajax({
            type: 'POST',
            url: App.View.Routes.newReport,
            data: data,
            success: (response) => {
                window.location = response.redirect;
                console.log(response);
            },
            error: (data) => {
                console.log(response);
                //return;
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

    // auto-open run report list
    if(UTIL.Request.get('newReport')) {
        $toggleButton.click();
        $form.find('[name="keywords"]').focus();
    }
};
