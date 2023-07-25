<?php

declare(strict_types=1);

it('welcome page is displayed', function (): void {
    $response = $this->get('/');

    $response->assertStatus(200);
});
