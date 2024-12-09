<?php
require_once 'core/dbConfig.php';
require_once 'core/models.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

if (isset($_GET['id'])) {
    $applicant_id = $_GET['id'];

    $query = "SELECT * FROM job_applicants WHERE id = :applicant_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':applicant_id', $applicant_id, PDO::PARAM_INT);
    $stmt->execute();
    $applicant = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$applicant) {
        echo "Invalid applicant ID.";
        exit();
    }

    $old_first_name = $applicant['first_name'];
    $old_last_name = $applicant['last_name'];
    $old_email = $applicant['email'];
    $old_art_specialization = $applicant['art_specialization'];
    $old_experience = $applicant['experience'];
    $old_preferred_medium = $applicant['preferred_medium'];
    $old_phone_number = $applicant['phone_number'];
} else {
    echo "No applicant ID provided.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $art_specialization = $_POST['art_specialization'];
    $experience = $_POST['experience'];
    $preferred_medium = $_POST['preferred_medium'];
    $phone_number = $_POST['phone_number'];

    $update_query = "UPDATE job_applicants SET first_name = :first_name, last_name = :last_name, 
                     email = :email, art_specialization = :art_specialization, experience = :experience, 
                     preferred_medium = :preferred_medium, phone_number = :phone_number WHERE id = :id";
    $update_stmt = $pdo->prepare($update_query);
    $update_stmt->bindParam(':first_name', $first_name);
    $update_stmt->bindParam(':last_name', $last_name);
    $update_stmt->bindParam(':email', $email);
    $update_stmt->bindParam(':art_specialization', $art_specialization);
    $update_stmt->bindParam(':experience', $experience);
    $update_stmt->bindParam(':preferred_medium', $preferred_medium);
    $update_stmt->bindParam(':phone_number', $phone_number);
    $update_stmt->bindParam(':id', $applicant_id, PDO::PARAM_INT);
    $update_stmt->execute();

    $action_details = "Updated applicant ID #$applicant_id with new details: ";

    $changes = [];
    if ($old_first_name !== $first_name) $changes[] = "First Name: $first_name";
    if ($old_last_name !== $last_name) $changes[] = "Last Name: $last_name";
    if ($old_email !== $email) $changes[] = "Email: $email";
    if ($old_art_specialization !== $art_specialization) $changes[] = "Art Specialization: $art_specialization";
    if ($old_experience !== $experience) $changes[] = "Experience: $experience";
    if ($old_preferred_medium !== $preferred_medium) $changes[] = "Preferred Medium: $preferred_medium";
    if ($old_phone_number !== $phone_number) $changes[] = "Phone Number: $phone_number";

    $action_details .= implode(", ", $changes);

    logActivity($user_id, $username, 'edit', $action_details);

    header("Location: index.php?message=Applicant updated successfully.");
    exit();
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Applicant</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <h1>Edit Applicant</h1>

    <form action="edit.php?id=<?= $applicant['id']; ?>" method="POST">
        <label for="first_name">First Name:</label>
        <input type="text" name="first_name" value="<?= $applicant['first_name']; ?>" required><br>

        <label for="last_name">Last Name:</label>
        <input type="text" name="last_name" value="<?= $applicant['last_name']; ?>" required><br>

        <label for="email">Email:</label>
        <input type="email" name="email" value="<?= $applicant['email']; ?>" required><br>

        <label for="art_specialization">Art Specialization:</label>
        <input type="text" name="art_specialization" value="<?= $applicant['art_specialization']; ?>" required><br>

        <label for="experience">Experience (years):</label>
        <input type="number" name="experience" value="<?= $applicant['experience']; ?>" required><br>

        <label for="preferred_medium">Preferred Medium:</label>
        <input type="text" name="preferred_medium" value="<?= $applicant['preferred_medium']; ?>" required><br>

        <label for="phone_number">Phone Number:</label>
        <input type="text" name="phone_number" value="<?= $applicant['phone_number']; ?>" required><br>

        <input type="submit" value="Update Applicant">
    </form>
</body>

</html>