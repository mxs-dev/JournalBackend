<?php
namespace app\models;

use Yii;
use app\models\records\{ AssignedSubjectRecord, SubjectRecord, TeachesRecord };


/**
 * Class Teacher
 * @package app\models
 *
 * @property  $teaches  TeachesRecord[]
 * @property  $subjects SubjectRecord[]
 */
class Teacher extends User
{
    public static function find() {
        return parent::find()->andWhere(['role' => static::ROLE_TEACHER]);
    }


    /**
     * @param $condition
     * @return static | null
     * @throws \yii\base\InvalidConfigException
     */
    public static function findAll($condition) {
        return static::findByCondition($condition)->all();
    }


    /**
     * @param $condition
     * @return static | null
     * @throws \yii\base\InvalidConfigException
     */
    public static function findOne($condition) {
        return static::findByCondition($condition)->one();
    }


    public function rules () {
        return [
            [
                ['email', 'name', 'surname', 'patronymic'],
                'required',
                'message' => Yii::t("app", Yii::t('app', "Field cannot be blank"))
            ],
            [
                'email', 'string', 'max' => 255
            ],
            [
                ['name', 'surname', 'patronymic'], 'string', 'max' => 100
            ],
            ['email', 'email'],
            ['role', 'default', 'value' => static::ROLE_TEACHER],
        ];
    }


    public function extraFields()
    {
        $fields = parent::extraFields();

        $fields[] = 'teaches';
        $fields[] = 'subjects';
        $fields[] = 'assignedSubjects';

        return $fields;
    }


    public function getTeaches () {
        return $this->hasMany(TeachesRecord::class, ['userId' => 'id']);
    }


    public function getSubjects () {
        return $this->hasMany(SubjectRecord::class, ['id' => 'subjectId'])->via('teaches');
    }


    public function getAssignedSubjects () {
        return $this->hasMany(SubjectRecord::class, ['id' => 'subjectId'])
            ->viaTable(AssignedSubjectRecord::tableName(), ['userId' => 'id']);
    }
}