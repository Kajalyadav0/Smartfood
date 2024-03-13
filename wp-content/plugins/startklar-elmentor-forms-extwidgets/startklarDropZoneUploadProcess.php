<?php
namespace StartklarElmentorFormsExtWidgets;

class startklarDropZoneUploadProcess
{
    static function process()
    {
        $uploads_dir_info = wp_upload_dir();
        $user = wp_get_current_user();
        if( !isset($user) || !is_object( $user ) || !is_a( $user, 'WP_User' /*||  $user->ID*/) ) {
            /*header("HTTP/1.1 401 Unauthorized");
            die(__("ERROR! Access is allowed by authorized users only!", "startklar-elmentor-forms-extwidgets"));*/
            $user_id = 0;
        }else{
            $user_id = $user->ID;
        }
        if (in_array('administrator',  $user->roles)){ $admin_mode = 1;}



        if (!isset($_FILES["file"]) && !isset($_POST["mode"])) {
            die(__("There is no file to upload.", "startklar-elmentor-forms-extwidgets"));
        }
        $hash = sanitize_text_field($_POST["hash"]);
        if (!isset($hash) || empty($hash) ) {
            die(__("No HASH code match.", "startklar-elmentor-forms-extwidgets"));
        }

        if (isset($_POST["mode"]) && $_POST["mode"] == "remove" && isset($_POST["fileName"])){
            $fileName = sanitize_text_field($_POST["fileName"]);
            $newFilepath = $uploads_dir_info['basedir']."/elementor/forms/".$user_id."/temp/".$hash."/".$fileName;
            if (file_exists($newFilepath)) {
                unlink($newFilepath);
            }
            die();
        }


        $filepath = $_FILES['file']['tmp_name'];
        $fileSize = filesize($filepath);
        /*$fileinfo = finfo_open(FILEINFO_MIME_TYPE);
        $filetype = finfo_file($fileinfo, $filepath);*/

        if ($fileSize === 0) {
            die(__("The file is empty.", "startklar-elmentor-forms-extwidgets"));
        }
        /*if ($fileSize > (1024 * 1024 * 3)) { // 3 MB (1 byte * 1024 * 1024 * 3 (for 3 MB))
            die("The file is too large");
        }*/

        /*$file_ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
        if (!in_array($file_ext, ["pdf","txt","jpg"])) {
            die(__("File not allowed.", "startklar-elmentor-forms-extwidgets"));
        }*/


        $newFilepath = $uploads_dir_info['basedir']."/elementor/forms/".$user_id."/temp/".$hash."/".$_FILES['file']['name'];
        $target_dir = dirname($newFilepath);
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        if (!copy($filepath, $newFilepath)) { // Copy the file, returns false if failed
            die(__("Can't move file.", "startklar-elmentor-forms-extwidgets"));
        }
        unlink($filepath); // Delete the temp file
        //echo "File uploaded successfully :)";
        //json_encode(["test_name"=>"test_val"])
        die();

    }
}