/*
*Eric Dos Santos
*
/

/*--------LES VARIABLES UTILES--------*/
var base_url = $('input[name="base_url_name"]').val();
var Status = function() { //Object Question
    this.statusGeneraleValue = 0;
    this.statusStatistiquesValue = 0;
    this.statusParticulierValue = 0;
    this.statusProfessionnelValue = 0;
}

var keywordidvalue=1;
var table=$('#tableListeQuestions');

/*---------------------*/
$(function() {
	var status = new Status();

    moment.locale('fr').format('LLL');
    /*----CHARGEMENTS DES ELEMENTS-----*/

    $('input[name="idDatePop"]').val(moment().format('DD/MMM/YYYY'));

    /*----CHARGEMENT DES EVENTS------------*/
    $(document).on('change','input[type=checkbox]',function() {

        var idButton=this.id;
        var valIDElement= $(this).closest('td').siblings(':first-child').text();
        var boolChecked= this.checked;

        updateDataIfCheckBoxCheked(idButton,valIDElement,boolChecked);
    });

   $('a[href="#tabQuestions"]').on('shown.bs.tab', function(e) {
      chargementDeTableQuestions();
    });



    $('a[data-toggle="tab"]').trigger('shown.bs.tab');
   

    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
      e.preventDefault();
        var target = $(e.target).attr("href") // activated tab
         
         switch(target){
         	case '#tabQuestions':
         	break;
         	case '#tabReponses':
            chargementDeTableReponses();
         	break;
         	default:
         	 alert('Erreur détécté');
         } 
    });


    $("#btnAddQuestion").on('click', function() {
        openModal();
    });

    $('#modalCreateQuestion').on('shown.bs.modal', function () {
    	setKeywordInputValue();
		  $("#idlibelleQuestionPop").focus();
	});

    $('#idKeywordPop').on('change', function() {
        keywordidvalue=(this.value);
    });
    /*------FONCTIONS POUR LES MODIFICATIONS DES CHECKBOXS DANS POP---*/
    $('input[type=checkbox]').on('change', function() {
        var id = this.id;
        var isChecked = this.checked ? 1 : 0;
        getSwitchButtonValueChecked(id, isChecked);
        return;
    });

    $("#btnValiderPop").on('click', function(event) {
    	event.preventDefault();
        sendDataToTableQuestionByAjax();
    });
    
    /*--APPEL AJAX pour les nouvelles questions dans la base de donnée---------*/

     function chargementDeTableQuestions(){

        $.ajax({
              type: "GET",
              url:base_url+'getQuestionsFromDB',
              dataType: 'json',
              success: function(quest) {

                var quest = jQuery.parseJSON(JSON.stringify(quest));

               $.each(quest, function(key, value){
                var i=1;
                var questVal=quest[key];

                var $TableRow = $('<tr></tr>'),
                 $TableDataColID='<td>'+questVal.id_question+'</td>',
                 $TableDataColQuestion='<td>'+questVal.libelle+'</td>',
                 $TableDataColActif='<td><div class="switch-button lg showcase-switch-button"><input id="switch-button-actifs'+questVal.id_question+'" '+isCheckBoxChecked(questVal.statusGenerale)+' type="checkbox"><label for="switch-button-actifs'+questVal.id_question+'"></label></div></td>',
                 $TableDataColStats='<td><div class="switch-button lg primary showcase-switch-button"><input id="switch-button-stats'+questVal.id_question+'" '+isCheckBoxChecked(questVal.statusStatistiques)+' type="checkbox"><label for="switch-button-stats'+questVal.id_question+'"></label></div></td>',
                 $TableDataColPart='<td><div class="switch-button lg info showcase-switch-button"><input id="switch-button-particulier'+questVal.id_question+'" '+isCheckBoxChecked(questVal.statusParticulier)+' type="checkbox"><label for="switch-button-particulier'+questVal.id_question+'"></label></div></td>',
                 $TableDataColPro='<td><div class="switch-button lg warning showcase-switch-button"><input id="switch-button-pro'+questVal.id_question+'" '+isCheckBoxChecked(questVal.statusProfessionnel)+' type="checkbox"><label for="switch-button-pro'+questVal.id_question+'"></label></div></td>',
                 $TableDataColMotsCles='<td>'+questVal.motscles+'</td>',
                 $TableDataColDateAjout='<td>'+questVal.dateAjout+'</td>';

                var $AllTD=$TableRow.append($TableDataColID).append($TableDataColQuestion).append($TableDataColActif).append($TableDataColStats).append($TableDataColPart).append($TableDataColPro).append($TableDataColMotsCles).append($TableDataColDateAjout);

                $("#tbodyQuestionsTables").append($AllTD);
                  i++;
              });
               
               table.dataTable().fnDestroy();

              table.DataTable({
                lengthMenu: [ [5, 10, -1], [5, 10, "All"] ],
                pageLength: 5,
                  "language": {
                    "url":"public/assets/json/French.json"
                  }
                });

               table.on( 'xhr', function () {
                  var json = table.ajax.json();
              } );

              },
              error: function() {
                  alert('Erreur de récupération des données!');
              }

        });
      }

    function chargementDesModifsDuTable() {

      var starListPlein='<i class="fa fa-star text-warning"></i>';
      var starListVide='<i class="fa fa-star "></i>';

      $('.txtStar').each(function() {
          var satisfValue = $(this).text();
          if(satisfValue){
              $(starListPlein).appendTo(".txtStar");
           }
        });
    }

    function sendDataToTableQuestionByAjax() {
        $.ajax({
            type: "POST",
            url: 'sendQuestionsToDB',
            dataType: 'json',
            data: {
            	token: $("input[name='token']").val(),
                libelle: $("#idlibelleQuestionPop").val(),
                keywordid: keywordidvalue,
                statusGenerale: status.statusGeneraleValue,
                statusStatistiques: status.statusStatistiquesValue,
                statusParticulier: status.statusParticulierValue,
                statusProfessionnel: status.statusProfessionnelValue,
            },
            success: function(res) {
            	if(res){
            		notie.alert(1, 'Insertion avec succès !', 2);
            		$('#modalCreateQuestion').modal('hide');
            	}
            },
             error: function() {
                  notie.alert(1, 'Erreur de récupération des données! !', 3);
              }
        });
    }

    function setKeywordInputValue() {
        $.getJSON("getDataCategorieFromDB", function(data) {
            $('#idKeywordPop').empty();
            $.each(data, function(index, value) {
              $('<option value="' + data[index].id + '">' + data[index].motscles + '</option>"').appendTo("#idKeywordPop");
            });
        });
    }

    function getSwitchButtonValueChecked(id, isChecked) {
        switch (id) {
            case 'switch-button-actifsPop':
                status.statusGeneraleValue = isChecked;
                break;
            case 'switch-button-statsPop':
                status.statusStatistiquesValue = isChecked;
                break;
            case 'switch-button-proPop':
                status.statusProfessionnelValue = isChecked;
                break;
            case 'switch-button-particulierPop':
                status.statusParticulierValue = isChecked;
                break;
            default:
                return 0;
        }
    }

    function isCheckBoxChecked(status){
        var valRetourne='';
        if(status==1){
            valRetourne = 'checked';
        }
        return valRetourne;        
    } 

    function updateDataIfCheckBoxCheked(idButton,valIDElement,isChecked){
      var dataToSend={
        token: $("input[name='token']").val(),
        keywordid:parseInt(valIDElement)
      };
        
        switch(idButton){
          case 'switch-button-actifs'+valIDElement:
            dataToSend['statusGenerale'] = isChecked?1:0;
            updateElementOnCheckBoxChange(dataToSend);
            break;
          case 'switch-button-stats'+valIDElement:
            dataToSend['statusStatistiques'] = isChecked?1:0;
            updateElementOnCheckBoxChange(dataToSend);
            break;
          case 'switch-button-particulier'+valIDElement:
            dataToSend['statusParticulier'] = isChecked?1:0;
            updateElementOnCheckBoxChange(dataToSend);
            break;
          case 'switch-button-pro'+valIDElement:
            dataToSend['statusProfessionnel'] = isChecked?1:0;
            updateElementOnCheckBoxChange(dataToSend);
            break;
          case 'remember':
            break;
           default:
             ;
        }
        
    }


    function updateDataIfCheckBoxUnchecked(idButton,valIDElement){

       console.log('Id du Bouton:'+idButton+'-- ID dans la base:'+valIDElement);
    }

    function updateElementOnCheckBoxChange(data){

      console.log("Dans la fonction update:"+JSON.stringify(data, null, "  "));
      var url="QuestionnaireController/updateIfQuestionChange";
             
      $.ajax({
          type: "POST",
          url:url,
          dataType: 'json',
          data:data,
          success: function(res) {
            notie.alert(2, 'Update avec succès!',2);
            console.log((res));
          },
          error: function(jqXHR, exception) {
            alert('Erreur de récupération des données!'+ jqXHR.responseText);
          }
      });
    }

    function openModal(){
       $('#modalCreateQuestion').modal();
    }
});