<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('the application returns a successful response', function () {
    $response = $this->followingRedirects()->get('/registration/programs');

    $response
        ->assertStatus(200)
        ->assertSee('ETC Planet')
        ->assertSee('Daftar Sekarang');
});
