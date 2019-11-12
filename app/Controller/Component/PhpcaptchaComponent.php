<?php
class PhpcaptchaComponent extends Component {
	var $Controller = null;
	
	function startup(Controller $controller) {
		App::import('Vendor', 'phpcaptcha/securimage');
		$this->Controller = $controller;
	}
	
	function show() {
		$options = array(
			'text_color' => new Securimage_Color('#000000'),
			'captcha_type' => 1,
			'noise_level' => 6
		);
		$phpcaptcha = new Securimage($options);
		
		$phpcaptcha->show();
		
		exit;
	}
	
	function play() {
		$phpcaptcha = new Securimage();
		$phpcaptcha->outputAudioFile();
		exit;
	}
}
?>
