<?php
defined('BASEPATH') OR exit('No direct script access allowed');

Class ReponsesModel extends CI_Model
  {

    public function insertResponses($data){

      $res=FALSE;
      
       if($this->db->insert('reponses', $data))
        {
          $res=TRUE;
        }
      return $res; 
    }

    public function selectAllResponses() {
         $arr = array();

        $query = $this->db->get('reponses');

        foreach ($query->result_array() as $row)
          {
              $arr[]=$row;
          }
          return $arr;
    }


    public function getAllUnserializedReponses() {

       $arr = array();

        $query = $this->db->get('reponses');

        foreach ($query->result_array() as &$row)
        {
          $row=$this->util->perform_changes_on($row);

          $arr[]=$row;
        }
        
        return $arr;
              
    }

    public function getCommentaireClient() {

      // $this->db->
      $query = $this->db->get('client');
 
        $resultat = $query->result_array();
 
 
        if (empty($resultat)) {
            return false;
        } else {
            return $resultat;
        }
    }


    public function getQuestionsById($id) {

      $arr=array();

      $arrayConditions=array('id_question' => $id);
      $this->db->select('id_question,motscles,statusStatistiques,statusParticulier,statusProfessionnel');
      $this->db->join('categorie', 'categorie.id = question.keywordId');
      $this->db->where($arrayConditions);
      $query=$this->db->get('question');

       foreach ($query->result_array() as $row)
          {
              $arr[]=$row;
          }
          return $arr;

    }

    function array_combine_($keys, $values){
        $result = array();

        foreach ($keys as $i => $k) {
         $result[$k][] = $values[$i];
         }

        array_walk($result, function(&$v){
         $v = (count($v) == 1) ? array_pop($v): $v;
         });

        return $result;
    }

    
    public function getAllReponsesByCategorieSatisfaitMoy() 
    {
       
       $unserializedReponses=$this->getAllUnserializedReponses();
       $arrayQuestions=array();
       $arrayReponses=array();
       
       $arrayMotsClesEtReponses=array();
       $arrayOfAllMotsClesEtReponses=array();

       foreach ($unserializedReponses as &$arrRep) {
        //echo "---------------------------------------";

          foreach ($arrRep as $key => &$value) {
            
            if($key=='idquestions') {


              $arrayMotsCles=array();

              for ($i=0; $i < count($value) ; $i++) { 
                

                  $arrayMotsCles[$i]=$this->getQuestionsById($value[$i])[0]['motscles'];


              }
            } elseif ($key=='reponses_recu') {
                
                $arrayReponses=$value;

            }

         }

         $arrayMotsClesEtReponses=$this->array_combine_($arrayMotsCles,$arrayReponses);
          

         $arrayOfAllMotsClesEtReponses[]=$arrayMotsClesEtReponses;

         
      }


      return $arrayOfAllMotsClesEtReponses;


    }

    public function getAllReponsesByCategorieSatisfaitPart() 
    {
       
       $unserializedReponses=$this->getAllUnserializedReponses();
       $arrayQuestions=array();
       $arrayReponses=array();
       
       $arrayMotsClesEtReponses=array();
       $arrayOfAllMotsClesEtReponses=array();

       foreach ($unserializedReponses as &$arrRep) {


          foreach ($arrRep as $key => &$value) {
            
            if($key=='idquestions') {


              $arrayMotsCles=array();


              for ($i=0; $i < count($value) ; $i++) { 

                  if($this->getQuestionsById($value[$i])[0]['statusParticulier']){
                    $arrayMotsCles[$i]=$this->getQuestionsById($value[$i])[0]['motscles'];
                  }

              }
            } elseif ($key=='reponses_recu') {
                
                $arrayReponses=$value;

            }

         }

         $arrayMotsClesEtReponses=$this->array_combine_($arrayMotsCles,$arrayReponses);
          

         $arrayOfAllMotsClesEtReponses[]=$arrayMotsClesEtReponses;


         
      }

      return $arrayOfAllMotsClesEtReponses;

    }

   public function getAllReponsesByCategorieSatisfaitPro() 
    {
       
       $unserializedReponses=$this->getAllUnserializedReponses();
       $arrayQuestions=array();
       $arrayReponses=array();
       
       $arrayMotsClesEtReponses=array();
       $arrayOfAllMotsClesEtReponses=array();

       foreach ($unserializedReponses as &$arrRep) {

          foreach ($arrRep as $key => &$value) {
            
            if($key=='idquestions') {
              $arrayMotsCles=array();
              for ($i=0; $i < count($value) ; $i++) { 
                

                  if($this->getQuestionsById($value[$i])[0]['statusProfessionnel']){
                    $arrayMotsCles[$i]=$this->getQuestionsById($value[$i])[0]['motscles'];
                  }

              }
            } elseif ($key=='reponses_recu') {
                
                $arrayReponses=$value;

            }

         }

         $arrayMotsClesEtReponses=$this->array_combine_($arrayMotsCles,$arrayReponses);
          

         $arrayOfAllMotsClesEtReponses[]=$arrayMotsClesEtReponses;


         
      }

      return $arrayOfAllMotsClesEtReponses;

    }

    public function array_combine_complexe($keys,$values) {

      $combined = array();

      foreach ($keys as $index => $element) {
              $combined[$index] = array('key' => $element, 'value' => $values[$index]);
      }

      return $combined;

    }


    public function insertClient($data) {
      
      $this->db->insert('client', $data); 
      $insert_id = $this->db->insert_id();

         return  $insert_id;
    }

  }