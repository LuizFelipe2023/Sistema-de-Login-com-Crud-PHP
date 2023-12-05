<?php

class User{
      private $id;
      private $email;
      private $password;

      public function __construct($email,$password)
      {
             $this->email = $email;
             $this->password = $password;
      }
      public function getId(){
             return $this->id;
      }
      public function getEmail(){
             return $this->email;
      }
      public function setEmail($email){
             $this->email = $email;
      }
      public function getPassword(){
             return $this->password;
      }
      public function setPassword($password){
             $this->password = $password;
      }
}
interface userDaoInterface{
    public function createUser(User $user);
    public function login($email,$password);
    public function redefinePassword($email, $newPassword);
}