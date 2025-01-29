const FormControl = function($form, submitUrl, onSubmitSuccess, onSubmitError) {
    let _self = this;
    let fields = {};
    let $submitButton = null;

    /**
     * Reurns an array of form fields
     *
     *
     */
    _self.fields = () => { return fields; };

    /**
     * Returns a field by its name or NULL if it doesn't exist
     *
     * @param name
     * @returns {jQuery|null}
     */
    _self.field = (name) => { return fields.hasOwnProperty(name) ? fields[name] : null; };

    let handleSubmit = () => {
        let data = {};
        for(let name in fields) {
            let $field = fields[name];
            data[name] = $field.val();
        }

        UTIL.Form.block($form);
        $.ajax({
            type: 'POST',
            url: submitUrl,
            dataType: 'json',
            data: data,
            success: (response) => {
                UTIL.Form.unblock($form);
                if(typeof onSubmitSuccess !== 'undefined') onSubmitSuccess(response);
            },
            error: (response) => {
                let errorMessage = 'There was an unexpected error.';
                if(response.responseJSON) {
                    if(response.responseJSON.message) {
                        errorMessage = 'Please check your input.';
                    }
                    if(response.responseJSON.errors) {
                        for(let name in response.responseJSON.errors) {
                            let field = _self.field(name);
                            if(field !== null) {
                                field.addClass('uk-form-danger');
                                field.attr('uk-tooltip',response.responseJSON.errors[name]);
                            }
                        }
                    }
                }

                UTIL.Alert.error(errorMessage);
                if(typeof onSubmitError !== 'undefined') onSubmitError();
                UTIL.Form.unblock($form);
            }
        });
    };

    // init
    {
        let addField = ($field) => {
            fields[$field.attr('name')] = $field;
            $field.on('input',() => {
                $field.removeClass('uk-form-danger');
            });
        };
        $form.find('input,textarea,select').each(function(){
            addField($(this));
        });

        $form.find("input").on('focus click', function () {
            $(this).select();
        });

        $submitButton = $form.find('.submit');
        $form.on('submit',function(){
            handleSubmit();
            return false;
        });
        $submitButton.on('click',function(e){
            e.preventDefault();
            let $this = $(this);
            if(!$this.is(':disabled') && !$this.hasClass('disabled')) {
                $form.submit();
            }
        });

    }; return _self;

};
