const SingleKeywordPageControl = function(){
    let $deleteModal = $('#modal_DeleteKeyword');

    // reports table
    $('#table_Reports').DataTable({
        paging: true,
        searching: true,
        info: false
    });

    if(App.View.Options.deleteKeyword) {
        // delete button
        $('#btn_toggleDeleteKeyword').on('click',function(e){
            e.preventDefault();
            $deleteModal.modal('show');
            return false;
        });

        // delete behavior
        $deleteModal.find('.primary.button').on('click', function (){
            $deleteModal.find('.button').toggleClass('disabled',true);
            $.ajax({
                type: 'DELETE',
                url: App.View.Routes.deleteKeyword,
                data: {keywordId: $deleteModal.data('keyword-id')},
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
