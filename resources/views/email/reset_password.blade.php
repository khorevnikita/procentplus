<div>
    <p>Здравствуйте, {{$user->email}}!</p>
    <p>Вы либо кто-либо другой запросил ссылку на сброс пароля в приложении Процент+</p>
    <p>
        <a href="{{url("/api/mobile_users/password/edit/$user->id?reset_password_token=$token")}}">Изменить пароль</a>
    </p>
    <p>Если Вы не запрашивали данное действие - просто игнорируйте данное письмо.</p>
    <p>Пароль не будет изменен до тех пор, пока Вы не проследуете по ссылке, данной выше.</p>
</div>
