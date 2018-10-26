<?php

namespace App\Http\Controllers\Api\V1;

use App\Specimen;
use App\Header;
use App\Character;
use App\Value;
use App\ActionLog;
use App\ActivityLog;
use App\MetaLog;
use App\UserLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;

class HomeController extends Controller
{
    public function getValuesByCharacter()
    {
        $all = Character::all();
        $characters = [];
        foreach ($all as $each) {
            $tpValues = Value::where('character_id', '=', $each->id)->orderBy('header_id', 'dec')->get();
            $characters []= $tpValues;
        }

        return $characters;
    }

    public function getCharacter(Request $request, $id)
    {
        $character = Character::where('id', '=', $id)->first();

        return $character;
    }

    public function store(Request $request)
    {
        if ($request->has('id')) {
            $character = Character::where('id', '=', $request->input('id'))->first();
            $character->name = $request->input('name');
            $character->method_as = $request->input('method_as');
            $character->method_from = $request->input('method_from');
            $character->method_to = $request->input('method_to');
            $character->method_include = $request->input('method_include');
            $character->method_exclude = $request->input('method_exclude');
            $character->method_at = $request->input('method_at');
            $character->unit = $request->input('unit');
            $character->measure_semantic = $request->input('measure_semantic');
            $character->entity_semantic = $request->input('entity_semantic');
            $character->creator = $request->input('creator');
            $character->username = $request->input('username');
            $character->usage_count = $request->input('usage_count');
            $character->show_flag = $request->input('show_flag');
            $character->save();

        } else {
            $character = Character::create([
                'name' => $request->input('name'),
                'method_as' => $request->input('method_as'),
                'method_from' => $request->input('method_from'),
                'method_to' => $request->input('method_to'),
                'method_include' => $request->input('method_include'),
                'method_exclude' => $request->input('method_exclude'),
                'method_at' => $request->input('method_at'),
                'unit' => $request->input('unit'),
                'measure_semantic' => $request->input('measure_semantic'),
                'entity_semantic' => $request->input('entity_semantic'),
                'creator' => $request->input('creator'),
                'username' => $request->input('username'),
                'usage_count' => $request->input('usage_count'),
                'show_flag' => $request->input('show_flag'),
            ]);
//            $headers = Header::orderBy('created_at', 'dec')->get();
            $headers = Header::all();
            foreach ($headers as $header) {
                Value::create([
                    'character_id' => $character->id,
                    'header_id' => $header->id,
                    'value' => ''
                ]);
            }
        }

        // update character header in Value Model
        $value = Value::where('character_id', '=', $character->id)->where('header_id', '=', 1)->first();
        $value->value = $character->name;
        $value->save();

        $characters = $this->getValuesByCharacter();
        $arrayCharacters = Character::all();

//        foreach ($arrayCharacters as $each) {
//            $usageCount = Value::where('character_id', '=', $each->id)
//                ->where('header_id', '>', 4)
//                ->where('value', '<>', '')
//                ->count();
//            $each->usageCount = $usageCount;
//        }

        $data = [
            'character'  => $character,
            'value'       => $value,
            'characters'    => $characters,
            'arrayCharacters' => $arrayCharacters
        ];

        return $data;
    }

    public function history(Request $request, $characterId)
    {
        $history = ActionLog::select('created_at')
            ->where('model_id', '=', $characterId)
            ->whereIn('action_type', ['update', 'create'])
            ->get();

        return $history;
    }

    public function getName(Request $request)
    {
        $characterName = Character::select('name')->get();

        return $characterName;
    }

    public function usage(Request $request, $characterId)
    {
        $values = Value::where('character_id', '=', $characterId)
            ->where('header_id', '>', 4)
            ->where('value', '<>', '')
            ->get();

        $usage = [];

        if (count($values) > 0) {
            foreach ($values as $each) {
                $tpUsage = Header::select('header')
                    ->where('id', '=', $each->header_id)
                    ->first();
                $usage []= $tpUsage;
            }
        }

        return $usage;
    }

    public function log(Request $request)
    {
        $actionLog = ActionLog::create($request->all());

        return $actionLog;
    }

    public function saveUserLog(Request $request) {
        $userLog = UserLog::create($request->all());

        return $userLog;
    }

    public function all(Request $request)
    {
        $headers = Header::orderBy('created_at', 'dec')->get();
//        $headers = Header::all();
        $characters = $this->getValuesByCharacter();
        $arrayCharacters = Character::all();

        foreach ($arrayCharacters as $each) {
            $usageCount = Value::where('character_id', '=', $each->id)
                ->where('header_id', '>', 4)
                ->where('value', '<>', '')
                ->count();
            $each->usageCount = $usageCount;
        }

        $data = [
            'headers'               => $headers,
            'characters'            => $characters,
            'arrayCharacters'       => $arrayCharacters
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

        $headers = Header::orderBy('created_at', 'dec')->get();
//        $headers = Header::all();
        $characters = $this->getValuesByCharacter();
        $arrayCharacters = Character::all();

        foreach ($arrayCharacters as $each) {
            $usageCount = Value::where('character_id', '=', $each->id)
                ->where('header_id', '>', 4)
                ->where('value', '<>', '')
                ->count();
            $each->usageCount = $usageCount;
        }

        $data = [
            'headers'       => $headers,
            'characters'    => $characters,
            'arrayCharacters'       => $arrayCharacters
        ];

        return $data;
    }

    public function deleteHeader(Request $request, $headerId) {
        Header::where('id', '=', $headerId)->delete();
        Value::where('header_id', '=', $headerId)->delete();
        $characters = $this->getValuesByCharacter();
        $arrayCharacters = Character::all();

        foreach ($arrayCharacters as $each) {
            $usageCount = Value::where('character_id', '=', $each->id)
                ->where('header_id', '>', 4)
                ->where('value', '<>', '')
                ->count();
            $each->usageCount = $usageCount;
        }

        $headers = Header::orderBy('created_at', 'dec')->get();

        $data = [
            'headers'               => $headers,
            'characters'            => $characters,
            'arrayCharacters'       => $arrayCharacters
        ];

        return $data;
    }

    public function update(Request $request) {
        $value = Value::where('id', '=', $request->input('id'))->first();
        $value->value = $request->input('value');
        $value->save();

        return $value;
    }

    public function delete(Request $request) {
        $character_id = $request->input('character_id');
        Character::where('id', '=', $character_id)->update(['show_flag' => false]);
//        Value::where('character_id', '=', $character_id)->delete();
        $arrayCharacters = Character::all();

        foreach ($arrayCharacters as $each) {
            $usageCount = Value::where('character_id', '=', $each->id)
                ->where('header_id', '>', 4)
                ->where('value', '<>', '')
                ->count();
            $each->usageCount = $usageCount;
        }

        $characters = $this->getValuesByCharacter();
        $data = [
            'characters'    => $characters,
            'arrayCharacters' => $arrayCharacters
        ];

        return $data;
    }

    public function undelete(Request $request) {
        $character_id = $request->input('character_id');
        Character::find($character_id)->update(['show_flag'=>true]);
    }

    public function activity_log(Request $request) {
        
        $actLog = ActivityLog::create($request->all());

        return $actLog;
    }

    public function saveMetaLog(Request $request) {
        $metaLog = MetaLog::create($request->all());

        return $metaLog;
    }

    public function getMetaLog(Request $request, $characterId) {
        $metaLogs = MetaLog::where('character_id', '=', $characterId)->orderBy('created_at', 'asc')->get();


        $metaHistory = [];
        foreach ($metaLogs as $eachLog) {
            $tpValue = $eachLog->created_at . ' ' . $eachLog->username . ' ' . $eachLog->description;
            $metaHistory []= $tpValue;
        }

        $data = [
            'metaHistory'   =>  $metaHistory
        ];

        return $data;
    }
}
