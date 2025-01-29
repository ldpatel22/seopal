<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Findings</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.4.1/semantic.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.4.1/semantic.min.js"></script>
    <style>
        #findings-content {
            display: none;
        }
    </style>
</head>
<body>



<div>

    <div class="ui segment inverted full-screen-report-hidden">
        <div class="ui grid">
            <div class="eight wide column">
                <h1 class="ui header" id="findings-header" style="color: white; cursor: pointer;">
                    Findings
                </h1>
            </div>
            <div class="eight wide column" style="text-align: right;">
                <button id="openHereButton" class="ui labeled icon basic inverted button">
                    <i class="folder open outline icon"></i>
                    Open Here
                </button>
                <button class="ui labeled icon basic inverted button toggle-full-screen-report">
                    <i class="window maximize outline icon"></i>
                    Full Screen
                </button>
            </div>
        </div>
    </div>
    <div id="findings-content">
        <div id="tabNavigation" class="ui top attached tabular small menu">
            <a data-tab="landingpages" class="active item">
                <i class="globe americas icon tab-info-popup"></i>
                <div class="ui special w250 popup" style="text-align: left">
                    <div class="header">Landing Pages (URLs)</div>
                    <hr/>
                    <div>
                        <p>
                            Landing pages that appear as results on the first page of Google Search on @include('shared._device',['device' => $report->device, 'iconOnly' => false]) devices in <i class="{{ $report->locale }} flag"></i> for the search term <strong>{{ $report->keyword->name }}</strong>.
                        </p>
                        <p>
                            <span class="ui grey text"><strong>Hint</strong>: you can sort landing pages based on different criteria (e.g. authority score or number of page/domain backlinks).</span>
                        </p>
                    </div>
                </div>
                <div class="mobile hidden">URLs</div>
            </a>
            <a data-tab="headings" class="item">
                <i class="grip lines icon tab-info-popup"></i>
                <div class="ui special w250 popup" style="text-align: left">
                    <div class="header">Headings</div>
                    <hr/>
                    <div>
                        <p>
                            All headings from @include('reports.osr._heading_icon',['headingLevel' => 1]) to @include('reports.osr._heading_icon',['headingLevel' => 6]) found within the landing pages.
                        </p>
                        <p>
                            <span class="ui grey text"><strong>Hint</strong>: headings up to @include('reports.osr._heading_icon',['headingLevel' => 3]) are shown by default. You can opt-in to see more heading levels by turning them on in the top right corner of the Headings report.</span>
                        </p>
                    </div>
                </div>
                <div class="mobile hidden">
                    Headings
                </div>
            </a>
            <a data-tab="links" class="item">
                <i class="linkify icon tab-info-popup"></i>
                <div class="ui special w250 popup" style="text-align: left">
                    <div class="header">Links</div>
                    <hr/>
                    <div>
                        <p>
                            All <strong>external links</strong> with their textual component found within the landing pages.
                        </p>
                        <p>
                            <span class="ui grey text"><strong>Hint</strong>: click on the arrow icon next link text to open the link in a new browser tab.</span>
                        </p>
                    </div>
                </div>
                <div class="mobile hidden">
                    Links
                </div>
            </a>
            <a data-tab="alts" class="item">
                <i class="image icon tab-info-popup"></i>
                <div class="ui special w250 popup" style="text-align: left">
                    <div class="header">Images (Alt Tags)</div>
                    <hr/>
                    <div>
                        <p>
                            All images with their alt tag found within the landing pages.
                        </p>
                        <p>
                            <span class="ui grey text"><strong>Hint</strong>: click on the image icon next to its alt tag to open the image in a new browser tab.</span>
                        </p>
                    </div>
                </div>
                <div class="mobile hidden">
                    Images
                </div>
            </a>


            {{--    <div data-tab="keywords" class="disabled item">--}}
            {{--        <i class="quote right icon"></i>--}}
            {{--        <span class="mobile hidden">Keywords</span>--}}
            {{--    </div>--}}


            <a data-tab="backlinks" class="item">
                <i class="external alternate icon tab-info-popup"></i>
                <div class="ui special w250 popup" style="text-align: left">
                    <div class="header">Backlinks</div>
                    <hr/>
                    <div>
                        <p>
                            Overview of first five, important backlinks per URL/landing page you've got inside URLs tab.
                        </p>
                        <p>
                            <span class="ui grey text"><strong>Hint</strong>: Click to this tab to get the backlinks per URL/landing page you've fot inside URLs tab.</span>
                        </p>
                    </div>
                </div>
                <div class="mobile hidden">
                    Backlinks
                </div>
            </a>
            <a data-tab="phrases" class="item">
                <i class="quote right icon tab-info-popup"></i>
                <div class="ui special w250 popup" style="text-align: left">
                    <div class="header">Most Used Keywords</div>
                    <hr/>
                    <div>
                        <p>
                            A list of most used keywords found in top ranking landing pages that generate traffic.
                        </p>
                        <p>
                            <span class="ui grey text"><strong>Hint</strong>: add a keyword to your Planner to see the statistics.</span>
                        </p>
                    </div>
                </div>
                <div class="mobile hidden">
                    Keywords
                </div>
            </a>
            <a data-tab="planner" class="right item">
                <i class="yellow lightbulb icon tab-info-popup"></i>
                <div class="ui special w250 popup" style="text-align: left">
                    <div class="header">Keyword Planner</div>
                    <hr/>
                    <div>
                        <p>
                            Shows phrases you've added from the "Phrases" tab with their statistics.
                            Shows backlinks you've added from the "Backlinks" tab.
                        </p>
                        <p>
                            <span class="ui grey text"><strong>Hint</strong>: select one or more phrases to and add them to your project keywords list. Select one or more backlinks to add them to your project backlinks list.</span>
                        </p>
                    </div>
                </div>
                <div class="mobile hidden">
                    Planner
                </div>
            </a>

            {{--        <a data-tab="bonus" class="item">--}}
            {{--            <i class="balance scale icon tab-info-popup"></i>--}}
            {{--            <div class="ui special w250 popup" style="text-align: left">--}}
            {{--                <div class="header">BONUS</div>--}}
            {{--                <hr/>--}}
            {{--                <div>--}}
            {{--                    <p>--}}
            {{--                        Use this tab to find some bonus information related to your keyword search.--}}
            {{--                    </p>--}}
            {{--                    <p>--}}
            {{--                        <span class="ui grey text"><strong>Hint</strong>: You can use the BONUS content for better SEO of your website for certain keywords.</span>--}}
            {{--                    </p>--}}
            {{--                </div>--}}
            {{--            </div>--}}
            {{--            <div class="mobile hidden">--}}
            {{--                BONUS--}}
            {{--            </div>--}}
            {{--        </a>--}}
        </div>

        <div id="tab_LandingPages" data-tab="landingpages" data-section="osr.landing_pages" class="ui bottom attached active tab segment">
            <div class="ui borderless center aligned vertical segment tmp">
                <i class="ui large grey loading spinner icon"></i>
            </div>
        </div>

        <div id="tab_Headings" data-tab="headings" data-section="osr.headings" class="ui bottom attached tab segment">
            <div class="ui borderless center aligned vertical segment tmp">
                <i class="ui large grey loading spinner icon"></i>
            </div>
        </div>

        <div id="tab_Links" data-tab="links" data-section="osr.links" class="ui bottom attached tab segment">
            <div class="ui borderless center aligned vertical segment tmp">
                <i class="ui large grey loading spinner icon"></i>
            </div>
        </div>

        <div id="tab_Alts" data-tab="alts" data-section="osr.alts" class="ui bottom attached tab segment">
            <div class="ui borderless center aligned vertical segment tmp">
                <i class="ui large grey loading spinner icon"></i>
            </div>
        </div>

        {{--<div id="tab_Keywords" data-tab="keywords" data-section="osr.keywords" class="ui bottom attached tab segment">--}}
        {{--    <div class="ui borderless center aligned vertical segment tmp">--}}
        {{--        <i class="ui large grey loading spinner icon"></i>--}}
        {{--    </div>--}}
        {{--</div>--}}

        <div id="tab_Phrases" data-tab="phrases" data-section="osr.phrases" class="ui bottom attached tab segment">
            <div class="ui borderless center aligned vertical segment tmp">
                <i class="ui large grey loading spinner icon"></i>
            </div>
        </div>

        <div id="tab_Backlinks" data-tab="backlinks" data-section="osr.backlinks" class="ui bottom attached tab segment">
            <div class="ui borderless center aligned vertical segment tmp">
                <p>To get access to backlinks, go back to URLs tab and click on the backlinks column for choose domain or landing page</p>
            </div>
        </div>

        <div id="tab_Planner" data-tab="planner" data-section="osr.planner" class="ui bottom attached tab refreshable segment">
            <div class="ui borderless center aligned vertical segment tmp">
                <i class="ui large grey loading spinner icon"></i>
            </div>
        </div>



        <div id="tab_Bonus" data-tab="bonus" data-section="osr.bonus" class="ui bottom attached tab segment">
            <div class="ui borderless center aligned vertical segment tmp">
                <i class="ui large grey loading spinner icon"></i>
            </div>
        </div>

    </div>
</div>

<script>
    document.getElementById('findings-header').addEventListener('click', function() {
        var content = document.getElementById('findings-content');
        if (content.style.display === 'none' || content.style.display === '') {
            content.style.display = 'block';
        } else {
            content.style.display = 'none';
        }
    });

    document.getElementById('openHereButton').addEventListener('click', function() {
        var content = document.getElementById('findings-content');
        content.style.display = 'block';
        //document.querySelector('#tabFindings .item[data-tab="findingsContent"]').click();
       const findingsTab = document.querySelector('#tabFindings .item[data-tab="findingsContent"]');
    	if (findingsTab) findingsTab.click();
    });

    // Initialize tabs and popups
    $('.menu .item').tab();
    $('.tab-info-popup').popup({
        inline: true,
        hoverable: true,
        position: 'top center',
        delay: {
            show: 300,
            hide: 800
        }
    });
</script>