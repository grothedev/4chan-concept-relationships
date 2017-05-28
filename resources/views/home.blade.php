<!doctype html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>4chan word analysis</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
        <link href="{{{ asset('/css/select2.min.css') }}}" rel="stylesheet" />
        
    </head>
    <body>
        <!-- input board or thread number, scope of analysis (thread, board, or site) -->
        <form class = "form-horizontal" role = "form" method = "GET" action = "{{ action('HomeController@getData') }}">
            <div class = "form-group{{ $errors->has('board') ? 'has-error' : '' }}">
                <label for = "board" class = "col-md-2 control-label">Board (* for entire site)</label>
                <input type = "text" id = "board" class = "form-control" name = "board" value = "{{ old('board') }}" required autofocus></input>

                @if ($errors->has('board'))
                    <span class = "help-block">
                        <strong>{{ $errors->first('board') }}</strong>
                    </span>
                @endif

            </div>

            <div class = "form-group{{ $errors->has('thread') ? 'has-error' : '' }}">
                <label for = "thread" class = "col-md-2 control-label">Thread #</label>
                <input type = "text" id = "thread" class = "form-control" name = "thread" value = "{{ old('thread') }}" autofocus></input>

                @if ($errors->has('thread'))
                    <span class = "help-block">
                        <strong>{{ $errors->first('thread') }}</strong>
                    </span>
                @endif

            </div>

            <div class="form-group">
                <div class="col-md-8 col-md-offset-2">
                    <button type="submit">
                        Submit
                    </button>
                </div>
            </div>

        </form>
    </body>
</html>
