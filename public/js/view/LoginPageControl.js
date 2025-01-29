const LoginPageControl = function() {
    let $form = $('#form_logIn');
    $('#btn_attemptLogin').on('click', () => { $form.submit(); });

    $form.on('submit',() => {
        submitLogin({
            'email': $form.find('[name="email"]').val(),
            'password': $form.find('[name="password"]').val()
        });
        return false;
    });

    let submitLogin = (data) => {
        UTIL.Form.block($form);

        $.ajax({
            type: 'POST',
            url: App.View.Routes.submitLogin,
            data: data,
            success: () => {
                window.location = App.View.Routes.redirectUrl;
            },
            error: (data) => {
                UTIL.Form.unblock($form);
                let error = (data.responseJSON && data.responseJSON.error) ? data.responseJSON.error : 'Invalid credentials.';
                $('body').toast({class: 'error', message: error});
            },
            dataType: 'json'
        });
    };
}


document.addEventListener("keypress", function(event) {
    if (event.key === "Enter") {
      event.preventDefault();
      document.getElementById("btn_attemptLogin").click();
    }
});