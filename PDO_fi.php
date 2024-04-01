<?php
class PDO_fi{

    private $DATABASE_HOST = 'localhost';
    private $DATABASE_USER = 'root';
    private $DATABASE_PASS = '';
    private $DATABASE_NAME = 'test'; 
    public $conn;

    function __construct(){
        try {
            $this->conn= new PDO('mysql:host=' . $this->DATABASE_HOST . ';dbname=' . $this->DATABASE_NAME . ';charset=utf8', $this->DATABASE_USER, $this->DATABASE_PASS);
        } catch (PDOException $exception) {
            // If there is an error with the connection, stop the script and display the error.
            // echo $exception->getCode();
            exit('Failed to connect to database!');
        }
    }

    public function getData($sql,$data){
        $stmt= $this->conn->prepare($sql);
        $status=[
            "success"=>0,
            "error"=>"",
            "stmt"=>$stmt            
        ];
        try{           
            $stmt->execute($data);
            $status["success"]=true;
            return $status;
        }catch(PDOException $e){
            // echo "Error:".$e->getMessage();
            $status["error"]=$e->getMessage();
            return $status ;           
        }
    }

    public function update($sql,$data){
        $stmt=$this->conn->prepare($sql);
       
        try{
            $this->conn->beginTransaction();
            $stmt->execute($data);            
            $this->conn->commit();
            return ["status"=>1,"mesg"=>"Updated Successfully"];
        }catch(PDOException $e){
            // echo "Error:".$e->getMessage();
            $this->conn->rollback();
            return ["status"=>0,"mesg"=>"Error:".$e->getMessage()];
        }

    }


    public function delete($sql,$data){
        $stmt=$this->conn->prepare($sql);
       
        try{
            $this->conn->beginTransaction();
            $stmt->execute($data);            
            $this->conn->commit();
            return ["status"=>1,"mesg"=>"Deleted Successfully"];
        }catch(PDOException $e){
            // echo "Error:".$e->getMessage();
            $this->conn->rollback();
            return ["status"=>0,"mesg"=>"Error:".$e->getMessage()];
        }
    }

    public function insert($table,$fields){
        $table_col=implode(",",array_keys($fields));
        $placeholder=[];
        foreach(array_keys($fields) as $key){
            $placeholder[]=":".$key;
        }
        $placeholder=implode(",",$placeholder);

        $sql = "INSERT INTO $table ({$table_col}) VALUES ($placeholder)";
        $stmt=$this->conn->prepare($sql);
       
        try{
            $this->conn->beginTransaction();
            $stmt->execute($fields);
            $lastInsertedId=$this->conn->lastInsertId();
            $this->conn->commit();
            return $lastInsertedId;
        }catch(PDOException $e){
            // echo "Error:".$e->getMessage();
            $this->conn->rollback();
            return 0;
        }
    }

    public function uploadPhoto($file,$uploadFileDir){
        if(!empty($file)){
            $fileTempPath=$file[ 'tmp_name'];        
            $fileName=$file['name'];
            $fileType=$file['type'];
            $fileNameCmps=explode('.',$fileName);
            $fileExtension=strtolower(end($fileNameCmps));
            $newFileName=md5(time().$fileName).".".$fileExtension;
            $allowedExtn=["png","jpg","jpeg"];
    
            if(in_array($fileExtension,$allowedExtn)){                
                $destFilePath=$uploadFileDir . $newFileName;
                if(move_uploaded_file($fileTempPath,$destFilePath)){
                    return ["folder"=>$destFilePath,"filename"=>$newFileName];
                }    
            }
        }
    }

           

}


?>