<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_have_teacher_profile()
    {
        $user = User::factory()->create();
        $this->assertNull($user->teacher);
    }
}
