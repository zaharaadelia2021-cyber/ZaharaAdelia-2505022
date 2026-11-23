<?php

test('the application sends guests to registration', function () {
    $response = $this->get('/');

    $response->assertRedirect(route('register'));
});
