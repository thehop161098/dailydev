<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Funny video</title>
    <link rel="stylesheet" href="/css/main.css">
    <link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <script src="http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script src="http://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.2/js/toastr.min.js"></script>
</head>

<body>
    @if (session('success'))
        <script>
            toastr.success('{{ session('success') }}', '', {timeOut: 5000});
        </script>
    @endif
    @if (session('errors'))
        <script>
            toastr.error('{!! implode('<br>', $errors->all()) !!}', '', {timeOut: 5000});
        </script>
    @endif
    <header>
        <div class="wp-header">
            <div class="logo">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
                    <!--! Font Awesome Pro 6.3.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                    <path
                        d="M575.8 255.5c0 18-15 32.1-32 32.1h-32l.7 160.2c0 2.7-.2 5.4-.5 8.1V472c0 22.1-17.9 40-40 40H456c-1.1 0-2.2 0-3.3-.1c-1.4 .1-2.8 .1-4.2 .1H416 392c-22.1 0-40-17.9-40-40V448 384c0-17.7-14.3-32-32-32H256c-17.7 0-32 14.3-32 32v64 24c0 22.1-17.9 40-40 40H160 128.1c-1.5 0-3-.1-4.5-.2c-1.2 .1-2.4 .2-3.6 .2H104c-22.1 0-40-17.9-40-40V360c0-.9 0-1.9 .1-2.8V287.6H32c-18 0-32-14-32-32.1c0-9 3-17 10-24L266.4 8c7-7 15-8 22-8s15 2 21 7L564.8 231.5c8 7 12 15 11 24z" />
                    </svg>
                <div class="logo-text">
                    <a href="/">Funny Movies</a>
                </div>
            </div>
            @guest
                <div class="login">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="email" required autofocus>
                        <input type="password" name="password" required placeholder="password">
                        <button type="submit">Login</button>
                    </form>
                </div>
            @endguest
            @auth
                <div class="user-login">
                    <p>Welcome: {{ auth()->user()->email }}</p>
                    <a href="/share-movie">Share a movie</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit">Logout</button>
                    </form>
                </div>
            @endauth
        </div>
    </header>
    <section class="main">
        <div class="wp-content">
            @foreach ($youtubes as $youtube)
                <div class="video-card">
                    <div class="video-clip">
                        <iframe width="300" height="200" src="https://www.youtube.com/embed/{{ $youtube->param_key }}">
                        </iframe>
                    </div>
                    <div class="video-information">
                        <h3 class="title">{{ $youtube->title }}</h3>
                        <div class="date">{{ \Carbon\Carbon::parse($youtube->create_date)->format('Y/m/d') }}</div>
                        <div class="des">{!! $youtube->description !!}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
    <footer>
        @coppyright
    </footer>
</body>

</html>
