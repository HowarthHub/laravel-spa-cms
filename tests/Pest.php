<?php

use App\Models\UserModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

function actingAsSuperAdmin(): UserModel
{
    $user = UserModel::factory()->active()->create();
    $user->assignRole('super-admin');

    test()->actingAs($user);

    return $user;
}

function actingAsAdmin(): UserModel
{
    $user = UserModel::factory()->active()->create();
    $user->assignRole('admin');

    test()->actingAs($user);

    return $user;
}

function actingAsEditor(): UserModel
{
    $user = UserModel::factory()->active()->create();
    $user->assignRole('editor');

    test()->actingAs($user);

    return $user;
}

function actingAsViewer(): UserModel
{
    $user = UserModel::factory()->active()->create();
    $user->assignRole('viewer');

    test()->actingAs($user);

    return $user;
}
