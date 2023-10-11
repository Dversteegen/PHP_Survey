<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="Author" content="Dany Versteegen" />
    <link rel="Stylesheet" href="css/style.css">
    <title>Survey Form</title>
</head>

<?php

session_start();

include "inc/layout.php";
include "inc/dbh.php";
include "inc/functions.php";

ExecuteStep();
GetNavigationBar();
?>


<body>

    <div id="container">

        <?php        
        // var_dump($_SESSION);
        //If there's no survey key, make a new one
        if (!isset($_SESSION["sess_key"])) {
            $_SESSION["sess_key"] = uniqid() . "_" . uniqueKey() . PHP_EOL;
        }
        echo "<h2>Survey Key: " . $_SESSION["sess_key"] . "</h2>";

        //If status isn't set, let the user choose an amount of questions
        if (!isset($_SESSION["sess_status"])) {

        ?>

            <div id="container_questions_step_one">

                <h2>Step 1: Choose the amount of questions</h2>

                <form method="POST" action="">
                    <input type="number" name="amount" required />
                    <button id="button_step_one" class="button_step" type="submit">Save amount of questions</button>
                </form>

            </div>
        <?php
            //If the status is set to step one, let the user fill in questions
        } else if ($_SESSION["sess_status"] == "stepOne") {

        ?>

            <div id="container_questions_step_two">

                <h2>Step 2: Enter all <?php echo $_SESSION["sess_amount"] ?> questions</h2>

                <form method="POST">

                    <?php
                    $count = 0;
                    $_SESSION["sess_questions"] = array();
                    for ($count = 0; $count < $_SESSION["sess_amount"]; $count++) {
                        $currentName = "question_" . strval($count);

                        //Adds an array to the array answers
                        array_push($_SESSION["sess_questions"], array($currentName, ""));
                        echo "<input type='text' name='" . $currentName . "' required/>";
                    }
                    ?>

                    <button id="button_step_two" class="button_step" type="submit">Save questions</button>
                </form>

            </div>

        <?php
            //If the status is set to step two, let the user answer the first question which doesn't have a belonging answer
        } else if ($_SESSION["sess_status"] == "stepTwo") {
        ?>
            <div id="container_questions_step_three">

                <h2>Step 3: Answering </h2>

                <?php
                //By counting the amount of answers in the array, we know at what index to put the the answer
                $index = count($_SESSION["sess_answers"]);
                $currentName = "answer_" . strval($index);

                //Adds an array to the array answers
                array_push($_SESSION["sess_answers"], array($currentName, ""));
                ?>
                <form method="POST">

                    <h2>Question: <?php echo $_SESSION["sess_questions"][$index][1] ?></h2>
                    <?php
                    echo "<input type='text' name='" . $currentName . "' required/>";
                    ?>
                    <button id="button_step_three" class="button_step" type="submit">Save answer</button>
                </form>
                <?php
                ?>

            </div>
        <?php
            //If the status is set to step three, give the user the option to save the survey to the database
        } else if ($_SESSION["sess_status"] == "stepThree") {
        ?>
            <div id="container_questions_step_three">
                <form method="POST">
                    <button class="button_step" type="submit">Save to Database</button>
                </form>
            </div>
        <?php
        }
        ?>
        <form method="POST" action="">
            <button type="submit" id="button_reset" name="button_reset">Reset session</button>
        </form>
        <h1 id="h1_session_table">Current Questions Table</h1>
        <table id="table_questions" class="table">
            <thead>
                <tr>
                    <th>Question ID</th>
                    <th>Question</th>
                    <th>Answer</th>
                </tr>
            </thead>
            <tbody>
                <?php GetQuestions(); ?>
            </tbody>
        </table>
        <h1 id="h1_database_table">Database Table</h1>
        <table id="table_database" class="table">
            <thead>
                <tr>
                    <th>Survey ID</th>
                    <th>Question</th>
                    <th>Answer</th>
                    <th>Answer Date</th>
                </tr>
            </thead>
            <tbody>
                <?php GetSurveyFromDatabase(); ?>
            </tbody>
        </table>
        <form method="POST" action="">
            <button type="submit" id="button_clear_database" name="button_clear_database">Clear Database</button>
        </form>
    </div>

</body>

</html>