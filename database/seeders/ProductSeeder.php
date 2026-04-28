<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $cat = fn (string $name) => Category::where('name', $name)->firstOrFail()->id;

        // ── Herbicides ────────────────────────────────────────────────
        Product::create(['name' => 'Roundup 1L',              'category_id' => $cat('Herbicides'),  'price_fcfa' => 8500,  'description' => 'Herbicide systémique à base de glyphosate']);
        Product::create(['name' => 'Herbicide Express 500ml', 'category_id' => $cat('Herbicides'),  'price_fcfa' => 5200,  'description' => 'Désherbant sélectif cultures céréalières']);
        Product::create(['name' => 'Calaris 250ml',           'category_id' => $cat('Herbicides'),  'price_fcfa' => 6800,  'description' => 'Herbicide pré-levée pour maïs']);

        // ── Insecticides ──────────────────────────────────────────────
        Product::create(['name' => 'Lambda-Cyhalothrine 50ml', 'category_id' => $cat('Insecticides'), 'price_fcfa' => 3500,  'description' => 'Insecticide contact et ingestion']);
        Product::create(['name' => 'Décis EC 25 100ml',        'category_id' => $cat('Insecticides'), 'price_fcfa' => 4800,  'description' => 'Pyréthrinoïde contre insectes ravageurs']);
        Product::create(['name' => 'Karaté Zeon 300ml',        'category_id' => $cat('Insecticides'), 'price_fcfa' => 7200,  'description' => 'Insecticide large spectre pour cultures']);

        // ── Fongicides ────────────────────────────────────────────────
        Product::create(['name' => 'Ridomil Gold 1kg',  'category_id' => $cat('Fongicides'), 'price_fcfa' => 12000, 'description' => 'Fongicide systémique contre mildiou']);
        Product::create(['name' => 'Mancozèbe 1kg',     'category_id' => $cat('Fongicides'), 'price_fcfa' => 7500,  'description' => 'Fongicide préventif polyvalent']);
        Product::create(['name' => 'Amistar 100ml',     'category_id' => $cat('Fongicides'), 'price_fcfa' => 9800,  'description' => 'Fongicide systémique azoxystrobine']);

        // ── NPK ───────────────────────────────────────────────────────
        Product::create(['name' => 'NPK 15-15-15 25kg',  'category_id' => $cat('NPK'), 'price_fcfa' => 18000, 'description' => 'Engrais équilibré cultures vivrières']);
        Product::create(['name' => 'NPK 0-23-19 25kg',   'category_id' => $cat('NPK'), 'price_fcfa' => 20000, 'description' => 'Engrais enrichissement sol potassique']);
        Product::create(['name' => 'NPK 20-10-10 25kg',  'category_id' => $cat('NPK'), 'price_fcfa' => 17500, 'description' => 'Engrais azoté pour croissance végétative']);

        // ── Urée ──────────────────────────────────────────────────────
        Product::create(['name' => 'Urée 46% 25kg',           'category_id' => $cat('Urée'), 'price_fcfa' => 16500, 'description' => 'Engrais azoté concentré, application foliaire']);
        Product::create(['name' => 'Sulfate d\'Ammonium 25kg', 'category_id' => $cat('Urée'), 'price_fcfa' => 13000, 'description' => 'Engrais azoté soufré, acidifiant']);

        // ── Engrais organiques ────────────────────────────────────────
        Product::create(['name' => 'Compost Premium 50kg',    'category_id' => $cat('Engrais organiques'), 'price_fcfa' => 9500, 'description' => 'Compost organique mature enrichi']);
        Product::create(['name' => 'Fumure Organique 50kg',   'category_id' => $cat('Engrais organiques'), 'price_fcfa' => 7000, 'description' => 'Fumier conditionné pour amendement sol']);
        Product::create(['name' => 'Biohumus Liquide 1L',     'category_id' => $cat('Engrais organiques'), 'price_fcfa' => 4500, 'description' => 'Extrait de vers de terre, stimulateur racinaire']);

        // ── Maïs ──────────────────────────────────────────────────────
        Product::create(['name' => 'Semence Maïs IKENNE 5kg',    'category_id' => $cat('Maïs'), 'price_fcfa' => 6500, 'description' => 'Variété haut rendement, cycle 90 jours']);
        Product::create(['name' => 'Semence Maïs CMS 8704 5kg',  'category_id' => $cat('Maïs'), 'price_fcfa' => 7800, 'description' => 'Hybride tolérant à la sécheresse']);

        // ── Cacao ─────────────────────────────────────────────────────
        Product::create(['name' => 'Semence Cacao T82 100g',       'category_id' => $cat('Cacao'), 'price_fcfa' => 15000, 'description' => 'Cacaoyer résistant au swollen shoot']);
        Product::create(['name' => 'Semence Cacao Mercedes 100g',  'category_id' => $cat('Cacao'), 'price_fcfa' => 18500, 'description' => 'Variété à haute teneur en beurre de cacao']);

        // ── Riz ───────────────────────────────────────────────────────
        Product::create(['name' => 'Semence Riz WITA 4 5kg',  'category_id' => $cat('Riz'), 'price_fcfa' => 5500, 'description' => 'Riz irrigué haut rendement']);
        Product::create(['name' => 'Semence Riz IR841 5kg',   'category_id' => $cat('Riz'), 'price_fcfa' => 6200, 'description' => 'Riz pluvial adapté zones humides CI']);
    }
}
