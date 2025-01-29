const UTIL = {

    Core: {



    },

    Request: {

        params: () => {
            return new URLSearchParams(window.location.search);
        },

        get: (param) => {
            let params = UTIL.Request.params();
            return params.get(param);
        },

    },

    Alert: {

        /**
         * Shows a success alert
         *
         * @param {String} message
         */
        success: (message) => {
            $('body').toast({
                class: 'success',
                className: { toast: 'ui message' },
                message: message
            });
        },

        /**
         * Shows an error alert
         *
         * @param {String} message
         */
        error: (message) => {
            $('body').toast({
                class: 'error',
                className: { toast: 'ui message' },
                message: message
            });
        },

        /**
         * Shows a warning alert
         *
         * @param {String} message
         */
        warning: (message) => {
            $('body').toast({
                class: 'warning',
                className: { toast: 'ui message' },
                message: message
            });
        },

    },

    Form: {

        /**
         * Blocks form input
         *
         * @param {jQuery} $form
         */
        block: ($form) => {
            $form.find('input,select,textarea').attr('disabled',true);
            $form.find('button[value="submit"]').attr('disabled',true).addClass('disabled');
            //$form.find('.loader').removeClass('hidden');
            $form.find('.submit').attr('disaabled',true).toggleClass('disabled',true);
        },

        /**
         * Unblocks form input
         *
         * @param {jQuery} $form
         */
        unblock: ($form) => {
            $form.find('input,select,textarea').removeAttr('disabled');
            $form.find('button[value="submit"]').removeAttr('disabled').removeClass('disabled');
            //$form.find('.loader').addClass('hidden');
            $form.find('.submit').removeAttr('disaabled',true).toggleClass('disabled',false);
        },

        /**
         * Clears form input
         *
         * @param {jQuery} $form
         * @returns {*}
         */
        clear: ($form) => {
            $form.find('input,select,textarea').val('');
        },

        /**
         * Clears all errors from a form
         *
         * @param {jQuery} $form
         */
        clearErrors: ($form) => {
            $form.find('.field').each(function(){
                UTIL.Form.Field.cleanError($(this));
            });
            $form.removeClass('error');
        },

        /**
         * Serializes form data into a JSON object
         *
         * @param {jQuery} $form
         * @returns {*}
         */
        toJson: ($form) => {
            let obj = {};
            let arr = $form.serializeArray();
            $.each(arr, function () {
                if (obj[this.name]) {
                    if (!obj[this.name].push) {
                        obj[this.name] = [obj[this.name]];
                    }
                    obj[this.name].push(this.value || '');
                } else {
                    obj[this.name] = this.value || '';
                }
            });
            return obj;
        },

        Field: {

            /**
             * Cleans error form a field
             *
             * @param {jQuery} $field
             */
            cleanError: ($field) => {
                $field.find('.error.message').remove();
            },

            /**
             * Adds an error form a field
             *
             * @param {jQuery} $field
             * @param {String} error
             */
            addError: ($field, error) => {
                let $error = $('<div class="ui error message"></div>').append(error);
                $field.append($error);
                $field.closest('.form').addClass('error');
            },

        },

    },

    Page: {

        /**
         * Scrolls the window to an element
         *
         * @param {jQuery} $component
         * @param {Number} offset (optional)
         */
        scrollTo: ($component, offset) => {
            offset = offset || 0;
            $('html, body').animate({
                scrollTop: $component.offset().top - offset
            }, 750);
        },

    },

    Validate: {
        Integer: () => {
            return (value) => {
                return !Number.isNaN(value) && Number.isInteger(parseInt(value));
            };
        },

        MinNumber: (min) => {
            return (value) => {
                return !Number.isNaN(value) && value >= min;
            };
        },

        MaxNumber: (max) => {
            return (value) => {
                return !Number.isNaN(value) && value <= max;
            };
        },

        Email: () => {
            return (value) => {
                return /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/.test(value);
            };
        },

        MinLength: (min) => {
            return (value) => {
                return (typeof value === 'string') && value.length >= min;
            };
        },

        MaxLength: (max) => {
            return (value) => {
                return (typeof value === 'string') && value.length <= max;
            };
        },
    }

};
