<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Package;
use App\Models\Unit;
use App\Models\Design;
use App\Models\Item;
use App\Models\PackageImage;
use Illuminate\Support\Facades\DB;

class PackageSeeder extends Seeder
{
    public function run()
    {
        // تعطيل فحص FK مؤقتاً لتجنب مشاكل الحذف
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        PackageImage::truncate();
        Item::truncate();
        Unit::truncate();
        Design::truncate();
        Package::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // إنشاء التصاميم الأساسية بالعربي والإنجليزي
        $designs = [
            ['name_ar' => 'تصميم كلاسيكي', 'name_en' => 'Classic Design', 'category' => 'bedroom'],
            ['name_ar' => 'تصميم عصري', 'name_en' => 'Modern Design', 'category' => 'bedroom'],
            ['name_ar' => 'تصميم معاصر', 'name_en' => 'Contemporary Design', 'category' => 'living_room'],
            ['name_ar' => 'تصميم طولي صناعي', 'name_en' => 'Industrial Kitchen Design', 'category' => 'kitchen'],
        ];

        foreach ($designs as $design) {
            Design::create($design);
        }

        // إنشاء الباكجات
        $packages = [
            [
                'name_ar' => 'استوديو',
                'name_en' => 'Studio',
                'price' => 15000,
                'description_ar' => 'باكج مثالي للوحدات الصغيرة مع تصميم عصري',
                'description_en' => 'Perfect package for small units with modern design',
                'number_of_pieces' => 3,
                'available_colors' => ['أحمر','أزرق','أخضر'],
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name_ar' => 'غرفة نوم واحدة',
                'name_en' => 'One Bedroom',
                'price' => 25000,
                'description_ar' => 'باكج متكامل لشقة بغرفة نوم واحدة',
                'description_en' => 'Complete package for a one-bedroom apartment',
                'number_of_pieces' => 4,
                'available_colors' => ['أحمر','أزرق','أخضر'],
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name_ar' => 'غرفتين نوم',
                'name_en' => 'Two Bedrooms',
                'price' => 40000,
                'description_ar' => 'باكج فاخر لشقة بغرفتين نوم',
                'description_en' => 'Luxury package for a two-bedroom apartment',
                'number_of_pieces' => 5,
                'available_colors' => ['أحمر','أزرق','أخضر'],
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name_ar' => 'ثلاث غرف نوم',
                'name_en' => 'Three Bedrooms',
                'price' => 60000,
                'description_ar' => 'باكج ملكي للفلل والشقق الكبيرة',
                'description_en' => 'Royal package for villas and large apartments',
                'number_of_pieces' => 6,
                'available_colors' => ['أحمر','أزرق','أخضر'],
                'is_active' => true,
                'sort_order' => 4,
            ],
        ];

        foreach ($packages as $packageData) {
            $package = Package::create($packageData);

            // إضافة الصورة الأساسية
            PackageImage::create([
                'package_id' => $package->id,
                'image_path' => 'packages/default-package.jpg',
                'is_primary' => true,
                'sort_order' => 1,
            ]);

            // إنشاء الوحدات لكل باكج
            for ($i = 1; $i <= $package->number_of_pieces; $i++) {
                switch ($i) {
                    case 1:
                        $unitName = ['ar' => 'غرفة النوم الرئيسية', 'en' => 'Master Bedroom'];
                        $unitType = 'bedroom';
                        break;
                    case 2:
                        $unitName = ['ar' => 'المعيشة', 'en' => 'Living Room'];
                        $unitType = 'living_room';
                        break;
                    case 3:
                        $unitName = ['ar' => 'المطبخ', 'en' => 'Kitchen'];
                        $unitType = 'kitchen';
                        break;
                    case 4:
                        $unitName = ['ar' => 'غرفة النوم الثانية', 'en' => 'Second Bedroom'];
                        $unitType = 'bedroom';
                        break;
                    case 5:
                        $unitName = ['ar' => 'الحمام', 'en' => 'Bathroom'];
                        $unitType = 'bathroom';
                        break;
                    default:
                        $unitName = ['ar' => 'غرفة النوم الثالثة', 'en' => 'Third Bedroom'];
                        $unitType = 'bedroom';
                }

                $unit = Unit::create([
                    'package_id' => $package->id,
                    'name_ar' => $unitName['ar'],
                    'name_en' => $unitName['en'],
                    'type' => $unitType,
                    'description_ar' => "وحدة {$unitName['ar']} للباكج {$package->name_ar}",
                    'description_en' => "Unit {$unitName['en']} for package {$package->name_en}",
                ]);

                // ربط التصاميم بالوحدة بعد التأكد من وجودها
                $designIds = Design::where('category', $unitType)->pluck('id')->toArray();
                if (!empty($designIds)) {
                    $unit->designs()->attach($designIds);
                }

                // إضافة القطع لكل وحدة فقط لو فيه تصميم مرتبط
                if (!empty($designIds)) {
                    $items = [
                        ['item_name_ar'=>'سرير مزدوج','item_name_en'=>'Double Bed','quantity'=>1,'dimensions'=>'200x180x50','material'=>'خشب طبيعي','color'=>'بني'],
                        ['item_name_ar'=>'خزانة ملابس','item_name_en'=>'Wardrobe','quantity'=>1,'dimensions'=>'200x60x220','material'=>'خشب MDF','color'=>'أبيض'],
                        ['item_name_ar'=>'طاولة جانبية','item_name_en'=>'Side Table','quantity'=>2,'dimensions'=>'50x50x60','material'=>'خشب + زجاج','color'=>'أسود'],
                    ];

                    foreach ($items as $item) {
                        Item::create([
                            'unit_id' => $unit->id,
                            'design_id' => $designIds[0],
                            'item_name_ar' => $item['item_name_ar'],
                            'item_name_en' => $item['item_name_en'],
                            'quantity' => $item['quantity'],
                            'dimensions' => $item['dimensions'],
                            'material' => $item['material'],
                            'color' => $item['color'],
                        ]);
                    }
                }
            }
        }
    }
}
