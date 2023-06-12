<?php

// grab your API key at http://steamcommunity.com/dev/apikey

// you may use this if you wish to always use the same api key,
// otherwise you'll have to specify it whenever you instantiate a SteamRequest object.

$GLOBALS["hardcode_api_key"] = "YOUR_API_KEY"; 

class SteamRequest {
	private $api_key;


	function __construct($set_api_key = "0") {
		if ($set_api_key == "0") {
			$this->api_key = $GLOBALS["hardcode_api_key"];
		}
		else
		{
			$this->api_key = $set_api_key;
		}
	}

	
	function ResolveVanityURL($url) {
		$find = strpos($url, "/id/");

		if (!$find) {
			return false;
		}

		$vanityName = substr($url, $find + 4);

		return $this->ResolveVanityName($vanityName);
	}

	function ResolveVanityName($vanityName) {
		$result = json_decode(file_get_contents("https://api.steampowered.com/ISteamUser/ResolveVanityURL/v1/?key=".$this->api_key."&vanityurl=".$vanityName));

		$response = $result->response;

		if ($response->success != 1) {
			return false;
		}

		return $response->steamid;
	}

	function GetSteamUser($SteamID) {
		$result = json_decode(file_get_contents("http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=".$this->api_key."&steamids=".$SteamID));
		
		$response = $result->response;

		if (sizeof($response->players) != 1) {
			return false;
		}

		return new SteamUser($response->players[0]);
	}
}

class SteamUser {
	public $steamid;
	public $name;
	public $profile_visibility = false;
	public $avatar_url = false;
	public $last_seen_unix = false;
	public $account_created_unix = false;
	public $status = false;
	public $playing_game = false;
	public $server_ip = false;

	function __construct($userData) {
		$this->steamid = $userData->steamid;
		$this->name = $userData->personaname;
		$this->profile_visibility = $userData->communityvisibilitystate;
		
		if (isset($userData->avatarfull))
			$this->avatar_url = $userData->avatarfull;
		
		if (isset($userData->personastate))
			$this->status = $userData->personastate;

		if (isset($userData->lastlogoff))
			$this->last_seen_unix = $userData->lastlogoff;
		
		if (isset($userData->timecreated))
			$this->account_created_unix = $userData->timecreated;

		if (isset($userData->gameextrainfo))
			$this->playing_game = $userData->gameextrainfo;
		
		if (isset($userData->gameserverip))
			$this->server_ip = $userData->gameserverip;
	}

	function GetProfileVisibility() {
		if ($this->profile_visibility == 3) {
			return "Public";
		}
		else {
			return "Private";
		}
	}

	function GetUserStatus() {
		switch ($this->status)
		{
			case 0:
				return "Offline (or private)";
			case 1:
				return "Online";
			case 2:
				return "Busy";
			case 3:
				return "Away";
			case 4:
				return "Snooze";
			case 5:
				return "Looking to trade";
			case 6:
				return "Looking to play";
			default:
				return "Unknown";
		}
	}

	function GetUserGame() {
		if ($this->playing_game) {
			return $this->playing_game;
		}
		
		return "Not playing";
	}

	function GetUserServerIP() {
		if ($this->server_ip) {
			return $this->server_ip;
		}
		
		return "Not playing any SteamWorks game match.";
	}

	function GetLastSeen() {
		if ($this->last_seen_unix) {
			return date("F j, Y, g:i a", $this->last_seen_unix);
		}
		
		return "Unknown";
	}

	function GetCreationDate() {
		if ($this->account_created_unix) {
			return date("F j, Y, g:i a", $this->account_created_unix);
		}
		
		return "Unknown";
	}
}

class SteamGame {
	public $name;
	public $price_usd;
	public $playing_right_now;

	function __construct($json) {
		// todo
	}
}

class SteamStatus {
	public $logon_service;
	public $steam_community;

	function __construct($json) {
		// todo
	}
}

class CStrikeStatus {
	public $mm_status;
	public $online_players;
	public $online_servers;
	public $searching_game;
	public $average_wait_seconds;

	function __construct($json) {
		// todo
	}
}

?>