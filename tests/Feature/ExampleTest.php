<?php

test('the Rasky auth entry point returns a successful response', function () {
    $response = $this->get(route('auth.login'));

    $response->assertStatus(200);
});
