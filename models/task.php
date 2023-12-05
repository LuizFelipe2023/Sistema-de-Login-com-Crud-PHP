<?php
class Task{
      private $id;
      private $title;
      private $description;
      private $status;
      private $userEmail;

      public function __construct($id,$title,$description,$status)
      {
             $this->id = $id;
             $this->title = $title;
             $this->description = $description;
             $this->status = $status;
      }
      public function getId(){
             return $this->id;
      }
      public function setId($id){
             $this->id = $id;
      }
      public function getTitle(){
             return $this->title;
      }
      public function setTitle($title){
             $this->title = $title;
      }
      public function getDescription(){
             return $this->description;
      }
      public function setDescription($description){
             $this->description = $description;
      }
      public function getStatus(){
             return $this->status;
      }
      public function setStatus($status){
             $this->status = $status;
      }
      public function getUserEMail(){
             return $this->userEmail;
      }
      public function setUserEmail($userEmail){
             $this->userEmail = $userEmail;
      }
}
interface taskDaoInterface{
    public function createTask(Task $task, $userEmail);
    public function getAllTasks($userEmail);
    public function getTaskByid($id);
    public function updateTask(Task $task,$userEmail,$id);
    public function deleteTask($taskId,$userEmail);
}