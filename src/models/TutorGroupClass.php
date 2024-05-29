<?php
require_once __DIR__ . '/../config/Database.php';

class TutorGroupClass{
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->dbConnection();
    }

    public function addClass($title, $description, $first_day, $first_time, $second_day, $second_time, $number_of_classes, $duration, $tutor, $pricing, $cover_image_path) {
        try {
            $query = "INSERT INTO GroupClasses(title, description, first_day, first_time, second_day, second_time, number_of_classes, duration, tutor_id, pricing, cover_image_path, status) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Active')";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$title, $description, $first_day, $first_time, $second_day, $second_time, $number_of_classes, $duration, $tutor, $pricing, $cover_image_path]);
            
            return true;
        } catch(PDOException $e) {
            // Throw the exception again so the controller can catch it
            throw $e;
        }
    }

    public function getTutors() 
    {
        $query = "SELECT id, username, email FROM all_users WHERE role = 'Tutor'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCurrentTutors($tutorID) 
    {
        $query = "SELECT id, username, email FROM all_users WHERE id = ".$tutorID."";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function TutorGetAllGroupClasses() {
        try {
            // Access student_id from session
            $student_id = isset($_SESSION['student_id']) ? $_SESSION['student_id'] : null;
    
            $query = "SELECT GroupClasses.*, all_users.username AS tutor_name,
                      (SELECT COUNT(*) FROM GroupClassesEnrollments WHERE GroupClassesEnrollments.class_id = GroupClasses.class_id) AS enrolled_students_count,
                      (CASE WHEN EXISTS (SELECT 1 FROM GroupClassesEnrollments WHERE GroupClassesEnrollments.class_id = GroupClasses.class_id AND GroupClassesEnrollments.student_id = ?) THEN 1 ELSE 0 END) AS is_enrolled
                      FROM GroupClasses 
                      LEFT JOIN all_users ON GroupClasses.tutor_id = all_users.id
                      ORDER BY GroupClasses.class_id DESC";
    
            $stmt = $this->conn->prepare($query);
            
            // Bind the student_id
            $stmt->bindParam(1, $student_id, PDO::PARAM_INT);
    
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error fetching all group classes: " . $e->getMessage());
        }
    }

    public function getClassDetails($classId) {
        try {
            $query = "SELECT * FROM GroupClasses WHERE class_id = :classId";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':classId', $classId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error fetching class details: " . $e->getMessage());
        }
    }    
    
    public function TutorGetGroupClassById($class_id) {
        try {
            $query = "SELECT * FROM GroupClasses WHERE class_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$class_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    // public function updateGroupClass($class_id, $title, $description, $first_day, $first_time, $second_day, $second_time, $number_of_classes, $duration, $tutor, $pricing, $cover_image_path) 
    // {
    //     try {
    //         $query = "UPDATE GroupClasses SET 
    //                     title = ?, description = ?, first_day = ?, first_time = ?, 
    //                     second_day = ?, second_time = ?, number_of_classes = ?, 
    //                     duration = ?, tutor_id = ?, pricing = ?, cover_image_path = ?, status = 'Active'
    //                   WHERE class_id = ?";
    
    //         $stmt = $this->conn->prepare($query);
    //         $stmt->execute([$title, $description, $first_day, $first_time, $second_day, $second_time, $number_of_classes, $duration, $tutor, $pricing, $cover_image_path, $class_id]);
    
    //         return true;
    //     } catch(PDOException $e) {
    //         // Throw the exception again so the controller can catch it
    //         throw $e;
    //     }
    // }

    public function updateGroupClass($class_id, $title, $description, $first_day, $first_time, $second_day, $second_time, $number_of_classes, $duration, $tutor, $pricing, $cover_image_path) 
    {
        try {
            // Check if the tutor exists in the all_users table
            $queryCheckTutor = "SELECT COUNT(*) FROM all_users WHERE id = ?";
            $stmtCheckTutor = $this->conn->prepare($queryCheckTutor);
            $stmtCheckTutor->execute([$tutor]);
            $tutorExists = $stmtCheckTutor->fetchColumn();

            // Update the GroupClasses table conditionally based on tutor existence
            $query = "UPDATE GroupClasses SET 
                        title = ?, description = ?, first_day = ?, first_time = ?, 
                        second_day = ?, second_time = ?, number_of_classes = ?, 
                        duration = ?, tutor_id = IF(?, ?, NULL), pricing = ?, cover_image_path = ?, status = 'Active'
                    WHERE class_id = ?";

            $stmt = $this->conn->prepare($query);
            $stmt->execute([$title, $description, $first_day, $first_time, $second_day, $second_time, $number_of_classes, $duration, $tutorExists ? $tutor : null, $tutorExists ? $tutor : null, $pricing, $cover_image_path, $class_id]);

            return true;
        } catch(PDOException $e) {
            // Throw the exception again so the controller can catch it
            throw $e;
        }
    }


    public function draftClass($class_id) {
        try {
            $query = "UPDATE GroupClasses SET status = 'Draft' WHERE class_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$class_id]);
    
            return true;
        } catch(PDOException $e) {
            throw $e;
        }
    }
    
    public function deleteClass($class_id) {
        try {
            $query = "DELETE FROM GroupClasses WHERE class_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$class_id]);
    
            return true;
        } catch(PDOException $e) {
            throw $e;
        }
    }

    // public function enrollStudent($student_id, $class_id, $date_enrolled, $amount_paid, $status) {
    //     try {
    //         $query = "INSERT INTO GroupClassesEnrollments(student_id, class_id, date_enrolled, amount_paid, status) 
    //                   VALUES (?, ?, ?, ?, ?)";
            
    //         $stmt = $this->conn->prepare($query);
    //         $stmt->execute([$student_id, $class_id, $date_enrolled, $amount_paid, $status]);
            
    //         return true;
    //     } catch(PDOException $e) {
    //         return ['error' => $e->getMessage()];
    //     }
    // }

    public function enrollStudent($student_id, $class_id, $date_enrolled, $amount_paid, $status) {
        $this->conn->beginTransaction(); // Start a new transaction
    
        try {
            // Check if the student is already enrolled in the class
            $checkQuery = "SELECT COUNT(*) FROM GroupClassesEnrollments WHERE student_id = ? AND class_id = ?";
            $checkStmt = $this->conn->prepare($checkQuery);
            $checkStmt->execute([$student_id, $class_id]);
            $isEnrolled = $checkStmt->fetchColumn() > 0;
    
            if ($isEnrolled) {
                // If the student is already enrolled, handle this case
                $this->conn->rollBack();
                return ['error' => 'Student is already enrolled in this class'];
            }
    
            // Proceed with enrollment
            $query = "INSERT INTO GroupClassesEnrollments (student_id, class_id, date_enrolled, amount_paid, status) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$student_id, $class_id, $date_enrolled, $amount_paid, $status]); // Ensure $status is 'paid'
    
            // Commit the transaction
            $this->conn->commit();
            return ['success' => true, 'message' => 'Student enrolled successfully'];
        } catch(PDOException $e) {
            // Rollback the transaction in case of error
            $this->conn->rollBack();
            return ['error' => $e->getMessage()];
        }
    }

    public function getEnrolledStudentsByClassId($class_id) {
        try {
            // This SQL query joins the GroupClassesEnrollments table with the all_users table
            // to find the details of students enrolled in a class.
            $query = "
                SELECT 
                    CONCAT(u.first_name, ' ', u.last_name) AS full_name, 
                    u.username,
                    u.email,
                    u.country 
                FROM 
                    GroupClassesEnrollments e
                INNER JOIN 
                    all_users u ON e.student_id = u.id 
                WHERE 
                    e.class_id = ?
            ";

            $stmt = $this->conn->prepare($query);
            $stmt->execute([$class_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            // Optionally, you could log this error or handle it as per your requirements
            throw $e;
        }
    }

    public function getAllStudents() {
        $query = "SELECT id AS student_id, username, email FROM all_users WHERE role = 'Student'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    

    public function addZoomMeeting($class_id, $zoom_link, $scheduled_date, $scheduled_time) {
        try {
            $query = "INSERT INTO GroupClassesZoomMeetings(class_id, zoom_link, scheduled_date, scheduled_time) VALUES (?, ?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$class_id, $zoom_link, $scheduled_date, $scheduled_time]);
            return true;
        } catch(PDOException $e) {
            // Optionally, you could log this error or handle it as per your requirements
            throw $e;
        }
    }

    public function getZoomMeetingsByClassId($class_id) {
        try {
            $query = "SELECT *
                        FROM GroupClassesZoomMeetings AS gczm
                        JOIN GroupClasses AS gc ON gczm.class_id = gc.class_id
                        WHERE gczm.class_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$class_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            throw $e;
        }
    }
}
