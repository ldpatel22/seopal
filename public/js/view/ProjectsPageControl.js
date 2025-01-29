const ProjectsPageControl = function(){
    let $form = $('#form_NewProject');

    let formToggle = new ToggleControl($form, $('#btn_toggleAddProject'));

    // init form behavior
    $form.on('submit', function (){
        let $form = $(this);
        let data = UTIL.Form.toJson($form);

        UTIL.Form.clearErrors($form);
        UTIL.Form.block($form);
        $.ajax({
            type: 'POST',
            url: App.View.Routes.newProject,
            data: data,
            success: (response) => {
                window.location = response.redirect;
            },
            error: (data) => {
                console.log(JSON.stringify(data.responseJSON));
                let error = (data.responseJSON && data.responseJSON.message) ? data.responseJSON.message : 'Unknown error.';
                if(data.responseJSON && data.responseJSON.errors) {
                    for(let key in data.responseJSON.errors) {
                        let $field = $form.find('[name="'+key+'"]').closest('.field');
                        UTIL.Form.Field.addError($field,'<p>'+data.responseJSON.errors[key]+'</p>');
                    }
                }
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
};
