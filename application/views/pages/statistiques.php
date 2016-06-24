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
                        <div class="panel-heading text-center" >Statistiques</div>
                        <div class="panel-body">
                            <div class="col-lg-12">
                               <!--<div class="panel panel-primary">-->
                                <ul class="nav nav-tabs" role="tablist">
                                    <li class="active"><a href="#tabReponses" role="tab" data-toggle="tab">Stats</a></li>
                                  </ul>

                                  <div class="tab-content">

                                    <div class="panel panel-default tab-pane tabs-up active"  id="tabReponses">
                  
                                          <div class="panel-body">

                                               <table id="tableListeStats" class="table table-bordered  table-striped">

                                                <thead>
                                                      <tr>
                                                        <th>MotsCles</th>
                                                        <th scope="col" class="text-center" >Particulier</th>
                                                        <th scope="col" class="text-center" >Professionnel</th>
                                                        <th scope="col"  class="text-center">Moyenne</th>
                                                      </tr>
                                                </thead>
                                                 <colgroup>
                                                    <col>
                                                    <col class="col-md-4">
                                                </colgroup>
                                                <tbody id="tbodyUserTables">
                                                <?php foreach ($statsByKeyword as $row):?>
                                                  <tr>
                                                          <td><?php echo htmlspecialchars($row[0],ENT_QUOTES,'UTF-8');?></td>
                                                          <td><?php echo htmlspecialchars($row[1],ENT_QUOTES,'UTF-8');?></td>
                                                          <td><?php echo htmlspecialchars($row[2],ENT_QUOTES,'UTF-8');?></td>
                                                          <td><?php echo htmlspecialchars($row[3],ENT_QUOTES,'UTF-8');?></td>
                                                  </tr>
                                                <?php endforeach;?>

                                                </tbody>
                                              </table>
                                          </div>
                                      
                                    </div>
                                      <!--Fin tabs reponses -->
                                  </div>
                    
                          </div>
                      </div>
                  </div>
                </div>
          </div>
          <div class="form-group">
                   <?=form_hidden('base_url_name',base_url());?>
          </div>
    </div>
        <!-- Fin Wraper -->

        <!--Un petit Script de changement de la coloration du Menu-->
        <script type="text/javascript" src="<?php echo base_url('public/assets/js/jquery/jquery-1.9.1.min.js'); ?>"></script>
        <script type="text/javascript">
        $(document).ready(function($){
          /*-----POUR LES MENUS--*/
           $("#menuStatistiques").toggleClass("active");//Ajouter la classe active sur le menu 

        });

        </script>