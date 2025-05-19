<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ListOptionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */


    public function run()
    {
        // Data to be inserted
        $data = [
            [
                'list_type' => 'location',
                'slug' => 'person-appt',
            ],
            [
                'list_type' => 'location',
                'slug' => 'telehealth-appt',
            ],
            [
                'list_type' => 'location',
                'slug' => 'unassigned',
            ],
            // Add more entries as required...
        ];

        // Loop through each item and insert into database
        foreach ($data as $item) {
            DB::table('list_options')->updateOrInsert(
                ['slug' => Str::slug($item['slug']), 'list_type' => $item['list_type']],
                [
                    'list_type' => $item['list_type'],
                    'slug' => Str::slug($item['slug']),
                    'title' => null,
                    'sequence' => null,
                    'is_default' => null,
                    'option_value' => null,
                    'mapping' => null,
                    'notes' => null,
                    'codes' => null,
                    'toggle_setting_1' => null,
                    'toggle_setting_2' => null,
                    'activity' => null,
                    'subtype' => null,
                    'edit_options' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
