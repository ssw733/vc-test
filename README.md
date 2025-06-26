Тестовое задание для vc.ru, Symfony 7.3-based, БД - mysql
<br/>
Установка: 
<br/>
```bash
cp .env_example .env.dev
composer i
php bin/console app:create-mock
symfony server:start
```

Имеется 3 таблицы: <br/>
post ( <br/>
id, <br/>
timestamp, <br/>
title,<br/>
data - содрежимое, <br/>
hotness, <br/>
views - счётчик просмотров (3 просмотра вместо 1000))
<br/><br/>
user ( <br/>
id, <br/>
ip - для идентификации уникального пользователя
)
<br/><br/>
user_posts - информация о постах, просмотренных пользвателями ( <br/>
id (необязательное), <br/>
postId, <br/>
userId, <br/>
timestamp (необязательное)<br/>
)

! post.views (скрыть посты, у которых более 3 просмотров) обновляем по крону из таблицы user_posts<br/>
! Инфо о просмотренных статьях отсылаем пакетами раз в 10 сек

Изменил вручную некоторые индексы в таблицах, не разобрался как в ORM создавать кастомный PRIMARY KEY

Demo http://79.132.124.108:8000/get_posts
