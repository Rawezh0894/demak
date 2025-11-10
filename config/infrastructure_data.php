<?php
/**
 * Infrastructure Categories Data
 * 
 * This file contains all infrastructure project categories and their details
 * Used by: pages/public/infrastructure.php
 */

// Make sure translations are loaded
if (!function_exists('t')) {
    die('Translation function not loaded. Please include translations.php first.');
}

// Infrastructure categories data
$infrastructure_categories = [
    'architectural' => [
        'title' => t('architectural_drawings'),
        'title_ku' => 'نەخشەی تەلارسازی',
        'title_ar' => 'الرسومات المعمارية',
        'title_en' => 'Architectural Drawings',
        'icon' => 'fas fa-drafting-compass',
        'color' => '#3b82f6',
        'description' => t('architectural_description'),
        'projects' => [
            [
                'id' => 1,
                'name' => t('floor_plan'),
                'name_ku' => 'نەخشەی نهۆم',
                'name_ar' => 'مخطط الطابق',
                'name_en' => 'Floor Plan',
                'image' => 'https://images.unsplash.com/photo-1581094794329-c8112a89af12?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'images' => [
                    'https://images.unsplash.com/photo-1581094794329-c8112a89af12?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1504307651254-35680f356dfd?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1541971875076-8f970d573be6?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
                ],
                'price' => '$2,500',
                'duration' => '5 ' . t('days'),
                'description' => t('detailed_floor_plan'),
                'features' => [
                    t('detailed_room_layout'),
                    t('doors_windows'),
                    t('furniture_arrangement'),
                    t('precise_measurements'),
                    t('electrical_water_plan')
                ]
            ],
            [
                'id' => 2,
                'name' => t('elevation_view'),
                'name_ku' => 'دیمەنی بەرزایی',
                'name_ar' => 'منظر الارتفاع',
                'name_en' => 'Elevation View',
                'image' => 'https://images.unsplash.com/photo-1504307651254-35680f356dfd?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'images' => [
                    'https://images.unsplash.com/photo-1504307651254-35680f356dfd?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1581094794329-c8112a89af12?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1541971875076-8f970d573be6?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
                ],
                'price' => '$1,800',
                'duration' => '3 ' . t('days'),
                'description' => t('building_elevation'),
                'features' => [
                    t('exterior_building_view'),
                    t('colors_materials'),
                    t('windows_doors'),
                    t('interior_design'),
                    t('lighting_shadows')
                ]
            ],
            [
                'id' => 3,
                'name' => t('section_view'),
                'name_ku' => 'دیمەنی بەش',
                'name_ar' => 'منظر المقطع',
                'name_en' => 'Section View',
                'image' => 'https://images.unsplash.com/photo-1541971875076-8f970d573be6?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'images' => [
                    'https://images.unsplash.com/photo-1541971875076-8f970d573be6?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1504307651254-35680f356dfd?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1581094794329-c8112a89af12?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
                ],
                'price' => '$1,200',
                'duration' => '2 ' . t('days'),
                'description' => t('cross_section'),
                'features' => [
                    t('interior_building_view'),
                    t('room_depths'),
                    t('columns_beams'),
                    t('electrical_system'),
                    t('water_system')
                ]
            ]
        ]
    ],
    'structural' => [
        'title' => t('structural_drawings'),
        'title_ku' => 'خەریتەی ستڕەکچەری',
        'title_ar' => 'الرسومات الإنشائية',
        'title_en' => 'Structural Drawings',
        'icon' => 'fas fa-cube',
        'color' => '#10b981',
        'description' => t('structural_description'),
        'projects' => [
            [
                'id' => 4,
                'name' => t('foundation_plan'),
                'name_ku' => 'نەخشەی بنچینە',
                'name_ar' => 'مخطط الأساس',
                'name_en' => 'Foundation Plan',
                'image' => 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'images' => [
                    'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1541888946425-d81bb19240f5?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1581094794329-c8112a89af12?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1504307651254-35680f356dfd?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
                ],
                'price' => '$3,200',
                'duration' => '7 ' . t('days'),
                'description' => t('foundation_design'),
                'features' => [
                    t('foundation_design'),
                    t('columns'),
                    t('beams'),
                    t('precise_measurements'),
                    t('materials_used')
                ]
            ],
            [
                'id' => 5,
                'name' => t('beam_layout'),
                'name_ku' => 'ڕێکخستنی تیر',
                'name_ar' => 'تخطيط العوارض',
                'name_en' => 'Beam Layout',
                'image' => 'https://images.unsplash.com/photo-1541888946425-d81bb19240f5?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'images' => [
                    'https://images.unsplash.com/photo-1541888946425-d81bb19240f5?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1581094794329-c8112a89af12?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1504307651254-35680f356dfd?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
                ],
                'price' => '$2,800',
                'duration' => '6 ' . t('days'),
                'description' => t('structural_beam'),
                'features' => [
                    t('beam_design'),
                    t('dimensions'),
                    t('positions'),
                    t('precise_measurements'),
                    t('materials_used')
                ]
            ],
            [
                'id' => 6,
                'name' => t('column_details'),
                'name_ku' => 'وردەکاری ستوون',
                'name_ar' => 'تفاصيل الأعمدة',
                'name_en' => 'Column Details',
                'image' => 'https://images.unsplash.com/photo-1581094794329-c8112a89af12?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'images' => [
                    'https://images.unsplash.com/photo-1581094794329-c8112a89af12?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1541888946425-d81bb19240f5?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1504307651254-35680f356dfd?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
                ],
                'price' => '$1,900',
                'duration' => '4 ' . t('days'),
                'description' => t('column_design'),
                'features' => [
                    t('column_design'),
                    t('dimensions'),
                    t('positions'),
                    t('precise_measurements'),
                    t('materials_used')
                ]
            ]
        ]
    ],
    'mechanical' => [
        'title' => t('mechanical_drawings'),
        'title_ku' => 'نەخشەی میکانیکی',
        'title_ar' => 'الرسومات الميكانيكية',
        'title_en' => 'Mechanical Drawings',
        'icon' => 'fas fa-cogs',
        'color' => '#f59e0b',
        'description' => t('mechanical_description'),
        'projects' => [
            [
                'id' => 7,
                'name' => t('hvac_plan'),
                'name_ku' => 'نەخشەی گەرم و سارد',
                'name_ar' => 'مخطط التدفئة والتهوية',
                'name_en' => 'HVAC Plan',
                'image' => 'https://images.unsplash.com/photo-1504307651254-35680f356dfd?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'images' => [
                    'https://images.unsplash.com/photo-1504307651254-35680f356dfd?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1541971875076-8f970d573be6?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1581094794329-c8112a89af12?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
                ],
                'price' => '$2,200',
                'duration' => '4 ' . t('days'),
                'description' => t('heating_ventilation'),
                'features' => [
                    t('heating_system'),
                    t('cooling_system'),
                    t('ventilation'),
                    t('ducts'),
                    t('controls')
                ]
            ],
            [
                'id' => 8,
                'name' => t('plumbing_plan'),
                'name_ku' => 'نەخشەی ئاو',
                'name_ar' => 'مخطط السباكة',
                'name_en' => 'Plumbing Plan',
                'image' => 'https://images.unsplash.com/photo-1541971875076-8f970d573be6?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'images' => [
                    'https://images.unsplash.com/photo-1541971875076-8f970d573be6?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1504307651254-35680f356dfd?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1581094794329-c8112a89af12?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
                ],
                'price' => '$1,600',
                'duration' => '3 ' . t('days'),
                'description' => t('water_system'),
                'features' => [
                    t('cold_water_system'),
                    t('hot_water_system'),
                    t('drainage_system'),
                    t('pumps'),
                    t('controls')
                ]
            ],
            [
                'id' => 9,
                'name' => t('fire_safety'),
                'name_ku' => 'ئاسایشی ئاگر',
                'name_ar' => 'السلامة من الحرائق',
                'name_en' => 'Fire Safety',
                'image' => 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'images' => [
                    'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1541971875076-8f970d573be6?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1504307651254-35680f356dfd?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1581094794329-c8112a89af12?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
                ],
                'price' => '$1,400',
                'duration' => '2 ' . t('days'),
                'description' => t('fire_protection'),
                'features' => [
                    t('fire_extinguishing_system'),
                    t('alarm_system'),
                    t('exit_routes'),
                    t('controls'),
                    t('protection')
                ]
            ]
        ]
    ],
    'electrical' => [
        'title' => t('electrical_drawings'),
        'title_ku' => 'نەخشەی کارەبا',
        'title_ar' => 'الرسومات الكهربائية',
        'title_en' => 'Electrical Drawings',
        'icon' => 'fas fa-bolt',
        'color' => '#8b5cf6',
        'description' => t('electrical_description'),
        'projects' => [
            [
                'id' => 10,
                'name' => t('electrical_plan'),
                'name_ku' => 'نەخشەی کارەبا',
                'name_ar' => 'المخطط الكهربائي',
                'name_en' => 'Electrical Plan',
                'image' => 'https://images.unsplash.com/photo-1541888946425-d81bb19240f5?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'images' => [
                    'https://images.unsplash.com/photo-1541888946425-d81bb19240f5?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1504307651254-35680f356dfd?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1581094794329-c8112a89af12?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
                ],
                'price' => '$2,000',
                'duration' => '5 ' . t('days'),
                'description' => t('power_distribution'),
                'features' => [
                    t('power_distribution'),
                    t('panels'),
                    t('circuits'),
                    t('controls'),
                    t('protection')
                ]
            ],
            [
                'id' => 11,
                'name' => t('lighting_plan'),
                'name_ku' => 'نەخشەی ڕووناکی',
                'name_ar' => 'مخطط الإضاءة',
                'name_en' => 'Lighting Plan',
                'image' => 'https://images.unsplash.com/photo-1504307651254-35680f356dfd?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'images' => [
                    'https://images.unsplash.com/photo-1504307651254-35680f356dfd?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1541888946425-d81bb19240f5?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1581094794329-c8112a89af12?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
                ],
                'price' => '$1,500',
                'duration' => '3 ' . t('days'),
                'description' => t('lighting_design'),
                'features' => [
                    t('lighting_design'),
                    t('light_types'),
                    t('positions'),
                    t('controls'),
                    t('energy')
                ]
            ],
            [
                'id' => 12,
                'name' => t('security_system'),
                'name_ku' => 'سیستەمی ئاسایش',
                'name_ar' => 'نظام الأمان',
                'name_en' => 'Security System',
                'image' => 'https://images.unsplash.com/photo-1581094794329-c8112a89af12?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'images' => [
                    'https://images.unsplash.com/photo-1581094794329-c8112a89af12?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1541888946425-d81bb19240f5?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1504307651254-35680f356dfd?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
                ],
                'price' => '$1,800',
                'duration' => '4 ' . t('days'),
                'description' => t('security_design'),
                'features' => [
                    t('cameras'),
                    t('alarm_system'),
                    t('access_control'),
                    t('controls'),
                    t('protection')
                ]
            ]
        ]
    ]
];

// Return the data for use in other files
return $infrastructure_categories;

