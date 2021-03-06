<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class ParseJsonService
{
    /**
     * Собрать ответ (результат) для сохранения в БД
     */
    public function parseAnswerToJson($result, $type, $id)
    {
        
        if($type == 'table') {
            
            foreach($result as $i => $cols) {
                foreach($cols as $j => $cell) {
                    $result[$i][$j] = (bool)$cell;
                }
            }

            $result = json_encode($result);

        } elseif($type == 'checkbox') {
            
            foreach($result as $i => $value) {
                $result[$i] = (bool)$value;
            }

            $result = json_encode($result);

        } elseif($type == 'radio') {

            $result = (int)$result;
            if($result < 0) $result = false;

        } elseif($type == 'directory') {

            $result = (int)$result;
            
            //Что за справочник
            $dataResult = DB::table('elements_data')->
                where([
                    ['element_id', $id],
                    ['key', 'alias'],
                ])->
                first();
            if(empty($dataResult)) return ['status' => 'fail'];
            $alias = $dataResult->value;

            //Есть ли в нем такой элемент
            $count = DB::table($alias)->where('id', $result)->count();
            if($count == 0) $result = false;


        } else {
            $result = false;
        }

        return $result;
        
    }

    /**
     * Разобрать ответы
     */
    public function parseAnswerToArray($result, $type, $id)
    {        
        if($type == 'table') {

            $result = json_decode($result);

        } elseif($type == 'checkbox') {

            $result = json_decode($result);

        } elseif($type == 'radio') {

            $result = (int)$result;

        } elseif($type == 'directory') {

            $result = (int)$result;

        } else {
            $result = false;
        }

        return $result;
        
    }

    /**
     * Разобрать варианты ответов
     */
    public function parseElemensDataToArray($data)
    {        
        $result = [];
        foreach($data as $value) {
            if($value->key == 'cols' || $value->key == 'rows') {
                $items = json_decode($value->value);
                foreach($items  as $item) {
                    $result[$value->key][] = ['value' => $item];
                }
            } else {
                $result[$value->key] = $value->value;
            }
        }

        return $result;        
    }

}