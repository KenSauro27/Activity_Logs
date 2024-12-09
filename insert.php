<?php


session_start();
require_once 'core/dbConfig.php';
require_once 'core/models.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'first_name' => $_POST['first_name'],
        'last_name' => $_POST['last_name'],
        'email' => $_POST['email'],
        'art_specialization' => $_POST['art_specialization'],
        'experience' => $_POST['experience'],
        'preferred_medium' => $_POST['preferred_medium'],
        'phone_number' => $_POST['phone_number']
    ];

    foreach ($data as $key => $value) {
        if (empty($value)) {
            $error_message = "Please fill out all fields.";
            break;
        }
    }

    if (!isset($error_message)) {
        $insertResult = insertApplicant($pdo, $data);

        if ($insertResult['statusCode'] == 200) {
            if (isset($_SESSION['user_id'], $_SESSION['username'])) {
                $user_id = $_SESSION['user_id'];
                $username = $_SESSION['username'];
                $actions = "Insert Applicant";
                $action_details = "Inserted a new applicant: " . json_encode($data);

                logActivity($user_id, $username, $actions, $action_details);
            }

            $_SESSION['message'] = $insertResult['message'];
            header("Location: index.php");
            exit();
        } else {
            $_SESSION['message'] = $insertResult['message'];
            $_SESSION['status'] = $insertResult['statusCode'];
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insert New Applicant</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <div class="container">
        <header>Insert New Applicant</header>

        <?php if (isset($error_message)) : ?>
            <p class="error"><?= $error_message; ?></p>
        <?php endif; ?>

        <form action="insert.php" method="POST">
            <label for="first_name">First Name:</label>
            <input type="text" name="first_name" value="<?= isset($data['first_name']) ? $data['first_name'] : ''; ?>" required><br>

            <label for="last_name">Last Name:</label>
            <input type="text" name="last_name" value="<?= isset($data['last_name']) ? $data['last_name'] : ''; ?>" required><br>

            <label for="email">Email:</label>
            <input type="email" name="email" value="<?= isset($data['email']) ? $data['email'] : ''; ?>" required><br>

            <label for="art_specializationspecialization">Art Specialization:</label>
            <input type="text" name="art_specialization" value="<?= isset($data['art_specialization']) ? $data['art_specialization'] : ''; ?>" required><br>

            <label for="experience">Experience:</label>
            <input type="text" name="experience" value="<?= isset($data['experience']) ? $data['experience'] : ''; ?>" required><br>

            <label for="preferred_medium">Preferred Medium:</label>
            <input type="text" name="preferred_medium" value="<?= isset($data['preferred_medium']) ? $data['preferred_medium'] : ''; ?>" required><br>

            <label for="phone_number">Phone Number:</label>
            <input type="text" name="phone_number" value="<?= isset($data['phone_number']) ? $data['phone_number'] : ''; ?>" required><br>

            <input type="submit" name="insertNewApplicantBtn" value="Insert Applicant">
        </form>

        <p><a href="index.php">Back to Applicant List</a></p>
    </div>
</body>

</html>