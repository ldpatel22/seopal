const UserProfilePageControl = function(){
    let $profileForm = $('#form_EditProfile');
    let $passwordForm = $('#form_EditPassword');
    let $editForm = $('#form_EditUser');
    let $addForm = $('#form_AddUser');
    let $deleteButton = $('.delete-user')
    let $deleteModal = $('#modal_DeleteUser');

    let profileToggle, passwordToggle;
    profileToggle = new ToggleControl($profileForm, $('#btn_toggleEditProfile').on('click', () => {
        passwordToggle.toggle(false);
    }));
    passwordToggle = new ToggleControl($passwordForm, $('#btn_toggleEditPassword').on('click', () => {
        profileToggle.toggle(false);
    }));


    $deleteButton.on('click', function(){

        $deleteModal.modal('show');

        $deleteModal.find('.primary.button').on('click', function (){
            $deleteModal.find('.button').toggleClass('disabled',true);
            $.ajax({
                type: 'DELETE',
                url: App.View.Routes.deleteUser,
                data: {id: $deleteButton.data('id')},
                success: (response) => {
                    $deleteModal.modal('hide');
                    UTIL.Alert.success('Sucessfully deleted user!');
                    setTimeout(function() {
                        window.location.reload();
                    }, 2000);

                },
                error: (data) => {
                    let error = (data.responseJSON && data.responseJSON.error) ? data.responseJSON.error : 'Unknown error.';
                    $deleteModal.find('.button').toggleClass('disabled',false);
                    $deleteModal.modal('hide');
                    UTIL.Alert.error(error);
                },
                dataType: 'json'
            });
        });

    });


    // form behavior
    $editForm.on('submit', function (){
        let $form = $(this);
        let data = UTIL.Form.toJson($form);

        UTIL.Form.block($form);
        $.ajax({
            type: 'PATCH',
            url: App.View.Routes.editUser,
            data: data,
            success: () => {
                UTIL.Alert.success("Sucessfully saved user's data!");
                UTIL.Form.unblock($form);
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



    // form behavior
    $addForm.on('submit', function (){
        let $form = $(this);
        let data = UTIL.Form.toJson($form);

        UTIL.Form.block($form);
        $.ajax({
            type: 'POST',
            url: App.View.Routes.addUser,
            data: data,
            success: () => {
                UTIL.Alert.success("Sucessfully added new user!");
                UTIL.Form.unblock($form);
                $addForm.trigger('reset');

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



    // form behavior
    $profileForm.on('submit', function (){
        let $form = $(this);
        let data = UTIL.Form.toJson($form);

        UTIL.Form.block($form);
        $.ajax({
            type: 'PATCH',
            url: App.View.Routes.editProfile,
            data: data,
            success: () => {
                window.location.reload();
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

    $passwordForm.on('submit', function (){
        let $form = $(this);
        let data = UTIL.Form.toJson($form);

        UTIL.Form.block($form);
        $.ajax({
            type: 'PATCH',
            url: App.View.Routes.editPassword,
            data: data,
            success: () => {
                passwordToggle.toggle(false);
                UTIL.Alert.success('Password updated!');
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

    // cancel buttons
    $profileForm.find('.cancel.button').on('click',function(e){
        e.preventDefault();
        profileToggle.toggle();
        return false;
    });
    $passwordForm.find('.cancel.button').on('click',function(e){
        e.preventDefault();
        passwordToggle.toggle();
        return false;
    });
};
