<?php


/**
 * Class Users
 */
class Users
{

    public function count($id_or_nick = NULL){

        global $DB;
        $result = 0;
        $id_or_nick = htmlspecialchars($id_or_nick);

        if ($id_or_nick) {
            $result = $DB->count("users", [
                "OR" => [
                    "id" => $id_or_nick,
                    "nickname" => $id_or_nick
                ]
            ]);
        } else {
            $result = $DB->count("users");
        }

        return $result;
    }

    public function get($id_or_nick, $select = [ "id", "user_group", "nickname", "email", "password", "date_registration", "blocked" ]){

        global $DB;
        $result = [];
        $id_or_nick = htmlspecialchars($id_or_nick);

        $result = $DB->select("users", $select,[
            "OR" => [
                "id" => $id_or_nick,
                "nickname" => $id_or_nick
            ]
        ]);

        return $result[0];
    }

    public function update($user_id, $updates){

        global $DB;
        $result = '';

        $user = Users::get($user_id, ["nickname"]);

        if ($_SESSION['nickname'] === $user[0]['nickname'] || $_SESSION['user_group'] === 'admin') {
            $result = $DB->update("users", $updates, [
                "id" => $user_id
            ]);
            $result = 'ok';
        } else {
            $result = 'Другой пользователь';
        }

        return $result;
    } 

    public function newUser($nickname, $email, $user_password){

        global $DB;
        $result = '';

        $user_nickname = htmlspecialchars($nickname);
        $user_email = htmlspecialchars($email);

        if ($user_nickname && $user_email && $user_password) {

            $nick_exist = Users::count($user_nickname);
            $user_exist = $DB->count("users", [
                "email" => $user_email,
            ]);

            if ($nick_exist !== 0) {
                $result = 'Этот никнейм уже используется, придумай другой';
            }

            if ($user_exist !== 0) {
                $result = 'На эту почту уже зарегистрирован аккаунт';
            }

            if (!ctype_alnum($user_nickname) || strlen($user_nickname) < 4 || strlen($user_nickname) > 15) {
                $result = 'Ник должен быть от 4 до 15 символов и состоять только из букв и цифр';
            }

            if (strlen($user_email) < 7 || strlen($user_email) > 60 || strpos($user_email, '@') === false) {
                $result = 'Не очень похоже на настоящую почту... Она должна быть от 7 до 60 символов.';
            }

            if (strlen($user_password) < 6 || strlen($user_password) > 60) {
                $result = 'Пароль должен быть от 6 до 60 символов';
            }

            if (!$result) {

                $DB->insert("users", [
                    "user_group" => 'user',
                    "nickname" => $user_nickname,
                    "email" => $user_email,
                    "password" => hash('sha256', $user_nickname . $user_password . SALT),
                ]);

                $_SESSION['login'] = true;
                $_SESSION['nickname'] = $user_nickname;
                $_SESSION['user_group'] = 'user';

                $result = 'ok';
            }
        }

        return $result;
    }

    public function login($nickname, $password){

        global $DB;
        $result = '';

        $user_nickname = htmlspecialchars($nickname);
        $user_password = hash('sha256', $nickname . $password . SALT);

        $user_exist = Users::count($user_nickname);

        if ($user_exist !== 1) {

            $result = 'Такого пользователя не существует';

        } else {

            $user = Users::get($user_nickname,[ "nickname", "user_group", "password", "blocked" ]);

            if ($user['password'] !== $user_password) {
                $result = 'Неверный логин пользователя или пароль';
            }
            if ($user['blocked'] === '1') {
                $result = 'Ваш аккаунт заблокирован';
            }

        }

        if (!$result) {

            $_SESSION['login'] = true;
            $_SESSION['nickname'] = $user['nickname'];
            $_SESSION['user_group'] = $user['user_group'];

            $result = 'ok';
        }

        return $result;
    }

    public function getAll($limit = 10, $page = 1, $sort_by_desc = true, $blocked = false){

        global $DB;
        $result = [];
        $page = ($page - 1) * $limit;

        if ($sort_by_desc) {
            $result = $DB->select("users",[ "id", "user_group", "nickname", "email", "password", "date_registration", "blocked" ],[
                "blocked" => $blocked,
                "ORDER" => [
                    "date_registration" => 'DESC'
                ],
                "LIMIT" => [$page,$limit]
            ]);
        } else {
            $result = $DB->select("users",[ "id", "user_group", "nickname", "email", "password", "date_registration", "blocked" ],[
                "blocked" => $blocked,
                "ORDER" => [
                    "date_registration" => 'ASC'
                ],
                "LIMIT" => [$page,$limit]
            ]);
        }

        return $result;
    }
}