<?php

namespace Database\Seeders;

use App\Models\Keyword;
use App\Models\Category;
use Illuminate\Database\Seeder;

class KeywordSeeder extends Seeder
{
    private static function createCategory(string $slug, string $name, string $description): Category
    {
        return Category::firstOrCreate([
            'slug' => $slug
        ], [
            'slug' => $slug,
            'name' => $name,
            'description' => $description
        ]);
    }

    private static function createKeyword(string $slug, Category $category, string $name): Keyword
    {
        return Keyword::firstOrCreate([
            'slug' => $slug
        ], [
            'slug' => $slug,
            'category_id' => $category->id,
            'name' => $name
        ]);
    }

    public function run()
    {
        $category = self::createCategory(
            'adult_toys',
            'Adult Toys',
            'Merchants specializing in adult toys. Anything in this category is considered NSFW and therefore found exclusively in the ADDD.'
        );
        self::createKeyword('dildos_plugs', $category, 'Dildos and Butt Plugs');
        self::createKeyword('mastubators', $category, 'Strokers and Masturbators');
        self::createKeyword('adult_toys_other', $category, 'Adult Toys and Items (Other)');

        $category = self::createCategory(
            'adult_gear',
            'Adult Gear',
            'Dealers presenting a selection of both provocative and alluring items like rubber and latex gear. Embrace your playful side with puppy gear and discover the perfect blend of elegance and restraint with collars, cuffs, harnesses, and leashes.'
        );
        self::createKeyword('rubber_latex', $category, 'Rubber and Latex Gear');
        self::createKeyword('puppy_gear', $category, 'Puppy Gear');
        self::createKeyword('adult_harness', $category, 'Collars, Cuffs, Harnesses');
        self::createKeyword('adult_leashes', $category, 'Leashes');

        $category = self::createCategory(
            'literary_arts',
            'Literary Arts',
            'Dealers offering books, novels, anthologies, comics and similar artwork with a focus on written language.'
        );
        self::createKeyword('books_ebooks', $category, 'Books and eBooks');
        self::createKeyword('comics_novels', $category, 'Comics and Graphic Novels');

        $category = self::createCategory(
            'visual_arts',
            'Visual Arts',
            'Artists showcasing artwork in various forms such as prints, stickers, calendars, portfolios, 3D models and illustrations using traditional and digital techniques.'
        );
        self::createKeyword('orig_drawings', $category, 'Original Drawings and Paintings');
        self::createKeyword('art_prints', $category, 'Prints and Posters');
        self::createKeyword('badges', $category, 'Badges');
        self::createKeyword('stickers', $category, 'Stickers');
        self::createKeyword('art_books', $category, 'Art Books');
        self::createKeyword('calendars', $category, 'Calendars');
        self::createKeyword('tapestries', $category, 'Wall Scrolls and Tapestries');
        self::createKeyword('3d_models', $category, '3D Models');
        self::createKeyword('sculptures', $category, 'Sculptures');
        self::createKeyword('visual_arts_other', $category, 'Visual Arts (Other)');

        $category = self::createCategory(
            'commissions_services',
            'Commissions and Services',
            'Services offered by artists, such as traditional and digital commissions, customizable avatars, VR integration, and online art classes, providing opportunities for personalized creations and learning experiences.'
        );
        self::createKeyword('commissions', $category, 'Commissions');
        self::createKeyword('3d_modeling', $category, '3D Modeling');
        self::createKeyword('3d_printing', $category, '3D Printing');
        self::createKeyword('vr_integration', $category, 'VR Integration');
        self::createKeyword('workshops', $category, 'Workshops, Classes, Tutorials');

        $category = self::createCategory(
            'clothing',
            'Clothing and Wearables',
            'Diverse apparel items like T-shirts, hoodies, sweaters, or kigurumis. Also, accessories like scarves and bandanas.'
        );
        self::createKeyword('t_shirts', $category, 'T-Shirts');
        self::createKeyword('hoodies_jackets', $category, 'Hoodies, Sweaters and Jackets');
        self::createKeyword('underwearh', $category, 'Underwear');
        self::createKeyword('hats_beanes', $category, 'Hats and Beanies');
        self::createKeyword('bandanas', $category, 'Bandanas');
        self::createKeyword('kigurumis', $category, 'Kigurumis');
        self::createKeyword('clothing_other', $category, 'Clothing (Other)');

        $category = self::createCategory(
            'fursuits_accessories',
            'Fursuits and Fursuit Accessories',
            'Dealers offering a variety of premade fursuits and partials, fursuit parts such as head bases, care products or cooling utilities.'
        );
        self::createKeyword('fursuits_premade', $category, 'Premade Fursuits and Partials');
        self::createKeyword('fursuit_parts', $category, 'Fursuit Parts (Eyes, Teeth, Electronics, etc.)');
        self::createKeyword('head_bases', $category, 'Head Bases');
        self::createKeyword('fursuit_care', $category, 'Fursuit Care Items (Sprays, Brushes, etc.)');
        self::createKeyword('fursuit_cooling', $category, 'Cooling Utilities (Vests, Fans, etc.)');

        $category = self::createCategory(
            'foods_drinks',
            'Foods and Drinks',
            'Offerings of delectable treats or handcrafted drinks like craft beer, cider, mead, and liqueur.'
        );
        self::createKeyword('alcohol', $category, 'Drinks (Alcoholic)');
        self::createKeyword('foods_drinks_other', $category, 'Foods and Drinks (Other)');

        $category = self::createCategory(
            'plushies',
            'Plushies',
            'Dealers offering soft, cuddly companions and plushy props, waiting to find a new home.'
        );
        self::createKeyword('plushy_animals', $category, 'Plushy Animals');
        self::createKeyword('plushy_objects', $category, 'Plushy Objects and Props');

        $category = self::createCategory(
            'music_audio',
            'Music and Audio',
            'Offerings from dealers with a melodic array of music, audio books, and other audio treasures.'
        );
        self::createKeyword('music', $category, 'Music');
        self::createKeyword('music_audio_other', $category, 'Music and Audio (Other)');

        $category = self::createCategory(
            'furnishings',
            'Home and Bedding Décor',
            'Transform your home into a cozy haven of furry flair with blankets, pillows, dakimakuras, towels, mouse pads, playmats, coasters and other items from our Dealers.'
        );
        self::createKeyword('blankets', $category, 'Blankets and Bed Sheets');
        self::createKeyword('pillows', $category, 'Pillows and Dakimakuras');
        self::createKeyword('towels', $category, 'Towels');
        self::createKeyword('mouse_pads', $category, 'Mouse Pads and Playmats');
        self::createKeyword('coasters', $category, 'Coasters');
        self::createKeyword('furnishings_other', $category, 'Furnishings (Other)');

        $category = self::createCategory(
            'crafts',
            'Original Crafts',
            'Dealers demonstrating their expertise in creating unique and creative items for your daily life, including pins and buttons, lanyards, keychains, mugs, bags, complete games, and more.'
        );
        self::createKeyword('pins_buttons', $category, 'Pins and Buttons');
        self::createKeyword('lanyards_charms', $category, 'Lanyards, Keychains and Charms');
        self::createKeyword('games', $category, 'Games');
        self::createKeyword('cups_mugs', $category, 'Cups and Mugs');
        self::createKeyword('bags', $category, 'Bags');
        self::createKeyword('patches', $category, 'Patches');
        self::createKeyword('magnets', $category, 'Magnets');
        self::createKeyword('candles', $category, 'Candles');
        self::createKeyword('jewelery', $category, 'Jewelery (Rings, Necklaces, Pendants, etc.)');
        self::createKeyword('crafts_other', $category, 'Original Crafts (Other)');
    }
}
