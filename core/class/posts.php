<?php

/**
 * Class Posts
 */
class Posts
{

    public function count($id_or_url = NULL){

        global $DB;
        $result = 0;
        $id_or_url = htmlspecialchars($id_or_url);

        if ($id_or_url) {
            $result = $DB->count("posts", [
                "OR" => [
                    "id" => $id_or_url,
                    "url" => $id_or_url
                ]
            ]);
        } else {
            $result = $DB->count("posts");
        }

        return $result;
    }

    public function get($id_or_url, $select = [ "id", "title", "content", "image", "url", "date", "owner", "archive" ]){

        global $DB;
        $result = [];
        $id_or_url = htmlspecialchars($id_or_url);

        $result = $DB->select("posts", $select,[
            "OR" => [
                "id" => $id_or_url,
                "url" => $id_or_url
            ]
        ]);

        return $result[0];
    }

    public function delete($post_id){

        global $DB;
        $result = '';

        $post = Posts::get($post_id, ["owner","image"]);

        if ($_SESSION['nickname'] === $post[0]['owner'] || $_SESSION['user_group'] === 'admin') {

            if ($post['image']) {
                unlink( PROJECT_DIR . $post['image'] );
            }
            $result = $DB->delete("posts", [
                "id" => $post_id
            ]);
            $result = 'ok';

        } else {
            $result = 'Пост другого пользователя';
        }

        return $result;
    } 

    public function update($post_id, $updates){

        global $DB;
        $result = '';

        $post = Posts::get($post_id, ["owner"]);

        if ($_SESSION['nickname'] === $post[0]['owner'] || $_SESSION['user_group'] === 'admin') {
            $result = $DB->update("posts", $updates, [
                "id" => $post_id
            ]);
            $result = 'ok';
        } else {
            $result = 'Пост другого пользователя';
        }

        return $result;
    } 

    private function uploadImage($post_url, $file){
        $result = array();

        $image_type = getimagesize($file['image']['tmp_name']);

        if($image_type['mime'] != 'image/png' && 
                $image_type['mime'] != 'image/jpeg' && 
                $image_type['mime'] != 'image/pjpeg' && $image_type['mime'] != 'image/webp') {

                $result = [ 'status' => 0, 'message' => 'Некорректный тип файла' ];
        }

        if (filesize($file['image']['tmp_name']) > 26214400) {
            // 25 MB = 26214400 bytes
            $result = [ 'status' => 0, 'message' => 'Файл больше 25МБ, выберите другой' ];
        }

        if (empty($result)) {
            $image_hash_name = $post_url . '-' . md5(basename($file['image']['name']).rand(0,9).time()) . '.' . pathinfo($file['image']['name'], PATHINFO_EXTENSION);

            $image_path = PROJECT_DIR . UPLOAD_DIR . $image_hash_name;

            if (!move_uploaded_file($file['image']['tmp_name'], $image_path)) {
                $result = [ 'status' => 0, 'message' => 'Ошибка при загрузке файла' ];
            } else {
                $result = [ 'status' => 1, 'image_path' => UPLOAD_DIR . $image_hash_name ];
            }
        }

        return $result;
    }

    public function newPost($title, $content, $url, $file){
        
        global $DB;
        $result = '';

        if ($title && $content && $url) {

            $post_title = htmlspecialchars($title);
            $post_content = htmlspecialchars($content);
            $post_url = $url;

            $url_exist = Posts::count($post_url);

            if ($url_exist !== 0) {
                $result = 'Такой URL уже есть у другой статьи';
            }


            if ($file['image']['tmp_name'] && !$result) {
                $image_upload = Posts::uploadImage($post_url, $file);
                if ($image_upload['status'] === 1) {
                    $image_path = $image_upload['image_path'];
                } else {
                    $result = $image_upload['message'];
                }
            }

            if (!$result) {

                $DB->insert("posts",[
                    "title" => $post_title,
                    "content" => $post_content,
                    "url" => $post_url,
                    "image" => $image_path,
                    "owner" => $_SESSION['nickname'],
                ]);

                $result = 'ok';
            }
        }

        return $result;
    }

    public function updatePost($post_id, $title, $content, $url, $file){
        $result = '';

        if ($post_id && $title && $content && $url) {

            $post_title = htmlspecialchars($title);
            $post_content = htmlspecialchars($content);
            $post_url = $url;

            $url_now = Posts::get($post_id, ["url"]);

            if ($url_now['url'] !== $post_url) {
                $new_url_exist = Posts::count($post_url);

                if ($new_url_exist !== 0) {
                    $result = 'Такой URL уже есть у другой статьи';
                }
            }

            if ($file['image']['tmp_name'] && !$result) {
                $image_upload = Posts::uploadImage($post_url, $file);
                if ($image_upload['status'] === 1) {
                    $image_path = $image_upload['image_path'];

                    $image_now = Posts::get($post_id, ["image"]);
                    if ($image_now['image']) {
                        unlink( PROJECT_DIR . $image_now['image'] );
                    }
                } else {
                    $result = $image_upload['message'];
                }
            }

            if (!$result) {
                
                if ($image_path) {
                    $update = Posts::update($post_id, [
                        "title" => $post_title,
                        "content" => $post_content,
                        "url" => $post_url,
                        "image" => $image_path,
                    ]);
                } else {
                    $update = Posts::update($post_id, [
                        "title" => $post_title,
                        "content" => $post_content,
                        "url" => $post_url,
                    ]);
                }

                if ($update === 'ok') {
                    $result = 'ok';
                } else {
                    $result = $update;
                }
            }
        }

        return $result;
    }

    public function getAll($limit = 10, $page = 1, $sort_by_desc = true, $archive = false){

        global $DB;
        $result = [];
        $page = ($page - 1) * $limit;

        if ($sort_by_desc) {
            $result = $DB->select("posts",["id","title","content","image","url","date","owner","archive"],[
                "archive" => $archive,
                "ORDER" => [
                    "date" => 'DESC'
                ],
                "LIMIT" => [$page,$limit]
            ]);
        } else {
            $result = $DB->select("posts",["id","title","content","image","url","date","owner","archive"],[
                "archive" => $archive,
                "ORDER" => [
                    "date" => 'ASC'
                ],
                "LIMIT" => [$page,$limit]
            ]);
        }

        return $result;
    }

}