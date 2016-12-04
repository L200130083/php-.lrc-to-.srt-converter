<?php
class Upload
{
    private $error = NULL;
    private $result;
    function __construct()
    {

        $this->max_size    = 1048576; //1 MiB
        $this->extension   = array('lrc'); //allowed extension
        $this->upload_path = "lrc/"; //upload directory
    }
    function do_upload($field = FALSE)
    {
		if ( ! $field){
			$this->error = "Please set field name";
			return FALSE;
		}
        if (empty($_FILES[$field]['name']))
        {
            $this->error = 'No FIle Selected!';
            return FALSE;
        }
        $file_name = $_FILES[$field]['name'];
        $file_size = $_FILES[$field]['size'];
        $file_tmp  = $_FILES[$field]['tmp_name'];
        $file_type = $_FILES[$field]['type'];
        $extension = @end(explode('.', $file_name));
        if (!in_array($extension, $this->extension)) //check file extension
        {
            $this->error = "File Extension Not Allowed";
            return FALSE;
        }
        
        if ($file_size > $this->max_size) //exceed max upload size?
        {
            $this->error = "File Size must be less than $this->max_size Bytes";
            return FALSE;
        }
        if ($this->error != NULL) //if any error occured return false
        {
            return FALSE;
        }
        move_uploaded_file($file_tmp, $this->upload_path . $file_name); //move it
        $this->result['file_name'] = $file_name;
        //$this->result['file_size'] = $file_size;
        $this->result['file_path'] = $this->upload_path . $file_name;
        return TRUE;
    }
    function error()
    {
        return $this->error;
    }
    function data()
    {
        return $this->result;
        
    }
}
?>