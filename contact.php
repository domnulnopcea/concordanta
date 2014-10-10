<?php
    if (isset($_POST['send_message'])) {
        require_once './utils.php';

        require_once './utils/swiftmailer/lib/swift_required.php';

        $name = $_POST['name'];
        $subject = $_POST['subject'];
        $message = $_POST['message'];
        $email_from = $_POST['email'];

        UtilEmail::sendContactEmail(array('domnulnopcea@gmail.com', 'dan.petronela.nicoara@gmail.com'), $name, $subject, $message, $email_from);

        header('Location: contact_success.php');
    }

    require_once './header.php';
?>

<form action="contact.php" name="contact" method="post">
    <table align="center" class="backgroundForm" cellspacing="3"
           cellpadding="3" border="0">
        <tr>
            <td colspan="2" class="headerPageTextSmall">Trimite-ne un mesaj</td>
        </tr>
        <tr>
            <td width="80px" valign="top">Numele tau</td>
            <td><input class="inputText" type="text" name="name" value=""/>
            </td>
        </tr>
        <tr>
            <td width="80px" valign="top">Email</td>
            <td><input class="inputText" type="text" name="email" value=""/>
                (camp necesar daca doresti un raspuns)
            </td>
        </tr>
        <tr>
            <td valign="top">Subiect</td>
            <td><input class="inputText" type="text" name="subject" value=""/>
            </td>
        </tr>
        <tr>
            <td valign="top">Mesaj</td>
            <td><textarea name="message" class="inputTextAreaLarge"></textarea>
            </td>
        </tr>
        <tr>
            <td></td>
            <td align="left">
                <hr size="1"/>
                <button type="submit" class="btn btn-success" name="send_message">Trimite mesaj</button>
            </td>
        </tr>
    </table>
</form>

<?php require_once './footer.php'; ?>