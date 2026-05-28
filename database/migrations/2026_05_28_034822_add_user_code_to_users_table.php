<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('user_code', 20)->nullable()->unique()->after('id');
        });

        // Backfill existing users
        $users = DB::table('users')->get();
        foreach ($users as $user) {
            $prefix = match($user->role) {
                'tenant' => 'T',
                'manager' => 'M',
                'superadmin' => 'SA',
                default => 'U'
            };
            
            DB::table('users')->where('id', $user->id)->update([
                'user_code' => $prefix . '-' . strtoupper(Str::random(6))
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('user_code');
        });
    }
};
