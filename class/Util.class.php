<?php

    class Util
    {
		/**
		 * Number of elements to show on one page.
		 * @var unknown_type
		 */
        const ELEMENTS_PER_PAGE = 20;
		/**
		 * Creates pagination to the specific page.
		 * @param unknown_type $totalElements - Number of total elements.
		 * @param unknown_type $currentPage - Current page.
		 * @param unknown_type $pageLink - TODO
		 * @param unknown_type $parameters - TODO
		 */
        public static function renderPaginator ($totalElements, $currentPage, $pageLink, $parameters = null)
        {
            if ($parameters)
                $parameters = '&' . $parameters;
            $totalPages = round($totalElements / Util::ELEMENTS_PER_PAGE);

            $startPage = $currentPage - 10;
            $endPage = $currentPage + 10;

            if ($startPage < 1)
                $startPage = 1;

            if ($endPage > $totalPages)
                $endPage = $totalPages;
			// Enable the first and previous options if current page is greater then 2.
            if ($currentPage >= 2) {
                echo '<a href="' . $pageLink . '.php?page=1' . $parameters . '">First</a> ';
                echo '<a href="' . $pageLink . '.php?page=' . ($currentPage - 1) . $parameters . '">Prev</a> ';
            } else {
                echo 'First Prev ';
            }
			// Build the pagination html.
            for ($i = $startPage; $i <= $endPage; $i++) {
            	// If this is the current page then apply a style to it.
				if ($i == $currentPage) {
					echo '<a href="' . $pageLink .
					'.php?page=' . $i . $parameters . '"
					style="text-decoration: initial !important;color: red;">' . $i . '</a> ';
				}
				else {
                	echo '<a href="' . $pageLink . '.php?page=' . $i . $parameters . '">' . $i . '</a> ';
				}
            }
			// Enable next and last options if current page is eqal to last - 1 page.
            if ($currentPage <= ($totalPages - 1)) {
                echo '<a href="' . $pageLink . '.php?page=' . ($currentPage + 1) . $parameters . '">Next</a> ';
                echo '<a href="' . $pageLink . '.php?page=' . ($totalPages) . $parameters . '">Last</a> ';
            } else {
                echo 'Next Last';
            }
        }
		/**
		 * Clear the input from html and php tags.
		 * @param unknown_type $value - The input to be cleaned.
		 */
        public static function cleanRegularInputField ($value)
        {
            return strip_tags(trim($value));
        }
		/**
		 * Validate email address.
		 * @param unknown_type $email - The email address to validate.
		 * @return number - 0 if there is no match or 1 if there is match.
		 */
        public static function validateEmailAddress ($email)
        {
            return preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i", $email);
        }
		/**
		 * TODO Add comment.
		 * @param unknown_type $mysqli
		 * @param unknown_type $email
		 * @param unknown_type $exceptionUserId
		 */
        public static function getEmailAddressByName ($mysqli, $email, $exceptionUserId = null)
        {
            $q = "select LOWER(email) as email from user where oa_user_flag is null and LOWER(email) = '" . $email . "'";
            if ($exceptionUserId)
                $q .= ' and id != ' . $exceptionUserId;

            if ($stmt = $mysqli->prepare($q)) {
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows) {
                    $row = $result->fetch_array(MYSQLI_ASSOC);
                    return $row['email'];
                } else
                    return false;
            }
        }

        /**
         * Generate a random password between 10000 and 99999.
         */
        public static function generateRandomPassword ()
        {
            return rand(10000, 99999);
        }

        /**
         * Check to see if the user is logged in the application.
         */
        public static function check_log_in ()
        {
            session_start();

            if (!$_SESSION['user_data']) {
                header('Location: ./../index.php');
                exit;
            }
        }
		/**
		 * TODO Add comment.
		 * @param unknown_type $mysqli
		 */
        public static function setUTF8Mode ($mysqli)
        {
            $query = "set names 'utf8'";
            $mysqli->query($query);
        }
    }