@extends('layouts.app')

@section('title', 'Aufgabe')

@section('content')
    <div class="centered-logo">
        <object class="logoobject" data="/img/svg/logo.svg" type="image/svg+xml"></object>
    </div>

    <div class="container landing-container">
        <hr/>
        <div class="col-xs-12 sc-panel scratch-panel">
            <div class="row">
                <div class="col-xs-12 content-top-border-12"></div>
            </div>
            <div class="row">
                <div class="col-xs-12 content">
                    <div class="row">
                        <ol class="breadcrumb" style="width: 80%; margin-left: 10%;">
                            <li><a href="/">Start</a></li>
                            <li><a href="/login">Login</a></li>
                            <li><a href="/categories">Kategorien</a></li>
                            <li><a href="/category/{{ $category->id }}">{{ $category->name }}</a></li>
                            <li><a href="/level/{{ $level->id }}">{{ $level->title }}</a></li>
                            <li><a href="/task/{{ $task->id }}">{{ $task->difficultyName() }}</a></li>
                        </ol>
                    </div>
                    @if($task->pdf_url)
                    <div class="row" style="text-align: right; padding-right: 40px;">
                        <a href="{{ $task->pdf_url }}" target="_blank" class="btn btn-primary btn-lg hvr-pulse-grow" style="margin-bottom: 15px;"><i class="fa fa-file-pdf-o"></i> </a>
                    </div>
                    @endif
                    <div class="row">
                        <div class="@if($task->youtube_url) col-xs-6 @else col-xs-12 @endif">
                            <p style="text-align: left; margin-left: 25px; font-size: 2rem; letter-spacing: 0.25rem; line-height: 3rem;">{!! $task->content !!}</p>
                        </div>
                        @if($task->youtube_url)
                        <div class="col-xs-6">
                            <!-- https://www.youtube.com/embed/8JG4aeVreCQ?rel=0&amp;showinfo=0 -->
                            <div style="margin-right: 25px;">
                                <iframe id="ytplayer" type="text/html" width="100%" height="390"
                                        style="background-color: white;"
                                        src="{{ $task->youtube_url }}"
                                        frameborder="0"
                                        scrolling="no"></iframe>
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="row">
                        @if($task->finished())
                            <h3 class="animated infinite pulse">Du hast diese Aufgabe richtig gelöst, weiter so! :)</h3>
                        @else
                            @if($errors->has('wrong_answer'))
                                <div class="label label-warning" style="font-size: 100%">{{ $errors->first('wrong_answer') }}</div>
                            @endif

                            <form method="POST" action="/task/{{ $task->id }}/finish">
                                {{ csrf_field() }}

                                @if($task->finish_type === App\Domain\Tasks\FinishType::MULTIPLE_CHOICE)
                                    @foreach($task->answers as $answer)
                                        <div class="radio">
                                            <label>
                                                <input type="radio" name="answer_id" id="answer_{{ $answer->id }}" value="{{ $answer->id }}">
                                                {{ $answer->label }}
                                            </label>
                                        </div>
                                    @endforeach
                                @endif

                                @if($task->teacherRequested())
                                    <h3 class="animated rubberBand">Ein Lehrer sollte gleich bei dir sein.</h3>
                                @else
                                    <button type="submit" class="btn btn-primary btn-lg hvr-pulse-grow" style="margin-top: 15px;">Aufgabe lösen</button>
                                @endif
                            </form>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 content-bottom-border-12"></div>
            </div>
        </div>
    </div>
@endsection