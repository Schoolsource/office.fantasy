<?php

class Password_Fn extends _function
{
	/* encrypt for laravel */
	public function PasswordHash($value, array $options = []){
		$cost = isset($options['rounds']) ? $options['rounds'] : 10;
		$hash = password_hash($value, PASSWORD_BCRYPT, ['cost' => $cost]);
		if ($hash === false) {
			throw new RuntimeException('Bcrypt hashing not supported.');
		}
		return $hash;
	}
}