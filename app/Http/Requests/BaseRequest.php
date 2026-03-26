<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    abstract public function authorize(): bool;

    /**
     * Handle a failed validation attempt.
     *
     * @throws HttpResponseException
     */
    protected function failedValidation(Validator $validator): void
    {
        $errors = [];
        foreach ($validator->errors()->toArray() as $field => $fieldErrors) {
            $errors[] = [
                'pointer' => $field,
                'reason' => $fieldErrors[0],
                'message' => 'Something failed',
            ];
        }

        throw new HttpResponseException(
            response()->json([
                'message' => 'Validation error',
                'errors' => $errors,
            ], Response::HTTP_UNPROCESSABLE_ENTITY),
        );
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    abstract public function rules(): array;

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     *
     * @throws \LogicException
     */
    public function messages(): array
    {
        throw new \LogicException("The 'messages' method must be implemented in the child class.");
    }

    /**
     * @param  mixed[]  $rules
     * @return mixed[]
     */
    protected function requiredRules(array $rules): array
    {
        return ['required', ...$rules];
    }

    /**
     * @param  mixed[]  $rules
     * @return mixed[]
     */
    protected function optionnalRules(array $rules): array
    {
        return ['sometimes', ...$rules];
    }

    /**
     * @param  mixed[]  $rules
     * @return mixed[]
     */
    protected function requiredWithoutRules(string $field, array $rules): array
    {
        return ["required_without:{$field}", ...$rules];
    }
}
