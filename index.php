<?php 
	include_once('data.php'); 
	global $settings;
	global $voters;
	global $spots;
	global $participants;
	
	$top_label = 'Σύγκεντρωτικά Περιφερειακής Ενότητας';
	
	read_xml();	
	get_stats();
	
	$sel = 'bars';
	if(isset($_GET['display'])){
		$sel = trim($_GET['display']);
	}
	
	$dimos_list = '<option class="all" value="all">Συγκεντρωτικά</option>';
	$enotita_list = '<option class="all" value="all">Συγκεντρωτικά</option>';
	$diamerisma_list = '<option class="all" value="all">Συγκεντρωτικά</option>';
	
	foreach($spots as $key=>$val){
		if(trim($_GET['dimos']) == $key){
		
			// Ο Δήμος είναι επιλεγμένος ως Φίλτρο
			$top_label = $key;
			$dimos_list .= '<option value="'.$key.'" selected="selected">'.$key.'</option>';
			
			foreach($val as $key2=>$val2){
				
				// Η Ενότητα είναι επιλεγμένη ως Φίλτρο
				if(trim($_GET['enotita']) == $key2){
					$top_label .= ' / '. $key2;
					$enotita_list .= '<option value="'.$key2.'" class="'.$key.'" selected="selected">'.$key2.'</option>';
					
					foreach($val2 as $key3=>$val3){
						// Το Διαμέρισμα είναι επιλεγμένο ως Φίλτρο
						if(trim($_GET['diamerisma']) == $key3){
							$top_label .= ' / '. $key3;
							
							$diamerisma_list .= '<option value="'.$key3.'" class="'.$key2.'"  selected="selected">'.$key3.'</option>';
						} else {
							$diamerisma_list .= '<option value="'.$key3.'" class="'.$key2.'" >'.$key3.'</option>';
						}
					}
				} else {
					$enotita_list .= '<option value="'.$key2.'" class="'.$key.'"  >'.$key2.'</option>';
					foreach($val2 as $key3=>$val3){
						$diamerisma_list .= '<option val="'.$key3.'" class="'.$key2.'" style="display:none;">'.$key3.'</option>';
					}
				}
			}
			
		}else{
			// Δεν έχει επιλεγεί κάτι..
			$dimos_list .= '<option val="'.$key.'">'.$key.'</option>';
			foreach($val as $key2=>$val2){
				$enotita_list .= '<option val="'.$key2.'" class="'.$key.'" style="display:none;">'.$key2.'</option>';
				foreach($val2 as $key3=>$val3){
					$diamerisma_list .= '<option val="'.$key3.'" class="'.$key2.'" style="display:none;">'.$key3.'</option>';
				}
			}
		}
		
	}

?>
<!DOCTYPE html>
<html lang="el">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Αποτελέσματα Εκλογών 25ης Ιανουαρίου 2015</title>
	
    <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css">
	<link href="js/table/bootstrap-table.css" rel="stylesheet" type="text/css">
	<link href="jqplot/jquery.jqplot.min.css" rel="stylesheet" type="text/css" />
	 <link href="css/style.css" rel="stylesheet" type="text/css">
	
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
	<script src="js/jquery.min.js"></script>
  </head>
  <body>
	<div class="container">
		<div class="row">
			<div class="col-sm-12 text-center">
				<h4>Αποτελέσματα: <strong>
				<?php 
					echo $top_label;
				?></strong></h4>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-4">
				Σε <strong><?php echo $settings['cur_eklogika']; ?></strong> 
				επι συνόλου <strong><?php echo $settings['all_eklogika']; ?></strong> Εκλ. Τμημάτων.
			</div>
			<div class="col-sm-4">
				<div class="progress">
				  <div class="progress-bar progress-bar-warning progress-bar-striped" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $settings['cur_eklogika_percentage']; ?>%">
					<strong><?php echo $settings['cur_eklogika_percentage']; ?>% Ενσωμάτωση</strong>
				  </div>
				</div>
			</div>
			<div class="col-sm-4 text-right">
				<p>Τελευταία Ενημέρωση: <strong><?php echo $settings['generated']; ?></strong></p>
			</div>
		</div>
		
		<div class="row">
			<form action="" id="filterform" class="form-horizontal" method="get">
				<div class="col-sm-2">
					<div class="form-group">
						<label for="dimos" class="control-label">Δήμος</label>
						<div class="">
							<select class="form-control" id="dimos" name="dimos">
								<?php echo $dimos_list; ?>
							</select>
						</div>
					</div>
				</div>
				<div class="col-sm-2">
					<div class="form-group">
						<label for="enotita" class="control-label">Δημοτική Ενότητα</label>
						<div class="">
							<select class="form-control" id="enotita" name="enotita">
								<?php echo $enotita_list; ?>
							</select>
						</div>
					</div>
				</div>
				<div class="col-sm-2">
					<div class="form-group">
						<label for="diamerisma" class="control-label">Δημοτικό Διαμέρισμα</label>
						<div class="">
							<select class="form-control" id="diamerisma" name="diamerisma">
								<?php echo $diamerisma_list; ?>
							</select>
						</div>
					</div>
				</div>
				<div class="col-sm-2">
					<div class="form-group">
						<label for="display" class="control-label">Προβολή ώς</label>
						<div class="">
							<select class="form-control" id="display" name="display">
								<option value="bars">Γράφημα</option>
								<option value="pie" <?php if($sel != 'bars'){ echo 'selected="selected"'; } ?>>Πίτα</option>
							</select>
						</div>
					</div>
				</div>
				<div class="col-sm-2">
					<div class="form-group">
						<label for="submit" class="control-label">&nbsp;</label>
						<div class="">
							<button type="submit" class="btn btn-default">Φιλτράρισμα</button>
						</div>
					</div>
				</div>
				<div class="col-sm-11 text-right italics">Μετά από κάθε σας επιλογή πατήστε το κουμπί "Φιλτράρισμα"</div>
			</form>
		</div>
		
		<div class="row results-row">
			<div class="col-sm-12">
				<div id="chart" style="width:100%; height:500px"></div>
			</div>
		</div>	
		
		 <div class="row">
			<div class="col-sm-8">
				<table id="viewdetails" 
					data-toggle="table"
					data-classes="table table-hover table-striped"
					data-show-header="false"
				>
					<thead>
						<tr>
							<th data-field="num" class="num"></th>
							<th data-field="image" class="image"></th>
							<th data-field="name" class="name">Όνομα</th>
							<th data-field="votes" class="votes">Ψήφοι</th>
							<th data-field="percentage" class="percentage">Ποσοστό</th>
						</tr>
					</thead>
					<tbody>
					<?php
						$cnt = 0;
						foreach($participants as $participant){
							$cnt++;
					?>
						<tr>
							<td data-field="num" class="num"><?php echo $cnt.'. '; ?></td>
							<?php if($participant['image'] != ''){ ?>
								<td data-field="image" class="image"><img src="logos/<?php echo $participant['image']; ?>" alt="<?php echo $participant['name']; ?>" /></td>
							<?php }else{ ?>
								<td data-field="image" class="image"></td>
							<?php }?>
							<td data-field="name" class="name"><?php echo $participant['name']; ?></td>
							<td data-field="votes" class="votes"><?php echo $participant['votes']; ?></td>
							<td data-field="percentage" class="percentage"><?php echo $participant['percentage']; ?>%</td>
						</tr>
					<?php
						}
					?>
					</tbody>
				</table>
			</div>
			<div class="col-sm-4">
				<table id="viewdetailsusers" 
					data-toggle="table"
					data-classes="table table-hover table-striped"
					data-show-header="false"
				>
					<thead>
						<tr>
							<th data-field="stat_name" class="stat_name"></th>
							<th data-field="stat_votes" class="stat_votes">Ψήφοι</th>
							<th data-field="stat_percentage" class="stat_percentage">Ποσοστό</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td data-field="stat_name" class="stat_name">Εγγεγραμμένοι Εκλογείς</td>
							<td data-field="stat_votes" class="stat_votes"><?php echo $voters['grameni']; ?></td>
							<td data-field="stat_percentage" class="stat_percentage"><?php echo $voters['percentage_grameni']; ?>%</td>
						</tr>
						<tr>
							<td data-field="stat_name" class="stat_name">Ψηφίσαντες Εκλογείς</td>
							<td data-field="stat_votes" class="stat_votes"><?php echo $voters['voted']; ?></td>
							<td data-field="stat_percentage" class="stat_percentage"><?php echo $voters['percentage_voted']; ?>%</td>
						</tr>
						<tr>
							<td data-field="stat_name" class="stat_name">Αποχή</td>
							<td data-field="stat_votes" class="stat_votes"><?php echo $voters['apoxi']; ?></td>
							<td data-field="stat_percentage" class="stat_percentage"><?php echo $voters['percentage_apoxi']; ?>%</td>
						</tr>
						<tr>
							<td data-field="stat_name" class="stat_name">Έγκυρα</td>
							<td data-field="stat_votes" class="stat_votes"><?php echo $voters['eggira']; ?></td>
							<td data-field="stat_percentage" class="stat_percentage"><?php echo $voters['percentage_eggira']; ?>%</td>
						</tr>
						<tr>
							<td data-field="stat_name" class="stat_name">Ακυρα</td>
							<td data-field="stat_votes" class="stat_votes"><?php echo $voters['akira']; ?></td>
							<td data-field="stat_percentage" class="stat_percentage"><?php echo $voters['percentage_akira']; ?>%</td>
						</tr>
						<tr>
							<td data-field="stat_name" class="stat_name">Λευκά</td>
							<td data-field="stat_votes" class="stat_votes"><?php echo $voters['leyka']; ?></td>
							<td data-field="stat_percentage" class="stat_percentage"><?php echo $voters['percentage_leyka']; ?>%</td>
						</tr>
					</tbody>
				</table>
				<br />
				<div class="alert alert-warning" role="alert">
					Σημείωση:<br />Τα εκλογικά αποτελέσματα που παρουσιάζονται στο Internet, υπενθυμίζεται ότι είναι σύμφωνα με τα τηλεγραφήματα των
					Δικαστικών Αντιπροσώπων. Τα επίσημα αποτελέσματα θα ανακοινωθούν από το Υπουργείο Εσωτερικών. 
				</div>
			</div>
		</div>
	</div>
	
	<script type="text/javascript">
		(function($){
			$(document).ready(function(){
			
				   $('#dimos').on('change', function(event){
						var dimos = $(this).val();
						
						// Ενότητες
						$("#enotita option").each(function(){
							if($(this).hasClass('all')){
								$(this).prop('selected', true);
							} else if($(this).hasClass(''+dimos+'')){
								$(this).prop('selected', false);
								$(this).css('display','block');
							} else {
								$(this).prop('selected', false);
								$(this).css('display','none');
							}
						});
						
						
						// Διαμερίσματα (κρύψε τα όλα).
						$("#diamerisma option").each(function(){
							if($(this).hasClass('all')){
								$(this).prop('selected', true);
							} else {
								$(this).prop('selected', false);
								$(this).css('display','none');
							}
						});
						
						$('.italics').css('color', '#E30D0D');
						$('.italics').css('font-weight', 'bold');
				   });
				   
				    $('#enotita').on('change', function(event){
						var enotita = $(this).val();
						
						// Ενότητες
						$("#diamerisma option").each(function(){
							if($(this).hasClass('all')){
								$(this).prop('selected', true);
							} else if($(this).hasClass(''+enotita+'')){
								$(this).prop('selected', false);
								$(this).css('display','block');
							} else {
								$(this).prop('selected', false);
								$(this).css('display','none');
							}
						});
						
						$('.italics').css('color', '#E30D0D');
						$('.italics').css('font-weight', 'bold');
					});
					
					$('#diamerisma').on('change', function(event){
						$('.italics').css('color', '#E30D0D');
						$('.italics').css('font-weight', 'bold');
					});
					
					$('#display').on('change', function(event){
						$('.italics').css('color', '#E30D0D');
						$('.italics').css('font-weight', 'bold');
					});
				<?php
					$data_line = array();
					$data_color = array();
					$others = 0;
					$cnt = 0;
					foreach($participants as $participant){
						$cnt++;
						if($cnt < $settings['bar_threshold']){
							if($sel != 'bars')
								$data_line[]= "['".str_replace("'","\'",$participant['name'])."', ".$participant['percentage']."]";
							else
								$data_line[]= "['".str_replace("'","\'",$participant['image'])."', ".$participant['percentage']."]";
							$data_color[] = "'".$participant['color']."'";
						}else
							$others = $others+$participant['percentage'];
					}
					if($sel != 'bars')
						$data_line[]= "['Λοιποί', ".$others."]";
					else
						$data_line[]= "['loipoi.gif', ".$others."]";
					$data_color[] = "'#CACACA'";
				?>
				
				var line = [<?php echo implode(',', $data_line); ?>];
			
				<?php if($sel != 'bars') { ?>
					
					$('#chart').jqplot([line], {
						animate: !$.jqplot.use_excanvas,
						seriesColors:[<?php echo implode(',', $data_color); ?>],
						grid: {
							drawGridlines: false,
							background: '#f8f8f8',
						},
						seriesDefaults:{
							renderer:$.jqplot.PieRenderer,
							rendererOptions: {
								showDataLabels: true,
							}
						},
						legend: {
							show: true,
						}
					});
					
				<?php } else { ?>
				
					$('#chart').jqplot([line], {
						animate: !$.jqplot.use_excanvas,
						seriesColors:[<?php echo implode(',', $data_color); ?>],
						grid: {
							drawGridlines: false,
							background: '#f8f8f8',
						},
						seriesDefaults:{
							renderer:$.jqplot.BarRenderer,
							pointLabels: { show: true },
							rendererOptions: {
								varyBarColor: true
							}
						},
						axesDefaults: {
							showTicks: false,
							showTickMarks: false       
						},
						axes:{
							xaxis:{
								renderer: $.jqplot.CategoryAxisRenderer
							},
							yaxis: {
								tickOptions:{
								  formatString: "%#.2f"
								}
							  }
							
						}
					});
					
					$('.jqplot-xaxis-tick').each(function(){
						var $image = $(this).text();
						var $image_url = '<img src="logos/'+$image+'" />';
						$(this).html($image_url);
					});
					
				<?php } ?>
			
			});
		})(jQuery);
	</script>
    
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/table/bootstrap-table.js"></script>
	
	<script type="text/javascript" src="jqplot/jquery.jqplot.min.js"></script>
	<script type="text/javascript" src="jqplot/plugins/jqplot.barRenderer.min.js"></script>
	<script type="text/javascript" src="jqplot/plugins/jqplot.pieRenderer.min.js"></script>
	<script type="text/javascript" src="jqplot/plugins/jqplot.categoryAxisRenderer.min.js"></script>
	<script type="text/javascript" src="jqplot/plugins/jqplot.pointLabels.min.js"></script>
	
  
  </body>
</html>