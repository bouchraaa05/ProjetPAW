<?php
// === Display all errors for testing ===
error_reporting(E_ALL);
ini_set("display_errors", 1);

// === Load students ===
$studentsFile = "students.json";
if (!file_exists($studentsFile)) {
    echo "No students found. Please add students first.";
    exit;
}

$students = json_decode(file_get_contents($studentsFile), true);
if (!is_array($students)) {
    echo "Invalid students data.";
    exit;
}

// === Attendance file for today ===
$today = date("Y-m-d");
$attendanceFile = "attendance_{$today}.json";
$message = "";

// === Handle form submission ===
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (file_exists($attendanceFile)) {
        $message = "Attendance for today has already been taken.";
    } else {
        $attendance = [];
        foreach ($students as $student) {
            $id = $student['id'];
            $status = $_POST["attendance_$id"] ?? "absent";
            $attendance[] = [
                "student_id" => $id,
                "status" => $status
            ];
        }
        file_put_contents($attendanceFile, json_encode($attendance, JSON_PRETTY_PRINT));
        $message = "Attendance successfully saved for today!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Take Attendance - <?php echo $today; ?></title>
<style>
body { font-family: Arial, sans-serif; margin: 30px; }
h2 { color: #1e3a8a; }
table { border-collapse: collapse; width: 70%; margin-top: 20px; }
th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
th { background-color: #3b82f6; color: #fff; }
button { margin-top: 20px; padding: 10px 20px; background-color: #1e3a8a; color: white; border: none; cursor: pointer; border-radius: 5px; }
button:hover { background-color: #2563eb; }
.message { margin-top: 20px; font-weight: bold; color: green; }
</style>
</head>
<body>

<h2>Take Attendance - <?php echo $today; ?></h2>

<?php if ($message) { ?>
    <p class="message"><?php echo $message; ?></p>
<?php } ?>

<?php if (!file_exists($attendanceFile)) { ?>
<form method="post" action="">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Last Name</th>
                <th>First Name</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($students as $student) { ?>
            <tr>
                <td><?php echo htmlspecialchars($student['id']); ?></td>
                <td><?php echo htmlspecialchars($student['lastname']); ?></td>
                <td><?php echo htmlspecialchars($student['firstname']); ?></td>
                <td>
                    <label>
                        <input type="radio" name="attendance_<?php echo $student['id']; ?>" value="present" checked> Present
                    </label>
                    <label>
                        <input type="radio" name="attendance_<?php echo $student['id']; ?>" value="absent"> Absent
                    </label>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    <button type="submit">Submit Attendance</button>
</form>
<?php } ?>

</body>
</html>
