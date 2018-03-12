<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="{{ URL::asset('public/css/app.css') }}">

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Центр Мониторинга в Образовании | Анкетирование и тестирование</title>
    
</head>
<body>
    <div class="alert alert-danger alert-browser">
        <p>К сожалению Вы используете браузер, который не поддерживается Единой Платформой Анкетирования. Для того, что бы продолжить работу, необходимо установить один из следующих современных браузеров:</p>
        <ul>
            <li>Google Chrome версии 50.0.0 и выше (<a href="https://www.google.ru/chrome/browser/desktop/" target="_blank" title="Скачать Google Chrome">скачать</a>)</li>
            <li>Mozilla Firefox версии 45.0.0 и выше (<a href="https://www.mozilla.org/ru/firefox/new/" target="_blank" title="Скачать Mozilla Firefox">скачать</a>)</li>
            <li>Opera версии 37.0.0 и выше (<a href="http://www.opera.com/ru" target="_blank" title="Скачать Opera">скачать</a>)</li>
            <li>Safari для Windows версии 5.1.7</li>
            <li>Safari для Mac OS версии 9 и выше</li>
        </ul>
    </div>
</body>
</html>