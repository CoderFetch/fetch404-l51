<?php namespace App\Http\Requests\Account;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ProfileSettingsUpdateRequest extends FormRequest {

	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return Auth::check() && Auth::user()->isConfirmed();
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		//
		$rules = [];

		if ($this->has('signature'))
		{
			$rules['signature'] = 'min:10|max:2500';
		}

		if ($this->has('avatar'))
		{
			$file_rules = 'mimes:jpeg,png|max:5120';

			$rules['avatar'] = $file_rules;
		}

		return $rules;
	}

	/**
	 * Get the validation messages that apply to the request.
	 *
	 * @return array
	 */
	public function messages()
	{
		return [
			'signature.min' => 'Signatures must be at least 10 characters long',
			'signature.max' => 'Signatures can be up to 2500 characters long',
			'avatar.mimes' => 'Avatars can only be JPEGs or PNGs',
			'avatar.max' => 'Avatars can be up to 5MB'
		];
	}
}
