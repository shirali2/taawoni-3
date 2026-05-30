<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class CollectionManagerScope implements Scope
{
    /**
     * اعمال فیلتر مجموعه برای مدیران.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        // فقط اگر مدیر لاگین کرده باشد و ادمین نباشد اعمال شود
        if (session()->has('manager') && !session()->has('admin')) {
            $groupId = session('id_grop_manager');

            if (!$groupId) {
                return;
            }

            $column = $this->getScopingColumn($model);

            if (!$column) {
                return;
            }

            if ($column === 'relation_user_grops') {
                // برای مدل کاربر، از طریق جدول واسط فیلتر می‌کنیم
                $builder->whereHas('user_grops', function ($q) use ($groupId) {
                    $q->where('grop_id', $groupId);
                });
            } elseif ($column === 'json_id_grops' || $column === 'json_id_grop') {
                // تعیین نام واقعی ستون در دیتابیس
                $actualColumn = ($column === 'json_id_grops') ? 'id_grops' : 'id_grop';
                
                $builder->where(function($q) use ($groupId, $actualColumn) {
                    $q->where($actualColumn, $groupId)
                      ->orWhere($actualColumn, 'LIKE', "%\"$groupId\"%")
                      ->orWhere($actualColumn, 'LIKE', "%,$groupId%")
                      ->orWhere($actualColumn, 'LIKE', "$groupId,%");
                });
            } elseif ($column) {
                // فیلتر مستقیم روی ستون مربوطه
                $builder->where($model->getTable() . '.' . $column, $groupId);
            }
        }
    }

    /**
     * تشخیص ستون مناسب برای فیلتر بر اساس مدل.
     */
    protected function getScopingColumn(Model $model)
    {
        // اگر مدل متد مخصوصی برای اعلام ستون داشته باشد
        if (method_exists($model, 'getCollectionColumn')) {
            return $model->getCollectionColumn();
        }

        // شناسایی بر اساس کلاس مدل
        if ($model instanceof \App\user) {
            return 'relation_user_grops';
        }

        if ($model instanceof \App\menu || $model instanceof \App\form) {
            return 'json_id_grop';
        }

        if ($model instanceof \App\product) {
            return 'json_id_grops';
        }

        if ($model instanceof \App\user_grop) {
            return 'grop_id';
        }

        // اگر ستون‌های جدید مد نظر کاربر وجود داشته باشند
        // (چک کردن فیزیکی ستون‌ها در اینجا به دلیل پرفورمنس انجام نمی‌شود، 
        // اما قراردادهای پروژه اولویت دارند)
        
        return 'id_grop';
    }
}
