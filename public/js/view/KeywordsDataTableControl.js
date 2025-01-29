const KeywordsDataTableControl = function () {

    let $table = $('#table_Keywords');
    let IDS = $table.find('tr').map((i,tr) => {
        return $(tr).data().id;
    }).toArray();

    let formatter = new KeywordsDataFormatter();
    let datatable = null;

    /**
     * Fetches stats for all keywords
     */
    let fetchStats = () => {
        $.ajax({
            type: 'GET',
            url: App.View.Routes.getStats,
            data: { ids: IDS },
            success: (response) => {
                for(let i in response.stats) {
                    let stat = response.stats[i];
                    let $tr = $table.find('tr[data-id="'+stat.keywordId+'"]');
                    if($tr.length) {
                        for(let key in stat.stats) {
                            let value = stat.stats[key];
                            let $td = $tr.find('td[data-key="'+key+'"]');
                            if($td.length) {
                                $td.attr('data-value',value);
                                $td.data('value',value);
                                $td.attr('data-sort',value);
                                $td.data('sort',value);
                            }
                        }
                    }
                }

                initDataTable();
            },
            error: (data) => {
                let error = (data.responseJSON && data.responseJSON.error) ? data.responseJSON.error : 'Unknown error.';
                UTIL.Alert.error(error);
            },
            dataType: 'json'
        });
    };

    let initDataTable = () => {
        // init data table
        datatable = $table.DataTable({
            paging: false,
            searching: true,
            info: false,
            destroy : true,
            columnDefs: [
                { targets: 'data-attr-type', orderable: true, sort: 'number' },
                { targets: 'no-sort',  orderable: false }
            ],
            order: [[1, 'asc']]
        });

        // render stats on draw
        datatable.on('draw', (e,settings) => {
            $(e.currentTarget).find('td').each(function(){
                let $td = $(this);
                if($td.hasClass('rendered')) return;

                let key = $td.data('key');
                if(!key) return;

                let value = $td.data('value');
                if(value == null) {
                    $td.html('<div class="ui placeholder" data-key="search"><div class="short line"></div></div>');
                } else {
                    value = formatter.format(key,value);
                    $td.html(value);
                }
            });
        });

        datatable.draw();
    };

    if(!$table.hasClass('empty')) {
        initDataTable();
        fetchStats();
    }
};
