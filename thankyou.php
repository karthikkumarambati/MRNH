<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| MRNH CALLBACK FORM
|--------------------------------------------------------------------------
| This file:
| 1. Receives the callback form
| 2. Validates the submitted data
| 3. Sends email using PHPMailer + Gmail SMTP
| 4. Displays success/error status
|--------------------------------------------------------------------------
*/


/*
|--------------------------------------------------------------------------
| ERROR REPORTING
|--------------------------------------------------------------------------
| Keep ON while testing.
| Change display_errors to 0 after everything works.
|--------------------------------------------------------------------------
*/

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);


/*
|--------------------------------------------------------------------------
| PHPMailer
|--------------------------------------------------------------------------
*/

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;


/*
|--------------------------------------------------------------------------
| Load PHPMailer
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/Exception.php';
require_once __DIR__ . '/PHPMailer.php';
require_once __DIR__ . '/SMTP.php';


/*
|--------------------------------------------------------------------------
| Status Variables
|--------------------------------------------------------------------------
*/

$status = 'error';

$displayMessage = '';


/*
|--------------------------------------------------------------------------
| Only Process POST Requests
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    $displayMessage = 'Invalid request. Please submit the form from the website.';

} else {

    /*
    |--------------------------------------------------------------------------
    | Get Form Data
    |--------------------------------------------------------------------------
    */

    $userName = trim(
        strip_tags(
            $_POST['name'] ?? ''
        )
    );


    $userPhone = preg_replace(
        '/\D+/',
        '',
        (string) ($_POST['phone'] ?? '')
    );


    $userSpeciality = trim(
        strip_tags(
            $_POST['speciality']
            ?? $_POST['concern']
            ?? ''
        )
    );


    $appointmentDate = trim(
        strip_tags(
            $_POST['appointment_date'] ?? ''
        )
    );


    /*
    |--------------------------------------------------------------------------
    | Validate Name
    |--------------------------------------------------------------------------
    */

    if ($userName === '') {

        $displayMessage =
            'Please enter your name.';

    }


    /*
    |--------------------------------------------------------------------------
    | Validate Phone
    |--------------------------------------------------------------------------
    */

    elseif (!preg_match('/^[0-9]{10}$/', $userPhone)) {

        $displayMessage =
            'Please enter a valid 10-digit mobile number.';

    }


    /*
    |--------------------------------------------------------------------------
    | Validate Speciality
    |--------------------------------------------------------------------------
    */

    elseif ($userSpeciality === '') {

        $displayMessage =
            'Please select a speciality.';

    }


    /*
    |--------------------------------------------------------------------------
    | Validate Appointment Date
    |--------------------------------------------------------------------------
    */

    elseif (
        $appointmentDate !== '' &&
        !preg_match(
            '/^\d{4}-\d{2}-\d{2}$/',
            $appointmentDate
        )
    ) {

        $displayMessage =
            'Invalid appointment date.';

    }


    /*
    |--------------------------------------------------------------------------
    | SEND EMAIL
    |--------------------------------------------------------------------------
    */

    else {

        /*
        |--------------------------------------------------------------------------
        | Gmail SMTP Credentials
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        | Use a Gmail App Password.
        | Do NOT use your normal Gmail password.
        |
        */

        $smtpUsername = 'noreply@mrghospitals.org';

        $smtpPassword = 'yukthemaaepzlabv';


        /*
        |--------------------------------------------------------------------------
        | Create PHPMailer
        |--------------------------------------------------------------------------
        */

        $mail = new PHPMailer(true);


        try {

            /*
            |--------------------------------------------------------------------------
            | SMTP
            |--------------------------------------------------------------------------
            */

            $mail->isSMTP();

            $mail->Host = 'smtp.gmail.com';

            $mail->SMTPAuth = true;

            $mail->Username = $smtpUsername;

            $mail->Password = $smtpPassword;

            $mail->SMTPSecure =
                PHPMailer::ENCRYPTION_STARTTLS;

            $mail->Port = 587;

            $mail->CharSet = 'UTF-8';


            /*
            |--------------------------------------------------------------------------
            | Sender
            |--------------------------------------------------------------------------
            */

            $mail->setFrom(
                $smtpUsername,
                'MRNH Website'
            );


            /*
            |--------------------------------------------------------------------------
            | Recipients
            |--------------------------------------------------------------------------
            */

            // $mail->addAddress(
            //     'leads@mrghospitals.org',
            //     'MRNH Leads'
            // );

            // $mail->addAddress(
            //     'expertopinion@mrghospitals.org',
            //     'Expert Opinion'
            // );

            // $mail->addAddress(
            //     'webleadsmrnh@gmail.com',
            //     'Website Leads'
            // );

            // $mail->addAddress(
            //     'branding@mrghospitals.org',
            //     'Branding Team'
            // );

            $mail->addAddress(
                'karthikambati.mrv@gmail.com',
                'Developing Team'
            );


            /*
            |--------------------------------------------------------------------------
            | Subject
            |--------------------------------------------------------------------------
            */

            $mail->isHTML(true);

            // $mail->Subject =
            //     'New MRNH Callback Request - ' .
            //     $userName;


            // Get form type
// =====================================================

// Get submitted form type
$formType = trim(
    strip_tags(
        $_POST['form_type'] ?? 'callback'
    )
);

// Default values
$emailSubject = 'New MRNH Callback Request - ' . $userName;
$emailHeading = 'New Callback Request';
$emailDescription = 'A new callback request has been submitted through the MRNH website.';
$emailRequestTitle = 'Callback Request';
$emailRequestMessage = 'Please contact the patient regarding this callback request.';

// Appointment form
if ($formType === 'appointment') {

    $emailSubject = 'New MRNH Appointment Request - ' . $userName;

    $emailHeading = 'New Appointment Request';

    $emailDescription =
        'A new appointment request has been submitted through the MRNH website.';

    $emailRequestTitle = 'Appointment Request';

    $emailRequestMessage =
        'Please contact the patient regarding this appointment request.';
}

// Set email subject
$mail->Subject = $emailSubject;

// ======================================================
            /*
            |--------------------------------------------------------------------------
            | Appointment Date
            |--------------------------------------------------------------------------
            */

            $dateRow = '';

            if ($appointmentDate !== '') {

                $dateRow = '
                    <tr>
                        <td style="
                            padding:12px 10px;
                            background:#f7f9fc;
                            border-bottom:1px solid #e5e8ed;
                            font-weight:bold;
                            color:#213f9a;
                        ">
                            Preferred Appointment Date
                        </td>

                        <td style="
                            padding:12px 10px;
                            border-bottom:1px solid #e5e8ed;
                            color:#333333;
                        ">
                            ' .
                            htmlspecialchars(
                                $appointmentDate,
                                ENT_QUOTES,
                                'UTF-8'
                            )
                            . '
                        </td>
                    </tr>
                ';
            }


            /*
            |--------------------------------------------------------------------------
            | Safe HTML Values
            |--------------------------------------------------------------------------
            */

            $safeName = htmlspecialchars(
                $userName,
                ENT_QUOTES,
                'UTF-8'
            );


            $safePhone = htmlspecialchars(
                $userPhone,
                ENT_QUOTES,
                'UTF-8'
            );


            $safeSpeciality = htmlspecialchars(
                $userSpeciality,
                ENT_QUOTES,
                'UTF-8'
            );


            /*
            |--------------------------------------------------------------------------
            | Email HTML
            |--------------------------------------------------------------------------
            */

            $mail->Body = '
            <!DOCTYPE html>

            <html>

            <head>
                <meta charset="UTF-8">
                <title>New Callback Request</title>
            </head>

            <body style="
                margin:0;
                padding:20px;
                background:#f4f7f9;
                font-family:Arial,Helvetica,sans-serif;
            ">

                <div style="
                    max-width:650px;
                    margin:0 auto;
                    background:#ffffff;
                    border:1px solid #e1e5ea;
                    border-radius:12px;
                    overflow:hidden;
                ">

                    <div style="
                        background:#213f9a;
                        padding:25px;
                        text-align:center;
                    ">

                        <h2 style="margin:0; color:#ffffff; font-size:24px;">' . $emailHeading . '</h2>

                        <p style="
                            margin:8px 0 0;
                            color:#dfe7ff;
                            font-size:14px;
                        ">
                            Malla Reddy Narayana
                            Multispeciality Hospital
                        </p>

                    </div>


                    <div style="padding:25px;">

                        <p style="margin:0 0 20px; color:#555555; font-size:15px; line-height:1.6;">' . $emailDescription . '</p>

                        <table style="
                            width:100%;
                            border-collapse:collapse;
                            font-size:15px;
                        ">

                            <tr>

                                <td style="
                                    padding:12px 10px;
                                    background:#f7f9fc;
                                    border-bottom:1px solid #e5e8ed;
                                    font-weight:bold;
                                    color:#213f9a;
                                    width:38%;
                                ">
                                    Patient Name
                                </td>

                                <td style="
                                    padding:12px 10px;
                                    border-bottom:1px solid #e5e8ed;
                                    color:#333333;
                                ">
                                    ' . $safeName . '
                                </td>

                            </tr>


                            <tr>

                                <td style="
                                    padding:12px 10px;
                                    background:#f7f9fc;
                                    border-bottom:1px solid #e5e8ed;
                                    font-weight:bold;
                                    color:#213f9a;
                                ">
                                    Mobile Number
                                </td>

                                <td style="
                                    padding:12px 10px;
                                    border-bottom:1px solid #e5e8ed;
                                    color:#333333;
                                ">
                                    ' . $safePhone . '
                                </td>

                            </tr>


                            <tr>

                                <td style="
                                    padding:12px 10px;
                                    background:#f7f9fc;
                                    border-bottom:1px solid #e5e8ed;
                                    font-weight:bold;
                                    color:#213f9a;
                                ">
                                    Speciality / Concern
                                </td>

                                <td style="
                                    padding:12px 10px;
                                    border-bottom:1px solid #e5e8ed;
                                    color:#333333;
                                ">
                                    ' . $safeSpeciality . '
                                </td>

                            </tr>


                            ' . $dateRow . '

                        </table>


                        <div style="
                            margin-top:25px;
                            padding:18px;
                            background:#fff8ef;
                            border-left:4px solid #f5951e;
                            border-radius:4px;
                        ">

                            <strong style="color:#213f9a; font-size:15px;">' . $emailRequestTitle . '</strong>

                         <p style="margin:10px 0 0; color:#444444; font-size:14px; line-height:1.7;"> ' . $emailRequestMessage . '</p>

                        </div>

                    </div>


                    <div style="
                        padding:15px;
                        background:#f4f4f4;
                        text-align:center;
                        color:#888888;
                        font-size:12px;
                    ">

                        This email was generated automatically
                        from the Malla Reddy Narayana Hospitals
                        website.

                    </div>

                </div>

            </body>

            </html>
            ';


            /*
            |--------------------------------------------------------------------------
            | Plain Text Email
            |--------------------------------------------------------------------------
            */

            $mail->AltBody =
                "New MRNH Callback Request\n\n" .
                "Patient Name: {$userName}\n" .
                "Mobile Number: {$userPhone}\n" .
                "Speciality / Concern: {$userSpeciality}\n" .
                (
                    $appointmentDate !== ''
                    ? "Preferred Appointment Date: {$appointmentDate}\n"
                    : ''
                ) .
                "\nPlease contact the patient regarding " .
                "this callback request.";


            /*
            |--------------------------------------------------------------------------
            | SEND
            |--------------------------------------------------------------------------
            */

            $mail->send();


            /*
            |--------------------------------------------------------------------------
            | SUCCESS
            |--------------------------------------------------------------------------
            */

            $status = 'success';

            $displayMessage =
                'Thank you, ' .
                htmlspecialchars(
                    $userName,
                    ENT_QUOTES,
                    'UTF-8'
                ) .
                '. Your callback request has been submitted successfully. Our team will contact you shortly.';


        } catch (Exception $e) {

            /*
            |--------------------------------------------------------------------------
            | ERROR LOG
            |--------------------------------------------------------------------------
            */

            error_log(
                'MRNH PHPMailer Error: ' .
                $mail->ErrorInfo
            );


            $displayMessage =
                'We could not send your request at this time. Please try again or call the hospital directly.';
        }
    }
}

?>


<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        <?php
        echo $status === 'success'
            ? 'Request Sent | MRNH'
            : 'Request Error | MRNH';
        ?>
    </title>


    <style>

        * {
            box-sizing: border-box;
        }

        body {
            font-family:
                Arial,
                Helvetica,
                sans-serif;

            background:
                linear-gradient(
                    135deg,
                    #f4f7f6,
                    #eef3ff
                );

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            min-height:
                100vh;

            margin:
                0;

            padding:
                20px;
        }


        .status-card {

            background:
                #ffffff;

            padding:
                45px 35px;

            border-radius:
                16px;

            box-shadow:
                0 15px 45px
                rgba(0, 0, 0, 0.10);

            text-align:
                center;

            max-width:
                560px;

            width:
                100%;

            border-top:
                6px solid #213f9a;
        }


        .status-icon {

            width:
                70px;

            height:
                70px;

            margin:
                0 auto 20px;

            border-radius:
                50%;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            font-size:
                38px;

            font-weight:
                bold;
        }


        .success-icon {

            color:
                #ffffff;

            background:
                #28a745;
        }


        .error-icon {

            color:
                #ffffff;

            background:
                #dc3545;
        }


        h1 {

            color:
                #213f9a;

            font-size:
                28px;

            margin:
                0 0 12px;
        }


        p {

            color:
                #666666;

            font-size:
                16px;

            line-height:
                1.7;

            margin:
                0 0 30px;
        }


        .back-btn {

            display:
                inline-block;

            background:
                #f5951e;

            color:
                #ffffff;

            text-decoration:
                none;

            padding:
                14px 28px;

            border-radius:
                7px;

            font-weight:
                bold;

            transition:
                background 0.3s ease;
        }


        .back-btn:hover {

            background:
                #d8831a;
        }


        .call-btn {

            display:
                inline-block;

            margin-left:
                8px;

            background:
                #213f9a;

            color:
                #ffffff;

            text-decoration:
                none;

            padding:
                14px 28px;

            border-radius:
                7px;

            font-weight:
                bold;
        }


        @media (max-width: 600px) {

            .status-card {

                padding:
                    35px 22px;
            }


            h1 {

                font-size:
                    24px;
            }


            .back-btn,
            .call-btn {

                display:
                    block;

                margin:
                    8px 0;

                width:
                    100%;
            }
        }

    </style>

</head>


<body>


    <div class="status-card">


        <?php if ($status === 'success'): ?>

            <div class="status-icon success-icon">
                ✓
            </div>


            <h1>
                Request Sent!
            </h1>


            <p>
                <?php echo $displayMessage; ?>
            </p>


            <a
                href="index.html"
                class="back-btn"
            >
                Return to Website
            </a>


        <?php else: ?>

            <div class="status-icon error-icon">
                ✕
            </div>


            <h1>
                Request Failed
            </h1>


            <p>
                <?php echo htmlspecialchars(
                    $displayMessage,
                    ENT_QUOTES,
                    'UTF-8'
                ); ?>
            </p>


            <a
                href="index.html#callback"
                class="back-btn"
            >
                Try Again
            </a>


            <a
                href="tel:08790387903"
                class="call-btn"
            >
                Call Hospital
            </a>


        <?php endif; ?>


    </div>


</body>

</html>