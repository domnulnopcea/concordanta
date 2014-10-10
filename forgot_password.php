<?php
    $errors = array('email_not_valid' => false, 'email_not_found' => false);
    if (isset($_POST['get_password'])) {
        require_once './utils.php';
        require_once './db/db_connect.php';

        require_once './utils/swiftmailer/lib/swift_required.php';

        $email = Util::cleanRegularInputField($_POST['email']);

        if (!Util::validateEmailAddress($email))
            $errors['email_not_valid'] = true;

        if (!Util::getEmailAddressByName($mysqli, $email)) {
            $errors['email_not_found'] = true;
        }

        if (!$errors['email_not_valid'] && !$errors['email_not_found']) {
            $random_password = Util::generateRandomPassword();
            User::updatePasswordWhereEmail($mysqli, $random_password, $email);

            UtilEmail::sendNewPaswordEmail($email, $random_password);

            header('Location: forgot_password_success.php');
        }
    }

    require_once 'header.php';
?>
<div>Introduceti adresa de email pe care ati folosit-o la crearea
    contului.
</div>
<form action="forgot_password.php" name="contact" method="post">
    <table align="center" class="backgroundForm" cellspacing="3"
           cellpadding="3" border="0">
        <tr>
            <td colspan="2" class="headerPageTextSmall">Recuperare parola</td>
        </tr>
        <tr>
            <td width="80px" valign="top">Adresa email</td>
            <td valign="top"><input class="inputText" type="text" name="email"
                                    value=""/> <?php if ($errors['email_not_valid']): ?> <br/>
                <div class="error">Adresa de email nu este valida</div> <?php elseif ($errors['email_not_found']): ?>
                <br/>
                <div class="error">Adresa de email nu apartine vreunui cont</div> <?php endif ?>
            </td>
        </tr>
        <tr>
            <td></td>
            <td align="left">
                <hr size="1"/>
                <input class="button" type="submit"
                       value="Recupereaza parola" name="get_password"/>
            </td>
        </tr>
    </table>
</form>

<?php require_once 'footer.php'; ?>