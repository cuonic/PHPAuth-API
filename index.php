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
            "error" => true,
            "message" => "not_found"
        ),
        404
    );
});

Flight::map('error', function(Exception $e) {
    Flight::json(
        array(
            "error" => true,
            "message" => $e->getMessage()
        ),
        500
    );
});

Flight::route('POST /login', function() {
    try {
        Flight::phpauth()->login(
            Flight::request()->data->email,
            Flight::request()->data->password
        );

        Flight::json(
            array(
                "error" => false,
                "message" => "logged_in"
            )
        );
    } catch (\Exception $e) {
        Flight::json(
            array(
                "error" => true,
                "message" => $e->getMessage()
            )
        );
    }
});

Flight::route('POST /register', function() {
    try {
        Flight::phpauth()->register(
            Flight::request()->data->email,
            Flight::request()->data->password,
            Flight::request()->data->repeatPassword
        );

        Flight::json(
            array(
                "error" => false,
                "message" => "registration_success"
            )
        );
    } catch (\Exception $e) {
        Flight::json(
            array(
                "error" => true,
                "message" => $e->getMessage()
            )
        );
    }
});

Flight::route('POST /change-password', function() {
    try {
        Flight::phpauth()->changePassword(
            Flight::request()->data->password,
            Flight::request()->data->newPassword,
            Flight::request()->data->repeatNewPassword
        );

        Flight::json(
            array(
                "error" => false,
                "message" => "password_changed"
            )
        );
    } catch (\Exception $e) {
        Flight::json(
            array(
                "error" => true,
                "message" => $e->getMessage()
            )
        );
    }
});

Flight::route('POST /change-email', function() {
    try {
        Flight::phpauth()->changeEmail(
            Flight::request()->data->password,
            Flight::request()->data->newEmail
        );

        Flight::json(
            array(
                "error" => false,
                "message" => "email_changed"
            )
        );
    } catch (\Exception $e) {
        Flight::json(
            array(
                "error" => true,
                "message" => $e->getMessage()
            )
        );
    }
});

Flight::route('POST /delete', function() {
    try {
        Flight::phpauth()->delete(
            Flight::request()->data->password
        );

        Flight::json(
            array(
                "error" => false,
                "message" => "account_deleted"
            )
        );
    } catch (\Exception $e) {
        Flight::json(
            array(
                "error" => true,
                "message" => $e->getMessage()
            )
        );
    }
});

Flight::start();
