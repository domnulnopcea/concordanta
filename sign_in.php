<?php
    $error = null;
    if (isset($_POST['sign_in'])) {

        require_once './db/db_connect.php';
        $username = $_POST['username'];
        $password = $_POST['password'];

        $query = 'SELECT username, id, email, first_name, last_name, super_user_flag, dont_ask_delete_derivate_form_flag ' .
            'FROM user ' .
            "WHERE username = ? AND password = MD5(?) " .
            "LIMIT 1";

        if ($stmt = $mysqli->prepare($query)) {

            $stmt->bind_param("ss", $username, $password);
            $stmt->execute();
            $result = $stmt->get_result();

            $row = $result->fetch_array(MYSQLI_ASSOC);
            if ($row['id']) {
                session_start();
                $_SESSION['user_data'] = $row;
                header('Location: ./index.php');

                exit;
            } else $error = true;
        }
    }

    require_once './header.php'
?>

<br/>

<div align="left">
    <form name="f_login" method="post" action="sign_in.php">
        <table>
            <tr>
                <td class="backgroundForm">
                    <table align="center" cellspacing="3"
                           cellpadding="3" width="300px">
                        <tr>
                            <td colspan="2" align="center">
                                <div style="text-align: center" class="headerPageTextSmall" align="center">Ai creat un cont pe site?</div>
                            </td>
                        </tr>
                        <tr>
                            <td>Utilizator</td>
                            <td><input class="inputText" type="text" value="" name="username"
                                       style="width: 240px;"/>
                            </td>
                        </tr>
                        <tr>
                            <td>Parola</td>
                            <td><input class="inputText" type="password" value=""
                                       name="password" style="width: 240px;"/>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td align="right">
                                <div>
                                    <a href="forgot_password.php">Ţi-ai uitat parola?</a>
                                </div>
                                <div>
                                    Nu ai cont? Creează-ţi <a href="sign_up.php">aici<a/>.
                                </div>
                            </td>
                        </tr>
                        <?php if ($error): ?>
                        <tr>
                            <td colspan="2"><span class="error">Numele de utilizator sau parolă
						greşită.</span></td>
                        </tr>
                        <?php endif ?>
                        <tr>
                            <td colspan="2">
                                <hr size="1"/>
                                <button type="submit" class="btn btn-success" name="sign_in">Conectare</button>
                            </td>
                        </tr>
                    </table>
                </td>
                <td></td>
                <td class="backgroundForm" valign="middle">
                    <table width="100%">
                        <tr>
                            <td align="center">
                                <div style="text-align: center" class="headerPageTextSmall" align="center">Autentificate folosind?</div>
                            </td>
                        </tr>
                        <tr>
                            <td align="center"><a href="login_google.php"><img src="./img/gmail.png" alt="Gmail" /></a></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </form>
</div>
<br/>
<?php require_once './footer.php'; ?>