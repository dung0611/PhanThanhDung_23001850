<?php

$students = [
    ["name" => "Nguyen Van An", "age" => 20, "score" => 8.5],
    ["name" => "Tran Thi Binh", "age" => 21, "score" => 6.5],
    ["name" => "Le Van Cuong", "age" => 19, "score" => 4.5],
    ["name" => "Pham Thi Dung", "age" => 20, "score" => 7.5],
];

// FUNCTIONS  

function getRank(float $score): string
{
    if ($score >= 8) {
        return "Giỏi";
    } elseif ($score >= 6.5) {
        return "Khá";
    } elseif ($score >= 5) {
        return "Trung bình";
    } else {
        return "Yếu";
    }
}

function displayStudent(array $student): void
{
    echo "Họ tên: " . $student["name"] . "<br>";
    echo "Tuổi: " . $student["age"] . "<br>";
    echo "Điểm: " . $student["score"] . "<br>";
    echo "Xếp loại: " . getRank($student["score"]) . "<br>";
    echo "-------------------------<br>";
}


// Tìm sinh viên có điểm cao nhất
 
function findBestStudent(array $students): array
{
    $best = $students[0];
    foreach ($students as $student) {
        if ($student["score"] > $best["score"]) {
            $best = $student;
        }
    }
    return $best;
}

/**
 * Tìm sinh viên có điểm thấp nhất
 */
function findWorstStudent(array $students): array
{
    $worst = $students[0];
    foreach ($students as $student) {
        if ($student["score"] < $worst["score"]) {
            $worst = $student;
        }
    }
    return $worst;
}

/**
 * Đếm số sinh viên đạt (điểm >= 5)
 */
function countPassedStudents(array $students): int
{
    $count = 0;
    foreach ($students as $student) {
        if ($student["score"] >= 5) {
            $count++;
        }
    }
    return $count;
}

/**
 * Tìm sinh viên theo tên, trả về null nếu không tìm thấy
 */
function findStudentByName(array $students, string $name): ?array
{
    foreach ($students as $student) {
        if ($student["name"] === $name) {
            return $student;
        }
    }
    return null;
}

// CODE CHÍNH

echo "<b>Danh sách sinh viên:</b><br>";
foreach ($students as $student) {
    displayStudent($student);
}

echo "<br><b>Sinh viên điểm cao nhất:</b><br>";
displayStudent(findBestStudent($students));

echo "<br><b>Sinh viên điểm thấp nhất:</b><br>";
displayStudent(findWorstStudent($students));

echo "<br>Số sinh viên đạt (>= 5 điểm): " . countPassedStudents($students) . "<br>";

echo "<br><b>Tìm sinh viên theo tên 'Le Van Cuong':</b><br>";
$found = findStudentByName($students, "Le Van Cuong");
if ($found !== null) {
    displayStudent($found);
} else {
    echo "Không tìm thấy sinh viên.<br>";
}

echo "<br><b>Tìm sinh viên theo tên 'Nguyen Van Khong Ton Tai':</b><br>";
$notFound = findStudentByName($students, "Nguyen Van Khong Ton Tai");
if ($notFound !== null) {
    displayStudent($notFound);
} else {
    echo "Không tìm thấy sinh viên.<br>";
}