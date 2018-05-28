<?php

namespace App\Http\Controllers\Api\V1;

use App\Specimen;
use App\Header;
use App\Character;
use App\Value;
use App\ActionLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;

class HomeController extends Controller
{
    public function getValuesByCharacter() {
        $all = Character::all();
        $characters = [];
        foreach ($all as $each) {
            $tpValues = Value::where('character_id', '=', $each->id)->orderBy('header_id')->get();
            $characters []= $tpValues;
        }

        return $characters;
    }

    public function store(Request $request)
    {
        $character = Character::create([
            'name' => $request->input('text')
        ]);
        $headers = Header::all();
        foreach ($headers as $header) {
            Value::create([
                'character_id' => $character->id,
                'header_id' => $header->id,
                'value' => ''
            ]);
        }

        // update character header in Value Model
        $value = Value::where('character_id', '=', $character->id)->where('header_id', '=', 1)->first();
        $value->value = $character->name;
        $value->save();

        $characters = $this->getValuesByCharacter();

        $data = [
            'value'       => $value,
            'characters'    => $characters
        ];

        return $data;
    }

    public function log(Request $request)
    {
        $actionLog = ActionLog::create($request->all());

        return $actionLog;
    }

    public function all(Request $request)
    {
        $headers = Header::all();
        $characters = $this->getValuesByCharacter();

        $data = [
            'headers'       => $headers,
            'characters'    => $characters
        ];

        return $data;
    }

    public function addHeader(Request $request) {
        $header = Header::create($request->all());
        $characters = Character::all();
        foreach ($characters as $character) {
            Value::create([
                'character_id' => $character->id,
                'header_id' => $header->id,
                'value' => ''
            ]);
        }

        $headers = Header::all();
        $characters = $this->getValuesByCharacter();

        $data = [
            'headers'       => $headers,
            'characters'    => $characters
        ];

        return $data;
    }

    public function update(Request $request) {
        $value = Value::where('id', '=', $request->input('id'))->first();
        $value->value = $request->input('value');
        $value->save();

        return $value;

//        $arrayValues = Value::where('character_id', '=', $request->input('character_id'))->get();
//
//        $valueSum = 0;
//        $index = 0;
//
//        foreach ($arrayValues as $item) {
//            if ($item['header_id'] > 3) {
//                $valueSum += (float)$item['value'];
//                $index++;
//            }
//        }
//
//        if ($index > 0) {
//            $average = $valueSum / $index;
//
//            $averageValue = Value::where('character_id', '=', $request->input('character_id'))->where('header_id', '=', 2)->get();
//            $averageValue->value = sprintf("%.3f", $average);
//            $averageValue->save();
//
//            return $averageValue;
//        } else {
//
//
//            $headers = Header::all();
//            $characters = $this->getValuesByCharacter();
//
//            $data = [
//                'headers'       => $headers,
//                'characters'    => $characters
//            ];
//
//            return $data;
//
//
//        }
    }

    public function delete(Request $request) {
        $character_id = $request->input('character_id');
        Character::where('id', '=', $character_id)->delete();
        Value::where('character_id', '=', $character_id)->delete();
        $characters = $this->getValuesByCharacter();
        $data = [
            'characters'    => $characters
        ];

        return $data;
    }
}
