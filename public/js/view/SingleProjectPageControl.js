const SingleProjectPageControl = function(){
    let $editForm = $('#form_EditProject');
    let $deleteModal = $('#modal_DeleteProject');

    if(App.View.Options.editProject) {
        let formToggle = new ToggleControl($editForm, $('#btn_toggleEditProject'));

        // edit form behavior
        $editForm.on('submit', function (){
            let $form = $(this);
            let data = UTIL.Form.toJson($form);

            UTIL.Form.block($form);
            $.ajax({
                type: 'PATCH',
                url: App.View.Routes.editProject,
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

        // cancel button
        $editForm.find('.cancel.button').on('click',function(e){
            e.preventDefault();
            formToggle.toggle();
            return false;
        });
    }

    if(App.View.Options.deleteProject) {
        // delete button
        $('#btn_toggleDeleteProject').on('click',function(e){
            e.preventDefault();
            $deleteModal.modal('show');
            return false;
        });

        // delete behavior
        $deleteModal.find('.primary.button').on('click', function (){
            $deleteModal.find('.button').toggleClass('disabled',true);
            $.ajax({
                type: 'DELETE',
                url: App.View.Routes.deleteProject,
                data: {projectId: $deleteModal.data('project-id')},
                success: (response) => {
                    window.location = response.redirect;
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
    }
};
