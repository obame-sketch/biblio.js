<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class HashPasswords extends Command
{
    protected $signature = 'hash:passwords';

    protected $description = 'Hash existing passwords';

    public function handle()
    {
        $users = DB::table('users')->get();
        
        foreach ($users as $user) {
            if (!Hash::needsRehash($user->password)) {
                DB::table('users')->where('id', $user->id)->update([
                    'password' => Hash::make($user->password)
                ]);
                $this->info("Password hashed for user: {$user->email}");
            }
        }
        
        $this->info('All passwords processed');
    }
}