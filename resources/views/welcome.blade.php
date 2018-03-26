<?php 
    if(Auth::check()) $user = Auth::user();
    else $user = array('name' => '', 'role' => '', 'id' => 0);
?>
<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        window.userID = <?=$user['id']?>;
        window.userName = '<?=$user['name']?>';
        window.userRole = '<?=$user['role']?>';
        window.userKey = '{{ $key }}';
        window.baseurl = "<?=getenv('APP_URL');?>";
    </script>

    <link rel="stylesheet" href="{{ URL::asset('public/css/app.css') }}?ver=1.3.1">

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Центр Мониторинга в Образовании | Анкетирование и тестирование</title>
    
</head>
<body>
    <div id="app">
        <router-view></router-view>
    </div>
    <script src="{{ url('public/js/app.js') }}?ver=1.3.1"></script>
    <!-- Yandex.Metrika counter --> <script type="text/javascript" > (function (d, w, c) { (w[c] = w[c] || []).push(function() { try { w.yaCounter45829977 = new Ya.Metrika({ id:45829977, clickmap:true, trackLinks:true, accurateTrackBounce:true, webvisor:true, trackHash:true }); } catch(e) { } }); var n = d.getElementsByTagName("script")[0], s = d.createElement("script"), f = function () { n.parentNode.insertBefore(s, n); }; s.type = "text/javascript"; s.async = true; s.src = "https://mc.yandex.ru/metrika/watch.js"; if (w.opera == "[object Opera]") { d.addEventListener("DOMContentLoaded", f, false); } else { f(); } })(document, window, "yandex_metrika_callbacks"); </script> <noscript><div><img src="https://mc.yandex.ru/watch/45829977" style="position:absolute; left:-9999px;" alt="" /></div></noscript> <!-- /Yandex.Metrika counter -->
</body>
</html>