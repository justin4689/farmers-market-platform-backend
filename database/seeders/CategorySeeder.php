<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // ── Pesticides ──────────────────────────────────────────────
        $pesticides = Category::create(['name' => 'Pesticides', 'parent_id' => null]);
        Category::create(['name' => 'Herbicides',  'parent_id' => $pesticides->id]);
        Category::create(['name' => 'Insecticides', 'parent_id' => $pesticides->id]);
        Category::create(['name' => 'Fongicides',   'parent_id' => $pesticides->id]);

        // ── Engrais ──────────────────────────────────────────────────
        $engrais = Category::create(['name' => 'Engrais', 'parent_id' => null]);
        Category::create(['name' => 'NPK',               'parent_id' => $engrais->id]);
        Category::create(['name' => 'Urée',              'parent_id' => $engrais->id]);
        Category::create(['name' => 'Engrais organiques', 'parent_id' => $engrais->id]);

        // ── Semences ─────────────────────────────────────────────────
        $semences = Category::create(['name' => 'Semences', 'parent_id' => null]);
        Category::create(['name' => 'Maïs',  'parent_id' => $semences->id]);
        Category::create(['name' => 'Cacao', 'parent_id' => $semences->id]);
        Category::create(['name' => 'Riz',   'parent_id' => $semences->id]);
    }
}
