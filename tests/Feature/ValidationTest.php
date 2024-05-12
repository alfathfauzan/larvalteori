<?php

namespace Tests\Feature;

use App\Rules\RegistrationRule;
use App\Rules\Uppercase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator as ValidationValidator;
use Tests\TestCase;

use function PHPUnit\Framework\assertTrue;

class ValidationTest extends TestCase
{
    public function testValidator()
    {
        $data = [
            "username" => "admin",
            "password" => "12345"

        ];

        $rules = [
            "username" => "required",
            "password" => "required"
        ];

        $validator = Validator::make($data,$rules);
        self::assertNotNull($validator);

    }

    public function testValidationException()
    {
        $data = [
            "username" => "admin",
            "password" => "12345"

        ];

        $rules = [
            "username" => "required",
            "password" => "required"
        ];

        $validator = Validator::make($data,$rules);
        self::assertNotNull($validator);

        try{

        }catch (ValidationException $exception){
            self::assertNotNull($exception->validator);
            $message = $exception->validator->errors();
            Log::error($message->toJson(JSON_PRETTY_PRINT));
        }
    }

    public function testValidatorValidData()
    {
        App::setlocale("id");
        $data = [
            "username" => "admin",
            "password" => "12345",
            "admin" => true

        ];

        $rules = [
            "username" => ["required","email","max:100"],
            "password" => ["required","min:6","max:6"]
        ];

        $validator = Validator::make($data,$rules);
        self::assertNotNull($validator);

        try{
            $valid = $validator->validate();
            Log::info(json_encode($valid, JSON_PRETTY_PRINT));
        }catch (ValidationException $exception){
            self::assertNotNull($exception->validator);
            $message = $exception->validator->errors();
            Log::error($message->toJson(JSON_PRETTY_PRINT));
        }
    }

    public function testValidatorInlineMessage()
    {
        $data = [
            "username" => "admin",
            "password" => "12345",
            "admin" => true

        ];

        $rules = [
            "username" => ["required","email","max:100"],
            "password" => ["required","min:6","max:6"]
        ];

        $messages = [
            "required" => ":attribute harus diisi",
            "email" => ":attribute harus berupa email",
            "min" => ":attribute minimal :min karakter",
            "max" => ":attribute maksimal :max karakter"

        ];

        $validator = Validator::make($data,$rules,$messages);
        self::assertNotNull($validator);

        try{
            $valid = $validator->validate();
            Log::info(json_encode($valid, JSON_PRETTY_PRINT));
        }catch (ValidationException $exception){
            self::assertNotNull($exception->validator);
            $message = $exception->validator->errors();
            Log::error($message->toJson(JSON_PRETTY_PRINT));
        }
    }

    public function testValidatorAddtionalValidation()
    {
        $data = [
            "username" => "admin@gmail.com",
            "password" => "admin@gmail.com",
            "admin" => true

        ];

        $rules = [
            "username" => ["required","email","max:100"],
            "password" => ["required","min:6","max:100"]
        ];

        $validator = Validator::make($data,$rules);
        $validator->after(function (\Illuminate\Validation\Validator $validator){
            $data = $validator->getData();
            if($data['username'] == $data['password']){
                $validator->errors()->add("password","password tidak boleh sama dengan username");
            }
        });
        self::assertNotNull($validator);

        try{
            $valid = $validator->validate();
            Log::info(json_encode($valid, JSON_PRETTY_PRINT));
        }catch (ValidationException $exception){
            self::assertNotNull($exception->validator);
            $message = $exception->validator->errors();
            Log::error($message->toJson(JSON_PRETTY_PRINT));
        }
    }
    public function testValidatorCustomRule()
    {
        $data = [
            "username" => "admin@gmail.com",
            "password" => "admin@gmail.com",
            "admin" => true

        ];

        $rules = [
            "username" => ["required","email","max:100",new Uppercase()],
            "password" => ["required","min:6","max:100", new RegistrationRule()]
        ];

        $validator = Validator::make($data,$rules);
        
        self::assertNotNull($validator);

        try{
            $valid = $validator->validate();
            Log::info(json_encode($valid, JSON_PRETTY_PRINT));
        }catch (ValidationException $exception){
            self::assertNotNull($exception->validator);
            $message = $exception->validator->errors();
            Log::error($message->toJson(JSON_PRETTY_PRINT));
        }
    }
}
