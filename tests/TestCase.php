<?php

use Faker\Factory;
use Faker\Generator;
use Laravel\Lumen\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__.'/../bootstrap/app.php';
    }

    /**
     * @return Generator
     */
    public function faker(): Generator
    {
        return Factory::create(config('app.faker_locale'));
    }

    /**
     * @param array $validations
     *
     * @return string $messages
     */
    public function validationMessages(array $validations): string
    {
        $messages = [];

        if (empty($validations)) {
            return $messages;
        }

        foreach ($validations as $key => $validation) {
            $attribute = str_replace('_', ' ', $key);
            $message   = [str_replace(':attribute', $attribute, trans($validation))];

            $messages[$key] = $message;
        }

        $messages = json_encode($messages);

        return $messages;
    }
}
