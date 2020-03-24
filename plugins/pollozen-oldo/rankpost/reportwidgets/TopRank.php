<?php namespace PolloZen\RankPost\ReportWidgets;

use Backend\Classes\ReportWidgetBase;
use ApplicationException;
use DB;
use Exception;

class TopRank extends ReportWidgetBase
{
    public function render()
    {        
        $tipo = $this->property('getdatatype');
                
        switch($tipo){
            case 'amin':
                   $this->getAMIN();
            break;
            case 'vote':
                   $this->getVoted();
            break;
            default:
                $this->getAMIN();
            break;
        }
        return $this->makePartial('widget');
    }

    public function defineProperties()
    {
        return [
            'numarticles' => [
                'title'             => 'Number of articles to display data for',
                'default'           => '5',
                'type'              => 'string',
                'validationPattern' => '^[0-9]+$'
            ],
            'getdatatype' => [
                'title'     => 'Type of report will be displayed',
                'default'   => 'amin',
                'type'      => 'dropdown',
                'options'   => ['amin'=>'AMIN Rank', 'vote'=>'Average Voted', ]
            ]
        ];
    }

    protected function getAMIN(){
        $limit = $this->property('numarticles');
        $topRank = DB::table('pollozen_rankpost_ranks as rank')
            ->select('rank.*','post.title','post.slug', DB::raw('datediff(now(), rank.created_at) as time_online'))
            ->join('rainlab_blog_posts as post','rank.post_id', '=', 'post.id', 'left')
            ->orderBy('rank.star','desc')
            ->orderBy('rank.vote','desc')
            ->orderBy('rank.updated_at','desc')
            ->limit($limit)
            ->get();
        foreach($topRank as $rank){
            $rank->amin = (($rank->vote/$rank->time_online) + $rank->star) / 2;
        }
        usort($topRank, array($this, "cmp"));
        $this->vars['title'] = 'Top AMIN Rank';
        $this->vars['help'] = 'It is the factor used to calculate the significance of the publication . It is based on number of votes, time of publication and user ratings';
        $this->vars['label'] = 'AMIN';
        $this->vars['topRank'] = $topRank;
    }
    protected function getVoted(){
        $limit = $this->property('numarticles');
        $topRank = DB::table('pollozen_rankpost_ranks as rank')
            ->select(
                'rank.*','post.title','post.slug', 
                DB::raw('TRUNCATE((rank.vote/datediff(now(), rank.created_at)),3) as amin')
                )
            ->join('rainlab_blog_posts as post','rank.post_id', '=', 'post.id', 'left')
            ->orderBy('amin','desc')
            ->orderBy('rank.vote','desc')
            ->limit($limit)
            ->get();

        $this->vars['title'] = 'Top Average Votes';
        $this->vars['help'] = 'Shows you the average votes (Votes Vs Publised time)';
        $this->vars['label'] = 'Average Votes';
        $this->vars['topRank'] = $topRank;
    }
    
    public function cmp($a, $b) {
        return strcmp($b->amin, $a->amin);
    }
}