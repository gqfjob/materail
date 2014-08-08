<?php
class My_Input extends CI_Input
{
	public function __construct()
	{
		if(isset($_POST['cookie']))
		{
			$cookie = json_decode($_POST['cookie'], TRUE);
			if(is_array($cookie))
			{
				foreach($cookie as $key => $value)
				{
					$_COOKIE[$key] = $value;
				}
			}
			
		}
		
		parent::__construct();
	}
}