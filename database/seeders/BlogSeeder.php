<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Blog;
use Illuminate\Support\Str;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        Blog::create([
            'title_ar' => 'تجهيزات تكنولوجية لراحة الضيوف',
            'title_en' => 'Tech Preparations for Guest Comfort',
            'slug_ar' => Str::slug('تجهيزات تكنولوجية لراحة الضيوف'),
            'slug_en' => Str::slug('Tech Preparations for Guest Comfort'),
            'excerpt_ar' => 'التكنولوجيا الحديثة تسهم في تحسين تجربة الإقامة.',
            'excerpt_en' => 'Modern technology enhances the guest experience.',
            'content_ar' => 'نص عربي طويل...',
            'content_en' => 'English content...',
            'image' => 'blogs/blog-01.jpg',
            'category_ar' => 'نصائح التأثيث',
            'category_en' => 'Furnishing Tips',
            'author_ar' => 'مصطفى خالد',
            'author_en' => 'Mostafa Khaled'
        ]);
    }
}
