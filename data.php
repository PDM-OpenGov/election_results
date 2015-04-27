<?php

global $settings;
global $xml;
global $voters;
global $spots;
global $participants;

// Ρυθμίσεις για την εφαρμογή
$settings = array(
	'filename' 	=> 'ResultsAnaEklogiko.xml',
	'rootnode'	=> 'ResultsAnaEklogikoXML',
	'bar_threshold' => 8,
	'all_eklogika'	=> 0,
	'cur_eklogika'	=> 0,
	'cur_eklogika_filtered'	=> 0,
	'cur_eklogika_percentage' => 0,
	'excluded' 	=> array('Grameni', 'NoOfPeopleVoted', 'AKIRA', 'LEYKA', 'eggira', 'ΔΗΜΟΣ', 'PLACE', 'Ν_ΔΗΜΟΣ', 'ConstitueNo', 'Checked_OK'),
	'generated'	=> '',
	'char_mapping'	=> array(
						'_x0020_' => ' ',
						'_x0028_' => "'",
						'_x0029_' => "'",
						'_x2013_' => '-',
						'_' => '.',
					)
);

function read_xml(){
	global $settings;
	global $xml;
	
	// Φορτωση του Αρχείου 
	if (file_exists($settings['filename'])) {
		$xml = simplexml_load_file($settings['filename']);
		$settings['generated'] = date('d/m/Y H:i', strtotime($xml->attributes()->generated));
	} else {
		exit('Σφάλμα! Το αρχείο δεν εντοπίστηκε. Παρακαλούμε προσπαθήστε ξανά!');
	}
}


function get_stats(){
	global $settings;
	global $xml;
	global $voters;
	global $spots;
	global $participants;
	
	$spots = array();
	$participants = array();
	
	$voters = array(
		'grameni' => 0,
		'apoxi'	=> 0,
		'voted' => 0,
		'akira' => 0,
		'leyka' => 0,
		'eggira' => 0,
		'percentage_grameni' => 0,
		'percentage_apoxi'	=> 0,
		'percentage_voted' => 0,
		'percentage_akira' => 0,
		'percentage_leyka' => 0,
		'percentage_eggira' => 0,
	);
	
	$filter_dimos = '';
	$filter_enotita = '';
	$filter_diamerisma = '';
	
	// Δες αν έχουν φιλτραριστεί τα αποτελέσματα και κατέγραψε τα φίλτρα
	if(isset($_GET['dimos']) and  trim($_GET['dimos']) != 'all'){ 
		$filter_dimos = trim($_GET['dimos']);
		
		if(isset($_GET['enotita']) and  trim($_GET['enotita']) != 'all'){ 
			$filter_enotita = trim($_GET['enotita']);
			
			if(isset($_GET['diamerisma']) and  trim($_GET['diamerisma']) != 'all'){ 
				$filter_diamerisma =  trim($_GET['diamerisma']);
			}
		}
	}
	
	// Ανάγνωση των στοιχείων όλων των εκλογικών κέντρων
	foreach ($xml->$settings['rootnode'] as $spot_xml){
		
		$settings['all_eklogika'] = $settings['all_eklogika'] + 1;
		
		// Μετατροπή των στοιχείων του εκάστοτε Εκλ. Κέντρου σε πίνακα για να γίνεται πιο εύκολα η επεξεργασία
		$spot = json_decode(json_encode((array)$spot_xml), TRUE);
		
		// Συγκέντρωση Δήμων, Δημοτικών Ενοτήτων και Εκλογικών Κέντρων σε Πίνακα ===============================
		$dimos = $spot['Ν_ΔΗΜΟΣ'];
		$dimotiki_enotita = $spot['ΔΗΜΟΣ'];
		$eklogiko_diamerisma = $spot['PLACE'];
		$eklogiko_kentro = $spot['ConstitueNo'];
		
		// Να καταγράψουμε το εκλογικό αν έχει φίλτρο ωστε να βγάλουμε σωστούς αριθμούς ενσωμάτωσης
		if($filter_dimos != ''){ // Έχουμε φίλτρο Δήμου
			if($filter_dimos == $dimos){ // Ανήκει στο Δήμο
			
				if($filter_enotita != ''){ // Έχουμε φίλτρο Ενότητας
					if($filter_enotita == $dimotiki_enotita){ // Ανήκει στην Ενότητα
						
						if($filter_diamerisma != ''){ // Έχουμε φίλτρο Διαμερίσματος
							if($filter_diamerisma == $eklogiko_diamerisma){ // Ανήκει στo Διαμέρισμα
								$settings['cur_eklogika_filtered'] = $settings['cur_eklogika_filtered'] +1 ;
							}
						} else {
							$settings['cur_eklogika_filtered'] = $settings['cur_eklogika_filtered'] +1 ;
						}
					} 
				} else {
					$settings['cur_eklogika_filtered'] = $settings['cur_eklogika_filtered'] +1 ;
				}
			}
		} 
		
		// Να δούμε αν έχει δεδομένα το Εκλογικό Κέντρο (αν δεν έχει να μην καταμετρηθεί και να συνεχίσει).
		if(intval($spot['Checked_OK']) != 1) { 
			continue;
		}
		
		if(array_key_exists ($dimos, $spots)){ // Έχουμε ξαναβρεί το Δήμο -> Αναζήτησε τη Δημοτική Ενότητα
		
			if(array_key_exists ($dimotiki_enotita , $spots[$dimos])){  // Έχουμε ξαναβρεί τη Δημοτική Ενότητα ->  Αναζήτησε το Εκλογικό Διαμέρισμα
			
				if(array_key_exists ($eklogiko_diamerisma , $spots[$dimos][$dimotiki_enotita])){  // Έχουμε ξαναβρεί το Εκλογικό Διαμέρισμα
					$spots[$dimos][$dimotiki_enotita][$eklogiko_diamerisma ][] = $eklogiko_kentro;
				} else {
					$spots[$dimos][$dimotiki_enotita][$eklogiko_diamerisma] = array($eklogiko_kentro);
				}
			} else {
				$spots[$dimos][$dimotiki_enotita] = array($eklogiko_diamerisma => array($eklogiko_kentro)); // Δεν έχουμε ξαναβρεί τη Δημοτική Ενότητα. Πρόσθεσε την.
			}
		} else {
			$spots[$dimos] = array( $dimotiki_enotita => array($eklogiko_diamerisma => array($eklogiko_kentro))); // Δεν έχουμε ξαναβρεί το Δήμο. Πρόσθεσε τον.
		}
		
		// Δες αν έχουν φιλτραριστεί τα αποτελέσματα και κατέγραψε μόνο αυτά που αφορούν τα φίλτρα
		
		if($filter_dimos != ''){ // Έχουμε φίλτρο Δήμου
			if($filter_dimos == $dimos){ // Ανήκει στο Δήμο
			
				if($filter_enotita != ''){ // Έχουμε φίλτρο Ενότητας
					if($filter_enotita == $dimotiki_enotita){ // Ανήκει στην Ενότητα
						
						if($filter_diamerisma != ''){ // Έχουμε φίλτρο Διαμερίσματος
							if($filter_diamerisma == $eklogiko_diamerisma){ // Ανήκει στo Διαμέρισμα
								add_data($spot);
							}
						} else {
							add_data($spot);
						}
					} 
				} else {
					add_data($spot);
				}
			}
		} else {
			// Δεν έχουμε κανένα φίλτρο
			add_data($spot);
		}
		
	}
	
	// Επεξεργασία των δεδομένων των Υποψηφίων Κομμάτων
	foreach($participants as $key=>$val){
		
		$percentage =  ($val * 100)/$voters['eggira'];
		
		$edit_participant = array(
			'name'			=>	edit_paticipant_name($key),
			'votes'			=>	number_format($val, 0, ',', '.'),
			'percentage'	=>	round($percentage, 2),
			'image' 		=>  get_paticipant_image($key),
			'color' 		=>  get_paticipant_color($key),
		);
		
		$participants[$key] = $edit_participant; 
		
		//echo $key.'<br />'; // Προσωρινό: Για να πάρουμε τα ονόματα των κομμάτων βάσει του export της Access
	}
	
	array_sort_by_column($participants, 'percentage');
	
	// Υπολόγισε την αποχή
	$voters['apoxi'] = $voters['grameni'] - $voters['voted'];
	
	// Υπολόγισε τα στατιστικά
	$voters['percentage_grameni'] 	= 100;
	$voters['percentage_apoxi'] 	= ($voters['apoxi'] * 100)/ $voters['grameni'];
	$voters['percentage_voted']		= ($voters['voted'] * 100)/ $voters['grameni'];
	$voters['percentage_akira']		= ($voters['akira'] * 100)/ $voters['grameni'];	
	$voters['percentage_leyka']		= ($voters['leyka'] * 100)/ $voters['grameni'];	
	$voters['percentage_eggira']	= ($voters['eggira'] * 100)/ $voters['grameni'];
	
	// Στατιστικά Εκλογικών Τμημάτων
	// Έχουμε φίλτρα, οπότε να δούμε με βάση τα φίλτρα το ποσοστό ενσωμάτωσης
	if($settings['cur_eklogika_filtered'] > 0)
		$settings['all_eklogika'] = $settings['cur_eklogika_filtered'];
	$settings['cur_eklogika_percentage'] = ($settings['cur_eklogika'] * 100)/ $settings['all_eklogika'];
	
	
	// Ορθή προβολή χιλιάδων
	$voters['grameni'] 	= 	number_format($voters['grameni'], 0, ',', '.');		
	$voters['apoxi'] 	= 	number_format($voters['apoxi'], 0, ',', '.');		
	$voters['voted']	=	number_format($voters['voted'], 0, ',', '.');		
	$voters['akira']	= 	number_format($voters['akira'], 0, ',', '.');		
	$voters['leyka']	=	number_format($voters['leyka'], 0, ',', '.');		
	$voters['eggira']	= 	number_format($voters['eggira'], 0, ',', '.');		
	$voters['percentage_apoxi'] 	= number_format($voters['percentage_apoxi'], 0, ',', '.');		
	$voters['percentage_voted']		= number_format($voters['percentage_voted'], 0, ',', '.');		
	$voters['percentage_akira']		= number_format($voters['percentage_akira']	, 0, ',', '.');		
	$voters['percentage_leyka']		= number_format($voters['percentage_leyka'], 0, ',', '.');		
	$voters['percentage_eggira']	= number_format($voters['percentage_eggira'], 0, ',', '.');		
	$settings['cur_eklogika_percentage'] = number_format($settings['cur_eklogika_percentage'], 0, ',', '.');		
}

function add_data($spot){
	global $settings;
	global $xml;
	global $voters;
	global $spots;
	global $participants;
	
	// Κατέγραψε το Εκλογικό (ως ενσωματωμένο)
	$settings['cur_eklogika'] = $settings['cur_eklogika'] +1 ;
	
	// Συγκέντρωση Στατιστικών Ψηφοφόρων ===============================================
	$voters['grameni'] 	= 	$voters['grameni'] 	+ 	$spot['Grameni'];			
	$voters['voted']	=	$voters['voted']	+ 	$spot['NoOfPeopleVoted'];
	$voters['akira']	= 	$voters['akira'] 	+ 	$spot['AKIRA'];
	$voters['leyka']	=	$voters['leyka'] 	+ 	$spot['LEYKA'];
	$voters['eggira']	= 	$voters['eggira'] 	+  	$spot['eggira'];


	// Λήψη στατιστικών ανα Υποψήφιο Κόμμα	============================================
	foreach($spot as $key=>$val){
		if(!in_array($key, $settings['excluded'])){ // Για να αποφύγουμε τις άλλες στήλες και να βρούμε μόνο τα κόματα
			if(array_key_exists ($key, $participants)){	// Έχουμε ξαναβρεί το Κόμμα -> Πρόσθεσε τις ψήφους του 
				$participants[$key] = $participants[$key] + $val;
			} else {
				$participants[$key] = $val;
			}
		}
	}
}

function get_paticipant_color($name){
	
	// Τα χρώματα ανα Κόμμα (για τις στήλες των bars)
	$colors = array(
		'ΑΝΕΞΑΡΤΗΤΟΙ_x0020_ΕΛΛΗΝΕΣ'	=> '#154ba1',
		'ΕΔΕΜ'						=> '#85802b',
		'ΕΕΚ_x0020__x2013__x0020_ΤΡΟΤΣΚΙΣΤΕΣ'	
									=> '#d83a41',
		'ΚΚΕ_x0020__x0028_μ-λ_x0029__x0020_Μ-Λ_x0020_ΚΚΕ-Εκλογική_x0020_Συνεργασία'	
									=> '#ef1923',
		'Λ_Α_Ο_Σ_'					=> '#2a86c5',
		'ΝΕΑ_x0020_ΔΗΜΟΚΡΑΤΙΑ'		=> '#003563',
		'ΠΑ_ΣΟ_Κ_'					=> '#086d18',
		'ΣΥ_ΡΙΖ_Α_'					=> '#88429c',
		'ΑΝΤ_ΑΡ_ΣΥ_Α_'				=> '#c11d24',
		'ΕΝΩΣΗ_x0020_ΚΕΝΤΡΩΩΝ'		=> '#ed9e65',
		'ΚΙΝΗΜΑ_x0020_ΔΗΜΟΚΡΑΤΩΝ_x0020_ΣΟΣΙΑΛΙΣΤΩΝ'	
									=> '#ed1c24',
		'ΚΚΕ'						=> '#bc0000',
		'ΟΡΓΑΝΩΣΗ_x0020_ΚΟΜΜΟΥΝΙΣΤΩΝ_x0020_ΔΙΕΘΝΙΣΤΩΝ_x0020_ΕΛΛΑΔΑΣ'	
									=> '#85802b',
		'ΠΡΑΣΙΝΟΙ-ΔΗΜΟΚΡΑΤΙΚΗ_x0020_ΑΡΙΣΤΕΡΑ'	
									=> '#fd0100',
		'ΤΕΛΕΙΑ_x0020__x2013__x0020_ΑΠΟΣΤΟΛΟΣ_x0020_ΓΚΛΕΤΣΟΣ'	
									=> '#00adef',
		'ΤΟ_x0020_ΠΟΤΑΜΙ'			=> '#b67063',
		'ΧΡΥΣΗ_x0020_ΑΥΓΗ'			=> '#0f0f0f',
	); 
	return $colors[$name];
}

function get_paticipant_image($name){

	// Τα εικονίδια ανα Κόμμα
	$images = array(
		'ΑΝΕΞΑΡΤΗΤΟΙ_x0020_ΕΛΛΗΝΕΣ'	=> 'anel.png',
		'ΕΔΕΜ'						=> '',
		'ΕΕΚ_x0020__x2013__x0020_ΤΡΟΤΣΚΙΣΤΕΣ'	
									=> 'eek-tros.png',
		'ΚΚΕ_x0020__x0028_μ-λ_x0029__x0020_Μ-Λ_x0020_ΚΚΕ-Εκλογική_x0020_Συνεργασία'	
									=> 'ml-kke.png',
		'Λ_Α_Ο_Σ_'					=> 'laos.png',
		'ΝΕΑ_x0020_ΔΗΜΟΚΡΑΤΙΑ'		=> 'nd.png',
		'ΠΑ_ΣΟ_Κ_'					=> 'pasok.gif',
		'ΣΥ_ΡΙΖ_Α_'					=> 'syriza.png',
		'ΑΝΤ_ΑΡ_ΣΥ_Α_'				=> 'antarsya.png',
		'ΕΝΩΣΗ_x0020_ΚΕΝΤΡΩΩΝ'		=> 'kentroon.png',
		'ΚΙΝΗΜΑ_x0020_ΔΗΜΟΚΡΑΤΩΝ_x0020_ΣΟΣΙΑΛΙΣΤΩΝ'	
									=> 'kinima.png',
		'ΚΚΕ'						=> 'kke.png',
		'ΟΡΓΑΝΩΣΗ_x0020_ΚΟΜΜΟΥΝΙΣΤΩΝ_x0020_ΔΙΕΘΝΙΣΤΩΝ_x0020_ΕΛΛΑΔΑΣ'	
									=> '',
		'ΠΡΑΣΙΝΟΙ-ΔΗΜΟΚΡΑΤΙΚΗ_x0020_ΑΡΙΣΤΕΡΑ'	
									=> 'da.png',
		'ΤΕΛΕΙΑ_x0020__x2013__x0020_ΑΠΟΣΤΟΛΟΣ_x0020_ΓΚΛΕΤΣΟΣ'	
									=> 'teleia.png',
		'ΤΟ_x0020_ΠΟΤΑΜΙ'			=> 'potami.png',
		'ΧΡΥΣΗ_x0020_ΑΥΓΗ'			=> 'xa.png',
	);
	return $images[$name];
}

function edit_paticipant_name($name){
	global $settings;
	foreach($settings['char_mapping'] as $key=>$val){ // Αντικαθιστά τους περίεργους χαρακτήρες
		$name = str_replace($key, $val, $name);
	}
	return $name;
}

// http://stackoverflow.com/a/2699153
function array_sort_by_column(&$arr, $col, $dir = SORT_DESC) {
    $sort_col = array();
    foreach ($arr as $key=> $row) {
        $sort_col[$key] = $row[$col];
    }

    array_multisort($sort_col, $dir, $arr);
}

?>