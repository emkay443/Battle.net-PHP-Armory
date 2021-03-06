<?php
class wowchar{
	public $name = "";
	public $realm = "";
	public $region = "";
	public $language = "";
	
	public $urllang = "";
	
	public $useragent = "";
	public $header = array();

	public $level = "";
	public $class = "";

	public $load = "";
	public $json;

	/* Constructor */
	/* Expects the character's name, realm, region (eu, us, etc.) and localization (de, en, fr, etc.) */
	public function __construct($n, $r, $re, $lang){
		$this->name = strtolower($n);
		$this->realm = strtolower($r);
		$this->region = strtolower($re);
		$this->language = strtolower($lang);
		
		if(strtolower($lang) != "en"){
			$this->urllang = strtolower($lang);
		} else {
			$this->urllang = "www";
		}
		
		$lang1 = $this->language;
		if($lang1 != "en"){
			$lang2 = $lang1.'-'.strtoupper($lang1);
		} else {
			$lang2 = "en-US";
		}

		$charURL = "http://".$this->region.".battle.net/api/wow/character/".utf8_encode($this->realm)."/".utf8_encode($this->name)."?fields=items,professions,talents,mounts,titles,guild,progression";
		$this->useragent = "Mozilla/5.0 (Windows; U; Windows NT 6.1; ".$lang2."; rv:1.9.1.6) Gecko/20091201 Firefox/3.5.6 (.NET CLR 3.5.30729)";
		ini_set('user_agent',$this->useragent);
		header('Content-Type: text/html; charset=utf-8; Accept-Language: '.strtolower($lang2).','.$lang1.';q=0.5');
		$this->header[] = 'Accept-Language: '.strtolower($lang2).','.$lang1.';q=0.5';
		$curl = curl_init();
		echo curl_error($curl);
		curl_setopt ($curl, CURLOPT_URL, $charURL);
		curl_setopt($curl, CURLOPT_USERAGENT, $this->useragent);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl,CURLOPT_HTTPHEADER, $this->header);
		$load = curl_exec($curl);
		curl_close($curl);
		$this->json = json_decode($load);
	}
	
	/* Returns the raw JSON data */
	public function getJSON(){
		return $this->json;
	}

	/* Returns the character's region (EU, US, TW, etc.) */
	public function getRegion(){
		return $this->region;
	}
	
	/* Returns the localization (en, de, fr, etc.) */
	public function getLanguage(){
		return $this->language;
	}

	/* Returns true, if the character exists, and false, if not */
	public function charExist(){
		if(isset($this->json->status)){
				return false;
		} else {
			return true;
		}
	}

	/* Returns the character's name */
	public function getName(){
		return $this->json->name;
	}

	/* Returns the character's level */
	public function getLevel(){
		return $this->json->level;
	}

	/* Returns the character's realm */
	public function getRealm(){
		return $this->json->realm;
	}

	/* Returns the character's gender */
	public function getGender(){
		return $this->json->gender;
	}
	
	/* Returns the character's achievmenet points */
	public function getAchievementPoints(){
		return $this->json->achievementPoints;
	}

	/* Returns the character's average itemlevel */
	public function getAverageItemLevel(){
		return $this->json->items->averageItemLevel;
	}
	
	/* Returns the character's average itemlevel (equipped items)*/
	public function getAverageItemLevelEquipped(){
		return $this->json->items->averageItemLevelEquipped;
	}
	
	/* Returns the character's avatar URL */
	public function getAvatarLink(){
		return 'http://'.$this->getRegion().'.battle.net/static-render/'.$this->getRegion().'/'.$this->json->thumbnail;
	}
	
	/* Returns the HTML code for the character's avatar */
	public function getAvatar(){
		return '<img src="'.$this->getAvatarLink().'" border="0">';
	}
	
	
	#############################
	###    CLASS FUNCTIONS    ###
	#############################
	
	/* Returns the character's class */
	public function getClass(){
		$clsURL = "http://".$this->region.".battle.net/api/wow/data/character/classes";
		$curl = curl_init();
		echo curl_error($curl);
		curl_setopt ($curl, CURLOPT_URL, $clsURL);
		curl_setopt($curl, CURLOPT_USERAGENT, $this->useragent);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl,CURLOPT_HTTPHEADER, $this->header);
		$load = curl_exec($curl);
		curl_close($curl);
		$classes = json_decode($load)->classes;
		
		foreach($classes as $cls){
			if($cls->id == $this->getClassID()){
				return $cls->name;
			}
		}
		return false;
	}

	/* Returns the character's class id */
	public function getClassID(){
		return $this->json->class;
	}


	############################
	###    RACE FUNCTIONS    ###
	############################
	
	/* Returns the character's race */
	public function getRace(){
		$clsURL = "http://".$this->region.".battle.net/api/wow/data/character/races";
		$curl = curl_init();
		echo curl_error($curl);
		curl_setopt ($curl, CURLOPT_URL, $clsURL);
		curl_setopt($curl, CURLOPT_USERAGENT, $this->useragent);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl,CURLOPT_HTTPHEADER, $this->header);
		$load = curl_exec($curl);
		curl_close($curl);
		$races = json_decode($load)->races;
		
		foreach($races as $race){
			if($race->id == $this->getRaceID()){
				return $race->name;
			}
		}
		return false;
	}

	/* Returns the character's race id */
	public function getRaceID(){
		return $this->json->race;
	}

	/* Returns the character's faction ("Horde" or "Alliance") */
	public function getFaction(){
		$clsURL = "http://".$this->region.".battle.net/api/wow/data/character/races";
		$curl = curl_init();
		echo curl_error($curl);
		curl_setopt ($curl, CURLOPT_URL, $clsURL);
		curl_setopt($curl, CURLOPT_USERAGENT, $this->useragent);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl,CURLOPT_HTTPHEADER, $this->header);
		$load = curl_exec($curl);
		curl_close($curl);
		$races = json_decode($load)->races;

		foreach($races as $race){
			if($race->id == $this->getRaceID()){
				return ucfirst($race->side);
			}
		}
		return false;
	}

	/* Returns the character's faction id (1 for Horde, 0 for Alliance) */
	public function getFactionID(){
		switch($this->json->race){
			case 1:
			case 3:
			case 4:
			case 7:
			case 11:
			case 22:
				return 0;
				break;
			default:
				return 1;
		}  
	}


	#############################
	###    GUILD FUNCTIONS    ###
	#############################
	
	/* Returns true, if the character has a guild */
	public function getHasGuild(){
		return (isset($this->json->guild->name));
	}
	
	/* Returns the guild's name or false, if the character is not in a guild */
	public function getGuildName(){
		if($this->getHasGuild()){
			return $this->json->guild->name;
		} else {
			return false;
		}
	}	
	
	/* Returns the guild's level or false, if the character is not in a guild */
	public function getGuildLevel(){
		if($this->getHasGuild()){
			return $this->json->guild->level;
		} else {
			return false;
		}
	}
	
	/* Returns the guild's member count or false, if the character is not in a guild */
	/* BUGGED AS OF 2011-07-09 */
	public function getGuildMembers(){
		if($this->getHasGuild()){
			return $this->json->guild->members;
		} else {
			return false;
		}
	}
	
	/* Returns the guild's achievement points or false, if the character is not in a guild */
	/* BUGGED AS OF 2011-07-09 */
	public function getGuildAchievementPoints(){
		if($this->getHasGuild()){
			return $this->json->guild->achievementPoints;
		} else {
			return false;
		}
	}
	
	/* Returns the url to the character's guild or false, if the character is not in a guild */
	public function getGuildLink(){
		if($this->getHasGuild()){
			return 'http://'.$this->getRegion().'.battle.net/wow/'.$this->getLanguage().'/guild/'.strtolower($this->getRealm()).'/'.$this->getGuildName().'/';
		} else {
			return false;
		}
	}


	############################
	###    ITEM FUNCTIONS    ###
	############################
	
	/* Possible item slots are: */
	/* head, neck, back, chest, shirt, tabard, wrist, hands, waist, legs, feet, finger1, finger2, trinket1, trinket2, mainHand, offHand, ranged */
	
	/* Expects item slot name */
	/* Returns the raw data of the item in the given slot */
	public function getItemData($slot){
		if(isset($this->json->items->$slot)){
				return $this->json->items->$slot;
		} else {
			return false;
		}
	}

	/* Expects item slot name */
	/* Returns the name of the item in the given slot */
	public function getItemName($slot){
		if(isset($this->json->items->$slot)){
				return $this->json->items->$slot->name;
		} else {
			return false;
		}
	}

	/* Same as above */
	public function getItem($slot){
		return $this->getItemName($slot);
	}

	/* Expects item slot name */
	/* Returns the id of the item in the given slot */
	public function getItemID($slot){
		if(isset($this->json->items->$slot)){
				return $this->json->items->$slot->id;
		} else {
			return false;
		}
	}

	/* Expects item slot name */
	/* Returns the icon url (56x56) of the item in the given slot */
	public function getItemIcon($slot){
		if(isset($this->json->items->$slot)){
				return 'http://eu.media.blizzard.com/wow/icons/56/'.$this->json->items->$slot->icon.'.jpg';
		} else {
			return false;
		}
	}

	/* Expects item slot name */
	/* Returns the rarity color of the item in the given slot */
	public function getItemColor($slot){
		if(isset($this->json->items->$slot)){
				switch($this->json->items->$slot->quality){
					case 0:
						return '#9d9d9d';
						break;
					case 1:
						return '#ffffff';
						break;
					case 2:
						return '#1eff00';
						break;
					case 3:
						return '#0070ff';
						break;
					case 4:
						return '#a335ee';
						break;
					case 5:
						return '#ff8000';
						break;
					case 6:
						return '#e6cc80';
						break;
					case 7:
						return '#e6cc80';
						break;
					default:
						return false;
				}
		} else {
			return false;
		}
	}
	
	/* Expects item slot name */
	/* Returns a list of gems ("gem0:gem1:gem2") in the item in the given slot */
	public function getItemGems($slot){
		if(isset($this->json->items->$slot)){
			$tParm = $this->json->items->$slot->tooltipParams;
			$output = '';
			if(isset($tParm->gem0)){
				$output = $tParm->gem0;
			}
			if(isset($tParm->gem1)){
				$output .= ':'.$tParm->gem1;
			}
			if(isset($tParm->gem2)){
				$output .= ':'.$tParm->gem2;
			}
			if(isset($tParm->gem3)){
				$output .= ':'.$tParm->gem3;
			}
			if(isset($tParm->gem4)){
				$output .= ':'.$tParm->gem4;
			}
			if($slot == "waist" && $this->getLevel() >= 80){
				$output .= ';sock';
			}
			return $output;
		} else {
			return false;
		}		
	}
	
	/* Expects item slot name */
	/* Returns the enchant on the item in the given slot or false if not existing or unenchanted */
	public function getItemEnchant($slot){
		if(isset($this->json->items->$slot)){
			$tParm = $this->json->items->$slot->tooltipParams;
			if(isset($tParm->enchant)){
				return $tParm->enchant;
			} else {
				return false;
			}
		} else {
			return false;
		}	
	}
	
	/* Expects item slot name */
	/* Returns a list of equipped set items ("item0:item1:item2") of the same set as the item in the given slot, or empty string, if there is no set, or false, if the item doesn't exist */
	public function getItemSetItems($slot){
		if(isset($this->json->items->$slot)){
			if(isset($this->json->items->$slot->tooltipParams->set)){
				$count = 1;
				$output = '';
				foreach($this->json->items->$slot->tooltipParams->set as $setItem){
					if($count != 1) $output .= ':';
					$output .= $setItem;
					$count++;
				}
				return $output;
			} else {
				return false;
			}
		} else {
			return false;
		}	
	}
	
	/* Expects item slot name */
	/* Returns the WoWHead URL of the item in the given slot, or false, if the item doesn't exist */
	public function getItemLink($slot){
		if(isset($this->json->items->$slot)){
			return 'http://'.$this->urllang.'wowhead.com/item='.$this->json->items->$slot->id.'" rel="gems='.$this->getItemGems($slot).';ench='.$this->getItemEnchant($slot).';pcs='.$this->getItemSetItems($slot);
		} else {
			return false;
		}
	}
	
	/* Expects item slot name */
	/* Returns the WoWHead URL on the item's icon with a 2px border colored by rarity, or false, if the item doesn't exist */
	public function getItemIconLink($slot){
		if(isset($this->json->items->$slot)){
			return '<a href="'.$this->getItemLink($slot).'" target="_blank"><img src="'.$this->getItemIcon($slot).'" style="border:2px solid '.$this->getItemColor($slot).'"></a>';
		} else {
			return false;
		}		
	}
	
	/* Expects item id */
	/* Returns the itemlevel of the item with the given id or false, if an item with that id doesn't exist */
	public function getItemLevel($itemID){
		$itemURL = "http://".$this->region.".battle.net/api/wow/data/item/".$itemID;
		$curl = curl_init();
		echo curl_error($curl);
		curl_setopt ($curl, CURLOPT_URL, $itemURL);
		curl_setopt($curl, CURLOPT_USERAGENT, $this->useragent);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl,CURLOPT_HTTPHEADER, $this->header);
		$load = curl_exec($curl);
		curl_close($curl);
		$item = json_decode($load);
		
		if(isset($item->itemLevel)){
			return $item->itemLevel;
		} else {
			return false;
		}
	}


	##################################
	###    PROFESSION FUNCTIONS    ###
	##################################
	
	/* Returns true, if the player has a profession or false, if not */
	public function getHasProfessionOne(){
		if($this->getHasProfessionOne()){
			return true;
		} else {
			return false;
		}
	}
	
	/* Returns the name of the character's first profession or false, if the player doesn't have a profession */
	public function getProfessionOne(){
		if($this->getHasProfessionOne()){
			return $this->json->professions->primary[0]->name;
		} else {
			return false;
		}
	}
	
	/* Returns the raw data of the character's first profession or false, if the player doesn't have a profession */
	public function getProfessionOneData(){
		if($this->getHasProfessionOne()){
			return $this->json->professions->primary[0];
		} else {
			return false;
		}
	}
	
	/* Returns the skill ("Skill/Max") of the character's first profession or false, if the player doesn't have a profession */
	public function getProfessionOneSkill(){
		if($this->getHasProfessionOne()){
			return $this->getProfessionOneData()->rank.'/'.$this->getProfessionOneData()->max;
		} else {
			return false;
		}
	}
	
	/* Returns true, if the player has a second profession or false, if not */
	public function getHasProfessionTwo(){
		if($this->getHasProfessionTwo()){
			return true;
		} else {
			return false;
		}
	}
	
	/* Returns the name of the character's second profession or false, if the player doesn't have a second profession */
	public function getProfessionTwo(){
		if($this->getHasProfessionTwo()){
			return $this->json->professions->primary[1]->name;
		} else {
			return false;
		}
	}
	
	/* Returns the raw data of the character's second profession or false, if the player doesn't have a second profession */
	public function getProfessionTwoData(){
		if($this->getHasProfessionTwo()){
			return $this->json->professions->primary[1];
		} else {
			return false;
		}
	}
	
	/* Returns the skill ("Skill/Max") of the character's second profession or false, if the player doesn't have a second profession */
	public function getProfessionTwoSkill(){
		if($this->getHasProfessionTwo()){
			return $this->getProfessionTwoData()->rank.'/'.$this->getProfessionTwoData()->max;
		} else {
			return false;
		}
	}
	
	/* Returns how many professions the character has (0, 1 or 2) */
	public function getProfessionCount(){
		$count = 0;
		if(isset($this->json->professions->primary)){
			foreach($this->json->professions->primary as $tmp){
				$count++;
			}
		}
		return $count;
	}
	
	/* Returns true, if the character has learned "First Aid, or false, if not" */
	public function getHasFirstAid(){
		if(isset($this->json->professions->secondary)){
			foreach($this->json->professions->secondary as $tmp){
				if($tmp->id == 129) return true;
			}
			return false;
		}
	}
	
	/* Returns the skill ("Skill/Max") of "First Aid" or false, if the player hasn't learned "First Aid" */
	public function getFirstAidSkill(){
		if($this->getHasFirstAid()){
			foreach($this->json->professions->secondary as $tmp){
				if($tmp->id== 129) return $tmp->rank.'/'.$tmp->max;
			}
			return false;
		}
	}
	
	/* Returns true, if the character has learned "Fishing", or false, if not */
	public function getHasFishing(){
		if(isset($this->json->professions->secondary)){
			foreach($this->json->professions->secondary as $tmp){
				if($tmp->id == 356) return true;
			}
			return false;
		}
	}
	
	/* Returns the skill ("Skill/Max") of "Fishing" or false, if the player hasn't learned "Fishing" */
	public function getFishingSkill(){
		if($this->getHasFishing()){
			foreach($this->json->professions->secondary as $tmp){
				if($tmp->id== 356) return $tmp->rank.'/'.$tmp->max;
			}
			return false;
		}
	}
	
	/* Returns true, if the character has learned "Cooking" */
	public function getHasCooking(){
		if(isset($this->json->professions->secondary)){
			foreach($this->json->professions->secondary as $tmp){
				if($tmp->id == 185) return true;
			}
			return false;
		}
	}
	
	/* Returns the skill ("Skill/Max") of "Cooking" or false, if the player hasn't learned "Cooking" */
	public function getCookingSkill(){
		if($this->getHasCooking()){
			foreach($this->json->professions->secondary as $tmp){
				if($tmp->id== 185) return $tmp->rank.'/'.$tmp->max;
			}
			return false;
		}
	}
	
	/* Returns true, if the character has learned "Archaeology", or false, if not */
	public function getHasArchaeology(){
		if(isset($this->json->professions->secondary)){
			foreach($this->json->professions->secondary as $tmp){
				if($tmp->id == 794) return true;
			}
			return false;
		}
	}
	
	/* Returns the skill ("Skill/Max") of "Archaeology" or false, if the player hasn't learned "Archaeology" */
	public function getArchaeologySkill(){
		if($this->getHasArchaeology()){
			foreach($this->json->professions->secondary as $tmp){
				if($tmp->id== 794) return $tmp->rank.'/'.$tmp->max;
			}
			return false;
		}
	}


	##############################
	###    TALENT FUNCTIONS    ###
	##############################
	
	/* Returns, how many talent specs the player has, or false, if not */
	public function getHasTalents(){
		if(isset($this->json->talents)){
			return count($this->json->talents);
		}
		return false;
	}
	
	/* Returns the raw data of the first talent spec, or false, if the player hasn't got talents */
	public function getTalentOneData(){
		if($this->getHasTalents()){
			return $this->json->talents[0];
		}
		return false;
	}
	
	/* Returns the name of the first talent spec, or false, if the player hasn't got talents */
	public function getTalentOneName(){
		if($this->getTalentOneData()){
			return $this->getTalentOneData()->name;
		}	
		return false;
	}
	
	/* Returns the talent points ("0/1/2") of the first talent spec, or false, if the player hasn't got talents */
	public function getTalentOnePoints(){
		if($this->getTalentOneData()){
			$count = 0;
			$out = "";
			foreach($this->getTalentOneData()->trees as $tmp){
				if($count > 0) $out .= "/";
				$out .= $tmp->total;
				$count++;
			}
			return $out;
		}
		return false;
	}
	
	/* Returns the raw data of the second talent spec, or false, if the player hasn't got dualspec */
	public function getTalentTwoData(){
		if($this->getHasTalents() == 2){
			return $this->json->talents[1];
		}
		return false;
	}
	
	/* Returns the name of the second talent spec, or false, if the player hasn't got dualspec */
	public function getTalentTwoName(){
		if($this->getTalentTwoData()){
			return $this->getTalentTwoData()->name;
		}	
		return false;
	}
	
	/* Returns the talent points ("0/1/2") of the second talent spec, or false, if the player hasn't got dualspec */
	public function getTalentTwoPoints(){
		if($this->getTalentTwoData()){
			$count = 0;
			$out = "";
			foreach($this->getTalentTwoData()->trees as $tmp){
				if($count > 0) $out .= "/";
				$out .= $tmp->total;
				$count++;
			}
			return $out;
		}
		return false;
	}
	
	/* Returns the id (0 or 1) of the active talent spec */
	public function getActiveTalent(){
		$count = 0;
		foreach($this->json->talents as $spec){
			if(isset($spec->name)) return $count;
			$count++;
		}
		return -1;
	}
	
	/* Returns the name of the active talent spec */
	public function getActiveTalentName(){
		if($this->getActiveTalent() != -1){
			if(isset($this->json->talents[$this->getActiveTalent()]->name)) {
				return $this->json->talents[$this->getActiveTalent()]->name;
			} else {
				return false;
			}
		}
		return false;
	}
	
	/* Returns the talent points ("0/1/2") of the active talent spec */
	public function getActiveTalentPoints(){
		if($this->getActiveTalent() != -1){
			$count = 0;
			$out = "";
			foreach($this->json->talents[$this->getActiveTalent()]->trees as $tmp){
				if($count > 0) $out .= "/";
				$out .= $tmp->total;
				$count++;
			}
			return $out;
		}
		return false;	
	}
	
	/* Returns the id (0 or 1) of the inactive talent spec */
	public function getInactiveTalent(){
		$count = 0;
		foreach($this->json->talents as $spec){
			if(!isset($spec->selected)) return $count;
			$count++;
		}
		return -1;
	}
	
	/* Returns the name of the inactive talent spec */
	public function getInactiveTalentName(){
		if($this->getInactiveTalent() != -1){
			if(isset($this->json->talents[$this->getInactiveTalent()]->name)){
				return $this->json->talents[$this->getInactiveTalent()]->name;
			} else {
				return false;
			}
		}
		return false;
	}
	
	/* Returns the talent points ("0/1/2") of the inactive talent spec */
	public function getInactiveTalentPoints(){
		if($this->getInactiveTalent() != -1){
			$count = 0;
			$out = "";
			foreach($this->json->talents[$this->getInactiveTalent()]->trees as $tmp){
				if($count > 0) $out .= "/";
				$out .= $tmp->total;
				$count++;
			}
			return $out;
		}
		return false;	
	}
	
	/* Returns the URL to the first talentspec or false, if the player hasn't got any talents */
	public function getTalentOneLink(){
		if($this->getHasTalens() > 0){
			return 'http://'.$this->getRegion().'.battle.net/wow/'.$this->getLanguage().'/character/'.$this->getRealm().'/'.$this->getName().'/talent/primary';
		} else {
			return false;
		}
	}
	
	/* Returns the URL to the second talentspec or false, if the player hasn't got dualspec */
	public function getTalentTwoLink(){
		if($this->getHasTalens() > 0){
			return 'http://'.$this->getRegion().'.battle.net/wow/'.$this->getLanguage().'/character/'.$this->getRealm().'/'.$this->getName().'/talent/secondary';
		} else {
			return false;
		}
	}


	###################################
	###    PROGRESSION FUNCTIONS    ###
	###################################
	
	/* Expects raid name (localized!) */
	/* Returns, how often the character has completed the given raid, or false, if there isn't any raid with the given name */
	public function getProgression($raid){
		if(isset($this->json->progression->raids)){
			$found = false;
			foreach($this->json->progression->raids as $r){
				if($r->name == $raid){
					$found = $r;
				}
			}
			if(!$found){
				return false;
			} else {
				return $found->normal;
			}
		} else {
			return false;
		}
	}
	
	/* Expects raid and boss name (localized!) */
	/* Returns, how often the character has killed the given boss in the given raid, or false, if there isn't any boss or raid with the given names */
	public function getProgressionEncounter($raid, $encounter){
		if(isset($this->json->progression->raids)){
			$found1 = false;
			$found2 = false;
			foreach($this->json->progression->raids as $r){
				if($r->name == $raid){
					$found1 = $r;
				}
			}
			if(!$found1){
				return false;
			} else {
				foreach($found1->bosses as $b){
					if($b->name == $encounter){
						$found2 = $b;
					}
				}
				if(!$found2){
					return false;
				} else {
					return $found2->normalKills;
				}
			}
		} else {
			return false;
		}
	}
	
	/* Expects raid name (localized!) */
	/* Returns, how often the character has completed the given raid on heroic mode, or false, if there isn't any raid with the given name */
	public function getProgressionHeroic($raid){
		if(isset($this->json->progression->raids)){
			$found = false;
			foreach($this->json->progression->raids as $r){
				if($r->name == $raid){
					$found = $r;
				}
			}
			if(!$found){
				return false;
			} else {
				return $found->heroic;
			}
		} else {
			return false;
		}
	}
	
	/* Expects raid and boss name (localized!) */
	/* Returns, how often the character has killed the given boss in the given raid on heroic mode, or false, if there isn't any boss or raid with the given names */
	public function getProgressionEncounterHeroic($raid, $encounter){
		if(isset($this->json->progression->raids)){
			$found1 = false;
			$found2 = false;
			foreach($this->json->progression->raids as $r){
				if($r->name == $raid){
					$found1 = $r;
				}
			}
			if(!$found1){
				return false;
			} else {
				foreach($found1->bosses as $b){
					if($b->name == $encounter){
						$found2 = $b;
					}
				}
				if(!$found2){
					return false;
				} else {
					return $found2->heroicKills;
				}
			}
		} else {
			return false;
		}
	}
}
?>
