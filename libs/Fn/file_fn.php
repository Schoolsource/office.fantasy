<?php

class File_Fn extends _function
{

	public function type($type){

        switch ($type) {
            case "pdf": $typename = "application/pdf"; break;
            case "doc": $typename = "application/msword"; break;
            case "docx": $typename = "application/vnd.openxmlformats-officedocument.wordprocessingml.document"; break;
            case "exe": $typename = "application/octet-stream"; break;
            case "zip": $typename = "application/zip"; break;
            case "xls": $typename = "application/vnd.ms-excel"; break;
            case "ppt": $typename = "application/vnd.ms-powerpoint"; break;
            case "gif": $typename = "image/gif"; break;
            case "png": $typename = "image/png"; break;
            case "jpe": 
            case "jpeg": 
            case "jpg": $typename = "image/jpg"; break;
            default: $typename = ""; break;
        }

        return $typename;
	}

    public function validate(&$err, $userfile=null, $type=null, $max_size=25){
        $err = "";
        $userfile = empty($userfile) ? $_FILES['file'] : $userfile;

        if(!is_uploaded_file($userfile['tmp_name'])){
            $err = "ส่งไฟล์ไม่สำเร็จ! ";

            if(($userfile['error']==UPLOAD_ERR_INI_SIZE) or ($userfile['error']==UPLOAD_ERR_FORM_SIZE))
                $err .= "ไฟลืมีขนาดใหญ่กว่าที่กำหนด";
            elseif($userfile['error']==UPLOAD_ERR_PARTIAL)
                $err .= "ข้อมูลของไฟล์ถูกส่งมาไม่ครบ";
            elseif($userfile['error']==UPLOAD_ERR_NO_FILE)
                $err .= "คุณไม่ได้เลือกไฟล์ที่จะส่ง";

        }else{

            $max_size = $max_size*1024*1024;
            if($userfile['size'] > $max_size)
                $err .= "ไฟล์มีขนาดใหญ่กว่าที่กำหนด {$max_size}MB";

            if(!empty($type)){
                if(is_array($type)){
                    $error_types = "";
                    $_types = false;

                    foreach ($type as $key) {
                        $error_types .= !empty($error_types)? ", ":"";
                        $error_types .= "{$key}";

                        if($userfile['type'] == $this->type($key) ) $_types = true;
                    }

                    if($_types===false)
                        $err = "ไฟล์ที่ส่งมาไม่ได้อยู่ในรูปแบบ {$error_types}";

                }elseif($userfile['type'] != $type)
                    $err .= "ไฟล์ที่ส่งมาไม่ได้อยู่ในรูปแบบ {$type}";
                
            }
        }

        if($err)
        return false;
        else return true;
    }
}