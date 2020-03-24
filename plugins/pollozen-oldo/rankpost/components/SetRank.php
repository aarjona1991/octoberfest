<?php namespace PolloZen\RankPost\Components;

use Cms\Classes\ComponentBase;
use PolloZen\RankPost\Models\Rank;
use Db;

class SetRank extends ComponentBase
{
   /**
   * Variable que contiene la calificación del post
   * @var float
   */
   public $rank;

   /**
   * Variable que contiene el id del rank
   * @var integer
   */
   public $rankId;

   /**
   * Variable que contiene la cantidad de votos recibidos
   * @var integer
   */
   public $votes;

   /**
   * Variable que contiene la calificación dada en el momento
   * @var integer
   */
   public $receivedStars;

   /**
    * Variable que controla el step del votador
    * @var  float
    */
   public $step;

   /**
    * Variable de propiedad que controla el tamaño de la herramienta
    * @var string
    */
   public $toolsize;

   /**
    * Variable de propiedad que controla el tipo de icono simbolo que se muestra
    * @var string
    */
   public $symbol;

   public function componentDetails()
   {
      return [
         'name'        => 'SetRank Component',
         'description' => 'Shows a rank tool'
      ];
   }

   public function defineProperties()
   {
      return [
         'symbol'    => [
            'title'         =>  'Symbol used for rank',
            'description'   =>  'Which symbol must be displayed in the rank tool',
            'default'       =>  '&#xf005;',
            'type'          =>  'dropdown',
            'options'       =>  ['&#xf005;' =>'Stars','&#xf004;'=>'Hearts','&#xf06d;'=>'Fire', '&#xf164;'=>'Thumbs up']

         ],
         'toolsize'  => [
            'title'         =>  'Rank Size',
            'description'   =>  'Controls the symbol\'s size',
            'default'       =>  'sm',
            'type'          =>  'dropdown',
            'options'       =>  ['xs' => 'Extra Small' ,'sm'=>'Small', 'md' =>'Medium', 'lg' => 'Large', 'xl'=>'Extra Large']
         ],
         'step'  => [
            'title'         => 'Rank Step',
            'description'   => 'Rank\'s step avaible for the user',
            'default'       => '0.5',
            'type'          => 'dropdown',
            'options'       =>  ['0.5' =>'Half stars','1'=>'One complete stars','0.3'=>'One third star']
         ]
      ];
   }

   public function onRun(){
   if($this->page[ 'post' ]){ 
      $this->addJs('/plugins/PolloZen/RankPost/assets/js/star-rating.min.js');
      $this->addCss('/plugins/PolloZen/RankPost/assets/css/star-rating.min.css');
      $this->addCss('https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css');
      $this->symbol = $this->property('symbol');
      $this->toolsize = $this->property('toolsize');
      $this->step = $this->property('step');

      if(!isset($this->page[ 'post' ]->rank->star)){
         $newRank = new Rank;
         $newRank->post_id=$this->page[ 'post' ]->id;
         $newRank->save();
         $this->rankId = $newRank->id;
         $this->rank  = 5;
      } else {
         $this->rank = $this->page[ 'post' ]->rank->star;
         $this->rankId = $this->page['post']->rank->id;
         }
      }
   }

   public function onRank(){
      $this->receivedStars = post('star');
      $this->rankId = post('rankId');
      $this->rank = post('rank');
      $newRank = Rank::find($this->rankId);

      if(!$newRank)
      return;

      $this->rank = ($this->rank + $this->receivedStars) / 2;

      $newRank->star = $this->rank;
      $newRank->save();
      $newRank->increment('vote');
   }
}