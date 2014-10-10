<?php

    class WordForm
    {

        public static function getWordFormsForWordID ($mysqli, $word_id)
        {
            $q = 'select id, word_form_id ' .
                'from word_form ' .
                'where (deleted_flag IS NULL or deleted_flag = 0) and word_id = ' . $word_id;

            if ($stmt = $mysqli->prepare($q)) {
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows)
                    return $result;
                else
                    return false;
            }
        }

        public static function getByID ($mysqli, $id)
        {
            $q = 'select * ' .
                'from word_form ' .
                'where id = ' . $id;

            if ($stmt = $mysqli->prepare($q)) {
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows)
                    return $result->fetch_array(MYSQLI_ASSOC);
                else
                    return false;
            }
        }

        public static function getDeletedWordsWithLimit($mysqli, $page, $elementsPerPage, $word = null) {
            $q = 'select word_form.id, word.name as base_word, wf.name as form_word, user.first_name, user.last_name, user.oa_user_flag ' .
                'from word_form ' .
                'left join word on word_form.word_id = word.id ' .
                'left join word wf on word_form.word_form_id = wf.id ' .
                'left join user on word_form.deleted_by = user.id ' .
                'where deleted_flag = 1 ';

            if ($word)
                $q .= " and (word.name like '%" . $word . "%' OR wf.name like '%" . $word . "%') ";

            $q .=  'limit ' . ($page - 1) * $elementsPerPage . ', ' . $elementsPerPage;

            if ($stmt = $mysqli->prepare($q)) {
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows)
                    return $result;
                else
                    return false;
            }
        }

        public static function getAllDeletedWords($mysqli, $word = null) {
            $q = 'select word_form.id ' .
                 'from word_form ' .
                 'left join word on word.id = word_form.word_id ' .
                 'where deleted_flag = 1 ' .
                 "and word.name like '%" . $word . "%'";

            if ($stmt = $mysqli->prepare($q)) {
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows)
                    return $result;
                else
                    return false;
            }
        }

        public static function cancelDeletedWordForm($mysqli, $id) {
            $query = "update word_form set deleted_flag = 0, deleted_by = NULL where id = " . $id . " LIMIT 1";
            $mysqli->query($query);
        }
    }