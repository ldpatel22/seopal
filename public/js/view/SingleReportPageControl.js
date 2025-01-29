const OsrPlanPhraseModalControl = function (phrasesControl, $tr) {
    let $modal = $('#osr_AddToPlannerModal').clone().appendTo($('body'));
    let data = $tr.data();
    data.reportId = phrasesControl.reportId;

    let $phrase = $modal.find('[name="phrase"]');
    $phrase.val(data.name);

    $modal.find('.approve.button').on('click', () => {
        data.name = $phrase.val();

        $tr.addClass('yellow');
        $.ajax({
            type: 'POST',
            url: App.View.Routes.osrPlanPhrase,
            data: data,
            success: () => {
                UTIL.Alert.success("Succesfully added phrase to Planner!");
            },
            error: (data) => {
                let error = (data.responseJSON && data.responseJSON.error) ? data.responseJSON.error : 'Unknown error.';
                UTIL.Alert.error(error);
                $tr.removeClass('yellow');
            },
            dataType: 'json'
        });

    });

    $modal.modal({
        autoShow: true,
        onShow: () => { $phrase.focus(); },
        onHidden: () => { $modal.remove(); }
    });
};

const OsrPhrasesControl = function(reportId, $html) {
    var THIS = this;
    let $table = $('#osr_PhrasesTable');

    let sortPhrases = () => {
        $table.find('.page-popup').popup('destroy');
        $table.find('.distribution-popup').popup('destroy');

        let $tbody = $table.find('tbody');
        $tbody.find('tr').sort(function(a, b) {
            var aSum = 0;
            $('.distribution-popup .label', a).each(function(){ aSum += parseInt($(this).text()); });
            let $a = $(a);
            if($a.hasClass('yellow')) aSum += 100;
            else if($a.hasClass('grey')) aSum -= 100;

            var bSum = 0;
            $('.distribution-popup .label', b).each(function(){ bSum += parseInt($(this).text()); });
            let $b = $(b);
            if($b.hasClass('yellow')) bSum += 100;
            else if($b.hasClass('grey')) bSum -= 100;

            return bSum-aSum;
        }).appendTo($tbody);

        $table.find('.page-popup').popup({ inline: true, on: 'click', position: 'bottom center' });
        $table.find('.distribution-popup').popup({ inline: true, on: 'click', position: 'bottom center' });
    };

    let initFilters = () => {
        $html.find('#osr_PhrasesFilter .label').on('click', function(e){
            let $this = $(this);
            let color = $this.data().color;
            let tag = $this.data().tag;
            if($this.hasClass('basic')) {
                $this.removeClass('basic').addClass(color);
                $table.addClass(tag);
            } else {
                $this.removeClass(color).addClass('basic');
                $table.removeClass(tag);
            }
        });
    };

    $table.find('[data-action="blacklist"]').on('click',function (){
        let $tr = $(this).parents('tr');
        if($tr.hasClass('grey')) return;

        $tr.addClass('hide');
        $.ajax({
            type: 'POST',
            url: App.View.Routes.osrBlacklistPhrase,
            data: { phrase: $tr.data().name, reportId: reportId },
            success: () => {
                $tr.appendTo($table.find('tbody')).addClass('grey').removeClass('hide');
            },
            error: (data) => {
                let error = (data.responseJSON && data.responseJSON.error) ? data.responseJSON.error : 'Unknown error.';
                UTIL.Alert.error(error);
                $tr.removeClass('hide');
            },
            dataType: 'json'
        });
    });


    $table.on('click', '.show-related', function () {
        let $this = $(this);
        let id = $this.data('id');
        $table.find('tr.related-row[data-related-id="' + id + '"').toggle(250);
        $this.toggleClass('expanded');
    });

    $table.find('[data-action="plan"]').on('click',function (){
        let $tr = $(this).parents('tr');
        if($tr.hasClass('yellow')) return;
        new OsrPlanPhraseModalControl(THIS,$tr);
    });

    THIS.reportId = reportId;
    THIS.sort = sortPhrases;
    sortPhrases();
    initFilters();
};

/* 387: So we can add backlinks for tracking inside the planner */
const OsrPlanBacklinksModalControl = function (phrasesControl, $tr, report_id = "") {

    let $modal_backlinks = $('.backlinks-modal').clone().appendTo($('body'));

    let data = $tr.data();

    if(phrasesControl.reportId){
        data.reportId = phrasesControl.reportId;
    }else{
        data.reportId = report_id;
    }


    let $phrase = $modal_backlinks.find('[name="backlink"]');
    $phrase.val(data.name);

    $modal_backlinks.find('.approve.button').on('click', () => {
        data.name = $phrase.val();

        $tr.addClass('yellow');
        //$modal.remove();
        $.ajax({
            type: 'POST',
            url: App.View.Routes.osrPlanBacklink,
            data: data,
            success: () => {
                UTIL.Alert.success('Backlink successfully added to your planner!');
                $modal_backlinks.remove();
            },
            error: (data) => {
                let error = (data.responseJSON && data.responseJSON.error) ? data.responseJSON.error : 'Unknown error.';
                UTIL.Alert.error(error);
                $tr.removeClass('yellow');
            },
            dataType: 'json'
        });

    });

    $modal_backlinks.modal({
        autoShow: true,
        onShow: () => { $phrase.focus(); },
        onHidden: () => { $modal_backlinks.remove(); }
    });
};

const OsrBacklinksControl = function(reportId) {
    var THIS = this;

    $(document).on('click', '#osr_BacklinksTable [data-action="plan"]', function (){
        let $tr = $(this).parents('tr');
        if($tr.hasClass('yellow')) return;
        new OsrPlanBacklinksModalControl(THIS,$tr);
    });

    THIS.reportId = reportId;
};

/* 387: So we can add bonus keywords for tracking inside the planner */
const OsrPlanBonusModalControl = function (phrasesControl, $tr, report_id = "") {

    let $modal_bonus = $('.bonus-modal').clone().appendTo($('body'));

    let data = $tr.data();

    if(phrasesControl.reportId){
        data.reportId = phrasesControl.reportId;
    }else{
        data.reportId = report_id;
    }


    let $phrase = $modal_bonus.find('[name="keyword"]');
    $phrase.val(data.name);

    $modal_bonus.find('.approve.button').on('click', () => {
        data.name = $phrase.val();

        $tr.addClass('yellow');
        //$modal.remove();
        $.ajax({
            type: 'POST',
            url: App.View.Routes.osrPlanBonus,
            data: data,
            success: () => {
                UTIL.Alert.success('Keyword successfully added to your planner!');
                $modal_bonus.remove();
            },
            error: (data) => {
                let error = (data.responseJSON && data.responseJSON.error) ? data.responseJSON.error : 'Unknown error.';
                UTIL.Alert.error(error);
                $tr.removeClass('yellow');
            },
            dataType: 'json'
        });

    });

    $modal_bonus.modal({
        autoShow: true,
        onShow: () => { $phrase.focus(); },
        onHidden: () => { $modal_bonus.remove(); }
    });
};

const OsrBonusControl = function(reportId) {
    var THIS = this;

    $(document).on('click', '.osr_BonusTable [data-action="plan"]', function (){
        alert("OK");
        let $tr = $(this).parents('tr');
        if($tr.hasClass('yellow')) return;
        new OsrPlanBonusModalControl(THIS,$tr);
    });

    THIS.reportId = reportId;
};

/* 387: So we can add bonus keywords for tracking inside the planner */
const OsrContentModalControl = function (phrasesControl, $tr, report_id = "") {

    let $modal_bonus = $('.bonus-modal').clone().appendTo($('body'));

    let data = $tr.data();

    if(phrasesControl.reportId){
        data.reportId = phrasesControl.reportId;
    }else{
        data.reportId = report_id;
    }


    let $phrase = $modal_bonus.find('[name="keyword"]');
    $phrase.val(data.name);

    $modal_bonus.find('.approve.button').on('click', () => {
        data.name = $phrase.val();

        $tr.addClass('yellow');
        //$modal.remove();
        $.ajax({
            type: 'POST',
            url: App.View.Routes.osrPlanBonus,
            data: data,
            success: () => {
                UTIL.Alert.success('Keyword successfully added to your planner!');
                $modal_bonus.remove();
            },
            error: (data) => {
                let error = (data.responseJSON && data.responseJSON.error) ? data.responseJSON.error : 'Unknown error.';
                UTIL.Alert.error(error);
                $tr.removeClass('yellow');
            },
            dataType: 'json'
        });

    });

    $modal_bonus.modal({
        autoShow: true,
        onShow: () => { $phrase.focus(); },
        onHidden: () => { $modal_bonus.remove(); }
    });
};

const OsrContentControl = function(reportId) {
    let sectionsControl = new ReportSectionsControl(reportId);
    let $menuItems = $('.tabular.menu .item');

    // init tab behavior
    $menuItems.on('click', function(){
        let $section = $('.tab[data-tab="'+$(this).data('tab')+'"]');
        loadSection($section);
    }).tab();

    let loadSection = ($section) => {
        let $loader = $section.find('.tmp');

            if($section.hasClass('loaded')) return;

            let section = $section.data('section');

            /* 387: This sections doesn't have a loading on click */
            if(section == 'osr.backlinks') return;

            $loader = $loader.clone();
            $section.html('');
            $loader.toggleClass('hide',false).appendTo($section);

            $.ajax({
                type: 'GET',
                url: App.View.Routes.getReportSection,
                data: { reportId, section },
                success: (response) => {
                    console.log(response);
                    if(response.html) {

                        let $html = $(response.html);
                        $loader.toggleClass('hide',true).after($html);
                        if(!$section.hasClass('refreshable')) {
                            $section.toggleClass('loaded',true);
                        }
                        sectionsControl.initSection(section,$html);
                    }
                },
                error: (data) => {
                    let error = (data.responseJSON && data.responseJSON.error) ? data.responseJSON.error : 'Unknown error.';
                    UTIL.Alert.error(error);
                },
                dataType: 'json'
            });

    };
};

const OsrContentRecommendationsControl = function (reportId, $html) {
    $html.find('.tmp').each(function(){
        let $this = $(this);
        let section = $this.attr('data-section');

        $.ajax({
            type: 'GET',
            url: App.View.Routes.getReportSection,
            data: { reportId, section },
            success: (response) => {
                if(response.html) {
                    $this.parent().html(response.html);
                }
            },
            error: (data) => {
                let error = (data.responseJSON && data.responseJSON.error) ? data.responseJSON.error : 'Unknown e7rror.';
                UTIL.Alert.error(error);
            },
            dataType: 'json'
        });
    });

}

const OsrPlannerControl = function(reportId, $html) {
    let $table = $('#osr_PlannerTable');
    let $addButton = $html.find('#osr_addPhrasesButton');
    let $checkboxes = $html.find('input[type="checkbox"]');
    let $deleteModal = $('#modal_DeletePlanner');

    $('.delete-planner').click(function(){

        let id = $(this).data('id');
        let type = $(this).data('type');

        $deleteModal.modal('show');

        $deleteModal.find('.primary.button').on('click', function (){
            $deleteModal.find('.button').toggleClass('disabled',true);
            $.ajax({
                type: 'DELETE',
                url: App.View.Routes.deletePlanner,
                data: {id: id, type: type},
                success: (response) => {
                    $deleteModal.modal('hide');
                    $deleteModal.find('.button').toggleClass('disabled',false);
                    UTIL.Alert.success('Sucessfully deleted this item!');
                    $('[data-id="' + id + '"][data-type="' + type + '-row"]').remove();

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

    let updatePhraseCheckbox = () => {
        var phrases = [];
        $checkboxes.not('disabled').filter(':checked').each(function(){
            phrases.push(Number.parseInt($(this).val()));
        });
        $addButton.data('phrases',phrases);
        $addButton.toggleClass('disabled',phrases.length == 0);
    };

    let importPhrases = () => {
        let phrases = $addButton.data('phrases');
        if(phrases.length == 0) return;

        // TODO block

        $.ajax({
            type: 'POST',
            url: App.View.Routes.osrImportPhrases,
            data: { reportId, phrases },
            success: () => { $('#tabNavigation [data-tab="planner"]').click(); },
            error: (data) => {
                let error = (data.responseJSON && data.responseJSON.error) ? data.responseJSON.error : 'Unknown error.';
                UTIL.Alert.error(error);
                // TODO unblock
            },
            dataType: 'json'
        });
    };

    let loadStats = () => {

        $.ajax({
            type: 'GET',
            url: App.View.Routes.osrFetchPhrasesStats,
            data: { reportId },
            success: (response) => {
                let STATS = {};
                for(let i in response.stats) {
                    let stat = response.stats[i];
                    STATS[stat.phraseId] = stat.stats;
                }
                $table.find('tr').each(function(){
                    let $tr = $(this);
                    let phraseId = $tr.attr('data-id');
                    if(STATS.hasOwnProperty(phraseId)) {
                        for(let key in STATS[phraseId]) {
                            let value = STATS[phraseId][key];
                            let formattedValue = value;
                            switch (key) {
                                case 'search_volume':
                                    formattedValue = parseInt(value).toLocaleString();
                                    break;
                                case 'cpc':
                                    formattedValue = '$' + parseFloat(value).toLocaleString();
                                    break;
                                case 'organic_results':
                                    formattedValue = parseInt(STATS[phraseId][key]).toLocaleString();
                                    break;
                            }
                            $tr.find('td[data-key="'+key+'"]').text(formattedValue);
                        }
                    }
                });
            },
            error: (data) => {
                let error = (data.responseJSON && data.responseJSON.error) ? data.responseJSON.error : 'Unknown error.';
                UTIL.Alert.error(error);
            },
            dataType: 'json'
        });

    };

    $checkboxes.on('change',updatePhraseCheckbox);
    $addButton.on('click',importPhrases);
    $html.find('.checkbox').checkbox();
    loadStats();
};

const ReportSectionsControl = function(reportId) {

    let _initHeadingsFilter = $html => {
        let $filters = $html.find('#osr_HeadingsLevelFilter .label');
        $filters.on('click', function(e){
            let $this = $(this);
            let level = $this.data().level;
            let color = $this.data().color;
            let $table = $('#osr_HeadingsTable');
            if($this.hasClass('basic')) {
                $this.removeClass('basic').addClass(color);
                $table.addClass('l'+level);
            } else {
                $this.removeClass(color).addClass('basic');
                $table.removeClass('l'+level);
            }
        });
    };

    let _initLandingPagePopups = $html => {
        $html.find('.page-info-popup').popup({
            inline: true,
            on: 'click',
            position: 'bottom left'
        });
    };

    let _initKeywordHighlighting = ($html,dataKey) => {
        $html.on('click','[data-'+dataKey+'s]',function(e){
            e.preventDefault();
            let $this = $(this);
            let data = $this.data(dataKey+'s');

            if($this.hasClass('yellow')) {
                $this.removeClass('yellow');

                for(let id in data) {
                    let $entry = $html.find('[data-'+dataKey+'_id="'+id+'"]');
                    $entry.text($entry.attr('data-name'))
                        .removeClass('ui').removeClass('yellow').removeClass('text');
                }

                return false;
            }

            $html.find('[data-'+dataKey+'s].yellow').click();
            $this.addClass('yellow');

            //let keywordLength = $this.parent().find('a').text().length;

            for(let id in data) {
                let $heading = $html.find('[data-'+dataKey+'_id="'+id+'"]');
                // let headingText = $heading.data('name');
                // let index = data[id];
                // let textLength = headingText.length;
                // let html;
                // if(index === 0) {
                //     if(textLength === keywordLength) {
                //         html = '<span class="ui primary text">'+headingText+'</span>';
                //     } else {
                //         html = '<span class="ui primary text">'+headingText.substr(0,keywordLength)+'</span>'
                //             + headingText.substr(keywordLength);
                //     }
                // } else {
                //     html = headingText.substr(0,index)
                //         + '<span class="ui primary text">'+headingText.substr(index,keywordLength)+'</span>'
                //         + headingText.substr(index+keywordLength);
                // }
                //$heading.html(html).parents('td').addClass('yellow');
                $heading.addClass('ui').addClass('yellow').addClass('text');
            }
        });
    };

    let initOsrLandingPages = $html => {
        $('table:not(.irelevant)').DataTable({
            paging: false,
            searching: true,
            info: false
        });

        /* 387: Add posibility to click on the backlinks opener, take the data and then open the backlinks tab */
        $('.open-backlinks').click(function(){

            const type = $(this).attr('data-backlinks');
            const id = $(this).attr('data-id');
            const report_id = $(this).attr('data-report-id');

            $('[data-tab="backlinks"]').removeClass("disabled").click();
            $('#tab_Backlinks').html(
                '<div class="ui borderless center aligned vertical segment tmp"><i class="ui large grey loading spinner icon"></i></div>'
            );
            $('.backlinks-modal').remove();
            loadBacklinks(type, id, report_id);

        });
    };

    let loadBacklinks = (type, id, report_id) => {



        $.ajax({
            type: 'GET',
            url: App.View.Routes.osrFetchBacklinks,
            data: { type : type, id : id, report_id : report_id  },
            success: (response) => {
                if(response.backlinks != ""){
                    $('#tab_Backlinks').html(response.backlinks);

                    $(document).on('click', '#osr_BacklinksTable [data-action="plan"]', function (){
                        let $tr = $(this).parents('tr');
                        if($tr.hasClass('yellow')) return;
                        new OsrPlanBacklinksModalControl("",$tr, report_id);
                    });
                }

            },
            error: (data) => {
                let error = (data.responseJSON && data.responseJSON.error) ? data.responseJSON.error : 'Unknown error.';
                UTIL.Alert.error(error);
            },
            dataType: 'json'
        });

    }

    let initOsrHeadings = $html => {
        _initLandingPagePopups($html);
        _initKeywordHighlighting($html,'heading');
        _initHeadingsFilter($html);
    };

    let initOsrLinks = $html => {
        _initLandingPagePopups($html);
        _initKeywordHighlighting($html,'link');
    };

    let initOsrAlts = $html => {
        _initLandingPagePopups($html);
        _initKeywordHighlighting($html,'alt');
    };

    let initOsrKeywords = $html => {
        $html.find('.count-popup').popup({
            inline: true,
            on: 'click',
            position: 'top right'
        });
    };

    /**
     * Initializes a given report section
     * Lab387: Init section's JS
     *
     * @param {String} section
     * @param {jQuery} $html
     */
    this.initSection = (section,$html) => {
        switch (section) {
            case 'osr.landing_pages':
                initOsrLandingPages($html);
                break;
            case 'osr.headings':
                initOsrHeadings($html);
                break;
            case 'osr.links':
                initOsrLinks($html);
                break;
            case 'osr.alts':
                initOsrAlts($html);
                break;
            case 'osr.keywords':
                initOsrKeywords($html);
                break;
            case 'osr.backlinks':
                new OsrBacklinksControl(reportId);
                break;
            case 'osr.phrases':
                new OsrPhrasesControl(reportId,$html);
                break;
            case 'osr.planner':
                new OsrPlannerControl(reportId,$html);
                break;
            case 'osr.bonus':
                new OsrBonusControl(reportId,$html);
                break;
            case 'osr.content':
                new OsrContentControl(reportId, $html);
                break;
            case 'osr.content_titles':
                //new OsrContentControl(reportId, $html);
                return;
            case 'osr.content_description':
                //new OsrContentControl(reportId, $html);
                return;
            case 'osr.content_posts':
                //new OsrContentControl(reportId, $html);
                return;
            case 'osr.recommendations.content':
                new OsrContentRecommendationsControl(reportId, $html);
                return;
        }

        $html.find('.info-popup').popup({inline: true});
    };

    return this;
};

const SingleReportPageControl = function(params) {
    let reportId = params.reportId;
    let reportStatus = params.status;
    let sectionsControl = new ReportSectionsControl(reportId);

    const CHECK_STATUS_TIMEOUT = 2500;

    let $tabMenus = $('.tabular.menu');
    let $failMessage = $('#failMessage');
    let $projectStages = $('#projectStages');

    let updateReport = () => {
        $.ajax({
            type: 'GET',
            url: App.View.Routes.getProgress,
            data: { reportId },
            success: (response) => {
                $projectStages.replaceWith(response.html);
                $projectStages = $('#projectStages');
                reportStatus = response.status;
                switch (reportStatus) {
                    case ReportStatus.SCHEDULED:
                    case ReportStatus.RUNNING:
                        setTimeout(() => { updateReport() }, CHECK_STATUS_TIMEOUT);
                        break;
                    case ReportStatus.FAILED:
                        $tabMenus.hide();
                        $failMessage.show();
                        $tabMenus.each(function(){
                            $(this).find('.active.item').first().hide();
                        });
                        break;
                    case ReportStatus.COMPLETED:
                        $tabMenus.each(function(){
                            $(this).find('.active.item').first().click();
                        });
                        break;
                }
            },
            error: (data) => {
                let error = (data.responseJSON && data.responseJSON.error) ? data.responseJSON.error : 'Unknown error.';
                UTIL.Alert.error(error);
                setTimeout(() => { updateReport() }, CHECK_STATUS_TIMEOUT);
            },
            dataType: 'json'
        });
    };

    /**
     * Loads a given report section
     *
     * @param  {jQuery} $section
     */
    let loadSection = ($section) => {
        let $loader = $section.find('.tmp');

        if(reportStatus === ReportStatus.COMPLETED) {
            if($section.hasClass('loaded')) return;

            let section = $section.data('section');

            /* 387: This sections doesn't have a loading on click */
            if(section == 'osr.backlinks') return;

            $loader = $loader.clone();
            $section.html('');
            $loader.toggleClass('hide',false).appendTo($section);

            $.ajax({
                type: 'GET',
                url: App.View.Routes.getReportSection,
                data: { reportId, section },
                success: (response) => {
                    if(response.html) {
                        let $html = $(response.html);
                        $loader.toggleClass('hide',true).after($html);
                        if(!$section.hasClass('refreshable')) {
                            $section.toggleClass('loaded',true);
                        }
                        sectionsControl.initSection(section,$html);
                    }
                },
                error: (data) => {
                    let error = (data.responseJSON && data.responseJSON.error) ? data.responseJSON.error : 'Unknown error.';
                    UTIL.Alert.error(error);
                },
                dataType: 'json'
            });
        } else {
            // still loading, leave loader ON
            $loader.toggleClass('hide',false);
        }
    };

    // init tab behavior
    $tabMenus.each(function(){
        $(this).find('.item').on('click', function(){
            let $section = $('.tab[data-tab="'+$(this).data('tab')+'"]');
            loadSection($section);
        }).tab();
    });

    // toggle fullscreen
    $('.toggle-full-screen-report').on('click', () => {
        $('body').toggleClass('full-screen-report');
    });

    $('.tab-info-popup').popup({inline: true,position: 'top left'});
    // $('.tab-info-popup').popup({
    //     inline: true,
    //     on: 'hover',
    //     position: 'bottom'
    // });

    // show first tab by default and update report
    $tabMenus.each(function() {
        $(this).find('.item').first().click();
    });
    if(reportStatus !== ReportStatus.COMPLETED && reportStatus !== ReportStatus.FAILED) updateReport();

    let $deleteModal = $('#modal_DeleteReport');

    if(App.View.Options.deleteReport) {
        // delete button
        $('#btn_toggleDeleteReport').on('click',function(e){
            e.preventDefault();
            $deleteModal.modal('show');
            return false;
        });

        // delete behavior
        $deleteModal.find('.primary.button').on('click', function (){
            $deleteModal.find('.button').toggleClass('disabled',true);
            $.ajax({
                type: 'DELETE',
                url: App.View.Routes.deleteReport,
                data: {reportId: $deleteModal.data('report-id')},
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
