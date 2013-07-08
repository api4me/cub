<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Lcaptcha {

/*{{{ variable */
    private $CI;
    private $word;
    private $font;
    private $image;
    private $width = 150;
    private $height = 40;
/*}}}*/
/*{{{ __contruct */
    public function __construct() {
        $this->CI =& get_instance();
    }
/*}}}*/
/*{{{ show */
	public function show($word = '', $font = '') {
		if (!extension_loaded('gd')) {
			return false;
		}

        $this->word = $word;
        $this->font = $font;

		// -----------------------------------
		// Do we have a "word" yet?
		// -----------------------------------
	   if ($word == '') {
			$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

			$str = '';
			for ($i = 0; $i < 4; $i++) {
				$str .= substr($pool, mt_rand(0, strlen($pool) -1), 1);
			}

			$this->word = $str;
	   }

       if ($font == '') {
           $this->font = APPPATH . 'libraries/font/OleoScript-Bold.ttf';
       }

		// -----------------------------------
		// Determine angle and position
		// -----------------------------------
		$length	= strlen($this->word);
		$angle	= ($length >= 6) ? rand(-($length-6), ($length-6)) : 0;
		$x_axis	= rand(6, (360/$length)-16);
		$y_axis = ($angle >= 0 ) ? rand($this->height, $this->width) : rand(6, $this->height);

		// -----------------------------------
		// Create image
		// -----------------------------------

		// PHP.net recommends imagecreatetruecolor(), but it isn't always available
		if (function_exists('imagecreatetruecolor')) {
			$im = imagecreatetruecolor($this->width, $this->height);
		} else {
			$im = imagecreate($this->width, $this->height);
		}

		// -----------------------------------
		//  Assign colors
		// -----------------------------------
		$bg_color		= imagecolorallocate($im, 255, 255, 255);
		$border_color	= imagecolorallocate($im, 255, 255, 255);
		$text_color		= imagecolorallocate($im, 59, 89, 152);
		$grid_color		= imagecolorallocate($im, 105, 175, 35);
		$shadow_color	= imagecolorallocate($im, 255, 240, 240);

		// -----------------------------------
		//  Create the rectangle
		// -----------------------------------
		imagefilledrectangle($im, 0, 0, $this->width, $this->height, $bg_color);

		// -----------------------------------
		//  Create the spiral pattern
		// -----------------------------------
		$theta		= 1;
		$thetac		= 7;
		$radius		= 16;
		$circles	= 20;
		$points		= 32;

		for ($i = 0; $i < ($circles * $points) - 1; $i++) {
			$theta = $theta + $thetac;
			$rad = $radius * ($i / $points );
			$x = ($rad * cos($theta)) + $x_axis;
			$y = ($rad * sin($theta)) + $y_axis;
			$theta = $theta + $thetac;
			$rad1 = $radius * (($i + 1) / $points);
			$x1 = ($rad1 * cos($theta)) + $x_axis;
			$y1 = ($rad1 * sin($theta )) + $y_axis;
			imageline($im, $x, $y, $x1, $y1, $grid_color);
			$theta = $theta - $thetac;
		}

		// -----------------------------------
		//  Write the text
		// -----------------------------------
		$use_font = ($this->font != '' AND file_exists($this->font) AND function_exists('imagettftext')) ? TRUE : FALSE;

		if ($use_font == FALSE) {
			$font_size = 5;
			$x = rand(0, $this->width/($length/3));
			$y = 0;
		} else {
			$font_size	= 24;
			$x = rand(0, $this->width/($length/1.5));
			$y = $font_size+2;
		}

		for ($i = 0; $i < strlen($this->word); $i++) {
			if ($use_font == FALSE) {
				$y = rand(0 , $this->height/2);
				imagestring($im, $font_size, $x, $y, substr($this->word, $i, 1), $text_color);
				$x += ($font_size*2);
			} else {
				$y = rand($this->height/2, $this->height-3);
				imagettftext($im, $font_size, $angle, $x, $y, $text_color, $this->font, substr($this->word, $i, 1));
				$x += $font_size;
			}
		}

		// -----------------------------------
		//  Create the border
		// -----------------------------------
		imagerectangle($im, 0, 0, $this->width-1, $this->height-1, $border_color);

		// -----------------------------------
		//  Generate the image
		// -----------------------------------
        $this->CI->lsession->set("captcha", $this->word);
        ob_clean();
        header("Content-type: image/jpeg");
		imagejpeg($im);
		imagedestroy($im);
	}
/*}}}*/

}
