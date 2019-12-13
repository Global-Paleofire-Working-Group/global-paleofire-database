<?php
/*
 * fichier Pages/CDA/core_view.php
 *
 */
function cmpDepths($charcoalA, $charcoalB){
	if ($charcoalA["ID_CHARCOAL_UNITS"] > $charcoalB["ID_CHARCOAL_UNITS"]){
		return 1;
	} else if ($charcoalA["ID_CHARCOAL_UNITS"] < $charcoalB["ID_CHARCOAL_UNITS"]){
		return -1;
	} else if ($charcoalA["ID_CHARCOAL_UNITS"] == $charcoalB["ID_CHARCOAL_UNITS"]){
		if ($charcoalA["DEPTHS_LIST_DECODE"][0][2] > $charcoalB["DEPTHS_LIST_DECODE"][0][2])
			return 1;
	}
	return -1;
}


if (isset($_SESSION['started'])) {
	require './Models/Site.php';
	require_once './Models/Sample.php';
	require './Models/CalibrationMethod.php';
	require './Models/CalibrationVersion.php';
	require './Models/DateComment.php';
	require './Models/AgeUnits.php';
	require_once './Models/DataBaseVersion.php';
	require_once './Models/DateType.php';
	require_once './Models/MatDated.php';
	require_once './Library/PaleofireHtmlTools.php';

	// we search who is connected, a contributor is allowed to modify his own data

	$data_contributor = NULL;
	if(isset($_SESSION['gcd_user_id'])){
		$id_contact = WebAppUserGCD::getContactId($_SESSION['gcd_user_id']);
		if ($id_contact != NULL) $data_contributor = Contact::getObjectPaleofireFromId ($id_contact);
	}

	//$current_sample_id = null;
	$core_id = null;
	if (isset($_GET['core_id']) && is_numeric($_GET['core_id'])) {
		$core_id = $_GET['core_id'];
	}

	if ($core_id != null) {
		$core = Core::getObjectPaleofireFromId($core_id);
		$site_name = Site::getNameFromStaticList($core->getSiteId());
		?>

		<div class="btn-toolbar" role="toolbar">
			<a role="button" class="btn btn-default btn-xs" href="index.php?p=CDA/site_view2&gcd_menu=CDA&site_id=<?php echo $core->getSiteId() ?>">
				<span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span>
				Go to site : <?php echo $site_name; ?>
			</a>

			<?php
			// affichage d'un menu pour modifier la carotte
			// pour l'instant seul un administrateur peut modifier une carotte les carottes ne sont pas rattachées à un utilisateur particulier
			if (isset($_SESSION['gcd_user_role']) &&(($_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR)||($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR))) {
				?>
				<a role="button" class="btn btn-default btn-xs" style="float:right" href="index.php?p=ADA/edit_core&gcd_menu=ADA&id=<?php echo $core_id; ?>">
					<span class="glyphicon glyphicon-edit" aria-hidden="true"></span>
					Edit this core
				</a>
				<?php
				if ($core->countSamples() == 0){
					if (NoteCore::countPaleofireObjects(NoteCore::ID_CORE." = ".$core->getIdValue()) == 0){
						// la carotte n'a pas de sample et pas de note
						// donc on propose la suppression
						?>
						<a role="button" class="btn btn-default btn-xs" style="float:right" data-toggle="modal" data-target="#dialog-paleo" data-whatever="[&quot;delcore&quot;,<?php echo $core->getIdValue() ?>]">
							<span class="glyphicon glyphicon-remove" aria-hidden="true"></span> Delete
						</a>
						<?php
					}
				}
			} // FIN if ($_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR)
			?>
		</div>

		<h3 class="paleo">Core <?php echo $core->getName(); ?></h3>

		<ul class="nav nav-tabs" role="tablist">
			<li class="active"><a href="#description" aria-controls="description" role="tab" data-toggle="tab">Description</a></li>
			<li ><a href="#age" aria-controls="age" role="tab" data-toggle="tab">Age model</a></li>
			<li><a href="#charcoals" aria-controls="charcoals" role="tab" data-toggle="tab">Charcoal</a></li>
			<li ><a href="#data_quality" aria-controls="data_quality" role="tab" data-toggle="tab">Charts of data quality</a></li>
			<li ><a href="#publication" aria-controls="publicaton" role="tab" data-toggle="tab">Publication</a></li>
		</ul>

		<!-- Tab panes -->
		<div class="tab-content" style="padding-top:10px;border-color:lightgray;border-width:1px;border-style:none solid solid solid;">
			<div class="tab-pane active" id="description">
				<div class="row">
					<div class="col-md-6">
						<dl class="dl-horizontal">
							<dt>Latitude</dt><dd><?php echo $core->_latitude ?></dd>
							<dt>Longitude</dt><dd><?php echo $core->_longitude ?></dd>
							<dt>Elevation</dt><dd><?php echo $core->_elevation ?></dd>
							<dt>Water depth</dt><dd><?php echo $core->_water_depth_value ?></dd>
							<dt>Coring date</dt><dd><?php echo $core->_coring_date ?></dd>
							<dt>Core type</dt><dd><?php echo ($core->_core_type_id != null) ? CoreType::getNameFromStaticList($core->_core_type_id) : "no value"; ?></dd>
							<dt>Depo context</dt><dd><?php echo ($core->_depo_context_id != null) ? DepoContext::getNameFromStaticList($core->_depo_context_id) : "no value"; ?></dd>

							<?php
							// if (isset($_SESSION['gcd_user_role']) && (($_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR) || ($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR))) {
							echo '<dt>Storage address</dt>';
							echo '<dd>';
							if (isset($core->_affiliation_id)){
								$affiliation = Affiliation::getObjectFromStaticList($core->_affiliation_id);
								echo '<address>';
								if ($affiliation->getAddress1() != null) echo '<strong>'.$affiliation->getAddress1().'</strong></br>';
								if ($affiliation->getAddress2() != null) echo $affiliation->getAddress2().'</br>';
								if ($affiliation->getCity() != null) echo $affiliation->getCity().'</br>';
								if ($affiliation->getStateProv() != null) echo $affiliation->getStateProv().'</br>';
								if ($affiliation->getCountryId() != null) {
									$country = Country::getNameFromStaticList($affiliation->getCountryId());
									if ($country != null) echo $country.'</br>';
								}
								echo '</address>';
							}
							else {echo "not known";}
							echo '</dd>';
							echo '<dt>Notes</dt>';
							echo '<dd>';
							if (isset($_SESSION['gcd_user_role']) && (($_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR) || ($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR) || ($_SESSION['gcd_user_role'] == WebAppRoleGCD::CONTRIBUTOR))) {

								echo '<div class="btn-toolbar" role="toolbar" align="right">';
								echo '            <a role="button" class="btn btn-default btn-xs" href="index.php?p=ADA/add_note_core&gcd_menu=ADA&id_core='.$core_id.'">
                                                    <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                                                    Add a note
                                                </a>
                                         </div>';

							}
							$noteList = NoteCore::getObjectsPaleofireFromWhere(sql_equal(NoteCore::ID_CORE, $core_id));
							if ($noteList != null){
								foreach($noteList as $note){
									echo '<div class="list-group">';
									echo '<h5>'.str_replace("\n", "<br/>", $note->getCoreNoteWhat()).'<br/>';
									$date = new DateTime($note->getCoreNoteDate());
									$contact = Contact::getObjectPaleofireFromId($note->getCoreNoteWho());
									$legende = 'Added '.$date->format("j F Y").' by '.$contact->getFirstName(). ' '. $contact->getLastName();
									$dataDialog = '[&quot;core&quot;,&quot;'.$note->getIdValue().'&quot;,&quot;'.str_replace([ "\r\n", "\n", "\r"],"<br>", trim($note->getCoreNoteWhat())).'&quot;,&quot;'.$legende.'&quot;]';
									echo '<small>'.$legende.'</small></h5>';
									if (isset($_SESSION['gcd_user_role']) && (($_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR) || ($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR))) {
										echo '<div class="btn-toolbar" role="toolbar" align="right">
                                                         <a role="button" class="btn btn-default btn-xs" href="index.php?p=ADA/add_note_core&gcd_menu=ADA&id='.$note->getIdValue().'">
                                                             <span class="glyphicon glyphicon-edit" aria-hidden="true"></span>
                                                             Edit
                                                         </a>';
										if(isset($_SESSION['gcd_user_role']) &&(($_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR) || ($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR))){
											echo '<a role="button" class="btn btn-default btn-xs" data-toggle="modal" data-target="#dialog-paleo" data-whatever="'.$dataDialog.'">
                                                            <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                                                            Delete
                                                         </a>';
										}
										echo '</div>';
									}
									echo '</div>';
								}
							}
							echo '</dd>';
							// }
							?>

						</dl>
					</div>
					<div class="col-md-6">
						<div id="map_core" style="width:auto"></div>
					</div>
				</div>
			</div>
			<div class="tab-pane" id="age">
				<?php
				if (($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR) || ($_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR) || ($_SESSION['gcd_user_role'] == WebAppRoleGCD::CONTRIBUTOR)){
					?>
					<div class="btn-toolbar" role="toolbar" align="right" style="float:right;margin-bottom:5px; margin-right:15px">
						<a role="button" class="btn btn-default btn-xs" href="index.php?p=ADA/add_age_model&gcd_menu=ADA&id_core=<?php echo $core_id; ?>">
							<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
							Add an age model
						</a>
					</div>
					<?php
				}
				?>
				<?php
				$tousLesGraphs = [];
				foreach ($core->getAllAgeModel() as $age_model) {
					?>
					<div class="panel panel-primary">
						<div class="panel-heading">Age model : <?php echo $age_model->getName(); ?>
							<?php if (isset($_SESSION['gcd_user_role']) && (($_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR) || ($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR))) {
								$dataDialog = '[&quot;delagemodel&quot;,&quot;'.$age_model->getIdValue().'&quot;,&quot;'.str_replace([ "\r\n", "\n", "\r"],"</br>", trim($age_model->getName())).'&quot;,&quot;&quot;]';
								?>
								<div class="btn-toolbar" role="toolbar" style="float:right">
									<a role="button" class="btn btn-default btn-xs" href="index.php?p=ADA/add_age_model&gcd_menu=ADA&id=<?php echo $age_model->getIdValue(); ?>">
										<span class="glyphicon glyphicon-edit" aria-hidden="true"></span>
										Edit this age model
									</a>
									<a role="button" class="btn btn-default btn-xs" data-toggle="modal" data-target="#dialog-paleo" data-whatever="<?php echo $dataDialog; ?>">
										<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
										Delete
									</a>
								</div>
							<?php } // END if ($_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR)?>
						</div>
						<div class="panel-body">
							<div class="container-fluid">
								<div class="row">
									<div class="col-md-5">
										<div class="panel panel-info">
											<div class="panel-heading">Age model description</div>
											<div class="panel body">
												<dl class="dl-horizontal">
													<dt>Method</dt>
													<?php
													if (isset($age_model->_age_model_method)) {
														echo "<dd>".$age_model->_age_model_method->getName()."</dd>";
													} else {
														echo "<dd>no age model method</dd>";
													}
													?>
													<dt>Modeller</dt>
													<?php
													if ($age_model->getContactId() != null){
														$contact = CONTACT::getObjectPaleofireFromId($age_model->getContactId());
														if ($contact != null){
															echo "<dd>".$contact->getFirstName()." ".$contact->getName()."</dd>";
														} else {
															echo "<dd>no modeller</dd>";
														}
													}
													?>
												</dl>
											</div>
										</div>
									</div>
									<?php
									if (isset($_SESSION['gcd_user_role']) && (($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR) || ($_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR) || ($_SESSION['gcd_user_role'] == WebAppRoleGCD::CONTRIBUTOR))){
										?>
										<div class="col-md-7">
											<div class="panel panel-info">
												<div class="panel-heading">Notes
													<div class="btn-toolbar" role="toolbar" style="float:right">
														<a role="button" class="btn btn-default btn-xs" href="index.php?p=ADA/add_note_age_model&gcd_menu=ADA&id_age_model=<?php echo $age_model->getIdValue(); ?>">
															<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
															Add a note
														</a>
													</div>
												</div>
												<ul class="list-group">
													<?php
													$noteAMList = NoteAgeModel::getObjectsPaleofireFromWhere(sql_equal(NoteAgeModel::ID_AGE_MODEL, $age_model->getIdValue()));
													if ($noteAMList != null){
														foreach($noteAMList as $note){
															echo '<li class="list-group-item"><div class="row"><div class="col-md-8">';
															echo '<h5>'.str_replace("\n", "<br/>", $note->getName()).'<br/>';
															$date = new DateTime($note->getNoteDate());
															$contact = Contact::getObjectPaleofireFromId($note->getNoteWho());
															$legende = 'Added '.$date->format("j F Y").' by '.$contact->getFirstName(). ' '. $contact->getLastName();
															$dataDialog = '[&quot;agemodel&quot;,&quot;'.$note->getIdValue().'&quot;,&quot;'.str_replace([ "\r\n", "\n", "\r"],"</br>", trim($note->getName())).'&quot;,&quot;'.$legende.'&quot;]';
															echo '<small>'.$legende.'</small></h5></div>';
															if (($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR) || ($_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR))  {
																echo '<div class="col-md-4"><div class="btn-toolbar" role="toolbar" align="right">
                                                                     <a role="button" class="btn btn-default btn-xs" href="index.php?p=ADA/add_note_age_model&gcd_menu=ADA&id='.$note->getIdValue().'">
                                                                         <span class="glyphicon glyphicon-edit" aria-hidden="true"></span>
                                                                         Edit
                                                                     </a>';
																if ($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR)  {
																	echo '<a type="button" class="btn btn-default btn-xs" data-toggle="modal" data-target="#dialog-paleo" data-whatever="'.$dataDialog.'">
                                                                         <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                                                                         Delete
                                                                     </a>
                                                                 </div></div>';
																}
															}
															echo '</div></li>';
														}
													}
													?>
												</ul>
											</div>
										</div>
										<?php
									}
									?>
								</div>
								<div class="row">
									<div class="col-md-12">
										<div class="panel panel-info">
											<div class="panel-heading">Estimated ages</div>
											<div id="chart_age<?php echo $age_model->getIDValue(); ?>"></div>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-12">
										<div class="panel panel-info">
											<?php
											if (isset($_SESSION['gcd_user_role']) && (($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR) || ($_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR) || ($_SESSION['gcd_user_role'] == WebAppRoleGCD::CONTRIBUTOR))){
												?>
												<div class="panel-heading">Date info
													<div class="btn-toolbar" role="toolbar" style="float:right">
														<a role="button" class="btn btn-default btn-xs" href="index.php?p=ADA/add_date_info&gcd_menu=ADA&id_age=<?php echo $age_model->getIdValue(); ?>">
															<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
															Add a date info
														</a>
													</div>
												</div>
												<?php
											}
											?>
											<table class="table table-bordered table-condensed table-responsive">
												<tr>
													<th>Depth</th>
													<th>Age (Error +/-)</th>
													<th>Calibrated Age (Error +/-)</th>
													<th>Units</th>
													<th>Calibration method (version)</th>
													<th>Laboratory number</th>
													<?php
													if (isset($_SESSION['gcd_user_role']) && (($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR) || ($_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR) || ($_SESSION['gcd_user_role'] == WebAppRoleGCD::CONTRIBUTOR))){
														echo '<th>Comment</th>';
													}
													?>
													<th></th>
												</tr>
												<?php
												$result_ages = $age_model->getDatesInfo();

												$tabPourGraph = [];
												$tabPourGraph1 = [];
												$tabPourGraph2 = [];
												while ($values = fetchAssoc($result_ages)) {

													//var_dump($values);
													$calibration_method = null;
													$calibration_version = null;
													$tabAges = json_decode($values['AGES']);
													$age = NULL;
													$age_calibre = NULL;
													$err_pos_calibre = NULL;
													$err_neg_calibre = NULL;
													$err_pos = NULL;
													$err_neg = NULL;

													foreach($tabAges as $eltAge){
														$age_units = AgeUnits::getObjectFromStaticList($eltAge[0]);
														if($age_units != NULL){
															if ($age_units->_age_units_calornot == true){
																$age_calibre = $eltAge[1];
																$err_pos_calibre = $eltAge[2];
																$err_neg_calibre = $eltAge[3];
																$unit_calibre = $age_units->getName();
																if($eltAge[4] != 'null') $calibration_method = CalibrationMethod::getNameFromStaticList($eltAge[4]);
																if($eltAge[5] != 'null') $calibration_version = CalibrationVersion::getNameFromStaticList($eltAge[5]);
															} else {;
																$age = $eltAge[1];
																$err_pos = $eltAge[2];
																$err_neg = $eltAge[3];
																$unit = $age_units->getName();
															}
														}
													}

													$depth = $values["DEPTH_VALUE"];
													$tooltip = "'Depth: ".$depth." Age: ".$age_calibre."(+".$err_pos_calibre."/-".$err_neg_calibre.")'";
													$tabPourGraph[] = "[".$depth.",".$age_calibre.", null,".$tooltip."]";

													echo '<tr style="font-size:85%">';
													echo "<td>".$depth."</td>";
													if (isset($age)){ echo "<td>".$age." (".$err_pos."/".$err_neg.")"."</td>";}
													else echo "<td></td>";
													if(isset($age_calibre)) {
														echo '<td><span style="color:red">'.$age_calibre."</span> (".$err_pos_calibre."/".$err_neg_calibre.")"."</td>";
														echo "<td>".$unit_calibre."</td>";
														echo "<td>".$calibration_method." ".$calibration_version."</td>";
													} else {
														echo '<td></td><td></td><td></td>';
													}

													echo "<td>".$values["DATE_LAB_NUMBER"]."</td>";
													if (isset($_SESSION['gcd_user_role'])){
														if (($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR) || ($_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR) || ($_SESSION['gcd_user_role'] == WebAppRoleGCD::CONTRIBUTOR)){
															echo "<td>".(isset($values["id_date_comment"]) ? DateComment::getFieldValueFromWhere(DateComment::NAME, sql_equal(DateComment::ID, $values["id_date_comment"])) : "")."</td>";
															echo '<td><div class="btn-group" role="toolbar">
                                        <a role="button" class="btn btn-default btn-xs" aria-disabled="true" href="index.php?p=ADA/add_date_info&id='.$values["ID_DATE_INFO"].'&age_model_id='.$age_model->getIdValue().'">
                                            <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>';
														}
														if ($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR){
															echo '<a role="button" class="btn btn-default btn-xs disabled" href="index.php?p=ADA/del_date_info&id='.$values["ID_DATE_INFO"].'&age_model='.$age_model->getIdValue().'">
                                            <span class="glyphicon glyphicon-minus" aria-hidden="true" aria-disabled="true"></span></a>
                                        <a role="button" class="btn btn-default btn-xs disabled" href="index.php?p=ADA/del_date_info&id='.$values["ID_DATE_INFO"].'">
                                            <span class="glyphicon glyphicon-remove" aria-hidden="true" aria-disabled="true"></span></a>
                                    </div></td>';
														}
													}
													echo "</tr>";
												}

												$estimatedAges = $age_model->getEstimatedAges();
												$unitpourGraph = "";

												$units = NULL;
												$iCourbe = 0;
												while ($values = fetchAssoc($estimatedAges)) {
													if ($units != $values["units"]){
														$units = $values["units"];
														$iCourbe++;
													}
													$tooltip = "'Depth: ".$values["depth"]." Estimated age: ".$values["age"]."(+".$values["pos_err"]."/-".$values["neg_err"].")";
													if (($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR) || ($_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR) || ($_SESSION['gcd_user_role'] == WebAppRoleGCD::CONTRIBUTOR)){
														$tooltip .= " Charcoals quantity : ".$values["quantity"]." ".$values["units"];
													}
													$tooltip .= "'";
													$tabPourGraph[] = "[".$values["depth"].", null, ".$values["age"].",".$tooltip."]";

													$error = "";
													if ($values["pos_err"] != NULL) $error += "+".$values["pos_err"];
													if ($values["neg_err"] != NULL) $error += "-".$values["neg_err"];
													if ($values["pos_err"] != NULL || $values["neg_err"] != NULL) $error = " (".$error.") ";
													$tooltip = "'Estimated age: ".$values["age"].$error."- Charcoals quantity (".$values["units"].") : ".$values["quantity"]."'";
													$tabPourGraph1[$units][] = "[".$values["quantity"].", ".$values["age"].",".$tooltip."]";
													$tooltip = "'Depth: ".$values["depth"] ." - Charcoals quantity (".$values["units"].") : ".$values["quantity"]."'";
													$tabPourGraph2[$units][] = "[".$values["quantity"].", ".$values["depth"].",".$tooltip."]";
													$unitpourGraph=$values["units"];
												}
												$tabPourGraph = implode($tabPourGraph, ',');

												foreach($tabPourGraph1 as $units=>$elt){
													$tabPourGraph1[$units] = implode($tabPourGraph1[$units], ',');
													$tabPourGraph2[$units] = implode($tabPourGraph2[$units], ',');
												}

												$tousLesGraphs[$age_model->getIDValue()] = $tabPourGraph;
												?>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				<?php } // foreach ($core->getAllAgeModel() as $age_model) {?>
			</div>
			<div class="tab-pane" id="charcoals">
				<?php

				$listeCharcoals = Charcoal::getCharcoalsFromCoreOrderedByUnits($core_id);

				if(count($listeCharcoals) > 0){
					if(isset($_SESSION['gcd_user_role'])){
						if ($_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR || $_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR) {
							echo '<div class="btn-toolbar" role="toolbar" align="right">
                                <a role="button" class="btn btn-default btn-xs" data-toggle="modal" data-target="#dialog-paleo" data-whatever="[&quot;changestatuscharcoals&quot;,&quot;'.$core_id.'&quot;,&quot;'.$core->getName().'&quot;]">
                                    <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                    Change status for all charcoals
                                </a>';
							if($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR){
								echo '<a role="button" class="btn btn-default btn-xs" data-toggle="modal" data-target="#dialog-paleo" data-whatever="[&quot;delcharcoals&quot;,&quot;'.$core_id.'&quot;]">
                                      <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                                      Delete all charcoals
                                  </a>';
							}
							echo '</div>';
						}
					}
				}

				$i=0;
				$numSerie = 0;
				$units = NULL;
				$tabSerie = [];

				$charcoalsTable = NULL;

				// on décode le tableau json de depths pour pouvoir fair un tri avec une fonction de comparaison
				for($i = 0; $i < count($listeCharcoals); $i++){
					$listeCharcoals[$i]["DEPTHS_LIST_DECODE"] = json_decode("[".$listeCharcoals[$i]["DEPTH_LIST"]."]");
				}
				usort($listeCharcoals, "cmpDepths");

				if ($listeCharcoals != NULL){
					foreach ($listeCharcoals as $charcoal){
						if ($units != $charcoal["ID_CHARCOAL_UNITS"]){
							$units = $charcoal["ID_CHARCOAL_UNITS"];
							$numSerie ++;

							if ($numSerie > 1) {
								echo '</table>';
								echo '</div></div></div>'; //fin div panel-info, fin div col-md-12  fin div row
							}

							$libUnitéGraph = CharcoalUnits::getNameFromStaticList($charcoal["ID_CHARCOAL_UNITS"]);
							$tabSerie[$libUnitéGraph] = $numSerie;

							echo '<div class="row">
                                <div class="col-md-12">
                                    <div class="panel panel-info">
                                        <div class="panel-heading"><h1 class="panel-title"> Quantity ('.htmlentities($libUnitéGraph, ENT_QUOTES, "UTF-8").')</h1></div>
                                        <div id="chart_quantity_age'.$core_id.$numSerie.'"></div>
                                        <div id="chart_quantity_depth'.$core_id.$numSerie.'"></div>
                                        <button type="button" class="btn btn-info" onclick="displayOrHide(this, tableCharcoals'.$numSerie.');">
                                        Show the charcoals table
                                        </button>
                                      ';

							echo '<table class="table table-bordered table-condensed table-responsive" style="display:none" id="tableCharcoals'.$numSerie.'">';
							echo '<tr>
                            <th>Name</th>
                            <th>Size</th>
                            <th>Method</th>';

							if (($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR)
								|| ($_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR)
								|| ($_SESSION['gcd_user_role'] == WebAppRoleGCD::CONTRIBUTOR)){
								echo "<th>Quantity</th>";
							}

							echo '<th>Units</th>
                            <th>Depth up (cm)</th>
                            <th>Depth middle (cm)</th>
                            <th>Depth down (cm)</th>
                            <th>Estimated age (Cal BP)</th>
                            <th>Database</th>
                            <th>Authors</th>
                            <th>Contribution</th>';

							if (($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR)
								|| ($_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR)
								|| ($data_contributor != NULL && $charcoal["ID_CONTACT"] == $data_contributor->getIdValue())){
								echo '<th>Update</th>';
								echo '<th style="width:60px;"></th>';
								echo '<th>Status</th>';
							}

							echo '</tr>';
						}
						// display charcoals if status is validated or waiting
						// OR if the connected person is the contributor of the date or an adminitrator or super administrator
						if((Status::isValid($charcoal['ID_STATUS']) || Status::isWaiting($charcoal['ID_STATUS']))
							|| ($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR)
							|| ($_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR
								|| ($data_contributor != NULL && $charcoal["ID_CONTACT"] == $data_contributor->getIdValue()))){

							$label_units = CharcoalUnits::getNameFromStaticList($charcoal["ID_CHARCOAL_UNITS"]);
							echo '<tr style="font-size:85%" id="trc'.$charcoal["ID_CHARCOAL"].'">';
							echo "<td>".$charcoal["SAMPLE_NAME"]."</td>";
							if($charcoal["CHARCOAL_SIZE_VALUE"] == NULL){
								$charcoal_size = CharcoalSize::getObjectFromStaticList($charcoal["ID_CHARCOAL_SIZE"]);
								// c'est des données issues des anciennes base de données, on affiche juste le ID_CHARCOAL_SIZE
								// et on affichage la size convertie en cm3 (si elle n'est pas déjà en cm3)
								if (strstr($charcoal_size->getName(), "cm")){
									echo "<td>". htmlentities($charcoal_size->getName(), ENT_QUOTES, "UTF-8")."</td>";
								} else {
									echo "<td>". htmlentities($charcoal_size->getName(), ENT_QUOTES, "UTF-8")." (&asymp;". htmlentities($charcoal_size->getConvertedValueFor1cm3())."cm<sup>3</sup>)</td>";
								}
							} else {
								echo "<td>".$charcoal["CHARCOAL_SIZE_VALUE"]." ".htmlentities(CharcoalSize::getNameFromStaticList($charcoal["ID_CHARCOAL_SIZE"]), ENT_QUOTES, "UTF-8")."</td>";
							}

							echo "<td>". htmlentities(CharcoalMethod::getNameFromStaticList($charcoal["ID_CHARCOAL_METHOD"]), ENT_QUOTES, "UTF-8")."</td>";
							if (($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR)
								|| ($_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR)
								|| ($_SESSION['gcd_user_role'] == WebAppRoleGCD::CONTRIBUTOR)){
								echo "<td>".$charcoal["QUANTITY"]."</td>";
							}
							echo "<td>".htmlentities($label_units, ENT_QUOTES, "UTF-8")."</td>";
							//var_dump($label_units);
							//var_dump(htmlentities($label_units, ENT_QUOTES, "UTF-8"));
							// affichage depth et estimated age
							$depthUp = NULL;
							$depthMi = NULL;
							$depthDo = NULL;
							$age_estimated = "";
							$age_err_neg = "";
							$age_err_pos = "";

							if ($charcoal["DEPTHS_LIST_DECODE"] != null){
								foreach($charcoal["DEPTHS_LIST_DECODE"] as $depth){
									switch($depth[0]){
										case 1 : $depthUp = $depth; break;
										case 2 : $depthDo = $depth; break;
										case 3 : $depthMi = $depth; break;
									}
								}
								if ($depthMi != NULL) {
									$age_estimated = $depthMi[2];
									$age_err_pos = $depthMi[3];
									$age_err_neg = $depthMi[4];
								} else if ($depthDo != NULL){
									$age_estimated = $depthDo[2];
									$age_err_pos = $depthDo[3];
									$age_err_neg = $depthDo[4];
								}else if ($depthUp != NULL){
									$age_estimated = $depthUp[2];
									$age_err_pos = $depthUp[3];
									$age_err_neg = $depthUp[4];
								}
							}
							if ($depthUp != NULL) echo "<td>".$depthUp[1]."</td>";
							else echo '<td></td>';
							if ($depthMi != NULL) echo "<td>".$depthMi[1]."</td>";
							else echo '<td></td>';
							if ($depthDo != NULL) echo "<td>".$depthDo[1]."</td>";
							else echo '<td></td>';

							$error = "";

							if ($age_err_pos != NULL) $error += "+".$age_err_pos;
							if ($age_err_neg != NULL) $error += "-".$age_err_neg;
							if ($age_err_pos != NULL || $age_err_neg != NULL) $error = "(".$error.")";
							echo "<td>".$age_estimated." ".$error."</td>";

							// on récupère la version de la bdd où le charcoal a été ajouté
							echo "<td>". DataBaseVersion::getNameFromStaticList($charcoal["ID_DATABASE"])."</td>";

							// on récupère les auteurs
							$text_authors = "";
							if ($charcoal['AUTHORS_LIST'] != NULL){
								$author_list = explode(",", $charcoal['AUTHORS_LIST']);
								if ($author_list != NULL){
									foreach($author_list as $author){
										$contact = Contact::getObjectFromStaticList($author);
										if ($contact != NULL) $text_authors .= $contact->getFirstName()." ".$contact->getLastName().", ";
									}
									$text_authors = trim($text_authors, ", ");
								}
							}
							echo "<td>".$text_authors."</td>";

							// on récupère le contributeur
							$contact = Contact::getObjectFromStaticList($charcoal["ID_CONTACT"]);
							if ($contact != null)
								echo "<td>".$contact->getFirstName()." ".$contact->getLastName()." ". $charcoal["CREATION_DATE"]."</td>";
							else echo "<td></td>";

							if (isset($_SESSION['gcd_user_role']) && (($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR)
									|| ($_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR
										|| ($data_contributor != NULL && $charcoal["ID_CONTACT"] == $data_contributor->getIdValue())))){

								// display last person who made an update
								$contact = Contact::getObjectFromStaticList($charcoal["ID_LATEST_CONTACT"]);
								if ($contact != null)
									echo "<td>".$contact->getFirstName()." ".$contact->getLastName()." ". $charcoal["UPDATE_DATE"]."</td>";
								else echo "<td></td>";

								// display toolbar to modify and delete
								echo '<td><div class="btn-group" role="toolbar">';

								echo '<a role="button" class="btn btn-default btn-xs" aria-disabled="true" href="index.php?p=ADA/add_charcoal&id='.$charcoal["ID_CHARCOAL"].'">
                                    <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>';
								if($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR){
									echo '<a role="button" class="btn btn-default btn-xs" data-toggle="modal" data-target="#dialog-paleo" data-whatever="[&quot;delcharcoal&quot;,&quot;'.$charcoal["ID_CHARCOAL"].'&quot;,&quot;'.$charcoal["SAMPLE_NAME"].'&quot;,&quot;'.$core_id.'&quot;]">
                                            <span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>';
								}
								echo '</div></td>';

								// display status
								if(Status::isValid($charcoal["ID_STATUS"])){
									echo '<td class="bg-success paleo-status">'. Status::getNameFromStaticList($charcoal["ID_STATUS"])."</td>";
								} else if (Status::isWaiting($charcoal["ID_STATUS"])){
									echo '<td class="bg-warning paleo-status">'. Status::getNameFromStaticList($charcoal["ID_STATUS"])."</td>";
								} else if (Status::isDenied($charcoal["ID_STATUS"])) {
									echo '<td class="bg-danger paleo-status">'. Status::getNameFromStaticList($charcoal["ID_STATUS"])."</td>";
								} else {
									echo '<td class="paleo-status">'. Status::getNameFromStaticList($charcoal["ID_STATUS"])."</td>";
								}
							}

							// fin de la ligne
							echo "</tr>";

							$i++;
						}// end if
					}// end foreach
					// fin du tableau
					echo '</table>';
					echo '</div></div></div>'; //fin div panel-info, fin div col-md-12  fin div row
				}// end if
				?>
			</div>
			<div class="tab-pane" id="dateinfo">
				<?php
				$listeDateInfo = DateInfo::getListeFromCore($core_id);
				//var_dump($listeDateInfo);
				?>
			</div>
			<?php  //} END if ($_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR) ?>
			<div class="tab-pane" id="data_quality">
				<div class="container-fluid">
					<div class="row">
						<?php

						$objCharcoalUnits = CharcoalUnits::getDataQuality($core_id);
						$objRepCharcoalUnits = CharcoalUnits::getRepartition($core_id);

						$tabTableAAfficher = Array(
							"status" => Array("Status", Status::getDataQuality($core_id), "status", Status::getRepartition($core_id), 1),
							"database" => Array("Database version", DataBaseVersion::getDataQuality($core_id), "database version", DataBaseVersion::getRepartition($core_id), 1),
							"charcoalSize" => Array("Charcoal size", CharcoalSize::getDataQuality($core_id), "charcoal size", CharcoalSize::getRepartition($core_id), 0),
							"dataSource" => Array("Data source", DataSource::getDataQuality($core_id), "data source", DataSource::getRepartition($core_id), 0),
							"pub" => Array("Publication", Publi::getDataQuality($core_id), "publication", Publi::getRepartition($core_id), 0),
							"charcoalMethod" => Array("Charcoal method", CharcoalMethod::getDataQuality($core_id), "charcoal method", CharcoalMethod::getRepartition($core_id), 0),
							"dateType" => Array("Date type", DateType::getDataQuality($core_id), "date type", DateType::getRepartition($core_id), 0),
							"matDated" => Array("Material dated", MatDated::getDataQuality($core_id), "material dated", MatDated::getRepartition($core_id), 0),
							"charcoalUnits" => Array("Charcoal units", CharcoalUnits::getDataQuality($core_id), "charcoal units", CharcoalUnits::getRepartition($core_id), 0)
						);

						foreach($tabTableAAfficher as $key => $elt){
							if (($elt[4] == 1 && isset($_SESSION['gcd_user_role']) && (($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR) || ($_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR)))
								|| $elt[4] == 0)
							{
								echo '<div class="col-md-6">';
								if ($elt[4] == 1) echo '  <div class="panel panel-info">';
								else echo '  <div class="panel panel-default">';

								echo '      <div class="panel-heading">
                                        <h3 class="panel-title">'.$elt[0].'</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div id="piechart_'.$key.'"></div>
                                        <div id="piechart_rep_'.$key.'" style="width: 100%;"></div>
                                    </div>
                                </div>
                            </div>';
							}
						}

						?>
					</div>
				</div>
			</div>
			<div class="tab-pane" id="publication">
				<?php
				$_link_resolver = "http://dx.doi.org/";
				$liste_publi = $core->getAllPubliIds();
				foreach ($liste_publi as $publi_id) {
					$publi = Publi::getObjectPaleofireFromId($publi_id);
					if($publi != NULL){
						echo '<div class="panel panel-info">';
						echo '<div id="pub' . $publi->getIdValue(). '">'.$publi->getName().'</div>';
						if ($publi->_publi_link != null) {echo "</br> <a href=".$publi->_publi_link.">".$publi->_publi_link."</a>";}
						if ($publi->_doi != null) {echo "</br>DOI : <a href='".$_link_resolver.$publi->_doi."'>".$publi->_doi."</a>";}
						echo '</div>';
					}
				}
				?>
			</div>
		</div>

		<?php // script google pour les geochart et les piechart ?>
		<script type="text/javascript" src="https://www.google.com/jsapi"></script>

		<script src="https://unpkg.com/leaflet@1.5.1/dist/leaflet.js"
		        integrity="sha512-GffPMF3RvMeYyc1LWMHtK8EbPv0iNZ8/oTtHPx9/cc2ILxQ+u905qIwdpULaqDkyBKgOaB57QTMg7ztg8Jm2Og=="
		        crossorigin=""></script>

		<?php // affichage piechart puis geochart ?>
		<script type="text/javascript">

			google.load("visualization", "1.1", {packages:["corechart", "line"]});
			google.setOnLoadCallback(drawChart);

			// affichage des piecharts
			function drawDocumentedUndocumentedCharts() {
				var data;
				var options;
				<?php
				foreach($tabTableAAfficher as $key => $elt){
					if (($elt[4] == 1 && isset($_SESSION['gcd_user_role'])) && (($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR) || ($_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR))
						|| $elt[4] == 0){
						$listeData = $elt[1];
						if ($listeData != null){
							echo 'var tab = '.$listeData->tabNbParElt.";\n";
							echo 'data = google.visualization.arrayToDataTable(tab);'."\n";
							echo "if (tab.length == 2 && tab[1][0] === 'undocumented')\n";
							echo "{\n options = {title : 'Documented samples for ".$elt[2]."', 
                                  'slices' : {0: {color: '#dc3912'}}, 'height':'175', 'chartArea' : {'width':'100%', 'height':'80%'}};\n";
							echo " $('#piechart_rep_".$key."').hide();\n }\n";
							echo "else options = {title: 'Percent of documented sites for ".$elt[2]."', 'height':'175', 'chartArea':{'width':'100%', 'height':'80%'}};\n";
							echo "var chart".$key." = new google.visualization.PieChart(document.getElementById('piechart_".$key."'));\n";
							echo "chart".$key.".draw(data, options);\n\n";
						} else {
							echo "$('#piechart_".$key."').text('No recorded data');";
						}
					}
				}
				?>
				$("#tabstats ul li").first().addClass("active");
				$("#tabstats div.tab-content div.tab-pane").first().addClass("active");
			}

			// affiche les diagramme camembert avec la répartition par catégorie
			function drawDistributionCharts() {
				var data;
				var options;
				<?php
				foreach($tabTableAAfficher as $key => $elt){
					if (($elt[4] == 1 && isset($_SESSION['gcd_user_role'])) && (($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR) || ($_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR))
						|| $elt[4] == 0){
						$listeData = $elt[3];
						if ($elt[1] != null){
							if ($listeData != null){
								echo 'data = google.visualization.arrayToDataTable('.$listeData->tabNbParElt.');'."\n";
								echo "options = {'title': 'Distribution of samples by ".$elt[2]."', "
									. "'height':'175', "
									. "'chartArea':{'width':'100%', 'height':'75%', 'left':'0'}, "
									. "'colors' : ['#ff9900','#109618','#990099','#0099c6','#dd4477','#66aa00','#b82e2e',"
									. "'#316395','#994499','#22aa99','#aaaa11','#6633cc','#e67300','#8b0707','#651067','#329262','#5574a6','#3b3eac',"
									. "'#b77322','#16d620','#b91383','#f4359e','#9c5935','#a9c413','#2a778d','#668d1c','#bea413','#0c5922','#743411'] "
									. "};\n";
								echo "var chart".$key." = new google.visualization.PieChart(document.getElementById('piechart_rep_".$key."'));\n";
								echo "chart".$key.".draw(data, options);\n\n";
							} else {
								// inutile d'afficher le message en double
								//echo "$('#piechart_rep_".$key."').text('No recorded data');";
							}
						}
					}
				}
				?>
			}

			// affichage des charcoals
			function drawCharcoalChart1(data, numSerie, unit) {
				var options1 = {
					chart: {'title': 'Quantity_Age', 'subtitle': ' '},
					width:1050,
					height:300,
					hAxis: {'title':'Age (cal BP)', 'direction':-1},
					vAxis: {'title':'Quantity (' + unit + ')','direction':+1},
					orientation:'vertical',
					series:{ 0:{color: 'blue', curveType:'none', pointSize:3, pointShape:'circle'}},
					chartArea:{width:'70%',height:'70%'},
					legend:'none',
				};

				var dataTable1 = new google.visualization.DataTable();
				dataTable1.addColumn('number', 'Estimated age');
				dataTable1.addColumn('number', 'Quantity(' + unit + ')');
				dataTable1.addColumn({type:'string', role:'tooltip'});
				dataTable1.addRows(data);
				var chart1 = new google.visualization.LineChart(document.getElementById('chart_quantity_age<?php echo $core_id?>'+numSerie));
				chart1.draw(dataTable1, options1);
			}

			function drawCharcoalChart2(data, numSerie, unit) {
				var options2 = {
					chart: {'title': 'Quantity_Depth', 'subtitle': ' '},
					width:1050,
					height:300,
					hAxis: {'title':'Depth (m)', 'direction':-1},
					vAxis: {'title':'Quantity (' + unit + ')','direction':+1},
					orientation:'vertical',
					series:{ 0:{curveType:'none', pointSize:3, pointShape:'circle'}},
					chartArea:{width:'70%',height:'70%'},
					legend:'none',
				};
				var dataTable2 = new google.visualization.DataTable();
				dataTable2.addColumn('number', 'Depth');
				dataTable2.addColumn('number', 'Quantity (' + unit + ')');
				dataTable2.addColumn({type:'string', role:'tooltip'});
				dataTable2.addRows(data);
				var chart2 = new google.visualization.LineChart(document.getElementById('chart_quantity_depth<?php echo $core_id?>'+numSerie));
				chart2.draw(dataTable2, options2);
			}

			// affichage des ages
			function drawChart() {
				var options = {chart: {'title': 'Age model', 'subtitle': 'Name of age model'},
					width:900,
					height:500,
					hAxis: {'title':'Age', 'direction':-1},
					vAxis: {'title':'Depth (m)','direction':-1},
					orientation:'vertical',
					series:{
						0:{color: 'red'},
						1:{color: 'blue', curveType:'none', pointSize:2, pointShape:'circle'}
					},
				};
				var dataTable;
				var chart;

				<?php foreach($tousLesGraphs as $id_age => $data){ ?>
				dataTable = new google.visualization.DataTable();
				dataTable.addColumn('number', 'Depth');
				dataTable.addColumn('number', 'Calibrated age');
				dataTable.addColumn('number', 'Estimated age');
				dataTable.addColumn({type:'string', role:'tooltip'});
				dataTable.addRows([<?php echo $data; ?>]);
				chart = new google.visualization.ScatterChart(document.getElementById('chart_age<?php echo $id_age?>'));
				chart.draw(dataTable, options);

				<?php } // END foreach($tousLesGraphs as $id_age -> $data)?>
				<?php
				if (isset($tabPourGraph1)){
					foreach($tabPourGraph1 as $unit=>$data){
						echo "\n".'drawCharcoalChart1(['.$data.'],'.$tabSerie[$unit].',"'.$unit.'");';
					}
				}
				if (isset($tabPourGraph2)){
					foreach($tabPourGraph2 as $unit=>$data){
						echo "\n".'drawCharcoalChart2(['.$data.'],'.$tabSerie[$unit].',"'.$unit.'");';
					}
				}
				?>

				drawDocumentedUndocumentedCharts();
				drawDistributionCharts();
			}

			var gcdIcon_OK = './images/marker_red.png';

			// =========================== Leaflet map initialisation =======================================
			var latlng = [<?php echo $core->_latitude; ?>, <?php echo $core->_longitude; ?>];

			var mymap = L.map('map_core').setView(latlng, 7)

			var osmUrl='http://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png';
			var osmAttrib='Map data © <a href="http://openstreetmap.org">OpenStreetMap</a> contributors. Tiles courtesy of HOT';

			L.tileLayer(osmUrl, {
				attribution: osmAttrib,
				maxZoom: 18,
				id: 'osm',
			}).addTo(mymap);

			var coreicon = L.icon({
				iconUrl: gcdIcon_OK
			});

			var marker = L.marker(
				latlng,
				{icon: coreicon}
			).addTo(mymap);
			//=========================================================================================

			$(function(){
				$('#dialog-paleo').on('shown.bs.modal', function (event) {

					var button = $(event.relatedTarget);
					var recipient = button.data('whatever');
					var modal = $(this);
					modal.find('.modal-title').html('<p class="text-danger"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> Deletion<p>');

					if (recipient[0] == 'core'){
						modal.find('.modal-body').html('<h3>Confirm the deletion of the following core note ?</h3><p>' + recipient[2] + '</p><p>' + recipient[3] + '<p>');
						modal.find('#dialog-btn-yes').attr('href', "index.php?p=ADA/del_note_core&gcd_menu=ADA&id=" + recipient[1]);
					} else if (recipient[0] == 'agemodel'){
						modal.find('.modal-body').html('<h3>Confirm the deletion of the following age model note ?</h3><p>' + recipient[2] + '</p><p>' + recipient[3] + '<p>');
						modal.find('#dialog-btn-yes').attr('href', "index.php?p=ADA/del_note_age_model&gcd_menu=ADA&id=" + recipient[1]);
					} else if (recipient[0] == 'delagemodel'){
						modal.find('.modal-body').html('<h3>Confirm the deletion of the following age model?</h3><p>' + recipient[2] + '</p><p>' + recipient[3] + '<p>');
						modal.find('#dialog-btn-yes').attr('href', "index.php?p=ADA/del_age_model&gcd_menu=ADA&id=" + recipient[1]);
					} else if (recipient[0] == 'delcharcoal'){
						modal.find('.modal-body').html('<h3>Confirm the deletion of the following charcoal?</h3><p>' + recipient[2]);
						modal.find('#dialog-btn-yes').attr('href', "index.php?p=ADA/del_charcoal&gcd_menu=ADA&id=" + recipient[1] + "&id_core=" + recipient[3]);
					} else if (recipient[0] == 'delcharcoals'){
						modal.find('.modal-body').html('<h3>Confirm the deletion of all charcoals ?</h3>');
						modal.find('#dialog-btn-yes').attr('href', "index.php?p=ADA/del_charcoal&gcd_menu=ADA&id_core=" + recipient[1]);
					} else if (recipient[0] == 'delcore'){
						modal.find('.modal-body').html('<h3>Confirm the deletion of the following core ?</h3><p><?php echo $core->getName(); ?></p>');
						modal.find('#dialog-btn-yes').attr('href', "index.php?p=ADA/del_core&id=<?php echo $core->getIdValue(); ?>");
					} else if (recipient[0] == 'changestatuscharcoals'){
						modal.find('.modal-title').html('<p class="text-primary"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> Modification<p>');
						<?php
						$listStatus = Status::getStaticList();
						$options = "";
						$tab_statut;
						foreach($listStatus as $status){
							$options .= "<option value='" . $status['ID_STATUS']. "'>" . data_securisation::tohtml($status['STATUS_DESC']) . "</option>";
							$class = "";
							if(Status::isValid($status['ID_STATUS'])) $class = "bg-success";
							else if(Status::isWaiting ($status['ID_STATUS'])) $class = "bg-warning";
							else if(Status::isDenied($status['ID_STATUS'])) $class = "bg-danger";
							$tabStatus[$status['ID_STATUS']] = [$status['STATUS_DESC'], $class];
						}
						?>
						var tabStatus = eval(<?php echo json_encode($tabStatus); ?>);
						modal.find('.modal-body').html('<h2>Select the new status for all charcoals of core ' + recipient[2] + ' : </h2>'
							+ "<select id='select_status'><?php echo $options; ?></select>");

						modal.find('#dialog-btn-yes').click(function(){
							// set mousse waiting pointer
							$('body').css("cursor", "wait");
							// hide dialog
							$("#dialog-paleo").modal("hide");
							$(this).off('click');
							// get the new value of status
							var id_status = $("#select_status").val();
							var url = "/Pages/ADA/up_status_charcoal_ajax.php?id_core=<?php echo $core_id?>&s="+ id_status +"&"+ recipient[1];
							$.getJSON(url, function(result){
								if (result != null && result.result == "success"){
									// success => change display of status in table
									$("td.paleo-status").attr("class", tabStatus[id_status][1]+ " paleo-status");
									$("td.paleo-status").text(tabStatus[id_status][0]);
									// unset mousse waiting pointer
									$('body').css("cursor", "default");
									// show dialog with success message
									$("#dialog-simple").modal("show");
									$("#dialog-simple #dialog-title").html('<span class="glyphicon glyphicon-check text-success" aria-hidden="true"></span><span class="text-success"> Success</span>');
									$("#dialog-simple #dialog-text").html('<h2>Status have been updated</h2>');

								} else {
									// unset mousse waiting pointer
									$('body').css("cursor", "default");
									// fail => show error message
									$("#dialog-simple").modal("show");
									$("#dialog-simple #dialog-title").html('<span class="glyphicon glyphicon-check text-danger" aria-hidden="true"></span><span class="text-danger"> Error</span>');
									$("#dialog-simple #dialog-text").html('<h2>An error has occured. Status have not been updated</h2>');
								}
							})
								.fail(function( jqxhr, textStatus, error ) {
									// unset mousse waiting pointer
									$('body').css("cursor", "default");
									// fail => show error message
									$("#dialog-simple").modal("show");
									$("#dialog-simple #dialog-title").html('<span class="glyphicon glyphicon-check text-danger" aria-hidden="true"></span><span class="text-danger"> Error</span>');
									$("#dialog-simple #dialog-text").html('<h2>An error has occured. Status have not been updated</h2>');
								});
						});

					}
				});
			});

			function displayOrHide(anchor, elt){
				if ($(elt).is(":visible")){
					$(elt).hide();
					$(anchor).html('<span class="glyphicon glyphicon-eye" aria-hidden="true"></span>Show the charcoals table');
				} else {
					$(elt).show();
					$(anchor).html('<span class="glyphicon glyphicon-eye" aria-hidden="true"></span>Hide the charcoals table');
				}
			}
		</script>
		<?php
	}
}