<?php

namespace Database\Seeders;

use App\Models\DocumentType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocumentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DocumentType::create([
            'title' => 'Engagement Contract',
            'description' => 'A legal contract agreement between contractors and clients',
        ]);

        DocumentType::create([
            'title' => 'Others',
            'description' => 'Other internal documents that need signing',
        ]);
    }
}
