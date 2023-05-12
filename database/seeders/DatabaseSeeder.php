<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

         \App\Models\User::create([
             'name' => 'Docs Admin',
             'email' => 'documenter@yopmail.com',
             'job_title' => 'C.E.O',
             'password' => bcrypt('123456'),
         ]);
        $this::call(DocumentTypeSeeder::class);
    }
}
