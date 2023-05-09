<?php


try {

$username = getenv('DB_USER'); // e.g. 'your_db_user'
            $password = getenv('DB_PASS'); // e.g. 'your_db_password'
            $dbName = getenv('DB_NAME'); // e.g. 'your_db_name'
            $instanceUnixSocket = getenv('INSTANCE_UNIX_SOCKET'); // e.g. '/cloudsql/project:region:instance'

// Connect using UNIX sockets
            $dsn = sprintf(
                'mysql:dbname=%s;unix_socket=%s',
                $dbName,
                $instanceUnixSocket
            );

$db = new PDO($dsn,
              $username,
              $password,
              # [START_EXCLUDE]
              // Here we set the connection timeout to five seconds and ask PDO to
              // throw an exception if any errors occur.
              [
                    PDO::ATTR_TIMEOUT => 5,
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
              ]
              # [END_EXCLUDE]
);

} catch (TypeError $e) {
            throw new RuntimeException(
                sprintf(
                    'Invalid or missing configuration! Make sure you have set ' .
                        '$username, $password, $dbName, ' .
                        'and $instanceUnixSocket (for UNIX socket mode). ' .
                        'The PHP error was %s',
                    $e->getMessage()
                ),
                (int) $e->getCode(),
                $e
            );
        } catch (PDOException $e) {
            throw new RuntimeException(
                sprintf(
                    'Could not connect to the Cloud SQL Database. Check that ' .
                        'your username and password are correct, that the Cloud SQL ' .
                        'proxy is running, and that the database exists and is ready ' .
                        'for use. For more assistance, refer to %s. The PDO error was %s',
                    'https://cloud.google.com/sql/docs/mysql/connect-external-app',
                    $e->getMessage()
                ),
                (int) $e->getCode(),
                $e
            );
}


function isValidJSON($str) {
   json_decode($str);
   return json_last_error() == JSON_ERROR_NONE;
}

$json_params = file_get_contents("php://input");

if (strlen($json_params) > 0 && isValidJSON($json_params)) {
  $decoded_params = json_decode($json_params);
  $input = $decoded_params->{"input"};

  $sql = "insert into name values ('$input')";
  $result = $db->query($sql);

  if ($result) {

    $resultArray = array( array('result' => 'success')  );
 
    // Finally, encode the array to JSON and output the results
    echo json_encode($resultArray);


  } else {

    $resultArray = array( array('result' => 'fail') );
    echo json_encode($resultArray);
  }
}

/*
foreach($db->query('SELECT * FROM name') as $row) {
  echo $row['name']; //etc...
}
*/
