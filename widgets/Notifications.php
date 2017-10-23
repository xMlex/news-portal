<?php
/**
 * Created by Maxim Novikov
 * Date: 19.10.17 20:33
 */

namespace app\widgets;


use app\models\PostNotifications;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

class Notifications extends Widget
{
    public $template = '<div class="alert alert-info alert-dismissible fade in post-notification" role="alert" style="display: {display}">
<p>Есть непрочитанная новость: {message}</p>
<p> <a data-url="{url}" class="btn btn-success view" data-id="{id}" >Прочитать новость</a>
<a class="btn btn-default as-read" data-id="{id}">Отметить как прочитано</a></p></div>';

    public function run()
    {
        $output = '';
        if (\Yii::$app->user->isGuest) {
            return $output;
        }

        foreach (\Yii::$app->user->identity->unreadPostNotifications as $i => $postNotification) {
            /** @var PostNotifications $postNotification */
            $url = $postNotification->post->getStaticUrl();
            $message = Html::a($postNotification->post->title,
                '#',
                ['class' => 'view as-read', 'data-url' => $url, 'data-id' => $postNotification->post_id]);
            $output .= str_replace(['{message}', '{id}', '{display}', '{url}'], [
                $message,
                $postNotification->post_id,
                $i > 0 ? 'none' : 'block',
                $url
                ], $this->template);
        }

        $this->view->registerJs('$(".post-notification a").click(function(e){
            e.preventDefault();
            
            var $this = $(this), id = $this.data("id");

            $.get("' . Url::to(['/post/set-read']) . '",{id:id},function(d){
             
             $this.closest(".post-notification").hide("fast", function(){ 
                if($this.hasClass("view")){ 
                    document.location.href = $this.data("url");
                }
                $(this).remove(); 
              });
             $(".post-notification").first().show("fast");
            });
        });', View::POS_READY);

        return $output;
    }
}