<?php

namespace App\Filament\Resources\PackageResource\Pages;

use App\Filament\Resources\PackageResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePackage extends CreateRecord
{
    protected static string $resource = PackageResource::class;

    protected function getHeaderActions(): array
    {
        $actions = [];

        // Add dev-only action to fill form with mock data
        if (app()->environment("local")) {
            $actions[] = Actions\Action::make("fillMockData")
                ->label("Fill Mock Data")
                ->icon("heroicon-o-beaker")
                ->color("warning")
                ->action(function () {
                    $this->form->fill([
                        "name" => [
                            "en" => "Luxury Wellness Retreat Package",
                            "ar" => "باقة الاستجمام الفاخرة",
                        ],
                        "status" => \App\Enums\PackageStatus::active->value,
                        "durations" => 7,
                        "tags" => "wellness, luxury, spa, retreat, health",
                        "destinations" => [1], // Assuming at least one destination exists
                        "description" => [
                            "en" =>
                                "Experience ultimate relaxation and rejuvenation with our exclusive wellness retreat package. This comprehensive program combines traditional therapies with modern wellness techniques.",
                            "ar" =>
                                "اختبر الاسترخاء والتجديد النهائي مع باقة الاستجمام الحصرية لدينا. يجمع هذا البرنامج الشامل بين العلاجات التقليدية وتقنيات العافية الحديثة.",
                        ],
                        "chips" => [\App\Enums\PackageChip::yoga->value],
                        "goal" => [
                            "en" =>
                                "Achieve complete mental and physical wellness through a carefully curated program of activities, treatments, and nutritional guidance.",
                            "ar" =>
                                "تحقيق العافية الكاملة العقلية والجسدية من خلال برنامج منسق بعناية من الأنشطة والعلاجات والإرشادات الغذائية.",
                        ],
                        "program" => [
                            "en" =>
                                "<p><strong>Day 1-2:</strong> Arrival and assessment</p><p><strong>Day 3-5:</strong> Intensive wellness activities</p><p><strong>Day 6-7:</strong> Integration and departure preparation</p>",
                            "ar" =>
                                "<p><strong>اليوم 1-2:</strong> الوصول والتقييم</p><p><strong>اليوم 3-5:</strong> أنشطة العافية المكثفة</p><p><strong>اليوم 6-7:</strong> التكامل والاستعداد للمغادرة</p>",
                        ],
                        "activities" => [
                            "en" =>
                                "<ul><li>Daily yoga and meditation sessions</li><li>Spa treatments</li><li>Nutritional consultations</li><li>Fitness training</li></ul>",
                            "ar" =>
                                "<ul><li>جلسات يوغا وتأمل يومية</li><li>علاجات السبا</li><li>استشارات غذائية</li><li>تدريب اللياقة البدنية</li></ul>",
                        ],
                        "stay" => [
                            "en" =>
                                "<p>Luxury ocean-view suite with private balcony, king-size bed, and spa-inspired bathroom. All meals included with organic, locally-sourced ingredients.</p>",
                            "ar" =>
                                "<p>جناح فاخر بإطلالة على المحيط مع شرفة خاصة وسرير كينج وحمام مستوحى من السبا. جميع الوجبات مشمولة مع مكونات عضوية محلية المصدر.</p>",
                        ],
                        "iv_drips" => [
                            "en" =>
                                "<p><strong>Wellness IV Drip:</strong> Vitamin C, B-complex, Magnesium</p><p><strong>Energy Boost IV:</strong> B12, Amino acids, Electrolytes</p>",
                            "ar" =>
                                "<p><strong>محلول العافية الوريدي:</strong> فيتامين C، مركب B، المغنيسيوم</p><p><strong>محلول تعزيز الطاقة:</strong> B12، الأحماض الأمينية، الإلكتروليتات</p>",
                        ],
                    ]);

                    $this->dispatch("notify", [
                        "type" => "success",
                        "message" => "Form filled with mock data!",
                    ]);
                });
        }

        return $actions;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl("index");
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return "Package created successfully";
    }
}
