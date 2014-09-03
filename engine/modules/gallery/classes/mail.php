<?PHP
/*
=====================================================
 DataLife Engine - by SoftNews Media Group
 TWS Gallery - by Al-x
-----------------------------------------------------
 Version TWS Gallery 5.2
 Powered by http://wonderfullife.ru/
 Support by http://wonderfullife.ru/, http://inker.wonderfullife.ru/
-----------------------------------------------------
 Copyright (c) 2007,2012 TWS
=====================================================
 Данный код защищен авторскими правами
 This file may no be redistributed in whole or significant part.	
 Файл не может быть изменён или использован без прямого согласия автора
 Запрещается использование файла в люббых комменрческих целях
=====================================================
 Файл: class.mail.php
-----------------------------------------------------
 Назначение: Класс генерации письма
=====================================================
 Версия class.mail.php 1,2
=====================================================
*/
if(!defined('DATALIFEENGINE'))
{
  die("Hacking attempt!");
}

class Mailer {

	var $template	= "";
	var $e_template	= "";
	var $c_template	= "";
	var $pm_bot		= "";
	var $subject	= "";
	var $data 		= array();
	var $block_data 		= array();
	var $use_html	= false;
	var $mail = false;

	function Mailer_set($pm_send=false,$pm_bot='',$set_new=0,$mail_metod='',$admin_mail='',$smtp_host='',$smtp_port=0,$smtp_user='',$smtp_pass=''){
	global $config;

		if ($pm_send && $pm_bot==''){

			global $db;

			$row = $db->super_query("SELECT name FROM " . USERPREFIX . "_users WHERE user_id=1 LIMIT 0,1");
			$pm_bot = $row['name'];

		}

		if (!$pm_send){

			if ($set_new){

				$config['mail_metod'] = $mail_metod;
				$config['admin_mail'] = $admin_mail;
				$config['smtp_host'] = $smtp_host;
				$config['smtp_port'] = intval($smtp_port);
				$config['smtp_user'] = $smtp_user;
				$config['smtp_pass'] = $smtp_pass;

			}

			include_once ENGINE_DIR.'/classes/mail.class.php';
			$this->mail = new dle_mail ($config, $this -> use_html);

		} else $this -> clear_vars_once($pm_bot);

		if ($this -> template != "") $this -> load_template($this -> template);

	}

	function load_template($tpl_name){
	global $db;

		$row = $db->super_query("SELECT template FROM " . PREFIX . "_tws_email WHERE name='{$tpl_name}' LIMIT 0,1");

		if (!$row['template']) { die ("Template not found: ".$tpl_name); }

		$this -> template = $tpl_name;
		$this -> e_template = $row['template'];

	}

	function clear_vars_once ($pm_bot){
	global $db;

		$this -> subject = $db->safesql($this -> subject);
		$this -> pm_bot = $db->safesql($pm_bot);

	}

	function bbcode_tpl(){

		include_once ENGINE_DIR . '/classes/parse.class.php';

		$parse = new ParseFilter( );
		$parse->safe_mode = true;
		$parse->allow_url = true;
		$parse->allow_image = true;

		//$this -> e_template = $parse->BB_Parse($parse->process($this -> e_template), false);

	}

	function do_send_message ($email, $user_id = 0){
	global $db;

		$this -> compile();

		if (!$this -> c_template || !$this -> subject) return 0;

		if ($this -> pm_bot == ""){

			$this->mail->send(stripslashes($email), $this -> subject, $this -> c_template);

			return $this->mail->send_error;

		} else {

			$this -> c_template = stripslashes(str_replace(array("\r\n", "\n"), "<br />", $this -> c_template));

			$this -> c_template = $db->safesql($this -> c_template);
			$user_id = intval($user_id);

			if (!$user_id && $email){

				$email = $db->safesql($email);
				$user_to = $db->super_query("SELECT user_id FROM " . USERPREFIX . "_users where email = '".$email."'");
				$user_id = $user_to['user_id'];

			}

			if (!$user_id) return 0;

			$db->query("INSERT INTO " . USERPREFIX . "_pm (subj, text, user, user_from, date, pm_read, folder) values ('".$this -> subject."', '".$this -> c_template."', '".$user_id."', '".$this -> pm_bot."', '".TIME."', 'no', 'inbox')");
			$db->query("UPDATE " . USERPREFIX . "_users set pm_all=pm_all+1, pm_unread=pm_unread+1 where user_id='".$user_id."'");

		}

	}


	function set($name, $var) {

		if (is_array($var) && count($var)) {

			foreach ($var as $key => $key_var) {

				$this->set($key , $key_var);

			} 

		} else $this -> data[$name] = $var;

	}


	function set_block($name, $var){

		if (is_array($var) && count($var)){

			foreach ($var as $key => $key_var){

				$this->set_block($key , $key_var);
	
			}

		} else $this->block_data[$name] = $var;

	}


	function compile($global=0) {

		foreach ($this->data as $key_find => $key_replace) {
		
				$find[] = $key_find;
				$replace[] = $key_replace;
		
		}
	
	if ($global)
		$this -> e_template = str_replace($find, $replace, $this -> e_template);
	else
		$this -> c_template = str_replace($find, $replace, $this -> e_template);

	if (count($this->block_data)) {

		foreach ($this->block_data as $key_find => $key_replace) {

			$find_preg[] = $key_find;
			$replace_preg[] = $key_replace;

		}

		if ($global)
			$this -> e_template = preg_replace($find_preg, $replace_preg, $this -> e_template);
		else
			$this -> c_template = preg_replace($find_preg, $replace_preg, $this -> c_template);

	}

	$this->_clear();

	}

	function _clear() {

		$this->data = array();
		$this->block_data = array();

	}

	function clear() {

		$this->data = array();
		$this->block_data = array();
		$this->e_template = null;
		$this->c_template = null;
		$this->template = "";

	}

}

?>