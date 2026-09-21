<?php

// CLASS 

class Student
{
    public string $name;
    public int $age;
    public float $score;

    public function __construct(string $name, int $age, float $score)
    {
        $this->name = $name;
        $this->age = $age;
        $this->score = $score;
    }

    /**
     * Trả về xếp loại dựa trên điểm số
     */
    public function getRank(): string
    {
        if ($this->score >= 8) {
            return "Giỏi";
        } elseif ($this->score >= 6.5) {
            return "Khá";
        } elseif ($this->score >= 5) {
            return "Trung bình";
        } else {
            return "Yếu";
        }
    }

    /**
     * Kiểm tra sinh viên đạt hay không (điểm >= 5)
     */
    public function isPassed(): bool
    {
        return $this->score >= 5;
    }

    /**
     * Hiển thị thông tin sinh viên
     */
    public function display(): void
    {
        echo "Họ tên: " . $this->name . "<br>";
        echo "Tuổi: " . $this->age . "<br>";
        echo "Điểm: " . $this->score . "<br>";
        echo "Xếp loại: " . $this->getRank() . "<br>";
        echo "Trạng thái: " . ($this->isPassed() ? "Đạt" : "Không đạt") . "<br>";
        echo "-------------------------<br>";
    }
}

// FUNCTIONS 

/**
 * Tìm sinh viên có điểm cao nhất trong danh sách object Student
 */
function findBestStudent(array $students): Student
{
    $best = $students[0];
    foreach ($students as $student) {
        if ($student->score > $best->score) {
            $best = $student;
        }
    }
    return $best;
}

/**
 * Đếm số sinh viên đạt trong danh sách object Student
 */
function countPassedStudents(array $students): int
{
    $count = 0;
    foreach ($students as $student) {
        if ($student->isPassed()) {
            $count++;
        }
    }
    return $count;
}

/**
 * Tính điểm trung bình của danh sách object Student
 */
function calculateAverageScore(array $students): float
{
    $total = 0;
    foreach ($students as $student) {
        $total += $student->score;
    }
    return count($students) > 0 ? $total / count($students) : 0;
}

// CODE CHÍNH 

$student1 = new Student("Nguyen Van An", 20, 8.5);
$student2 = new Student("Tran Thi Binh", 21, 6.5);
$student3 = new Student("Le Van Cuong", 19, 4.5);
$student4 = new Student("Pham Thi Dung", 20, 7.5);

$students = [$student1, $student2, $student3, $student4];

echo "<b>Danh sách sinh viên:</b><br>";
foreach ($students as $student) {
    $student->display();
}

echo "<br><b>Sinh viên điểm cao nhất:</b><br>";
findBestStudent($students)->display();

echo "<br>Số sinh viên đạt: " . countPassedStudents($students) . "<br>";

$average = calculateAverageScore($students);
echo "Điểm trung bình của lớp: " . round($average, 2) . "<br>";