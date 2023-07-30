<?php
/*
 * Filename: SimpleAuth.php
 * Version: 1.0
 * Author: Heiswayi Nrird
 * Dscription: Simple PHP script to protect any PHP page using session
 * Website: https://heiswayi.nrird.com
 *
 * HOW TO USE
 * ==========
 * Just include this code snippet on the top of your PHP page you want to protect:
 * ```
 * <?php
 * session_start();
 * require("SimpleAuth.php");
 * $login = new Login;
 * $login->authorize();
 * ?>
 * ```
 *
 * To logout, put this line of action URI at the end of your page name:
 * Example:
 * <a href="index.php?action=clear_login">Logout</a>
 *
 */

// SETTINGS
// ------------------------------------------------------------------------------------------------
// Set your username
define('LOGIN_USER', "Rivera");
// Set your password
define('LOGIN_PASS', "23River@B");

// LANGUAGES
// ------------------------------------------------------------------------------------------------
define('LOGOUT_COMPLETE', "You've been successfully logged out.");
define('INCORRECT_USERNAME_PASSWORD', "Invalid login credentials!");
define('STARTER_GREETING', "This page is restricted, please log in.");
define('USERNAME', "Username");
define('PASSWORD', "Password");
define('ENTER_USERNAME', "Enter Username");
define('ENTER_PASSWORD', "Enter Password");
define('REMEMBER_THIS_COMPUTER', "Remember this computer");
define('BUTTON_LOGIN', "Log in &rarr;");

// ================================================================================================
// ### DO NOT TOUCH ANYTHING BELOW THIS LINE ###
// ================================================================================================

class Login {
	// unique prefix that is used with this object (on cookies and password salt)
	var $prefix = "login_";
	// days "remember me" cookies will remain
	var $cookie_duration = 21;
	// temporary values for comparing login are auto set here. do not set your own $user or $pass here
	var $user = "";
	var $pass = "";

  function authorize() {
	//save cookie info to session
	if(isset($_COOKIE[$this->prefix.'user'])){
		$_SESSION[$this->prefix.'user'] = $_COOKIE[$this->prefix.'user'];
		$_SESSION[$this->prefix.'pass'] = $_COOKIE[$this->prefix.'pass'];
	}

	//if setting vars
	if(isset($_POST['action']) && $_POST['action'] == "set_login"){

		$this->user = $_POST['user'];
		$this->pass = md5($this->prefix.$_POST['pass']); //hash password. salt with prefix

		$this->check();//dies if incorrect

		//if "remember me" set cookie
		if(isset($_POST['remember'])){
			setcookie($this->prefix."user", $this->user, time()+($this->cookie_duration*86400));// (d*24h*60m*60s)
			setcookie($this->prefix."pass", $this->pass, time()+($this->cookie_duration*86400));// (d*24h*60m*60s)
		}

		//set session
		$_SESSION[$this->prefix.'user'] = $this->user;
		$_SESSION[$this->prefix.'pass'] = $this->pass;
	}

	//if forced log in
	elseif(isset($_GET['action']) && $_GET['action'] == "prompt"){
		session_unset();
		session_destroy();
		//destroy any existing cookie by setting time in past
		if(!empty($_COOKIE[$this->prefix.'user'])) setcookie($this->prefix."user", "blanked", time()-(3600*25));
		if(!empty($_COOKIE[$this->prefix.'pass'])) setcookie($this->prefix."pass", "blanked", time()-(3600*25));

		$this->prompt();
	}

	//if clearing the login
	elseif(isset($_GET['action']) && $_GET['action'] == "clear_login"){
		session_unset();
		session_destroy();
		//destroy any existing cookie by setting time in past
		if(!empty($_COOKIE[$this->prefix.'user'])) setcookie($this->prefix."user", "blanked", time()-(3600*25));
		if(!empty($_COOKIE[$this->prefix.'pass'])) setcookie($this->prefix."pass", "blanked", time()-(3600*25));

		$msg = '<span class="green">'.LOGOUT_COMPLETE.'</span>';
		$this->prompt($msg);
	}

	//prompt for
	elseif(!isset($_SESSION[$this->prefix.'pass']) || !isset($_SESSION[$this->prefix.'user'])){
		$this->prompt();
	}

	//check the pw
	else{
		$this->user = $_SESSION[$this->prefix.'user'];
		$this->pass = $_SESSION[$this->prefix.'pass'];
		$this->check();//dies if incorrect
	}

}

function check(){

	if(md5($this->prefix . LOGIN_PASS) != $this->pass || LOGIN_USER != $this->user){
		//destroy any existing cookie by setting time in past
		if(!empty($_COOKIE[$this->prefix.'user'])) setcookie($this->prefix."user", "blanked", time()-(3600*25));
		if(!empty($_COOKIE[$this->prefix.'pass'])) setcookie($this->prefix."pass", "blanked", time()-(3600*25));
		session_unset();
		session_destroy();

		$msg='<span class="red">'.INCORRECT_USERNAME_PASSWORD.'</span>';
		$this->prompt($msg);
	}
}

function prompt($msg=''){
?>
<html><head><title><?php echo STARTER_GREETING; ?></title>	<style>
article,aside,details,figcaption,figure,footer,header,hgroup,main,nav,section,summary{display:block}audio,canvas,video{display:inline-block;*display:inline;*zoom:1}audio:not([controls]){display:none;height:0}[hidden]{display:none}html{font-size:100%;-ms-text-size-adjust:100%;-webkit-text-size-adjust:100%}html,button,input,select,textarea{font-family:sans-serif}body{margin:0}a:focus{outline:thin dotted}a:active,a:hover{outline:0}h1{font-size:2em;margin:.67em 0}h2{font-size:1.5em;margin:.83em 0}h3{font-size:1.17em;margin:1em 0}h4{font-size:1em;margin:1.33em 0}h5{font-size:.83em;margin:1.67em 0}h6{font-size:.67em;margin:2.33em 0}abbr[title]{border-bottom:1px dotted}b,strong{font-weight:700}blockquote{margin:1em 40px}dfn{font-style:italic}hr{-moz-box-sizing:content-box;box-sizing:content-box;height:0}mark{background:#ff0;color:#000}p,pre{margin:1em 0}code,kbd,pre,samp{font-family:monospace,serif;_font-family:'courier new',monospace;font-size:1em}pre{white-space:pre;white-space:pre-wrap;word-wrap:break-word}q{quotes:none}q:before,q:after{content:'';content:none}small{font-size:80%}sub,sup{font-size:75%;line-height:0;position:relative;vertical-align:baseline}sup{top:-.5em}sub{bottom:-.25em}dl,menu,ol,ul{margin:1em 0}dd{margin:0 0 0 40px}menu,ol,ul{padding:0 0 0 40px}nav ul,nav ol{list-style:none;list-style-image:none}img{border:0;-ms-interpolation-mode:bicubic}svg:not(:root){overflow:hidden}figure{margin:0}form{margin:0}fieldset{border:1px solid silver;margin:0 2px;padding:.35em .625em .75em}legend{border:0;padding:0;white-space:normal;*margin-left:-7px}button,input,select,textarea{font-size:100%;margin:0;vertical-align:baseline;*vertical-align:middle}button,input{line-height:normal}button,select{text-transform:none}button,html input[type=button],input[type=reset],input[type=submit]{-webkit-appearance:button;cursor:pointer;*overflow:visible}button[disabled],html input[disabled]{cursor:default}input[type=checkbox],input[type=radio]{box-sizing:border-box;padding:0;*height:13px;*width:13px}input[type=search]{-webkit-appearance:textfield;-moz-box-sizing:content-box;-webkit-box-sizing:content-box;box-sizing:content-box}input[type=search]::-webkit-search-cancel-button,input[type=search]::-webkit-search-decoration{-webkit-appearance:none}button::-moz-focus-inner,input::-moz-focus-inner{border:0;padding:0}textarea{overflow:auto;vertical-align:top}table{border-collapse:collapse;border-spacing:0}[hidden]{display:none!important}.pure-img{max-width:100%;height:auto;display:block}.pure-g{letter-spacing:-.31em;*letter-spacing:normal;*word-spacing:-.43em;text-rendering:optimizespeed;font-family:FreeSans,Arimo,"Droid Sans",Helvetica,Arial,sans-serif;display:-webkit-flex;-webkit-flex-flow:row wrap;display:-ms-flexbox;-ms-flex-flow:row wrap}.opera-only :-o-prefocus,.pure-g{word-spacing:-.43em}.pure-u{display:inline-block;*display:inline;zoom:1;letter-spacing:normal;word-spacing:normal;vertical-align:top;text-rendering:auto}.pure-g [class *="pure-u"]{font-family:sans-serif}.pure-u-1,.pure-u-1-1,.pure-u-1-2,.pure-u-1-3,.pure-u-2-3,.pure-u-1-4,.pure-u-3-4,.pure-u-1-5,.pure-u-2-5,.pure-u-3-5,.pure-u-4-5,.pure-u-5-5,.pure-u-1-6,.pure-u-5-6,.pure-u-1-8,.pure-u-3-8,.pure-u-5-8,.pure-u-7-8,.pure-u-1-12,.pure-u-5-12,.pure-u-7-12,.pure-u-11-12,.pure-u-1-24,.pure-u-2-24,.pure-u-3-24,.pure-u-4-24,.pure-u-5-24,.pure-u-6-24,.pure-u-7-24,.pure-u-8-24,.pure-u-9-24,.pure-u-10-24,.pure-u-11-24,.pure-u-12-24,.pure-u-13-24,.pure-u-14-24,.pure-u-15-24,.pure-u-16-24,.pure-u-17-24,.pure-u-18-24,.pure-u-19-24,.pure-u-20-24,.pure-u-21-24,.pure-u-22-24,.pure-u-23-24,.pure-u-24-24{display:inline-block;*display:inline;zoom:1;letter-spacing:normal;word-spacing:normal;vertical-align:top;text-rendering:auto}.pure-u-1-24{width:4.1667%;*width:4.1357%}.pure-u-1-12,.pure-u-2-24{width:8.3333%;*width:8.3023%}.pure-u-1-8,.pure-u-3-24{width:12.5%;*width:12.469%}.pure-u-1-6,.pure-u-4-24{width:16.6667%;*width:16.6357%}.pure-u-1-5{width:20%;*width:19.969%}.pure-u-5-24{width:20.8333%;*width:20.8023%}.pure-u-1-4,.pure-u-6-24{width:25%;*width:24.969%}.pure-u-7-24{width:29.1667%;*width:29.1357%}.pure-u-1-3,.pure-u-8-24{width:33.3333%;*width:33.3023%}.pure-u-3-8,.pure-u-9-24{width:37.5%;*width:37.469%}.pure-u-2-5{width:40%;*width:39.969%}.pure-u-5-12,.pure-u-10-24{width:41.6667%;*width:41.6357%}.pure-u-11-24{width:45.8333%;*width:45.8023%}.pure-u-1-2,.pure-u-12-24{width:50%;*width:49.969%}.pure-u-13-24{width:54.1667%;*width:54.1357%}.pure-u-7-12,.pure-u-14-24{width:58.3333%;*width:58.3023%}.pure-u-3-5{width:60%;*width:59.969%}.pure-u-5-8,.pure-u-15-24{width:62.5%;*width:62.469%}.pure-u-2-3,.pure-u-16-24{width:66.6667%;*width:66.6357%}.pure-u-17-24{width:70.8333%;*width:70.8023%}.pure-u-3-4,.pure-u-18-24{width:75%;*width:74.969%}.pure-u-19-24{width:79.1667%;*width:79.1357%}.pure-u-4-5{width:80%;*width:79.969%}.pure-u-5-6,.pure-u-20-24{width:83.3333%;*width:83.3023%}.pure-u-7-8,.pure-u-21-24{width:87.5%;*width:87.469%}.pure-u-11-12,.pure-u-22-24{width:91.6667%;*width:91.6357%}.pure-u-23-24{width:95.8333%;*width:95.8023%}.pure-u-1,.pure-u-1-1,.pure-u-5-5,.pure-u-24-24{width:100%}.pure-button{display:inline-block;*display:inline;zoom:1;line-height:normal;white-space:nowrap;vertical-align:baseline;text-align:center;cursor:pointer;-webkit-user-drag:none;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none}.pure-button::-moz-focus-inner{padding:0;border:0}.pure-button{font-family:inherit;font-size:100%;*font-size:90%;*overflow:visible;padding:.5em 1em;color:#444;color:rgba(0,0,0,.8);*color:#444;border:1px solid #999;border:0 rgba(0,0,0,0);background-color:#E6E6E6;text-decoration:none;border-radius:1px}.pure-button-hover,.pure-button:hover,.pure-button:focus{filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#00000000', endColorstr='#1a000000', GradientType=0);background-image:-webkit-gradient(linear,0 0,0 100%,from(transparent),color-stop(40%,rgba(0,0,0,.05)),to(rgba(0,0,0,.1)));background-image:-webkit-linear-gradient(transparent,rgba(0,0,0,.05) 40%,rgba(0,0,0,.1));background-image:-moz-linear-gradient(top,rgba(0,0,0,.05) 0,rgba(0,0,0,.1));background-image:-o-linear-gradient(transparent,rgba(0,0,0,.05) 40%,rgba(0,0,0,.1));background-image:linear-gradient(transparent,rgba(0,0,0,.05) 40%,rgba(0,0,0,.1))}.pure-button:focus{outline:0}.pure-button-active,.pure-button:active{box-shadow:0 0 0 1px rgba(0,0,0,.15) inset,0 0 6px rgba(0,0,0,.2) inset}.pure-button[disabled],.pure-button-disabled,.pure-button-disabled:hover,.pure-button-disabled:focus,.pure-button-disabled:active{border:0;background-image:none;filter:progid:DXImageTransform.Microsoft.gradient(enabled=false);filter:alpha(opacity=40);-khtml-opacity:.4;-moz-opacity:.4;opacity:.4;cursor:not-allowed;box-shadow:none}.pure-button-hidden{display:none}.pure-button::-moz-focus-inner{padding:0;border:0}.pure-button-primary,.pure-button-selected,a.pure-button-primary,a.pure-button-selected{background-color:#0078e7;color:#fff}.pure-form input[type=text],.pure-form input[type=password],.pure-form input[type=email],.pure-form input[type=url],.pure-form input[type=date],.pure-form input[type=month],.pure-form input[type=time],.pure-form input[type=datetime],.pure-form input[type=datetime-local],.pure-form input[type=week],.pure-form input[type=number],.pure-form input[type=search],.pure-form input[type=tel],.pure-form input[type=color],.pure-form select,.pure-form textarea{padding:.5em .6em;display:inline-block;border:1px solid #ccc;box-shadow:inset 0 1px 3px #ddd;border-radius:1px;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box}.pure-form input:not([type]){padding:.5em .6em;display:inline-block;border:1px solid #ccc;box-shadow:inset 0 1px 3px #ddd;border-radius:4px;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box}.pure-form input[type=color]{padding:.2em .5em}.pure-form input[type=text]:focus,.pure-form input[type=password]:focus,.pure-form input[type=email]:focus,.pure-form input[type=url]:focus,.pure-form input[type=date]:focus,.pure-form input[type=month]:focus,.pure-form input[type=time]:focus,.pure-form input[type=datetime]:focus,.pure-form input[type=datetime-local]:focus,.pure-form input[type=week]:focus,.pure-form input[type=number]:focus,.pure-form input[type=search]:focus,.pure-form input[type=tel]:focus,.pure-form input[type=color]:focus,.pure-form select:focus,.pure-form textarea:focus{outline:0;outline:thin dotted \9;border-color:#129FEA}.pure-form input:not([type]):focus{outline:0;outline:thin dotted \9;border-color:#129FEA}.pure-form input[type=file]:focus,.pure-form input[type=radio]:focus,.pure-form input[type=checkbox]:focus{outline:thin dotted #333;outline:1px auto #129FEA}.pure-form .pure-checkbox,.pure-form .pure-radio{margin:.5em 0;display:block}.pure-form input[type=text][disabled],.pure-form input[type=password][disabled],.pure-form input[type=email][disabled],.pure-form input[type=url][disabled],.pure-form input[type=date][disabled],.pure-form input[type=month][disabled],.pure-form input[type=time][disabled],.pure-form input[type=datetime][disabled],.pure-form input[type=datetime-local][disabled],.pure-form input[type=week][disabled],.pure-form input[type=number][disabled],.pure-form input[type=search][disabled],.pure-form input[type=tel][disabled],.pure-form input[type=color][disabled],.pure-form select[disabled],.pure-form textarea[disabled]{cursor:not-allowed;background-color:#eaeded;color:#cad2d3}.pure-form input:not([type])[disabled]{cursor:not-allowed;background-color:#eaeded;color:#cad2d3}.pure-form input[readonly],.pure-form select[readonly],.pure-form textarea[readonly]{background:#eee;color:#777;border-color:#ccc}.pure-form input:focus:invalid,.pure-form textarea:focus:invalid,.pure-form select:focus:invalid{color:#b94a48;border-color:#ee5f5b}.pure-form input:focus:invalid:focus,.pure-form textarea:focus:invalid:focus,.pure-form select:focus:invalid:focus{border-color:#e9322d}.pure-form input[type=file]:focus:invalid:focus,.pure-form input[type=radio]:focus:invalid:focus,.pure-form input[type=checkbox]:focus:invalid:focus{outline-color:#e9322d}.pure-form select{border:1px solid #ccc;background-color:#fff}.pure-form select[multiple]{height:auto}.pure-form label{margin:.5em 0 .2em}.pure-form fieldset{margin:0;padding:.35em 0 .75em;border:0}.pure-form legend{display:block;width:100%;padding:.3em 0;margin-bottom:.3em;color:#333;border-bottom:1px solid #e5e5e5}.pure-form-stacked input[type=text],.pure-form-stacked input[type=password],.pure-form-stacked input[type=email],.pure-form-stacked input[type=url],.pure-form-stacked input[type=date],.pure-form-stacked input[type=month],.pure-form-stacked input[type=time],.pure-form-stacked input[type=datetime],.pure-form-stacked input[type=datetime-local],.pure-form-stacked input[type=week],.pure-form-stacked input[type=number],.pure-form-stacked input[type=search],.pure-form-stacked input[type=tel],.pure-form-stacked input[type=color],.pure-form-stacked select,.pure-form-stacked label,.pure-form-stacked textarea{display:block;margin:.25em 0}.pure-form-stacked input:not([type]){display:block;margin:.25em 0}.pure-form-aligned input,.pure-form-aligned textarea,.pure-form-aligned select,.pure-form-aligned .pure-help-inline,.pure-form-message-inline{display:inline-block;*display:inline;*zoom:1;vertical-align:middle}.pure-form-aligned textarea{vertical-align:top}.pure-form-aligned .pure-control-group{margin-bottom:.5em}.pure-form-aligned .pure-control-group label{text-align:right;display:inline-block;vertical-align:middle;width:10em;margin:0 1em 0 0}.pure-form-aligned .pure-controls{margin:1.5em 0 0 10em}.pure-form input.pure-input-rounded,.pure-form .pure-input-rounded{border-radius:2em;padding:.5em 1em}.pure-form .pure-group fieldset{margin-bottom:10px}.pure-form .pure-group input{display:block;padding:10px;margin:0;border-radius:0;position:relative;top:-1px}.pure-form .pure-group input:focus{z-index:2}.pure-form .pure-group input:first-child{top:1px;border-radius:4px 4px 0 0}.pure-form .pure-group input:last-child{top:-2px;border-radius:0 0 4px 4px}.pure-form .pure-group button{margin:.35em 0}.pure-form .pure-input-1{width:100%}.pure-form .pure-input-2-3{width:66%}.pure-form .pure-input-1-2{width:50%}.pure-form .pure-input-1-3{width:33%}.pure-form .pure-input-1-4{width:25%}.pure-form .pure-help-inline,.pure-form-message-inline{display:inline-block;padding-left:.3em;color:#666;vertical-align:middle;font-size:.875em}.pure-form-message{display:block;color:#666;font-size:.875em}@media only screen and (max-width :480px){.pure-form button[type=submit]{margin:.7em 0 0}.pure-form input:not([type]),.pure-form input[type=text],.pure-form input[type=password],.pure-form input[type=email],.pure-form input[type=url],.pure-form input[type=date],.pure-form input[type=month],.pure-form input[type=time],.pure-form input[type=datetime],.pure-form input[type=datetime-local],.pure-form input[type=week],.pure-form input[type=number],.pure-form input[type=search],.pure-form input[type=tel],.pure-form input[type=color],.pure-form label{margin-bottom:.3em;display:block}.pure-group input:not([type]),.pure-group input[type=text],.pure-group input[type=password],.pure-group input[type=email],.pure-group input[type=url],.pure-group input[type=date],.pure-group input[type=month],.pure-group input[type=time],.pure-group input[type=datetime],.pure-group input[type=datetime-local],.pure-group input[type=week],.pure-group input[type=number],.pure-group input[type=search],.pure-group input[type=tel],.pure-group input[type=color]{margin-bottom:0}.pure-form-aligned .pure-control-group label{margin-bottom:.3em;text-align:left;display:block;width:100%}.pure-form-aligned .pure-controls{margin:1.5em 0 0}.pure-form .pure-help-inline,.pure-form-message-inline,.pure-form-message{display:block;font-size:.75em;padding:.2em 0 .8em}}.pure-menu ul{position:absolute;visibility:hidden}.pure-menu.pure-menu-open{visibility:visible;z-index:2;width:100%}.pure-menu ul{left:-10000px;list-style:none;margin:0;padding:0;top:-10000px;z-index:1}.pure-menu>ul{position:relative}.pure-menu-open>ul{left:0;top:0;visibility:visible}.pure-menu-open>ul:focus{outline:0}.pure-menu li{position:relative}.pure-menu a,.pure-menu .pure-menu-heading{display:block;color:inherit;line-height:1.5em;padding:5px 20px;text-decoration:none;white-space:nowrap}.pure-menu.pure-menu-horizontal>.pure-menu-heading{display:inline-block;*display:inline;zoom:1;margin:0;vertical-align:middle}.pure-menu.pure-menu-horizontal>ul{display:inline-block;*display:inline;zoom:1;vertical-align:middle}.pure-menu li a{padding:5px 20px}.pure-menu-can-have-children>.pure-menu-label:after{content:'\25B8';float:right;font-family:'Lucida Grande','Lucida Sans Unicode','DejaVu Sans',sans-serif;margin-right:-20px;margin-top:-1px}.pure-menu-can-have-children>.pure-menu-label{padding-right:30px}.pure-menu-separator{background-color:#dfdfdf;display:block;height:1px;font-size:0;margin:7px 2px;overflow:hidden}.pure-menu-hidden{display:none}.pure-menu-fixed{position:fixed;top:0;left:0;width:100%}.pure-menu-horizontal li{display:inline-block;*display:inline;zoom:1;vertical-align:middle}.pure-menu-horizontal li li{display:block}.pure-menu-horizontal>.pure-menu-children>.pure-menu-can-have-children>.pure-menu-label:after{content:"\25BE"}.pure-menu-horizontal>.pure-menu-children>.pure-menu-can-have-children>.pure-menu-label{padding-right:30px}.pure-menu-horizontal li.pure-menu-separator{height:50%;width:1px;margin:0 7px}.pure-menu-horizontal li li.pure-menu-separator{height:1px;width:auto;margin:7px 2px}.pure-menu.pure-menu-open,.pure-menu.pure-menu-horizontal li .pure-menu-children{background:#fff;border:1px solid #b7b7b7}.pure-menu.pure-menu-horizontal,.pure-menu.pure-menu-horizontal .pure-menu-heading{border:0}.pure-menu a{border:1px solid transparent;border-left:0;border-right:0}.pure-menu a,.pure-menu .pure-menu-can-have-children>li:after{color:#777}.pure-menu .pure-menu-can-have-children>li:hover:after{color:#fff}.pure-menu .pure-menu-open{background:#dedede}.pure-menu li a:hover,.pure-menu li a:focus{background:#eee}.pure-menu li.pure-menu-disabled a:hover,.pure-menu li.pure-menu-disabled a:focus{background:#fff;color:#bfbfbf}.pure-menu .pure-menu-disabled>a{background-image:none;border-color:transparent;cursor:default}.pure-menu .pure-menu-disabled>a,.pure-menu .pure-menu-can-have-children.pure-menu-disabled>a:after{color:#bfbfbf}.pure-menu .pure-menu-heading{color:#565d64;text-transform:uppercase;font-size:90%;margin-top:.5em;border-bottom-width:1px;border-bottom-style:solid;border-bottom-color:#dfdfdf}.pure-menu .pure-menu-selected a{color:#000}.pure-menu.pure-menu-open.pure-menu-fixed{border:0;border-bottom:1px solid #b7b7b7}.pure-paginator{letter-spacing:-.31em;*letter-spacing:normal;*word-spacing:-.43em;text-rendering:optimizespeed;list-style:none;margin:0;padding:0}.opera-only :-o-prefocus,.pure-paginator{word-spacing:-.43em}.pure-paginator li{display:inline-block;*display:inline;zoom:1;letter-spacing:normal;word-spacing:normal;vertical-align:top;text-rendering:auto}.pure-paginator .pure-button{border-radius:0;padding:.8em 1.4em;vertical-align:top;height:1.1em}.pure-paginator .pure-button:focus,.pure-paginator .pure-button:active{outline-style:none}.pure-paginator .prev,.pure-paginator .next{color:#C0C1C3;text-shadow:0 -1px 0 rgba(0,0,0,.45)}.pure-paginator .prev{border-radius:2px 0 0 2px}.pure-paginator .next{border-radius:0 2px 2px 0}@media (max-width:480px){.pure-menu-horizontal{width:100%}.pure-menu-children li{display:block;border-bottom:1px solid #000}}.pure-table{border-collapse:collapse;border-spacing:0;empty-cells:show;border:1px solid #cbcbcb}.pure-table caption{color:#000;font:italic 85%/1 arial,sans-serif;padding:1em 0;text-align:center}.pure-table td,.pure-table th{border-left:1px solid #cbcbcb;border-width:0 0 0 1px;font-size:inherit;margin:0;overflow:visible;padding:.5em 1em}.pure-table td:first-child,.pure-table th:first-child{border-left-width:0}.pure-table thead{background:#e0e0e0;color:#000;text-align:left;vertical-align:bottom}.pure-table td{background-color:transparent}.pure-table-odd td{background-color:#f2f2f2}.pure-table-striped tr:nth-child(2n-1) td{background-color:#f2f2f2}.pure-table-bordered td{border-bottom:1px solid #cbcbcb}.pure-table-bordered tbody>tr:last-child td,.pure-table-horizontal tbody>tr:last-child td{border-bottom-width:0}.pure-table-horizontal td,.pure-table-horizontal th{border-width:0 0 1px;border-bottom:1px solid #cbcbcb}.pure-table-horizontal tbody>tr:last-child td{border-bottom-width:0}
.wrapper{padding:10px;}.highlight{padding:10px;}.center{margin:0 auto;max-width:300px;background:#f3f3f3;padding:10px;border:1px solid #ddd;}
.green{color:green;}.red{color:red;}
p.copyright{color:#999;}p.copyright a{text-decoration:none;color:#999;font-weight:bold;}
</style></head><body>
<div class="wrapper"><div class="highlight"><div class="center">
<form class="pure-form pure-form-stacked" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post">
    <fieldset>
        <legend><?php if ($msg !== '') { echo $msg; } else { echo STARTER_GREETING; } ?></legend>
        <input type="hidden" name="action" value="set_login">
        <!-- <label for="username"><strong><?php echo USERNAME; ?>:</strong></label> -->
        <input id="username" type="text" name="user" placeholder="<?php echo ENTER_USERNAME; ?>" class="pure-input-1">
        <!-- <label for="password"><strong><?php echo PASSWORD; ?>:</strong></label> -->
        <input id="password" type="password" name="pass" placeholder="<?php echo ENTER_PASSWORD; ?>" class="pure-input-1">
        <label for="remember" class="pure-checkbox">
            <input id="remember" name="remember" type="checkbox"> <?php echo REMEMBER_THIS_COMPUTER; ?>
        </label>
        <button type="submit" class="pure-button pure-button-primary"><?php echo BUTTON_LOGIN; ?></button>
    </fieldset>
</form>
</div></div></div>
</body></html>

<?php exit;}} ?>
