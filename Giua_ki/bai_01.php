<?php

// Khai báo tên và điểm của học sinh.
$name = "Phan Thanh Dũng";
$score = 9.0;

// CLASS 

class Student
{
    public string $name;
    public float $score;

    public function __construct(string $name, float $score)
    {
        $this->name = $name;
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
     * Hiển thị thông tin sinh viên
     */
    public function display(): void
    {
        echo "Họ tên: " . $this->name . "<br>";
        echo "Điểm: " . $this->score . "<br>";
        echo "Xếp loại: " . $this->getRank() . "<br>";
    }
}

$student = new Student($name, $score);
echo "<meta charset='UTF-8'>";
$student->display();





