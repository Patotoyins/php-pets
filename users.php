<?php
include "headers.php";


class User
{
  // function login($json)
  // {
  //   // {"username":"admin","password":"admin"}
  //   include "connection.php";
  //   $json = json_decode($json, true);
  //   $sql = "SELECT * FROM tbl_personal_information WHERE email = :username  AND BINARY personal_password = :password";
  //   $stmt = $conn->prepare($sql);
  //   $stmt->bindParam(':username', $json['username']);
  //   $stmt->bindParam(':password', $json['password']);
  //   $stmt->execute();
  //   return $stmt->rowCount() > 0 ? json_encode($stmt->fetch(PDO::FETCH_ASSOC)) : 0;
  // }

  function login($json)
{
    include "connection.php";
    $json = json_decode($json, true);


    $sql = "SELECT * FROM tbladmin WHERE adm_email = :username AND BINARY adm_password = :password";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':username', $json['username']);
    $stmt->bindParam(':password', $json['password']);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return json_encode([
            'adm_id' => $user['adm_id'],
            // 'user_level_id' => $user['user_level_id'],
            'adm_user_level' => $user['adm_user_level'],
            'adm_name' => $user['adm_name'],
            'adm_email' => $user['adm_email']
        ]);
    }


    $sql = "SELECT * FROM tblsupervisor WHERE sup_email = :username AND BINARY sup_password = :password";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':username', $json['username']);
    $stmt->bindParam(':password', $json['password']);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return json_encode([
            'sup_id' => $user['sup_id'],
            'sup_user_level' => $user['sup_user_level'],
            'sup_name' => $user['sup_name'],
            'sup_email' => $user['sup_email']
        ]);
    }


    $sql = "SELECT * FROM tblcandidates WHERE cand_email = :username AND BINARY cand_password = :password";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':username', $json['username']);
    $stmt->bindParam(':password', $json['password']);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return json_encode([
            'cand_id' => $user['cand_id'],
            'cand_firstname' => $user['cand_firstname'],
            'cand_lastname' => $user['cand_lastname'],
            'cand_email' => $user['cand_email'],
            'user_level_id' => 'applicant'
        ]);
    }

    return json_encode(null);
}





function signup($json)
{
  // example data
  // {"personalInfo":{"firstName":"Mel Angelo","lastName":"Macario","middleName":"Sabido","email":"xmoonlightboy@gmail.com","alternateEmail":"xmoonlightboy@gmail.com","contact":"09676887868","alternateContact":"09676887868","presentAddress":"balay","permanentAddress":"admin","gender":"Female","dob":"2024-08-07","sss":"123","tin":"123","philhealth":"123","pagibig":"123","password":"melmacs","confirmPassword":"admin"},
  // "educationalBackground":[{"institution":1,"course":32,"courseDateGraduated":"2024-07-31","graduateCourse":3,"graduateCourseDate":"2024-08-13","prcLicense":"License mo to","prcLicenseNumber":"123123"},{"institution":9,"course":33,"courseDateGraduated":"2024-08-01","graduateCourse":2,"graduateCourseDate":"2024-08-15","prcLicense":"License mo to","prcLicenseNumber":"123123"},{"institution":2,"course":34,"courseDateGraduated":"2024-08-03","graduateCourse":1,"graduateCourseDate":"2001-03-14","prcLicense":"License mo to","prcLicenseNumber":"123123"}],
  // "employmentHistory":[{"position":"123","company":"123","startDate":"2024-08-07","endDate":"2024-08-15"},{"position":"asd","company":"qwe","startDate":"2024-08-08","endDate":"2024-08-15"},{"position":"123","company":"qweqwe","startDate":"2024-08-01","endDate":"2024-08-19"}],
  // "skills":[{"skills":2},{"skills":5},{"skills":1},{"skills":7},{"skills":3}],
  // "trainings":[{"training":1},{"training":4},{"training":2},{"training":3}],
  // "positionId": "1",
  // "isSubscribeToEmail": 1}
  include "connection.php";
  $conn->beginTransaction();
  try {
    $json = json_decode($json, true);
    $personalInformation = $json['personalInfo'];
    $educationalBackground = $json['educationalBackground'];
    $employmentHistory = $json['employmentHistory'];
    $skills = $json['skills'];
    $trainings = $json['trainings'];
    $createdDateTime = getCurrentDate();

    $sql = "INSERT INTO tblcandidates(cand_lastname, cand_firstname, cand_middlename, cand_contactNo,
            cand_alternateContactNo, cand_email, cand_alternateEmail, cand_presentAddress,
            cand_permanentAddress, cand_dateofBirth, cand_sex, cand_sssNo, cand_tinNo,
            cand_philhealthNo, cand_pagibigNo, cand_password, cand_createdDatetime)
            VALUES(:last_name, :first_name, :middle_name, :contact_number, :alternate_contact_number,
            :email, :alternate_email, :present_address, :permanent_address, :date_of_birth,
            :sex, :sss_number, :tin_number, :philhealth_number, :pagibig_number,
            :personal_password, :created_datetime)";
    $stmt = $conn->prepare($sql);

    $stmt->bindParam(':last_name', $personalInformation['lastName']);
    $stmt->bindParam(':first_name', $personalInformation['firstName']);
    $stmt->bindParam(':middle_name', $personalInformation['middleName']);
    $stmt->bindParam(':contact_number', $personalInformation['contact']);
    $stmt->bindParam(':alternate_contact_number', $personalInformation['alternateContact']);
    $stmt->bindParam(':email', $personalInformation['email']);
    $stmt->bindParam(':alternate_email', $personalInformation['alternateEmail']);
    $stmt->bindParam(':present_address', $personalInformation['presentAddress']);
    $stmt->bindParam(':permanent_address', $personalInformation['permanentAddress']);
    $stmt->bindParam(':date_of_birth', $personalInformation['dob']);
    $stmt->bindParam(':sex', $personalInformation['gender']);
    $stmt->bindParam(':sss_number', $personalInformation['sss']);
    $stmt->bindParam(':tin_number', $personalInformation['tin']);
    $stmt->bindParam(':philhealth_number', $personalInformation['philhealth']);
    $stmt->bindParam(':pagibig_number', $personalInformation['pagibig']);
    $stmt->bindParam(':personal_password', $personalInformation['password']);
    $stmt->bindParam(':created_datetime', $createdDateTime);
    $stmt->execute();
    $newId = $conn->lastInsertId();
    if ($stmt->rowCount() > 0) {
      $sql = "INSERT INTO tbleducbackground (
              educ_personalId, educ_coursesId, educ_course_graduated, educ_coursegradId,
              educ_institutionId, educ_dategraduate, 	educ_prcCert, educ_prcLicenseNo)
              VALUES (:personal_info_id, :courses_id, :course_date_graduated, :course_graduate_id,
              :institution_id, :date_of_graduation, :prc_license, :prc_license_number)";
      foreach ($educationalBackground as $item) {
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':personal_info_id', $newId);
        $stmt->bindParam(':courses_id', $item['course']);
        $stmt->bindParam(':course_date_graduated', $item['courseDateGraduated']);
        $stmt->bindParam(':course_graduate_id', $item['graduateCourse']);
        $stmt->bindParam(':institution_id', $item['institution']);
        $stmt->bindParam(':date_of_graduation', $item['graduateCourseDate']);
        $stmt->bindParam(':prc_license', $item['prcLicense']);
        $stmt->bindParam(':prc_license_number', $item['prcLicenseNumber']);
        $stmt->execute();
      }
      if ($stmt->rowCount() > 0) {
        $sql = "INSERT INTO tblemploymenthistory(empH_candId , empH_positionName, empH_companyName,
                empH_startDate, empH_endDate) VALUES (:personal_info_id, :employment_position_name,
                :employment_company_name, :employment_start_date, :employment_end_date)";
        foreach ($employmentHistory as $item) {
          $stmt = $conn->prepare($sql);
          $stmt->bindParam(':personal_info_id', $newId);
          $stmt->bindParam(':employment_position_name', $item['position']);
          $stmt->bindParam(':employment_company_name', $item['company']);
          $stmt->bindParam(':employment_start_date', $item['startDate']);
          $stmt->bindParam(':employment_end_date', $item['endDate']);
          $stmt->execute();
        }

        if ($stmt->rowCount() > 0) {
          $sql = "INSERT INTO tbltraining (training_candId , training_perTId )
                  VALUES (:personal_info_id, :personal_training_id)";
          foreach ($trainings as $item) {
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':personal_info_id', $newId);
            $stmt->bindParam(':personal_training_id', $item['training']);
            $stmt->execute();
          }

          if ($stmt->rowCount() > 0) {
            $sql = "INSERT INTO tblskills(skills_candId , skills_perSId)
                    VALUES (:personal_info_id, :personal_skill_id)";
            foreach ($skills as $item) {
              $stmt = $conn->prepare($sql);
              $stmt->bindParam(':personal_info_id', $newId);
              $stmt->bindParam(':personal_skill_id', $item['skills']);
              $stmt->execute();
            }
            if ($stmt->rowCount() > 0) {
              $sql = "INSERT INTO tblpositionapplied(posA_candId, posA_jobMId, posA_datetime)
                      VALUES (:personal_info_id, :apply_position_id, :created_datetime)";
              $stmt = $conn->prepare($sql);
              $stmt->bindParam(':personal_info_id', $newId);
              $stmt->bindParam(':apply_position_id', $json['positionId']);
              $stmt->bindParam(':created_datetime', $createdDateTime);
              $stmt->execute();
              if ($stmt->rowCount() > 0) {
                $sql = "INSERT INTO tblconsent(cons_candId , cons_subscribetoemailupdates)
                    VALUES (:personal_info_id, :subscribe_to_email_updates)";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':personal_info_id', $newId);
                $stmt->bindParam(':subscribe_to_email_updates', $json['isSubscribeToEmail']);
                $stmt->execute();
                if ($stmt->rowCount() > 0) {
                  $conn->commit();
                  return 1;
                }
              }
            }
          }
        }
      }
    }
  } catch (PDOException $th) {
    $conn->rollBack();
    return $th;
  }
}

function getInstitution()
{
  include "connection.php";
  $sql = "SELECT * FROM tblinstitution";
  $stmt = $conn->prepare($sql);
  $stmt->execute();
  return $stmt->rowCount() > 0 ? json_encode($stmt->fetchAll(PDO::FETCH_ASSOC)) : 0;
}

function getCourses()
{
  include "connection.php";
  $sql = "SELECT * FROM tblcourses";
  $stmt = $conn->prepare($sql);
  $stmt->execute();
  return $stmt->rowCount() > 0 ? json_encode($stmt->fetchAll(PDO::FETCH_ASSOC)) : 0;
}

function getCourseGraduate()
{
  include "connection.php";
  $sql = "SELECT * FROM tblcoursesgraduate";
  $stmt = $conn->prepare($sql);
  $stmt->execute();
  return $stmt->rowCount() > 0 ? json_encode($stmt->fetchAll(PDO::FETCH_ASSOC)) : 0;
}

function getPinCode($json)
{
  // {"email": "qkyusans@gmail"}
  include "connection.php";
  include "send_email.php";

  $data = json_decode($json, true);
  if (recordExists($data['email'], "tblcandidates", "email")) return -1;

  $firstLetter = strtoupper(substr($data['email'], 0, 1));
  $thirdLetter = strtoupper(substr($data['email'], 2, 1));
  $pincode = $firstLetter . rand(100, 999) . $thirdLetter . rand(10000, 99999);

  $currentDateTime = new DateTime("now", new DateTimeZone('Asia/Manila'));
  $expirationDateTime = $currentDateTime->add(new DateInterval('PT15M'));
  $expirationTimestamp = $expirationDateTime->format('Y-m-d H:i:s');

  // $sql = "INSERT INTO tbl_pincode (pin_email, pin_code, pin_expiration_date) VALUES (:email, :pincode, :pin_expiration_date)";
  // $stmt = $conn->prepare($sql);
  // $stmt->bindParam(':email', $data['email']);
  // $stmt->bindParam(':pincode', $pincode);
  // $stmt->bindParam(':pin_expiration_date', $expirationTimestamp);
  // $stmt->execute();
  $sendEmail = new SendEmail();
  $sendEmail->sendEmail($data['email'], $pincode . " - Your PIN Code", "Please use the following code to complete the first step:<br /><br /> <b>$pincode</b>");

  return json_encode(["pincode" => $pincode, "expirationDate" => $expirationTimestamp]);
}
function getSkills()
{
  include "connection.php";
  $sql = "SELECT * FROM tblpersonalskills";
  $stmt = $conn->prepare($sql);
  $stmt->execute();
  return $stmt->rowCount() > 0 ? json_encode($stmt->fetchAll(PDO::FETCH_ASSOC)) : 0;
}

function isEmailExist($json)
{
  // {"email": "qkyusans@gmail"}
  include "connection.php";
  $data = json_decode($json, true);
  if (recordExists($data['email'], "tblcandidates", "email")) {
    return -1;
  } else {
    return 1;
  }
}

function getAllDataForDropdownSignup()
{
  include "connection.php";
  $conn->beginTransaction();
  try {
    $data = [];

    $sql = "SELECT * FROM tblinstitution";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $data['institution'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $sql = "SELECT * FROM tblcourses";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $data['courses'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $sql = "SELECT * FROM tblcoursesgraduate";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $data['courseGraduate'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $sql = "SELECT * FROM tblpersonalskills";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $data['skills'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $sql = "SELECT * FROM tblpersonaltraining";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $data['training'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $conn->commit();

    return json_encode($data);
  } catch (\Throwable $th) {
    $conn->rollBack();
    return 0;
  }
}
 //user


  function sendEmail($json)
  {
    include "send_email.php";
    // {"emailToSent":"xhifumine@gmail.com","emailSubject":"Kunwari MESSAGE","emailBody":"Kunwari message ni diri hehe <b>102345</b>"}
    $data = json_decode($json, true);
    $sendEmail = new SendEmail();
    return $sendEmail->sendEmail($data['emailToSent'], $data['emailSubject'], $data['emailBody']);
  }

  function getActiveJob()
  {
      include "connection.php";

      $sql = "
          SELECT a.jobM_id, a.jobM_title, a.jobM_description, a.jobM_status,
                 GROUP_CONCAT(DISTINCT c.duties_text SEPARATOR '|') as duties_text,
                 GROUP_CONCAT(DISTINCT d.jeduc_text SEPARATOR '|') as jeduc_text,
                 GROUP_CONCAT(DISTINCT e.jwork_responsibilities SEPARATOR '|') as jwork_responsibilities,
                 GROUP_CONCAT(DISTINCT e.jwork_duration SEPARATOR '|') as jwork_duration,



                 GROUP_CONCAT(DISTINCT f.jknow_text SEPARATOR '|') as jknow_text,
                 GROUP_CONCAT(DISTINCT g.jskills_text SEPARATOR '|') as jskills_text,
                 GROUP_CONCAT(DISTINCT h.jtrng_text SEPARATOR '|') as jtrng_text,
                 (SELECT COUNT(*)
                  FROM tblpositionapplied b
                  WHERE b.posA_jobMId = a.jobM_id) as Total_Applied
          FROM tbljobsmaster a
          LEFT JOIN tbljobsmasterduties c ON a.jobM_id = c.duties_jobId
          LEFT JOIN tbljobseducation d ON a.jobM_id = d.jeduc_jobId
          LEFT JOIN tbljobsworkexperience e ON a.jobM_id = e.jwork_jobId
          LEFT JOIN tbljobsknowledge f ON a.jobM_id = f.jknow_jobId
          LEFT JOIN tbljobsskills g ON a.jobM_id = g.jskills_jobId
          LEFT JOIN tbljobstrainings h ON a.jobM_id = h.jtrng_jobId
          WHERE a.jobM_status = 1
          GROUP BY a.jobM_id";

      try {
          $stmt = $conn->prepare($sql);
          $stmt->execute();

          $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
          // Ensure the result is an array before encoding
          if (is_array($result)) {
              echo json_encode($result);
          } else {
              // Return an empty array if no data is found
              echo json_encode([]);
          }
      } catch (PDOException $e) {
          // Handle database errors
          echo json_encode(["error" => $e->getMessage()]);
      }
  }





  function getAppliedJobs()
  {
    include "connection.php";

    try {

      $json = file_get_contents('php://input');
      $data = json_decode($json, true);


      error_log(print_r($data, true));

      if (!isset($data['cand_id'])) {
        return json_encode(["error" => "cand_id not provided"]);
      }

      $cand_id = (int) $data['cand_id'];

      $sql = "SELECT a.jobM_title
                  FROM tbljobsmaster a
                  INNER JOIN tblpositionapplied b
                  ON a.jobM_id  = b.posA_jobMId
                  WHERE b.posA_candId  = :cand_id";

      $stmt = $conn->prepare($sql);
      $stmt->bindParam(':cand_id', $cand_id, PDO::PARAM_INT);
      $stmt->execute();

      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

      if (empty($result)) {
        return json_encode(["error" => "No applied jobs found"]);
      }

      return json_encode($result);

    } catch (PDOException $e) {
      return json_encode(["error" => "Database error: " . $e->getMessage()]);
    }
  }


  function applyForJob()
  {
      include "connection.php";

      // Decode JSON input
      $input = json_decode(file_get_contents('php://input'), true);

      // Check if required parameters are set
      if (!isset($input['user_id']) || !isset($input['jobId'])) {
          echo json_encode(["error" => "Missing required parameters"]);
          return;
      }

      $user_id = $input['user_id'];
      $jobId = $input['jobId'];

      // Validate if the job ID exists in tbljobsmaster
      $sqlCheckJob = "SELECT jobM_id FROM tbljobsmaster WHERE jobM_id = :jobId";
      $stmtCheckJob = $conn->prepare($sqlCheckJob);
      $stmtCheckJob->bindParam(':jobId', $jobId, PDO::PARAM_INT);
      $stmtCheckJob->execute();

      if ($stmtCheckJob->rowCount() == 0) {
          echo json_encode(["error" => "Invalid job ID"]);
          return;
      }

      // Check if the user has already applied for the job
      $sqlCheckApplication = "SELECT posA_id FROM tblpositionapplied WHERE posA_candId = :user_id AND posA_jobMId = :jobId";
      $stmtCheckApplication = $conn->prepare($sqlCheckApplication);
      $stmtCheckApplication->bindParam(':user_id', $user_id, PDO::PARAM_INT);
      $stmtCheckApplication->bindParam(':jobId', $jobId, PDO::PARAM_INT);
      $stmtCheckApplication->execute();

      if ($stmtCheckApplication->rowCount() > 0) {
          echo json_encode(["error" => "You have already applied for this job"]);
          return;
      }


      $currentDateTime = date('Y-m-d H:i:s');

      $sql = "
          INSERT INTO tblpositionapplied (posA_candId, posA_jobMId, posA_datetime)
          VALUES (:user_id, :jobId, :posA_datetime)
      ";

      try {
          $stmt = $conn->prepare($sql);
          $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
          $stmt->bindParam(':jobId', $jobId, PDO::PARAM_INT);
          $stmt->bindParam(':posA_datetime', $currentDateTime, PDO::PARAM_STR);
          $stmt->execute();

          echo json_encode(["success" => "Job applied successfully"]);
      } catch (PDOException $e) {
          echo json_encode(["error" => $e->getMessage()]);
      }
  }






} //user

function recordExists($value, $table, $column)
{
  include "connection.php";
  $sql = "SELECT COUNT(*) FROM $table WHERE $column = :value";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":value", $value);
  $stmt->execute();
  $count = $stmt->fetchColumn();
  return $count > 0;
}

function uploadImage()
{
  if (isset($_FILES["file"])) {
    $file = $_FILES['file'];
    // print_r($file);
    $fileName = $_FILES['file']['name'];
    $fileTmpName = $_FILES['file']['tmp_name'];
    $fileSize = $_FILES['file']['size'];
    $fileError = $_FILES['file']['error'];
    // $fileType = $_FILES['file']['type'];

    $fileExt = explode(".", $fileName);
    $fileActualExt = strtolower(end($fileExt));

    $allowed = ["jpg", "jpeg", "png"];

    if (in_array($fileActualExt, $allowed)) {
      if ($fileError === 0) {
        if ($fileSize < 25000000) {
          $fileNameNew = uniqid("", true) . "." . $fileActualExt;
          $fileDestination = 'images/' . $fileNameNew;
          move_uploaded_file($fileTmpName, $fileDestination);
          return $fileNameNew;
        } else {
          return 4;
        }
      } else {
        return 3;
      }
    } else {
      return 2;
    }
  } else {
    return "";
  }
}

function getCurrentDate()
{
  $today = new DateTime("now", new DateTimeZone('Asia/Manila'));
  return $today->format('Y-m-d h:i:s A');
}

$input = json_decode(file_get_contents('php://input'), true);



$operation = isset($input["operation"]) ? $input["operation"] : "0";
// $operation = isset($input["operation"]) ? $input["operation"] : "0";


$json = isset($input["json"]) ? $input["json"] : "0";

$user = new User();

switch ($operation) {
  case "login":
    echo $user->login($json);
    break;
  case "signup":
    echo $user->signup($json);
    break;
  case "getInstitution":
    echo $user->getInstitution();
    break;
  case "getCourses":
    echo $user->getCourses();
    break;
  case "sendEmail":
    echo $user->sendEmail($json);
    break;
  case "getActiveJob":
    echo $user->getActiveJob();
    break;
  case "getAppliedJobs":
    echo $user->getAppliedJobs();
    break;
  case "applyForJob":
    echo $user->applyForJob();
    break;
  case "getPinCode":
    echo $user->getPinCode($json);
    break;
  case "getSkills":
    echo $user->getSkills();
    break;
  case "isEmailExist":
    echo $user->isEmailExist($json);
    break;
  case "getCourseGraduate":
    echo $user->getCourseGraduate();
    break;
  case "getAllDataForDropdownSignup":
    echo $user->getAllDataForDropdownSignup();
    break;


  default:
    echo json_encode("WALA KA NAGBUTANG OG OPERATION SA UBOS HAHAHHA BOBO");
    http_response_code(400); // Bad Request
    break;
}

