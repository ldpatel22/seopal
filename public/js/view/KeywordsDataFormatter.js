const KeywordsDataFormatter = function() {

    const THIS = this;

    const INTENTS = [
        // 0 - Commercial
        '<span class="ui tiny yellow label">C</span>',
        // 1 - Informational
        '<span class="ui tiny blue label">I</span>',
        // 2 - Navigational
        '<span class="ui tiny violet label">N</span>',
        // 3 - Transactional
        '<span class="ui tiny green label">C</span>'
    ];

    /**
     * Formats keyword cdata
     *
     * @param {string} key
     * @param {*} value
     *
     * @returns {string}
     */
    THIS.format = (key,value) => {
        let formattedValue = value;
        switch (key) {
            case 'search_volume':
                formattedValue = parseInt(value).toLocaleString();
                break;
            case 'cpc':
                formattedValue = '$' + parseFloat(value).toLocaleString();
                break;
            case 'organic_results':
                formattedValue = parseInt(value).toLocaleString();
                break;
            case `intent`:
                if(INTENTS[parseInt(value)]) {
                    formattedValue = INTENTS[parseInt(value)];
                }
                break;
        }
        return formattedValue;
    };

    return THIS;

};
