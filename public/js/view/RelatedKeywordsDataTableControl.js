const RelatedKeywordsDataTableControl = function () {

    let $table = $('#table_Keywords');
    let formatter = new KeywordsDataFormatter();

    let fetchRelatedKeywords = (id, $td) => {
        $.ajax({
            type: 'GET',
            url: App.View.Routes.getRelatedKeywords,
            data: { id: id },
            success: (response) => {
                if(response.keywords.length == 0) {
                    $('tr[data-id="' + id + '"]').after( function() {
                        return "<tr class='related-row' data-related-id='" + id + "' data-type='related' style='display: none'>"
                            + "<td class='no-sort'></td><td colspan='8'>No related keywords found.</td>";
                    });
                }
                else for(let i in response.keywords) {
                    let keyword = response.keywords[i];
                    $('tr[data-id="' + id + '"]').after( function() {
                        return "<tr class='related-row' data-related-id='" + id + "' data-type='related' style='display: none'>"
                            + "<td class='no-sort'></td>"
                            + "<td>" + keyword.keyword + "</td>"
                            + "<td><i class='plus square outline icon primary button add-keyword' data-keyword='" + keyword.keyword + "' title='Add to your keywords'></i></td>"
                            + "<td data-key='search_volume'>" + formatter.format('search_volume',keyword.search_volume) + "</td>"
                            + "<td data-key='cpc'>" + formatter.format('cpc',keyword.cpc) + "</td>"
                            + "<td data-key='competition'>" + formatter.format('competition',keyword.competition) + "</td>"
                            + "<td data-key='intent'>" + formatter.format('intent',keyword.intent) + "</td>"
                            + "<td></td>"
                            + "<td></td>";
                    });
                }
                $td.data('loaded', 'true');
                $td.removeClass('loading');
                $table.find('tr[data-related-id="' + id + '"').toggle(250);
                $td.addClass('expanded');
            },
            error: (data) => {
                let error = (data.responseJSON && data.responseJSON.error) ? data.responseJSON.error : 'Unknown error.';
                UTIL.Alert.error(error);
            },
            dataType: 'json'
        });
    };

    // Open/Close Related Keywords
    $table.on('click', '.show-related', function () {
        let $this = $(this);
        let id = $this.data('id');
        let loaded = $this.data('loaded') === 'true';

        if(!loaded) {
            $this.addClass('loading');
            fetchRelatedKeywords(id, $this);
        } else {
            $table.find('tr.related-row[data-related-id="' + id + '"').toggle(250);
            $this.toggleClass('expanded');
        }
    });

    // Add Keyword
    $table.on('click', '.add-keyword', function(){
        let $button = $(this);
        let keyword = $button.data('keyword');

        let $loader = $('<i class="ui large grey loading spinner icon"></i>');
        $button.hide().after($loader);

        $.ajax({
            type: 'POST',
            url: App.View.Routes.addKeywords,
            data: { keywords : keyword },
            success: (response) => {
                if(response.redirect !== "") {
                    $loader.remove();
                    $button.closest('.related-row').removeClass('related-row');
                    $button.remove();
                    UTIL.Alert.success('Keyword <strong>' + keyword + '</strong> added.');
                }
            },
            error: (data) => {
                let error = (data.responseJSON && data.responseJSON.error) ? data.responseJSON.error : 'Unknown error.';
                UTIL.Alert.error(error);
            },
            dataType: 'json'
        });

    });

};
