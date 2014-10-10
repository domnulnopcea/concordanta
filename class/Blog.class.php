<?php

    class Blog
    {

        public static function getBlogArticles ($mysqli)
        {

            $q = "select blog.id, title, content, blog.date_created, user.first_name, user.last_name " .
                "from blog " .
                "left join user on user.id = blog.created_by " .
                "order by id desc";
            if ($stmt = $mysqli->prepare($q)) {
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows) {
                    return $result;
                } else
                    return false;
            }
        }

        public static function getArticleByID ($mysqli, $id)
        {
            $q = "select blog.id, title, content, date_created, user.first_name, user.last_name " .
                "from blog " .
                "left join user on user.id = blog.created_by " .
                "where blog.id = " . $id . ' ' .
                "order by id desc";
            if ($stmt = $mysqli->prepare($q)) {
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows) {
                    return $result->fetch_array(MYSQLI_ASSOC);
                } else
                    return false;
            }
        }
    }