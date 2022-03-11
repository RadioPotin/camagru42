<?

include_once 'dbh.php';

const SENDER_EMAIL_ADDRESS = 'no-reply@email.com';

const APP_URL = 'http://localhost:8888';

// returns a random hex for account activation code generation
function generate_activation_code(): string
{
    return bin2hex(random_bytes(16));
}

// takes email of user and
// prior generated activation code for
// his account registration confirmation
function send_activation_email(string $email, string $activation_code): void
{
    // create the activation link
    $activation_link = APP_URL . "/activate.php?email=$email&activation_code=$activation_code";

    // set email subject & body
    $subject = 'Please activate your account';
    $message = <<<MESSAGE
            Hi,
            Please click the following link to activate your account:
            $activation_link
MESSAGE;
    // email header
    $header = "From:" . SENDER_EMAIL_ADDRESS;

    // send the email
    mail($email, $subject, nl2br($message), $header);
}

// deletes a user from login table
function delete_user_by_usern(int $usern, int $active = 0)
{
    $sql = 'DELETE FROM login
        WHERE usern =:usern and active=:active';

    $pdo = connect_todb();
    $statement = $pdo->prepare($sql);
    $statement->bindParam(':usern', $id);
    $statement->bindParam(':active', $active);

    return $statement->execute();
}

function find_unverified_user(string $activation_code, string $email)
{
    $sql = 'SELECT
        usern,
        activation_code,
        activation_expiry < now() as expired
        FROM login
        WHERE active = 0
        AND email=:email';

    $pdo = connect_todb();
    $statement = $pdo->prepare($sql);

    $statement->bindParam(':email', $email);
    $statement->execute();

    $user = $statement->fetchAll();

    if ($user) {
        // already expired, delete the in active user with expired activation code
        if ((int)$user['expired'] === 1) {
            delete_user_by_usern($user['usern']);
            return null;
        }
        // verify the password
        if (password_verify($activation_code, $user['activation_code'])) {
            return $user;
        }
    }

    return null;
}

function activate_user(int $username): bool
{
    $sql = 'UPDATE login
        SET active = 1,
        activated_at = CURRENT_TIMESTAMP
        WHERE usern=:usern';

    $pdo = connect_todb();
    $statement = $pdo->prepare($sql);
    $statement->bindParam(':usern', $username);

    return $statement->execute();
}

?>
