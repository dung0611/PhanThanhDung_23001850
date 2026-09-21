<?php


$students = [
    ["name" => "Nguyen Van An", "age" => 20, "score" => 8.5],
    ["name" => "Tran Thi Binh", "age" => 21, "score" => 6.5],
    ["name" => "Le Van Cuong", "age" => 19, "score" => 4.5],
    ["name" => "Pham Thi Dung", "age" => 20, "score" => 7.5],
];

// FUNCTIONS 

// Tính điểm trung bình của danh sách sinh viên
 
function calculateAverageScore(array $students): float
{
    $total = 0;
    foreach ($students as $student) {
        $total += $student["score"];
    }
    return count($students) > 0 ? $total / count($students) : 0;
}


// Trả về xếp loại dựa trên điểm số

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

// Hiển thị thông tin một sinh viên (bao gồm xếp loại)

function displayStudent(array $student): void
{
    echo "Họ tên: " . $student["name"] . "<br>";
    echo "Tuổi: " . $student["age"] . "<br>";
    echo "Điểm: " . $student["score"] . "<br>";
    echo "Xếp loại: " . getRank($student["score"]) . "<br>";
    echo "-------------------------<br>";
}

// CODE CHÍNH 

foreach ($students as $student) {
    displayStudent($student);
}

$average = calculateAverageScore($students);
echo "Điểm trung bình của tất cả sinh viên: " . round($average, 2) . "<br>";