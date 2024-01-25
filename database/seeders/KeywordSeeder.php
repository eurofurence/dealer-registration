<?php

namespace Database\Seeders;

use App\Models\Keyword;
use App\Models\Category;
use Illuminate\Database\Seeder;

class KeywordSeeder extends Seeder
{
    private static function createCategory(string $name, string $description): Category
    {
        return Category::firstOrCreate([
            'name' => $name
        ], [
            'name' => $name,
            'description' => $description
        ]);
    }

    private static function createKeyword(Category $category, string $name): Keyword
    {
        return Keyword::firstOrCreate([
            'name' => $name
        ], [
            'category_uuid' => $category->uuid,
            'name' => $name
        ]);
    }

    public function run()
    {
        $category = self::createCategory(
            'Adult Toys',
            'Merchants specializing in adult toys. Anything in this category is considered NSFW and therefore found exclusively in the ADDD.'
        );
        self::createKeyword($category, 'Dildos and Butt Plugs');
        self::createKeyword($category, 'Strokers and Masturbators');
        self::createKeyword($category, 'Adult Toys and Items (Other)');

        $category = self::createCategory(
            'Adult Gear',
            'Dealers presenting a selection of both provocative and alluring items like rubber and latex gear. Embrace your playful side with puppy gear and discover the perfect blend of elegance and restraint with collars, cuffs, harnesses, and leashes.'
        );
        self::createKeyword($category, 'Rubber and Latex Gear');
        self::createKeyword($category, 'Puppy Gear');
        self::createKeyword($category, 'Collars, Cuffs, Harnesses');
        self::createKeyword($category, 'Leashes');

        $category = self::createCategory(
            'Literary Arts',
            'Dealers offering books, novels, anthologies, comics and similar artwork with a focus on written language.'
        );
        self::createKeyword($category, 'Books and eBooks');
        self::createKeyword($category, 'Comics and Graphic Novels');

        $category = self::createCategory(
            'Visual Arts',
            'Artists showcasing artwork in various forms such as prints, stickers, calendars, portfolios, 3D models and illustrations using traditional and digital techniques.'
        );
        self::createKeyword($category, 'Original Drawings and Paintings');
        self::createKeyword($category, 'Prints and Posters');
        self::createKeyword($category, 'Badges');
        self::createKeyword($category, 'Stickers');
        self::createKeyword($category, 'Art Books');
        self::createKeyword($category, 'Calendars');
        self::createKeyword($category, 'Wall Scrolls and Tapestries');
        self::createKeyword($category, '3D Models');
        self::createKeyword($category, 'Sculptures');
        self::createKeyword($category, 'Visual Arts (Other)');

        $category = self::createCategory(
            'Commissions and Services',
            'Services offered by artists, such as traditional and digital commissions, customizable avatars, VR integration, and online art classes, providing opportunities for personalized creations and learning experiences.'
        );
        self::createKeyword($category, 'Commissions');
        self::createKeyword($category, '3D Modeling');
        self::createKeyword($category, '3D Printing');
        self::createKeyword($category, 'VR Integration');
        self::createKeyword($category, 'Workshops, Classes, Tutorials');

        $category = self::createCategory(
            'Clothing and Wearables',
            'Diverse apparel items like T-shirts, hoodies, sweaters, or kigurumis. Also, accessories like scarves and bandanas.'
        );
        self::createKeyword($category, 'T-Shirts');
        self::createKeyword($category, 'Hoodies, Sweaters and Jackets');
        self::createKeyword($category, 'Underwear');
        self::createKeyword($category, 'Hats and Beanies');
        self::createKeyword($category, 'Bandanas');
        self::createKeyword($category, 'Kigurumis');
        self::createKeyword($category, 'Clothing (Other)');

        $category = self::createCategory(
            'Fursuits and Fursuit Accessories',
            'Dealers offering a variety of premade fursuits and partials, fursuit parts such as head bases, care products or cooling utilities.'
        );
        self::createKeyword($category, 'Premade Fursuits and Partials');
        self::createKeyword($category, 'Fursuit Parts (Eyes, Teeth, Electronics, etc.)');
        self::createKeyword($category, 'Head Bases');
        self::createKeyword($category, 'Fursuit Care Items (Sprays, Brushes, etc.)');
        self::createKeyword($category, 'Cooling Utilities (Vests, Fans, etc.)');

        $category = self::createCategory(
            'Foods and Drinks',
            'Offerings of delectable treats or handcrafted drinks like craft beer, cider, mead, and liqueur.'
        );
        self::createKeyword($category, 'Drinks (Alcoholic)');
        self::createKeyword($category, 'Foods and Drinks (Other)');

        $category = self::createCategory(
            'Plushies',
            'Dealers offering soft, cuddly companions and plushy props, waiting to find a new home.'
        );
        self::createKeyword($category, 'Plushy Animals');
        self::createKeyword($category, 'Plushy Objects and Props');

        $category = self::createCategory(
            'Music and Audio',
            'Offerings from dealers with a melodic array of music, audio books, and other audio treasures.'
        );
        self::createKeyword($category, 'Music');
        self::createKeyword($category, 'Music and Audio (Other)');

        $category = self::createCategory(
            'Home and Bedding Décor',
            'Transform your home into a cozy haven of furry flair with blankets, pillows, dakimakuras, towels, mouse pads, playmats, coasters and other items from our Dealers.'
        );
        self::createKeyword($category, 'Blankets and Bed Sheets');
        self::createKeyword($category, 'Pillows and Dakimakuras');
        self::createKeyword($category, 'Towels');
        self::createKeyword($category, 'Mouse Pads and Playmats');
        self::createKeyword($category, 'Coasters');
        self::createKeyword($category, 'Furnishings (Other)');

        $category = self::createCategory(
            'Original Crafts',
            'Dealers demonstrating their expertise in creating unique and creative items for your daily life, including pins and buttons, lanyards, keychains, mugs, bags, complete games, and more.'
        );
        self::createKeyword($category, 'Pins and Buttons');
        self::createKeyword($category, 'Lanyards, Keychains and Charms');
        self::createKeyword($category, 'Games');
        self::createKeyword($category, 'Cups and Mugs');
        self::createKeyword($category, 'Bags');
        self::createKeyword($category, 'Patches');
        self::createKeyword($category, 'Magnets');
        self::createKeyword($category, 'Candles');
        self::createKeyword($category, 'Jewelery (Rings, Necklaces, Pendants, etc.)');
        self::createKeyword($category, 'Original Crafts (Other)');
    }
}
