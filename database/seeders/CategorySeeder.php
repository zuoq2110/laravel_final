<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Technical Support',
            'Account Issues',
            'Billing',
            'Feature Request',
            'General Inquiry',
            'Hardware Issue',
            'Software Issue',
            'Network Problem',
            'Security Concern'
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['name' => $category]);
        }
    }
}