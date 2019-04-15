<?php
/**
 * Copyright © 2019 Denistrator. No rights reserved.
 * bitch
 */

namespace Denistrator\Theme\Block\Sections;


class Portfolio extends \Magento\Framework\View\Element\Template
{
    public function getPortfolioItems()
    {
        $portfolioItems = [
            [
                'imgPath' => 'project-timberland',
                'imgExt' => 'svg',
                'link' => 'https://www.timberland.co.il/',
                'titleEN' => 'Timberland (Israel)',
                'titleRU' => 'Timberland (Израиль)',
                'alternateImg' => true,
                'labels' => [
                    'magento' => [
                        'icon' => 'magento',
                        'text' => 'Magento 2 Project'
                    ]
                ]
            ],
            [
                'imgPath' => 'project-nautica',
                'imgExt' => 'svg',
                'link' => 'https://www.nautica.co.il/',
                'titleEN' => 'Nautica (Israel)',
                'titleRU' => 'Nautica (Израиль)',
                'alternateImg' => true,
                'labels' => [
                    'magento' => [
                        'icon' => 'magento',
                        'text' => 'Magento 2 Project'
                    ]
                ]
            ],
            [
                'imgPath' => 'project-layam',
                'imgExt' => 'png',
                'link' => 'https://www.layam.com/',
                'titleEN' => 'Layam Sakal (Israel)',
                'titleRU' => 'Layam Sakal (Израиль)',
                'alternateImg' => true,
                'labels' => [
                    'magento' => [
                        'icon' => 'magento',
                        'text' => 'Magento 2 Project'
                    ]
                ]
            ],
            [
                'imgPath' => 'project-1',
                'imgExt' => 'png',
                'link' => 'http://denistrator.esy.es/mountains',
                'titleEN' => 'Mountains',
                'titleRU' => 'Mountains',
                'githubLink' => 'https://github.com/denistrator/training-01-mountains',
                'mod' => 'portfolio-collapse-pos',
            ],
            [
                'imgPath' => 'project-2',
                'imgExt' => 'png',
                'link' => 'http://denistrator.esy.es/modest',
                'titleEN' => 'Modest',
                'titleRU' => 'Modest',
                'githubLink' => 'https://github.com/denistrator/spalah-lessons--lesson-8'
            ],
            [
                'imgPath' => 'project-3',
                'imgExt' => 'png',
                'link' => 'http://denistrator.esy.es/tajam',
                'titleEN' => 'Tajam',
                'titleRU' => 'Tajam',
                'githubLink' => 'https://github.com/denistrator/spalah-lessons--lesson-7',
                'alternateImg' => true
            ],
            [
                'imgPath' => 'project-4',
                'imgExt' => 'png',
                'link' => 'http://denistrator.esy.es/yellow_moon',
                'titleEN' => 'Yellow Moon',
                'titleRU' => 'Yellow Moon',
                'githubLink' => 'https://github.com/denistrator/spalah-lessons--lesson-6'
            ],
            [
                'imgPath' => 'project-no-image',
                'imgExt' => 'png',
                'link' => 'http://denistrator.esy.es/naturetour/',
                'titleEN' => 'Naturetour',
                'titleRU' => 'Naturetour',
                'githubLink' => '',
                'mod' => 'old',
                'alternateImg' => true
            ]
        ];

        return $portfolioItems;
    }
}
