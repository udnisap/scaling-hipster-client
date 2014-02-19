<?php
$target_path = "scripts/";
$ret = array();
try {
    $ret['status'] = array();
    if (!isset($_REQUEST['method'])) {
        throw new Exception("Method is not found", 404);
    } else {
        $method = $_REQUEST['method'];
        switch ($method) {
            case "run":
                if (!isset($_REQUEST['file']))
                    throw new Exception("File parameter is missing", 406);
                $file = $_REQUEST['file'];
                $file_path = $target_path.$file;
                if (!file_exists($filepath))
                    throw new Exception("File is not found", 404);

                $data = isset($_REQUEST['data'])? $_REQUEST['data'] : "";
                $ret['results'] = shell_exec("$file_path $data");
                break;

            case "deploy":
                if (!isset($_FILES['uploadedfile']['file']))
                    throw new Exception("File is not found", 406);

                $file_path = $target_path . basename($_FILES['uploadedfile']['file']);

                if (move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $file_path)) {
                    
                } else {
                    throw new Exception("Error uploading the file", 400);
                }
                break;

            case "status":
                $ret['results'] = shell_exec('top -b -n 1 2>&1 ');
		die($ret['results']);
                break;

            default:
                throw new Exception("Method not allowed $method", 405);
                break;
        }
        $ret['status']['type'] = 'success';
        $ret['status']['code'] = 200;
    }
} catch (Exception $exc) {
    $ret['status'] = array('type' => 'error', 'code' => $exc->getCode(), 'message' => $exc->getMessage());
} 
header('X-PHP-Response-Code:', true, $ret['status']['code']);
//http_response_code($ret['status']['code']);
echo json_encode($ret);

?>
