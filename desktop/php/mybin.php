<?php
if (!isConnect('admin')) {
	throw new Exception('{{401 - Accès non autorisé}}');
}
$plugin = plugin::byId('mybin');
sendVarToJS('eqType', $plugin->getId());
$eqLogics = eqLogic::byType($plugin->getId());
?>

<div class="row row-overflow">
	<div class="col-xs-12 eqLogicThumbnailDisplay">
		<legend><i class="fas fa-cog"></i>  {{Gestion}}</legend>
		<div class="eqLogicThumbnailContainer">
			<div class="cursor eqLogicAction logoPrimary" data-action="add">
				<i class="fas fa-plus-circle"></i>
				<br>
				<span>{{Ajouter}}</span>
			</div>
			<div class="cursor eqLogicAction logoSecondary" data-action="gotoPluginConf">
				<i class="fas fa-wrench"></i>
				<br>
				<span>{{Configuration}}</span>
			</div>
		</div>
		<legend><i class="icon divers-slightly"></i> {{Mes poubelles}}</legend>
		<div class="input-group" style="margin:5px;">
			<input class="form-control roundedLeft" placeholder="{{Rechercher}}" id="in_searchEqlogic"/>
			<div class="input-group-btn">
				<a id="bt_resetSearch" class="btn roundedRight" style="width:30px"><i class="fas fa-times"></i></a>
			</div>
		</div>
		<div class="eqLogicThumbnailContainer">
			<?php
			foreach ($eqLogics as $eqLogic) {
                if($eqLogic->getConfiguration('type','') == 'whole') {
                    continue;
                }
				$opacity = ($eqLogic->getIsEnable()) ? '' : 'disableCard';
				echo '<div class="eqLogicDisplayCard cursor '.$opacity.'" data-eqLogic_id="' . $eqLogic->getId() . '">';
				echo '<img src="' . $eqLogic->getImage() . '"/>';
				echo '<br>';
				echo '<span class="name">' . $eqLogic->getHumanName(true, true) . '</span>';
				echo '</div>';
			}
			?>
		</div>
	</div>

	<div class="col-xs-12 eqLogic" style="display: none;">
		<div class="input-group pull-right" style="display:inline-flex">
			<span class="input-group-btn">
				<a class="btn btn-default btn-sm eqLogicAction roundedLeft" data-action="configure"><i class="fas fa-cogs"></i> {{Configuration avancée}}
				</a><a class="btn btn-default btn-sm eqLogicAction" data-action="copy"><i class="fas fa-copy"></i> {{Dupliquer}}
				</a><a class="btn btn-sm btn-success eqLogicAction" data-action="save"><i class="fas fa-check-circle"></i> {{Sauvegarder}}
				</a><a class="btn btn-danger btn-sm eqLogicAction roundedRight" data-action="remove"><i class="fas fa-minus-circle"></i> {{Supprimer}}</a>
			</span>
		</div>
		<ul class="nav nav-tabs" role="tablist">
			<li role="presentation"><a href="#" class="eqLogicAction" aria-controls="home" role="tab" data-toggle="tab" data-action="returnToThumbnailDisplay"><i class="fa fa-arrow-circle-left"></i></a></li>
			<li role="presentation" class="active"><a href="#eqlogictab" aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-tachometer-alt"></i> {{Equipement}}</a></li>
			<li role="presentation"><a href="#commandtab" aria-controls="profile" role="tab" data-toggle="tab"><i class="fa fa-list-alt"></i> {{Commandes}}</a></li>
		</ul>
		<div class="tab-content">
			<div role="tabpanel" class="tab-pane active" id="eqlogictab">
				<br/>
				<form class="form-horizontal">
					<fieldset>
						<div class="col-lg-6">
							<legend><i class="fas fa-wrench"></i> {{Général}}</legend>
							<div class="form-group">
								<label class="col-sm-3 control-label">{{Nom de l'équipement My Bin}}</label>
								<div class="col-sm-7">
									<input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;" />
									<input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom de l'équipement My Bin}}"/>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label" >{{Objet parent}}</label>
								<div class="col-sm-7">
									<select id="sel_object" class="eqLogicAttr form-control" data-l1key="object_id">
										<option value="">{{Aucun}}</option>
										<?php	$options = '';
										foreach ((jeeObject::buildTree(null, false)) as $object) {
											$options .= '<option value="' . $object->getId() . '">' . str_repeat('&nbsp;&nbsp;', $object->getConfiguration('parentNumber')) . $object->getName() . '</option>';
										}
										echo $options;
										?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label">{{Catégorie}}</label>
								<div class="col-sm-9">
									<?php
									foreach (jeedom::getConfiguration('eqLogic:category') as $key => $value) {
										echo '<label class="checkbox-inline">';
										echo '<input type="checkbox" class="eqLogicAttr" data-l1key="category" data-l2key="' . $key . '" />' . $value['name'];
										echo '</label>';
									}
									?>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label">{{Options}}</label>
								<div class="col-sm-7">
									<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isEnable" checked/>{{Activer}}</label>
									<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isVisible" checked/>{{Visible}}</label>
								</div>
							</div>
							<br>

							<legend><i class="icon divers-slightly"></i> {{Détails de la poubelle}}</legend>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">{{Couleur de la poubelle}}</label>
                                <div class="col-sm-7">
                                    <select id="sel_object_template" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="color">
                                        <option value="green">{{Verte}}</option>
                                        <option value="yellow">{{Jaune}}</option>
                                        <option value="braun">{{Marron}}</option>
                                        <option value="blue">{{Bleue}}</option>
                                    </select>
                                </div>
                            </div>
							<div class="form-group">
								<label class="col-sm-3 control-label">{{Semaine(s) de ramassage}}</label>
								<div class="col-sm-7">
								    <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="paire" />{{Semaines paires}}</label>
                                    <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="impaire" />{{Semaines impaires}}</label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label">{{Jour(s) de ramassage}}</label>
								<div class="col-sm-7">
								    <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="day_1" />{{Lundi}}</label>
                                    <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="day_2" />{{Mardi}}</label>
                                    <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="day_3" />{{Mercredi}}</label>
                                    <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="day_4" />{{Jeudi}}</label>
                                    <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="day_5" />{{Vendredi}}</label>
                                    <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="day_6" />{{Samedi}}</label>
                                    <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="day_0" />{{Dimanche}}</label>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label">{{Heure de ramassage}}</label>
								<div class="col-sm-7">
                                    <span class="col-sm-2">
                                        <select id="sel_object_template" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="hour">
                                        <?php
                                        for ($i = 0; $i <= 23; $i++) {
                                            echo '<option value="'.$i.'">';
                                            if ($i < 10) {
                                                echo '0';
                                            }
                                            echo $i.'</option>';
                                        }
                                        ?>
                                        </select>
                                    </span>
                                    <span class="col-sm-1">
                                        <label>h</label>
                                    </span>
                                    <span class="col-sm-2">
                                        <select id="sel_object_template" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="minute">
                                        <?php
                                        for ($i = 0; $i <= 55; $i = $i + 5) {
                                            echo '<option value="'.$i.'">';
                                            if ($i < 10) {
                                                echo '0';
                                            }
                                            echo $i.'</option>';
                                        }
                                        ?>
                                        </select>
                                    </span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label">{{Notification}}</label>
								<div class="col-sm-7">
                                    <span class="col-sm-6">
                                        <select id="sel_object_template" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="notif_veille">
                                            <option value="1">{{La veille}}</option>
                                            <option value="0">{{Le jour même}}</option>
                                        </select>
                                    </span>
                                    <span class="col-sm-1">
                                        <label>{{à}}</label>
                                    </span>
                                    <span class="col-sm-2">
                                        <select id="sel_object_template" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="notif_hour">
                                        <?php
                                        for ($i = 0; $i <= 23; $i++) {
                                            echo '<option value="'.$i.'">';
                                            if ($i < 10) {
                                                echo '0';
                                            }
                                            echo $i.'</option>';
                                        }
                                        ?>
                                        </select>
                                    </span>
                                    <span class="col-sm-1">
                                        <label>h</label>
                                    </span>
                                    <span class="col-sm-2">
                                        <select id="sel_object_template" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="notif_minute">
                                        <?php
                                        for ($i = 0; $i <= 55; $i = $i + 5) {
                                            echo '<option value="'.$i.'">';
                                            if ($i < 10) {
                                                echo '0';
                                            }
                                            echo $i.'</option>';
                                        }
                                        ?>
                                        </select>
                                    </span>
								</div>
							</div>
							<br>
							<legend><i class="icon divers-slightly"></i> {{Action(s) sur collecte}}</legend>
                            <div class="form-group">
                                <a class="btn btn-success btn-sm addAction" data-type="action_collect" style="margin:5px;"><i class="fas fa-plus-circle"></i> {{Ajouter une action}}</a>
                                <div id="div_action_collect"></div>
                            </div>
						</div>
					</fieldset>
				</form>
				<hr>
			</div>

			<div role="tabpanel" class="tab-pane" id="commandtab">
				<!--<a class="btn btn-success btn-sm cmdAction pull-right" data-action="add" style="margin-top:5px;">
				<i class="fa fa-plus-circle"></i> {{Commandes}}</a><br/> -->
				<br/>
                <table id="table_cmd" class="table table-bordered table-condensed">
                    <thead>
                        <tr>
                            <th style="width:50px;">{{Id}}</th>
                            <th style="width:300px;">{{Nom}}</th>
                            <th>{{Type}}</th>
                            <th class="col-xs-3">{{Options}}</th>
                            <th class="col-xs-2">{{Action}}</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
			</div>
		</div>

	</div>
</div>

<!-- Inclusion du fichier javascript du plugin (dossier, nom_du_fichier, extension_du_fichier, nom_du_plugin) -->
<?php include_file('desktop', 'mybin', 'js', 'mybin');?>
<!-- Inclusion du fichier javascript du core - NE PAS MODIFIER NI SUPPRIMER -->
<?php include_file('core', 'plugin.template', 'js');?>