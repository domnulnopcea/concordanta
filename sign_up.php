<?php
    require_once './utils.php';
    require_once './db/db_connect.php';

    $errors = array('first_name_empty' => false, 'last_name_empty' => false, 'email_empty' => false, 'email_not_valid' => false,
        'email_duplicate' => false, 'username_empty' => false, 'password_empty' => false, 'passwords_do_not_match' => false);

    if (isset($_POST['create'])) {

        $first_name = Util::cleanRegularInputField($_POST['first_name']);
        $last_name = Util::cleanRegularInputField($_POST['last_name']);
        $email = Util::cleanRegularInputField($_POST['email']);
        $username = Util::cleanRegularInputField($_POST['username']);
        $password1 = Util::cleanRegularInputField($_POST['password1']);
        $password2 = Util::cleanRegularInputField($_POST['password2']);

        if (empty($first_name))
            $errors['first_name_empty'] = true;

        if (empty($last_name))
            $errors['last_name_empty'] = true;

        if (empty($email))
            $errors['email_empty'] = true;

        if (!Util::validateEmailAddress($email))
            $errors['email_not_valid'] = true;
        $email_exists = Util::getEmailAddressByName($mysqli, $email);
        if ($email_exists)
            $errors['email_duplicate'] = true;

        if (empty($username))
            $errors['username_empty'] = true;

        if (empty($password1))
            $errors['password_empty'] = true;

        if ($password1 != $password2)
            $errors['passwords_do_not_match'] = true;

        $hasError = false;
        foreach ($errors as $key => $value)
            if ($value) {
                $hasError = true;
                break;
            }
        if (!$hasError) {
            $date = date('Y-m-d H:m');
            $q = 'insert into user(first_name, last_name, email, username, password, date_created) values(?, ?, ?, ?, MD5(?), ?)';

            if ($stmt = $mysqli->prepare($q)) {
                $stmt->bind_param("ssssss", $first_name, $last_name, $email, $username, $password1, $date);
                $stmt->execute();

                // trimite mail cu datele
                require_once '../utils/swiftmailer/lib/swift_required.php';
                UtilEmail::sendSignUpEmail(array($email), array('domnulnopcea@gmail.com', 'dan.petronela.nicoara@gmail.com'), $username, $password1);

                header('Location: sign_up_success.php');
            }
        }
    }

    require_once './header.php';
?>
<div align="left">
    <form name="sign_up" method="post" action="sign_up.php">
        <table class="backgroundForm" cellpadding="3" cellspacing="3">
            <tr>
                <td colspan="2">
                    <div class="headerPageTextSmall">Formular pentru creare cont</div>
                </td>
            </tr>

            <tr>
                <td valign="top">Nume</td>
                <td><input class="inputText" type="text"
                           value="<?php if (isset($last_name))
                               echo $last_name ?>"
                           name="last_name"/> <?php if ($errors['last_name_empty']): ?> <br/>
                    <div class="error">Numele nu poate fi gol</div> <?php endif ?>
                </td>
            </tr>
            <tr>
                <td valign="top">Prenume</td>
                <td><input class="inputText" type="text"
                           value="<?php if (isset($first_name))
                               echo $first_name ?>"
                           name="first_name"/> <?php if ($errors['first_name_empty']): ?> <br/>
                    <div class="error">Prenumele nu poate fi gol</div> <?php endif ?>
                </td>
            </tr>
            <tr>
                <td valign="top">Email</td>
                <td><input class="inputText" type="text"
                           value="<?php if (isset($email))
                               echo $email ?>"
                           name="email"/> <?php if ($errors['email_empty']): ?> <br/>
                    <div class="error">Completati adrea de email</div> <?php elseif ($errors['email_not_valid']): ?>
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
                           value="<?php if (isset($username))
                               echo $username ?>"
                           name="username"/> <?php if ($errors['username_empty']): ?> <br/>
                    <div class="error">Numele de utilizator nu poate fi gol</div> <?php endif ?>
                </td>
            </tr>
            <tr>
                <td valign="top">Parola</td>
                <td><input class="inputText" type="password" value=""
                           name="password1"/> <?php if ($errors['password_empty']): ?> <br/>
                    <div class="error">Completati parola</div> <?php endif ?>
                </td>
            </tr>
            <tr>
                <td valign="top">Repetati parola</td>
                <td><input class="inputText" type="password" value=""
                           name="password2"/> <?php if ($errors['passwords_do_not_match']): ?>
                    <br/>
                    <div class="error">Parola trebuie sa fie aceasi in ambele campuri.</div>
                    <?php endif ?>
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <hr size="1"/>
                    <button type="submit" class="btn btn-success" name="create">Creeaza cont</button>
                </td>
            </tr>
        </table>
    </form>
</div>
<?php require_once './footer.php'; ?>