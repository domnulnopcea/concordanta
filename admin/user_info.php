<?php
    require_once './../utils.php';

    Util::check_log_in();

    require_once '../db/db_connect.php';
    Util::setUTF8Mode($mysqli);

    $errors = array('empty_first_name' => false, 'empty_last_name' => false, 'empty_email' => false, 'email_not_valid' => false, 'email_duplicate' => false, 'empty_user_name' => false);

    $user = User::getById($mysqli, $_SESSION['user_data']['id']);
    // TODO Refactor this ! After "if" there is the same information. Which will remain ? 
    // Use User class getters.
    $firstName = $user['first_name'];
    $lastName = $user['last_name'];
    $email = $user['email'];
    $userName = $user['username'];
    $dont_ask_delete_derivate_form_flag = $user['dont_ask_delete_derivate_form_flag'];

    if (isset($_POST['create'])) {
    	// Get the values from the POST method.
        $firstName = Util::cleanRegularInputField($_POST['first_name']);
        $lastName = Util::cleanRegularInputField($_POST['last_name']);
        $email = Util::cleanRegularInputField($_POST['email']);
        $userName = Util::cleanRegularInputField($_POST['username']);
        // The new option for the user profile.
        $dont_ask_delete_derivate_form_flag = isset($_POST['dont_ask_delete_derivate_form_flag']) ? 1 : 0;
        // Check if first name is empty.
        if (empty($firstName))
            $errors['empty_first_name'] = true;
        // Check if the last name is empty.
        if (empty($lastName))
            $errors['empty_last_name'] = true;
        // Check if the email is empty.
        if (empty($email))
            $errors['empty_email'] = true;
        else if (!Util::validateEmailAddress($email))
            $errors['email_not_valid'] = true;
        else if (Util::getEmailAddressByName($mysqli, $email, $_SESSION['user_data']['id']))
            $errors['email_duplicate'] = true;

        if (empty($userName))
            $errors['empty_user_name'] = true;

        $error_found = false;
        foreach ($errors as $error)
            if ($error)
                $error_found = true;
        if (!$error_found) {
            User::updateByID($mysqli, $_SESSION['user_data']['id'], $lastName, $firstName, $email, $userName, $dont_ask_delete_derivate_form_flag);
            $_SESSION['user_data'] = User::getById($mysqli, $_SESSION['user_data']['id']);
        }
    }

    require_once './include/admin_head.php';
?>

<div align="left">
    <form name="user_info" method="post" action="user_info.php">
        <table class="backgroundForm" cellpadding="3" cellspacing="3">
            <tr>
                <td colspan="2">
                    <div class="headerPageTextSmall">Informatii profil</div>
                </td>
            </tr>

            <tr>
                <td valign="top">Nume</td>
                <td><input class="inputText" type="text"
                           value="<?php echo $lastName ?>" name="last_name"/> <?php if ($errors['empty_last_name']): ?>
                    <br/>
                    <div class="error">Numele nu poate fi gol</div> <?php endif ?>
                </td>
            </tr>
            <tr>
                <td valign="top">Prenume</td>
                <td><input class="inputText" type="text"
                           value="<?php echo $firstName ?>"
                           name="first_name"/> <?php if ($errors['empty_first_name']): ?>
                    <br/>
                    <div class="error">Prenumele nu poate fi gol</div> <?php endif ?>
                </td>
            </tr>
            <tr>
                <td valign="top">Email</td>
                <td><input class="inputText" type="text"
                           value="<?php echo $email ?>" name="email"/> <?php if ($errors['empty_email']): ?>
                    <br/>
                    <div class="error">Completati adresa de email</div> <?php elseif ($errors['email_not_valid']): ?>
                    <br/>
                    <div class="error">Adresa de email nu este valida
                    </div> <?php elseif ($errors['email_duplicate']): ?>
                    <br/>
                    <div class="error">Adresa de email nu este disponibila</div> <?php endif ?>
                </td>
            </tr>
            <tr>
                <td valign="top">Utilizator</td>
                <td><input class="inputText" type="text"
                           value="<?php echo $userName ?>" name="username"/> <?php if ($errors['empty_user_name']): ?>
                    <br/>
                    <div class="error">Numele de utilizator nu poate fi gol</div> <?php endif ?>
                </td>
            </tr>
            <tr>
            	<td align="right">
                    <input type="checkbox" name="dont_ask_delete_derivate_form_flag" 
                    <?php if ($dont_ask_delete_derivate_form_flag == 1) echo 'checked';?> />
                </td>
                <td>Nu ma intreba la operatiunea de stergere forma derivata
                    <br/>
                </td>
            </tr>
            <tr>
                <td></td>
                <td><input class="button" type="submit" value="Salveaza date"
                           name="create"/>
                </td>
            </tr>
            <tr>
            <td></td>
            	<td>
            		<?php 
	            		if (isset($_POST['create'])) { 
	            			if (!$error_found) echo '<span style="color:green;"> Datele au fost salvate </span>'; 
	            		}
            		?>
            	</td>
            </tr>
        </table>
    </form>
</div>
