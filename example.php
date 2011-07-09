<?php
	echo '<html><head><script type="text/javascript" src="http://static.wowhead.com/widgets/power.js"></script></head><body>';

	include('armory_char.php');
	
	if(!isset($_POST['submit'])){
		$a = new wowchar("Emkáy", "Aegwynn", "eu", "de");
	} else {
		$a = new wowchar(utf8_decode($_POST['charakter']), $_POST['realm'], $_POST['region'], $_POST['language']);
	}
	if($a->charExist()){
		echo $a->getAvatar().' '.$a->getName().', Stufe '.$a->getLevel().' '.$a->getRace().' '.$a->getClass().' auf '.$a->getRealm().' ('.strtoupper($a->getRegion()).') - Itemlevel: '.$a->getAverageItemLevel().' (Angelegt: '.$a->getAverageItemLevelEquipped().')<br>';
		if($a->getHasGuild()) echo '<a href="'.$a->getGuildLink().'" target="_blank">'.$a->getGuildName().' (Level '.$a->getGuildLevel().', '.$a->getGuildMembers().' Mitglieder)</a><br><br>';
		echo $a->getItemIconLink('waist').'<br>';
		echo $a->getItemLevel($a->getItemID('waist')).'<br><br>';
		if($a->getHasProfessionOne()) echo $a->getProfessionOne().' ('.$a->getProfessionOneSkill().')<br>';
		if($a->getHasProfessionTwo()) echo $a->getProfessionTwo().' ('.$a->getProfessionTwoSkill().')<br><br>';
		echo $a->getActiveTalentName().' ('.$a->getActiveTalentPoints().')<br>';
		echo $a->getInactiveTalentName().' ('.$a->getInactiveTalentPoints().')<br><br>';
		echo 'Eiskronenzitadelle (normal): '.$a->getProgression('Eiskronenzitadelle').'<br>';
		echo 'Modermiene, ICC (normal): '.$a->getProgressionEncounter('Eiskronenzitadelle', 'Modermiene').'<br><br>';
	} else {
		echo "Character not found. Battle.net offline?";
	}

	echo '<br><hr><form action="'.$_SERVER['REQUEST_URI'].'" method="POST">Charaktername: <input type="text" name="charakter"><br>Realm: <input type="text" name="realm"><br>Region: <input type="text" name="region" value="eu"><br>Sprache: <input type="text" name="language" value="de"><br><br><input type="submit" name="submit" value="Charakter suchen"></form>';

	echo '</body>';
?>
