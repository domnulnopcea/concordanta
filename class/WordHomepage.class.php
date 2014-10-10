<?php

    class WordHomepage
    {

        public static function getRandomWord ($mysqli)
        {
            $q = 'select word_id from word_for_homepage order by rand() limit 1';
            $r = $mysqli->query($q);

            return $r->fetch_array(MYSQLI_ASSOC);
        }
    }