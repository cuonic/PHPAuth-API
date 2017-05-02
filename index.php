<?php

require 'vendor/autoload.php';

$db_host = "localhost";
$db_user = "root";
$db_pass = "root";
$db_name = "phpauth_db";

Flight::register(
    "phpauth",
    "\PHPAuth\PHPAuth",
    array(
        new \PHPAuth\Database\MySQL(
            $db_host,
            $db_user,
            $db_pass,
            $db_name
        )
    )
);

Flight::map('notFound', function() {
    Flight::json(
        array(
            "message" => "not_found"
        ),
        404
    );
});

Flight::map('error', function($e) {
    Flight::json(
        array(
            "message" => $e->getMessage(),
            "file" => $e->getFile() . ":" . $e->getLine()
        ),
        400
    );
});

Flight::route('GET /session', function() {
    Flight::json(
        Flight::phpauth()->getSessionInfo()
    );
});

Flight::route('GET /session/active', function() {
    $currentSession = Flight::phpauth()->getCurrentSession();
    $sessions = Flight::phpauth()->getActiveSessions();
    $sessionsArray = array();

    foreach($sessions as $session) {
        $sessionArray = $session->toArray();

        if($session->getUuid() == $currentSession->getUuid()) {
            $sessionArray['isCurrentSession'] = true;
        } else {
            $sessionArray['isCurrentSession'] = false;
        }

        $sessionsArray[] = $sessionArray;
    }

    Flight::json(array(
        "sessions" => $sessionsArray
    ));
});

Flight::route('DELETE /session/@sessionUuid', function($sessionUuid) {
    Flight::phpauth()->deleteSession($sessionUuid);
});

Flight::route('GET /log', function() {
    $logs = Flight::phpauth()->getLogs();

    $logsArray = array();

    foreach($logs as $log) {
        $logsArray[] = $log->toArray();
    }

    Flight::json(array(
        "logs" => $logsArray
    ));
});

Flight::route('POST /login', function() {
    if(strlen(Flight::request()->data->isPersistent) > 0) {
        $isPersistent = true;
    } else {
        $isPersistent = false;
    }

    Flight::phpauth()->login(
        Flight::request()->data->email,
        Flight::request()->data->password,
        $isPersistent
    );

    Flight::json(
        array(
            "message" => "logged_in"
        )
    );
});

Flight::route('POST /register', function() {
    Flight::phpauth()->register(
        Flight::request()->data->email,
        Flight::request()->data->password,
        Flight::request()->data->repeatPassword
    );

    Flight::json(
        array(
            "message" => "registration_success"
        )
    );
});

Flight::route('POST /change-password', function() {
    Flight::phpauth()->changePassword(
        Flight::request()->data->password,
        Flight::request()->data->newPassword,
        Flight::request()->data->repeatNewPassword
    );

    Flight::json(
        array(
            "message" => "password_changed"
        )
    );
});

Flight::route('POST /change-email', function() {
    Flight::phpauth()->changeEmail(
        Flight::request()->data->password,
        Flight::request()->data->email
    );

    Flight::json(
        array(
            "error" => false,
            "message" => "email_changed"
        )
    );
});

Flight::route('POST /delete', function() {
    Flight::phpauth()->delete(
        Flight::request()->data->password
    );

    Flight::json(
        array(
            "error" => false,
            "message" => "account_deleted"
        )
    );
});

Flight::route('GET /logout', function() {
    Flight::phpauth()->logout();

    Flight::json(
        array(
            "message" => "logged_out"
        )
    );
});

Flight::route('POST /activate', function() {
    Flight::phpauth()->activate(
        Flight::request()->data->token
    );

    Flight::json(
        array(
            "message" => "account_activated"
        )
    );
});

Flight::start();
