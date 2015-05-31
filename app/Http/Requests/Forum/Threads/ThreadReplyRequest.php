<?php namespace App\Http\Requests\Forum\Threads;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ThreadReplyRequest extends FormRequest {

	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		$topic = $this->route()->getParameter('topic');

		return Auth::check() && Auth::user()->isConfirmed() && $topic->canView;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		$rules = [
			//
			'body' => 'required|min:20|max:4500'
		];

		$files = $this->file('files');

		$file_rules = 'mimes:jpeg,png,zip|max:5120';
		if (count($files) > 0) {
			foreach ($files as $key => $image) {
				$rules['files.'.$key] = $file_rules;
			}
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
		$messages = [
			//
			'body.required' => 'A message is required.',
			'body.min' => 'Messages must be at least 20 characters long.',
			'body.max' => 'Messages can be up to 4500 characters long.'
		];

		if ($this->has('attachments'))
		{
			$attachments = $this->input('attachments');
			foreach ($attachments as $key => $file) {
				$messages['attachments.'.$key.'.mimes'] = 'Only JPGs, PNGs, and ZIP files are allowed.';
				$messages['attachments.'.$key.'.size'] = 'Files can not be larger than 5MB.';
			}
		}

		return $messages;
	}
}
