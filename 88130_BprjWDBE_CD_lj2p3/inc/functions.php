<?php

//For generating the unique key
function uniqueKey($lenght = 5)
{

    if (function_exists("random_bytes")) {
        $bytes = random_bytes(ceil($lenght / 2));
    } elseif (function_exists("openssl_random_pseudo_bytes")) {
        $bytes = openssl_random_pseudo_bytes(ceil($lenght / 2));
    } else {
        throw new Exception("no cryptographically secure random function available");
    }
    return substr(bin2hex($bytes), 0, $lenght);
}

//This function gets called every post
//Based on a variable defined in the session, go to the next step
function ExecuteStep()
{
    if (isset($_POST['button_reset'])) {
        CheckSessionStatus();
    } else if (isset($_POST['button_clear_database'])) {
        ClearDatabase();
    } else {

        if (!isset($_SESSION["sess_status"]) && isset($_POST['amount'])) {
            SaveAmount();
        } else if (isset($_SESSION["sess_status"])) {

            if ($_SESSION["sess_status"] == "stepOne" && !isset($_SESSION["sess_answers"])) {
                SaveQuestions();
            } else if ($_SESSION["sess_status"] == "stepTwo") {
                SaveAnswer();
            } else if ($_SESSION["sess_status"] == "stepThree") {
                SaveToDatabase();
            }
        }
    }
}

//Save the amount of question in a session
function SaveAmount()
{
    $_SESSION["sess_status"] = "stepOne";

    if (isset($_POST['amount'])) {
        $_SESSION["sess_amount"] = $_POST['amount'];
    }
}

//Save the questions in a session
function SaveQuestions()
{
    $_SESSION["sess_status"] = "stepTwo";
    $count = 0;
    foreach ($_SESSION["sess_questions"] as $question) {
        if (isset($_POST[$question[0]])) {
            $_SESSION["sess_questions"][$count][1] = $_POST[$question[0]];
        }

        $count++;
    }
    $_SESSION["sess_answers"] = array();
}

//Save an answer in a session, when all answers are given change the status
function SaveAnswer()
{
    $count = count($_SESSION["sess_answers"]) - 1;
    if (isset($_POST[$_SESSION["sess_answers"][$count][0]])) {
        $_SESSION["sess_answers"][$count][1] = $_POST[$_SESSION["sess_answers"][$count][0]];
    }

    if ($count + 1 == count($_SESSION["sess_questions"])) {
        $_SESSION["sess_status"] = "stepThree";
    }
}

//Show every question with an answer in the table
function GetQuestions()
{
    if (isset($_SESSION["sess_questions"]) && isset($_SESSION["sess_answers"])) {

        $count = 0;
        foreach ($_SESSION["sess_questions"] as $question) {
            if ($_SESSION["sess_answers"][$count][1] != "") {

                echo "<tr><td>" . $count . "</td><td>" . $question[1] . "</td><td>" . $_SESSION["sess_answers"][$count][1] . "</td></tr>";
                $count++;
            }
        }
    }
}

//Loops through all question and calls the function to save them in the database
function SaveToDatabase()
{
    $count = 0;
    foreach ($_SESSION["sess_questions"] as $question) {

        InsertIntoDatabase($_SESSION["sess_key"], $question[1], $_SESSION["sess_answers"][$count][1]);
        $count++;
    }
    CheckSessionStatus();
}

//Save a new record in the database
function InsertIntoDatabase($surveyKey, $question, $answer)
{
    date_default_timezone_set('Europe/Amsterdam');
    $date = date("Y-m-d h:i:s");
    $query = "INSERT INTO survey (survey_key, question, answer, answer_date) VALUES ('$surveyKey', '$question', '$answer', '$date')";
    $conn = ConnectToDatabase();
    $conn->exec($query);
}

//Get all the records saved in the database
function GetSurveyFromDatabase()
{
    $sql = "SELECT * FROM survey";
    $statement = ConnectToDatabase("picture")->query($sql);
    $surveys = $statement->fetchAll();
    ShowSurveyFromDatabase($surveys);
}

//Show all the records retrieved from the database in a table
function ShowSurveyFromDatabase($surveys)
{
    $count = 0;
    foreach ($surveys as $survey) {
        echo "<tr><td>" . $survey["survey_key"] . "</td><td>" . $survey["question"] . "</td><td>" . $survey["answer"] . "</td><td>" . $survey["answer_date"] . "</td></tr>";
        $count++;
    }
}

//Clears the database
function ClearDatabase()
{
    $query = "DELETE FROM survey";
    $statement = ConnectToDatabase()->query($query);
    $statement->execute();
}

//Resets the session
function CheckSessionStatus()
{
    session_unset();
}
