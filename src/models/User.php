<?php
require_once __DIR__ . '/../config/Database.php';

class User
{
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->dbConnection();
    }

    public function register($username, $firstName, $lastName, $email, $password, $role, $profilePicture = '')
    {
        $token = bin2hex(random_bytes(50)); // Generate a verification token
        $tokenExpiry = date("Y-m-d H:i:s", strtotime("+15 minutes")); // Token expiry time

        $stmt = $this->conn->prepare("
            INSERT INTO all_users (
                username, 
                first_name, 
                last_name, 
                email, 
                password, 
                role, 
                signup_date, 
                last_login_date,
                token,
                token_expiry,
                email_verified,
                mobile_no,
                country,
                language_and_education_level,
                languages_spoken,
                education_experience,
                native_language,
                working_with,
                levels_you_teach,
                cv_filepath,
                profile_photo_filepath,
                official_id_filepath,
                video_introduction_link
            ) VALUES (
                :username, 
                :firstName, 
                :lastName, 
                :email, 
                :password, 
                :role, 
                CURDATE(), 
                CURDATE(),
                :token,
                :tokenExpiry,
                0,
                NULL,
                NULL,
                NULL,
                NULL,
                NULL,
                NULL,
                NULL,
                NULL,
                NULL,
                :profilePicture,
                NULL,
                NULL
            )
        ");

        $stmt->bindparam(":username", $username);
        $stmt->bindparam(":firstName", $firstName);
        $stmt->bindparam(":lastName", $lastName);
        $stmt->bindparam(":email", $email);
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt->bindparam(":password", $hashedPassword);
        $stmt->bindparam(":role", $role);
        $stmt->bindparam(":profilePicture", $profilePicture);
        $stmt->bindparam(":token", $token);
        $stmt->bindparam(":tokenExpiry", $tokenExpiry);

        $stmt->execute();
        return ['id' => $this->conn->lastInsertId(), 'token' => $token];
    }

    /****************** Gmail Login ***********************/ 
    public function UserLogin($username, $firstName, $lastName, $email, $password, $role, $email_verified, $profilePicture = '')
    {
        $token = bin2hex(random_bytes(50)); // Generate a verification token
        $tokenExpiry = date("Y-m-d H:i:s", strtotime("+15 minutes")); // Token expiry time

        $stmt = $this->conn->prepare("
            INSERT INTO all_users (
                username, 
                first_name, 
                last_name, 
                email, 
                password, 
                role, 
                signup_date, 
                last_login_date,
                token,
                token_expiry,
                email_verified,
                mobile_no,
                country,
                language_and_education_level,
                languages_spoken,
                education_experience,
                native_language,
                working_with,
                levels_you_teach,
                cv_filepath,
                profile_photo_filepath,
                official_id_filepath,
                video_introduction_link
            ) VALUES (
                :username, 
                :firstName, 
                :lastName, 
                :email, 
                :password, 
                :role, 
                CURDATE(), 
                CURDATE(),
                :token,
                :tokenExpiry,
                :email_verified,
                NULL,
                NULL,
                NULL,
                NULL,
                NULL,
                NULL,
                NULL,
                NULL,
                NULL,
                :profilePicture,
                NULL,
                NULL
            )
        ");

        $stmt->bindparam(":username", $username);
        $stmt->bindparam(":firstName", $firstName);
        $stmt->bindparam(":lastName", $lastName);
        $stmt->bindparam(":email", $email);
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt->bindparam(":password", $hashedPassword);
        $stmt->bindparam(":role", $role);
        $stmt->bindparam(":profilePicture", $profilePicture);
        $stmt->bindparam(":token", $token);
        $stmt->bindparam(":tokenExpiry", $tokenExpiry);
        $stmt->bindparam(":email_verified", $email_verified);

        $stmt->execute();
        return ['id' => $this->conn->lastInsertId(), 'token' => $token];
    }

    public function usernameExists($username)
    {
        $stmt = $this->conn->prepare("SELECT 1 FROM all_users WHERE Username = :username");
        $stmt->execute([':username' => $username]);

        return $stmt->fetchColumn();
    }

    public function emailExists($email)
    {
        $stmt = $this->conn->prepare("SELECT 1 FROM all_users WHERE Email = :email");
        $stmt->execute([':email' => $email]);

        return $stmt->fetchColumn();
    }

    public function verifyPassword($username, $password)
    {
        $stmt = $this->conn->prepare("SELECT Password FROM all_users WHERE Username = :username");
        $stmt->execute([':username' => $username]);

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return password_verify($password, $row['Password']);
        } else {
            return false;
        }
    }

    public function isEmailVerified($username)
    {
        $stmt = $this->conn->prepare("SELECT email_verified FROM all_users WHERE Username = :username");
        $stmt->execute([':username' => $username]);

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['email_verified'];
        } else {
            return false;
        }
    }

    public function getUserRole($username)
    {
        $stmt = $this->conn->prepare("SELECT Role FROM all_users WHERE Username = :username");
        $stmt->execute([':username' => $username]);

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['Role'];
        } else {
            return null;
        }
    }

    public function profileApproved($username)
    {
        $stmt = $this->conn->prepare("SELECT profile_approved FROM all_users WHERE username = :username");
        $stmt->execute([':username' => $username]);

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['profile_approved']; // return the actual value from the database
        } else {
            return null; // return null if no such user exists
        }
    }

    public function getId($username)
    {
        $stmt = $this->conn->prepare("SELECT id FROM all_users WHERE Username = :username");
        $stmt->execute([':username' => $username]);

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['id'];
        } else {
            return null;
        }
    }

    public function getUserData($username)
    {
        $stmt = $this->conn->prepare("
            SELECT
                id,
                username,
                first_name,
                last_name,
                email,
                mobile_no,
                country,
                languages_spoken,
                language_and_education_level,
                profile_photo_filepath,
                education_experience,	
                native_language,
                working_with,
                levels_you_teach,	
                cv_filepath,	
                official_id_filepath,
                video_introduction_link
            FROM 
                all_users
            WHERE 
                username = :username
        ");

        $stmt->bindparam(":username", $username);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row;
        } else {
            return null;
        }
    }

    public function updateProfile($username, $firstName, $lastName, $email, $mobileNo, $country, $languagesSpoken, $languageAndEducationLevel, $profilePicture = '')
    {
        $stmt = $this->conn->prepare("
            UPDATE all_users SET
                first_name = :firstName,
                last_name = :lastName,
                email = :email,
                mobile_no = :mobileNo,
                country = :country,
                languages_spoken = :languagesSpoken,
                language_and_education_level = :languageAndEducationLevel,
                profile_photo_filepath = :profilePicture
            WHERE
                username = :username
        ");

        $stmt->bindparam(":username", $username);
        $stmt->bindparam(":firstName", $firstName);
        $stmt->bindparam(":lastName", $lastName);
        $stmt->bindparam(":email", $email);
        $stmt->bindparam(":mobileNo", $mobileNo);
        $stmt->bindparam(":country", $country);
        $stmt->bindparam(":languagesSpoken", $languagesSpoken);
        $stmt->bindparam(":languageAndEducationLevel", $languageAndEducationLevel);
        $stmt->bindparam(":profilePicture", $profilePicture);

        $stmt->execute();
        return true;
    }

    public function updateTutorProfile(
        $username,
        $firstName,
        $lastName,
        $email,
        $mobileNo,
        $country,
        $languagesSpoken,
        $nativeLanguage,
        $workingWith,
        $levelsYouTeach,
        $educationExperience,
        $videoIntroduction,
        $profilePicture,
        $cv,
        $officialID,
        $profileApprovedValue
    ) {
        try {
            $stmt = $this->conn->prepare(
                "
                UPDATE 
                    all_users
                SET
                    first_name = :firstName,
                    last_name = :lastName,
                    email = :email,
                    mobile_no = :mobileNo,
                    country = :country,
                    languages_spoken = :languagesSpoken,
                    native_language = :nativeLanguage,
                    working_with = :workingWith,
                    levels_you_teach = :levelsYouTeach,
                    education_experience = :educationExperience,
                    video_introduction_link = :videoIntroduction," .
                    ($profilePicture != "" ? " profile_photo_filepath = :profilePicture," : "") .
                    ($cv != "" ? " cv_filepath = :cv," : "") .
                    ($officialID != "" ? " official_id_filepath = :officialID," : "") .
                    " profile_approved = :profileApprovedValue
                WHERE 
                    username = :username
            "
            );
    
            $stmt->bindparam(":firstName", $firstName);
            $stmt->bindparam(":lastName", $lastName);
            $stmt->bindparam(":email", $email);
            $stmt->bindparam(":mobileNo", $mobileNo);
            $stmt->bindparam(":country", $country);
            $stmt->bindparam(":languagesSpoken", $languagesSpoken);
            $stmt->bindparam(":nativeLanguage", $nativeLanguage);
            $stmt->bindparam(":workingWith", $workingWith);
            $stmt->bindparam(":levelsYouTeach", $levelsYouTeach);
            $stmt->bindparam(":educationExperience", $educationExperience);
            $stmt->bindparam(":videoIntroduction", $videoIntroduction);
            $stmt->bindparam(":username", $username);
    
            if ($profilePicture != "") {
                $stmt->bindparam(":profilePicture", $profilePicture);
            }
            if ($cv != "") {
                $stmt->bindparam(":cv", $cv);
            }
            if ($officialID != "") {
                $stmt->bindparam(":officialID", $officialID);
            }
            $stmt->bindparam(":profileApprovedValue", $profileApprovedValue);
            $stmt->execute();
    
            return true;
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }
    

    public function getTutorsData($language = null)
    {
        $query = "
            SELECT 
                a.username,
                first_name,
                last_name,
                email,
                mobile_no,
                country,
                languages_spoken,
                language_and_education_level,
                profile_photo_filepath,
                education_experience,    
                native_language,
                working_with,
                levels_you_teach,  
                cv_filepath, 
                official_id_filepath,
                video_introduction_link,
                AVG(b.star_rating) as average_rating
            FROM 
                all_users a
            LEFT JOIN
                bookings b ON a.username = b.tutor_username
            WHERE 
                Role = 'Tutor' AND a.profile_approved = 1
        ";

        if ($language !== null) {
            if ($language == 'other') {
                $query .= " AND languages_spoken NOT LIKE '%english%' AND languages_spoken NOT LIKE '%french%' AND languages_spoken NOT LIKE '%spanish%'";
            } else {
                $query .= " AND languages_spoken LIKE :language";
            }
        }

        $query .= " GROUP BY a.username";

        // Order tutors with profile pictures first
        $query .= " ORDER BY CASE WHEN profile_photo_filepath IS NOT NULL AND profile_photo_filepath != '' THEN 1 ELSE 0 END DESC";

        $stmt = $this->conn->prepare($query);

        if ($language !== null && $language != 'other') {
            $language = '%' . $language . '%';
            $stmt->bindParam(':language', $language);
        }

        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $tutors = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Further refine the sorting in PHP
            usort($tutors, function ($a, $b) {
                // Check profile completeness and education experience
                $a_completeness = strlen($a['languages_spoken'] . $a['native_language'] . $a['levels_you_teach'] . $a['education_experience']);
                $b_completeness = strlen($b['languages_spoken'] . $b['native_language'] . $b['levels_you_teach'] . $b['education_experience']);

                if ($a_completeness > $b_completeness) return -1;
                if ($a_completeness < $b_completeness) return 1;

                // Compare average ratings if profile completeness is equal
                return $b['average_rating'] <=> $a['average_rating']; // Higher ratings come first
            });

            return $tutors;
        } else {
            return [];
        }
    }

    public function getTutorByUsername($username)
        {
        // Prepare the SQL statement to get basic tutor details
        $stmt = $this->conn->prepare("
            SELECT 
                username,
                first_name,
                last_name,
                email,
                mobile_no,
                country,
                languages_spoken,
                language_and_education_level,
                profile_photo_filepath,
                education_experience,    
                native_language,
                working_with,
                levels_you_teach,  
                cv_filepath, 
                official_id_filepath,
                video_introduction_link
            FROM 
                all_users
            WHERE 
                Role = 'Tutor' AND username = :username
        ");

        // Bind the username parameter
        $stmt->bindParam(':username', $username);

        // Execute the statement
        $stmt->execute();

        // Fetch the tutor's basic details
        $tutor = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($tutor === false) {
            return null;
        }

        // Fetch the average rating for the tutor
        $stmtAvgRating = $this->conn->prepare("
            SELECT 
                AVG(star_rating) as average_rating
            FROM 
                bookings
            WHERE 
                tutor_username = :username
        ");

        $stmtAvgRating->bindParam(':username', $username);
        $stmtAvgRating->execute();
        $avgRatingResult = $stmtAvgRating->fetch(PDO::FETCH_ASSOC);

        $tutor['average_rating'] = $avgRatingResult['average_rating'] ?? 0;

        // Fetch the individual reviews for the tutor
        $stmtReviews = $this->conn->prepare("
        SELECT 
            review,
            star_rating,
            username as student_username,
            review_date
        FROM 
            bookings
        WHERE 
            tutor_username = :username AND
            star_rating IS NOT NULL
        ORDER BY 
            review_date DESC
        ");


        $stmtReviews->bindParam(':username', $username);
        $stmtReviews->execute();
        $reviews = $stmtReviews->fetchAll(PDO::FETCH_ASSOC);

        $tutor['reviews'] = $reviews;

        return $tutor;
        }

        // GET TUTOr REVIEWS

        public function getTutorReviewsByUsername($username)
    {
        // Initialize the array to store results
        $result = [];

        // Fetch the average rating for the tutor
        $stmtAvgRating = $this->conn->prepare("
            SELECT 
                AVG(star_rating) as average_rating
            FROM 
                bookings
            WHERE 
                tutor_username = :username
        ");

        $stmtAvgRating->bindParam(':username', $username);
        $stmtAvgRating->execute();
        $avgRatingResult = $stmtAvgRating->fetch(PDO::FETCH_ASSOC);

        $result['average_rating'] = $avgRatingResult['average_rating'] ?? 0;

        // Fetch the individual reviews for the tutor
        $stmtReviews = $this->conn->prepare("
            SELECT 
                review,
                star_rating,
                username as student_username,
                review_date
            FROM 
                bookings
            WHERE 
                tutor_username = :username AND
                star_rating IS NOT NULL
            ORDER BY 
                review_date DESC
        ");

        $stmtReviews->bindParam(':username', $username);
        $stmtReviews->execute();
        $reviews = $stmtReviews->fetchAll(PDO::FETCH_ASSOC);

        $result['reviews'] = $reviews;

        return $result;
    }



    // EMAIL VERIFICATION

    public function getUserByUsername($username)
    {
        $stmt = $this->conn->prepare("SELECT * FROM all_users WHERE username = :username");
        $stmt->execute([':username' => $username]);
        return $stmt->fetch();
    }

    public function updateUserTokenAndVerification($id)
    {
        $stmt = $this->conn->prepare("UPDATE all_users SET token = NULL, email_verified = 1 WHERE id = :id");
        $stmt->execute([':id' => $id]);
    }

    public function updateUserTokenAndExpiry($id, $newToken, $newExpiry)
    {
        $stmt = $this->conn->prepare("UPDATE all_users SET token = :token, token_expiry = :expiry WHERE id = :id");
        $stmt->execute([':id' => $id, ':token' => $newToken, ':expiry' => $newExpiry]);
    }

    public function getUserDetails($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM all_users WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function getUserRoleByEmail($email)
    {
        $stmt = $this->conn->prepare("SELECT Role FROM all_users WHERE Email = :email");
        $stmt->execute([':email' => $email]);

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['Role'];
        } else {
            return null;
        }
    }
    public function getIdByEmail($email)
    {
        $stmt = $this->conn->prepare("SELECT id FROM all_users WHERE Email = :email");
        $stmt->execute([':email' => $email]);

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['id'];
        } else {
            return null;
        }
    }

    // Password Reset

    public function generateResetToken($email)
    {
        $token = bin2hex(random_bytes(50));
        $expiry = date("Y-m-d H:i:s", strtotime('+1 hour'));

        $sql = "UPDATE all_users SET reset_token = :reset_token, reset_token_expiry = :reset_token_expiry WHERE email = :email";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['reset_token' => $token, 'reset_token_expiry' => $expiry, 'email' => $email]);

        if ($stmt->rowCount() > 0) {
            return $token;
        } else {
            return false;
        }
    }

    public function getEmailFromToken($token)
    {
        $sql = "SELECT email FROM all_users WHERE reset_token = :reset_token AND reset_token_expiry > NOW()";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['reset_token' => $token]);
        $user = $stmt->fetch();

        if ($user) {
            return $user['email'];
        }

        return null;
    }


    public function resetPassword($token, $password)
    {
        $sql = "SELECT * FROM all_users WHERE reset_token = :reset_token AND reset_token_expiry > NOW()";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['reset_token' => $token]);
        $user = $stmt->fetch();

        if ($user) {
            $newPasswordHash = password_hash($password, PASSWORD_DEFAULT);
            $sql = "UPDATE all_users SET password = :password, reset_token = NULL, reset_token_expiry = NULL WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute(['password' => $newPasswordHash, 'id' => $user['id']]);
        }

        return false;
    }

    public function invalidateToken($token)
    {
        $sql = "UPDATE all_users SET reset_token = NULL, reset_token_expiry = NULL WHERE reset_token = :reset_token";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute(['reset_token' => $token]);
    }

    public function isAccountActive($username) {
        $sql = "SELECT disable_account FROM all_users WHERE username = :username";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if($result && $result['disable_account'] == 0) {
            return true; // Account is active
        }
        return false; // Account is suspended or deactivated
    }
    

}

?>
