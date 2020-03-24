<?php namespace PolloZen\RankPost;

use Backend;
use System\Classes\PluginBase;
// use RainLab\Blog\Models\Post as PostModel;
// use RainLab\Blog\Controllers\Posts as PostController;
use PolloZen\RankPost\Models\Rank as RankModel;

/**
 * RankPost Plugin Information File
 */
class Plugin extends PluginBase
{
    public $require = ['RainLab.Blog'];
    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'RankPost',
            'description' => 'Allows you to set a rank for RainLab Blog Posts.',
            'author'      => 'PolloZen',
            'icon'        => 'icon-star'
        ];
    }

    public function boot()
    {
        PostModel::extend(function($model){
            $model->hasOne['rank'] = ['PolloZen\RankPost\Models\Rank'];
        });

        // Extend the controller
        PostController::extendFormFields(function($form, $model, $context) {
            if (!$model instanceof PostModel) return;
            if (!$model->exists) return;

            RankModel::getFromPost($model);

            $form->addSecondaryTabFields([
                'rank[star]' => [
                    'label'     => 'Rank',
                    'tab'       => 'Ranking',
                    'type'      => 'number',
                    'slugify'   => true
                ],
                'rank[vote]' =>[
                    'label'     => 'Votes',
                    'tab'       => 'Ranking',
                    'type'      => 'number',
                    'slugify'   => true
                ]
            ]);
        });

    }

    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents()
    {
        return [
            'PolloZen\RankPost\Components\SetRank' => 'SetRank',
        ];
    }

    public function registerReportWidgets()
    {
        return [
            'PolloZen\RankPost\ReportWidgets\TopRank'=>[
                'label'   => 'Report the top ranked articles',
                'context' => 'dashboard'
            ]
        ];
    }

    /**
     * Registers back-end navigation items for this plugin.
     *
     * @return array
     */
    public function registerNavigation()
    {
        return []; // Remove this line to activate

        return [
            'rankpost' => [
                'label'       => 'RankPost',
                'url'         => Backend::url('pollozen/rankpost/mycontroller'),
                'icon'        => 'icon-leaf',
                'permissions' => ['pollozen.rankpost.*'],
                'order'       => 500,
            ],
        ];
    }

}
