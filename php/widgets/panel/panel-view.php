<?
/*
  ____                  _  __        ___     _            _   
 |  _ \ __ _ _ __   ___| | \ \      / (_) __| | __ _  ___| |_ 
 | |_) / _` | '_ \ / _ \ |  \ \ /\ / /| |/ _` |/ _` |/ _ \ __|
 |  __/ (_| | | | |  __/ |   \ V  V / | | (_| | (_| |  __/ |_ 
 |_|   \__,_|_| |_|\___|_|    \_/\_/  |_|\__,_|\__, |\___|\__|
                                               |___/          
//////////////////// PANEL WIDGET - VIEW ////////////////////*/
?>

<div ng-controller="panelWidgetCtrl" ng-init="setPanelID('<?php echo $panel_id; ?>')" ng-cloak>
	<div ng-include="panel_url"></div>
</div>

