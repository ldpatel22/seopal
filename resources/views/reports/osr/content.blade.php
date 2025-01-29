{{-- <div class='multi-content'>    
    <div class='left'>
        <div class="ui link inverted vertical pointing tabular menu">
            <span data-tab="contentTitles" class="active item">Titles</span>
            <span data-tab="contentDescription" class="item">Description</span>
            <span data-tab="contentPosts" class="item">Content</span>
        </div>
    </div>
    <div class='right'>
        <div id="tab_contentTitles#" data-tab="contentTitles" data-section="osr.content_titles" class="ui bottom active loaded tab segment">
            @include('reports.osr.content_titles')            
        </div>
        <div id="tab_contentDescription" data-tab="contentDescription" data-section="osr.content_description" class="ui bottom tab segment">
            <div class="ui borderless center aligned vertical segment tmp">
                <i class="ui large grey loading spinner icon"></i>
            </div>
        </div>
        <div id="tab_contentPosts" data-tab="contentPosts" data-section="osr.content_posts" class="ui bottom tab segment">
            <div class="ui borderless center aligned vertical segment tmp">
                <i class="ui large grey loading spinner icon"></i>
            </div>
        </div>
    </div>

</div> --}}


<div class="ui link three item tabular menu">
    <span data-tab="contentTitles" class="active item">Titles</span>
    <span data-tab="contentDescription" class="item">Descriptions</span>
    <span data-tab="contentPosts" class="item">Content</span>
</div>

<div id="tab_contentTitles#" data-tab="contentTitles" data-section="osr.content_titles" class="ui bottom active loaded tab">
    @include('reports.osr.content_titles')
</div>
<div id="tab_contentDescription" data-tab="contentDescription" data-section="osr.content_description" class="ui bottom tab">
    <div class="ui borderless center aligned vertical segment tmp">
        <i class="ui large grey loading spinner icon"></i>
    </div>
</div>
<div id="tab_contentPosts" data-tab="contentPosts" data-section="osr.content_posts" class="ui bottom tab">
    <div class="ui borderless center aligned vertical segment tmp">
        <i class="ui large grey loading spinner icon"></i>
    </div>
</div>