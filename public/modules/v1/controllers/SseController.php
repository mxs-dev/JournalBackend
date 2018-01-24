<?php
namespace app\modules\v1\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\filters\auth\{
    CompositeAuth, HttpBearerAuth, QueryParamAuth
};
use yii\web\Controller;

use app\filters\CustomCors;
use app\sse\SseEvent;



class SseController extends Controller
{
    public $messageHandler;


    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => CompositeAuth::class,
            'authMethods' => [
                QueryParamAuth::class,
            ],
        ];

        $behaviors['verbs'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'index' => ['GET'],
            ]
        ];

        return $behaviors;
    }


    public function actionIndex () {
        $this->setSSEHeaders();

        $this->sendMsg(time(), ['type' => 'open']);

        Yii::$app->eventService->subscribe(['chan-1'], [$this, 'redisEventListener']);

        exit(0);
    }


    public function redisEventListener ($redis, $channel, string $eventSerialized) {

        /** @var SseEvent $event */
        $event = unserialize($eventSerialized);


        if (empty($event->permissionName)){
            $this->sendMsg(time(), (array)$event->data);
            return;
        }


        if (Yii::$app->user->can($event->permissionName, $event->permissionData)){
            $this->sendMsg(time(), (array)$event->data);
        }
    }


    private function setSSEHeaders () {
        header("Content-Type: text/event-stream");
        header("Cache-Control: no-cache");
        header("Access-Control-Allow-Origin: null");
        header("Access-Control-Expose-Headers: *");
        header("Access-Control-Allow-Credentials: true");
    }


    private function sendMsg($id, $msg) {
        $message = json_encode($msg);

        echo "id: $id" . PHP_EOL;
        echo "data: $message \n";
        echo PHP_EOL;

        ob_flush();
        flush();
    }
}