<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('microsoft_id')->nullable()->after('id');
            $table->string('avatar')->nullable()->after('email');
            $table->json('office_groups')->nullable()->after('avatar');
            $table->json('office_roles')->nullable()->after('office_groups');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'microsoft_id',
                'avatar',
                'office_groups',
                'office_roles'
            ]);
        });
    }
};
