<?php

// ini_set('gd.jpeg_ignore_warning', 1);

class Upload {

    protected $options;

    function __construct() {

        $this->fn = new _function();
        
        $this->options = array(

            'mkdir_mode' => 0755,

            'image_type' => array('image/gif','image/png','image/jpeg','image/pjpeg'),

            'readfile_chunk_size' => 10 * 1024 * 1024, // 10 MiB

            'image_size' => array(
                'area' => array(260,260),
                'normal' => array(1280,960)
            )
        );
    }

    public function quad($path, $max_size = array(950, 950), $options=array()) {
        list($width, $height) = getimagesize($path);

        if( $width > $height && $width < $max_size[0] ){
            $max_size = array($width, $width);
        }
        elseif( $height < $max_size[1] ){
            $max_size = array($height, $height);
        }

        $desired[0] = $width;
        $desired[1] = $height;

        if( isset($options['full']) ){
            if( $width > $height ){
                $desired[0] = $max_size[0];
                $desired[1] = round( ( $max_size[0]*$height ) / $width );
            }
            else{
                $desired[1] = $max_size[1];
                $desired[0] = round( ( $max_size[1]*$width ) / $height );
            }
        }

        $dst = array(0,0);
        if($desired[0]>$desired[1]){
            $dst[1] = ($max_size[1]/2)-($desired[1]/2);
        }
        else if($desired[1]>$desired[0]){
            $dst[0] = ($max_size[0]/2)-($desired[0]/2);
        }

        $imageTmp = $this->import( $path );

        $imageNew = imagecreatetruecolor($max_size[0], $max_size[1]);
        imagealphablending( $imageNew, false );
        imagesavealpha( $imageNew, true );

        $whiteBackground = imagecolorallocate($imageNew, 255, 255, 255); 
        imagefill( $imageNew, 0, 0, $whiteBackground );

        imagecopyresampled($imageNew, $imageTmp, $dst[0], $dst[1], 0, 0, $desired[0], $desired[1], $width, $height);

        switch( $this->getType($path) ){
            case 'bmp': imagewbmp($imageNew, $path); break;
            case 'gif': imagegif($imageNew, $path); break;
            case 'jpg':  case 'jpeg': imagejpeg($imageNew, $path); break;
            case 'png': imagepng($imageNew, $path, 0); break;
        }
    }

    public function minimize($path, $max_size = array(950, 950)){

        list($width, $height) = getimagesize($path);

        if( $width > $height && $width > $max_size[0] ){
            $desired[0] = $max_size[0];
            $desired[1] = round( ( $max_size[0]*$height ) / $width );
        }
        else if($height > $max_size[1]){
            $desired[1] = $max_size[1];
            $desired[0] = round( ( $max_size[1]*$width ) / $height );
        }

        if( !empty($desired) ){
            $imageTmp = $this->import( $path );
            $new_img = imagecreatetruecolor($desired[0], $desired[1]);
            imagealphablending( $new_img, false );
            imagesavealpha( $new_img, true );
            @imagecopyresampled($new_img, $imageTmp, 0, 0, 0, 0, $desired[0], $desired[1], $width, $height);

            switch( $this->getType($path) ){
                case 'bmp': imagewbmp($new_img, $path); break;
                case 'gif': imagegif($new_img, $path); break;
                case 'jpg':  case 'jpeg': imagejpeg($new_img, $path); break;
                case 'png': imagepng($new_img, $path, 0); break;
            }
        }
    }

    public function imageToJpg($originalImage, $outputImage, $quality=100) {
        if( $this->getType($originalImage)!='jpg' ){

            $imageTmp = $this->import( $originalImage );
            imagejpeg($imageTmp, $outputImage, 100);

            /*$final = imagecreatetruecolor($tn_w, $tn_h);
            $backgroundColor = imagecolorallocate($final, 255, 255, 255);*/

            imagedestroy($imageTmp);
            unlink($originalImage);
        }
    }

    public function convertImage($originalImage, $outputImage, $quality=100) {

        if( !file_exists($originalImage) ) return false;

        $imageTmp = $this->import( $originalImage );
        if( $imageTmp ){

            // quality is a value from 0 (worst) to 100 (best)
            imagejpeg($imageTmp, $outputImage, $quality);
            imagedestroy($imageTmp);
        }
        

    }
    public function lined($source, $dest) {
        
        $png = imagecreatefrompng($source);
        $jpeg = imagecreatefromjpeg($dest);

        list($newwidth, $newheight) = getimagesize( $source );
        list($width, $height) = getimagesize( $dest );

        $out = imagecreatetruecolor($newwidth, $newheight);
        imagecopyresampled($out, $jpeg, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
        imagecopyresampled($out, $png, 0, 0, 0, 0, $newwidth, $newheight, $newwidth, $newheight);

        imagejpeg($out, $dest, 100);
    }
    public function cropimage( $data, $dest ) {

        list($width, $height) = getimagesize($dest);
        $newwidth = $data['width'];
        $newheight = ($newwidth*$height) / $width;

        $src_image = $this->import($dest);
        $dst_image = imagecreatetruecolor($data['width'], $data['height']);
        imagealphablending( $dst_image, false );
        imagesavealpha( $dst_image, true );

        $dst_x = intval( isset($data['x']) ? $data['x']:$data['X'] );
        $dst_y = intval( isset($data['y']) ? $data['y']:$data['Y'] );

        imagecopyresized($dst_image, $src_image, 0,0, $dst_x, $dst_y, $data['width'], $data['height'], $data['width'], $data['height']);

        switch( $this->getType($dest) ){
            case 'bmp': imagewbmp($dst_image, $dest); break;
            case 'gif': imagegif($dst_image, $dest); break;
            case 'jpg':  case 'jpeg': imagejpeg($dst_image, $dest); break;
            case 'png': imagepng($dst_image, $dest, 9); break;
        }
    }
    public function import($path) {

        switch( $this->getType($path) ){
            case 'bmp': return imagecreatefromwbmp($path); break;
            case 'gif': return imagecreatefromgif($path); break;
            case 'jpg': case 'jpeg': return @imagecreatefromjpeg($path); break;
            case 'png': return @imagecreatefrompng($path); break;
            default:
                throw new Exception('Invalid image: '.$path);
            break;
        }
    }

    // 
    public function copies($source, $dest){
        //print_r($dest); die;
        return copy($source, $dest);
    }

    // ตรวจสอบ ไฟล์ : ใช้ไปแล้ว
    public function lists( $resulfs ){
        $data = array();

        for ($i=0; $i < count($resulfs['tmp_name']); $i++) { 

            $data[] = array(
                'error'=> $resulfs['error'][$i],
                'name'=> $resulfs['name'][$i],
                'size'=> $resulfs['size'][$i],
                'tmp_name'=> $resulfs['tmp_name'][$i],
                'type'=> $resulfs['type'][$i],
                'extension' => $this->getExtension($resulfs['name'][$i]),
                'caption_text' => !empty($_POST['caption_text'][$i]) ? $_POST['caption_text'][$i]: ''
            );
        }

        return $data;
    }

    // ใช้ไปแล้ว
    public function convert_size( $size, $unit='MB'){
        switch ( strtoupper($unit) )  {
            case 'MB':
                return ($size/1024)/1024;
                break;
            
            default: // bytes
                return $size;
                break;
        }
    }
    // ตรวจสอบ ไฟล์ : ใช้ไปแล้ว
    public function validate( &$err, $options=array() ){
        
        $options = array_merge(array(
            'max_size' => 25,
            'unit_size' =>'MB',
        ), $options);

        if (!is_uploaded_file($this->current['tmp_name'])) {
            $err = 'ส่งไฟล์ไม่สำเร็จ เห็ตุผลคือ';

            if (($this->current['error'] == UPLOAD_ERR_INI_SIZE) or ($this->current['error'] == UPLOAD_ERR_FORM_SIZE)) {
                $err .= 'ภาพนี้มีขนาดใหญ่เกินไปที่จะอัปโหลด เพื่อให้ได้ผลลัพธ์ที่ดีที่สุด, ปรับขนาดภาพให้อยู่ภายใต้ 4MB, และลองอีกครั้ง';
            } elseif ($this->current['error'] == UPLOAD_ERR_PARTIAL) {
                $err .= 'ข้อมูลที่ส่งมาไม่ครบ';
            } elseif ($this->current['error'] == UPLOAD_ERR_NO_FILE) {
                $err .= 'คุณไม่ได้เลือกไฟล์ที่จะส่ง';
            }
        }

        else if( $this->convert_size( $this->current['size'], $options['unit_size'] ) > $options['max_size'] ){
            $err = "ไฟล์มีขนาดเกินที่กำหนด ({$options['max_size']} {$options['unit_size']})";
        }

        if( isset($options['type']) ){

            if( !in_array( $this->getType($this->current['name']), $this->getListType($options['type']) ) ){
                $err = 'รูปแบบไฟล์ไม่ถูกต้อง';
            }
        }

        if ( $err ) return false;
        else return true;
    }
    // ใช้ไปแล้ว
    public function getListType($type){
        
        switch ($type) {
            case 'photo':
            case 'picture':
            case 'image':
                return array('png','jpe','jpeg','jpg','gif','bmp');
                break;

            case 'video':
                return array('mp4');
                break;

            case 'file':
                return array(
                    'txt',

                    'zip','rar',

                    'pdf',

                    // office
                    'ppt','pptx','doc','docx','xls',
                    'odt','ods'
                );
                break;

            case 'pdf':
                return array('pdf');
                break;

            case 'word':
                return array('doc','docx');
                break;
            
            default:
                return array();
                break;
        }
    }

    /* ใช้ไปแล้ว ที่ไฟล: donwload */
    public function getMimeType($filename){

        $mime = $this->mime_content_type($filename);
        if(strstr($mime, "video/")){
            return 'video';
        }else if(strstr($mime, "image/")){
            return 'images';
        }
        else{
            return 'files';
        }
    }
    public function getType($filename) {
        return strtolower(substr(strrchr($filename,"."),1));
    }
    public function getExtension($filename){
        //strtolower(array_pop(explode('.',$filename)));
        return strtolower(strrchr($filename, '.'));
    }
    public function fix_file_extension($name){

        // Add missing file extension for known image types:
        if (strpos($name, '.') === false && 
                preg_match('/^image\/(gif|jpe?g|png)/', $type, $matches)) {
            $name .= '.'.$matches[1];
        }

        return $name;
    }
    public function formatSizeUnits($bytes) {
        if      ($bytes>=1073741824) {$bytes=round(($bytes/1073741824),2).' GB';}
        else if ($bytes>=1048576)    {$bytes=round(($bytes/1048576),2).' MB';}
        else if ($bytes>=1024)       {$bytes=round(($bytes/1024),2).' KB';}
        else if ($bytes>1)           {$bytes=$bytes.' bytes';}
        else if ($bytes==1)          {$bytes=$bytes.' byte';}
        else                        {$bytes='0 byte';}
        return $bytes;
    }
    public function mime_content_type($filename) {

        $mime_types = array(
            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',

            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',

            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',

            // audio
            'mp3' => 'audio/mpeg',

            // video
            'qt' => 'video/quicktime',
            'mp4' => 'video/mp4',
            'mov' => 'video/quicktime',

            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',

            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',

            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );

        $ext = $this->getExtension($filename);

        if (array_key_exists($ext, $mime_types)) {
            return $mime_types[$ext];
        }
        elseif (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME);
            $mimetype = finfo_file($finfo, $filename);
            finfo_close($finfo);
            return $mimetype;
        }
        else {
            return 'application/octet-stream';
        }
    }
    public function getContentType($type) {
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

}