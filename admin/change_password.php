<?php
    require_once './../utils.php';
    require_once './../db/db_connect.php';

    Util::check_log_in();
    Util::setUTF8Mode($mysqli);

    require_once './include/admin_head.php';

    require_once './../utils/swiftmailer/lib/swift_required.php';

    $errors = array(
        'currentPasswordDoesNotMatch' => false, // If the current password does not match.
        'newPasswordDoesNotMatch' => false, // Holds the error when the new password and the confirm new password do not match.
        'currentNewPasswordMatch' => false); // Holds the error when the current password and the new password are the same.

    $success = false; // If there are no error to display.

    if (isset($_POST['changePassword'])) {
        // Clear input.
        $currentPassword = Util::cleanRegularInputField($_POST['currentPassword']);
        $newPassword = Util::cleanRegularInputField($_POST['newPassword']);
        $verifyNewPassword = Util::cleanRegularInputField($_POST['verifyNewPassword']);

        // The new password and the confirm new password do not match.
        if ($newPassword != $verifyNewPassword) {
            $errors['newPasswordDoesNotMatch'] = true;
        }
        elseif ($currentPassword == $newPassword) {
            $errors['currentNewPasswordMatch'] = true;
        }
        else {
            // Retrieve the username, id and email for the user with the current password specify.
            $query = 'SELECT id, email ' .
                'FROM user ' .
                'WHERE password = MD5(?) ' .
                'LIMIT 1';

            if ($stmt = $mysqli->prepare($query)) {
                $stmt->bind_param("s", $currentPassword);
                $stmt->execute();
                $result = $stmt->get_result();
                // If the user has been found with the current password then update the password with the new one.
                if ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                    $newQuery = 'UPDATE user SET password = MD5("' . $newPassword . '") WHERE id = ' . $row['id'];
                    $mysqli->query($newQuery);
                    // And also send e-mail with the new passord.
                    UtilEmail::sendNewPaswordEmail($row['email'], $newPassword);
                    $success = true;
                }
                // Otherwise popup an error.
                else {
                    $errors['currentPasswordDoesNotMatch'] = true;
                }
            }
        }
    }
?>

<div align="left">
    <form name="f_login" method="post" action="change_password.php">
        <table align="center" class="backgroundForm" cellspacing="3"
               cellpadding="3" width="480px">
            <tr>
                <td colspan="2">
                    <div class="headerPageTextSmall">Aici puteti schimba parola</div>
                </td>
            </tr>
            <tr>
                <td>Parola curenta</td>
                <td><input class="inputText" type="password" name="currentPassword"/>
                </td>
            </tr>
            <tr>
                <td>Parola noua</td>
                <td><input class="inputText" type="password" name="newPassword"/>
                </td>
            </tr>
            <tr>
                <td>Confirmare parola noua</td>
                <td><input class="inputText" type="password"
                           name="verifyNewPassword">
                </td>
            </tr>
            <?php if ($errors['currentPasswordDoesNotMatch']): ?>
            <tr>
                <td colspan="2"><span class="error">Parolă curentă greşită.</span>
                </td>
            </tr>
            <?php elseif ($errors['newPasswordDoesNotMatch']): ?>
            <tr>
                <td colspan="2"><span class="error">Parolă reintrodusă greşit.</span>
                </td>
            </tr>
            <?php elseif ($errors['currentNewPasswordMatch']): ?>
            <tr>
                <td colspan="2"><span class="error">Parola curentă si cea nouă
						coincid.</span>
                </td>
            </tr>
            <?php elseif ($success): ?>
            <tr>
                <td colspan="2"><span class="error">Parolă schimbată cu succes. Un
						e-mail a fost trimis cu noua parolă.</span>
                </td>
            </tr>
            <?php endif ?>
            <tr>
                <td></td>
                <td><input class="button" type="submit" name="changePassword"
                           value="Schimbare parola"/>
                </td>
            </tr>
        </table>
    </form>
</div>

<?php
    require_once './include/admin_footer.php';
?>