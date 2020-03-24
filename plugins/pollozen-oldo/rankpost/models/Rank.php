<?php namespace PolloZen\RankPost\Models;

use Model;
use RainLab\Blog\Models\Post;

/**
 * Rank Model
 */
class Rank extends Model
{
    /**
     * @var string The database table used by the model.
     */
    public $table = 'pollozen_rankpost_ranks';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];

    /**
     * @var array Relations
     */
    public $belongsTo = [
        'post' => ['RainLab\Blog\Models\Post']
    ];

    public static function getFromPost($post)
    {
        if($post->rank)
            return $post->rank;
        $rank = new static;
        $rank->post = $post;
        $rank->save();

        $post->rank = $rank;

        return $rank;
    }

}