<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MessageTemplatesSeeder extends Seeder
{
    public function run()
    {
        DB::table('message_templates')->insert([
            ['key' => 'holiday_approved', 'message' => 'Your holiday request has been approved!'],
            ['key' => 'holiday_denied', 'message' => 'Your holiday request has been denied.'],
            ['key' => 'new_announcement', 'message' => 'A new announcement has been posted. Check it out!'],
        ]);
    }
}
