<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Token extends CI_Controller {

    function __construct() {
        parent::__construct();
    }

    function getToken() {
        try {
            // Get DfpUser from credentials in "../auth.ini"
            // relative to the DfpUser.php file's directory.
            $user = new DfpUser();

            // Log SOAP XML request and response.
            $user->LogDefaults();

            // Get the UserService.
            $userService = $user->GetService('UserService', 'v201208');

            // Set defaults for page and statement.
            $page = new UserPage();
            $filterStatement = new Statement();
            $offset = 0;

            do {
                // Create a statement to get all users.
                $filterStatement->query = 'LIMIT 500 OFFSET ' . $offset;

                // Get users by statement.
                $page = $userService->getUsersByStatement($filterStatement);

                if (isset($page->results)) {
                    echo "****************************************************************************** funciono! <br>";
                    new_var_dump($page->results);
                    echo "****************************************************************************************** <br>";
                }

                $offset += 500;
            } while ($offset < $page->totalResultSetSize);

            print "Number of results found: " . $page->totalResultSetSize . "\n";
        } catch (Exception $e) {
            print $e->getMessage() . "\n";
        }
    }

}