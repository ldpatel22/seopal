@extends('layouts.auth')

@section('title')
    Summary | {{ $project->name }}
@endsection

@section('content')

    {{-- Summary--}}
    <div class="ui segment inverted title {{ $options['editProject'] ? 'with-attachment' : '' }}">
        <h1 class="ui header">
            Project Summary
            @if($options['editProject'])
                <button id="btn_toggleEditProject" class="ui  icon basic inverted button right floated">
                    <i class="edit icon"></i>
                    <span>Edit Project</span>
                </button>
            @endif
            @if($options['deleteProject'])
                <button id="btn_toggleDeleteProject" class="ui icon basic inverted button right floated">
                    <i class="trash icon"></i>
                    <span>Delete Project</span>
                </button>
            @endif
        </h1>
        @if($options['editProject'])
            <form id="form_EditProject" class="ui attached form fluid segment">
                <input type="hidden" name="projectId" value="{{ $project->id }}" />
                <div class="field">
                    <div class="two fields">
                        {{-- Name --}}
                        <div class="field">
                            <label>Project Name</label>
                            <input type="text" name="name" placeholder="Name your project" value="{{ $project->name }}">
                        </div>

                        {{-- Locale --}}
                        <div class="field">
                            <label>Country</label>
                            <div class="ui fluid search selection dropdown">
                                <input type="hidden" name="locale" value="{{ $project->locale }}">
                                <i class="dropdown icon"></i>
                                <div class="default text">Select primary country</div>
                                <div class="menu">
                                    @foreach(config('locales') as $key => $value)
                                        <div class="item" data-value="{{ $key }}"><i class="{{ $key }} flag"></i>{{ $value }}</div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <button class="ui primary submit button">Update Project</button>
                <button class="ui cancel button">Cancel</button>
            </form>
        @endif
    </div>
    @unless($keywords->count())
        <div class="ui info message">
            <div class="header">
                Add Keywords
            </div>
            <p>
                Your project doesn't have any keywords at the moment.
                <a class="ui mini primary button" href="{{ $project->permalink() }}/?addKeywords=yes">Add Keywords</a>
            </p>
        </div>
    @endunless
    <div class="ui borderless segment">
        <div class="ui five column stackable grid">
            <div class="column">
                <div>
                    <strong>Name</strong>
                </div>
                <p>{{ $project->name }}</p>
            </div>
            <div class="column">
                <div>
                    <strong>Country</strong>
                </div>
                <p>
                    @include('shared._locale',['locale' => $project->locale, 'flagOnly' => false])
                </p>
            </div>
            <div class="column">
                <div>
                    <strong>Owner</strong>
                </div>
                <p>
                    <img class="ui avatar image" src="{{ $project->user->getAvatar() }}" style="width: 20px; height: 20px;" />
                    {{ $project->user->name }}
                </p>
            </div>
            <div class="column">
                <div>
                    <strong>Keywords</strong>
{{--                    <a href="{{ $project->permalink() }}/?addKeywords=yes" class="ui mini icon basic borderless button" title="Add Keywords">--}}
{{--                        <i class="plus icon"></i> Add--}}
{{--                    </a>--}}
                </div>
                <p>
                    <a href="{{ route('keywords.all') }}">{{ $keywords->count() }}</a>
                </p>
            </div>
            <div class="column">
                <div>
                    <strong>Last Report</strong>
{{--                    <a href="{{ route('reports.all') }}/?newReport=yes" class="ui mini icon basic borderless button" title="New Report">--}}
{{--                        <i class="chart line icon"></i> Run--}}
{{--                    </a>--}}
                </div>
                <p>
                    @php $lastReport = $project->getLastReport(); @endphp
                    @if($lastReport)
                        <a href="{{ $lastReport->permalink() }}">{{ $lastReport->created_at->diffForHumans() }}</a>
                    @else
                        <span class="ui red text">never</span>
                    @endif
                </p>
            </div>
        </div>
    </div>

    {{-- Keywords --}}
    @include('keywords._keywords')

    {{-- External Users --}}
    @if($options['viewExternalUsers'])
        <div class="ui segment inverted title with-attachment">
            <h1 class="ui header">
                External Users
                <button id="btn_toggleEditProject" class="ui icon basic inverted button right floated disabled">
                    <i class="user icon"></i>
                    <span>Invite User</span>
                </button>
            </h1>
        </div>
        <div class="ui segment">
            <div class="ui relaxed divided list">
                This option is currently not available.
            </div>
        </div>
    @endif

    {{-- Delete Modal --}}
    @if($options['deleteProject'])
        <div id="modal_DeleteProject" class="ui tiny test modal front transition hidden" data-project-id="{{ $project->id }}">
            <div class="header">
                Delete Project?
            </div>
            <div class="content">
                <p>By deleting this project you will lose access to all reports.</p>
                <p>Please confirm you wish to delete this project.</p>
            </div>
            <div class="actions">
                <div class="ui cancel button">Cancel</div>
                <div class="ui primary button">Confirm Deletion</div>
            </div>
        </div>
    @endif

@endsection

@section('body-end')

    {!! inject_js([
        'app/ToggleControl',
        'view/SingleProjectPageControl',
        'view/KeywordsDataFormatter',
        'view/KeywordsDataTableControl',
        'view/RelatedKeywordsDataTableControl',
        'view/NewKeywordControl',
    ]) !!}

    <script>

        $(() => {
            new SingleProjectPageControl();
            new KeywordsDataTableControl();
            new RelatedKeywordsDataTableControl();
            new NewKeywordControl();
        });

    </script>

@endsection
