<section class="content">
      
      <?php
         require_once(__DIR__.'/../common/headerView.php'); 
         require_once(__DIR__.'/../common/menuHeadView.php'); 
         
      ?>
      <pre>
        </pre>
        <div class="warper container-fluid">
        <hr class="no-margn">

           <div class="row">
            
                <div class="col-md-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading text-center" >Commentaire client</div>
                        <div class="panel-body">
                            <div class="col-lg-12">
                               <!--<div class="panel panel-primary">-->
                                <ul class="nav nav-tabs" role="tablist">
                                    <li class="active"><a href="#tabReponses" role="tab" data-toggle="tab">client</a></li>
                                  </ul>

                                  <div class="tab-content">

                                    <div class="panel panel-default tab-pane tabs-up active"  id="tabReponses">
                  
                                          <div class="panel-body">

                                              <table id="tableListeReponses" class="table table-bordered  table-striped table-hover">
                                                 <thead>
                                                    <tr>
                                                        <th rowspan="2">id</th>
                                                        <th scope="col" class="text-center" >Nom</th>
                                                        <th scope="col" class="text-center" >Prénom</th>
                                                        <th scope="col" class="text-center" >E-mail</th>
                                                        <th scope="col" class="text-center" >Téléphone</th>
                                                        <th scope="col" class="text-center" >Commentaire</th>
                                                        <!-- <th scope="col" >dateAjout</th> -->
                                                    </tr>
                                                </thead>
                                                    <tbody id="tbodyCommentaireTables">
                                                    <?php echo form_open("");
                                                      //die(print_r($data));

                                                        foreach ($clientRep as $value) { ?>
                                                          <tr>
                                                           <td><?php echo $value['id']; ?></td>
                                                          
                                                            <td><?php echo $value['nom']; ?></td>
                                                          
                                                            <td><?php echo $value['prenom']; ?></td>
                                                          
                                                            <td><?php echo $value['email']; ?></td>
                                                          
                                                            <td><?php echo $value['telephone']; ?></td>
                                                            
                                                            <td><?php echo $value['commentaire']; ?></td>
                                                          </tr>
                                                        <?php }
                                                    ?>
                                                    </tbody>
                                              </table>
                                              
                                          </div>
                                      
                                    </div>
                                      <!--Fin tabs commentaires -->
                                  </div>
                    
                          </div>
                      </div>
                  </div>
                </div>
          </div>
          <div class="form-group">
                   <?=form_hidden('base_url_name',base_url());?>
          </div>
          <?php require_once(__DIR__.'/../pop/viewReponsesModal.php'); ?>
    </div>
        <!-- Fin Wraper -->

        <!--Un petit Script de changement de la coloration du Menu-->
        <script type="text/javascript" src="<?php echo base_url('public/assets/js/jquery/jquery-1.9.1.min.js'); ?>"></script>
        <script type="text/javascript">
        $(document).ready(function($){
          /*-----POUR LES MENUS--*/
           $("#menuClients").toggleClass("active");//Ajouter la classe active sur le menu 

        });

        </script>