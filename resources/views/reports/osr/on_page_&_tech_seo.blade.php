<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>On Page & Tech SEO</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.4.1/semantic.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.4.1/semantic.min.js"></script>
    <style>
        #description {
            margin: 20px;
            padding: 20px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<div>
    <div class="ui segment inverted full-screen-report-hidden">
        <div class="ui grid">
            <div class="eight wide column">
                <h1 class="ui header" id="on-page-header" style="color: white; cursor: pointer;">
                    On Page & Tech SEO
                </h1>
            </div>
            <div class="eight wide column" style="text-align: right;">
                <button class="ui labeled icon basic inverted button">
                    <i class="folder open outline icon"></i>
                    Open Here
                </button>
                <button class="ui labeled icon basic inverted button toggle-full-screen-report">
                    <i class="window maximize outline icon"></i>
                    Fullscreen
                </button>
            </div>
        </div>
    </div>

    <div id="on-page-content" style="display: none;">
        <!-- Description Box -->
        <div id="description">
            <h2>How can it help you?</h2>
<p>This page offers AI-powered, SEO-optimized content for the keyword 'izrada web stranica.' It includes five suggested landing page titles, tailored descriptions, and content recommendations inspired by the top-ranking websites. Clicking the header will toggle the visibility of this content, allowing you to view detailed SEO insights and suggestions for improving your webpage's visibility and relevance.</p>       </div>

        <!-- Tab Navigation Menu with Only Content Tab -->
        <div id="tabNavigation" class="ui top attached tabular small menu">
            <a data-tab="content" class="active item">
                <i class="file alternate icon tab-info-popup"></i>
                <div class="mobile hidden">Content</div>
            </a>
        </div>

        <div id="tab_Content" data-tab="content" data-section="osr.content" class="ui bottom attached active tab segment">
            <div class="ui borderless center aligned vertical segment tmp">
                <i class="ui large grey loading spinner icon"></i>
                <p>Content will load here based on your selections.</p>
            </div>
        </div>
    </div>
</div>

<!-- Inline JavaScript to Toggle On Page & Tech SEO Content -->
<script>
    document.getElementById('on-page-header').addEventListener('click', function() {
        var content = document.getElementById('on-page-content');
        if (content.style.display === 'none' || content.style.display === '') {
            content.style.display = 'block';
        } else {
            content.style.display = 'none';
        }
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

</body>
</html>
