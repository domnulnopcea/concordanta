<?php
    session_start();
    require_once './utils.php';

    try {

        $openid = new LightOpenID('www.concordanta.ro');
        if (!$openid->mode) {

            $openid->identity = 'https://www.google.com/accounts/o8/id';
            $openid->required = array(
                'namePerson',
                'namePerson/first',
                'namePerson/last',
                'contact/email',
            );
            header('Location: ' . $openid->authUrl());

        } elseif ($openid->mode == 'cancel') {
            header('Location: sign_in.php');
        } else {
            require_once './db/db_connect.php';
            $user_data = $openid->getAttributes();
            $user_data['google_auth'] = true;

            $oa_user = User::getOAPossibleUser($mysqli, $user_data['contact/email'], $user_data['namePerson/first'], $user_data['namePerson/last'], $user_data['contact/email']);
            if ($oa_user) {
                $user_data['id'] = $oa_user['id'];
                $user_data['super_user_flag'] = $oa_user['super_user_flag'];
                $user_data['oa_user_flag'] = $oa_user['oa_user_flag'];
                $user_data['dont_ask_delete_derivate_form_flag'] = $oa_user['dont_ask_delete_derivate_form_flag'];
                $user_data['date_created'] = $oa_user['date_created'];
            } else {
                // create a user in our database for future use... we need an id for this user
                $q = 'insert into user(oa_user_flag, first_name, last_name, email, username, password, date_created) values(?, ?, ?, ?, ?, MD5(?), ?)';
                $date = date('Y-m-d H:m');
                require_once './db/db_connect.php';
                $empty_password = '';
                $oa_user_flag = 1;
                if ($stmt = $mysqli->prepare($q)) {
                    $stmt->bind_param("issssss", $oa_user_flag, $user_data['namePerson/first'], $user_data['namePerson/last'], $user_data['contact/email'], $user_data['contact/email'], $empty_password, $date);
                    $stmt->execute();
                    $user_data['id'] = $mysqli->insert_id;
                }
            }

            $_SESSION['user_data'] = $user_data;
            header('Location: index.php');
        }
    } catch (ErrorException $e) {
        echo $e->getMessage();
    }