<?php

namespace Database\Seeders;

use App\Models\PostChemoRecommendation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PostChemoRecommendationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $recommendations = [
            [
                'description' => 'التزم بالوصفة التي كتبت من قبل طبيبك لتأخذها في المنزل بعد الجرعة الكيميائية حسب الفترة المحددة لكل دواء.',
                'type' => 'recommendation',
                'order' => 1,
            ],
            [
                'description' => 'احرص على شرب 8 إلى 10 أكواب من السوائل يومياً أثناء كامل فترة العلاج كي لا تتعرض للتجفاف.',
                'type' => 'recommendation',
                'order' => 2,
            ],
            [
                'description' => 'احرص على تناول وجبات صغيرة متنوعة متعددة أثناء اليوم.',
                'type' => 'recommendation',
                'order' => 3,
            ],
            [
                'description' => 'إن التغذية المتوازنة المتنوعة هي المناسبة لجميع المرضى، وفي حال وجود حمية غذائية خاصة لحالتك سنخبرك عنها.',
                'type' => 'recommendation',
                'order' => 4,
            ],
            [
                'description' => 'الإقياءات أوالإسهالات هي سبب مهم لفقدان السوائل واضطراب الشوارد وقد تؤدي لقصور كلوي، لذلك من المهم تعويض السوائل والشوارد وقد يتطلب ذلك تعويضها عن طريق السيرومات بالوريد.',
                'type' => 'recommendation',
                'order' => 5,
            ],
            [
                'description' => 'احرص على إجراء التحاليل الدموية المطلوبة منك بعد الجرعة لتحديد الحاجة لنقل مشتقات الدم أو إعطاء الأدوية الرافعة للمناعة.',
                'type' => 'recommendation',
                'order' => 6,
            ],

            // الأعراض التحذيرية
            [
                'description' => '( درجة مئوية38 أو أكثر)  ارتفاع درجة الحرارة',
                'type' => 'warning_symptom',
                'order' => 1,
            ],
            [
                'description' => 'حدوث قشعريرة أو بردية',
                'type' => 'warning_symptom',
                'order' => 2,
            ],
            [
                'description' => 'التهاب بلعوم أو سعال حديث',
                'type' => 'warning_symptom',
                'order' => 3,
            ],
            [
                'description' => 'حس حرقة عند التبول',
                'type' => 'warning_symptom',
                'order' => 4,
            ],
            [
                'description' => 'إحمرار أو انتفاخ أو تقيح مكان القثطرة الوريدية',
                'type' => 'warning_symptom',
                'order' => 5,
            ],
            [
                'description' => 'حدوث قلاعات فموية مزعجة عند البلع ',
                'type' => 'warning_symptom',
                'order' => 6,
            ],
            [
                'description' => 'حدوث إقياءات على الرغم من تناول مضادات الغثيان',
                'type' => 'warning_symptom',
                'order' => 7,
            ],
            [
                'description' => 'حدوث ألم صدري أو بطني مهم لم يكن موجوداً عندك',
                'type' => 'warning_symptom',
                'order' => 8,
            ],
            [
                'description' => ' إسهالات ل 3 مرات أو أكثر باليوم الواحد',
                'type' => 'warning_symptom',
                'order' => 9,
            ],
            [
                'description' => 'إمساك شديد لمدة يومين',
                'type' => 'warning_symptom',
                'order' => 10,
            ],
            [
                'description' => 'رؤية دم بالبول',
                'type' => 'warning_symptom',
                'order' => 11,
            ],
            [
                'description' => 'وجود دم مع البراز أو براز بلون أسود',
                'type' => 'warning_symptom',
                'order' => 12,
            ],
            [
                'description' => ' الشعور بالدوار أو عدم القدرة على التوازن',
                'type' => 'warning_symptom',
                'order' => 13,
            ],
        ];
        foreach ($recommendations as $rec) {
            PostChemoRecommendation::updateOrCreate(
                ['description' => $rec['description'], 'type' => $rec['type']],
                $rec
            );
        }
    }
}
