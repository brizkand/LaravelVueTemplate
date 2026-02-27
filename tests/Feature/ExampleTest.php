<?php

test('the application returns a successful response', function () {
    $response = $this->get('/');

    $response->assertStatus(status: 200);
});
