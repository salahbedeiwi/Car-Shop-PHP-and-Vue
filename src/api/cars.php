<?php 
header('Access-Control-Allow-Origin: *');
$results = array();
$results["error"] = false;
$results["message"] = "";
//connect to db
$db_name = "vue_php_car_shop";
$connect = @mysql_connect("localhost", "root", "");
if(isset($connect) && isset($db_name)){
    $connect_db = @mysql_select_db($db_name);
    if($connect_db){
        $results["is_db_connected"] =  true;
        $results["connection_msg"] =  "Successfully Connected!";
    }else{
        $results["is_db_connected"] =  false;
        $results["connection_msg"] =  "Error: DB is not found!";
    }
} else {
    $results["is_db_connected"] =  false;
    $results["connection_msg"] =  "Error: Connection Setup is not correct!";
}
$action = "";
if(isset($_GET["action"])){
    $action = $_GET["action"];
}
function generateRandomString($length = 10){
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
//CRUD
if($action == "read"){
    $sql = mysql_query("select * from `cars`");
    $sqlNumOfRows = mysql_num_rows($sql);
    if($sqlNumOfRows < 1){
        $results["error"] = true;
        $results["message"] = "No Cars Added Yet...";
    }
    $carsArr = array();
    while($rows = mysql_fetch_assoc($sql)){
        array_push($carsArr, $rows);
    }
    $results["cars"] = $carsArr;
}
if($action == "deleteCar"){
    $name = $_POST["name"];
    $id = $_POST["id"];
    $image = $_POST["image"];
    $sql = mysql_query("select * from `cars` where id = '$id'");
    $sqlNumOfRows = mysql_num_rows($sql);
    if($sqlNumOfRows >= 1){
        $sql = mysql_query("delete from `cars` where id = '$id'");
        if($sql){
            $dir = "../assets/cars/".$image;
            if(unlink($dir)){
                $results["message"] = "Successfully Deleted This Car :: $name";
            }else{
                $results["error"] = true;
                $results["message"] = "Failed Deleting car image from folder";
            }
        }else{
            $results["error"] = true;
            $results["message"] = "Failed Deleting this record, try again...";
        }
    }else{
        $results["error"] = true;
        $results["message"] = "This record does not exists...";
    }
}
if($action == "update"){
    //files
    $imgName = "";
    $upload_path = "";
    if( isset($_FILES["image"]["name"] )){
        $img_name = $_FILES["image"]["name"];
        $valid_extensions = array("png","jpg","jpeg","gif");
        $extensions = pathinfo($img_name, PATHINFO_EXTENSION);
        if(in_array($extensions, $valid_extensions)){
            $newGeneratedId = generateRandomString(75);
            $upload_path = "../assets/cars/". $newGeneratedId. ".".$extensions;
            if(file_exists($upload_path)){
                $newGeneratedId = generateRandomString(75);
                $imgName = $newGeneratedId. ".".$extensions;
            }else{
                $imgName = $newGeneratedId. ".".$extensions;
            }
        }else{
            $results["error"] = true;
            $results["message"] = "Car Image has to be : PNG, JPEG, JPG, or GIF";
        }
    }else{
        $results["error"] = true;
        $results["message"] = "Must Select Car Image!!";
    }
    $results["newImageUploaded"] = $imgName;
    if($imgName != "" && $upload_path != ""){
        $name = $_POST["name"];
        $price = $_POST["price"];
        $description = $_POST["description"];
        $yearModel = $_POST["yearModel"];
        //add image to folder and send data to db
        if(move_uploaded_file($_FILES["image"]["tmp_name"], $upload_path)){
            $results["added_new_car"] = true;
            //insert into db
            $sql = mysql_query("insert into `cars` 
                                    (name, price, description,image, modelYear) 
                                VALUES 
                                    ('$name','$price','$description','$imgName','$yearModel') ");
            if($sql){
                $results["error"] = false;
                $results["message"] = "Added New Car Successfully!";
                $results["added_data"] = true;
            }else{
                $results["error"] = true;
                $results["message"] = "Failed Saving Car Image, try again!";
                $results["added_data"] = false;
            }
        }else{
            $results["error"] = true;
            $results["message"] = "Failed Saving Car Image, try again!";
            $results["added_new_car"] = false;
        }
    }
}
if($action == "updateCar"){
    //files
    $imgName = "";
    $upload_path = "";
    $newImageUploaded = $_POST['newImageUploaded'];
    $oldImage = $_POST['oldImage'];
    if( isset($_FILES["image"]["name"]) && $newImageUploaded === "t"){
        $img_name = $_FILES["image"]["name"];
        $valid_extensions = array("png","jpg","jpeg","gif");
        $extensions = pathinfo($img_name, PATHINFO_EXTENSION);
        if(in_array($extensions, $valid_extensions)){
            $newGeneratedId = generateRandomString(75);
            $upload_path = "../assets/cars/". $newGeneratedId. ".".$extensions;
            if(file_exists($upload_path)){
                $newGeneratedId = generateRandomString(75);
                $imgName = $newGeneratedId. ".".$extensions;
            }else{
                $imgName = $newGeneratedId. ".".$extensions;
            }
        }else{
            $results["error"] = true;
            $results["message"] = "Car Image has to be : PNG, JPEG, JPG, or GIF";
        }
    }else{
        if($newImageUploaded === "t"){
            $results["error"] = true;
            $results["message"] = "Must Select Car Image!!";
        }
    }
    if($newImageUploaded === "f"){
        $imgName = $oldImage;
    }
    $results["newImageUploadedIs"] = $imgName;
    $name = $_POST["name"];
    $price = $_POST["price"];
    $id = $_POST["id"];
    $description = $_POST["description"];
    $yearModel = $_POST["yearModel"];
    //add image to folder and send data to db
    if($newImageUploaded === "t"){
        //erase old image
        $dir = "../assets/cars/".$oldImage;
        unlink($dir);
        //add new image
        if(move_uploaded_file($_FILES["image"]["tmp_name"], $upload_path)){
            $results["added_new_car"] = true;
        }else{
            $results["error"] = true;
            $results["message"] = "Failed Saving Copy of this Car Image, try again!";
            $results["added_new_car"] = false;
        }
    }
    //insert into db
    $sql =  mysql_query("update `cars` 
                            SET 
                                name = '$name',
                                price = '$price',
                                description = '$description',
                                image = '$imgName',
                                modelYear = '$yearModel'
                            WHERE 
                                id = '$id'
                        ");
    if($sql){
        $results["error"] = false;
        $results["message"] = "Updated Current Car Successfully!";
        $results["added_data"] = true;
    }else{
        $results["error"] = true;
        $results["message"] = "Failed Updating This Car Info, try again!";
        $results["added_data"] = false;
    }
}
//



echo json_encode($results);
//disconnect db
mysql_close($connect);
?>