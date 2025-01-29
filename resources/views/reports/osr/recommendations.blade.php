<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Recommendations</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.4.1/semantic.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.4.1/semantic.min.js"></script>
    <style>
        #recommendations-content {
            display: none;
        }
    </style>
</head>
<body>

<div>
    <div class="ui segment inverted full-screen-report-hidden">
        <div class="ui grid">
            <div class="eight wide column">
                <h1 class="ui header" id="recommendations-header" style="color: white; cursor: pointer;">
                    Recommendations
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
    <div id="recommendations-content">
    <div id="tabRecommendations" class="ui top attached tabular small menu">
        <a data-tab="recommendedContent" class="item">
            <i class="file americas icon tab-info-popup"></i>
            <div class="mobile hidden">Content</div>
        </a>
        <a data-tab="recommendedSemantic" class="item">
            <i class="font americas icon tab-info-popup"></i>
            <div class="mobile hidden">Semantic</div>
        </a>
    </div>
    <div id="tab_RecommendedStrategy" data-tab="recommendedStrategy" data-section="osr.recommendations.strategy" class="ui bottom attached tab segment">
        <div class="ui borderless center aligned vertical segment tmp">
            <i class="ui large grey loading spinner icon"></i>
        </div>
    </div>
    <div id="tab_RecommendedContent" data-tab="recommendedContent" data-section="osr.recommendations.content" class="ui bottom attached tab segment">
        <div class="ui borderless center aligned vertical segment tmp">
            <i class="ui large grey loading spinner icon"></i>
        </div>
    </div>
    <div id="tab_RecommendedSemantic" data-tab="recommendedSemantic" data-section="osr.recommendations.semantic" class="ui bottom attached tab segment">
        <div class="ui borderless center aligned vertical segment tmp">
            <i class="ui large grey loading spinner icon"></i>
        </div>
    </div>
    <div id="tab_RecommendedBacklinks" data-tab="recommendedBacklinks" data-section="osr.recommendations.backlinks" class="ui bottom attached tab segment">
        <div class="ui borderless center aligned vertical segment tmp">
            <i class="ui large grey loading spinner icon"></i>
        </div>
    </div>
    </div>
</div>
<script>
    document.getElementById('recommendations-header').addEventListener('click', function() {
        var content = document.getElementById('recommendations-content');
        if (content.style.display === 'none' || content.style.display === '') {
            content.style.display = 'block';
        } else {
            content.style.display = 'none';
        }
    });

    document.getElementById('openHereButton').addEventListener('click', function() {
        var content = document.getElementById('recommendations-content');
        content.style.display = 'block';
        document.querySelector('#tabRecommendations .item[data-tab="recommendedContent"]').click();
    });

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


