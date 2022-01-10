<?php

/**
 * Class Comments
 */
class Comments
{
    public function new($post_id, $content){
        
        global $DB;
        $result = '';

        if ($post_id && $content) {

            $content = htmlspecialchars($content);

            if (mb_strlen($content) < 4 || mb_strlen($content) > 350) {
                $result = 'Комментарий может быть от 4 до 350 символов.';
            }

            if (!$result) {

                $DB->insert("comments",[
                    "post_id" => $post_id,
                    "content" => $content,
                    "owner" => $_SESSION['nickname'],
                ]);

                $result = 'ok';
            }
        } else {
            $result = 'Переданы не все параметры.';
        }

        return $result;
    }

    public function count($post_id = NULL){

        global $DB;
        $result = 0;

        if ($post_id) {
            $result = $DB->count("comments", [
                "post_id" => $post_id
            ]);
        } else {
            $result = $DB->count("comments");
        }

        return $result;
    } 

    public function getForPost($limit = 10, $page = 1, $post_id){

        global $DB;
        $result = [];
        $page = ($page - 1) * $limit;

        $result = $DB->select("comments",["id","owner","post_id","content","date"],[
            "post_id" => $post_id,
            "ORDER" => [
                "date" => 'DESC'
            ],
            "LIMIT" => [$page,$limit]
        ]);

        return $result;
    }

    public function getAll($limit = 10, $page = 1){

        global $DB;
        $result = [];
        $page = ($page - 1) * $limit;

        $result = $DB->select("comments",["id","owner","post_id","content","date"],[
            "ORDER" => [
                "date" => 'DESC'
            ],
            "LIMIT" => [$page,$limit]
        ]);

        return $result;
    }

    public function delete($comment_id){

        global $DB;
        $result = '';

        $comment = $DB->select("comments",["id","owner"],[
            "id" => $comment_id
        ]);

        if ($_SESSION['nickname'] === $comment[0]['owner'] || $_SESSION['user_group'] === 'admin') {
            $result = $DB->delete("comments", [
                "id" => $comment_id
            ]);
            $result = 'ok';
        } else {
            $result = 'Комментарий другого пользователя';
        }

        return $result;
    } 

}