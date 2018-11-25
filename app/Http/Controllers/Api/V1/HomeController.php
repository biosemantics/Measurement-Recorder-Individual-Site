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
use App\CharaUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only(['getArrayCharacters', 'delete', 'addHeader', 'deleteHeader', 'getHeaders', 'store']);
    }
    public function getValuesByCharacter()
    {
        $all = Character::all();
        $headers = $this->getHeaders();
        $characters = [];
        foreach ($all as $c) {
            $value_array = [];
            $min = -1; $max = 0;
            $range_exist = false;
            foreach ($headers as $h) {
                $v = Value::where(['character_id'=>$c->id, 'header_id'=>$h->id])->first();
                
                if ($h->id >= 4 && is_numeric($v->value)) {
                    $range_exist = true;
                    if ($min < 0 || $min > $v->value) $min = $v->value;
                    if ($max < $v->value) $max = $v->value;
                }

                $v->username = $c->username;
                $v->unit = $c->unit;
                array_push($value_array, $v);
            }
            array_push($value_array, [
                'character_id' => $c->id, 
                'header_id' => 0,
                'username' => $c->username,
                'unit' => $c->unit,
                'value' => $range_exist ? ($min.' - '.$max) : '0 - 0'
            ]);
            array_push($characters, $value_array);
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
        $charauser = NULL;
        if ($request->has('id')) {
            $charauser = CharaUser::firstOrNew(['character_id'=>$request->input('id'), 'user_id'=>Auth::id()]);
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
            $charauser = new CharaUser;
            $tmp_username = '';
            if ($request->has('clone_id')) {
                $original_username = Character::find($request->input('clone_id'))->username;
                if (!empty($original_username))
                    $tmp_username = $original_username.';'.$request->input('username');
            }
            if (empty($tmp_username))
                $tmp_username = $request->input('username');

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
                'username' => $tmp_username,
                'usage_count' => $request->input('usage_count'),
                'show_flag' => $request->input('show_flag'),
            ]);
            $charauser->character_id = $character->id;
            $charauser->user_id = Auth::id();

            $headers = Header::all();
            foreach ($headers as $header) {
                Value::create([
                    'character_id' => $character->id,
                    'header_id' => $header->id,
                    'value' => ''
                ]);
            }
        }

        $charauser->show_flag = 1;
        $charauser->save();

        // update character header in Value Model
        $value = Value::where('character_id', '=', $character->id)->where('header_id', '=', 1)->first();
        $value->value = $character->name;
        $value->save();

        $characters = $this->getValuesByCharacter();
        $arrayCharacters = $this->getArrayCharacters();

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
            ->where('header_id', '>=', 4)
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
        $headers = $this->getHeaders();
        $characters = $this->getValuesByCharacter();
        $arrayCharacters = $this->getArrayCharacters();

        $data = [
            'headers'               => $headers,
            'characters'            => $characters,
            'arrayCharacters'       => $arrayCharacters
        ];

        return $data;
    }

    public function addHeader(Request $request) {
        $user_id = Auth::id();
        $header = new Header;
        $header->header = $request->input('header');
        $header->user_id = $user_id;
        $header->save();

        $characters = Character::all();
        foreach ($characters as $character) {
            Value::create([
                'character_id' => $character->id,
                'header_id' => $header->id,
                'value' => ''
            ]);
        }

        $headers = $this->getHeaders();
        $characters = $this->getValuesByCharacter();
        $arrayCharacters = $this->getArrayCharacters();

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
        $arrayCharacters = $this->getArrayCharacters();
        $headers = $this->getHeaders();

        $data = [
            'headers'               => $headers,
            'characters'            => $characters,
            'arrayCharacters'       => $arrayCharacters
        ];

        return $data;
    }

    public function update(Request $request) {
        $value = Value::where('id', '=', $request->input('id'))->first();
        
        $v = $request->input('value');

        if (is_numeric($v)) {
            $value->value = $v;
        } else {
            $varr = preg_split('/(?<=[0-9])(?=[a-z]+)/i',$v);
            if (count($varr)==2 && is_numeric($varr[0])) {
                $c = Character::find($value->character_id);
                if ($c->unit == $varr[1]) {
                    $value->value = $varr[0];
                } else {
                    return ['error_input' => 1];
                }
            }
        }

        $value->save();

        $characters = $this->getValuesByCharacter();
        $arrayCharacters = $this->getArrayCharacters();

        return [
            'error_input' => 0,
            'updatedCharacter' => $value,
            'characters' => $characters,
            'arrayCharacters' => $arrayCharacters
        ];
    }

    public function delete(Request $request) {
        $character_id = $request->input('character_id');
        
        $charauser = CharaUser::firstOrNew(['character_id'=>$character_id, 'user_id'=>Auth::id()]);
        $charauser->show_flag = 0;
        $charauser->save();
        // Character::where('id', '=', $character_id)->update(['show_flag' => false]);
        // Value::where('character_id', '=', $character_id)->delete();
        $arrayCharacters = $this->getArrayCharacters();
        $characters = $this->getValuesByCharacter();
        $data = [
            'characters'    => $characters,
            'arrayCharacters' => $arrayCharacters
        ];

        return $data;
    }

    public function getArrayCharacters() {
        $user_id = Auth::id();
        $arrayCharacters = Character::all();        
        foreach ($arrayCharacters as $c) {
            $charauser = CharaUser::where(['character_id'=>$c->id, 'user_id'=>$user_id])->first();
            if ($charauser) {
                $c->show_flag = $charauser->show_flag;
            } else {
                $c->show_flag = 0;
            }
            $usageCount = Value::where('character_id', '=', $c->id)
                ->where('header_id', '>=', 4)
                ->where('value', '<>', '')
                ->count();
            $c->usage_count = $usageCount;
        }
        return $arrayCharacters;
    }

    public function getHeaders() {
        return Header::where('user_id', Auth::id())
                ->orWhere('user_id', NULL)
                ->orderBy('id', 'desc')->get();
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

    public function setCharashow(Request $request) {
        $character_id = $request->input('character_id');
        $user_id = $request->input('user_id');
        $show_flag = $request->input('show_flag');
        $record = CharaUser::firstOrNew(['character_id'=>$character_id, 'user_id'=>$user_id]);
        $record->show_flag = $show_flag;
        $record->save();
    }
}
