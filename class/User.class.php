<?php
    /**
     * Holds information about the user.
     *
     */
    class User {

        private $userName = "";
        private $password = "";
        private $firstName = "";
        private $lastName = "";
        private $superUserFlag = "";
        private $dateCreated = "";
        private $email = "";
        private $dontAsk = "";

        public function __construct ($userName, $password, $firstName, $lastName, $superUserFlag, $dataCreated, $email, $dontAsk) {
            $this->userName = $userName;
            $this->password = $password;
            $this->firstName = $firstName;
            $this->lastName = $lastName;
            $this->superUserFlag = $superUserFlag;
            $this->dateCreated = $dataCreated;
            $this->email = $email;
            $this->dontAsk = $dontAsk;
        }

        public function getUserName () {
            return $this->userName;
        }

        public function getPassword () {
            return $this->password;
        }

        public function getFirstName () {
            return $this->firstName;
        }

        public function getLastName () {
            return $this->lastName;
        }

        public function getSuperUserFlag () {
            return $this->superUserFlag;
        }

        public function getDateCreated () {
            return $this->dateCreated;
        }

        public function getEmail () {
            return $this->email;
        }
        
        public function getDontAsk () {
        	return $this->dontAsk;
        }

        /**
         * Update user password using its email.
         * @param unknown_type $mysqli - DB connection.
         * @param unknown_type $password - User's new password.
         * @param unknown_type $email_address User email address.
         */
        public static function updatePasswordWhereEmail ($mysqli, $password, $email_address) {
            $query = "update user set password = MD5('" . $password . "') where email = '" . $email_address . "' LIMIT 1";
            $mysqli->query($query);
        }

        /**
         * Return a user by its id.
         * @param unknown_type $mysqli - DB connection.
         * @param unknown_type $id - User's id.
         */
        public static function getById ($mysqli, $id) {
            $q = "select * from user where id = ? limit 1";

            if ($stmt = $mysqli->prepare($q)) {
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows)
                	// TODO Should return a user object. Flavius ?!
                    return $result->fetch_array(MYSQLI_ASSOC);
                else
                    return null;
            }
        }

        /**
         * Return all the users in the application.
         * @param unknown_type $mysqli - DB connection.
         */
        public static function getAll ($mysqli, $page = null, $elementsPerPage = null) {
            $q = "SELECT * FROM user order by id desc";
            if ($page)
                $q .=  ' limit ' . ($page - 1) * $elementsPerPage . ', ' . $elementsPerPage;

            return $mysqli->query($q);
        }

        /**
         * Update user profile using its id.
         * @param unknown_type $mysqli - DB connection.
         * @param unknown_type $id - USer id.
         * @param unknown_type $lastName - User last name.
         * @param unknown_type $firstName - User first name.
         * @param unknown_type $email - User email.
         * @param unknown_type $userName - User's user name.
         * @param unknown_type $dontAsk - User's option.
         */
        public static function updateByID ($mysqli, $id, $lastName, $firstName, $email, $userName, $dontAsk) {
            $q = "UPDATE user SET last_name = ?, first_name = ?, email = ?, username = ?, dont_ask_delete_derivate_form_flag = ? where id = ?";

            if ($stmt = $mysqli->prepare($q)) {
                $stmt->bind_param("ssssii", $lastName, $firstName, $email, $userName, $dontAsk, $id);
                $stmt->execute();
            }
        }

        public static function getOAPossibleUser($mysqli, $username, $first_name, $last_name, $email) {

            $query = "SELECT * FROM user WHERE username = ? and first_name = ? and last_name = ? and email = ? and oa_user_flag = 1";
            if ($stmt = $mysqli->prepare($query)) {

                $stmt->bind_param("ssss", $username, $first_name, $last_name, $email);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows)
                    return $result->fetch_array(MYSQLI_ASSOC);
                else
                    return null;
            }
        }

        public static function getStatistics($mysqli, $userId) {
            $query = "select count(suggestion_user.id) as total
                        from user
                        left join suggestion_user on user.id = suggestion_user.user_id
                        where user.id = ?";

            $suggestions = 0;
            if ($stmt = $mysqli->prepare($query)) {

                $stmt->bind_param("i", $userId);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows) {
                    $result = $result->fetch_array(MYSQLI_ASSOC);
                    $suggestions = $result['total'];
                }
            }

            $query = "select count(suggestion.id) as total
                        from user
                        left join suggestion on user.id = suggestion.processed_by
                        where user.id = ?";

            $processed = 0;
            if ($stmt = $mysqli->prepare($query)) {

                $stmt->bind_param("i", $userId);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows) {
                    $result = $result->fetch_array(MYSQLI_ASSOC);
                    $processed = $result['total'];
                }
            }

            $query = "select count(word_form.id) as total
                        from user
                        left join word_form on user.id = word_form.deleted_by
                        where user.id = ?";

            $deleted = 0;
            if ($stmt = $mysqli->prepare($query)) {

                $stmt->bind_param("i", $userId);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows) {
                    $result = $result->fetch_array(MYSQLI_ASSOC);
                    $deleted = $result['total'];
                }
            }

            $query = "select count(id) as total
                        from word_for_homepage
                        where user_id = ?";

            $for_homepage = 0;
            if ($stmt = $mysqli->prepare($query)) {

                $stmt->bind_param("i", $userId);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows) {
                    $result = $result->fetch_array(MYSQLI_ASSOC);
                    $for_homepage = $result['total'];
                }
            }

            return array('deleted' => $deleted, 'suggestions' => $suggestions, 'processed' => $processed, 'for_homepage' => $for_homepage);
        }
    }

?>