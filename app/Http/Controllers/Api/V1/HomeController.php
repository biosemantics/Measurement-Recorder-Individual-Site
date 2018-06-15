<?php

namespace App\Http\Controllers\Api\V1;

use App\Specimen;
use App\Header;
use App\Character;
use App\Value;
use App\ActionLog;
use App\ActivityLog;
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
            $tpValues = Value::where('character_id', '=', $each->id)->orderBy('header_id')->get();
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
            $character->method_from = $request->input('method_from');
            $character->method_to = $request->input('method_to');
            $character->unit = $request->input('unit');
            $character->measureSemantic = $request->input('measureSemantic');
            $character->entitySemantic = $request->input('entitySemantic');
            $character->creator = $request->input('creator');
            $character->save();

        } else {
            $character = Character::create([
                'name' => $request->input('name'),
                'method_from' => $request->input('method_from'),
                'method_to' => $request->input('method_to'),
                'unit' => $request->input('unit'),
                'measureSemantic' => $request->input('measureSemantic'),
                'entitySemantic' => $request->input('entitySemantic'),
                'creator' => $request->input('creator'),
            ]);
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

        $data = [
            'value'       => $value,
            'characters'    => $characters
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
            ->where('header_id', '>', 3)
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

    public function all(Request $request)
    {
        $headers = Header::all();
        $characters = $this->getValuesByCharacter();
        $arrayCharacters = Character::all();

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

        $headers = Header::all();
        $characters = $this->getValuesByCharacter();
        $arrayCharacters = Character::all();

        $data = [
            'headers'       => $headers,
            'characters'    => $characters,
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
        Character::where('id', '=', $character_id)->delete();
        Value::where('character_id', '=', $character_id)->delete();
        $characters = $this->getValuesByCharacter();
        $data = [
            'characters'    => $characters
        ];

        return $data;
    }

    public function activity_log(Request $request) {
        
        $actLog = ActivityLog::create($request->all());

        return $actLog;
    }
}
