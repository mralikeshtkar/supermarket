<?php

namespace Modules\Core\Responses\Api;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use phpDocumentor\Reflection\Types\This;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class ApiResponse
{
    #region Properties

    /**
     * Validation data.
     *
     * @var array
     */
    private array $validation_data;

    /**
     * Validation rules.
     *
     * @var array
     */
    private array $validation_rules;

    /**
     * Validation messages.
     *
     * @var array
     */
    private array $validation_messages;

    /**
     * Validation attributes.
     *
     * @var array
     */
    private array $validation_attributes;

    /**
     * Response Http code.
     *
     * @var int
     */
    private int $code = HttpResponse::HTTP_OK;

    /**
     * Response data.
     *
     * @var array
     */
    private array $data = [];

    /**
     * Response errors.
     *
     * @var array
     */
    private array $errors = [];

    /**
     * Response message.
     *
     * @var string
     */
    private string $message;

    /**
     * Status response status process.
     *
     * @var bool
     */
    private bool $status = true;

    /**
     * Status response has validation error.
     *
     * @var bool
     */
    private bool $has_error = false;

    #endregion

    #region Getter and Setters

    /**
     * Push custom data to response data with specified key.
     *
     * @param mixed $key
     * @param mixed $data
     * @return $this
     */
    public function addData(mixed $key, mixed $data): static
    {
        $this->data[$key] = $data;
        return $this;
    }

    /**
     * Push custom error to response errors with specified key.
     *
     * @param mixed $key
     * @param mixed $data
     * @return $this
     */
    public function addError(mixed $key, mixed $data): static
    {
        $this->errors[$key] = $data;
        return $this;
    }

    /**
     * Get response message.
     *
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Set response message.
     *
     * @param string $message
     * @return $this
     */
    public function setMessage(string $message): static
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Get validation data.
     *
     * @return array
     */
    public function getValidationData(): array
    {
        return $this->validation_data;
    }

    /**
     * Set validation data.
     *
     * @param array $validation_data
     * @return $this
     */
    public function setValidationData(array $validation_data): static
    {
        $this->validation_data = $validation_data;
        return $this;
    }

    /**
     * Get validation rules.
     *
     * @return array
     */
    public function getValidationRules(): array
    {
        return $this->validation_rules;
    }

    /**
     * Set validation rules.
     *
     * @param array $validation_rules
     * @return $this
     */
    public function setValidationRules(array $validation_rules): static
    {
        $this->validation_rules = $validation_rules;
        return $this;
    }

    /**
     * Get validation messages.
     *
     * @return array
     */
    public function getValidationMessages(): array
    {
        return $this->validation_messages;
    }

    /**
     * Set validation messages.
     *
     * @param array $validation_messages
     * @return $this
     */
    public function setValidationMessages(array $validation_messages): static
    {
        $this->validation_messages = $validation_messages;
        return $this;
    }

    /**
     * Set validation attributes.
     *
     * @return array
     */
    public function getValidationAttributes(): array
    {
        return $this->validation_attributes;
    }

    /**
     * Set validation attributes.
     *
     * @param array $validation_attributes
     * @return $this
     */
    public function setValidationAttributes(array $validation_attributes): static
    {
        $this->validation_attributes = $validation_attributes;
        return $this;
    }

    /**
     * Get response http code.
     *
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * Set response http code.
     *
     * @param int $code
     * @return $this
     */
    public function setCode(int $code): static
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Get response url.
     *
     * @return string
     */
    public function getUri(): string
    {
        return Route::current()->uri ?? "";
    }

    /**
     * Get response data.
     *
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Set response data.
     *
     * @param array $data
     * @return $this
     */
    public function setData(array $data): static
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Get response status process.
     * if status equals to true means process completed else process not-completed.
     *
     * @return bool
     */
    public function getStatus(): bool
    {
        return $this->status;
    }

    /**
     * Set response process has error.
     *
     * @return $this
     */
    public function hasProcessError(): static
    {
        $this->setStatus(false);
        return $this;
    }

    /**
     * Set response status process.
     *
     * @param bool $status
     * @return $this
     */
    public function setStatus(bool $status): static
    {
        $this->status = $status;
        return $this;
    }

    /**
     * If it has error is true, we have some validation errors.
     *
     * @return $this
     */
    public function hasError(): static
    {
        $this->has_error = true;
        return $this;
    }

    /**
     * @return bool
     */
    public function getHasError(): bool
    {
        return $this->has_error;
    }

    /**
     * Get Response errors.
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Set Response errors.
     *
     * @param array $errors
     * @return $this
     */
    public function setErrors(array $errors): static
    {
        $this->errors = $errors;
        return $this;
    }

    #endregion

    #region Methods

    /**
     * Initialize class And set data, rules, attributes and messages.
     *
     * @param array $data
     * @param array $rules
     * @param array $messages
     * @param array $attributes
     * @return ApiResponse
     */
    public static function init(array $data, array $rules, array $messages = [], array $attributes = []): ApiResponse
    {
        $self = new self();
        $self->setValidationData($data)
            ->setValidationRules($rules)
            ->setValidationMessages($messages)
            ->setValidationAttributes($attributes);
        return $self;
    }

    /**
     * Initialize class and prepare to json message.
     *
     * @param string $message
     * @param int $code
     * @return ApiResponse
     */
    public static function message(string $message, int $code = 200): ApiResponse
    {
        $self = new self();
        $self->setMessage($message)
            ->setCode($code);
        return $self;
    }

    public static function authorize(bool $access, string $message = null)
    {
        $self = new self();
        $self->setMessage($message ?? trans('core::messages.access_forbidden'))
            ->setCode(HttpResponse::HTTP_FORBIDDEN);
        abort_if(!$access, $self->send());
    }

    /**
     * Initialize class and send json message.
     *
     * @param string $message
     * @param int $code
     * @return JsonResponse
     */
    public static function sendMessage(string $message, int $code = 200): JsonResponse
    {
        $self = new self();
        $self->setMessage($message)
            ->setCode($code);
        return $self->send();
    }

    /**
     * Send an error json with has error is true.
     *
     * @param string $message
     * @param int $code
     * @return JsonResponse
     */
    public static function sendError(string $message, int $code = 500): JsonResponse
    {
        $self = new self();
        $self->setMessage($message)
            ->hasError()
            ->setCode($code);
        return $self->send();
    }

    /**
     * Validation data with specified rules.
     */
    public function validate()
    {
        $validator = Validator::make($this->getValidationData(), $this->getValidationRules(), $this->getValidationMessages(), $this->getValidationAttributes());
        if ($validator->fails()) {
            $this->setMessage('we have some validation errors!')
                ->setCode(HttpResponse::HTTP_UNPROCESSABLE_ENTITY)
                ->setErrors($validator->errors()->toArray())
                ->hasError();
            throw new HttpResponseException($this->send());
        }
    }

    /**
     * Send a response json.
     *
     * @return JsonResponse
     */
    public function send(): JsonResponse
    {
        return Response::json([
            'status' => $this->getStatus(),
            'has_error' => $this->getHasError(),
            'message' => $this->getMessage(),
            'code' => $this->getCode(),
            'uri' => $this->getUri(),
            'data' => $this->getData(),
            'errors' => $this->getErrors(),
        ], $this->getCode());
    }

    #endregion
}
