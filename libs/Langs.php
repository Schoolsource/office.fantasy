<?php

class Langs
{
	private $supported = array('th','en'); // '',
	private $data = array();
	private $lang = null;
	private $code = null;

	public function set($name)
	{
		$this->code = $name;
		Session::init();
        Session::set('lang', $name);
	}

	public function get($lang=null)
	{

		if( empty($lang) ){
			Session::init();
	       	$lang = Session::get('lang');

	       	if( !isset($lang) ){
	       		$lang = $this->search();
	       	}
		}

		if( in_array($lang, $this->supported) ){
			$this->code = $lang;
			return $this->code;
		}
	}

	public function lists()
	{
		return array(
			'th' => 'ภาษาไทย',
			'en' => 'English'
		);
	}

	public function load()
	{
		if( empty($this->code) ){
			$this->search();
		}
		
		$path = LIBS."Langs/{$this->code}.php";
		if( file_exists($path) ) {
			require_once "Langs/{$this->code}.php";

			$lang = New $this->code;
			// $this->lang = $lang->translations();
		}
	}

	public function getCode()
	{
		Session::init();
	    $lang = Session::get('lang');

	    if( empty($lang) ){
       		$lang = $this->search();
       	}

		return $lang;
	}

	public function search()
	{
		// $browser_lang = !empty($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? strtok(strip_tags($_SERVER['HTTP_ACCEPT_LANGUAGE']), ',') : '';
		// $browser_lang = substr($browser_lang, 0,2);

		// Now check if you support this language and set it
		// if(array_key_exists($browser_lang, $this->languages /* define this array to compare */))
		    // return $browser_lang;
		// else{
		    // return default lang
		// }

		// $browser_lang = !empty($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? strtok(strip_tags($_SERVER['HTTP_ACCEPT_LANGUAGE']), ',') : '';

		// $language = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE']: null;
		
		// print_r($languages); die;

		if( !empty($_SERVER['HTTP_ACCEPT_LANGUAGE']) ){

			$languages = explode(',',$_SERVER['HTTP_ACCEPT_LANGUAGE']);
			foreach($languages as $lang)
	        {
	            // $lang = str_replace('-','_',$lang);
	            if(in_array($lang, $this->supported))
	            {
	                // Set the page locale to the first supported language found
	                $code = $lang;
	                break;
	            }
	        }
		}
		// $code = '';
        
        if( empty($this->code)  ){
        	$code = $this->supported[1];
        }	

        if( empty($code) ){
        	$code = $this->supported[1];
        }

        return $code;
	}

	public function translate($key, $val=null, $lang=null)
	{
		$this->get($lang);

		$path = LIBS."Langs/{$this->code}.php";
		if( !empty($this->code) && file_exists($path) ){
			require_once $path;
			$lang = New $this->code;

			if( !empty($val) ){
				return $lang->{$key}($val);
			}
			else{
				return $lang->basics($key);
			}

		}
		elseif( !empty($val) ){
			return $val; // $this->{$key}($val);
		}
		else{
			return $key; //$this->translations($key);
		}

	}

	private function translations($text)
	{
		$data = array(
			// 'Home' => 'Home'
		);

		return !empty($data[$text]) ? $data[$text]: $text;
	}

}