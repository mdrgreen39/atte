<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atte</title>
    <link rel="stylesheet" href="https://unpkg.com/ress/dist/ress.min.css" />
    <link rel="stylesheet" href="{{ asset('css/common.css')}}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@100..900&display=swap" rel="stylesheet">

    @yield('css')
</head>

<body>
    <header class="header">
        <h1 class="header__logo">Atte</h1>
        @yield('nav')
    </header>
    <div class="content">
        @yield('content')
    </div>
    <footer class="footer">
        <p class="footer__logo">
            Atte,inc.
        </p>
    </footer>
</body>

</html>