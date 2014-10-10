<?php

    class Suggestion
    {

        public static function getDeleteSuggestions ($mysqli, $page, $elementsPerPge)
        {
            $q = "select SQL_CALC_FOUND_ROWS * from suggestion " .
                "where for_delete_flag = 1 order by id desc LIMIT " . (($page - 1) * $elementsPerPge) . ', ' . $elementsPerPge;

            if ($stmt = $mysqli->prepare($q)) {
                $stmt->execute();
                $result = $stmt->get_result();

                $qq = "SELECT FOUND_ROWS() as count;";
                $stmt2 = $mysqli->prepare($qq);
                $stmt2->execute();
                $result_total = $stmt2->get_result();
                $count = $result_total->fetch_array(MYSQLI_ASSOC);

                return array($result, $count['count']);
            }
        }

        public static function getUserForSuggestionID ($mysqli, $suggestionID)
        {
            $q = "select suggestion_user.id, user.first_name, user.last_name " .
                "from suggestion_user " .
                "left join user on user.id = suggestion_user.user_id " .
                "where suggestion_user.suggestion_id = " . $suggestionID . ' ' .
                "and user.id is not null " .
                "limit 1";

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