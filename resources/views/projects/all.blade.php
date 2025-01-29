@extends('layouts.auth')

@section('title')
    {{ __('projects.pageTitle') }}
@endsection

@section('content')

    {{-- My Projects --}}
    <div class="ui segment inverted title with-attachment">
        <h1 class="ui header">
            {{ __('projects.ownedProjects') }}
            <button id="btn_toggleAddProject" class="ui icon basic inverted button right floated">
                <i class="plus icon"></i>
                {{ __('projects.addProject') }}
            </button>
        </h1>
        <form id="form_NewProject" class="ui attached form fluid segment">
            <div class="field">
                <div class="two fields">
                    {{-- Name --}}
                    <div class="field">
                        <label>Project Name</label>
                        <input type="text" name="name" placeholder="Name your project">
                    </div>

                    {{-- Locale --}}
                    <div class="field">
                        <label>Primary Locale</label>
                        <div class="ui fluid search selection dropdown">
                            <input type="hidden" name="locale" value="">
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
            <button class="ui primary submit button">Add Project</button>
            <button class="ui cancel button">Cancel</button>
        </form>
    </div>
    <div class="ui segment">
        <div class="ui relaxed divided list">
            @if($ownedProjects->isEmpty())
                {{ __('projects.noOwnedProjects') }}
            @else
                @foreach($ownedProjects as $project)
                    <div class="item">
                        <i class="large folder middle aligned icon"></i>
                        <div class="content">
                            <a class="header" href="{{ $project->permalink() }}">{{ $project->name }}</a>
                            <div class="description">
                                <i class="{{ $project->locale }} flag"></i>
                                Last report:
                                @php $lastReport = $project->getLastReport(); @endphp
                                @if($lastReport)
                                    <a href="{{ $lastReport->permalink() }}">{{ $lastReport->created_at->diffForHumans() }}</a>
                                @else
                                    <span class="ui red text">never</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    {{-- External Projects --}}
    <div class="ui segment inverted title">
        <h1 class="ui header">
            {{ __('projects.externalProjects') }}
        </h1>
    </div>
    <div class="ui segment">
        <div class="ui relaxed divided list">
            @if($externalProjects->isEmpty())
                {{ __('projects.noExternalProjects') }}
            @else
                @foreach($externalProjects as $project)
                    <div class="item">
                        <i class="large folder middle aligned icon"></i>
                        <div class="content">
                            <a class="header" href="{{ $project->permalink() }}">{{ $project->name }}</a>
                            <div class="description">
                                <i class="{{ $project->locale }} flag"></i>
                                Last report: 10h ago
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

@endsection

@section('body-end')

    {!! inject_js([
        'app/ToggleControl',
        'view/ProjectsPageControl'
    ]) !!}

    <script>

        $(() => { new ProjectsPageControl(); });

    </script>

@endsection
