<?php defined('BASEPATH') OR exit('No direct script access allowed');

class QuestionnaireController extends CI_Controller {

	private $data;
	private $dataResponses;

	/**
	 * {Cette fonction correspond au constructeuur de ma Classe}
	 */
	function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * {Cette fonction sert à tester si nécessaire que l'utilisateur se connecte et que session existe}
	 */
	function index()
	{
		 //Enlever le commentaire si l'utilisateur doit être connecté pour répondre au questionnaire
		if (!$this->ion_auth->logged_in())
		{
			// Redirection vers la page de login
			redirect('login', 'refresh');
		}
	}


	/**
	 * {Cette fonction affiche le view questionnaire pour les clients}
	 */
	function load_questions(){
		//Ici tu appel ton model 
		$this->data=$this->questionModel->getQuestionActiforParticulier();
		$data['resultatArray']=$this->data;

		$this->loadPage('questionnaire',0,$data);

	}

	/**
	 * Cette fonction affiche le view questionnaire pour le pro
	 */
	function load_questions_pro() {

		//$this->index() Si besoin de se connecter avant d'arriver sur la page
		$this->data=$this->questionModel->getQuestionForProfessionnel();
		$data['resultatArray']=$this->data;

		$this->loadPage('questionnairepro',0,$data);
		
	}

	/**
	 * { Cette fonction genère la vue pour les modifications des questions clients}
	 */
	function load_modifications(){

		//Controle de la session
		$this->index();
		$user = $this->ion_auth->user()->row();
		

		//Récupération des données du model
		$data['resultatArrayAllQuestion']=$this->questionModel->getAllQuestion();
		$data['Nom']=$user->last_name;
		$data['Prenom']=$user->first_name;

		$this->loadPage('modificationQuestions',1,$data);

	}

	function load_statistique() {
		//Controle de la session
		$this->index();
		$user = $this->ion_auth->user()->row();
		

		//Récupération des données du model
		$data['resultatArrayAllQuestion']=$this->questionModel->getAllQuestion();
		$data['Nom']=$user->last_name;
		$data['Prenom']=$user->first_name;

		$data['statsByKeyword']=$this->getAllKeywordStats();



		$this->loadPage('statistiques',1,$data);

	}

	function load_client() {
		//Controle de la session
		$this->index();
		$user = $this->ion_auth->user()->row();	

		//Récupération des données du model
		// $data['resultatArrayAllQuestion']=$this->questionModel->getAllQuestion();
		$data['Nom']=$user->last_name;
		$data['Prenom']=$user->first_name;

		$data['clientRep']=$this->reponsesModel->getCommentaireClient();
		// $this->$clientRep;
		$this->loadPage('client',1,$data);

	}


	function load_modif_utilisateur() {

		//Controle de la session
		$this->index();
		$user = $this->ion_auth->user()->row();

		//Récupération des données du model
		$this->data['resultatArrayAllQuestion']=$this->questionModel->getAllQuestion();
		$this->data['Nom']=$user->last_name;
		$this->data['Prenom']=$user->first_name;
		//list the users
		$this->data['users'] = $this->ion_auth->users()->result();
			foreach ($this->data['users'] as $k => $user)
			{
				$this->data['users'][$k]->groups = $this->ion_auth->get_users_groups($user->id)->result();
			}

		$this->loadPage('modificationUtilisateurs',1,$this->data);

	}

	function load_parametrages() {

		//Controle de la session
		$this->index();
		$user = $this->ion_auth->user()->row();
		$this->data['Nom']=$user->last_name;
		$this->data['Prenom']=$user->first_name;

		$this->loadPage('parametrageQuestionnaire',1,$this->data);

	}



	function getClient() {

		// $this->data
		$clientRep=$this->reponsesModel->getCommentaireClient();

		// foreach ($clientRep as $key => $value) {
  //               echo $value['titre_actu'].'<br>';
  //               echo $value['texte_actu'].'<br>';
  //               echo $value['id'].'<br>';    
  //               echo '<input type="hidden" value="'.$value['id'].'" name="id" ><br>';
  //               echo '<input type="submit" value="Modifier" name="modifier">';
  //               echo '<input type="submit" value="Supprimer" name="supprimer"><br>';
  //           }

		// $clientRep = array (
		// 		'id'   => 'id', 
  //               'nom'   => 'nom', 
  //               'prenom'   => 'prenom',
  //               'email'   => 'email',
  //               'telephone'   => 'telephone',
  //               'commentaire'   => 'commentaire'
		// 	);

		print_r($clientRep);

		return $clientRep;
	}


	function validate_responses() {

		$reponses= array();
		$valReturnToAjax=FALSE;

		$dataidquestions=$this->session->has_userdata('$dataidquestions')?$this->session->userdata('$dataidquestions'):[];

		$reponses=array_map(function($value){return intval($value);},$this->input->post('dataReponsesSend'));

		$sanitizedReponsesData = array_filter($reponses,
			function($var){//Callback qui enlène les valeurs null de monArray
    			return !is_null($var);
		});

		$resultIdQuestionsDiff = array_intersect_key($dataidquestions,$sanitizedReponsesData);//On enlève l'id des éléments que le client décide de ne pas répondre
		
		$dataResponses = array(
			'idQuestions' => serialize($resultIdQuestionsDiff),
			'reponses_recu' => serialize($sanitizedReponsesData),
			'satisfaction' => $this->input->post('satisfaction')
		);



		$dataClient=$this->input->post('dataInfosClient');

		/*

		$dataClient = array(
			'nom' => $this->input->post('nomclient'),
			'prenom' => $this->input->post('prenomclient'),
			'email' => $this->input->post('emailclient'),
			'telephone' => $this->input->post('telclient'),
			'commentaire'=>$this->input->post('commentaireClient'),
		);*/

		$idClient=$this->reponsesModel->insertClient($dataClient);//On récupère l'id du dernier client inséré

		//On insère l'id du client dans la liste des réponses à envoyer
		$dataResponsesToInsert=$this->util->array_insert_associative($dataResponses,array('idClient' => $idClient),2);

		//$this->util->printr($dataResponsesToInsert);
		if($this->reponsesModel->insertResponses($dataResponsesToInsert)){
			$this->session->set_userdata('some_name', 'some_value');//Cr&er une valeur de session pour bloquer le formulaire si la personne a déjà envoyé
			$valReturnToAjax=TRUE;
		}
		
		echo json_encode($dataResponsesToInsert);

	}


	function redirectUser($valCase){
		//Je nettoie tout avant tout redirection
		if($this->session->userdata('$dataResponses')){
			$this->session->unset_userdata('$dataResponses');
		}
		$valCase?redirect('https://www.facebook.com/Econcept-informatique-138047422928154/', 'refresh'):redirect('questions','refresh');

		$this->session->set_flashdata('$reponseServer',1);
			
		return 0;

	}



	function updateIfQuestionChange(){

		$passedArray=array(
			'statusGenerale' => $this->input->post('statusGenerale'),
			'statusStatistiques' => $this->input->post('statusStatistiques'),
			'statusParticulier' => $this->input->post('statusParticulier'),
			'statusProfessionnel' => $this->input->post('statusProfessionnel')
			);

		$emptyRemovedData = array_filter($passedArray,
			function($var){//Callback qui enlène les valeurs null de monArray
    			return !is_null($var);
		});

		/*$data = array(
			'statusGenerale' => ($this->input->post('statusGenerale'))
		);*/
		$dataRet=$this->questionModel->updateQuestion($this->input->post('keywordid'),$emptyRemovedData);

		echo ($dataRet);//Retour à Ajax
	}



	function sendResponsesToTable(){

		$data=$this->reponsesModel->selectAllResponses();
		$output = array();
		foreach($data as $k => $value) {
		    $output[] = array_values($value);
		}
		//echo json_encode(array('data' => $output));
		echo json_encode($data);

	}


	
	function sendQuestionsToTable(){

		$data=$this->questionModel->getAllQuestion();

		echo json_encode($data);

	}



	function sendQuestionsToDB(){

		
		$data = array(
			'libelle'  => $this->input->post('libelle'),
			'keywordid' => $this->input->post('keywordid'),
			'statusGenerale' => $this->input->post('statusGenerale'),
			'statusStatistiques' => $this->input->post('statusStatistiques'),
			'statusParticulier' => $this->input->post('statusParticulier'),
			'statusProfessionnel' => $this->input->post('statusProfessionnel')
		);
		$dataRet=$this->questionModel->insertQuestion($data);
		echo json_encode($dataRet);
		
	}



	function getDataCategorieFromDB(){
		$data=$this->questionModel->getAllcategorie();
		echo json_encode($data);
	}

	function getStatsMoy() {

		$AcceuilArr=array();
		$RapiditeArr=array();
		$QualiteArr=array();
		$ChoixArr=array();
		$PrixArr=array();
		$PourcentageMotsClesMoy=array();

		$dataRecupQuestionsMotclesMoy=$this->reponsesModel->getAllReponsesByCategorieSatisfaitMoy();

		foreach ($dataRecupQuestionsMotclesMoy as $element) {
			foreach ($element as $key => $value) {
				switch ($key) {
					case 'Accueil':
					
						if(is_array($value)){
							foreach ($value as $akey => $avalue) {
								$AcceuilArr[]=$avalue;
							}
						}else{
							$AcceuilArr[]=$value;
						}

						break;
					case 'Rapidité':
						if(is_array($value)){
							foreach ($value as $akey => $avalue) {
								$RapiditeArr[]=$avalue;
							}
						}else{
							$RapiditeArr[]=$value;
						}
						break;
					case 'Qualité':
						if(is_array($value)){
							foreach ($value as $akey => $avalue) {
								$QualiteArr[]=$avalue;
							}
						}else{
							$QualiteArr[]=$value;
						}
						break;
					case 'Choix':
						if(is_array($value)){
							foreach ($value as $akey => $avalue) {
								$ChoixArr[]=$avalue;
							}
						}else{
							$ChoixArr[]=$value;
						}
						break;
					case 'Prix':
						if(is_array($value)){
							foreach ($value as $akey => $avalue) {
								$PrixArr[]=$avalue;
							}
						}else{
							$PrixArr[]=$value;
						}
						break;
					default:
						break;
				}
			}
		}


		$pourcentageAccueilMoy=(count($AcceuilArr)>0)?ceil((array_sum($AcceuilArr)*100)/((count($AcceuilArr))*5)):0;
		$pourcentageRapiditeMoy=(count($RapiditeArr)>0)?ceil((array_sum($RapiditeArr)*100)/((count($RapiditeArr))*5)):0;
		$pourcentageQualiteMoy=(count($QualiteArr)>0)?ceil((array_sum($QualiteArr)*100)/((count($QualiteArr))*5)):0;
		$pourcentageChoixMoy=(count($ChoixArr)>0)?ceil((array_sum($ChoixArr)*100)/((count($ChoixArr))*5)):0;
		$pourcentagePrixMoy=(count($PrixArr)>0)?ceil((array_sum($PrixArr)*100)/((count($PrixArr))*5)):0;

		$PourcentageMotsClesMoy=array(
			"pourcentageAccueil"=>$pourcentageAccueilMoy,
			"pourcentageRapidite"=>$pourcentageRapiditeMoy,
			"pourcentageQualite"=>$pourcentageQualiteMoy,
			"pourcentageChoix"=>$pourcentageChoixMoy,
			"pourcentagePrix"=>$pourcentagePrixMoy
	);

	return $PourcentageMotsClesMoy;

	}

	function getStatsPro() {

		$AcceuilArr=array();
		$RapiditeArr=array();
		$QualiteArr=array();
		$ChoixArr=array();
		$PrixArr=array();
		$PourcentageMotsClesPro=array();

		$dataRecupQuestionsMotclesPro=$this->reponsesModel->getAllReponsesByCategorieSatisfaitPro();

		foreach ($dataRecupQuestionsMotclesPro as $element) {
			foreach ($element as $key => $value) {
				switch ($key) {
					case 'Accueil':
					
						if(is_array($value)){
							foreach ($value as $akey => $avalue) {
								$AcceuilArr[]=$avalue;
							}
						}else{
							$AcceuilArr[]=$value;
						}

						break;
					case 'Rapidité':
						if(is_array($value)){
							foreach ($value as $akey => $avalue) {
								$RapiditeArr[]=$avalue;
							}
						}else{
							$RapiditeArr[]=$value;
						}
						break;
					case 'Qualité':
						if(is_array($value)){
							foreach ($value as $akey => $avalue) {
								$QualiteArr[]=$avalue;
							}
						}else{
							$QualiteArr[]=$value;
						}
						break;
					case 'Choix':
						if(is_array($value)){
							foreach ($value as $akey => $avalue) {
								$ChoixArr[]=$avalue;
							}
						}else{
							$ChoixArr[]=$value;
						}
						break;
					case 'Prix':
						if(is_array($value)){
							foreach ($value as $akey => $avalue) {
								$PrixArr[]=$avalue;
							}
						}else{
							$PrixArr[]=$value;
						}
						break;
					default:
						break;
				}
			}
		}


		$pourcentageAccueilPro=(count($AcceuilArr)>0)?ceil((array_sum($AcceuilArr)*100)/((count($AcceuilArr))*5)):0;
		$pourcentageRapiditePro=(count($RapiditeArr)>0)?ceil((array_sum($RapiditeArr)*100)/((count($RapiditeArr))*5)):0;
		$pourcentageQualitePro=(count($QualiteArr)>0)?ceil((array_sum($QualiteArr)*100)/((count($QualiteArr))*5)):0;
		$pourcentageChoixPro=(count($ChoixArr)>0)?ceil((array_sum($ChoixArr)*100)/((count($ChoixArr))*5)):0;
		$pourcentagePrixPro=(count($PrixArr)>0)?ceil((array_sum($PrixArr)*100)/((count($PrixArr))*5)):0;

		$PourcentageMotsClesPro=array(
			"pourcentageAccueil"=>$pourcentageAccueilPro,
			"pourcentageRapidite"=>$pourcentageRapiditePro,
			"pourcentageQualite"=>$pourcentageQualitePro,
			"pourcentageChoix"=>$pourcentageChoixPro,
			"pourcentagePrix"=>$pourcentagePrixPro
	);

	return $PourcentageMotsClesPro;

	}

	function getStatsPart() {

		$AcceuilArr=array();
		$RapiditeArr=array();
		$QualiteArr=array();
		$ChoixArr=array();
		$PrixArr=array();
		$PourcentageMotsClesPart=array();

		$dataRecupQuestionsMotclesPart=$this->reponsesModel->getAllReponsesByCategorieSatisfaitPart();

		foreach ($dataRecupQuestionsMotclesPart as $element) {
			foreach ($element as $key => $value) {
				switch ($key) {
					case 'Accueil':
					
						if(is_array($value)){
							foreach ($value as $akey => $avalue) {
								$AcceuilArr[]=$avalue;
							}
						}else{
							$AcceuilArr[]=$value;
						}

						break;
					case 'Rapidité':
						if(is_array($value)){
							foreach ($value as $akey => $avalue) {
								$RapiditeArr[]=$avalue;
							}
						}else{
							$RapiditeArr[]=$value;
						}
						break;
					case 'Qualité':
						if(is_array($value)){
							foreach ($value as $akey => $avalue) {
								$QualiteArr[]=$avalue;
							}
						}else{
							$QualiteArr[]=$value;
						}
						break;
					case 'Choix':
						if(is_array($value)){
							foreach ($value as $akey => $avalue) {
								$ChoixArr[]=$avalue;
							}
						}else{
							$ChoixArr[]=$value;
						}
						break;
					case 'Prix':
						if(is_array($value)){
							foreach ($value as $akey => $avalue) {
								$PrixArr[]=$avalue;
							}
						}else{
							$PrixArr[]=$value;
						}
						break;
					default:
						break;
				}
			}
		}


		$pourcentageAccueilPart=(count($AcceuilArr)>0)?ceil((array_sum($AcceuilArr)*100)/((count($AcceuilArr))*5)):0;
		$pourcentageRapiditePart=(count($RapiditeArr)>0)?ceil((array_sum($RapiditeArr)*100)/((count($RapiditeArr))*5)):0;
		$pourcentageQualitePart=(count($QualiteArr)>0)?ceil((array_sum($QualiteArr)*100)/((count($QualiteArr))*5)):0;
		$pourcentageChoixPart=(count($ChoixArr)>0)?ceil((array_sum($ChoixArr)*100)/((count($ChoixArr))*5)):0;
		$pourcentagePrixPart=(count($PrixArr)>0)?ceil((array_sum($PrixArr)*100)/((count($PrixArr))*5)):0;

		$PourcentageMotsClesPart=array(
			"pourcentageAccueil"=>$pourcentageAccueilPart,
			"pourcentageRapidite"=>$pourcentageRapiditePart,
			"pourcentageQualite"=>$pourcentageQualitePart,
			"pourcentageChoix"=>$pourcentageChoixPart,
			"pourcentagePrix"=>$pourcentagePrixPart
	);

	return $PourcentageMotsClesPart;

	}

	function getStatsGlobalByKeyword() {
		$AcceuilArr=array();
		$RapiditeArr=array();
		$QualiteArr=array();
		$ChoixArr=array();
		$PrixArr=array();
		$PourcentageMotsClesMoy=array();

		$dataRecupQuestionsMotclesMoy=$this->reponsesModel->getAllReponsesByCategorieSatisfaitMoy();

		foreach ($dataRecupQuestionsMotclesMoy as $element) {
			foreach ($element as $key => $value) {
				switch ($key) {
					case 'Accueil':
					
						if(is_array($value)){
							foreach ($value as $akey => $avalue) {
								$AcceuilArr[]=$avalue;
							}
						}else{
							$AcceuilArr[]=$value;
						}

						break;
					case 'Rapidité':
						if(is_array($value)){
							foreach ($value as $akey => $avalue) {
								$RapiditeArr[]=$avalue;
							}
						}else{
							$RapiditeArr[]=$value;
						}
						break;
					case 'Qualité':
						if(is_array($value)){
							foreach ($value as $akey => $avalue) {
								$QualiteArr[]=$avalue;
							}
						}else{
							$QualiteArr[]=$value;
						}
						break;
					case 'Choix':
						if(is_array($value)){
							foreach ($value as $akey => $avalue) {
								$ChoixArr[]=$avalue;
							}
						}else{
							$ChoixArr[]=$value;
						}
						break;
					case 'Prix':
						if(is_array($value)){
							foreach ($value as $akey => $avalue) {
								$PrixArr[]=$avalue;
							}
						}else{
							$PrixArr[]=$value;
						}
						break;
					default:
						break;
				}
			}
		}

	$pourcentageAccueilMoy=(count($AcceuilArr)>0)?ceil((array_sum($AcceuilArr)*100)/((count($AcceuilArr))*5)):0;
	$pourcentageRapidite=(count($RapiditeArr)>0)?ceil((array_sum($RapiditeArr)*100)/((count($RapiditeArr))*5)):0;
	$pourcentageQualite=(count($QualiteArr)>0)?ceil((array_sum($QualiteArr)*100)/((count($QualiteArr))*5)):0;
	$pourcentageChoix=(count($ChoixArr)>0)?ceil((array_sum($ChoixArr)*100)/((count($ChoixArr))*5)):0;
	$pourcentagePrix=(count($PrixArr)>0)?ceil((array_sum($PrixArr)*100)/((count($PrixArr))*5)):0;

	$PourcentageMotsClesAccueil=array(
		"motsCles"=>'Accueil',
		"pourcentageAccueilPart"=>$pourcentageAccueilPart,
		"pourcentageAccueilPro"=>$pourcentageRapiditePro,
		"pourcentageAccueilMoy"=>$pourcentageAccueilMoy
	);

	$PourcentageMotsClesRapidite=array(
		"motsCles"=>'rapidite',
		"pourcentageRapiditePart"=>$pourcentageRapiditePart,
		"pourcentageRapiditePro"=>$pourcentageRapiditePro,
		"pourcentageRapiditeMoy"=>$pourcentageRapiditeMoy
	);

	$PourcentageMotsClesQualite=array(
		"motsCles"=>'Qualite',
		"pourcentageQualitePart"=>$pourcentageQualitePart,
		"pourcentageQualitePro"=>$pourcentageQualitePro,
		"pourcentageQualiteMoy"=>$pourcentageQualiteMoy
	);
	$PourcentageMotsClesChoix=array(
		"motsCles"=>'Choix',
		"pourcentageChoixPart"=>$pourcentageChoixPart,
		"pourcentageChoixPro"=>$pourcentageChoixPro,
		"pourcentageChoixMoy"=>$pourcentageChoixMoy
	);

	$PourcentageMotsClesChoix=array(
		"motsCles"=>'Prix',
		"pourcentagePrixPart"=>$pourcentagePrixPart,
		"pourcentagePrixPro"=>$pourcentagePrixPro,
		"pourcentagePrixMoy"=>$pourcentagePrixMoy
	);





	$PourcentageMotsCles=array(
		"motsCles"=>'Accueil',
		"pourcentageAccueil"=>$pourcentageAccueil,
		"pourcentageRapidite"=>$pourcentageRapidite,
		"pourcentageQualite"=>$pourcentageQualite,
		"pourcentageChoix"=>$pourcentageChoix,
		"pourcentagePrix"=>$pourcentagePrix
	);

	return $PourcentageMotsCles;


	}

	function getAllKeywordStats() {

		$arrayMoyenneGenerale=array();

		$arrStatsAccueil=array("Accueil");
		$arrStatsRapidite=array("Rapidite");
		$arrStatsQualite=array("Qualite");
		$arrStatsChoix=array("Choix");
		$arrStatsPrix=array("Prix");

		$arrayMoyenneGenerale=array($this->getStatsPart(),$this->getStatsPro(),$this->getStatsMoy());

		foreach ($arrayMoyenneGenerale as $row) {
		
			$arrStatsAccueil[]=$row['pourcentageAccueil'];
			$arrStatsRapidite[]=$row['pourcentageRapidite'];
			$arrStatsQualite[]=$row['pourcentageQualite'];
			$arrStatsChoix[]=$row['pourcentageChoix'];
			$arrStatsPrix[]=$row['pourcentagePrix'];

		}

		$arrayPourcentageArranged=array($arrStatsAccueil,$arrStatsRapidite,$arrStatsQualite,$arrStatsChoix,$arrStatsPrix);

		return $arrayPourcentageArranged;

	}


	function testserialize(){

		// $mondataOriginal=$this->reponsesModel->selectAllResponses();
		
		//$mondataDeserialized=$this->reponsesModel->getAllUnserializedReponses();
		$dataRecupQuestionsMotclesMoy=$this->reponsesModel->getAllReponsesByCategorieSatisfaitMoy();
		$dataRecupQuestionsMotclesPart=$this->reponsesModel->getAllReponsesByCategorieSatisfaitPart();
		$dataRecupQuestionsMotclesPro=$this->reponsesModel->getAllReponsesByCategorieSatisfaitPro();

		$data['dataRecupQuestionsMotclesMoy']=$dataRecupQuestionsMotclesMoy;
		$data['dataRecupQuestionsMotclesPart']=$dataRecupQuestionsMotclesPart;
		$data['dataRecupQuestionsMotclesPro']=$dataRecupQuestionsMotclesPro;

		$data['statsMoy']=$this->getStatsMoy();
		$data['statsPart']=$this->getStatsPart();
		$data['statsPro']=$this->getStatsPro();

		$this->loadPage('pagetest',0,$data);
	}


	function loadPage($page,$type,$data){//headerView contient tous les css et footerview tous les javascript
		$this->load->view('common/headerView');
		if($type){
			
			$this->load->view('common/asideView',$data);
		}
		$this->load->view('pages/'.$page,$data);
		$this->load->view('common/footerView');
	}



}
