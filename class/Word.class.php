<?php

    class Word
    {
        public static function getByName ($mysqli, $name) {
            $query = "SELECT id, name FROM word WHERE LOWER(name) = ? ";
            if ($stmt = $mysqli->prepare($query)) {

                $stmt->bind_param("s", mb_strtolower($name));
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows)
                    return $result->fetch_array(MYSQLI_ASSOC);
                else
                    return null;
            }
        }

        public static function getByID ($mysqli, $id) {
            $q = 'select id, name from word where id = ' . $id;
            $r = $mysqli->query($q);

            return $r->fetch_array(MYSQLI_ASSOC);
        }

        public static function getWordLike ($mysqli, $name, $limit = null) {
            $q = "select word.id, name " .
                "from word " .
                "left join word_form on word_form.word_id = word.id " .
                "where word_form.deleted_flag IS NULL " .
                "AND LOWER(name) LIKE '%" . mb_strtolower($name) . "%' " .
                "group by word_form.word_id ";
            if ($limit)
                $q .= "LIMIT 10";

            $result = $mysqli->query($q);
            if ($result->num_rows)
                return $result;
            else
                return false;
        }

        public static function getWordsForIDs ($mysqli, $wordIds) {
            $q = 'select id, name from word where id in (' . implode(', ', $wordIds) . ')';
            if ($stmt = $mysqli->prepare($q)) {
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows)
                    return $result;
                else
                    return false;
            }
        }

        public static function getWordVersesExactForm ($mysqli, $wordName) {
            $q = 'select word.id, bible.verse, bible.chapter, bible.book, bible.text ' .
                'from word ' .
                'left join word_verse on word.id = word_verse.word_id ' .
                'left join bible on bible.id = word_verse.verse_id ' .
                'where LOWER(name) = LOWER(?) ';

            if ($stmt = $mysqli->prepare($q)) {
                $stmt->bind_param("s", $wordName);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows)
                    return $result;
                else
                    return false;
            }
        }

        public static function getWordVersesForWords ($mysqli, $words) {
            $q = 'select word.id, bible.verse, bible.chapter, bible.book, bible.text ' .
                'from word ' .
                'left join word_verse on word.id = word_verse.word_id ' .
                'left join bible on bible.id = word_verse.verse_id ' .
                'where ';

            $q_search_part = array();
            for ($i = 0; $i < count($words); $i++)
                $q_search_part[] = "LOWER(word.name) = LOWER('" . $words[$i] . "')";

            $q .= '(' . implode(' OR ', $q_search_part) . ') ';
            $q .= 'ORDER BY word.id';

            if ($stmt = $mysqli->prepare($q)) {
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows)
                    return $result;
                else
                    return false;
            }
        }
    }