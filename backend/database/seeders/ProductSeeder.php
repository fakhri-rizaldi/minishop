<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::pluck('id', 'slug');

        $products = [
            // Tanaman Indoor
            [
                'category_id' => $categories['tanaman-indoor'],
                'name' => 'Monstera Deliciosa',
                'description' => 'Tanaman hias tropis dengan daun berlubang artistik yang mudah dirawat.',
                'price' => 185000,
                'stock' => 8,
                'image_url' => 'https://picsum.photos/seed/monstera-deliciosa/600/600',
            ],
            [
                'category_id' => $categories['tanaman-indoor'],
                'name' => 'Sansevieria Trifasciata',
                'description' => 'Lidah mertua pembersih udara ruangan, tahan dalam kondisi cahaya redup.',
                'price' => 65000,
                'stock' => 15,
                'image_url' => 'https://picsum.photos/seed/sansevieria-trifasciata/600/600',
            ],
            [
                'category_id' => $categories['tanaman-indoor'],
                'name' => 'Ficus Lyrata Ketapang Biola',
                'description' => 'Pohon hias indoor dengan daun lebar mengkilap untuk sudut ruangan estetis.',
                'price' => 275000,
                'stock' => 3, // stok <= 5
                'image_url' => 'https://picsum.photos/seed/ficus-lyrata/600/600',
            ],
            [
                'category_id' => $categories['tanaman-indoor'],
                'name' => 'Calathea Orbifolia',
                'description' => 'Tanaman hias dengan corak daun garis perak yang anggun dan memesona.',
                'price' => 120000,
                'stock' => 0, // stok 0
                'image_url' => 'https://picsum.photos/seed/calathea-orbifolia/600/600',
            ],
            [
                'category_id' => $categories['tanaman-indoor'],
                'name' => 'Philodendron Pink Princess',
                'description' => 'Tanaman koleksi langka dengan corak variegasi merah muda yang kontras dan mewah.',
                'price' => 350000,
                'stock' => 6,
                'image_url' => 'https://picsum.photos/seed/pink-princess/600/600',
            ],
            [
                'category_id' => $categories['tanaman-indoor'],
                'name' => 'Anthurium Clarinervium',
                'description' => 'Tanaman berdaun beludru berbentuk hati dengan urat daun putih menyala.',
                'price' => 220000,
                'stock' => 4, // stok <= 5
                'image_url' => 'https://picsum.photos/seed/anthurium-clari/600/600',
            ],
            [
                'category_id' => $categories['tanaman-indoor'],
                'name' => 'Epipremnum Aureum Sirih Gading',
                'description' => 'Tanaman merambat hijau semburat emas yang sangat adaptif dan mudah diperbanyak.',
                'price' => 45000,
                'stock' => 25,
                'image_url' => 'https://picsum.photos/seed/sirih-gading/600/600',
            ],
            [
                'category_id' => $categories['tanaman-indoor'],
                'name' => 'Zamioculcas Zamiifolia ZZ Plant',
                'description' => 'Tanaman tahan banting dengan daun tebal mengilap yang membutuhkan sedikit air.',
                'price' => 95000,
                'stock' => 11,
                'image_url' => 'https://picsum.photos/seed/zz-plant/600/600',
            ],

            // Pot & Wadah
            [
                'category_id' => $categories['pot-dan-wadah'],
                'name' => 'Pot Terakota Rustik 20cm',
                'description' => 'Pot tanah liat berpori alami yang menjaga kelembapan akar tanaman.',
                'price' => 45000,
                'stock' => 20,
                'image_url' => 'https://picsum.photos/seed/pot-terakota/600/600',
            ],
            [
                'category_id' => $categories['pot-dan-wadah'],
                'name' => 'Pot Keramik Putih Minimalis',
                'description' => 'Pot keramik dengan lapisan mengkilap dan tatakan kayu estetik.',
                'price' => 85000,
                'stock' => 5, // stok <= 5
                'image_url' => 'https://picsum.photos/seed/pot-keramik/600/600',
            ],
            [
                'category_id' => $categories['pot-dan-wadah'],
                'name' => 'Pot Semen Industrial Kubus',
                'description' => 'Pot beton cetak bertekstur kasar untuk tampilan interior modern kontemporer.',
                'price' => 65000,
                'stock' => 14,
                'image_url' => 'https://picsum.photos/seed/pot-semen/600/600',
            ],
            [
                'category_id' => $categories['pot-dan-wadah'],
                'name' => 'Pot Gantung Makrame Katun',
                'description' => 'Gantungan pot anyaman tali katun bohemian alami untuk tanaman gantung.',
                'price' => 55000,
                'stock' => 10,
                'image_url' => 'https://picsum.photos/seed/pot-makrame/600/600',
            ],
            [
                'category_id' => $categories['pot-dan-wadah'],
                'name' => 'Pot Kaca Hidroponik Botani',
                'description' => 'Wadah propagasi kaca transparan dengan penyangga kayu vintage.',
                'price' => 75000,
                'stock' => 7,
                'image_url' => 'https://picsum.photos/seed/pot-hidroponik/600/600',
            ],
            [
                'category_id' => $categories['pot-dan-wadah'],
                'name' => 'Pot Keramik Matte Terracotta',
                'description' => 'Pot keramik finishing matte warna tanah liat hangat berlubang drainase.',
                'price' => 110000,
                'stock' => 2, // stok <= 5
                'image_url' => 'https://picsum.photos/seed/pot-matte/600/600',
            ],

            // Perlengkapan Taman
            [
                'category_id' => $categories['perlengkapan-taman'],
                'name' => 'Penyiram Tanaman Kuningan',
                'description' => 'Watering can klasik berbahan stainless kuningan dengan corong presisi.',
                'price' => 145000,
                'stock' => 10,
                'image_url' => 'https://picsum.photos/seed/watering-can/600/600',
            ],
            [
                'category_id' => $categories['perlengkapan-taman'],
                'name' => 'Gunting Dahan Baja Karbon',
                'description' => 'Gunting pemangkas tanaman tajam anti-karat dengan gagang ergonomis.',
                'price' => 95000,
                'stock' => 12,
                'image_url' => 'https://picsum.photos/seed/pruning-shears/600/600',
            ],
            [
                'category_id' => $categories['perlengkapan-taman'],
                'name' => 'Sarung Tangan Kebun Kulit Sintetis',
                'description' => 'Sarung tangan pelindung anti-duri fleksibel dan nyaman dipakai berkebun.',
                'price' => 40000,
                'stock' => 30,
                'image_url' => 'https://picsum.photos/seed/garden-gloves/600/600',
            ],
            [
                'category_id' => $categories['perlengkapan-taman'],
                'name' => 'Semprotan Kabut Tanaman Kaca Vintage',
                'description' => 'Botol mister tanaman pompa tembaga untuk menyegarkan daun tropis.',
                'price' => 80000,
                'stock' => 16,
                'image_url' => 'https://picsum.photos/seed/glass-mister/600/600',
            ],
            [
                'category_id' => $categories['perlengkapan-taman'],
                'name' => 'Sekop Mini Taman Gagang Kayu',
                'description' => 'Sekop kecil berbahan besi tebal untuk memindahkan tanah pot indoor.',
                'price' => 35000,
                'stock' => 22,
                'image_url' => 'https://picsum.photos/seed/mini-trowel/600/600',
            ],

            // Dekorasi Rumah
            [
                'category_id' => $categories['dekorasi-rumah'],
                'name' => 'Rak Tanaman Kayu Jati 3 Tingkat',
                'description' => 'Stand tanaman bertingkat dari kayu jati solid untuk tata ruang hijau.',
                'price' => 320000,
                'stock' => 4, // stok <= 5
                'image_url' => 'https://picsum.photos/seed/plant-stand/600/600',
            ],
            [
                'category_id' => $categories['dekorasi-rumah'],
                'name' => 'Keranjang Anyaman Seagrass',
                'description' => 'Cover pot anyaman serat alami ramah lingkungan untuk tanaman besar.',
                'price' => 75000,
                'stock' => 18,
                'image_url' => 'https://picsum.photos/seed/seagrass-basket/600/600',
            ],
            [
                'category_id' => $categories['dekorasi-rumah'],
                'name' => 'Terarium Geometris Kaca Tembaga',
                'description' => 'Wadah terarium bentuk prisma kaca untuk tanaman sukulen dan lumut.',
                'price' => 165000,
                'stock' => 9,
                'image_url' => 'https://picsum.photos/seed/terarium-geo/600/600',
            ],
            [
                'category_id' => $categories['dekorasi-rumah'],
                'name' => 'Gantungan Dinding Kayu Pinus',
                'description' => 'Hanger dinding kayu solid minimalis untuk menggantung pot makrame.',
                'price' => 85000,
                'stock' => 13,
                'image_url' => 'https://picsum.photos/seed/wall-hanger/600/600',
            ],
        ];

        foreach ($products as $prod) {
            Product::firstOrCreate(
                ['name' => $prod['name']],
                $prod
            );
        }
    }
}
