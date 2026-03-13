<?php

test('the public homepage is accessible', function () {
    $response = $this->get('/');

    $response->assertOk();
});
