<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tournaments', function (Blueprint $table) {
            $table->unsignedInteger('max_participants')->nullable()->after('name');
            $table->boolean('use_groups')->default(false)->after('max_participants');
            $table->unsignedInteger('group_count')->nullable()->after('use_groups');
        });
    }

    public function down(): void
    {
        Schema::table('tournaments', function (Blueprint $table) {
            $table->dropColumn(['max_participants', 'use_groups', 'group_count']);
        });
    }
};
