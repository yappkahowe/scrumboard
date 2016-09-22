<?php

use App\User;

abstract class TestCase extends Illuminate\Foundation\Testing\TestCase
{
    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost';

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }

    protected function me()
    {
        return User::first();
    }

    protected function headers()
    {
        $token = $this->me()->api_token;

        return ['HTTP_Authorization' => 'Bearer ' . $token];
    }

    public function get($uri, array $headers = [])
    {
        return parent::get($uri, $this->headers());
    }

    public function post($uri, array $data = [], array $headers = [])
    {
        return parent::post($uri, $data, $this->headers());
    }

    public function patch($uri, array $data = [], array $headers =[])
    {
        return parent::patch($uri, $data, $this->headers());
    }

    public function delete($uri, array $data = [], array $headers = [])
    {
        return parent::delete($uri, $data, $this->headers());
    }
}
