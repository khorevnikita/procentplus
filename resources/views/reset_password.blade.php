<html>
<head>
    <meta content="text/html; charset=UTF-8;" http-equiv="Content-Type">
    <title>Процент+ Изменение пароля</title>
    <meta name="csrf-param" content="authenticity_token">
    <meta name="csrf-token" content="@csrf">

    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <link rel="stylesheet" href="/css/index.css"/>
    <style type="text/css">/* Chart.js */
        @-webkit-keyframes chartjs-render-animation {
            from {
                opacity: 0.99
            }
            to {
                opacity: 1
            }
        }

        @keyframes chartjs-render-animation {
            from {
                opacity: 0.99
            }
            to {
                opacity: 1
            }
        }

        .chartjs-render-monitor {
            -webkit-animation: chartjs-render-animation 0.001s;
            animation: chartjs-render-animation 0.001s;
        }</style>
</head>
<body>
<div class="wrapper">
    <div class="l-main" role="main">
        <div class="l-content">
            <div class="container">
                <div class="l-box l-box--center">
                    <div class="l-box__header l-box__header--margin">
                        <img class="logo__img" src="/images/logo.jpg">
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="l-box l-box--center">
                    <div class="l-box__header">
                        <h3 class="title">Изменение пароля пользователя приложения Процент+</h3>
                    </div>
                    <div class="l-box__main">
                        <form class="form" id="new_mobile_user" action="{{url("/api/mobile_users/password")}}" accept-charset="UTF-8" method="post">
                            <input name="utf8" type="hidden" value="✓">
                            @method("PUT")
                            <input type="hidden" name="mobile_user[id]" value="{{$user->id}}">
                            <input type="hidden" value="{{Request::input("reset_password_token")}}" name="mobile_user[reset_password_token]" id="mobile_user_reset_password_token">
                            <div class="form__item">
                                <input autofocus="autofocus" autocomplete="off" class="input" placeholder="Новый пароль" type="password" name="mobile_user[password]"
                                       id="mobile_user_password">
                                <em>(не менее 6 символов)</em>
                            </div>
                            <div class="form__item">
                                <input autocomplete="off" class="input" placeholder="Подтверждение пароля" type="password" name="mobile_user[password_confirmation]"
                                       id="mobile_user_password_confirmation">
                            </div>
                            <div class="form__footer">
                                <input type="submit" name="commit" value="Изменить пароль" class="form__btn btn btn--prime" data-disable-with="Изменить пароль">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
<script>
    $("#new_mobile_user").submit(function (e) {
        e.preventDefault();
        let url = $(this).attr("action");
        var data = {};
        for (var d of $(this).serializeArray()) {
            data[d['name']] = d['value'];
        }
        $.post(url, data, function (r) {
            if (r.errors_count === 0) {
                $("form").css("display",'none')
            }
            $(".title").text(r.msg)
        })
    })
</script>
</html>
